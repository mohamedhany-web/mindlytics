<?php $__env->startSection('title', 'ربط WhatsApp Business — Meta Cloud API'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
    $savedEnabled = (bool) old('enable_service', $config['enabled'] ?? false);
    $savedAppId = old('app_id', $config['app_id'] ?? '');
    $savedPhoneId = old('phone_number_id', $config['phone_number_id'] ?? '');
    $savedWaba = old('business_account_id', $config['business_account_id'] ?? '');
    $savedApiUrl = old('api_url', $config['api_url'] ?? '');
    $savedWebhookToken = old('webhook_verify_token', $config['webhook_verify_token'] ?? '');
    $displayPhone = $connectionMeta['display_phone'] ?? $config['display_phone_number'] ?? '—';
    $displayName = $connectionMeta['display_name'] ?? $config['verified_display_name'] ?? '—';
    $hasSecret = (bool) ($config['has_app_secret'] ?? false);
    $hasToken = (bool) ($config['has_access_token'] ?? false);
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'ربط WhatsApp Business (Meta)',
        'subtitle' => 'إعداد يدوي عبر Meta Cloud API — بدون Bridge أو QR.',
        'icon' => 'fab fa-whatsapp',
        'actions' => '
            <button type="button" id="btn-test-connection" class="' . $waBtnSecondary . '"><i class="fas fa-vial"></i> اختبار الربط</button>
            <a href="' . route('admin.whatsapp.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-tachometer-alt"></i> لوحة الواتساب</a>
        ',
        'statCards' => [
            ['label' => 'حالة الربط', 'value' => $isConnected ? 'متصل' : 'غير متصل', 'icon' => $isConnected ? 'fas fa-check-circle' : 'fas fa-exclamation-circle', 'bg' => $isConnected ? 'bg-emerald-100' : 'bg-amber-100', 'text' => $isConnected ? 'text-emerald-600' : 'text-amber-600', 'description' => $connectionMeta['label'] ?? '—'],
            ['label' => 'إرسال الواتساب', 'value' => $savedEnabled ? 'مفعّل' : 'معطّل', 'icon' => 'fas fa-power-off', 'bg' => $savedEnabled ? 'bg-sky-100' : 'bg-slate-100', 'text' => $savedEnabled ? 'text-sky-600' : 'text-slate-600', 'description' => $savedEnabled ? 'جاهز بعد اكتمال Token' : 'فعّل من النموذج'],
            ['label' => 'App ID', 'value' => $savedAppId !== '' ? $savedAppId : '—', 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => $hasSecret ? 'App Secret محفوظ' : 'App Secret مطلوب'],
            ['label' => 'Phone Number ID', 'value' => $savedPhoneId !== '' ? $savedPhoneId : '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => $hasToken ? 'Access Token محفوظ' : 'Access Token مطلوب'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="test-result" class="hidden rounded-xl border px-4 py-3 text-sm"></div>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-database text-emerald-600"></i>
                البيانات المحفوظة حالياً
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحدَّث تلقائياً بعد كل حفظ ناجح</p>
        </div>
        <div class="p-5 sm:p-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">رقم الواتساب</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($displayPhone); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">الاسم المعتمد</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($displayName); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Phone Number ID</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedPhoneId !== '' ? $savedPhoneId : '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">WABA ID</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedWaba !== '' ? $savedWaba : '—'); ?></p>
                </div>
            </div>

            <?php if(!empty($connectionMeta['last_error']) && !$isConnected): ?>
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 space-y-2">
                    <p><i class="fas fa-exclamation-triangle ml-1"></i> <?php echo e($connectionMeta['last_error']); ?></p>
                    <?php if(str_contains(mb_strtolower($connectionMeta['last_error'] ?? ''), 'token') || str_contains(mb_strtolower($connectionMeta['last_error'] ?? ''), 'صلاحية')): ?>
                        <div class="text-xs text-rose-900/90 leading-relaxed border-t border-rose-200 pt-2">
                            <p class="font-bold mb-1">كيف تحصل على Token صحيح؟</p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>افتح <a href="https://business.facebook.com/settings/system-users" target="_blank" rel="noopener" class="underline font-semibold">Meta Business → System Users</a></li>
                                <li>أنشئ System User أو اختر موجوداً → Generate Token</li>
                                <li>اختر تطبيقك وفعّل: <code>whatsapp_business_messaging</code> و <code>whatsapp_business_management</code></li>
                                <li>اختر «Never» للصلاحية (توكن دائم) — لا تستخدم توكن مؤقت من API Setup</li>
                                <li>اضغط «مسح بيانات الربط» ثم الصق التوكن الجديد في Access Token واحفظ</li>
                                <li>فعّل «تفعيل إرسال الواتساب» ثم اضغط «اختبار الربط»</li>
                            </ol>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($hasSecret || $hasToken || $savedAppId !== '' || $savedPhoneId !== ''): ?>
                <form method="POST" action="<?php echo e(route('admin.whatsapp.disconnect')); ?>" class="mt-4"
                      onsubmit="return confirm('مسح بيانات الربط وتعطيل الإرسال؟');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                        <i class="fas fa-trash-alt"></i>
                        مسح بيانات الربط
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-key text-emerald-600"></i>
                إعدادات Meta Cloud API
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحفظ في المنصة (مشفّرة) — لا حاجة لتعديل ملف <code class="bg-slate-100 px-1 rounded">.env</code></p>
        </div>
        <div class="p-5 sm:p-6">
            <form method="POST" action="<?php echo e(route('admin.whatsapp.settings.update')); ?>" class="space-y-5" id="wa-settings-form">
                <?php echo csrf_field(); ?>

                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-black">1</span>
                        بيانات التطبيق (Meta App)
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Meta App ID *</label>
                            <input type="text" name="app_id" value="<?php echo e($savedAppId); ?>" required
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="123456789012345">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">
                                Meta App Secret <?php echo e($hasSecret ? '' : '*'); ?>

                                <?php if($hasSecret): ?>
                                    <span class="inline-flex items-center gap-1 text-emerald-700 text-[11px] font-bold mr-1"><i class="fas fa-lock"></i> محفوظ</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" name="app_secret" value=""
                                   autocomplete="new-password" class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm"
                                   placeholder="<?php echo e($hasSecret ? 'محفوظ — اتركه فارغاً للإبقاء' : 'App Secret من Meta Developers'); ?>"
                                   <?php if(!$hasSecret): ?> required <?php endif; ?>>
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?php echo e($waLabelClass); ?>">Graph API URL *</label>
                            <input type="url" name="api_url" value="<?php echo e($savedApiUrl); ?>" required
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-[11px] font-black">2</span>
                        بيانات الإرسال (WhatsApp Business)
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">
                                Access Token
                                <?php if($hasToken): ?>
                                    <span class="inline-flex items-center gap-1 text-emerald-700 text-[11px] font-bold mr-1"><i class="fas fa-lock"></i> محفوظ</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" name="access_token" value=""
                                   autocomplete="new-password" class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm"
                                   placeholder="<?php echo e($hasToken ? 'محفوظ — اتركه فارغاً للإبقاء' : 'System User Token من Meta'); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Phone Number ID</label>
                            <input type="text" name="phone_number_id" value="<?php echo e($savedPhoneId); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="من WhatsApp → API Setup">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">WhatsApp Business Account ID (WABA)</label>
                            <input type="text" name="business_account_id" value="<?php echo e($savedWaba); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="اختياري — للـ Webhook">
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer px-4 py-2.5 rounded-xl border-2 <?php echo e($savedEnabled ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white'); ?> w-full">
                                <input type="checkbox" name="enable_service" value="1" <?php if($savedEnabled): echo 'checked'; endif; ?> class="rounded text-emerald-600">
                                تفعيل إرسال الواتساب
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-[11px] font-black">3</span>
                        Webhook — لاستقبال ردود العملاء وتتبع التسليم
                    </p>
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-xs text-amber-950 leading-relaxed mb-4 space-y-2">
                        <p class="font-bold">مهم: بدون Webhook لن تظهر رسائل العملاء في المحادثات</p>
                        <p>الرسائل الصادرة من النظام تُسجَّل تلقائياً، لكن <strong>ردود العملاء</strong> تصل فقط عبر Webhook من Meta. بدون ربط Webhook ستشاهد رسائلك فقط وليس رد العميل.</p>
                        <p>يجب أن يكون Callback URL على نطاق <strong>HTTPS عام</strong> (ليس localhost) — Meta لا يرسل أحداثاً إلى جهازك المحلي مباشرة.</p>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Webhook Verify Token</label>
                            <input type="text" name="webhook_verify_token" value="<?php echo e($savedWebhookToken); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="مثال: mindlytics_wh_2026">
                            <p class="text-[10px] text-slate-500 mt-1">اختر أي نص — <strong>احفظ الإعدادات أولاً</strong> ثم انسخه نفسه في Meta</p>
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Webhook URL (انسخه إلى Meta)</label>
                            <input type="text" readonly value="<?php echo e($config['webhook_url']); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm bg-slate-50" onclick="this.select()">
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-700 leading-relaxed space-y-2">
                        <p class="font-bold text-slate-800">خطوات الربط في Meta Developers:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>اكتب Verify Token أعلاه واضغط <strong>حفظ الإعدادات</strong> — لا تربط في Meta قبل الحفظ.</li>
                            <li>افتح <strong>developers.facebook.com</strong> → تطبيقك → <strong>WhatsApp → Configuration</strong>.</li>
                            <li>Callback URL: <code class="dir-ltr"><?php echo e($config['webhook_url']); ?></code> (حرفياً — ليس WhatsappWebhook)</li>
                            <li>Verify Token: نفس القيمة المحفوظة → <strong>Verify and save</strong></li>
                            <li>اشترك في: <code>messages</code> (لردود العملاء) و <code>message_status</code> (لحالة التسليم)</li>
                            <li>في نفس الصفحة: <strong>عطّل</strong> خيار «Attach a client certificate» إلا إذا ضبطت mTLS على السيرفر.</li>
                            <li>بعد الحفظ: انزل لجدول <strong>Webhook fields</strong> وفعّل Subscribe أمام <code>messages</code> (زر أزرق).</li>
                        </ol>

                        <div id="webhook-status-panel" class="mt-4 rounded-xl border border-slate-200 bg-white p-4 text-slate-800 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-bold text-sm">حالة Webhook من Meta</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" id="btn-refresh-webhook" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-700 text-white">
                                        <i class="fas fa-sync-alt"></i> تحديث الحالة
                                    </button>
                                    <button type="button" id="btn-sync-webhook" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-violet-600 hover:bg-violet-700 text-white">
                                        <i class="fas fa-link"></i> مزامنة الاشتراك مع Meta
                                    </button>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500">المزامنة تربط Callback URL وحقول messages و message_status تلقائياً (يتطلب App Secret محفوظاً).</p>
                            <div id="webhook-status-summary" class="grid sm:grid-cols-2 gap-2 text-xs"></div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs border border-slate-200 rounded-lg overflow-hidden">
                                    <thead class="bg-slate-50 text-slate-600">
                                        <tr>
                                            <th class="text-right px-3 py-2">الحقل</th>
                                            <th class="text-right px-3 py-2">الوصف</th>
                                            <th class="text-right px-3 py-2">الاشتراك في Meta</th>
                                        </tr>
                                    </thead>
                                    <tbody id="webhook-fields-body"></tbody>
                                </table>
                            </div>
                            <div id="webhook-issues-box" class="hidden rounded-lg border border-rose-200 bg-rose-50 p-3 text-rose-900 text-xs"></div>
                            <p id="webhook-action-result" class="hidden text-xs"></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-sky-50 border border-sky-200 p-4 text-xs text-sky-900 leading-relaxed space-y-2">
                    <p class="font-bold">صلاحيات Meta المطلوبة:</p>
                    <p><code>whatsapp_business_management</code> · <code>whatsapp_business_messaging</code> · <code>business_management</code></p>
                    <p>بعد الحفظ: أضف Webhook URL في Meta Developers → WhatsApp → Configuration. للرسائل النصية الحرة: المستلم يجب أن يراسل <strong>+<?php echo e(ltrim($config['display_phone_number'] ?? '', '+') ?: 'رقم الواتساب'); ?></strong> أولاً خلال 24 ساعة.</p>
                </div>

                <button type="submit" class="<?php echo e($waBtnPrimary); ?>">
                    <i class="fas fa-save"></i>
                    حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-paper-plane text-emerald-600"></i>
                اختبار إرسال رسالة
            </h3>
        </div>
        <div class="p-5 sm:p-6 space-y-3">
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">رقم الاختبار</label>
                    <input type="text" id="test-phone" placeholder="2010xxxxxxx" class="<?php echo e($waInputClass); ?> text-sm dir-ltr">
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">نص الرسالة (اختياري)</label>
                    <input type="text" id="test-message" placeholder="رسالة الاختبار" class="<?php echo e($waInputClass); ?> text-sm">
                </div>
            </div>
            <p class="text-[11px] text-slate-500">اترك الرقم فارغاً للتحقق من Token فقط. لإرسال حقيقي: يجب أن يكون المستلم ضمن نافذة 24 ساعة أو Template معتمد.</p>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const testUrl = <?php echo json_encode(route('admin.whatsapp.test-connection'), 15, 512) ?>;
    const webhookRefreshUrl = <?php echo json_encode(route('admin.whatsapp.webhook.refresh'), 15, 512) ?>;
    const initialWebhook = <?php echo json_encode($webhookDiagnostics ?? [], 15, 512) ?>;

    function badge(ok, yes = 'مشترك', no = 'غير مشترك') {
        return ok
            ? '<span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">' + yes + '</span>'
            : '<span class="inline-flex px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold">' + no + '</span>';
    }

    function fieldBadge(subscribed) {
        if (subscribed === null || subscribed === undefined) {
            return '<span class="inline-flex px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold">غير معروف</span>';
        }
        return badge(!!subscribed);
    }

    function renderWebhookPanel(webhook) {
        const meta = webhook?.meta || {};
        const summary = document.getElementById('webhook-status-summary');
        const tbody = document.getElementById('webhook-fields-body');
        const issuesBox = document.getElementById('webhook-issues-box');
        if (!summary || !tbody) return;

        summary.innerHTML = [
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">Callback في Meta</span><p class="dir-ltr break-all font-mono text-[11px] mt-1">' + (meta.callback_url || '—') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">رابط النظام</span><p class="dir-ltr break-all font-mono text-[11px] mt-1">' + (webhook.webhook_url || '—') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">تطابق الرابط</span><p class="mt-1">' + badge(meta.callback_matches === true, 'متطابق', 'غير متطابق') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">اشتراك WABA</span><p class="mt-1">' + badge(meta.waba_app_subscribed === true, 'نعم', 'لا') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">آخر Webhook وصل</span><p class="mt-1 text-[11px]">' + (webhook.last_webhook_at || 'لم يصل بعد') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">Meta يصل للسيرفر</span><p class="mt-1">' + badge(!!webhook.webhook_reachable, 'نعم', 'لا') + '</p></div>',
            '<div class="rounded-lg border border-slate-200 bg-slate-50 p-2"><span class="text-slate-500">رسائل واردة مسجّلة</span><p class="mt-1 font-bold">' + (webhook.inbound_message_count ?? 0) + '</p></div>',
        ].join('');

        const rows = webhook.field_rows?.length ? webhook.field_rows : (meta.field_rows || []);
        tbody.innerHTML = rows.map(row => (
            '<tr class="border-t border-slate-100">'
            + '<td class="px-3 py-2 font-mono dir-ltr">' + row.field + (row.required ? ' *' : '') + '</td>'
            + '<td class="px-3 py-2">' + (row.label || row.field) + '</td>'
            + '<td class="px-3 py-2">' + fieldBadge(row.subscribed) + '</td>'
            + '</tr>'
        )).join('') || '<tr><td colspan="3" class="px-3 py-3 text-slate-500">اضغط «تحديث الحالة» لقراءة الحقول من Meta</td></tr>';

        const issues = webhook.issues || [];
        if (issuesBox) {
            if (issues.length) {
                issuesBox.className = 'rounded-lg border border-rose-200 bg-rose-50 p-3 text-rose-900 text-xs';
                issuesBox.classList.remove('hidden');
                issuesBox.innerHTML = '<p class="font-bold mb-1">ملاحظات:</p><ul class="list-disc list-inside space-y-1">' + issues.map(i => '<li>' + i + '</li>').join('') + '</ul>';
            } else if (webhook.receiving_replies) {
                issuesBox.classList.remove('hidden');
                issuesBox.className = 'rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-emerald-900 text-xs';
                issuesBox.innerHTML = '<i class="fas fa-check-circle ml-1"></i> Webhook يعمل ويستقبل رسائل واردة.';
            } else {
                issuesBox.classList.add('hidden');
                issuesBox.innerHTML = '';
            }
        }
    }

    async function callWebhookRefresh(sync) {
        const box = document.getElementById('webhook-action-result');
        if (box) {
            box.classList.remove('hidden', 'text-emerald-700', 'text-rose-700');
            box.textContent = sync ? 'جاري المزامنة مع Meta…' : 'جاري تحديث الحالة…';
        }
        try {
            const res = await fetch(webhookRefreshUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ sync: !!sync }),
            });
            const data = await res.json();
            if (data.webhook) renderWebhookPanel(data.webhook);
            if (box) {
                if (data.success || (data.webhook?.meta?.messages_subscribed)) {
                    box.classList.add('text-emerald-700');
                    box.textContent = sync ? 'تمت المزامنة — راجع جدول الحقول أعلاه.' : 'تم تحديث الحالة من Meta.';
                } else {
                    box.classList.add('text-rose-700');
                    box.textContent = data.sync?.app?.error || data.sync?.waba?.error || 'تعذّر التحقق — راجع الملاحظات.';
                }
            }
        } catch (_) {
            if (box) {
                box.classList.add('text-rose-700');
                box.textContent = 'خطأ في الاتصال بالسيرفر';
            }
        }
    }

    renderWebhookPanel(initialWebhook);
    document.getElementById('btn-refresh-webhook')?.addEventListener('click', () => callWebhookRefresh(false));
    document.getElementById('btn-sync-webhook')?.addEventListener('click', () => callWebhookRefresh(true));

    document.getElementById('btn-test-connection')?.addEventListener('click', async function () {
        const box = document.getElementById('test-result');
        if (!box) return;

        box.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800');
        box.textContent = 'جاري الاختبار…';

        const form = document.getElementById('wa-settings-form');
        const fd = new FormData(form);
        fd.append('test_phone', document.getElementById('test-phone')?.value || '');
        fd.append('test_message', document.getElementById('test-message')?.value || '');

        try {
            const res = await fetch(testUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                box.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
                box.textContent = data.message || 'نجح الاختبار — يمكنك تحديث الصفحة لرؤية البيانات المحدّثة.';
            } else {
                box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
                box.textContent = data.error || 'فشل الاختبار';
            }
        } catch (e) {
            box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
            box.textContent = 'خطأ في الاتصال بالسيرفر';
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\settings.blade.php ENDPATH**/ ?>