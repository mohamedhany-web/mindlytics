<?php $__env->startSection('title', 'إرسال واتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $canSend = (bool) ($connectionMeta['can_send'] ?? false);
    $connBadge = [
        'label' => $connectionMeta['label'] ?? 'غير معروف',
        'class' => $connectionMeta['badge_class'] ?? 'bg-slate-100 text-slate-700 border-slate-200',
    ];
    $templatesJson = $templates->map(fn ($t) => [
        'id' => $t->id,
        'title' => $t->title,
        'content' => $t->content,
    ])->values();
    $studentsJson = $students->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'phone' => $s->phone,
    ])->values();
    $waSendConfig = [
        'templates' => $templatesJson,
        'students' => $studentsJson,
        'oldRecipient' => old('recipient_type', 'manual'),
        'oldMessage' => old('message', ''),
        'oldPhone' => old('phone', ''),
        'oldUserId' => old('user_id', ''),
        'oldCourseId' => old('course_id', ''),
        'oldTemplateId' => old('template_id', ''),
    ];
    $templateVariables = [
        ['var' => '{{student_name}}', 'desc' => 'اسم الطالب'],
        ['var' => '{{courses_count}}', 'desc' => 'عدد الكورسات'],
        ['var' => '{{avg_score}}', 'desc' => 'متوسط الدرجات'],
        ['var' => '{{month_name}}', 'desc' => 'الشهر الحالي'],
        ['var' => '{{date}}', 'desc' => 'التاريخ'],
        ['var' => '{{platform_name}}', 'desc' => 'اسم المنصة'],
    ];
?>

<script>window.__waSendFormConfig = <?php echo json_encode($waSendConfig, 15, 512) ?>;</script>
<script>
function whatsappSendForm() {
    const config = window.__waSendFormConfig || {};
    return {
        recipientType: config.oldRecipient || 'manual',
        message: config.oldMessage || '',
        phone: config.oldPhone || '',
        userId: config.oldUserId ? String(config.oldUserId) : '',
        courseId: config.oldCourseId ? String(config.oldCourseId) : '',
        templateId: config.oldTemplateId ? String(config.oldTemplateId) : '',
        templates: config.templates || [],
        students: config.students || [],
        submitting: false,

        setRecipientType(type) {
            this.recipientType = type;
        },

        get charCount() {
            return (this.message || '').length;
        },

        get previewText() {
            if (!this.message || !this.message.trim()) {
                return 'اكتب رسالتك لرؤية المعاينة هنا...';
            }
            return this.message;
        },

        get recipientHint() {
            if (this.recipientType === 'manual') {
                return this.phone ? `إلى: ${this.phone}` : 'أدخل رقم الهاتف أعلاه';
            }
            if (this.recipientType === 'single_student' && this.userId) {
                const s = this.students.find(x => String(x.id) === String(this.userId));
                return s ? `إلى: ${s.name} — ${s.phone}` : '';
            }
            if (this.recipientType === 'all_students') {
                return `إرسال جماعي — ${this.students.length} طالب`;
            }
            if (this.recipientType === 'course_students') {
                return this.courseId ? 'إرسال لطلاب الكورس المحدد' : 'اختر الكورس أعلاه';
            }
            return '';
        },

        recipientPanelTitle() {
            const map = {
                manual: 'أدخل رقم الهاتف للإرسال',
                single_student: 'اختر الطالب المستلم',
                course_students: 'اختر الكورس',
                all_students: 'إرسال لجميع الطلاب',
            };
            return map[this.recipientType] || 'تفاصيل المستلم';
        },

        selectedStudentPhone() {
            const s = this.students.find(x => String(x.id) === String(this.userId));
            return s ? s.phone : '';
        },

        applyTemplate() {
            const t = this.templates.find(x => String(x.id) === String(this.templateId));
            if (t && t.content) {
                this.message = t.content;
            }
        },

        onStudentPick() {
            const s = this.students.find(x => String(x.id) === String(this.userId));
            if (s) this.phone = s.phone;
        },

        insertVariable(v) {
            this.message = (this.message || '') + v;
        },

        submitLabel() {
            const map = {
                manual: 'إرسال الآن',
                single_student: 'إرسال للطالب',
                course_students: 'إرسال لطلاب الكورس',
                all_students: 'إرسال جماعي',
            };
            return map[this.recipientType] || 'إرسال';
        },

        onSubmit(e) {
            if (this.recipientType === 'all_students' || this.recipientType === 'course_students' || this.recipientType === 'single_student') {
                const n = this.recipientType === 'single_student' ? 1
                    : (this.recipientType === 'course_students' ? 'طلاب الكورس' : this.students.length);
                if (!confirm('بدء إرسال ' + n + ' رسالة في الخلفية؟\n\nسيتم توجيهك لصفحة متابعة — من تم ومن فشل.')) {
                    e.preventDefault();
                    return;
                }
            }
            this.submitting = true;
        },

        resetForm() {
            this.message = '';
            this.phone = '';
            this.userId = '';
            this.courseId = '';
            this.templateId = '';
            this.recipientType = 'manual';
            this.submitting = false;
        },
    };
}
</script>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'send'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'إرسال رسالة واتساب',
        'subtitle' => 'رسالة فردية، طالب، كورس، أو إرسال جماعي — مع معاينة حية وقوالب جاهزة.',
        'icon' => 'fas fa-paper-plane',
        'actions' => '
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border ' . $connBadge['class'] . '">
                <span class="w-2 h-2 rounded-full ' . ($canSend ? 'bg-emerald-500 animate-pulse' : 'bg-current opacity-60') . '"></span>
                ' . $connBadge['label'] . '
            </span>
            <a href="' . route('admin.whatsapp.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-qrcode"></i> الاتصال</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(! $canSend): ?>
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 text-sm text-amber-900">
            <div class="flex items-start gap-3 flex-1">
                <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold">الواتساب غير جاهز للإرسال</p>
                    <p class="mt-0.5 text-amber-800/90">
                        <?php echo e($connectionMeta['label'] ?? 'تحقق من الاتصال'); ?>

                        <?php if(!empty($connectionMeta['last_error'])): ?>
                            — <?php echo e($connectionMeta['last_error']); ?>

                        <?php else: ?>
                            — أكمل ربط Meta من صفحة الإعدادات قبل الإرسال.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.whatsapp.index')); ?>" class="<?php echo e($waBtnPrimary); ?> text-sm shrink-0">فتح لوحة الاتصال</a>
        </div>
    <?php endif; ?>

    <?php if($canSend && !empty($connectionMeta['send_warnings'])): ?>
        <div class="rounded-2xl border-2 border-sky-200 bg-sky-50 px-5 py-4 text-sm text-sky-950 space-y-2">
            <p class="font-bold flex items-center gap-2">
                <i class="fas fa-info-circle text-sky-600"></i>
                مهم قبل الإرسال
            </p>
            <ul class="list-disc list-inside space-y-1 text-sky-900/90">
                <?php $__currentLoopData = $connectionMeta['send_warnings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($warning); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <li>الرسائل النصية الحرة (بدون قالب Meta) تُرسل فقط خلال <strong>24 ساعة</strong> من آخر رسالة من العميل لرقمكم.</li>
                <li>للتسويق أو أول تواصل: أنشئ <strong>قالب رسالة معتمد</strong> في Meta Business ثم أرسله عبر API (قريباً في المنصة).</li>
                <li>بعد الإرسال راجع <a href="<?php echo e(route('admin.whatsapp.messages')); ?>" class="underline font-semibold">سجل الرسائل</a> — إن فشل التسليم ستظهر الحالة «فشل» مع السبب.</li>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6"
         x-data="whatsappSendForm()">

        <form method="POST" action="<?php echo e(route('admin.whatsapp.send.post')); ?>" @submit="onSubmit" class="xl:col-span-8 space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="recipient_type" :value="recipientType">

            <section class="<?php echo e($waSectionClass); ?>">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-users text-emerald-600"></i>
                        المستلمون
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">اختر طريقة تحديد من تريد مراسلته</p>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <?php $__currentLoopData = [
                            'manual' => ['icon' => 'fas fa-phone', 'label' => 'رقم يدوي', 'desc' => 'إدخال رقم مباشرة'],
                            'single_student' => ['icon' => 'fas fa-user-graduate', 'label' => 'طالب', 'desc' => 'اختر من القائمة'],
                            'course_students' => ['icon' => 'fas fa-book', 'label' => 'طلاب كورس', 'desc' => 'إرسال جماعي'],
                            'all_students' => ['icon' => 'fas fa-users', 'label' => 'كل الطلاب', 'desc' => 'كل من لديه رقم'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button"
                                    @click="setRecipientType('<?php echo e($type); ?>')"
                                    :class="recipientType === '<?php echo e($type); ?>'
                                        ? 'border-emerald-500 bg-emerald-50/80 shadow-md shadow-emerald-500/10'
                                        : 'border-slate-200 bg-white hover:border-emerald-300'"
                                    class="h-full p-4 rounded-xl border-2 text-right transition-all cursor-pointer">
                                <i class="<?php echo e($meta['icon']); ?> text-emerald-600 text-lg mb-2 block"></i>
                                <p class="font-bold text-slate-900 text-sm"><?php echo e($meta['label']); ?></p>
                                <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($meta['desc']); ?></p>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <div class="rounded-xl border-2 border-emerald-200/80 bg-emerald-50/40 p-4 sm:p-5">
                        <p class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-arrow-left text-emerald-600"></i>
                            <span x-text="recipientPanelTitle()"></span>
                        </p>

                        <div x-show="recipientType === 'manual'" x-cloak x-transition.opacity>
                            <label class="<?php echo e($waLabelClass); ?>">رقم الهاتف للإرسال</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" x-model="phone"
                                       placeholder="01012345678 أو 201012345678"
                                       :required="recipientType === 'manual'"
                                       :disabled="recipientType !== 'manual'"
                                       class="<?php echo e($waInputClass); ?> pl-10 dir-ltr text-right font-mono text-base disabled:bg-slate-100 disabled:cursor-not-allowed">
                            </div>
                            <p class="text-xs text-slate-600 mt-2">أدخل رقم الواتساب — يُضاف رمز مصر +20 تلقائياً عند الإرسال.</p>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div x-show="recipientType === 'single_student'" x-cloak x-transition.opacity>
                            <label class="<?php echo e($waLabelClass); ?>">اختر الطالب</label>
                            <select name="user_id" x-model="userId"
                                    :required="recipientType === 'single_student'"
                                    :disabled="recipientType !== 'single_student'"
                                    class="<?php echo e($waSelectClass); ?> disabled:bg-slate-100" @change="onStudentPick">
                                <option value="">— اختر طالباً —</option>
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($student->id); ?>" data-phone="<?php echo e($student->phone); ?>">
                                        <?php echo e($student->name); ?> — <?php echo e($student->phone); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <template x-if="userId && selectedStudentPhone()">
                                <p class="text-xs text-emerald-800 mt-2 font-mono dir-ltr text-right">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    سيُرسل إلى: <span x-text="selectedStudentPhone()"></span>
                                </p>
                            </template>
                            <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div x-show="recipientType === 'course_students'" x-cloak x-transition.opacity>
                            <label class="<?php echo e($waLabelClass); ?>">اختر الكورس</label>
                            <select name="course_id" x-model="courseId"
                                    :required="recipientType === 'course_students'"
                                    :disabled="recipientType !== 'course_students'"
                                    class="<?php echo e($waSelectClass); ?> disabled:bg-slate-100">
                                <option value="">— اختر كورساً —</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="text-xs text-slate-600 mt-2">يُرسل لجميع الطلاب المسجّلين في الكورس ولديهم رقم هاتف.</p>
                            <?php $__errorArgs = ['course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div x-show="recipientType === 'all_students'" x-cloak x-transition.opacity
                             class="rounded-lg bg-violet-100/80 border border-violet-200 px-4 py-3 text-sm text-violet-900 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5 shrink-0"></i>
                            <span>سيتم الإرسال لـ <strong><?php echo e($students->count()); ?></strong> طالب لديهم رقم هاتف — عبر دفعة في الخلفية مع فاصل آمن بين الرسائل.</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="<?php echo e($waSectionClass); ?>">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-emerald-600"></i>
                        محتوى الرسالة
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                        <div class="mb-5">
                            <label class="<?php echo e($waLabelClass); ?>">قالب رسالة (اختياري)</label>
                            <select name="template_id" x-model="templateId" @change="applyTemplate" class="<?php echo e($waSelectClass); ?>">
                                <option value="">— رسالة مخصصة —</option>
                                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($template->id); ?>"><?php echo e($template->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="text-xs text-slate-500 mt-1.5">
                                مع القالب في الإرسال الجماعي تُستبدل المتغيرات لكل طالب تلقائياً
                                · <a href="<?php echo e(route('admin.messages.templates')); ?>" class="text-emerald-600 hover:underline">إدارة القوالب</a>
                            </p>
                        </div>

                        
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="<?php echo e($waLabelClass); ?> mb-0">نص الرسالة</label>
                                <span class="text-xs font-mono tabular-nums"
                                      :class="charCount > 4000 ? 'text-rose-600 font-bold' : 'text-slate-500'"
                                      x-text="charCount + ' / 4096'"></span>
                            </div>
                            <textarea name="message" rows="8" required maxlength="4096" x-model="message"
                                      class="<?php echo e($waTextareaClass); ?>"
                                      placeholder="اكتب رسالتك هنا...&#10;&#10;يمكنك استخدام *نص عريض* و _مائل_ كما في واتساب"></textarea>
                            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="<?php echo e($waBtnPrimary); ?>" :disabled="submitting">
                                <i class="fas fa-paper-plane" :class="submitting && 'fa-spinner fa-spin'"></i>
                                <span x-text="submitting ? 'جاري الإرسال...' : submitLabel()"></span>
                            </button>
                            <button type="button" @click="resetForm()" class="<?php echo e($waBtnSecondary); ?>">
                                <i class="fas fa-undo"></i> مسح
                            </button>
                            <a href="<?php echo e(route('admin.whatsapp.messages')); ?>" class="<?php echo e($waBtnSecondary); ?> mr-auto">
                                <i class="fas fa-history"></i> السجل
                            </a>
                        </div>
                </div>
            </section>
        </form>

        
        <div class="xl:col-span-4 space-y-4">
            
            <section class="<?php echo e($waSectionClass); ?> overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-green-600">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fab fa-whatsapp"></i>
                        معاينة الرسالة
                    </h3>
                </div>
                <div class="p-4 bg-[#e5ddd5] min-h-[320px]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    <div class="max-w-[280px] mr-auto">
                        <div class="bg-[#dcf8c6] rounded-lg rounded-tr-none shadow-md p-3 relative">
                            <p class="text-[10px] font-bold text-emerald-800 mb-1"><?php echo e(config('app.name', 'Mindlytics')); ?></p>
                            <p class="text-sm text-slate-900 whitespace-pre-wrap break-words leading-relaxed min-h-[60px]"
                               x-text="previewText"></p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-[10px] text-slate-500"><?php echo e(now()->format('H:i')); ?></span>
                                <i class="fas fa-check-double text-[10px] text-sky-500"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-2 text-center" x-text="recipientHint"></p>
                    </div>
                </div>
            </section>

            
            <section class="<?php echo e($waSectionClass); ?>">
                <div class="px-5 py-3 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-code text-violet-600"></i>
                        متغيرات القوالب
                    </h3>
                </div>
                <div class="p-4 grid grid-cols-1 gap-1.5 text-xs">
                    <?php $__currentLoopData = $templateVariables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" @click="insertVariable(<?php echo \Illuminate\Support\Js::from($item['var'])->toHtml() ?>)"
                                class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg hover:bg-emerald-50 border border-transparent hover:border-emerald-200 text-right transition-colors group">
                            <code class="bg-slate-100 group-hover:bg-white px-1.5 py-0.5 rounded font-mono text-emerald-700"><?php echo e($item['var']); ?></code>
                            <span class="text-slate-500"><?php echo e($item['desc']); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            
            <?php if($recentMessages->isNotEmpty()): ?>
                <section class="<?php echo e($waSectionClass); ?>">
                    <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">آخر الإرسالات</h3>
                        <a href="<?php echo e(route('admin.whatsapp.messages')); ?>" class="text-xs text-emerald-600 hover:underline">الكل</a>
                    </div>
                    <ul class="divide-y divide-slate-100 max-h-[220px] overflow-y-auto">
                        <?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="px-4 py-3 hover:bg-slate-50/80 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-mono text-slate-600 dir-ltr text-right">+<?php echo e($msg->phone_number); ?></p>
                                        <p class="text-xs text-slate-700 truncate mt-0.5"><?php echo e(Str::limit($msg->message, 45)); ?></p>
                                    </div>
                                    <span class="shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold
                                        <?php echo e($msg->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($msg->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')); ?>">
                                        <?php echo e($msg->status_text); ?>

                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1"><?php echo e($msg->created_at?->diffForHumans()); ?></p>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\send.blade.php ENDPATH**/ ?>