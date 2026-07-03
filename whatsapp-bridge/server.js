/**
 * Mindlytics WhatsApp Bridge
 * ───────────────────────────
 * يعمل على سيرفر Node.js (VPS) — Laravel يتصل به عبر HTTP.
 */
require('dotenv').config();

const express = require('express');
const cors = require('cors');
const QRCode = require('qrcode');
const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const { promisify } = require('util');
const execAsync = promisify(exec);
const { Client, LocalAuth } = require('whatsapp-web.js');

const PORT = parseInt(process.env.PORT || '3001', 10);
const API_TOKEN = process.env.API_TOKEN || '';
const ALLOWED_ORIGINS = (process.env.ALLOWED_ORIGINS || '*').split(',').map((s) => s.trim());

const chromiumPaths = [
    process.env.CHROMIUM_PATH,
    '/usr/bin/chromium-browser',
    '/usr/bin/chromium',
    '/usr/bin/google-chrome',
].filter(Boolean);

function resolveExecutablePath() {
    for (const p of chromiumPaths) {
        if (fs.existsSync(p)) {
            return p;
        }
    }
    return undefined;
}

const app = express();
app.use(express.json({ limit: '1mb' }));
app.use(cors({ origin: ALLOWED_ORIGINS.includes('*') ? true : ALLOWED_ORIGINS }));

const state = {
    status: 'disconnected',
    qr: null,
    qrUpdatedAt: null,
    pairingCode: null,
    pairingPhone: null,
    pairingCodeUpdatedAt: null,
    pairingMode: false,
    phone: null,
    pushname: null,
    platform: null,
    lastError: null,
    lastErrorAt: null,
    readyAt: null,
};

let client = null;
let initializing = false;
let reconnectTimer = null;
let reconnectPaused = false;
let restartScheduled = false;

/** طابور إرسال — رسالة واحدة في كل مرة (يمنع crash Puppeteer) */
const sendQueue = [];
let sendQueueRunning = false;

function authMiddleware(req, res, next) {
    if (!API_TOKEN) {
        return res.status(500).json({ success: false, error: 'API_TOKEN is not configured on bridge server.' });
    }
    const header = req.headers.authorization || '';
    const token = header.startsWith('Bearer ') ? header.slice(7) : req.headers['x-api-token'];
    if (token !== API_TOKEN) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
    }
    next();
}

function normalizePhoneDigits(phone) {
    let digits = String(phone || '').replace(/\D/g, '');
    if (!digits) {
        return '';
    }
    if (digits.startsWith('0')) {
        digits = '20' + digits.slice(1);
    }
    if (!digits.startsWith('20') && digits.length <= 11) {
        digits = '20' + digits;
    }
    return digits;
}

function formatChatId(phone) {
    const digits = normalizePhoneDigits(phone);
    if (!digits) {
        throw new Error('Invalid phone number.');
    }
    return `${digits}@c.us`;
}

function clearPairingState() {
    state.pairingCode = null;
    state.pairingPhone = null;
    state.pairingCodeUpdatedAt = null;
    state.pairingMode = false;
}

function isProtocolError(err) {
    const msg = String(err?.message || err || '');
    return (
        msg.includes('Execution context was destroyed') ||
        msg.includes('Protocol error') ||
        msg.includes('Session closed') ||
        msg.includes('Target closed') ||
        msg.includes('browser has disconnected')
    );
}

function sleep(ms) {
    return new Promise((r) => setTimeout(r, ms));
}

function cleanupSessionLock() {
    const sessionDirs = [
        path.resolve('./.wwebjs_auth/session'),
        path.resolve('./.wwebjs_auth/session-default'),
    ];

    for (const sessionDir of sessionDirs) {
        const lockCandidates = [
            path.join(sessionDir, 'SingletonLock'),
            path.join(sessionDir, 'SingletonCookie'),
            path.join(sessionDir, 'SingletonSocket'),
            path.join(sessionDir, 'lockfile'),
        ];
        for (const p of lockCandidates) {
            try {
                if (fs.existsSync(p)) {
                    fs.unlinkSync(p);
                    console.log('Removed stale lock:', p);
                }
            } catch (_) {
                /* ignore */
            }
        }
    }
}

/** قتل Chrome/Puppeteer العالق — يحافظ على مجلد .wwebjs_auth (بدون logout) */
async function killStaleChromium() {
    cleanupSessionLock();

    const authPath = path.resolve('./.wwebjs_auth');
    const sessionPath = path.join(authPath, 'session');

    if (process.platform === 'linux') {
        try {
            await execAsync(`fuser -k "${sessionPath}" 2>/dev/null || true`);
        } catch (_) {
            /* ignore */
        }

        try {
            const { stdout } = await execAsync('pgrep -af "chromium|chrome" 2>/dev/null || true');
            for (const line of stdout.split('\n')) {
                if (!line.includes('wwebjs_auth') && !line.includes('mindlytics-whatsapp')) {
                    continue;
                }
                const pid = parseInt(line.trim().split(/\s+/)[0], 10);
                if (pid > 1 && pid !== process.pid) {
                    try {
                        process.kill(pid, 'SIGKILL');
                        console.log('Killed stale browser pid:', pid);
                    } catch (_) {
                        /* ignore */
                    }
                }
            }
        } catch (_) {
            /* ignore */
        }

        const patterns = [
            sessionPath,
            authPath,
            'wwebjs_auth/session',
            'wwebjs_auth',
            'mindlytics-whatsapp-bridge',
            '--user-data-dir=' + sessionPath,
        ];
        for (const pattern of patterns) {
            try {
                await execAsync(`pkill -9 -f "${pattern}" 2>/dev/null || true`);
            } catch (_) {
                /* ignore */
            }
        }
    }

    await sleep(3500);
    cleanupSessionLock();
}

function scheduleProcessRestart(reason) {
    if (restartScheduled) {
        return;
    }
    restartScheduled = true;
    reconnectPaused = true;
    console.log('Bridge process restart scheduled:', reason);
    setTimeout(() => {
        process.exit(0);
    }, 600);
}

/**
 * تجهيز جلسة جديدة — يوقف العميل، ينتظر initialize الجاري، ويقتل Chrome العالق.
 */
async function prepareForNewSession() {
    if (reconnectTimer) {
        clearTimeout(reconnectTimer);
        reconnectTimer = null;
    }

    reconnectPaused = false;
    restartScheduled = false;

    await destroyClient();

    for (let i = 0; i < 60 && initializing; i++) {
        await sleep(250);
    }

    initializing = false;
    client = null;
    await killStaleChromium();
}

/**
 * إصلاح الاتصال بدون مسح الربط — يحل "browser is already running"
 */
async function repairSession() {
    console.log('Repairing WhatsApp session (keeping auth)...');
    await prepareForNewSession();
    state.lastError = null;
    state.lastErrorAt = null;

    await buildClient(true, !state.pairingMode, 0);
    await sleep(3000);
    if (client) {
        await refreshClientInfo();
    }

    if (state.lastError && isBrowserLockError({ message: state.lastError })) {
        scheduleProcessRestart('repair-browser-lock');
        return {
            ...connectionSnapshot(),
            restarting: true,
            message: 'Chrome عالق — يُعاد تشغيل Bridge تلقائياً. انتظر 15 ثانية.',
        };
    }

    return connectionSnapshot();
}

function isBrowserLockError(err) {
    const msg = String(err?.message || err || '');
    return msg.includes('browser is already running') || msg.includes('userDataDir');
}

async function destroyClient() {
    if (!client) {
        return;
    }
    const old = client;
    client = null;
    initializing = false;
    try {
        if (old.pupBrowser) {
            await old.pupBrowser.close();
        }
    } catch (_) {
        /* ignore */
    }
    try {
        await old.destroy();
    } catch (_) {
        /* ignore */
    }
    await sleep(800);
}

function scheduleReconnect(delayMs = 5000) {
    if (reconnectPaused || reconnectTimer) {
        return;
    }
    reconnectTimer = setTimeout(async () => {
        reconnectTimer = null;
        if (state.status === 'ready') {
            return;
        }
        console.log('Attempting WhatsApp reconnect...');
        await buildClient();
    }, delayMs);
}

async function buildClient(fromRepair = false, useQrMode = false, lockAttempt = 0) {
    if (client || initializing) {
        return;
    }
    initializing = true;
    if (!fromRepair) {
        state.lastError = null;
    }
    cleanupSessionLock();

    const pairingPhone = !useQrMode && state.pairingPhone ? state.pairingPhone : null;
    if (useQrMode) {
        clearPairingState();
    }

    const clientOptions = {
        authStrategy: new LocalAuth({ dataPath: './.wwebjs_auth' }),
        puppeteer: {
            headless: true,
            executablePath: resolveExecutablePath(),
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--disable-software-rasterizer',
                '--no-zygote',
                '--disable-extensions',
            ],
        },
    };

    if (pairingPhone) {
        state.pairingMode = true;
        state.pairingPhone = pairingPhone;
        clientOptions.pairWithPhoneNumber = {
            phoneNumber: pairingPhone,
            showNotification: true,
            intervalMs: 180000,
        };
    }

    client = new Client(clientOptions);

    client.on('qr', async (qr) => {
        if (state.pairingMode) {
            return;
        }
        state.status = 'qr';
        state.qr = qr;
        state.qrUpdatedAt = new Date().toISOString();
        try {
            console.log('\nScan QR with WhatsApp:\n');
            console.log(await QRCode.toString(qr, { type: 'terminal', small: true }));
        } catch (_) {
            /* ignore */
        }
    });

    client.on('code', (code) => {
        state.status = 'pairing';
        state.pairingCode = String(code);
        state.pairingCodeUpdatedAt = new Date().toISOString();
        state.qr = null;
        console.log('Pairing code:', code);
    });

    client.on('authenticated', () => {
        state.status = 'authenticated';
        state.qr = null;
    });

    client.on('auth_failure', (msg) => {
        state.status = 'auth_failure';
        state.lastError = String(msg);
        client = null;
        initializing = false;
    });

    client.on('ready', async () => {
        state.status = 'ready';
        state.qr = null;
        clearPairingState();
        state.readyAt = new Date().toISOString();
        state.lastError = null;
        state.lastErrorAt = null;
        await refreshClientInfo();
        console.log('WhatsApp bridge READY', state.phone || '(phone pending)');
    });

    client.on('disconnected', async (reason) => {
        state.status = 'disconnected';
        state.phone = null;
        state.pushname = null;
        state.lastError = String(reason || 'disconnected');
        state.lastErrorAt = new Date().toISOString();
        await destroyClient();
        scheduleReconnect(8000);
    });

    try {
        await client.initialize();
    } catch (err) {
        if (isBrowserLockError(err) && lockAttempt < 4) {
            console.log(`Browser lock — cleanup attempt ${lockAttempt + 1}/4`);
            await destroyClient();
            await killStaleChromium();
            initializing = false;
            client = null;
            await sleep(2000 + lockAttempt * 1000);
            return buildClient(true, useQrMode, lockAttempt + 1);
        }
        state.status = 'error';
        state.lastError = err.message;
        state.lastErrorAt = new Date().toISOString();
        await destroyClient();
        if (isBrowserLockError(err)) {
            reconnectPaused = true;
            scheduleProcessRestart('initialize-browser-lock');
        } else {
            scheduleReconnect(10000);
        }
    } finally {
        initializing = false;
    }
}

async function refreshClientInfo() {
    if (!client) {
        return false;
    }

    try {
        const info = client.info;
        if (info?.wid?.user) {
            state.phone = info.wid.user;
            state.pushname = info.pushname || state.pushname;
            state.platform = info.platform || state.platform;
        }

        const wwebState = await client.getState();
        if (wwebState === 'CONNECTED') {
            if (state.status !== 'ready') {
                state.status = 'ready';
            }
            state.lastError = null;
            return true;
        }
    } catch (err) {
        if (isProtocolError(err)) {
            return state.status === 'ready';
        }
    }

    return state.status === 'ready' && !!client;
}

async function isActuallyConnected() {
    if (!client) {
        return false;
    }

    try {
        const wwebState = await client.getState();
        return wwebState === 'CONNECTED';
    } catch (err) {
        if (isProtocolError(err) && (state.status === 'ready' || state.status === 'degraded')) {
            return true;
        }

        return false;
    }
}

function connectionSnapshot() {
    const sessionPresent = (state.status === 'ready' || state.status === 'degraded') && !!client;
    const connected = sessionPresent;

    return {
        success: true,
        status: state.status,
        connected,
        healthy: connected,
        /** يُملأ في /api/status بعد فحص getState() — يطابق ما يحتاجه الإرسال */
        send_ready: null,
        phone: state.phone,
        pushname: state.pushname,
        platform: state.platform,
        ready_at: state.readyAt,
        qr_pending: state.status === 'qr',
        pairing_mode: state.pairingMode,
        pairing_phone: state.pairingPhone,
        pairing_code: connected ? null : state.pairingCode,
        pairing_code_updated_at: state.pairingCodeUpdatedAt,
        /** يُعرض فقط عند انقطاع الاتصال — لا نُظهر خطأً قديماً والجلسة شغّالة */
        last_error: connected ? null : state.lastError,
        last_error_at: connected ? null : state.lastErrorAt,
        queue_length: sendQueue.length,
    };
}

async function startPairingMode(phone) {
    const digits = normalizePhoneDigits(phone);
    if (!digits || digits.length < 10) {
        throw new Error('Invalid phone. Use country code + number, e.g. 2010xxxxxxxx.');
    }

    await prepareForNewSession();

    state.lastError = null;
    state.lastErrorAt = null;
    state.qr = null;
    state.pairingPhone = digits;
    state.pairingCode = null;
    state.pairingMode = true;
    state.status = 'pairing';

    await buildClient(false, false, 0);
    await sleep(3000);

    if (state.lastError && isBrowserLockError({ message: state.lastError })) {
        console.log('Pairing: browser still locked — scheduling process restart...');
        scheduleProcessRestart('pairing-browser-lock');
        return {
            ...connectionSnapshot(),
            restarting: true,
            message: 'Chrome عالق — يُعاد تشغيل Bridge. انتظر 15 ثانية ثم أعد طلب الرمز.',
        };
    }

    return connectionSnapshot();
}

async function switchToQrMode() {
    await prepareForNewSession();
    clearPairingState();
    state.status = 'disconnected';
    state.qr = null;

    await buildClient(false, true, 0);
    await sleep(2000);

    return connectionSnapshot();
}

async function markSendError(err) {
    state.lastError = err.message;
    state.lastErrorAt = new Date().toISOString();

    if (isProtocolError(err)) {
        state.status = 'degraded';
        try {
            await refreshClientInfo();
        } catch (_) {
            /* ignore */
        }
        if (state.status === 'ready' && client) {
            return;
        }
        state.status = 'error';
        state.phone = null;
        await destroyClient();
        scheduleReconnect(5000);
    }
}

async function ensureReady() {
    for (let attempt = 0; attempt < 2; attempt++) {
        if (client && (state.status === 'ready' || state.status === 'degraded')) {
            await refreshClientInfo();
            if (await isActuallyConnected()) {
                if (state.status === 'degraded') {
                    state.status = 'ready';
                }
                return true;
            }
        }

        if (!client && !initializing) {
            await buildClient();
            await sleep(2000);
        }

        if (client) {
            await refreshClientInfo();
            if (await isActuallyConnected()) {
                return true;
            }
        }

        if (attempt === 0) {
            console.log('ensureReady: session not ready — running repair before send...');
            await repairSession();
            await sleep(3000);
        }
    }

    return false;
}

async function sendOneMessage(phone, message, simulateTyping = true) {
    if (!(await ensureReady())) {
        throw new Error('WhatsApp is not connected. Status: ' + state.status);
    }

    const chatId = formatChatId(phone);
    let lastErr = null;

    for (let attempt = 1; attempt <= 3; attempt++) {
        try {
            if (simulateTyping) {
                try {
                    const chat = await client.getChatById(chatId);
                    const typingMs = 1200 + Math.floor(Math.random() * 1800);
                    await chat.sendStateTyping();
                    await sleep(typingMs);
                } catch (_) {
                    /* typing optional */
                }
            }

            const result = await client.sendMessage(chatId, String(message));
            state.lastError = null;
            state.lastErrorAt = null;
            state.status = 'ready';

            try {
                await refreshClientInfo();
            } catch (_) {
                /* optional */
            }

            return {
                success: true,
                message_id: result?.id?._serialized || null,
                chat_id: chatId,
            };
        } catch (err) {
            lastErr = err;
            console.error(`Send attempt ${attempt} failed:`, err.message);

            if (isProtocolError(err)) {
                await markSendError(err);
                await sleep(3000 * attempt);
                if (!(await ensureReady())) {
                    continue;
                }
            } else {
                state.lastError = err.message;
                state.lastErrorAt = new Date().toISOString();
                throw err;
            }
        }
    }

    throw lastErr || new Error('Send failed after retries');
}

function enqueueSend(phone, message, simulateTyping) {
    return new Promise((resolve, reject) => {
        sendQueue.push({ phone, message, simulateTyping, resolve, reject });
        runSendQueue();
    });
}

async function runSendQueue() {
    if (sendQueueRunning) {
        return;
    }
    sendQueueRunning = true;

    while (sendQueue.length > 0) {
        const job = sendQueue.shift();
        try {
            const result = await sendOneMessage(job.phone, job.message, job.simulateTyping);
            job.resolve(result);
        } catch (err) {
            job.reject(err);
        }
        await sleep(1800 + Math.floor(Math.random() * 1200));
    }

    sendQueueRunning = false;
}

app.get('/health', (_req, res) => {
    res.json({ ok: true, service: 'mindlytics-whatsapp-bridge', status: state.status });
});

app.get('/api/status', authMiddleware, async (_req, res) => {
    if (client) {
        await refreshClientInfo();
    }
    const snapshot = connectionSnapshot();
    snapshot.send_ready = await isActuallyConnected();
    snapshot.healthy = snapshot.send_ready;
    res.json(snapshot);
});

app.get('/api/qr', authMiddleware, async (_req, res) => {
    if (client) {
        await refreshClientInfo();
    }
    if (state.status === 'ready' && client) {
        return res.json({ success: true, connected: true, message: 'Already connected.' });
    }
    if (state.pairingMode) {
        await switchToQrMode();
    }
    if (!state.qr) {
        if (!client && !initializing) {
            buildClient(false, true).catch(() => {});
        }
        return res.json({ success: false, error: 'QR not ready yet. Retry in a few seconds.' });
    }
    const dataUrl = await QRCode.toDataURL(state.qr);
    res.json({
        success: true,
        qr: state.qr,
        qr_image: dataUrl,
        updated_at: state.qrUpdatedAt,
    });
});

app.post('/api/start', authMiddleware, async (_req, res) => {
    if (client) {
        await refreshClientInfo();
    }
    if (state.status === 'ready' && client) {
        return res.json({ success: true, status: state.status, message: 'Already connected.' });
    }
    const snapshot = await repairSession();
    res.json({ success: true, message: 'Repair started.', ...snapshot });
});

app.post('/api/repair', authMiddleware, async (_req, res) => {
    const snapshot = await repairSession();
    res.json({ success: true, message: snapshot.message || 'Session repaired without logout.', ...snapshot });
});

/** إصلاح قوي — يقتل Chrome ويعيد تشغيل عملية Node عبر PM2 */
app.post('/api/force-repair', authMiddleware, async (_req, res) => {
    try {
        await prepareForNewSession();
        state.lastError = null;
        state.lastErrorAt = null;
        state.status = 'disconnected';
        res.json({
            success: true,
            restarting: true,
            message: 'تم قتل Chrome العالق — Bridge يُعاد تشغيله الآن. انتظر 15 ثانية.',
        });
        scheduleProcessRestart('api-force-repair');
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/api/send', authMiddleware, async (req, res) => {
    const { phone, message } = req.body || {};
    if (!phone || !message) {
        return res.status(422).json({ success: false, error: 'phone and message are required.' });
    }

    const simulateTyping = req.body.simulate_typing !== false;

    try {
        const result = await enqueueSend(phone, message, simulateTyping);
        res.json({ success: true, ...result });
    } catch (err) {
        res.status(500).json({
            success: false,
            error: err.message,
            status: state.status,
            hint: isProtocolError(err)
                ? 'Bridge session crashed — wait 30s and retry, or restart PM2 on VPS.'
                : undefined,
        });
    }
});

app.post('/api/restart', authMiddleware, async (_req, res) => {
    const snapshot = await repairSession();
    res.json({ success: true, message: 'Bridge restart initiated.', ...snapshot });
});

app.get('/api/pairing-code', authMiddleware, async (_req, res) => {
    if (client) {
        await refreshClientInfo();
    }
    if (state.status === 'ready' && client) {
        return res.json({ success: true, connected: true, message: 'Already connected.' });
    }
    if (!state.pairingMode) {
        return res.json({
            success: false,
            error: 'Pairing not started. POST phone number to /api/pairing-code first.',
        });
    }
    if (!state.pairingCode) {
        return res.json({
            success: false,
            error: 'Pairing code not ready yet. Retry in a few seconds.',
            pairing_phone: state.pairingPhone,
        });
    }
    res.json({
        success: true,
        pairing_code: state.pairingCode,
        pairing_phone: state.pairingPhone,
        updated_at: state.pairingCodeUpdatedAt,
        hint: 'WhatsApp → الأجهزة المرتبطة → ربط جهاز → ربط برقم الهاتف',
    });
});

app.post('/api/pairing-code', authMiddleware, async (req, res) => {
    const { phone } = req.body || {};
    if (!phone) {
        return res.status(422).json({
            success: false,
            error: 'phone is required (country code + number, e.g. 2010xxxxxxxx).',
        });
    }
    if (state.status === 'ready' && client) {
        return res.json({ success: true, connected: true, message: 'Already connected.' });
    }
    try {
        const snapshot = await startPairingMode(phone);
        if (snapshot.last_error) {
            return res.status(503).json({
                success: false,
                error: snapshot.last_error,
                hint: 'Chrome عالق على VPS — نفّذ: bash repair-vps.sh أو pm2 restart mindlytics-whatsapp',
                ...snapshot,
            });
        }
        res.json({
            success: true,
            message: 'Pairing mode started. Poll GET /api/pairing-code for the code.',
            ...snapshot,
        });
    } catch (err) {
        res.status(422).json({ success: false, error: err.message });
    }
});

app.post('/api/qr-mode', authMiddleware, async (_req, res) => {
    if (state.status === 'ready' && client) {
        return res.json({ success: true, connected: true, message: 'Already connected.' });
    }
    const snapshot = await switchToQrMode();
    res.json({ success: true, message: 'Switched to QR mode.', ...snapshot });
});

app.post('/api/logout', authMiddleware, async (_req, res) => {
    try {
        if (client) {
            await client.logout();
        }
    } catch (_) {
        /* ignore */
    }
    await destroyClient();
    cleanupSessionLock();
    state.status = 'disconnected';
    state.qr = null;
    state.phone = null;
    clearPairingState();
    res.json({ success: true, message: 'Logged out.' });
});

function formatParticipantIds(phones) {
    const list = Array.isArray(phones) ? phones : [phones];
    const ids = [];
    for (const phone of list) {
        try {
            const id = formatChatId(phone);
            if (id && !ids.includes(id)) {
                ids.push(id);
            }
        } catch (_) {
            /* skip invalid */
        }
    }
    return ids;
}

async function requireReadyClient() {
    if (state.status !== 'ready' || !client) {
        throw new Error('WhatsApp session not ready. Connect via QR or pairing first.');
    }
    return client;
}

async function getGroupChatByJid(jid) {
    const wa = await requireReadyClient();
    const chat = await wa.getChatById(jid);
    if (!chat || !chat.isGroup) {
        throw new Error('Group not found or invalid JID.');
    }
    return chat;
}

function serializeGroupChat(chat) {
    const id = chat.id?._serialized || chat.id || '';
    return {
        jid: id,
        subject: chat.name || chat.formattedTitle || '',
        description: chat.description || '',
        participant_count: chat.participants?.length || 0,
        announce_only: !!chat.groupMetadata?.announce,
        restrict: !!chat.groupMetadata?.restrict,
        participants: (chat.participants || []).map((p) => ({
            id: p.id?._serialized || p.id,
            is_admin: !!p.isAdmin,
            is_super_admin: !!p.isSuperAdmin,
        })),
    };
}

app.post('/api/groups/create', authMiddleware, async (req, res) => {
    try {
        const { subject, participants, description, announce_only, restrict } = req.body || {};
        if (!subject || !String(subject).trim()) {
            return res.status(422).json({ success: false, error: 'subject is required.' });
        }
        const ids = formatParticipantIds(participants || []);
        if (ids.length < 1) {
            return res.status(422).json({ success: false, error: 'At least one valid participant phone is required.' });
        }
        const wa = await requireReadyClient();
        const created = await wa.createGroup(String(subject).trim(), ids);
        const jid = created?.gid?._serialized || created?.id?._serialized || created;
        const groupChat = await wa.getChatById(jid);
        if (description) {
            try {
                await groupChat.setDescription(String(description));
            } catch (_) {}
        }
        if (announce_only) {
            try {
                await groupChat.setMessagesAdminsOnly(true);
            } catch (_) {}
        }
        if (restrict) {
            try {
                await groupChat.setInfoAdminsOnly(true);
            } catch (_) {}
        }
        let invite_link = null;
        try {
            const code = await groupChat.getInviteCode();
            invite_link = `https://chat.whatsapp.com/${code}`;
        } catch (_) {}
        res.json({ success: true, group: serializeGroupChat(groupChat), invite_link });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.get('/api/groups/:jid', authMiddleware, async (req, res) => {
    try {
        const groupChat = await getGroupChatByJid(req.params.jid);
        let invite_link = null;
        try {
            const code = await groupChat.getInviteCode();
            invite_link = `https://chat.whatsapp.com/${code}`;
        } catch (_) {}
        res.json({ success: true, group: serializeGroupChat(groupChat), invite_link });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.patch('/api/groups/:jid', authMiddleware, async (req, res) => {
    try {
        const { subject, description, announce_only, restrict } = req.body || {};
        const groupChat = await getGroupChatByJid(req.params.jid);
        if (subject && String(subject).trim()) {
            await groupChat.setSubject(String(subject).trim());
        }
        if (description !== undefined) {
            await groupChat.setDescription(String(description || ''));
        }
        if (announce_only !== undefined) {
            await groupChat.setMessagesAdminsOnly(!!announce_only);
        }
        if (restrict !== undefined) {
            await groupChat.setInfoAdminsOnly(!!restrict);
        }
        res.json({ success: true, group: serializeGroupChat(groupChat) });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/api/groups/:jid/participants', authMiddleware, async (req, res) => {
    try {
        const ids = formatParticipantIds(req.body?.participants || []);
        if (!ids.length) {
            return res.status(422).json({ success: false, error: 'participants array required.' });
        }
        const groupChat = await getGroupChatByJid(req.params.jid);
        const result = await groupChat.addParticipants(ids);
        res.json({ success: true, group: serializeGroupChat(groupChat), add_result: result });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.delete('/api/groups/:jid/participants', authMiddleware, async (req, res) => {
    try {
        const ids = formatParticipantIds(req.body?.participants || []);
        if (!ids.length) {
            return res.status(422).json({ success: false, error: 'participants array required.' });
        }
        const groupChat = await getGroupChatByJid(req.params.jid);
        await groupChat.removeParticipants(ids);
        res.json({ success: true, group: serializeGroupChat(groupChat) });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.get('/api/groups/:jid/invite', authMiddleware, async (req, res) => {
    try {
        const groupChat = await getGroupChatByJid(req.params.jid);
        const code = await groupChat.getInviteCode();
        res.json({ success: true, invite_code: code, invite_link: `https://chat.whatsapp.com/${code}` });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/api/groups/:jid/leave', authMiddleware, async (req, res) => {
    try {
        const groupChat = await getGroupChatByJid(req.params.jid);
        await groupChat.leave();
        res.json({ success: true, message: 'Left group.' });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.listen(PORT, () => {
    console.log(`Mindlytics WhatsApp Bridge listening on http://127.0.0.1:${PORT}`);
    if (!API_TOKEN) {
        console.warn('WARNING: Set API_TOKEN in .env before production use.');
    }
    // لا نفتح Chrome تلقائياً — يمنع تعارض browser is already running
    prepareForNewSession()
        .then(() => {
            state.status = 'disconnected';
            console.log('Bridge ready — call /api/repair or /api/pairing-code to connect.');
        })
        .catch((err) => console.error('Startup cleanup error:', err.message));
});
