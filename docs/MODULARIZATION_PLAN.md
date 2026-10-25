# خطة تحويل Mindlytics إلى Domain Modules

> الحالة: مسودة للنقاش · تاريخ الإعداد: 2026-10-22

## 1. الوضع الحالي (أرقام فعلية من الكود)

| العنصر | العدد | الملاحظة |
|---|---|---|
| Models | 232 | كلها في `app/Models` بلا تقسيم |
| Services | 116 | 7 مجلدات فقط مقسّمة (Hr, Investment, Scholarship, MetaSocial, MetaAds, Community, Branch) |
| Controllers | 375 | `Admin` وحده 169، وأكبرها `AccountingReportsController` (1262 سطر) |
| Views | — | `resources/views/admin` فيه 108 عنصر |
| Routes | ~1500 | `admin.php` وحده فيه 851 تعريف Route |
| Console Commands | 26 | مختلطة بين HR وSales والمحاسبة والكورسات |
| Tests | 49 ملف | تغطية قليلة جداً مقارنة بحجم المشروع |

**عدد الملفات خارج كل مجال التي تستخدم Models هذا المجال (مؤشر الترابط):**

| المجال | ملفات خارجية تعتمد عليه | التقييم |
|---|---|---|
| Investment | 0 | معزول تماماً |
| Scholarship | 1 | شبه معزول |
| HR | ~20 | ترابط متوسط (أساساً مع Sales وUser) |
| Sales | ~22 | كبير لكنه متماسك داخلياً |
| Accounting | ~49 | **أكثر مجال مترابط** (Orders/Payments يستخدمها الكل) |

> ⚠️ بناءً على الأرقام: المحاسبة **ليست** المجال الأسهل للبدء رغم وضوحها في الذهن. نبدأ بـ Investment كتجربة، ثم HR، والمحاسبة تأتي لاحقاً.

---

## 2. الهيكل المستهدف

```
app/
├── Domains/
│   ├── Hr/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/
│   │   │   │   └── Employee/
│   │   │   ├── Middleware/
│   │   │   └── Requests/
│   │   ├── Console/Commands/
│   │   ├── Observers/
│   │   ├── Jobs/
│   │   ├── Events/            # للتواصل مع المجالات الأخرى
│   │   ├── routes/
│   │   │   ├── admin.php
│   │   │   └── employee.php
│   │   ├── resources/views/   # namespace: hr::
│   │   └── HrServiceProvider.php
│   ├── Accounting/
│   ├── Sales/
│   └── ...
├── Shared/   (أو تبقى في app/ كما هي = Shared Kernel)
│   ├── Models/User.php, Role, Permission, Branch ...
│   └── Support/ Helpers/ Middleware العامة
```

**قرارات تقنية:**
- الـ namespace هو `App\Domains\Hr\...`، وهو يعمل تلقائياً مع `"App\\": "app/"` الموجود في `composer.json`، **فلا نحتاج لأي package** (لا `nwidart/laravel-modules`).
- كل Domain له `ServiceProvider` يسجّل: routes و views (`loadViewsFrom(..., 'hr')`) و commands و observers والـ schedule الخاص به.
- الـ Providers تُسجَّل في `bootstrap/providers.php`.
- **`User` يبقى في `App\Models\User`** نهائياً (يستخدمه `config/auth.php` و Sanctum `tokenable_type`)، وكذلك `Role`, `Permission`, `Branch`, `ActivityLog`, `Notification`.
- الـ migrations تبقى في `database/migrations` (لا نحرّك تاريخ الـ migrations).

---

## 3. خريطة المجالات

### Shared Kernel (يبقى في `app/` كما هو)
- **Models:** User, Role, Permission, Branch, ActivityLog, Notification, TwoFactorLog, PlatformErrorLog, GeoIpLookup, MobileAppSetting
- **Services:** SecurityService, PlatformErrorLogger, GeoIpLookupService, Branch/BranchResolver, UserDeletionService, StatisticsCacheService, ExcelExportService
- **Middleware العامة:** Security, SetLocale, CheckRole, EnsurePermission, ResolveBranchFromHost …
- Controllers: Auth/*, DashboardController, Admin/{Role, Permission, UserPermission, ActivityLog, TwoFactorLog, PlatformErrorLog, SystemSettings, Branch, Profile}

### Investment ⭐ (المرحلة 1 – تجربة)
- Models: InvestmentInquiry, InvestmentPlan, InvestmentPolicy
- Services: Investment/*
- Controllers: Admin/Investment/*, Public/InvestmentController

### Scholarship (المرحلة 1) ✅ تم – انظر القسم 11
- Models: ScholarshipGroup, ScholarshipProgram, ScholarshipRegistration
- Services: Scholarship/*
- Controllers: Admin/Scholarship/*, Instructor/Scholarship/*, Instructor/ScholarshipController, Public/ScholarshipRegistrationController
- Views: `resources/views/scholarships`

### Hr (المرحلة 2) ✅ تم – انظر القسم 12 (القائمة أدناه هي الخطة الأصلية؛ التصنيف النهائي في القسم 12)
- **Models:** EmployeeAgreement, EmployeeAttendanceRecord, EmployeeDailyReport, EmployeeJob, EmployeePresenceDaily, EmployeePresenceViolation, EmployeeSalaryAddition, EmployeeSalaryDeduction, EmployeeSalaryPayment, EmployeeTask, EmployeeTaskDeliverable, EmployeeWorkUnlock, LeaveRequest, WorkSchedule, TeamsAttendanceFile, HrApplicationFile, HrApplicationScore, HrApplicationSkill, HrJobApplication, HrJobPosting, HrRubric, Internship, InternshipApplication
- **Services:** Employee* (Attendance, AttendancePenalty, DailyReport, Payroll, Presence, MonthlyPerformance*, Task*, Deliverable*), Hr/AtsScoringService, Hr/CVParserService
- **Controllers:**
  - Admin: Employee*, AdminLeave, Salary, EmployeeSalaryPayroll, EmployeeJob, WorkSchedule, Attendance (الموظفين), Performance, Internship*, Hr/*
  - Employee: Employee*, EmployeeLeave, EmployeeCalendar, EmployeeAttendance, EmployeePresence, EmployeeJob, Agreement
  - Public: CareersController, InternshipController
- **Commands:** ApplyEmployeeAttendancePenalties, ApplyEmployeeDailyReportPenalties, CheckEmployeePresence, CleanupOutOfEmploymentDeductions, RemindEmployeeDailyReport
- **Observers:** EmployeeSalaryDeductionObserver
- **Middleware:** EnsureEmployeeWorkAccess
- **نقاط الترابط:** Sales يستخدم Attendance/Deductions (SalesDailyKpiPenalty, SalesShiftScheduleService)، Marketing يستخدم WorkSchedule → نعرض لهم واجهة عبر `Hr\Services\*` بدلاً من الوصول المباشر للـ Models.

### Accounting / Finance (المرحلة 4) ✅ تم – انظر القسم 14
- **Models:** AccountingDebt, AccountingDebtRepayment, AgreementPayment, Expense, InstallmentAgreement, InstallmentPayment, InstallmentPlan, Invoice, Order, Payment, Transaction, Wallet, WalletReport, WalletTransaction, WithdrawalRequest, Coupon, CouponUsage, Subscription, Package, InstructorAgreement, InstructorPayoutDetail
- **Services:** AccountingComprehensiveExcelExport, AutoInstallmentAgreement, FawaterakService, KashierService, GatewayFeeCalculator, PaymentGatewaySettings, TransactionRefund, CustomerDiscount, InstructorCoursePercentage
- **Controllers:** Admin/Accounting*, Expense, Installment*, Invoice, Order, Payment, Transaction, Wallet, WithdrawalRequest, Coupon, Subscription, Package, InstructorAgreement, OfflineAgreement; Employee/AccountingController; Public/CheckoutController, FawaterkPluginController; Branch/BranchPayments, BranchOrders, BranchInvoices; Student/Order, Invoice, Wallet, Coupon; Instructor/WithdrawalRequest, TransferAccount
- **Commands:** ProcessInstallmentPayments, SyncInstructorAgreementPayments
- **ملاحظة:** تقسيم الـ controllers الضخمة (`AccountingReportsController`, `ReportsController`) إلى controllers أصغر + Query/Report services جزء من هذه المرحلة.

### Places (مع المرحلة 4 أو منفصل) ✅ تم – انظر القسم 14
- Models: PlaceDailyExpense, PlaceInvoice, PlaceMonthlySettlement, PlaceUsageLog
- Services: PlaceSettlementService · Controllers: Place/*, Admin/Place*
- Views: `place-office`

### Sales (المرحلة 3 – الأكبر) ✅ تم – انظر القسم 13
- **Models:** كل `Sales*` (~40 model)
- **Services:** كل `Sales*` (~45 service)
- **Controllers:** Admin/Sales*, Employee/Sales*, Employee/SalesManager*, Public/SalesCourseBoardLanding, SalesDiplomaBoardLanding
- **Commands:** ApplySalesDailyKpiPenalties, ApplySalesDailyReportPenalties, EnforceSalesImportContact, EvaluateSalesEnforcement, RemindSalesDailyReport, SendSalesFollowupReminders
- **Middleware:** EnsureSalesEmployee, EnsureSalesManager, EnsureSalesStaff
- Views: `resources/views/sales`
- **اقتراح:** داخل الـ module نقسّم فرعياً: `Leads/`, `Shifts/`, `Kpi/`, `Commission/`, `Reports/`, `TeamChat/`.

### Messaging (WhatsApp + Meta) ✅ تم – انظر القسم 13
- Models: WhatsApp*, MetaSocial*, MessageTemplate
- Services: WhatsApp*, MetaSocial/*, MetaAds/*, ExcelWhatsAppCampaign, WorkshopWhatsApp*
- Controllers: Admin/WhatsApp*, MetaSocial*, MetaAds*, ExcelWhatsApp; Employee/Sales*Inbox, SalesWhatsAppQueue; WhatsAppWebhookController, MetaSocialWebhookController; Concerns/Handles*Inbox
- Jobs: ProcessWhatsAppBatchJob · Helper: WhatsAppHelper.php
- ⚠️ **الـ Webhooks يجب أن تبقى على نفس الـ URLs** (مسجّلة عند Meta) ونفس استثناء CSRF.

### Marketing ✅ تم – انظر القسم 15
- Models: AdvertisingCampaign, CampaignDailyReport, MarketingCustomerSurvey, MarketingRegionDailyStat, ModeratorMarketing*, ModeratorMontageRequest, DesignTaskCycle, DesignCycleModeratorPlannerItem, PopupAd, Referral, ReferralProgram, LoyaltyProgram
- Services: Marketing*, CampaignReport, CustomerSurveyReward, Referral, MontageDeliverablesExcelImport
- Commands: ApplyMarketingPlanExecutionPenalties, RemindMarketingPlanEvents
- Middleware: EnsureModeratorEmployee, RecordMarketingRegionVisit

### Learning (الكورسات الأونلاين + التقييم) ✅ تم – انظر القسم 16
- **Courses:** AdvancedCourse, Course, CourseSection, CourseLesson, CurriculumItem, Lecture*, Lesson, LessonProgress, CourseEnrollment, StudentCourseEnrollment, CourseActivation, CourseReview, LearningPath*, LearningPattern*, Video*, Certificate, CertificateBranding, InstructorProfile, InstructorRequest
- **Assessment:** Exam, ExamAttempt, ExamQuestion, AdvancedExam, AdvancedQuestion, Question, QuestionBank, QuestionCategory, Assignment, AssignmentSubmission
- **Academic:** AcademicSubject, AcademicYear, Subject, School, Classroom
- Observers: AdvancedCourse, Assignment, Enrollment, ExamAttempt, Exam, LectureAssignment, Lecture
- هذا قلب المنصة وأكثرها حساسية → **آخر مرحلة**.

### Offline (الكورسات الحضورية + الورش) ✅ تم – انظر القسم 16
- Models: Offline*, AttendanceRecord, AttendanceStatistics, OfflineLocation, Workshop*
- Services: WorkshopAttendance, WorkshopPromo*

### Community ✅ تم – انظر القسم 15
- Models: Community*, ContributorProfile, CourseCommunity*, Group*, PeerChat*, PeerConnectionRequest, DirectMessage*, Badge, Achievement, UserAchievement, UserBadge, Journey*, PortfolioProject*
- Services: Community/*, Journey*
- Controllers: Community/*, Public/Community*, Admin/Community*

### Support / CMS ✅ تم – انظر القسم 15
- Models: SupportTicket*, ContactMessage, FAQ, BlogPost, MediaGallery, CalendarEvent, StudentReport

---

## 4. المرحلة 0 – التحضير (إلزامية قبل نقل أي ملف)

> ✅ **تم التنفيذ (2026-10-22):** البنود 1 و 3 و 5 و 6 و 7. البنود 2 و 8 و 9 إجراءات نشر/مراجعة تتكرر مع كل مرحلة.
>
> | الملف | الغرض |
> |---|---|
> | `app/Support/MorphMap.php` | خريطة الـ polymorphic types بمفاتيح = القيم المحفوظة حالياً |
> | `app/Support/LegacyClassAliases.php` | aliases كسولة للأسماء القديمة + `resolve()` لقيم الداتابيز |
> | `app/Providers/AppServiceProvider.php` | تسجيل الاثنين في `register()` |
> | `app/Models/ActivityLog.php` | `model()` يحل الأسماء القديمة ولا ينهار مع قيم مثل `'User'` |
> | `tests/Feature/Architecture/RouteSnapshotTest.php` + `tests/Snapshots/routes.txt` | snapshot لـ 1850 route (method, uri, name, middleware, الترتيب) |
> | `tests/Feature/Architecture/RouteControllersExistTest.php` | كل route يشير لـ controller/method موجود |
> | `tests/Unit/MorphMapAndAliasesTest.php` | سلامة الخريطة والـ aliases |
>
> **اكتُشف أثناء التنفيذ:** 10 routes قائمة حالياً تشير إلى methods غير موجودة (ترجع 500) – مسجّلة في `KNOWN_BROKEN` داخل `RouteControllersExistTest` وتحتاج إصلاحاً منفصلاً.

هذه المخاطر **ستكسر الإنتاج** لو تجاهلناها:

1. **Polymorphic relations تخزّن اسم الكلاس في الداتابيز.**
   `CurriculumItem::item`, `OfflineCurriculumItem::item`, `CourseCommunityReaction::reactable` تحفظ `App\Models\Lecture` إلخ في عمود `*_type`. لو نقلنا الـ Model تنكسر البيانات القديمة.
   → **القرار المنفّذ:** `Relation::morphMap` بمفاتيح هي نفس النصوص المحفوظة (`'App\Models\Lecture' => Lecture::class`)، **بدون migration للبيانات**. السبب: `admin/curriculum/builder.blade.php` يرسل `'App\\Models\\Lecture'` من الـ JS، و `CurriculumController` يتحقق بـ `in:App\Models\Lecture,...`، وعشرات الاستعلامات تقارن `item_type` بـ `X::class`؛ تحويل القيم لأسماء مختصرة كان سيكسرها كلها الآن. عند نقل الـ Model يتغير الـ value في الخريطة فقط ويبقى المفتاح.
   (`enforceMorphMap` غير مستخدم لأن `activity_logs.model_type` يحفظ أسماء كلاسات خارج الخريطة.)
   للتحقق على الإنتاج قبل نقل أي Model من Learning/Community:
   ```sql
   SELECT DISTINCT item_type FROM curriculum_items;
   SELECT DISTINCT item_type FROM offline_curriculum_items;
   SELECT DISTINCT reactable_type FROM course_community_reactions;
   ```
   كل قيمة يجب أن تكون مفتاحاً في `MorphMap::map()`.
2. **الـ Queue = database:** الـ jobs المعلّقة فيها اسم الكلاس serialized. → قبل نشر أي مرحلة تنقل Job: نفرّغ الـ queue (`php artisan queue:work --stop-when-empty`).
3. **159 Blade view تكتب `App\Models\...` مباشرة.** → نستخدم `class_alias` مؤقت (انظر الخطوة 5) ثم نحدّثها تدريجياً.
4. **Sanctum / auth:** `User` لا يتحرك (محسوم في القسم 2).
5. **Aliases للتوافق:** `app/Support/LegacyClassAliases.php` – نضيف سطراً في `MAP` لكل كلاس منقول:
   ```php
   'App\Models\EmployeeTask' => \App\Domains\Hr\Models\EmployeeTask::class,
   ```
   الـ alias يُنشأ عند أول استخدام للاسم القديم فقط. يسمح بنقل الملف دون كسر أي مرجع قديم (بما فيها الـ jobs المسلسلة و `activity_logs.model_type`)، ثم نحذف السطر بعد تحديث كل المراجع.
   ⚠️ `activity_logs` تحفظ أسماء الكلاسات للأبد → في المرحلة 7 لا نحذف السطر من `MAP` بل نُبقيه لـ `resolve()` (أو ننقل الخريطة لـ `config`).
6. **رفع التغطية بالاختبارات:** قبل كل مرحلة نكتب smoke tests لكل route في المجال (status 200/302 للمستخدم المناسب). اختبار عام واحد يمرّ على `Route::getRoutes()` ويتأكد أن كل controller موجود يحمي من أي خطأ namespace.
7. **Snapshot للـ routes:** حفظ `php artisan route:list --json` قبل كل مرحلة ومقارنته بعدها. **أسماء الـ routes والـ URLs لا تتغير أبداً** (الـ views تستخدم `route('admin.…')`).
8. **النشر على cPanel:** بما أن الرفع zip مع `vendor`، يجب تشغيل `composer dump-autoload -o` محلياً قبل الضغط في كل مرحلة، و `php artisan optimize:clear` على السيرفر بعدها.
   ⚠️ **الكاش (CACHE_STORE=database) يحفظ Models مسلسلة بأسماء كلاساتها:** الكود الجديد يقرأ الأسماء القديمة عبر الـ aliases (تم التحقق)،
   لكن **الرجوع لنسخة قديمة** بعد النشر يكسر الصفحات التي تقرأ كاشاً كتبه الكود الجديد → بعد أي rollback: `php artisan cache:clear` فوراً.
9. **ترتيب الـ routes:** التعليق في `admin.php` يقول "Route order is part of matching behavior" → عند فصل routes المجال نحافظ على نفس الترتيب النسبي ونفس الـ prefix/middleware، ونتحقق بالـ snapshot.

---

## 5. خطوات نقل أي Domain (Playbook يتكرر)

> المرجع المكتمل: `app/Domains/Investment` (انظر القسم 10).

0. **حصر قبل النقل:** كل ملفات المجال + كل مرجع لها خارجها، بما فيها:
   - `View::composer` / `View::share` في `AppServiceProvider` المربوطة **بأسماء views** (مثل `admin.investment.*`) – هذه لا تظهر عند البحث عن أسماء الكلاسات وتكسر الصفحات بـ `Undefined variable`.
   - ملفات بها تعديلات غير محفوظة (`git status`) – انقلها بـ `git mv` حتى تبقى التعديلات.
1. إنشاء `app/Domains/X/` و `XServiceProvider` وتسجيله في `bootstrap/providers.php`.
2. نقل الملفات بـ `git mv` (يحفظ التاريخ). ثم:
   - Models: تغيير الـ namespace + **إضافة `use App\Models\User;`** (وأي Model آخر من `App\Models`)؛ كانت تستخدمه بدون import لأنها في نفس الـ namespace.
   - Services و Controllers: تغيير الـ namespace والـ imports.
   - سطر لكل Model و Service في `LegacyClassAliases::MAP`.
   - أسماء الجداول والـ foreign keys المستنتجة (`investment_plans`, `investment_plan_id`) تعتمد على اسم الكلاس لا الـ namespace فلا تتغير – تحقق منها مرة.
3. Routes: ملفات `app/Domains/X/routes/{public,admin,...}.php` تحتوي نفس الـ group بنفس الـ prefix والأسماء، وتُستدعى بـ `require app_path('Domains/X/routes/admin.php');` **في نفس موضع الكود القديم** داخل `routes/web/*.php` (فترث مجموعة admin والترتيب). لا نحمّلها من الـ Provider.
4. Views: نقل إلى `Domains/X/resources/views` + `loadViewsFrom(..., 'x')`، ثم تحويل `view('admin.x.…')` و `@include/@extends('admin.x.…')` إلى `'x::admin.…'`.
   ⚠️ **لا تستبدل النص `'admin.x.` بشكل عام**: نفس النص موجود في أسماء الـ routes (`route('admin.x.plans.update')`). الاستبدال فقط داخل `view(` و `@include(` و `@extends(`.
   ⚠️ أي `View::composer` على الاسم القديم يُنقل إلى `XServiceProvider` بالاسم الجديد (`'x::admin.*'`).
   أي `\App\Models\X` داخل الـ Blade يُحدَّث للـ namespace الجديد.
5. نقل Commands و Observers والـ schedule (إن وُجدت).
6. `composer dump-autoload` محلياً (وقبل ضغط ملفات النشر على cPanel).
7. تحديث كل المراجع بـ search/replace (`use App\Models\EmployeeTask` → `use App\Domains\Hr\Models\EmployeeTask`).
   - لو الـ Model في `MorphMap`: حدّث الـ value فقط (لا تغيّر المفتاح)، وأي `->where('item_type', X::class)` أو `'reactable_type' => X::class` في ملف حدّثت imports فيه تصبح `Relation::getMorphAlias(X::class)`.
8. تشغيل: `php artisan test --filter='RouteSnapshotTest|RouteControllersExistTest|MorphMapAndAliasesTest'` (يجب أن تمر **بدون** تحديث الـ snapshot) + باقي الاختبارات + `php artisan view:cache` (يكشف أي view مفقود) + فتح الصفحات الرئيسية يدوياً.
9. commit/PR منفصل لكل Domain، نشر، مراقبة `PlatformErrorLog` يومين.
10. بعد الاستقرار: حذف الـ aliases الخاصة بالمجال.

---

## 6. قواعد التواصل بين المجالات

- ممنوع أن يستدعي Domain كلاس داخلي من Domain آخر مباشرة، **إلا** الـ Models من Shared Kernel.
- المسموح:
  1. استدعاء **Service عامة** (Public API) للمجال الآخر، مثلاً `Hr\Services\EmployeeAttendanceService`.
  2. **Events** للتأثيرات الجانبية: مثلاً `OrderPaid` يطلقه Accounting ويستمع له Learning لتفعيل الكورس، و Sales لحساب العمولة.
- علاقات Eloquent عبر المجالات (`belongsTo(Course::class)` من Order) مقبولة مؤقتاً، ونوثّقها كـ "ديون" نقلّلها مع الوقت.
- علاقات `User` الكثيرة (154 method في `User.php`، 1552 سطر): نفصلها إلى traits داخل كل Domain، مثلاً `App\Domains\Hr\Concerns\HasEmployeeRecords` ثم `use` في User.
- (اختياري لاحقاً) أداة مثل `deptrac` أو اختبار PHPUnit يفحص الـ imports ويمنع الاعتماديات الممنوعة.

---

## 7. ترتيب المراحل والجدول المقترح

| المرحلة | المحتوى | السبب | الحجم التقريبي |
|---|---|---|---|
| **0** | التحضير: morphMap، aliases، tests، route snapshot | بدونها النقل خطر | 3–5 أيام |
| **1** | Investment ثم Scholarship | معزولة (0–1 مرجع خارجي) → تجربة الـ Playbook بأقل مخاطرة | 2–3 أيام |
| **2** | Hr | مجال واضح، ~20 مرجع خارجي، فيه commands وpenalties | 1–1.5 أسبوع |
| **3** | Sales + Messaging | الأكبر حجماً لكن متماسك؛ Messaging مرتبط بـ Sales بقوة | 2–3 أسابيع |
| **4** | Accounting + Places | الأكثر ترابطاً → يحتاج Events (`OrderPaid`...) وتقسيم الـ controllers الضخمة | 2–3 أسابيع |
| **5** | Marketing, Community, Support/CMS | متوسطة | 1–2 أسبوع |
| **6** | Offline ثم Learning | قلب المنصة، بعد أن يكون النمط ثابتاً | 2–3 أسابيع |
| **7** | تنظيف: حذف كل الـ aliases، فصل traits من User، قواعد الاعتماديات | — | أسبوع |

> المبدأ: **كل مرحلة قابلة للنشر وحدها**، والمنصة تعمل بشكل طبيعي بين المراحل. لا يوجد "big bang rewrite".

---

## 8. تعريف "انتهى" لكل Domain

- [ ] لا يوجد ملف للمجال في `app/Models`, `app/Services`, `app/Http/Controllers` (عدا الـ aliases المؤقتة)
- [ ] routes المجال في مجلده، وكل أسماء الـ routes والـ URLs كما كانت (مقارنة الـ snapshot = صفر فروقات)
- [ ] views بـ namespace خاص (`hr::`)
- [ ] commands والـ schedule مسجّلة من الـ Provider
- [ ] smoke tests لكل routes المجال تمر
- [ ] لا imports لـ internals من مجالات أخرى (عدا الديون الموثّقة)
- [ ] تم النشر ومراقبة الأخطاء 48 ساعة

---

## 9. خطوة البداية المقترحة

1. ✅ المرحلة 0: `MorphMap` + `LegacyClassAliases` + route snapshot + اختبار وجود الـ controllers.
2. ✅ `app/Domains/Investment` كاملاً بالـ Playbook (نموذج مرجعي لباقي الفريق).
3. ✅ مراجعة الـ Playbook بعد Investment (القسم 5 محدَّث بالدروس).
4. ✅ `app/Domains/Scholarship` (القسم 11).
5. ✅ `app/Domains/Hr` (القسم 12).
6. ✅ المرحلة 3: `app/Domains/Sales` + `app/Domains/Messaging` (القسم 13).
7. ✅ المرحلة 4: `app/Domains/Accounting` + `app/Domains/Places` (القسم 14) + مراجعة الحسابات (`docs/ACCOUNTING_AUDIT.md`).
8. ✅ دفعة إصلاحات المحاسبة 1 (`docs/ACCOUNTING_AUDIT.md`)؛ الدفعة 2 (فواتيرك) بانتظار استعلام من الإنتاج.
9. ✅ المرحلة 5: `app/Domains/Marketing` + `app/Domains/Community` + `app/Domains/Cms` (القسم 15).
10. ✅ المرحلة 6: `app/Domains/Offline` ثم `app/Domains/Learning` (القسم 16). `app/Models` صار Shared Kernel فقط.
11. التالي: المرحلة 7 – التنظيف (حذف الـ aliases بعد استقرار الإنتاج، traits لـ `User`، قواعد الاعتماديات).

---

## 10. Investment – ما تم (2026-10-23)

```
app/Domains/Investment/
├── InvestmentServiceProvider.php     # views namespace "investment" + view composer لكلاسات الـ UI
├── Models/                           # InvestmentInquiry, InvestmentPlan, InvestmentPolicy
├── Services/                         # InvestmentPlanService, InvestmentStatsService
├── Http/Controllers/Admin/           # Dashboard, Plan, Inquiry, Policy
├── Http/Controllers/Public/          # InvestmentController
├── routes/admin.php                  # require من routes/web/admin.php داخل مجموعة admin
├── routes/public.php                 # require من routes/web/public.php
└── resources/views/{admin,public}/   # investment::admin.* و investment::public.*
```

- 27 ملفاً نُقلت بـ `git mv` (التاريخ محفوظ). الـ URLs وأسماء الـ routes والـ middleware والترتيب لم تتغير (`RouteSnapshotTest` مر بدون تحديث).
- 5 aliases في `LegacyClassAliases::MAP` للـ Models والـ Services.
- view composer `admin.investment.*` نُقل من `AppServiceProvider` إلى `InvestmentServiceProvider`.
- تم التحقق في المتصفح (قاعدة محلية): إنشاء خطة، الصفحة العامة، إرسال طلب، تغيير حالته، لوحة الاستثمار، حفظ السياسات.
- `phpunit.xml`: `memory_limit=512M`. الـ suite كاملة كانت على حافة 128M (تحميل ~1850 route مع كل test)، وتجاوزتها مع إضافة الاختبارات. قياس الذاكرة أثبت أن ملفات الـ routes الجديدة لا تضيف تسريباً.

**دروس دخلت الـ Playbook:** view composers مربوطة بأسماء الـ views؛ الاستبدال العام لـ `'admin.x.` يكسر أسماء الـ routes؛ Models تحتاج `use App\Models\User`؛ الـ routes تُستدعى بـ `require` في موضعها القديم.

---

## 11. Scholarship – ما تم (2026-10-23)

```
app/Domains/Scholarship/
├── ScholarshipServiceProvider.php    # views namespace "scholarship" + view composer لكلاسات sch*
├── Models/                           # ScholarshipGroup, ScholarshipProgram, ScholarshipRegistration
├── Services/                         # Activation, Program, Registration, Stats + ScholarshipCurriculumVisibilityService
├── Http/Controllers/Admin/           # Dashboard, Program, Course, Instructor, Registration, Group
├── Http/Controllers/Instructor/      # ScholarshipController, Registration, Group
├── Http/Controllers/Public/          # ScholarshipRegistrationController
├── routes/admin.php                  # require من routes/web/admin.php داخل مجموعة admin
├── routes/instructor.php             # require من routes/web/instructor.php بين courses و mind-map
├── routes/public.php                 # require من routes/web/auth.php
└── resources/views/{admin,instructor,public}/   # scholarship::admin.* و scholarship::instructor.* و scholarship::public.*
```

- 44 ملفاً نُقلت بـ `git mv`. `RouteSnapshotTest` مر **بدون** تحديث الـ snapshot.
- 8 aliases في `LegacyClassAliases::MAP` (3 Models + 5 Services).
- view composer `admin.scholarships.*` نُقل من `AppServiceProvider` إلى `ScholarshipServiceProvider` على `scholarship::admin.*`.
- `ScholarshipCurriculumVisibilityService` (كان في `app/Services`) نُقل للمجال: هو الـ Public API الذي يستخدمه Learning
  (`MyCourseController`, `StudentLearnController`, `CurriculumController`, `StudentEnrollmentController`, `CourseProgressService`,
  `OnlineEnrollmentsPdfExportService`, `ResyncCourseProgressCommand`).
- مراجع من نفس الـ namespace بدون import أُضيف لها `use`: `User`, `AdvancedCourse`, `CurriculumItem`, `CourseSection`,
  `StudentCourseEnrollment` (علاقات للمنح)، و `CourseProgressService`, `OnlineEnrollmentsPdfExportService`.
- **ديون موثّقة (علاقات Eloquent عبر المجالات):** `User::scholarshipRegistrations`, `AdvancedCourse::scholarshipProgram`,
  `CurriculumItem/CourseSection::visibleGroups`, `StudentCourseEnrollment::scholarshipRegistration`، و `DashboardController` (عدّاد التسجيلات المعلّقة).
- تم التحقق محلياً: كل صفحات لوحة المنح (11 صفحة) + إنشاء منحة + الصفحة العامة والتسجيل كضيف + تفعيل من الإدارة +
  إنشاء مجموعة + صفحات المدرب وإلغاء/إعادة التفعيل وتعديل مجموعة + صفحات الطالب (`my-courses/{id}/learn`).
- الـ suite كاملة: 206 passed، والـ 11 failures نفسها السابقة (migrations خاصة بـ MySQL على sqlite).

**ملاحظة جديدة للـ Playbook:** حصر المراجع يجب أن يشمل الـ Services ذات الاسم المرتبط بالمجال خارج مجلده
(`app/Services/ScholarshipCurriculumVisibilityService.php`)، والـ routes قد تكون موزّعة على أكثر من ملف (`auth.php` للصفحات العامة).

---

## 12. Hr – ما تم (2026-10-23)

```
app/Domains/Hr/
├── HrServiceProvider.php             # views "hr" + composer التوظيف + observer + commands + schedule
├── Models/                           # 22 model (Employee*, LeaveRequest, WorkSchedule, Hr*, Internship*)
├── Services/                         # 14 service (Employee* + AtsScoringService + CVParserService)
├── Support/                          # EmployeeAttendanceSettings, EmployeeDailyReportSettings
├── Mail/                             # EmployeeDeductionAddedMail, EmployeeTaskAssignedMail
├── Observers/                        # EmployeeSalaryDeductionObserver
├── Console/Commands/                 # employees:* (5 أوامر)
├── Http/Controllers/Admin/           # 13 controller + Recruitment/ (JobPosting, Application, Rubric)
├── Http/Controllers/Employee/        # Agreement, Attendance, DailyReport, Job, Leave, Presence, Task, Report
├── Http/Controllers/Public/          # CareersController, InternshipController
├── routes/admin/*.php                # 8 ملفات: recruitment, internships, employees, deductions, daily-reports, leaves, salaries, agreements
├── routes/employee/*.php             # 6 ملفات: attendance, tasks, leaves, daily-reports, agreements, reports
├── routes/public.php                 # careers + internships
└── resources/views/{admin,employee,public,emails}/   # hr::admin.* (admin/hr صار admin/recruitment)، hr::employee.*، hr::public.*، hr::emails.*
```

- 151 ملفاً نُقلت بـ `git mv` (94 عملية). `RouteSnapshotTest` مر **بدون** تحديث الـ snapshot.
- 40 alias في `LegacyClassAliases::MAP` (Models + Services + Support + Mail؛ الـ Mail لأن الرسائل الـ queued تحفظ اسم الكلاس).
- 123 ملفاً حُدّثت فيها الأسماء الكاملة، و 33 ملفاً أُضيف لها `use` (كانت تعتمد على نفس الـ namespace، أهمها `User.php` وملفات Sales/Marketing).
- 104 مرجع view حُوّل إلى `hr::` (بما فيها `view:` داخل الـ Mail classes).
- من `AppServiceProvider` إلى `HrServiceProvider`: view composer التوظيف (`hr::admin.recruitment.{jobs,applications,rubrics}.*`) وتسجيل `EmployeeSalaryDeductionObserver`.
- من `bootstrap/app.php` إلى `HrServiceProvider`: أوامر الـ schedule الأربعة `employees:*` (عبر `callAfterResolving(Schedule::class)`). `schedule:list` نفس المجموعة، وقائمة `artisan list` مطابقة تماماً لما قبل النقل.

**قرارات التصنيف (تختلف عن القائمة الأصلية في القسم 3):**

| الملف | القرار | السبب |
|---|---|---|
| `TeamsAttendanceFile`, `Admin/AttendanceController` | خارج HR | حضور الطلاب في المحاضرات (Learning/Offline) |
| `Admin/PerformanceController` | خارج HR | أداء النظام (cache/optimize) لا أداء الموظفين |
| `Admin/SalaryController` | خارج HR | رواتب المدربين (`InstructorAgreement`) → Accounting |
| `PenaltyWindow` | Shared (`app/Support`) | تستخدمه Sales و HR معاً |
| `EnsureEmployeeWorkAccess` + بوابة الموظف (`Employee/EmployeeController` dashboard, Profile, Settings, Notifications, Calendar) و `Admin/EmployeeNotificationController` | Shared | غلاف بوابة الموظف لكل الأقسام (Sales, Marketing, HR) |

**ديون موثّقة:**
- Sales تستدعي `Hr\Services\EmployeeAttendanceService` و `EmployeePresenceService` (Scorecard, OpsBoard, ScheduleCalendar) – مسموح (Public API).
- `EmployeeTask` له علاقات مع `DesignTaskCycle`, `ModeratorMontageRequest`, `ModeratorMarketingCalendarEvent` (Marketing)، و `Employee/EmployeeTaskController` يستخدم `MontageVideoHelper`.
- `EmployeeSalaryDeductionObserver` و `CleanupOutOfEmploymentDeductions` يلمسان `SalesDailyReport`؛ `EmployeePayrollService` يستخدم `SalesDailyReportService`.
- `EmployeeSalaryPayment` يرتبط بـ `Wallet`/`Expense` (Accounting).
- `User.php`: 10 علاقات HR (مرشحة لـ trait `HasEmployeeRecords` في المرحلة 7).

**تم التحقق محلياً:** 32 صفحة admin/عامة بلا معاملات + 14 صفحة تفاصيل/تعديل + تصدير الحضور والرواتب؛ إنشاء موظف، مهمة (الإشعار والـ mail)، خصم (الـ observer والـ mail)؛
بوابة الموظف (12 صفحة) + طلب إجازة + تقرير يومي، ثم موافقة الإدارة؛ إنشاء وظيفة → التقديم العام بـ CV (التحليل والتقييم الآلي) → إعادة التقييم؛
إنشاء فرصة تدريب → التقديم العام؛ مسير الرواتب داخل transaction مع rollback؛ `--help` للأوامر الخمسة.
الـ suite كاملة: 206 passed، والـ 11 failures نفسها السابقة.

**دروس جديدة للـ Playbook:**
- routes المجال قد تكون **متفرقة** بين routes مجالات أخرى (هنا 15 كتلة في 3 ملفات): ملف لكل كتلة يُستدعى في موضعها؛ دمجها في ملف واحد يغيّر الترتيب ويكسر الـ snapshot.
- المراجع الضمنية من نفس الـ namespace (`EmployeeJob::class` داخل `App\Models\User` بدون import) لا تظهر في البحث عن `App\Models\X` – تُكتشف بـ `token_get_all` لا بالـ regex.
- Commands تُكتشف تلقائياً من `app/Console/Commands` فقط → تُسجَّل من الـ Provider، والـ schedule ينتقل معها؛ قارن `artisan list` و `schedule:list` قبل/بعد.
- الـ Mail classes فيها اسم view (`view: 'emails.x'`) → جزء من استبدال الـ views، وتحتاج alias لأن الـ queued mail مسلسل.
- لا تعتمد على أسماء الملفات في التصنيف (`AttendanceController`, `PerformanceController`, `SalaryController` ليست HR) – افحص الـ imports.

---

## 13. Sales + Messaging – ما تم (2026-10-23)

نُقل المجالان في عملية واحدة (خريطة أسماء واحدة) لأنهما يستدعيان بعضهما بكثافة.

```
app/Domains/Sales/                         app/Domains/Messaging/
├── SalesServiceProvider.php               ├── MessagingServiceProvider.php   # views + composers + schedule طابور الواتساب
├── Models/        (34)                    ├── Models/        (18: WhatsApp*, MetaSocial*, MessageTemplate)
├── Services/      (42)                    ├── Services/      (25: WhatsApp*, MetaSocial*, MetaAds*, Excel/Workshop WhatsApp)
├── Support/       SalesDailyReportSettings├── Support/       MetaAds/MetaSocial/WhatsAppBridge/WhatsAppCloud Settings
├── Console/Commands/ (6: sales:*)         ├── Jobs/          ProcessWhatsAppBatchJob
├── Http/Middleware/  EnsureSales{Employee,Manager,Staff}
├── Http/Controllers/{Admin,Employee,Public}├── Http/Controllers/{Admin,Employee,Webhooks,Concerns}
├── routes/admin/{sales,boards}.php        ├── routes/admin/{whatsapp,meta-social,messages,meta-ads,workshop-whatsapp}.php
├── routes/employee/{team-chat,sales,staff,manager}.php
│                                          ├── routes/employee/{whatsapp-inbox,meta-social-inbox,manager-whatsapp}.php
├── routes/public.php                      ├── routes/webhooks.php
└── resources/views/                       └── resources/views/
    admin/* (كان admin/sales/*)                admin/{whatsapp,meta-social,messages}
    admin/{course,diploma}-board               admin/meta-ads (كان admin/marketing/meta-ads)
    employee/* (كان employee/sales/*)          employee/whatsapp (كان employee/sales/whatsapp)
    manager/* (كان employee/sales-manager/*)
    public/{course,diploma}-board
```

- 238 عملية `git mv` (385 ملفاً). `RouteSnapshotTest` مر **بدون** تحديث الـ snapshot.
- 125 alias (Sales 77 + Messaging 48؛ تشمل `ProcessWhatsAppBatchJob` لأن الـ queue = database).
- 274 ملفاً حُدّثت أسماؤها الكاملة، 56 ملفاً أُضيف لها `use` ضمني (منها `User.php`, `Workshop*`, `CampaignDailyReport`, `PenaltyWindow`).
- 226 مرجع view (222 آلياً + 4 يدوياً: ternary في `SalesManagerOpsBoardController` و `inboxView()` في 3 controllers).
- من `AppServiceProvider`: composers `admin.whatsapp.*` و `admin.meta-social.*` → `MessagingServiceProvider`.
- من `bootstrap/app.php`: 6 أوامر `sales:*` في الـ schedule → `SalesServiceProvider`، و `queue:work --queue=whatsapp` → `MessagingServiceProvider`. `artisan list` مطابق، و `schedule:list` نفس المجموعة.
- الـ middleware aliases (`sales.employee/manager/staff`) بقيت في `bootstrap/app.php` وتشير للكلاسات الجديدة.
- ⚠️ روابط الـ webhooks واستثناء CSRF لم تتغير: verify بتوكن خاطئ → 403، و POST بدون CSRF → 200 (لا 419).

**قرارات التصنيف:**

| الملف | القرار | السبب |
|---|---|---|
| `Admin/MessagesController` + `admin/messages` | Messaging | رسائل واتساب للطلاب عبر `WhatsAppService` |
| `Admin/WorkshopWhatsAppController`, `WorkshopWhatsApp*Service` | Messaging | حسب الخطة؛ يستخدم Models الورش (دين) |
| `Employee/Sales{,Manager}WhatsAppInbox`, `SalesMetaSocialInbox`, `SalesWhatsAppQueue` + `Concerns/Handles*Inbox` | Messaging | واجهات صندوق الرسائل لفريق المبيعات |
| `Sales{,Lead}GroupWhatsApp*` (Admin/Employee + Service) | Sales | شاشات مجموعات العملاء تستدعي خدمات Messaging |
| `AcademyWhatsApp` | Shared | يبني رابط wa.me فقط |
| `app/Helpers/WhatsAppHelper.php` | بقي مكانه | دوال عامة محمّلة عبر `composer.json` → `files` |
| مجموعات middleware في `employee.php` | بقيت في `routes/web` | كل مجموعة (`sales.employee`, `sales.manager`) تستدعي ملف Sales ثم ملف Messaging بداخلها |

**ديون موثّقة:**
- Sales ↔ Messaging في الاتجاهين: `SalesLead` مرتبط بمحادثات الواتساب/السوشيال، و `Handles*Inbox` تحدّث مراحل الـ Lead.
- `Workshop`, `WorkshopRegistration`, `WorkshopPromoActivation` (Offline) ↔ `SalesLead` و `WhatsAppMetaTemplate`.
- `CampaignDailyReport` (Marketing) ↔ `SalesDailyReport`؛ `PenaltyWindow` (Shared) يقرأ `SalesDailyReportSettings`.
- `User.php`: علاقات Sales/Messaging (مرشحة لـ traits في المرحلة 7).
- (اختياري لاحقاً) تقسيم فرعي داخل Sales (`Leads/`, `Shifts/`, `Kpi/`, `Commission/`, `TeamChat/`) – لم يُنفّذ حتى لا يتضاعف النقل.

**مشاكل موجودة قبل النقل (ليست منه):**
- `admin/messages/{monthly-reports,templates,settings}` ترجع 404: `/{message}` معرّف قبلها في نفس الـ group (نفس الترتيب في `main`).
- `SalesDailyReportsExcelExportService.php:111`: `$col++` على حرف عمود → deprecation في PHP 8.5 (`str_increment()`).

**تم التحقق محلياً:** 72 صفحة admin (ما عدا 4 تتصل بـ Meta: OAuth redirect/callback و settings/test و whatsapp/status) + 9 تصديرات (Excel/CSV/PDF)؛
إنشاء موظف مبيعات ومدير وفريق و Lead؛ بوابة موظف المبيعات (24 صفحة) ومدير المبيعات (41 صفحة)؛ نشاط على Lead مع انتقال المرحلة،
شات الفريق (فتح قناة + إرسال)، مزامنة التقرير اليومي؛ صفحات Lead/فريق بالمعاملات؛ الـ webhooks؛ `--help` للأوامر الستة.
الـ suite كاملة: 206 passed، والـ 11 failures نفسها السابقة.

**دروس جديدة للـ Playbook:**
- مجالان مترابطان بقوة يُنقلان معاً بخريطة واحدة؛ النقل المتتابع كان سيترك imports مؤقتة بين اسمين.
- أسماء الـ views قد تأتي من متغيرات (`$view = cond ? 'a' : 'b'`) أو methods (`inboxView()`) أو `response()->view()` – ابحث عن النصوص المتبقية بعد الاستبدال الآلي.
- مجموعة route مختلطة بين مجالين: اترك الـ group wrapper في `routes/web` واستدعِ ملف كل مجال بداخله.
- استبعد من فحص المتصفح أي route يتصل بخدمة خارجية (OAuth/test API) ووثّق ذلك.

---

## 14. Accounting + Places – ما تم (2026-10-23)

```
app/Domains/Accounting/                    app/Domains/Places/
├── AccountingServiceProvider.php          ├── PlacesServiceProvider.php
├── Models/      (21)                      ├── Models/   PlaceDailyExpense, PlaceInvoice, PlaceMonthlySettlement, PlaceUsageLog
├── Services/    (9: Kashier, Fawaterak, GatewayFee, Refund, Discount, …)
├── Support/     AccountingAnalytics       ├── Services/ PlaceSettlementService
├── Console/Commands/ (4)                  ├── Http/Controllers/Admin/  (UsageLog, DailyExpense, Settlement)
├── Http/Controllers/{Admin,Employee,Instructor,Student,Branch,Public}
│                                          ├── Http/Controllers/Office/ (بوابة مدير المكان — كان Place/)
├── routes/admin/*.php (11) employee/ instructor/ branch/ public/ student/
│                                          ├── routes/admin.php, routes/office.php
└── resources/views/{admin,employee,instructor,student,branch,public}
                                           └── resources/views/{admin/settlements, admin/usage-logs, office}
```

- 112 عملية `git mv` (192 ملفاً). `RouteSnapshotTest` مر **بدون** تحديث؛ `artisan list` و `schedule:list` بدون تغيير.
- 36 alias (Accounting 31 + Places 5). 137 ملفاً حُدّثت أسماؤها، و 33 أُضيف لها `use` ضمني (منها `User`, `AdvancedCourse`, `Branch`, `OfflineLocation`, `StudentCourseEnrollment`).
- 142 مرجع view + اسم `public.checkout` داخل قائمة views الـ branding في `AppServiceProvider` (نص عادي لا يلتقطه الاستبدال الآلي).
- 22 كتلة routes في 7 ملفات. ⚠️ روابط بوابات الدفع لم تتغير: `checkout/kashier/callback`، `checkout/fawaterak/{status}`، `js/checkout-pay-widget.v1.js`.
- **أمان البيانات:** لا توجد علاقات polymorphic لموديلات المحاسبة ولا أعمدة تخزن أسماء الكلاسات (فُحص الكود والـ migrations وقاعدة البيانات المحلية).
  `activity_logs.model_type` على الإنتاج قد يحتوي `App\Models\Order` وأمثاله ← `LegacyClassAliases::resolve()` يغطيها.

**قرارات التصنيف:** `Admin/ReportsController` (تقرير عام لكل الأقسام) و `BranchDashboardController` (غلاف بوابة الفرع) و `Student/CourseController`
و `CustomerSurveyRewardService` (Marketing) بقيت مكانها. `Admin/InstructorAccountController` و `Admin/SalaryController` (مستحقات المدربين) → Accounting.
`app/Http/Controllers/ExpenseController.php` نسخة قديمة غير مستخدمة – تُركت كما هي (لم تُحذف).
تقسيم `AccountingReportsController` (1262 سطر) **لم يُنفَّذ** في هذه المرحلة: الأولوية لصحة الحسابات قبل إعادة الهيكلة.

**تم التحقق محلياً:** 48 صفحة إدارة كـ super_admin + بوابات الطالب والمدرب والموظف والفرع والمكان؛ دورة كاملة
(محفظة → كورس → طلب بإيصال → رفض طلب → طلب جديد → اعتماد → فاتورة/دفعة/معاملة/إيداع/تسجيل)؛
ومقارنة أرقام 41 صفحة بين الكود الجديد ونسخة `main` على نفس البيانات: **متطابقة**.
الـ suite: 206 passed، والـ 11 failures نفسها السابقة.

**دروس جديدة للـ Playbook:**
- أسماء views قد تكون نصوصاً في **قوائم** (`$brandingViews`) لا داخل `view()`.
- ملفات `.blade.php` المنفردة في قائمة النقل يجب استبعادها من سكربتات الـ namespace.
- `require` أُضيف في مراحل سابقة داخل كتلة مجال آخر (هنا HR داخل كتلة المحاسبة) → حدود الكتل تُراجع يدوياً.
- للتحقق من أن النقل لم يغيّر أي رقم: تشغيل نسخة `main` على نفس القاعدة ومقارنة أرقام الصفحات.

---

## 15. Marketing + Community + Cms – ما تم (2026-10-24)

| المجال | Models | Services/Support | Controllers | أخرى |
|---|---|---|---|---|
| `Marketing` | 14 (حملات، خطط المشرفين، مونتاج، تصميم، إحالات، ولاء، استبيانات، مناطق) | 7 + 3 | 21 (Admin/Employee/Public/Student) | أمران `marketing:*` + الـ schedule، middleware `moderator.employee` و `RecordMarketingRegionVisit` |
| `Community` | 22 (بوابة المجتمع، مجتمع الكورس، المحادثات، الأوسمة، الرحلة، البورتفوليو) | 6 + 1 | 27 (Admin/Api V1/Portal/Instructor/Public/Student) | `CommunityNotificationMail`، middleware `community.contributor` |
| `Cms` | 8 (تذاكر الدعم، التواصل، FAQ، المدونة، الوسائط، التقويم، تقارير الطلاب) | 1 + 1 | 9 | — |

- 165 عملية `git mv` (267 ملفاً). `RouteSnapshotTest` مر **بدون** تحديث؛ `artisan list` و `schedule:list` بدون تغيير.
- 64 alias، 149 ملفاً حُدّثت أسماؤها، 38 أُضيف لها `use` ضمني، 170 مرجع view + نصّان في قوائم composers
  (`community.layouts.guest` و `public.contact`).
- 24 كتلة routes في 6 ملفات. `routes/api.php` بقي كما هو (يستخدم `use` في أعلى الملف وأُحدّثت الأسماء فيه).
- `bootstrap/app.php` لم يعد فيه `withSchedule`: كل الجدولة الآن في Providers المجالات (Hr, Sales, Messaging, Marketing).
- تقسيم المجلدات المشتركة: `admin/media/org-chart` → Marketing وباقي `admin/media` → Cms؛ `admin/mobile-app/course-community` → Community؛
  `employee/business-developer/marketing` → Marketing (دليل الـ BD بقي)؛ `admin/marketing/*` (مع `_tabs`/`_flash` المستخدمة في صفحات ورش الأكواد و Meta Ads) → Marketing.

**⚠️ بيانات محفوظة بأسماء الكلاسات (تم التعامل معها):**
- `course_community_reactions.reactable_type`: مفاتيح `MorphMap` بقيت `App\Models\CourseCommunityPost/Comment`؛ الـ 10 مقارنات/كتابات
  `'reactable_type' => X::class` صارت `Relation::getMorphAlias(X::class)`.
- `journey_share_events.shareable_type` (لم يكن في الخريطة): أُضيف `PortfolioProject` و `JourneyProfile` لـ `MorphMap` بمفاتيحهما القديمة،
  والكتابة في `JourneyShareCardController` والمقارنة في `JourneyAnalyticsService` عبر `getMorphAlias`.
- تحقق: تفاعل جديد عبر الـ API خُزّن `App\Models\CourseCommunityPost` والعدّاد = 1 في الكود الجديد ونسخة `main`؛
  مشاركة رحلة خُزّنت `App\Models\JourneyProfile` وأرقام صفحة التحليلات متطابقة في النسختين.

**قرارات التصنيف:** بقيت مشتركة: `Admin/ReportsController`، `DashboardController`، `Public/PageController`، `Public/LandingController`،
`Admin/AdminController`، `Student/CalendarController`، `Employee/EmployeeCalendarController`، صفحات `admin/mobile-app` الأخرى،
أمر `SendMonthlyReports`. `Group*` و `Task*` تخص الكورسات → مع Learning لاحقاً. `InstructorPersonalBrandingController` و
`MarketingCourseReviewController` (لوحة الإدارة) → Marketing؛ محرر البراندينغ في بوابة المدرب بقي.

**تم التحقق:** حالة كل الـ 94 route GET بلا معاملات (admin/employee/instructor/student/guest) **مطابقة** لنسخة `main` على نفس القاعدة،
والـ 8 routes الـ API بتوكن Sanctum مطابقة (200). الـ suite: 219 passed، والـ 11 failures نفسها.

**مشاكل موجودة قبل النقل (ليست منه):** `admin/blog` يرجع 500 – syntax error في `cms::admin.blog.index` (نفس الخطأ على `main`).

**دروس جديدة للـ Playbook:**
- `git mv src dest` عندما `dest` موجود يضع المجلد **داخله** (`dest/src`): انقل محتويات المجلد أو تأكد أن الهدف غير موجود؛
  اكتُشف بمقارنة حالة الصفحات مع `main` (11 صفحة 500) وصُحّح قبل الانتهاء.
- استبدال الأسماء الكاملة يغيّر أيضاً **النصوص** (مفاتيح `MorphMap`)؛ اختبار «morph keys keep legacy database values» يكشفه.
- ابحث عن كل `*_type` يُكتب أو يُقارن بـ `X::class` (ليس فقط علاقات morph): `shareable_type` لم يكن polymorphic رسمياً.
- مقارنة حالة كل route مع نسخة `main` على نفس القاعدة أرخص وأدق من فحص الصفحات واحدة واحدة.

---

## 16. Offline + Learning – ما تم (2026-10-24)

| المجال | Models | Controllers | أخرى |
|---|---|---|---|
| `Offline` | 18 (Offline*، OfflineLocation، Workshop*) | 21 (Admin/Branch/Instructor/Public/Student، منها إدارة وحجز المجموعات الأونلاين) | 3 services، `OfflineEnrollmentProvisioner`، `WorkshopAcceptanceMail`؛ 11 ملف routes |
| `Learning` | 54 (الكورسات، المنهج، المحاضرات، الواجبات، الاختبارات، الأسئلة، المسارات، الشهادات، الفيديو، المهام، المجموعات، الحضور الأونلاين) | 71 (Admin/Api/Branch/Instructor/Public/Student + الفيديو والمهام) | 5 services، 3 support، 4 أوامر، 7 observers (من `AppServiceProvider`)، `CourseEnrollmentActivatedMail`؛ 27 ملف routes |

- 265 عملية نقل (63 + 202)؛ ملف واحد غير متتبَّع في git (`Public/CourseCatalogController.php` – عمل غير محفوظ) نُقل بـ `mv` ويبقى untracked.
- 86 alias. `RouteSnapshotTest` مر **بدون** تحديث؛ `artisan list` و `schedule:list` بدون تغيير. الـ suite: 219 passed، والـ 11 failures نفسها.
- `app/Models` الآن: `User`, `Role`, `Permission`, `Branch`, `ActivityLog`, `Notification`, `TwoFactorLog`, `PlatformErrorLog`, `GeoIpLookup`, `MobileAppSetting` فقط.
- بقيت مشتركة في `app/Http/Controllers` (50): Auth، الإدارة العامة (مستخدمون/أدوار/صلاحيات/سجلات/إعدادات/تطبيق الموبايل/إشعارات/إحصائيات)،
  التقارير الشاملة، `DashboardController`، `LandingController`، `PageController`، بوابات الموظف/الفرع/ملف الطالب والمدرب، `views/video` (مشترك بين Offline و Learning).

**⚠️ أسماء الكلاسات المحفوظة في الداتابيز (curriculum_items و offline_curriculum_items):**
- سكربت استبدال الأسماء صار **يتجاهل النصوص التي تبدأ بعد علامة تنصيص** (مفاتيح `MorphMap` والقيم التي يرسلها الـ JS مثل `'App\\Models\\Lecture'`)،
  ويطبع قائمتها للمراجعة. نص واحد في **منتصف** سلسلة تغيّر رغم ذلك: قاعدة التحقق `in:App\Models\Lecture,…` في `Instructor\CurriculumController` →
  صارت تُبنى من `Relation::getMorphAlias` (نفس القيم الأصلية حرفياً). اكتُشف بفحص كل `T_CONSTANT_ENCAPSED_STRING` بـ `token_get_all`.
- المنهج الأونلاين: التحقق من `item_type` و `getMorphedModel` لإيجاد الموديل، والكتابات (`AdvancedExam`, `Assignment`, `LearningPattern`) عبر `getMorphAlias`.
- منهج الأوفلاين: الحقول المخفية في الواجهة والتحقق والكتابة والمقارنة (الشارات، الحذف) عبر `getMorphAlias`/`getMorphedModel`.
- `ScholarshipCurriculumVisibilityService::contentVisibleToStudent()` يحوّل النوع داخلياً (المستدعون يمررون `Lecture::class`).
- أسماء views ديناميكية صُحّحت يدوياً: `curriculumView()` في المتحكمين، ternary في `Student\LearningPatternController`، و `$typeView` في `pattern-content`.

**تم التحقق مقابل نسخة `main` على نفس القاعدة:** حالة كل الـ 23 route أوفلاين و 88 route تعلّم بلا معاملات مطابقة؛
إنشاء كورس أوفلاين من الإدارة ← قسم ← إرفاق محاضرة (بالقيمة التي تضعها الواجهة) ← ملاحظة: خُزّن `App\Models\OfflineLecture` و `App\Models\OfflineCurriculumNote`،
وصفحة المنهج متطابقة (الشارات صحيحة). المنهج الأونلاين: قسم ← إضافة محاضرة بنفس JSON الذي يرسله الـ JS ← خُزّن `App\Models\Lecture`؛
صفحات المدرب والطالب (`my-courses/2`, `learn`) و JSON المحاضرة **متطابقة** في النسختين.
فرق وحيد ظهر ثم اختفى: نسخة `main` فشلت في قراءة كاش كتبه الكود الجديد → انظر تحذير الكاش في القسم 4 (خطوة 8).

**مشاكل موجودة قبل النقل (ليست منه):** views غير موجودة: `tasks.*` (`TaskController` الجذري)، `admin.attendance.index`، `admin.subjects.create`،
`instructor.questions.index` → 500 في النسختين.

**دروس جديدة للـ Playbook:**
- `git mv` يرفض الملفات غير المتتبَّعة (عمل المستخدم غير المحفوظ): انقلها بـ `mv` وتبقى untracked.
- النصوص التي تحتوي أسماء كلاسات في **منتصف** سلسلة (قواعد validation) لا يلتقطها فحص «بعد علامة التنصيص»: افحص كل string literal بالـ tokenizer بعد الاستبدال.
- عند مقارنة نسختين على نفس القاعدة: امسح الكاش أولاً، وإلا تختلط Models مسلسلة بأسماء مختلفة.

