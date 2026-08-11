# Mindlytics — دليل تقني: واتساب · ميتا · الحملات · Lead Center

وثيقة تقنية لشرح ما هو مبني في النظام حول **WhatsApp Cloud**، **Meta Social (فيسبوك/إنستجرام/ماسنجر)**، **Meta Ads**، **الحملات الإعلانية الداخلية**، **تتبع التسويق**، و**مركز الليدز**.

> آخر تحديث مرتبط بميزات التسويق / Meta Ads / المناطق (أغسطس 2026).

---

## 1) نظرة معمارية عامة

```
┌─────────────────────────────────────────────────────────────────┐
│                     Mindlytics Admin / Sales                      │
└─────────────────────────────────────────────────────────────────┘
         │                    │                      │
         ▼                    ▼                      ▼
  WhatsApp Cloud API    Meta Graph (Social)     Meta Marketing API
  (WABA + Phone ID)     OAuth + Pages + IG      (Ad Accounts)
         │                    │                      │
         ▼                    ▼                      ▼
  whatsapp_* tables     meta_social_*          إعدادات JSON فقط
  + sales_leads         + Lead Center          (حملات على Meta مباشرة)
         │                    │
         └────────┬───────────┘
                  ▼
            sales_leads (CRM)
                  │
                  ▼
     advertising_campaigns (تتبع داخلي للسيلز)
                  │
                  ▼
     GTM / GA4 / Clarity / Meta Pixel (الويب العام)
```

| النظام | الغرض |
|--------|--------|
| **WhatsApp** | محادثات Cloud API، Inbox، طوابير، قوالب، دفعات، ربط CRM |
| **Meta Social** | ربط الصفحات، Messenger/IG Inbox، Lead Center، التقاط رقم/إيميل |
| **Meta Ads** | إدارة حملات Meta الحقيقية عبر Marketing API (نفس توكن السوشيال) |
| **الحملات الإعلانية** | سجل داخلي لتكلفة الكامبين + تقارير يومية من السيلز |
| **تتبع التسويق** | GTM / GA4 / Clarity / Meta Pixel على الصفحات العامة |
| **المناطق** | خريطة ودول + تواجد داخل مصر من البيانات الحالية |

---

## 2) WhatsApp (Cloud API)

### 2.1 الفكرة
النظام يدعم مسارين:

| الوضع | الوصف |
|-------|--------|
| **official / Cloud API** | المسار الأساسي عبر Meta WhatsApp Business Cloud |
| **Bridge (whatsapp-web.js)** | اختياري/قديم عبر Node في `whatsapp-bridge/` |

### 2.2 ملفات أساسية

| الطبقة | المسار |
|--------|--------|
| إعدادات Cloud | `app/Support/WhatsAppCloudSettings.php` |
| إعدادات Bridge | `app/Support/WhatsAppBridgeSettings.php` |
| Config | `config/whatsapp.php` + `config/services.php` → `whatsapp` |
| إرسال | `app/Services/WhatsAppService.php`, `WhatsAppCloudService.php` |
| Inbox / وارد | `app/Services/WhatsAppInboxService.php` |
| CRM | `app/Services/WhatsAppCrmService.php` |
| توزيع/تعيين | `app/Services/WhatsAppAssignmentService.php`, `WhatsAppQueueService.php` |
| دفعات | `app/Services/WhatsAppBatchService.php` + Job `ProcessWhatsAppBatchJob` |
| قوالب | `WhatsAppTemplateService`, `WhatsAppSuggestedTemplateService` |
| Webhook | `app/Http/Controllers/WhatsAppWebhookController.php` |
| Admin | `app/Http/Controllers/Admin/WhatsApp*.php` |
| سيلز | `Employee/SalesWhatsAppInboxController`, `SalesManagerWhatsAppInboxController` |

### 2.3 التخزين

**JSON (local disk):**

- `storage/app/site/whatsapp_cloud_settings.json` — App ID / Secret / Token / Phone Number ID / WABA (أسرار مشفّرة)
- `storage/app/site/whatsapp_webhook_status.json`
- Bridge: `storage/app/public/site/whatsapp_settings.json`

**جداول مهمة:**

- `whatsapp_business_connections`
- `whatsapp_conversations` / `whatsapp_conversation_messages`
- `whatsapp_contacts`
- `whatsapp_batches` / `whatsapp_batch_items`
- `whatsapp_meta_templates`
- `whatsapp_messages`
- tags / notes / events

### 2.4 المسارات (Routes)

| الاسم | الوظيفة |
|-------|---------|
| `webhooks.whatsapp.verify` | `GET /webhooks/whatsapp` — تحقق Meta |
| `webhooks.whatsapp.handle` | `POST /webhooks/whatsapp` — رسائل/حالات |
| `admin.whatsapp.*` | لوحة، إرسال، Inbox، قوالب، إعدادات، batches، تقارير |
| `sales.whatsapp.inbox.*` | Inbox موظف المبيعات |
| `sales-manager.whatsapp.*` | Inbox المدير + طابور غير المعيَّن |

### 2.5 التدفقات

1. **الربط:** أدمن → إعدادات واتساب → حفظ مفاتيح Cloud (+ اشتراك Webhook إن لزم).
2. **استقبال:** Meta → Webhook → `WhatsAppInboxService` → محادثة/رسائل → ربط `whatsapp_contacts` / `sales_leads` → تعيين أو طابور.
3. **إرسال:** Inbox / إرسال يدوي / Batch → `WhatsAppService` (+ pacing) → Graph Cloud API.
4. **الطابور:** رسائل واردة غير معيَّنة → `WhatsAppQueueService` → السيلز يدّعيها.
5. **الدفعات:** `whatsapp_batches` تُعالج على queue اسمه `whatsapp` (`ProcessWhatsAppBatchJob`).

### 2.6 Env (مراجع / fallback)

```env
WHATSAPP_ENABLED=
WHATSAPP_APP_ID=
WHATSAPP_APP_SECRET=
WHATSAPP_API_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_WEBHOOK_VERIFY_TOKEN=
WHATSAPP_WEBHOOK_BASE_URL=
WHATSAPP_QUEUE=whatsapp
WHATSAPP_QUEUE_ENABLED=
WHATSAPP_ASSIGNMENT_STRATEGY=
```

> عمليًا: الواجهة الإدارية هي مصدر الحقيقة لمفاتيح Cloud (تُحفظ في JSON مشفّر).

---

## 3) Meta Social (فيسبوك · إنستجرام · ماسنجر)

### 3.1 الفكرة
تطبيق Meta واحد عبر **Facebook Login (OAuth)** يربط:

- صفحات Facebook
- Messenger
- حساب Instagram Business المرتبط بالصفحة
- ثم Inbox + CRM + **Lead Center**

### 3.2 ملفات أساسية

| الطبقة | المسار |
|--------|--------|
| إعدادات | `app/Support/MetaSocialSettings.php` |
| Graph / OAuth | `app/Services/MetaSocial/MetaSocialGraphService.php` |
| صفحات | `MetaSocialPageService.php` |
| Inbox | `MetaSocialInboxService.php` |
| CRM | `MetaSocialCrmService.php` |
| التقاط تواصل | `MetaSocialContactCaptureService.php` |
| Lead Center | `MetaSocialLeadCenterService.php` |
| ربط الوكلاء | `MetaSocialAgentLinkService.php` |
| Webhook | `MetaSocialWebhookController.php` |
| OAuth | `Admin/MetaSocialOAuthController.php` |
| Lead Center UI | `Admin/MetaSocialLeadCenterController.php` |

### 3.3 التخزين

**JSON:**

- `storage/app/site/meta_social_settings.json` (App Secret مشفّر)
- `storage/app/site/meta_social_webhook_status.json`

**جداول:**

| جدول | الدور |
|------|--------|
| `meta_social_connections` | توكن المستخدم طويل الأمد بعد OAuth |
| `meta_social_pages` | الصفحات + page token + Instagram Business ID |
| `meta_social_conversations` | خيوط المحادثة + حقول CRM/Lead Center |
| `meta_social_messages` | الرسائل |
| `meta_social_agent_links` | ربط وكلاء Meta Business بمستخدمين محليين |

**حقول Lead/CRM على المحادثة (مهم):**

- `sales_lead_id`, `phone`, `email`, `notes`, `labels`
- `priority`, `reminder_at`, `lead_stage`

### 3.4 المسارات

| الاسم | الوظيفة |
|-------|---------|
| `webhooks.meta-social.verify/handle` | `/webhooks/meta-social` |
| `admin.meta-social.settings*` | إعدادات التطبيق |
| `admin.meta-social.oauth.*` | ربط / فصل Meta |
| `admin.meta-social.pages.*` | مزامنة/تفعيل الصفحات |
| `admin.meta-social.inbox.*` | Inbox الإدارة |
| `admin.meta-social.leads.*` | **Lead Center** |
| `admin.meta-social.agents.*` | ربط الوكلاء |
| `sales.meta-social.inbox.*` | Inbox السيلز |

### 3.5 التدفقات

1. **الربط:** إعداد App ID/Secret/Verify Token → OAuth → حفظ `MetaSocialConnection` → Sync Pages → تفعيل الصفحة → اشتراك Webhook.
2. **الاستقبال:** Webhook `object=page` → مطابقة الصفحة النشطة → `ingestMessagingEvent` (Messenger أو IG) → التقاط رقم/إيميل من النص إن وُجد.
3. **الرد:** من Inbox/Lead Center → Page Access Token → Send API.
4. **طلب رقم (Messenger):** Quick Reply لطلب الهاتف (`request-phone`).
5. **إنشاء ليد:** `MetaSocialCrmService::createLeadFromConversation` → `sales_leads` بمصدر `social`.

### 3.6 صلاحيات OAuth الافتراضية

تشمل (مع ضمان إضافة Ads للتكامل مع Meta Ads):

- `pages_*`, `pages_messaging`
- `instagram_basic`, `instagram_manage_messages`
- `business_management`
- `ads_management`, `ads_read`

### 3.7 Env

```env
META_SOCIAL_APP_ID=
META_SOCIAL_APP_SECRET=
META_SOCIAL_WEBHOOK_VERIFY_TOKEN=
META_SOCIAL_WEBHOOK_BASE_URL=
META_SOCIAL_OAUTH_BASE_URL=
META_SOCIAL_API_URL=https://graph.facebook.com/v21.0
```

**OAuth Redirect يجب أن يطابق في Meta Developers:**

`{APP_URL}/admin/meta-social/oauth/callback`

---

## 4) Lead Center (تقني)

### 4.1 أين؟
- واجهة: **السوشيال ميديا → Lead Center**
- Route prefix: `admin.meta-social.leads.*`
- View: `resources/views/admin/meta-social/leads.blade.php`
- Service: `MetaSocialLeadCenterService`

### 4.2 ماذا يفعل؟

| القدرة | التفاصيل |
|--------|----------|
| Pipeline | تبويبات: جديد / في CRM / لديه هاتف / منصة / أولوية / تذكيرات |
| Stages | مراحل نمط Business Suite ↔ `sales_leads.stage` |
| إنشاء ليد | من المحادثة → `SalesLead` (`source: social`) |
| ربط ليد موجود | `link-lead` |
| تعيين سيلز | assign حسب قواعد التعيين |
| Bulk | إجراءات جماعية |
| Export | CSV |
| أولوية / تذكير / Labels | حقول على المحادثة |
| طلب هاتف | Messenger quick reply |
| رد سريع | من داخل الليد |

### 4.3 التقاط البيانات تلقائيًا

`MetaSocialContactCaptureService`:

- يلتقط أرقام مصرية شائعة (`+20` / `01x…`) من نص الشات
- يلتقط الإيميل Regex
- يمسح تاريخ الرسائل عند الحاجة
- يدعم رد Quick Reply برقم الهاتف

إن لم يوجد رقم حقيقي عند إنشاء ليد، قد يُستخدم placeholder مثل:

`meta_{platform}_{participant_id}`

### 4.4 علاقة WhatsApp بالـ CRM (موازي لـ Lead Center)

واتساب **ليس** نفس شاشة Lead Center، لكن له CRM موازي:

- `WhatsAppCrmService`
- ربط `whatsapp_contacts` ↔ `sales_leads`
- ملاحظات/تاجات/مرحلة ليد من Inbox
- طابور غير المعيَّن للسيلز

**الناتج النهائي للطرفين:** نفس جدول `sales_leads` تقريبًا، بمصادر مختلفة (`social` / واتساب).

---

## 5) Meta Ads (حملات Meta الحقيقية)

### 5.1 الفكرة
قسم منفصل تحت التسويق لإدارة الحملات على **Meta Ads Manager عبر API** — ليس نفس «الحملات الإعلانية» الداخلية.

- يعتمد على **نفس توكن Meta Social OAuth** (لا حاجة لصق توكن جديد عادةً)
- يحتاج صلاحيات `ads_read` + `ads_management` (أعد الربط مرة إن التوكن قديم)

### 5.2 ملفات

| الملف | الدور |
|-------|--------|
| `app/Support/MetaAdsSettings.php` | Ad Account المختار + تفضيلات |
| `app/Services/MetaAds/MetaAdsGraphService.php` | Graph Marketing API |
| `Admin/MetaAdsSettingsController.php` | اختيار الحساب / اختبار |
| `Admin/MetaAdsCampaignController.php` | قائمة/إنشاء/إيقاف/ميزانية/جمهور |

### 5.3 التخزين

- `storage/app/site/meta_ads_settings.json`
- التوكن: من `MetaSocialConnection` أولًا، ثم override اختياري، ثم `.env`

### 5.4 Routes

`admin.meta-ads.settings*`  
`admin.meta-ads.campaigns.*` (index/create/show/pause/resume/budget/audience)  
`admin.meta-ads.settings.select-account`

### 5.5 القدرات المنفَّذة

- جلب Ad Accounts (شخصي + Business owned/client)
- اختيار الحساب بضغطة من البطاقات
- إنشاء Campaign + Ad Set مع استهداف مبسّط (دولة/عمر/جنس)
- تشغيل / إيقاف
- تحديث الميزانية اليومية
- تحديث الجمهور

> الـ Creative (صور/فيديو الإعلان) ما زال أسهل من Ads Manager؛ النظام يركز على الحملة/الميزانية/الجمهور/الحالة.

### 5.6 Env (اختياري)

```env
META_ADS_API_URL=https://graph.facebook.com/v21.0
META_ADS_AD_ACCOUNT_ID=
META_ADS_ACCESS_TOKEN=
```

---

## 6) الحملات الإعلانية الداخلية (سيلز)

**ليست** Meta Ads API.

| العنصر | المسار / الجدول |
|--------|------------------|
| موديل | `AdvertisingCampaign`, `CampaignDailyReport` |
| خدمة | `CampaignReportService` |
| أدمن | `AdvertisingCampaignController` |
| واجهات | `resources/views/admin/marketing/advertising-campaigns/` |

### الجداول

- `advertising_campaigns` — اسم، منصة، تكلفة، تواريخ، تفعيل
- `advertising_campaign_sales_user` — أي سيلز يبلّغ على أي كامبين
- `campaign_daily_reports` — يوميًا: رسائل واتساب/ماسنجر/IG، مؤهل، محوَّل…

### التدفق

1. الأدمن ينشئ حملة داخلية ويحدد التكلفة والمنصة.
2. يعيّن موظفي سيلز.
3. السيلز يرفع أرقام يومية.
4. الأدمن يرى التقارير والتصدير.

**الفرق الجوهري:**

| Meta Ads | الحملات الإعلانية |
|----------|-------------------|
| حملات حقيقية على فيسبوك/إنستجرام | سجل تشغيلي داخلي |
| API + ميزانية على Meta | تكلفة يدوية + تقارير سيلز |

---

## 7) تتبع التسويق (GTM / GA4 / Clarity / Meta Pixel)

| الملف | الدور |
|-------|--------|
| `app/Support/MarketingWebAnalyticsSettings.php` | إعدادات من الأدمن |
| `config/analytics.php` | Defaults من `.env` |
| `MarketingAnalyticsService.php` | بناء أحداث Ecommerce |
| `tracking-tags.blade.php` | حقن GTM/Clarity/Pixel + dataLayer |
| `ecommerce-datalayer.blade.php` | دفع الأحداث |
| صفحة الأدمن | `/admin/marketing-web-analytics` |

### الأحداث على الموقع العام

- `view_item_list` / `select_item` / `view_item`
- `begin_checkout`
- `purchase` (مرة واحدة لكل `transaction_id` عبر sessionStorage)
- Meta Pixel يMirror: ViewContent / InitiateCheckout / Purchase

### أين يُحقَن؟

الصفحات العامة + مسار الشراء فقط — **ليس** admin/employee حتى لا تتلوث التقارير.

### صفحة التتبع تعرض أيضًا

تحليل تلقائي لتواجد الجمهور **داخل مصر** (استبيان / عناوين / هواتف +20 / IP الدخول).

---

## 8) المناطق / الخريطة

| الملف | الدور |
|-------|--------|
| `MarketingRegionsService` | تجميع دول + مصر |
| `GeoIpLookupService` | IP → دولة/مدينة (مع كاش DB) |
| `RecordMarketingRegionVisit` | زيارة عامة واحدة/جلسة/يوم |
| `/admin/marketing-regions` | الخريطة والجداول |

**جداول:** `geo_ip_lookups`, `marketing_region_daily_stats`

---

## 9) خريطة سريعة للواجهات (Admin)

| القسم في السايدبار | ماذا يفتح |
|--------------------|-----------|
| واتساب | Inbox، إعدادات Cloud، قوالب، batches، تقارير |
| السوشيال ميديا | إعدادات Meta، الصفحات، Inbox، **Lead Center**، الوكلاء |
| التسويق → تتبع التسويق | GTM/GA4/Clarity/Pixel + تواجد مصر |
| التسويق → Meta Ads | حسابات الإعلانات + الحملات على Meta |
| التسويق → الحملات الإعلانية | الكامبين الداخلي + تقارير السيلز |
| التسويق → المناطق | خريطة العالم + أصول الزيارات/التسجيلات/الدخول |

---

## 10) قائمة تحقق تشغيل (Production)

### واتساب
- [ ] مفاتيح Cloud محفوظة من الأدمن
- [ ] Webhook URL: `https://{domain}/webhooks/whatsapp`
- [ ] Verify Token مطابق
- [ ] Queue worker على `whatsapp` يعمل

### Meta Social
- [ ] App ID/Secret + Verify Token
- [ ] OAuth Redirect مضبوط في Meta Developers
- [ ] OAuth تم بنجاح وصفحة مفعّلة
- [ ] Webhook: `https://{domain}/webhooks/meta-social`
- [ ] Lead Center يفتح ويعرض المحادثات

### Meta Ads
- [ ] بعد OAuth بصلاحيات `ads_*`
- [ ] اختيار Ad Account من البطاقات
- [ ] اختبار إنشاء حملة بحالة `PAUSED`

### التتبع
- [ ] GTM / Pixel من `/admin/marketing-web-analytics`
- [ ] `php artisan migrate` لجدول المناطق إن لزم
- [ ] GTM Preview + Meta Test Events على مسار شراء تجريبي

---

## 11) مراجع سريعة للكود

```
app/Support/WhatsAppCloudSettings.php
app/Support/MetaSocialSettings.php
app/Support/MetaAdsSettings.php
app/Support/MarketingWebAnalyticsSettings.php

app/Services/WhatsApp*.php
app/Services/MetaSocial/*
app/Services/MetaAds/MetaAdsGraphService.php
app/Services/MarketingAnalyticsService.php
app/Services/MarketingRegionsService.php

app/Http/Controllers/WhatsAppWebhookController.php
app/Http/Controllers/MetaSocialWebhookController.php
app/Http/Controllers/Admin/MetaSocialLeadCenterController.php
app/Http/Controllers/Admin/MetaAds*Controller.php
app/Http/Controllers/Admin/MarketingWebAnalyticsController.php
app/Http/Controllers/Admin/AdvertisingCampaignController.php

resources/views/admin/whatsapp/
resources/views/admin/meta-social/
resources/views/admin/marketing/meta-ads/
resources/views/admin/marketing/web-analytics/
resources/views/admin/marketing/advertising-campaigns/
resources/views/admin/marketing/regions/
resources/views/components/tracking-tags.blade.php
```

---

## 12) ملخص جملة واحدة

**واتساب** و**ميتا سوشيال** يجلبان المحادثات إلى Inbox ويربطانها بـ **CRM / Lead Center**؛ **Meta Ads** يدير الحملات المدفوعة على Meta بنفس الربط؛ **الحملات الإعلانية** تقيس أداء السيلز داخليًا؛ **تتبع التسويق + المناطق** يقيسان الموقع والتحويلات على الويب العام.
