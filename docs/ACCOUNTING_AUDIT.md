# مراجعة الحسابات في قسم المحاسبة

> التاريخ: 2026-10-23 · النطاق: `app/Domains/Accounting` و `app/Domains/Places` بعد نقلهما (المرحلة 4).
> **لم يُغيَّر أي حساب أو بيان بعد.** هذا التقرير للقراءة والقرار فقط. كل إصلاح مقترح أدناه يتغير فيه **الكود فقط**:
> لا migration، لا تعديل على بيانات الإنتاج، ولا إعادة حساب لأرقام قديمة إلا بقرار منفصل.

## كيف تمت المراجعة

- قراءة مسارات الفلوس: الدفع (يدوي، كاشير، فواتيرك)، اعتماد الطلب، الفواتير، المدفوعات، المعاملات، المحافظ، الاسترداد،
  المصروفات، مستحقات المدربين، العمولات، الرواتب، والتقارير (قائمة الدخل، المركز المالي، التدفقات).
- تجربة دورة كاملة محلياً: محفظة → كورس 1500 ج → طلب طالب بإيصال → اعتماد الإدارة. الناتج متسق:
  فاتورة INV-00000001 مدفوعة 1500، دفعة PAY-00000001، معاملة دائن TXN-00000001، إيداع في المحفظة 0 ← 1500، تسجيل نشط.
- مقارنة أرقام 41 صفحة محاسبة بين الكود الجديد ونسخة `main` على نفس البيانات: **متطابقة** (النقل لم يغيّر أي رقم).

## ملخص

| # | المشكلة | الخطورة | أثرها على الأرقام/الفلوس |
|---|---|---|---|
| C1 | دفع فواتيرك يُعتمد بدون التحقق من الدفع | 🔴 حرج | كورسات بدون دفع + إيراد وهمي في التقارير |
| C2 | ترقيم الفواتير/المدفوعات/المعاملات بـ `count() + 1` | 🔴 حرج | بعد أي حذف: كل اعتماد طلب يفشل (رقم مكرر) |
| H1 | اعتماد الطلب بدون قفل (race) | 🟠 عالي | فاتورتان ودفعتان وإيداع مزدوج لنفس الطلب |
| H2 | دفع مستحقات المدرب من «الماليات» لا يُسجَّل كمصروف | 🟠 عالي | الربح والنقدية أعلى من الحقيقة |
| H3 | عمولات المبيعات مسجّلة «دائن» فتظهر كإيراد | 🟠 عالي | الإيراد في تقارير المعاملات أعلى من الحقيقة |
| H4 | الاسترداد الجزئي لا يُنقص الإيراد | 🟠 عالي | الإيراد أعلى بقيمة المبالغ المستردة جزئياً |
| M1 | فشل الإيداع في المحفظة يُتجاهَل + سحب بدون قفل | 🟡 متوسط | رصيد المحفظة ≠ حركاتها؛ احتمال رصيد سالب |
| M2 | فشل تسجيل الطالب بعد الدفع يُتجاهَل | 🟡 متوسط | طالب دفع وفاتورته «مدفوعة» بدون وصول للكورس |
| M3 | الفاتورة تفقد السعر الأصلي والخصم | 🟡 متوسط | تقارير الخصومات من الفواتير = صفر |
| M4 | حذف نهائي للفواتير/المدفوعات/المعاملات | 🟡 متوسط | يكسر سجل التدقيق ولا يعكس المحفظة؛ يُطلق C2 |
| M5 | كورس «مجاني» يقبل طلب دفع يدوي بمبلغ صفر | 🟡 متوسط | سجل: أصلي 1500، خصم 0، صافي 0 (لا يتطابق) |
| L1–L6 | ملاحظات أقل خطورة | 🔵 | انظر القسم الأخير |

---

## 🔴 C1 — دفع فواتيرك يُعتمد بدون التحقق من الدفع

**أين:** `CheckoutController::fawaterakPrepare()` و `fawaterakReturn()`.

**ما يحدث:**
1. `fawaterakPrepare` ينشئ طلباً `pending` ويحفظ رقمه في الـ session **قبل** الدفع.
2. `GET /checkout/fawaterak/success` يعتمد الطلب فوراً: يفعّل الكورس، ينشئ فاتورة «مدفوعة» + دفعة `completed` + معاملة إيراد.
   الشرط الوحيد: الطلب في الـ session وكلمة `success` في الرابط. **لا يوجد أي اتصال بـ API فواتيرك ولا webhook موقّع.**

**النتيجة:** أي طالب مسجّل يضغط «ادفع» ثم يفتح `/checkout/fawaterak/success` بنفسه يحصل على الكورس مجاناً،
ويُسجَّل إيراد لم يُحصَّل. وضع الدفع الحالي في الإعدادات المحلية = `fawaterak`.

**الإصلاح المقترح (كود فقط):** قبل الاعتماد، التحقق من حالة الفاتورة عند فواتيرك (API الفاتورة أو webhook موقّع بـ HMAC)
ومطابقة المبلغ ورقم الطلب. إن لم يتأكد الدفع: يبقى الطلب `pending` مع رسالة «جاري التحقق». يحتاج تجربة على بيئة فواتيرك
التجريبية قبل النشر. **لا يمس أي طلب قديم.**

**للقياس على الإنتاج (قراءة فقط):**
```sql
-- كل دفعات فواتيرك ومحتوى الرد المحفوظ (هل فيه رقم فاتورة/مرجع من فواتيرك؟)
SELECT p.id, p.payment_number, p.amount, p.paid_at, p.gateway_response
FROM payments p WHERE p.payment_gateway = 'fawaterak' ORDER BY p.paid_at DESC;
```
قارن هذه القائمة بكشف حساب فواتيرك: أي دفعة هنا بدون مقابل في كشف فواتيرك = طلب اعتُمد بدون دفع.

---

## 🔴 C2 — الترقيم بـ `count() + 1`

**أين:** 41 موضعاً في الكود، منها `OrderController::approve`، `CheckoutController` (كاشير/فواتيرك)، `PaymentController`،
`TransactionController`، `InstallmentAgreementController`، `SubscriptionController`، `ExpenseController`،
`TransactionRefundService`، `SalesWinCommissionService`، `EmployeePayrollService`، `PlaceInvoice`، `PlaceMonthlySettlement`.

**ما يحدث:** `invoice_number` و `payment_number` و `transaction_number` عليها `unique`. الرقم التالي = عدد الصفوف + 1.
عند حذف أي صف ليس الأخير يصبح العدد أقل من أكبر رقم، فيتكرر الرقم ويفشل الإدخال.

**تم إثباته محلياً** (داخل transaction تم التراجع عنها):
```
created INV-00000002 → deleted INV-00000001 → next number would be INV-00000002
FAILS: UniqueConstraintViolationException … Duplicate entry 'INV-00000002'
```
بعدها **كل** اعتماد طلب يفشل ويتراجع بالكامل. وجود الأمر `order:test-approve` «لإيجاد الخطأ الدقيق» يرجّح أن هذا حدث فعلاً.
نفس المشكلة عند اعتمادين في نفس اللحظة.

**الإصلاح المقترح:** دالة ترقيم واحدة تأخذ أكبر رقم موجود (`MAX` على الجزء الرقمي) داخل قفل، مع إعادة محاولة عند التكرار.
نفس صيغة الأرقام الحالية. لا تغيير على الأرقام الموجودة.

**للقياس (قراءة فقط):**
```sql
SELECT 'invoices' t, COUNT(*) cnt, MAX(CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED)) max_no FROM invoices WHERE invoice_number LIKE 'INV-%'
UNION ALL SELECT 'payments', COUNT(*), MAX(CAST(SUBSTRING(payment_number, 5) AS UNSIGNED)) FROM payments WHERE payment_number LIKE 'PAY-%'
UNION ALL SELECT 'transactions', COUNT(*), MAX(CAST(SUBSTRING(transaction_number, 5) AS UNSIGNED)) FROM transactions WHERE transaction_number LIKE 'TXN-%';
```
إذا كان `max_no > cnt` في أي سطر، فالاعتماد القادم **سيفشل حتماً** عند الوصول لذلك الرقم.

---

## 🟠 H1 — اعتماد الطلب بدون قفل

**أين:** `OrderController::approve()` (و `kashierCallback()`).

**ما يحدث:** التحقق من أن الطلب `pending` يتم بـ `$order->refresh()` بدون `lockForUpdate()`. ضغطتان على «موافقة»
(أو مشرفان في نفس اللحظة) تمرّان معاً ← فاتورتان + دفعتان + إيداعان في المحفظة لنفس الطلب. (مسار فواتيرك يستخدم القفل صحيحاً.)

**الإصلاح:** `Order::whereKey($id)->lockForUpdate()->first()` داخل الـ transaction قبل الفحص.

**للقياس:**
```sql
SELECT JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.order_id')) order_id, COUNT(*) n, SUM(amount) total
FROM transactions WHERE category = 'course_payment' AND JSON_EXTRACT(metadata, '$.order_id') IS NOT NULL
GROUP BY order_id HAVING n > 1;
```

---

## 🟠 H2 — مستحقات المدرب المدفوعة من «الماليات» لا تُسجَّل كمصروف

**أين:** `SalaryController::markPaid()` (مسار `admin/salaries/pay/{payment}`).

**ما يحدث:** يغيّر حالة `AgreementPayment` إلى `paid` ويحفظ الإيصال فقط. **لا مصروف، لا سحب من محفظة، لا معاملة.**
قائمة الدخل (`AccountingAnalytics::incomeStatement`) تحسب تكلفة المدربين من **طلبات السحب المكتملة فقط**.
النتيجة: كل ما دُفع للمدربين من هذا المسار غائب عن المصروفات، والربح والنقدية في المحافظ أعلى من الحقيقة.

**الإصلاح المقترح:** عند «تم الدفع» تسجيل مصروف (فئة مستحقات مدربين) + سحب من المحفظة المختارة + معاملة مدين،
بدون ازدواج مع طلبات السحب (نفس المستحق لا يُحسب مرتين). **يحتاج قرارك:** من أي محفظة يُدفع، وهل نسجّل مصروفات للمدفوعات القديمة
(تسوية تاريخية) أم من تاريخ الإصلاح فقط.

**للقياس:**
```sql
SELECT 'agreement_payments paid' src, COUNT(*) n, SUM(amount) total FROM agreement_payments WHERE status = 'paid'
UNION ALL SELECT 'withdrawals completed', COUNT(*), SUM(amount) FROM withdrawal_requests WHERE status = 'completed';
```

---

## 🟠 H3 — عمولات المبيعات تظهر كإيراد

**أين:** `SalesWinCommissionService` يسجّل العمولة كمعاملة `type = credit` و `category = commission` (بدون `payment_id`).
`AccountingReportsController` (إيراد «من المعاملات» = كل `credit` بدون `payment_id`)، ورسم التدفق اللحظي، وتصدير Excel
(«إيراد») يعاملونها كإيراد للشركة. هي في الحقيقة **مستحق على الشركة** للموظف.

**الإصلاح المقترح (قراءة فقط، بدون تعديل بيانات):** استبعاد `category = 'commission'` من مجاميع الإيراد في التقارير،
وعرضها كبند «عمولات مستحقة». صفحة الموظف (`Employee/AccountingController`) تبقى كما هي لأنها تعرضها كدخل للموظف بشكل صحيح.

**للقياس:**
```sql
SELECT COUNT(*) n, SUM(amount) total FROM transactions WHERE type = 'credit' AND category = 'commission';
```

---

## 🟠 H4 — الاسترداد الجزئي لا يُنقص الإيراد

**أين:** `TransactionRefundService::syncPaymentAndInvoice()`.

**ما يحدث:** الدفعة تتحول `refunded` فقط إذا كان المسترد ≥ قيمتها. في الاسترداد الجزئي تبقى `completed` بقيمتها الكاملة،
والإيراد (`revenueBetween` = مجموع الدفعات `completed`) لا ينقص. حالة الفاتورة تُحسب من نفس المجموع فتبقى «مدفوعة».
في الاسترداد الكامل يحدث العكس: يختفي الإيراد من **الشهر الأصلي** (تعديل فترة سابقة) بدل تسجيل الاسترداد في شهره.

**الإصلاح المقترح:** الإيراد = تحصيلات الفترة − الاستردادات المسجّلة في الفترة (معاملات الاسترداد موجودة أصلاً).
**يحتاج قرارك:** هل الاسترداد يظهر في شهر حدوثه (المعيار المحاسبي) أم يُعدّل الشهر الأصلي (السلوك الحالي للاسترداد الكامل).

**للقياس:**
```sql
SELECT p.id, p.payment_number, p.amount paid, SUM(t.amount) refunded
FROM payments p JOIN transactions t ON t.payment_id = p.id AND t.type = 'debit' AND JSON_EXTRACT(t.metadata, '$.refunded_at') IS NOT NULL
WHERE p.status = 'completed' GROUP BY p.id, p.payment_number, p.amount;
```

---

## 🟡 المتوسطة

**M1 — المحافظ:**
- `OrderController::approve` يغلّف `Wallet::deposit()` بـ `catch (\Throwable)` ويكمل. `deposit()` يزيد الرصيد **ثم** يكتب الحركة؛
  لو فشلت الكتابة يبقى الرصيد زائداً بدون حركة ويتم الـ commit.
- `Wallet::withdraw()` يفحص الرصيد على نسخة في الذاكرة. `ExpenseController` يجلب المحفظة بـ `find()` بدون قفل ← اعتمادان متزامنان قد يجعلان الرصيد سالباً
  (التحويل بين محافظ الطالب ومسير الرواتب يستخدمان القفل صحيحاً).
- **إصلاح:** عدم ابتلاع خطأ الإيداع (فشل الاعتماد كله) + `lockForUpdate` قبل السحب.
- **للقياس:** `SELECT w.id, w.balance, COALESCE(SUM(CASE WHEN wt.type='deposit' THEN wt.amount ELSE -wt.amount END),0) ledger FROM wallets w LEFT JOIN wallet_transactions wt ON wt.wallet_id=w.id AND wt.status='completed' GROUP BY w.id, w.balance HAVING ROUND(w.balance - ledger, 2) <> 0;`

**M2 — تسجيل الطالب بعد الدفع:** فشل إنشاء/تحديث التسجيل في الكورس أو المسار يُسجَّل في اللوج فقط ويُعتمد الطلب.
**إصلاح:** فشل التسجيل = فشل الاعتماد (تراجع كامل) أو وضع الطلب في حالة «يحتاج مراجعة».

**M3 — الفاتورة:** عند الاعتماد `subtotal = total = المبلغ الصافي` و `discount_amount = 0` رغم أن الطلب يحتوي `original_amount` و `discount_amount`.
**إصلاح للفواتير الجديدة فقط:** `subtotal = original_amount`، `discount_amount = discount_amount`، `total = amount`.

**M4 — الحذف النهائي:** `InvoiceController::destroy` و `PaymentController::destroy` و `TransactionController::destroy` تحذف نهائياً بدون عكس
أثر المحفظة/التسجيل، وتكسر سجل التدقيق، وهي ما يُطلق C2. **اقتراح:** «إلغاء/إبطال» بالحالة بدل الحذف (يحتاج قرارك).

**M5 — كورس مجاني بطلب مدفوع:** كورس `is_free = 1` قَبِل طلب دفع يدوي بإيصال: أصلي 1500، خصم 0، صافي 0.
**إصلاح:** إذا كان الصافي صفراً يُحوَّل للتسجيل المجاني، ويُسجَّل الخصم = الأصلي.

---

## 🔵 ملاحظات أقل خطورة / للتحقق

- **L1:** إذا كان `FAWATERAK_PLUGIN_BEARER_TOKEN` فارغاً، يُرسَل `vendor_key` إلى متصفح المشتري داخل `pluginConfig.token`.
  يجب التأكد من وثائق فواتيرك أن هذا المفتاح مسموح عرضه؛ وإلا يُضبط توكن الإضافة المنفصل في `.env` الإنتاج.
- **L2:** عودة كاشير لا تقارن المبلغ المدفوع بمبلغ الطلب (التوقيع يغطي البيانات، فالخطر منخفض).
- **L3:** `balance_before` في حركات المحفظة يُقرأ من نسخة قديمة في الذاكرة (شكلي).
- **L4:** مشرف بدور `admin` (ليس `super_admin`) يرى **500** بدل 403 في صفحات المدفوعات/المعاملات/المحافظ/المصروفات/الاشتراكات
  لأن الـ controllers تلتقط استثناء الصلاحية وتحوله لخطأ عام.
- **L5:** `app/Http/Controllers/ExpenseController.php` نسخة قديمة غير مستخدمة من `Admin/ExpenseController` (لا route يشير إليها).
- **L6:** نسبة المدرب تُحسب على السعر النهائي قبل خصم عمولة البوابة؛ قرار سياسة: على الإجمالي أم الصافي؟

## ترتيب الإصلاح المقترح

| الدفعة | المحتوى | لماذا أولاً | أثر على الإنتاج |
|---|---|---|---|
| 1 | C2 (ترقيم آمن) + H1 (قفل الاعتماد) + M1 (المحافظ) | تمنع أخطاء وازدواج فلوس، بدون أي تغيير في الأرقام المعروضة | كود فقط |
| 2 | C1 (تحقق فواتيرك) | يمنع الكورسات بدون دفع | كود فقط؛ يحتاج تجربة على بيئة فواتيرك التجريبية |
| 3 | H3 + H4 + M3 (التقارير والفواتير الجديدة) | تصحيح الأرقام | تتغير أرقام التقارير (للأصح)؛ لا تعديل بيانات |
| 4 | H2 + M2 + M4 + M5 | تحتاج قرارات سياسة | حسب القرار |

قبل أي دفعة: تشغيل استعلامات «للقياس» أعلاه على الإنتاج (كلها `SELECT` فقط) لمعرفة حجم كل مشكلة فعلياً.

---

## ✅ الدفعة 1 – ما تم (2026-10-23): C2 + H1 + M1 (كود فقط)

**C2 — الترقيم:** `App\Domains\Accounting\Support\DocumentNumber::next($model, $column, $prefix, $pad)`.
- الرقم = `max(عدد الصفوف، أكبر رقم بنفس البادئة) + 1`. **بدون حذف = `count() + 1` تماماً** ← لا يتغير أي رقم على الإنتاج؛
  بعد حذف يتخطى الرقم المستخدم بدل أن يتكرر.
- داخل transaction يقفل آخر صف بنفس البادئة، فعمليتان متزامنتان تنتظران بعضهما.
- طُبّق على 38 موضعاً: INV / PAY / TXN / EXP / DEB / WDR / AGR / SALE-COM / SALE-BON / ONL-TXN / OFF-PAY / OFF-TXN / OFF-REF /
  رواتب الموظفين (PAY-سنة) / PLI / PMS. لم يُغيَّر: كود الوظائف في HR (ليس مستنداً مالياً) و `app/Http/Controllers/ExpenseController.php` (غير مستخدم).

**H1 — القفل:** `Order::whereKey(...)->lockForUpdate()` بدل `refresh()` في `OrderController::approve()` و `reject()`،
وفي `CheckoutController::kashierCallback()` (لو وصلت العودة الثانية بعد اعتماد الأولى تُعامل كنجاح بدون اعتماد ثانٍ).

**M1 — المحافظ:** `Wallet::deposit()` و `withdraw()` و `refund()` (نفس التوقيعات) صارت عبر دالة واحدة:
- قفل صف المحفظة + فحص الرصيد من قاعدة البيانات + تغيير الرصيد وتسجيل الحركة في transaction واحدة (أو لا شيء).
- `balance_before` صار دقيقاً.
- `OrderController::approve()`: فشل الإيداع **يوقف الموافقة كلها** مع رسالة واضحة للمشرف، بدل warning في اللوج مع اكتمال الموافقة.

**الاختبارات:** `tests/Unit/Accounting/DocumentNumberTest.php` (7) و `WalletMovementTest.php` (6) – كلها تمر.
الـ suite: 219 passed، والـ 11 failures نفسها السابقة.

**تحقق محلي على MySQL:**
- فاتورة وهمية `INV-00000003` مع عدد فواتير = 2 (الصيغة القديمة كانت ستولّد `INV-00000003` وتفشل) ← الاعتماد نجح بـ `INV-00000004` و `PAY-00000002` و `TXN-00000002`.
- طلبا اعتماد لنفس الطلب ← واحد نجح والثاني رُفض؛ فاتورة/دفعة/معاملة واحدة، والمحفظة 1500 ← 2300 مرة واحدة.
- عملية تمسك قفل الطلب 5 ثوانٍ ← طلب الاعتماد انتظر 4.2 ثانية ثم رُفض بـ 409 «تم تعديل حالة الطلب بالفعل» بدون أي فاتورة.

**⚠️ قبل النشر على الإنتاج:** ابحث في `storage/logs/laravel*.log` على الإنتاج عن:
```
Wallet deposit skipped during order approval
```
إن وُجد، فهناك سبب حقيقي يجعل الإيداع يفشل، وبعد هذه الدفعة ستتوقف تلك الموافقات (بدل أن تكتمل بفرق في رصيد المحفظة). يجب معرفة السبب أولاً.

**متبقٍ (خارج الدفعة):** `OfflineEnrollmentProvisioner` و `Admin/OfflineEnrollmentController` ما زالا يتجاهلان فشل الإيداع ويكملان.
بعد هذه الدفعة لم يعد ذلك يترك رصيداً بدون حركة (الإيداع صار ذرّياً)، لكن الدفعة تبقى مربوطة بمحفظة لم يدخلها المبلغ.

---

## ⏸️ الدفعة 2 (C1 — فواتيرك) – لم تبدأ في الكود بعد (2026-10-23)

**مؤكَّد من الوثائق الرسمية:** `GET {app|staging}.fawaterk.com/api/v2/getInvoiceData/{invoice_id}` بـ `Authorization: Bearer <vendor key>`
يُرجع `data.paid` (1/0)، `paid_at`، `total`، `currency`، `customer_email`، `pay_load`. (v2 معلن إيقافه 2027-01-01.)

**التصميم المتفق عليه للتنفيذ:**
1. `FawaterakService::fetchInvoice($id)` عبر Http (timeout 15s) بالـ vendor key.
2. في `fawaterakReturn('success')`: قراءة رقم فاتورة فواتيرك من الـ query (`invoice_id` / `invoiceId` / `InvoiceId`)، ثم الاعتماد فقط إذا:
   `paid == 1` و `total` = مبلغ الطلب (±0.01) و `currency` مطابقة و `customer_email` = بريد الطالب، والرقم غير مستخدم في `payments.transaction_id` لدفعة أخرى.
   يُخزَّن رقم فاتورة فواتيرك في `payments.transaction_id`.
3. أي فشل/عدم وصول للـ API ← الطلب يبقى `pending` + رسالة «جاري التحقق من الدفع» + log؛ الاعتماد اليدوي من الإدارة يبقى كما هو.
4. اختبارات بـ `Http::fake()`: مدفوع/غير مدفوع/مبلغ مختلف/بريد مختلف/رقم مكرر/API معطل/بدون رقم فاتورة.

**قبل التنفيذ (مهم):** شغّل على الإنتاج
`SELECT gateway_response FROM payments WHERE payment_gateway='fawaterak' ORDER BY id DESC LIMIT 5;`
لمعرفة اسم الحقل الذي يرسله فواتيرك فعلاً في رابط العودة (داخل `query`). إن لم يكن فيه رقم فاتورة، يلزم مسار آخر (webhook موقّع).
