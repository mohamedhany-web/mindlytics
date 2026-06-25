#!/bin/bash
# إصلاح Bridge بدون مسح جلسة الواتساب (لا logout)
set -e
cd /opt/mindlytics-whatsapp-bridge 2>/dev/null || cd "$(dirname "$0")/.."

echo "Stopping PM2..."
pm2 stop mindlytics-whatsapp 2>/dev/null || true

echo "Killing stale Chromium for wwebjs session..."
pkill -9 -f "wwebjs_auth/session" 2>/dev/null || true
pkill -9 -f "wwebjs_auth" 2>/dev/null || true
pkill -9 -f "mindlytics-whatsapp-bridge" 2>/dev/null || true
sleep 3

echo "Removing lock files..."
rm -f .wwebjs_auth/session/SingletonLock
rm -f .wwebjs_auth/session/SingletonCookie
rm -f .wwebjs_auth/session/SingletonSocket

echo "Starting PM2..."
pm2 start ecosystem.config.cjs 2>/dev/null || pm2 restart mindlytics-whatsapp

sleep 3
curl -s http://127.0.0.1:3001/health || true
echo ""
echo "Done. Refresh WhatsApp admin panel."
