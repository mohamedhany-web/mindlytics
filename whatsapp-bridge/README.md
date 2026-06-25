# Mindlytics WhatsApp Bridge

HTTP bridge between **Laravel (shared hosting)** and **whatsapp-web.js** (Node.js + Puppeteer).

Laravel cannot run Puppeteer on Hostinger shared hosting. Run this service on:

- A VPS (recommended)
- Railway / Render / Fly.io
- Your local PC (with ngrok for production Laravel to reach it)

## Quick start

```bash
cd whatsapp-bridge
npm install
cp .env.example .env
# Edit API_TOKEN to a long random secret
npm start
```

Default port: **3001**

## API (Bearer token required)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/health` | Public health check |
| GET | `/api/status` | Connection status |
| GET | `/api/qr` | QR code (base64 image) |
| GET | `/api/pairing-code` | Current pairing code (phone linking) |
| POST | `/api/pairing-code` | `{ "phone": "2010..." }` — start pairing mode |
| POST | `/api/qr-mode` | Switch back to QR mode |
| POST | `/api/start` | Start / re-init client |
| POST | `/api/send` | `{ "phone": "010...", "message": "..." }` |
| POST | `/api/logout` | Disconnect session |

Header: `Authorization: Bearer YOUR_API_TOKEN`

## Laravel admin

1. Open **Admin → قسم الواتساب → إعدادات الربط**
2. Service type: **whatsapp-web.js Bridge**
3. Bridge URL: `https://your-node-server:3001`
4. Token: same as `API_TOKEN` in bridge `.env`
5. Scan QR **or** use pairing code from **لوحة الواتساب**

See also **Admin → قسم الواتساب → التوثيق** for full Arabic guide (anti-ban, env vars, troubleshooting).

Session is stored in `.wwebjs_auth/` on the Node server (persists after restart).

## Production notes

- Use **PM2** or systemd to keep the process alive
- Restrict firewall; only Laravel server IP should reach the bridge if possible
- WhatsApp Web unofficial API — use at your own risk for business messaging
