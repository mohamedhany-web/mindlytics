# ربط Laravel (`Mindlytics`) بطبقة API للموبايل

> تنفَّذ الخطوات **داخل** مشروع `Mindlytics`. عقد OpenAPI والأدلة الحالية: **`docs/platform-api/`** (هذا المجلد).

## 1. تثبيت Laravel Sanctum

```bash
cd "/path/to/Mindlytics"
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

تأكد من وجود جدول `personal_access_tokens` بعد الهجرة.

## 2. تسجيل مسارات API في Laravel 11+

في `bootstrap/app.php` أضف تحميل ملف `routes/api.php` (إذا لم يكن موجودًا، أنشئه من الجذر كما في قالب Laravel):

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',   // أضف هذا السطر
    apiPrefix: 'api',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

أنشئ الملف `routes/api.php` إن لم يكن موجودًا، وابدأ بمسار صحة بسيط:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => ['status' => 'ok', 'service' => 'mindlytics-api']);
});
```

سيصبح العنوان: `GET /api/v1/health`.

## 3. نموذج المستخدم والتوكن

- على المستخدم `HasApiTokens` من Sanctum (انظر وثائق Sanctum).
- مسار تسجيل دخول API يتحقق من البريد وكلمة المرور ثم يعيد `plainTextToken`.

## 4. CORS و Accept

- إعداد `config/cors.php` للسماح بالأصول التي تحتاجها (غالبًا التطبيق الأصلي لا يستخدم CORS؛ المتصفح يستخدمه).
- تأكد أن طلبات الموبايل ترسل `Accept: application/json` حتى يعيد Laravel استجابات JSON للأخطاء (معالجات الاستثناء عندكم تدعم `expectsJson()`).

## 5. مواءمة Flutter

- في التطبيق: استبدال محاكاة تسجيل الدخول بطلب `POST` إلى `/api/v1/auth/login` (بعد تنفيذه).
- خزّن التوكن في `flutter_secure_storage` (أو بديل) وليس `SharedPreferences` للأسرار.

## 6. العقد (OpenAPI)

حدّث **`docs/platform-api/contracts/openapi.yaml`** كلما أضفت مسارًا جديدًا؛ يبقى المرجع بين الفريق والموبايل.
