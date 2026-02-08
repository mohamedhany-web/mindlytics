# نشر Laravel على الاستضافة المشتركة باستخدام Git

دليل تطبيق سير العمل: **تطوير محلي → Git → استضافة مشتركة** لمشروع Mindlytics.

---

## المرحلة 1: التطوير المحلي (على جهازك)

### 1. تهيئة Git في المشروع (مرة واحدة فقط)

إذا لم يكن المشروع مستودع Git بعد، نفّذ في مجلد المشروع:

```bash
git init
git add .
git commit -m "Initial commit - Mindlytics Laravel"
```

### 2. ربط المستودع البعيد (GitHub أو GitLab)

- أنشئ مستودعاً جديداً على **GitHub** أو **GitLab** (بدون تهيئة بـ README إن أردت دفع الكود الحالي).
- ثم اربط المشروع المحلي بالمستودع البعيد:

```bash
# استبدل الرابط بمستودعك الفعلي
git remote add origin https://github.com/mohamedhany-web/mindlytics.git
git branch -M main
git push -u origin main
```

### 3. سير العمل اليومي (بعد كل تعديل)

1. **تطوير واختبار** التعديلات محلياً (XAMPP أو بيئة محلية).
2. **حفظ التغييرات في Git:**
   ```bash
   git add .
   git commit -m "وصف التعديلات"
   ```
3. **رفع التغييرات إلى المستودع البعيد:**
   ```bash
   git push
   ```

---

## المرحلة 2: الاستضافة المشتركة (السيرفر)

يُفترض أن الاستضافة تدعم **SSH** أو **Git** (كثير من الاستضافات مثل cPanel تدعمه).

### أ) المرة الأولى: استنساخ المشروع على السيرفر

1. الدخول إلى السيرفر عبر **SSH** أو **Terminal** في cPanel.
2. الانتقال إلى مجلد الويب (غالباً فوق أو داخل `public_html`):

   ```bash
   cd ~/public_html
   # أو المجلد الذي يحدده لك المضيف، مثلاً:
   # cd ~/domains/yourdomain.com
   ```

3. استنساخ المستودع (يُنصح بوضع المشروع في مجلد ثم ربط `public_html` بمجلد `public` لاحقاً):

   ```bash
   git clone https://github.com/YOUR_USERNAME/mindlytics.git .
   ```
   أو استنساخ في مجلد فرعي ثم نقل الملفات حسب إعداد المضيف.

### ب) إعداد ملف .env على السيرفر

1. نسخ الملف النموذجي:
   ```bash
   cp .env.example .env
   ```

2. تعديل `.env` بقيم الإنتاج، مثلاً:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

3. إنشاء مفتاح التطبيق:
   ```bash
   php artisan key:generate
   ```

### ج) عند كل تحديث (بعد كل `git push` من جهازك)

نفّذ على السيرفر داخل مجلد المشروع:

```bash
# 1. جلب آخر التحديثات
git pull

# 2. تحديث الاعتماديات (إن تغيرت)
composer install --no-dev --optimize-autoloader

# 3. تشغيل الهجرات الجديدة (إن وجدت)
php artisan migrate --force

# 4. مسح الكاش وتجهيز التطبيق
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize
```

---

## ملخص أوامر النشر السريع (على السيرفر)

بعد كل `git push` من جهازك، على السيرفر:

```bash
git pull
php artisan migrate --force
php artisan optimize
```

---

## ملاحظات مهمة

| الموضوع | التوصية |
|--------|---------|
| **مجلد public** | على الاستضافة المشتركة غالباً يكون المطلوب أن يكون `public_html` يشير لمحتويات مجلد `public` في المشروع، أو ضبط Document Root ليشير إلى `path/to/mindlytics/public`. |
| **.env** | لا يُرفع إلى Git (موجود في `.gitignore`). أنشئه يدوياً على السيرفر من `.env.example`. |
| **storage & bootstrap/cache** | تأكد من أذونات الكتابة: `chmod -R 775 storage bootstrap/cache` |
| **SSL** | فعّل HTTPS من لوحة الاستضافة وحدّث `APP_URL` في `.env`. |

---

## ربط المجلدات في الاستضافة المشتركة

إذا كان Document Root مضبوطاً على `public_html` فقط:

- ضع المشروع كاملاً في مجلد (مثلاً `~/mindlytics`) ثم:
  - انسخ أو اربط محتويات `~/mindlytics/public` إلى `public_html`، **أو**
  - غيّر Document Root ليشير إلى `~/mindlytics/public` إن سمح المضيف بذلك.

بهذا يصبح سير العمل: **تطوير محلي → commit → push → على السيرفر: git pull + migrate + optimize** كما في الرسم الذي شاركته.
