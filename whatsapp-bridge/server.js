/**
 * Mindlytics WhatsApp Bridge
 * ───────────────────────────
 * يعمل على سيرفر Node.js (VPS / Railway / Render / جهاز محلي)
 * لا يمكن تشغيله على Shared Hosting — Laravel يتصل به عبر HTTP.
 */
require('dotenv').config();

const express = require('express');
const cors = require('cors');
const QRCode = require('qrcode');
const { Client, LocalAuth } = require('whatsapp-web.js');

const PORT = parseInt(process.env.PORT || '3001', 10);
const API_TOKEN = process.env.API_TOKEN || '';
const ALLOWED_ORIGINS = (process.env.ALLOWED_ORIGINS || '*').split(',').map((s) => s.trim());

const fs = require('fs');
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
    readyAt: null,
};

let client = null;
let initializing = false;

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

async function buildClient() {
    if (client || initializing) {
        return;
    }
    initializing = true;
    state.lastError = null;

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
    });

    client.on('ready', async () => {
        state.status = 'ready';
        state.qr = null;
        state.readyAt = new Date().toISOString();
        try {
            const info = client.info;
            state.phone = info?.wid?.user || null;
            state.pushname = info?.pushname || null;
            state.platform = info?.platform || null;
        } catch (_) {
            /* ignore */
        }
        console.log('WhatsApp bridge READY', state.phone);
    });

    client.on('disconnected', (reason) => {
        state.status = 'disconnected';
        state.phone = null;
        state.lastError = String(reason || 'disconnected');
        client = null;
        initializing = false;
    });

    try {
        await client.initialize();
    } catch (err) {
        state.status = 'error';
        state.lastError = err.message;
        client = null;
    } finally {
        initializing = false;
    }
}

app.get('/health', (_req, res) => {
    res.json({ ok: true, service: 'mindlytics-whatsapp-bridge', status: state.status });
});

app.get('/api/status', authMiddleware, (_req, res) => {
    res.json({
        success: true,
        status: state.status,
        phone: state.phone,
        pushname: state.pushname,
        platform: state.platform,
        ready_at: state.readyAt,
        qr_pending: state.status === 'qr',
        last_error: state.lastError,
    });
});

app.get('/api/qr', authMiddleware, async (_req, res) => {
    if (state.status === 'ready') {
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
    if (state.status === 'ready') {
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
    if (state.status !== 'ready' || !client) {
        return res.status(503).json({ success: false, error: 'WhatsApp is not connected.', status: state.status });
    }
    try {
        const chatId = formatChatId(phone);
        const chat = await client.getChatById(chatId);

        if (req.body.simulate_typing !== false) {
            const typingMs = 1800 + Math.floor(Math.random() * 3200);
            try {
                await chat.sendStateTyping();
            } catch (_) {
                /* ignore */
            }
            await new Promise((r) => setTimeout(r, typingMs));
        }

        const result = await client.sendMessage(chatId, String(message));
        res.json({
            success: true,
            message_id: result?.id?._serialized || null,
            chat_id: chatId,
        });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/api/logout', authMiddleware, async (_req, res) => {
    try {
        if (client) {
            await client.logout();
            await client.destroy();
        }
    } catch (_) {
        /* ignore */
    }
    client = null;
    initializing = false;
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
