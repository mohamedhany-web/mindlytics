# Mindlytics Platform API — وثائق وعقود داخل المشروع

هذا المجلد جزء من **`Mindlytics/`** (نفس مستودع الموقع). يضم **عقد OpenAPI** و**أدلة الربط**؛ التنفيذ البرمجي للـ API يبقى في **`routes/api.php`** و**`app/Http/Controllers/Api/V1/`**.

## المكوّنات

| جزء | المسار | الدور |
|-----|--------|--------|
| **الموقع (ويب)** | `routes/web.php` + Blade | جلسة المتصفح |
| **الـ API (JSON)** | `routes/api.php` تحت `/api/v1` | توكن Sanctum + JSON |
| **العقد والأدلة** | `docs/platform-api/` | مرجع للفريق والموبايل |

## الملفات هنا

- `contracts/openapi.yaml` — عقد المسارات (حدّثه عند إضافة endpoints).
- `docs/architecture.md` — مخطط المنصة.
- `docs/laravel-integration.md` — خطوات Sanctum و`api.php` (تاريخي/مرجعي).
- `docs/nujz-raf-api-domen-far3i.md` — نشر على استضافة مشتركة (دومين فرعي اختياري).

## تشغيل على الخادم (داخل جذر `Mindlytics`)

```bash
composer update
php artisan migrate
```

يتطلّب **`laravel/sanctum`** وجدول **`personal_access_tokens`** لتسجيل الدخول من التطبيق.

## Flutter — عنوان الخادم

```bash
flutter run --dart-define=WEB_BASE_URL=https://mindlytics-academy.com
```

أوفلاين للواجهة فقط:

```bash
flutter run --dart-define=OFFLINE_MODE=true
```
