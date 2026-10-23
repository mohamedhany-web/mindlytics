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

### Scholarship (المرحلة 1)
- Models: ScholarshipGroup, ScholarshipProgram, ScholarshipRegistration
- Services: Scholarship/*
- Controllers: Admin/Scholarship/*, Instructor/Scholarship/*, Instructor/ScholarshipController, Public/ScholarshipRegistrationController
- Views: `resources/views/scholarships`

### Hr (المرحلة 2)
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

### Accounting / Finance (المرحلة 4)
- **Models:** AccountingDebt, AccountingDebtRepayment, AgreementPayment, Expense, InstallmentAgreement, InstallmentPayment, InstallmentPlan, Invoice, Order, Payment, Transaction, Wallet, WalletReport, WalletTransaction, WithdrawalRequest, Coupon, CouponUsage, Subscription, Package, InstructorAgreement, InstructorPayoutDetail
- **Services:** AccountingComprehensiveExcelExport, AutoInstallmentAgreement, FawaterakService, KashierService, GatewayFeeCalculator, PaymentGatewaySettings, TransactionRefund, CustomerDiscount, InstructorCoursePercentage
- **Controllers:** Admin/Accounting*, Expense, Installment*, Invoice, Order, Payment, Transaction, Wallet, WithdrawalRequest, Coupon, Subscription, Package, InstructorAgreement, OfflineAgreement; Employee/AccountingController; Public/CheckoutController, FawaterkPluginController; Branch/BranchPayments, BranchOrders, BranchInvoices; Student/Order, Invoice, Wallet, Coupon; Instructor/WithdrawalRequest, TransferAccount
- **Commands:** ProcessInstallmentPayments, SyncInstructorAgreementPayments
- **ملاحظة:** تقسيم الـ controllers الضخمة (`AccountingReportsController`, `ReportsController`) إلى controllers أصغر + Query/Report services جزء من هذه المرحلة.

### Places (مع المرحلة 4 أو منفصل)
- Models: PlaceDailyExpense, PlaceInvoice, PlaceMonthlySettlement, PlaceUsageLog
- Services: PlaceSettlementService · Controllers: Place/*, Admin/Place*
- Views: `place-office`

### Sales (المرحلة 3 – الأكبر)
- **Models:** كل `Sales*` (~40 model)
- **Services:** كل `Sales*` (~45 service)
- **Controllers:** Admin/Sales*, Employee/Sales*, Employee/SalesManager*, Public/SalesCourseBoardLanding, SalesDiplomaBoardLanding
- **Commands:** ApplySalesDailyKpiPenalties, ApplySalesDailyReportPenalties, EnforceSalesImportContact, EvaluateSalesEnforcement, RemindSalesDailyReport, SendSalesFollowupReminders
- **Middleware:** EnsureSalesEmployee, EnsureSalesManager, EnsureSalesStaff
- Views: `resources/views/sales`
- **اقتراح:** داخل الـ module نقسّم فرعياً: `Leads/`, `Shifts/`, `Kpi/`, `Commission/`, `Reports/`, `TeamChat/`.

### Messaging (WhatsApp + Meta)
- Models: WhatsApp*, MetaSocial*, MessageTemplate
- Services: WhatsApp*, MetaSocial/*, MetaAds/*, ExcelWhatsAppCampaign, WorkshopWhatsApp*
- Controllers: Admin/WhatsApp*, MetaSocial*, MetaAds*, ExcelWhatsApp; Employee/Sales*Inbox, SalesWhatsAppQueue; WhatsAppWebhookController, MetaSocialWebhookController; Concerns/Handles*Inbox
- Jobs: ProcessWhatsAppBatchJob · Helper: WhatsAppHelper.php
- ⚠️ **الـ Webhooks يجب أن تبقى على نفس الـ URLs** (مسجّلة عند Meta) ونفس استثناء CSRF.

### Marketing
- Models: AdvertisingCampaign, CampaignDailyReport, MarketingCustomerSurvey, MarketingRegionDailyStat, ModeratorMarketing*, ModeratorMontageRequest, DesignTaskCycle, DesignCycleModeratorPlannerItem, PopupAd, Referral, ReferralProgram, LoyaltyProgram
- Services: Marketing*, CampaignReport, CustomerSurveyReward, Referral, MontageDeliverablesExcelImport
- Commands: ApplyMarketingPlanExecutionPenalties, RemindMarketingPlanEvents
- Middleware: EnsureModeratorEmployee, RecordMarketingRegionVisit

### Learning (الكورسات الأونلاين + التقييم)
- **Courses:** AdvancedCourse, Course, CourseSection, CourseLesson, CurriculumItem, Lecture*, Lesson, LessonProgress, CourseEnrollment, StudentCourseEnrollment, CourseActivation, CourseReview, LearningPath*, LearningPattern*, Video*, Certificate, CertificateBranding, InstructorProfile, InstructorRequest
- **Assessment:** Exam, ExamAttempt, ExamQuestion, AdvancedExam, AdvancedQuestion, Question, QuestionBank, QuestionCategory, Assignment, AssignmentSubmission
- **Academic:** AcademicSubject, AcademicYear, Subject, School, Classroom
- Observers: AdvancedCourse, Assignment, Enrollment, ExamAttempt, Exam, LectureAssignment, Lecture
- هذا قلب المنصة وأكثرها حساسية → **آخر مرحلة**.

### Offline (الكورسات الحضورية + الورش)
- Models: Offline*, AttendanceRecord, AttendanceStatistics, OfflineLocation, Workshop*
- Services: WorkshopAttendance, WorkshopPromo*

### Community
- Models: Community*, ContributorProfile, CourseCommunity*, Group*, PeerChat*, PeerConnectionRequest, DirectMessage*, Badge, Achievement, UserAchievement, UserBadge, Journey*, PortfolioProject*
- Services: Community/*, Journey*
- Controllers: Community/*, Public/Community*, Admin/Community*

### Support / CMS
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
4. التالي: Scholarship بنفس الخطوات، ثم HR.

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
