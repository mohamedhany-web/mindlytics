#!/usr/bin/env bash
# تثبيت WhatsApp Bridge على VPS — معزول عن باقي الخدمات
# التشغيل: bash install-vps.sh
set -euo pipefail

INSTALL_DIR="/opt/mindlytics-whatsapp-bridge"
PORT="${BRIDGE_PORT:-3001}"
REPO_URL="${MINDLYTICS_REPO:-https://github.com/mohamedhany-web/mindlytics.git}"

echo "==> Mindlytics WhatsApp Bridge installer"
echo "    Target: $INSTALL_DIR  |  Port: $PORT (localhost only)"

# ── 1) Node.js 20 ──
if ! command -v node &>/dev/null; then
    echo "==> Installing Node.js 20..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
fi
node -v
npm -v

# ── 2) Chromium dependencies (Puppeteer) ──
echo "==> Installing Chromium dependencies..."
apt-get update -qq
apt-get install -y git curl \
    chromium-browser fonts-liberation libgbm1 libasound2 \
    libatk-bridge2.0-0 libgtk-3-0 libnss3 libxss1 \
    2>/dev/null || apt-get install -y git curl chromium fonts-liberation libgbm1

# ── 3) PM2 ──
if ! command -v pm2 &>/dev/null; then
    echo "==> Installing PM2..."
    npm install -g pm2
fi

# ── 4) Get bridge files ──
mkdir -p "$INSTALL_DIR"
TMP_CLONE=$(mktemp -d)
trap 'rm -rf "$TMP_CLONE"' EXIT

echo "==> Cloning Mindlytics repo (whatsapp-bridge folder)..."
if git clone --depth 1 "$REPO_URL" "$TMP_CLONE/mindlytics" 2>/dev/null; then
    if [ -d "$TMP_CLONE/mindlytics/whatsapp-bridge" ]; then
        rsync -a --delete \
            --exclude node_modules \
            --exclude .wwebjs_auth \
            --exclude logs \
            "$TMP_CLONE/mindlytics/whatsapp-bridge/" "$INSTALL_DIR/"
    else
        echo "ERROR: whatsapp-bridge not found in GitHub repo."
        echo "Push whatsapp-bridge from your PC first, then re-run this script."
        exit 1
    fi
else
    echo "ERROR: git clone failed. Check internet / repo URL."
    exit 1
fi

cd "$INSTALL_DIR"
mkdir -p logs .wwebjs_auth

# ── 5) package.json — npm package (not local folder) ──
if grep -q 'file:../whatsapp-web.js-main' package.json 2>/dev/null; then
    sed -i 's|"whatsapp-web.js": "file:../whatsapp-web.js-main"|"whatsapp-web.js": "^1.26.0"|' package.json
fi

# ── 6) .env ──
if [ ! -f .env ]; then
    TOKEN=$(openssl rand -hex 32)
    cat > .env <<EOF
PORT=$PORT
API_TOKEN=$TOKEN
ALLOWED_ORIGINS=*
EOF
    echo ""
    echo "=============================================="
    echo "  SAVE THIS TOKEN — put it in Laravel admin:"
    echo "  $TOKEN"
    echo "=============================================="
    echo ""
else
    echo "==> .env exists — keeping current API_TOKEN"
    grep API_TOKEN .env || true
fi

# ── 7) npm + PM2 ──
echo "==> npm install (may take a few minutes)..."
npm install --omit=dev

echo "==> Starting with PM2..."
pm2 delete mindlytics-whatsapp 2>/dev/null || true
pm2 start ecosystem.config.cjs
pm2 save
pm2 startup systemd -u root --hp /root 2>/dev/null || pm2 startup

sleep 3
echo "==> Health check:"
curl -s "http://127.0.0.1:$PORT/health" || echo "(wait a few seconds and retry)"

echo ""
echo "DONE. Bridge runs on 127.0.0.1:$PORT only."
echo "Next: configure Nginx subdomain → proxy_pass http://127.0.0.1:$PORT"
echo "See: $INSTALL_DIR/deploy/VPS.md"
