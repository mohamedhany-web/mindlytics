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

function formatChatId(phone) {
    let digits = String(phone || '').replace(/\D/g, '');
    if (digits.startsWith('0')) {
        digits = '20' + digits.slice(1);
    }
    if (!digits.startsWith('20') && digits.length <= 11) {
        digits = '20' + digits;
    }
    return `${digits}@c.us`;
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
    const lockCandidates = [
        './.wwebjs_auth/session/SingletonLock',
        './.wwebjs_auth/session/SingletonCookie',
        './.wwebjs_auth/session/SingletonSocket',
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
        await old.destroy();
    } catch (_) {
        /* ignore */
    }
}

function scheduleReconnect(delayMs = 5000) {
    if (reconnectTimer) {
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

async function buildClient() {
    if (client || initializing) {
        return;
    }
    initializing = true;
    state.lastError = null;
    cleanupSessionLock();

    client = new Client({
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
    });

    client.on('qr', async (qr) => {
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
        if (isBrowserLockError(err)) {
            cleanupSessionLock();
            await destroyClient();
            await sleep(2000);
            initializing = false;
            return buildClient();
        }
        state.status = 'error';
        state.lastError = err.message;
        state.lastErrorAt = new Date().toISOString();
        await destroyClient();
        scheduleReconnect(10000);
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

function connectionSnapshot() {
    const connected = (state.status === 'ready' || state.status === 'degraded') && !!client;

    return {
        success: true,
        status: state.status,
        connected,
        healthy: connected,
        phone: state.phone,
        pushname: state.pushname,
        platform: state.platform,
        ready_at: state.readyAt,
        qr_pending: state.status === 'qr',
        /** يُعرض فقط عند انقطاع الاتصال — لا نُظهر خطأً قديماً والجلسة شغّالة */
        last_error: connected ? null : state.lastError,
        last_error_at: connected ? null : state.lastErrorAt,
        queue_length: sendQueue.length,
    };
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
    if (client && (state.status === 'ready' || state.status === 'degraded')) {
        await refreshClientInfo();
        if (state.status === 'ready' && client) {
            return true;
        }
    }
    if (!client && !initializing) {
        await buildClient();
        await sleep(2000);
    }
    if (client) {
        await refreshClientInfo();
    }
    return state.status === 'ready' && !!client;
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
        await sleep(300);
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
    res.json(connectionSnapshot());
});

app.get('/api/qr', authMiddleware, async (_req, res) => {
    if (client) {
        await refreshClientInfo();
    }
    if (state.status === 'ready' && client) {
        return res.json({ success: true, connected: true, message: 'Already connected.' });
    }
    if (!state.qr) {
        if (!client && !initializing) {
            buildClient().catch(() => {});
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
    await buildClient();
    res.json({ success: true, status: state.status, message: 'Initialization started.' });
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
    try {
        if (client) {
            try {
                await client.destroy();
            } catch (_) {
                /* ignore */
            }
        }
    } catch (_) {
        /* ignore */
    }
    client = null;
    initializing = false;
    state.status = 'disconnected';
    state.phone = null;
    state.pushname = null;
    state.qr = null;
    cleanupSessionLock();
    await sleep(1500);
    await buildClient();
    res.json({ success: true, status: state.status, message: 'Bridge restart initiated.' });
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
    res.json({ success: true, message: 'Logged out.' });
});

app.listen(PORT, () => {
    console.log(`Mindlytics WhatsApp Bridge listening on http://127.0.0.1:${PORT}`);
    if (!API_TOKEN) {
        console.warn('WARNING: Set API_TOKEN in .env before production use.');
    }
    buildClient().catch((err) => console.error('Init error:', err.message));
});
