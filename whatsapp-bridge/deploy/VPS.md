# نشر WhatsApp Bridge على VPS (معزول عن باقي الخدمات)

## الفكرة

```
Hostinger (Laravel)  ──HTTPS──►  wa-api.yourdomain.com  ──►  127.0.0.1:3001  (Node فقط)
```

- Bridge يستمع على **127.0.0.1:3001** (محلي على VPS)
- Nginx يعرض **subdomain** جديد فقط
- **لا يعدّل** إعدادات مواقعك الأخرى

---

## 1) على VPS — مجلد منفصل

```bash
sudo mkdir -p /opt/mindlytics-whatsapp-bridge
sudo chown $USER:$USER /opt/mindlytics-whatsapp-bridge
```

ارفع محتويات مجلد `whatsapp-bridge` من المشروع (بدون `node_modules`):

```bash
# من جهازك (SCP مثال):
scp -r whatsapp-bridge/* user@YOUR_VPS_IP:/opt/mindlytics-whatsapp-bridge/
```

**مهم:** ارفع أيضاً مجلد `whatsapp-web.js-main` بجانب bridge أو عدّل `package.json`:

```json
"whatsapp-web.js": "file:../whatsapp-web.js-main"
```

الأفضل على VPS:

```bash
/opt/
  mindlytics-whatsapp-bridge/   ← server.js, package.json
  whatsapp-web.js-main/         ← المكتبة
```

---

## 2) تثبيت Node (إن لم يكن موجوداً)

```bash
node -v   # يفضل 18+
cd /opt/mindlytics-whatsapp-bridge
npm install
mkdir -p logs .wwebjs_auth
cp .env.example .env
nano .env
```

`.env`:

```env
PORT=3001
API_TOKEN=63579cb1eb62c772233243674aa20b003051797495e17b736a84ebec46709e6b
ALLOWED_ORIGINS=*
```

**غيّر `API_TOKEN` على الإنتاج** لقيمة جديدة وطويلة.

---

## 3) تشغيل معزول بـ PM2

```bash
sudo npm install -g pm2
cd /opt/mindlytics-whatsapp-bridge
pm2 start ecosystem.config.cjs
pm2 status
pm2 logs mindlytics-whatsapp --lines 30
pm2 save
pm2 startup   # اتبع الأمر اللي يطبعه
```

**العزل:**
- process واحد باسم `mindlytics-whatsapp`
- port **3001** فقط (غيّره في `.env` لو مشغول: مثلاً 3010)
- حد ذاكرة 512MB — لو زاد يُعاد تشغيله تلقائياً

تحقق محلي على VPS:

```bash
curl http://127.0.0.1:3001/health
curl -H "Authorization: Bearer YOUR_TOKEN" http://127.0.0.1:3001/api/status
```

---

## 4) Subdomain + Nginx (اللينك العام)

### DNS

في لوحة الدomain أضف:

| النوع | الاسم | القيمة |
|-------|--------|--------|
| A | `wa-api` | IP الـ VPS |

→ الرابط: `https://wa-api.yourdomain.com`

### Nginx

```bash
sudo cp deploy/nginx-whatsapp-bridge.conf /etc/nginx/sites-available/whatsapp-bridge
sudo nano /etc/nginx/sites-available/whatsapp-bridge   # غيّر server_name
sudo ln -s /etc/nginx/sites-available/whatsapp-bridge /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
sudo certbot --nginx -d wa-api.yourdomain.com
```

**لا تلمس** ملفات `sites-enabled` لمواقعك الأخرى.

---

## 5) Firewall (اختياري — أكثر أماناً)

```bash
# افتح 80/443 فقط (غالباً مفتوحين)
# لا تفتح 3001 للعامة — Bridge محلي فقط
sudo ufw allow 80
sudo ufw allow 443
sudo ufw status
```

---

## 6) الربط من Laravel (Hostinger)

### أ) لوحة الأدمن

1. **قسم الواتساب → إعدادات الربط**
2. نوع الخدمة: **whatsapp-web.js Bridge**
3. رابط Bridge: `https://wa-api.yourdomain.com` (بدون `/` في الآخر)
4. التوken: **نفس** `API_TOKEN` في `.env` على VPS
5. احفظ

### ب) أو `.env` على Hostinger

```env
WHATSAPP_TYPE=wwebjs
WHATSAPP_LOCAL_API_URL=https://wa-api.yourdomain.com
WHATSAPP_BRIDGE_TOKEN=نفس_API_TOKEN_على_VPS
WHATSAPP_ENABLED=true
```

### ج) QR والإرسال

1. **لوحة الواتساب** → بدء الاتصال → امسح QR
2. جرب **إرسال رسالة**

---

## 7) Puppeteer على VPS (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install -y chromium-browser fonts-liberation \
  libappindicator3-1 libasound2 libatk-bridge2.0-0 libdrm2 \
  libgbm1 libgtk-3-0 libnspr4 libnss3 libx11-xcb1 libxcomposite1 \
  libxdamage1 libxrandr2 xdg-utils
```

لو Chrome مش موجود، عدّل `server.js`:

```javascript
puppeteer: {
  headless: true,
  executablePath: '/usr/bin/chromium-browser',
  args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'],
}
```

---

## استكشاف الأخطاء

| المشكلة | الحل |
|---------|------|
| Hostinger لا يتصل | تأكد HTTPS + DNS + `curl` من خارج VPS |
| 401 Unauthorized | التوken في Laravel = `API_TOKEN` على VPS |
| QR لا يظهر | `pm2 logs mindlytics-whatsapp` |
| port مشغول | غيّر `PORT` في `.env` + Nginx `proxy_pass` |

---

## تحديث Bridge لاحقاً

```bash
cd /opt/mindlytics-whatsapp-bridge
# ارفع الملفات الجديدة
npm install
pm2 restart mindlytics-whatsapp
```

الجلسة محفوظة في `.wwebjs_auth/` — لا تحذفها إلا لو عايز QR من جديد.
