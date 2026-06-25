#!/bin/bash
# إصلاح Chrome العالق + إعادة تشغيل Bridge — نفّذ على VPS كـ root أو مستخدم PM2
set -e
cd /opt/mindlytics-whatsapp-bridge 2>/dev/null || cd "$(dirname "$0")"

echo "=== Mindlytics WhatsApp Bridge — Force Repair ==="

echo "[1/5] Stopping PM2..."
pm2 stop mindlytics-whatsapp 2>/dev/null || true
sleep 2

echo "[2/5] Killing all stale Chromium/Chrome..."
pkill -9 -f "wwebjs_auth/session" 2>/dev/null || true
pkill -9 -f "wwebjs_auth" 2>/dev/null || true
pkill -9 -f "mindlytics-whatsapp-bridge" 2>/dev/null || true
pkill -9 -f "chromium.*wwebjs" 2>/dev/null || true
sleep 2

echo "[3/5] Removing lock files..."
rm -f .wwebjs_auth/session/SingletonLock
rm -f .wwebjs_auth/session/SingletonCookie
rm -f .wwebjs_auth/session/SingletonSocket
rm -f .wwebjs_auth/session/lockfile
fuser -k .wwebjs_auth/session 2>/dev/null || true
sleep 2

echo "[4/5] Starting PM2..."
pm2 start ecosystem.config.cjs 2>/dev/null || pm2 restart mindlytics-whatsapp
sleep 4

echo "[5/5] Health check..."
curl -s http://127.0.0.1:3001/health || true
echo ""
echo "Done. Wait 10s then open admin/whatsapp and click repair or request pairing code."
