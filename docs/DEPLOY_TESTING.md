# اختبار النقل إلى app/Domains قبل وبعد الرفع

الهدف: التأكد أن نقل الكود (modularity) وإصلاحات المحاسبة (الدفعة 1) لا يغيّران أي سلوك أو بيانات على الـ production.

## 1. الاختبارات المتاحة

| الأمر | ماذا يفحص | يلمس الداتابيز؟ |
|---|---|---|
| `php artisan test tests/Feature/Architecture` | كل كلاس/‏view/‏route موجود، قوالب Blade تُترجم، الأسماء القديمة المحفوظة (`App\Models\…`) لم تتغير، الـ providers والـ observers مسجلة، أوامر artisan والجدولة مطابقة للقطة (`tests/Snapshots/console.txt`) | لا (sqlite في الذاكرة) |
| `php artisan test` | كل الاختبارات (تشمل الترقيم الآمن والمحافظ) | لا |
| `SMOKE_DATABASE=mindlytics_smoke php artisan test tests/Smoke` | يفتح كل صفحة GET (أكثر من 500 صفحة) بالمستخدم المناسب لها ويتأكد أن لا شيء يرجع 500، ويجرب المسارات الحساسة كاملة: اعتماد طلب (فاتورة + دفعة + حركة محفظة)، المنهج أونلاين/أوفلاين، المجتمع، الكاش القديم | نعم، **على نسخة فقط**، وكل شيء داخل transaction يُتراجع عنه |
| `php artisan modularity:verify` | نفس فحوصات Architecture، كأمر يشتغل على السيرفر | لا |
| `php artisan modularity:verify --db` | + كل قيمة محفوظة في أعمدة `*_type` و `activity_logs.model_type` تُحل لكلاس موجود، وعدد الـ jobs في الطابور | **SELECT فقط** |

اختبارات الـ Smoke ترفض العمل إلا إذا كان اسم القاعدة ينتهي بـ `_smoke`، فلا يمكن تشغيلها على الإنتاج بالخطأ.

تم التأكد أن الفحوصات تلتقط الأعطال فعلاً بكسر الكود عمداً ثم إرجاعه: تغيير مفتاح في MorphMap، مقارنة `item_type` بـ `::class`، حذف provider، view بدون namespace، import لكلاس غير موجود. كلها فشلت كما هو متوقع.

## 2. إنشاء نسخة الـ smoke (محلياً أو على staging، وليس على سيرفر الإنتاج)

```bash
mysqldump --single-transaction --routines --no-tablespaces --set-gtid-purged=OFF -u USER -p mindlytics > /tmp/mindlytics.sql
mysql -u USER -p -e "DROP DATABASE IF EXISTS mindlytics_smoke; CREATE DATABASE mindlytics_smoke CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u USER -p mindlytics_smoke < /tmp/mindlytics.sql
```

الأفضل استخدام نسخة حديثة من الـ production (backup) حتى تختبر نفس البيانات الحقيقية.

## 3. قبل الرفع (على جهازك)

```bash
php artisan test
SMOKE_DATABASE=mindlytics_smoke php artisan test tests/Smoke
php artisan modularity:verify
```

كلها يجب أن تكون خضراء. تقرير كل الصفحات وحالتها يُكتب في `storage/logs/smoke-pages.txt`.

## 4. على السيرفر قبل الرفع (قراءة فقط)

```bash
php artisan modularity:verify --db
```

الكود القديم لا يحتوي الأمر، فشغّله بعد سحب الكود الجديد مباشرة وقبل إعادة تشغيل الـ workers (الخطوة 5).
لو `jobs في الطابور` أكبر من صفر: انتظر تفريغ الطابور أو `php artisan queue:work --stop-when-empty`، لأن الـ jobs المحفوظة فيها أسماء كلاسات (تعمل عبر الأسماء القديمة، لكن الأسلم تفريغها).

## 5. خطوات الرفع

```bash
git pull
composer install --no-dev -o        # أو: composer dump-autoload -o
php artisan optimize:clear
php artisan modularity:verify --db
php artisan optimize                 # config/route/view cache
php artisan queue:restart
```

لا توجد migrations في هذا التغيير، ولا تعديل على أي بيانات.

## 6. بعد الرفع

- افتح: لوحة الأدمن، صفحة كورس كطالب، منهج كورس كمدرب، الطلبات، المحاسبة.
- راقب `storage/logs/laravel.log` وجدول `platform_error_logs` أول ساعة.
- `php artisan schedule:list` يجب أن يعرض نفس الـ 13 مهمة.

## 7. الرجوع (rollback)

```bash
git checkout <commit السابق>
composer dump-autoload -o
php artisan optimize:clear
php artisan cache:clear              # مهم: الكاش (database) قد يحتوي كائنات بأسماء الكلاسات الجديدة
php artisan queue:restart
```

البيانات لم تتغير، فالرجوع آمن بدون أي تعديل على الداتابيز.
