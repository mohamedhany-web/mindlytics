<?php
    $template = $template ?? null;
    $buttons = old('buttons', $defaultButtons ?? $template?->buttons ?? [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'url_example' => '', 'phone' => '']]);
    if (! is_array($buttons) || $buttons === []) {
        $buttons = [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'url_example' => '', 'phone' => '']];
    }
    $lockName = $lockName ?? false;
    $lockedName = $lockedName ?? old('name', $template?->name);
    $showWorkshopPicker = $showWorkshopPicker ?? false;
    $workshops = $workshops ?? collect();
    $workshopVariableLabels = $workshopVariableLabels ?? [];
    $initialWorkshopId = (int) old('workshop_id', $initialWorkshopId ?? 0);
    $initialMode = $initialWorkshopId > 0 ? 'workshop' : 'general';
    $workshopPresetUrl = $workshopPresetUrl ?? '';

    $readyPresets = [
        [
            'key' => 'workshop_check_f',
            'label' => 'متابعة ورشة (مؤنث)',
            'display_name' => 'متابعة حضور ورشة — مؤنث',
            'name' => 'workshop_attendance_check_f',
            'category' => 'MARKETING',
            'language' => 'ar_EG',
            'body' => "ازي حضرتك ايه الاخبار معاكي @{{1}} من مايندليتكس.\nكنت عايزة اعرف حضرتك حضرت الوركشوب بتاعة @{{2}}؟",
            'examples' => ['1' => 'إسراء', '2' => 'الفرونت اند'],
            'labels' => ['1' => 'اسم الموظف/ة', '2' => 'موضوع الورشة'],
        ],
        [
            'key' => 'workshop_check_m',
            'label' => 'متابعة ورشة (مذكّر خفيف)',
            'display_name' => 'متابعة حضور ورشة — عام',
            'name' => 'workshop_attendance_check',
            'category' => 'MARKETING',
            'language' => 'ar_EG',
            'body' => "ازيك ايه الاخبار ✨\nمعاك @{{1}} من Mindlytics Academy كنت حابه اعرف حضرتك حضرت ال workshop بتاع ال @{{2}}؟",
            'examples' => ['1' => 'إسراء', '2' => 'frontend'],
            'labels' => ['1' => 'اسم الموظف/ة', '2' => 'موضوع الورشة'],
        ],
        [
            'key' => 'intro_agent_topic',
            'label' => 'ترحيب + اهتمام بموضوع',
            'display_name' => 'ترحيب واهتمام بموضوع',
            'name' => 'sales_intro_topic_ar',
            'category' => 'MARKETING',
            'language' => 'ar_EG',
            'body' => "أهلاً @{{1}} 👋\nمعاك @{{2}} من Mindlytics Academy.\nشفت إنك مهتم بـ @{{3}} — تحب أشرحلك باختصار؟",
            'examples' => ['1' => 'أحمد', '2' => 'إسراء', '3' => 'الفرونت اند'],
            'labels' => ['1' => 'اسم العميل', '2' => 'اسم الموظف/ة', '3' => 'الموضوع'],
        ],
    ];

    $defaultVarChips = [
        ['n' => 1, 'label' => 'اسم الموظف/ة', 'example' => 'إسراء'],
        ['n' => 2, 'label' => 'موضوع الورشة/الكورس', 'example' => 'الفرونت اند'],
        ['n' => 3, 'label' => 'اسم العميل', 'example' => 'أحمد'],
        ['n' => 4, 'label' => 'كود دعوة الجروب', 'example' => 'Ld0j8PUAprmCnDi65uUqTC'],
    ];

    $oldExamples = old('example_values', []);
?>

<div class="space-y-6" x-data="{
    headerType: <?php echo \Illuminate\Support\Js::from(old('header_type', $template?->header_type ?? ''))->toHtml() ?>,
    buttons: <?php echo \Illuminate\Support\Js::from($buttons)->toHtml() ?>,
    templateMode: <?php echo \Illuminate\Support\Js::from(old('template_mode', $initialMode))->toHtml() ?>,
    workshopId: <?php echo \Illuminate\Support\Js::from((string) ($initialWorkshopId ?: ''))->toHtml() ?>,
    templateName: <?php echo \Illuminate\Support\Js::from(old('name', $lockName ? $lockedName : ($template?->name ?? '')))->toHtml() ?>,
    displayName: <?php echo \Illuminate\Support\Js::from(old('display_name', $template?->display_name ?? ''))->toHtml() ?>,
    nameLocked: <?php echo \Illuminate\Support\Js::from((bool) $lockName)->toHtml() ?>,
    bodyText: <?php echo \Illuminate\Support\Js::from(old('body_text', $defaultBody ?? $template?->body_text ?? ''))->toHtml() ?>,
    footerText: <?php echo \Illuminate\Support\Js::from(old('footer_text', $template?->footer_text ?? ''))->toHtml() ?>,
    category: <?php echo \Illuminate\Support\Js::from(old('category', $template?->category ?? 'MARKETING'))->toHtml() ?>,
    language: <?php echo \Illuminate\Support\Js::from(old('language', $template?->language ?? 'ar_EG'))->toHtml() ?>,
    workshopPresetUrl: <?php echo \Illuminate\Support\Js::from($workshopPresetUrl)->toHtml() ?>,
    groupLinkHint: '',
    loadingPreset: false,
    activePreset: '',
    exampleValues: <?php echo \Illuminate\Support\Js::from(collect($oldExamples)->mapWithKeys(fn ($v, $k) => [(string) $k => (string) $v])->all() ?: ['1' => 'إسراء', '2' => 'الفرونت اند'])->toHtml() ?>,
    varLabels: <?php echo \Illuminate\Support\Js::from(collect($defaultVarChips)->mapWithKeys(fn ($c) => [(string) $c['n'] => $c['label']])->all())->toHtml() ?>,
    presets: <?php echo \Illuminate\Support\Js::from($readyPresets)->toHtml() ?>,
    varChips: <?php echo \Illuminate\Support\Js::from($defaultVarChips)->toHtml() ?>,
    workshopLabels: <?php echo \Illuminate\Support\Js::from($workshopVariableLabels)->toHtml() ?>,
    bodyEl: null,
    async applyWorkshopPreset() {
        if (!this.workshopId) return;
        this.loadingPreset = true;
        try {
            const url = this.workshopPresetUrl.replace('__ID__', this.workshopId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('failed');
            const data = await res.json();
            this.templateName = data.name || this.templateName;
            this.nameLocked = true;
            this.bodyText = data.body_text || this.bodyText;
            this.footerText = data.footer_text || this.footerText;
            this.category = data.category || 'UTILITY';
            this.language = data.language || 'ar_EG';
            this.buttons = (data.buttons && data.buttons.length)
                ? data.buttons
                : [{ type: 'QUICK_REPLY', text: '', url: '', url_example: '', phone: '' }];
            this.groupLinkHint = data.group_link || '';
            this.activePreset = '';
        } catch (e) {
            alert('تعذّر تحميل بيانات الورشة — حاول مرة أخرى.');
        } finally {
            this.loadingPreset = false;
        }
    },
    setGeneralMode() {
        this.templateMode = 'general';
        this.workshopId = '';
        this.nameLocked = <?php echo \Illuminate\Support\Js::from((bool) $lockName)->toHtml() ?>;
        this.groupLinkHint = '';
    },
    setWorkshopMode() {
        this.templateMode = 'workshop';
        if (this.workshopId) this.applyWorkshopPreset();
    },
    nextVarIndex() {
        const matches = [...(this.bodyText || '').matchAll(/\{\{(\d+)\}\}/g)].map(m => parseInt(m[1], 10));
        return matches.length ? Math.max(...matches) + 1 : 1;
    },
    insertAtCursor(text) {
        const el = this.bodyEl || this.$refs.bodyTextarea;
        if (!el) {
            this.bodyText = (this.bodyText || '') + text;
            return;
        }
        const start = el.selectionStart ?? (this.bodyText || '').length;
        const end = el.selectionEnd ?? start;
        const before = (this.bodyText || '').slice(0, start);
        const after = (this.bodyText || '').slice(end);
        this.bodyText = before + text + after;
        this.$nextTick(() => {
            el.focus();
            const pos = start + text.length;
            el.setSelectionRange(pos, pos);
        });
    },
    insertVar(n, label = null, example = null) {
        const key = String(n);
        if (label) this.varLabels[key] = label;
        if (example && !this.exampleValues[key]) this.exampleValues[key] = example;
        this.insertAtCursor('{{' + n + '}}');
    },
    insertNextVar(label, example) {
        const n = this.nextVarIndex();
        this.insertVar(n, label, example);
    },
    applyPreset(preset) {
        this.activePreset = preset.key;
        this.bodyText = preset.body;
        this.displayName = preset.display_name || this.displayName;
        if (!this.nameLocked) this.templateName = preset.name || this.templateName;
        this.category = preset.category || 'MARKETING';
        this.language = preset.language || 'ar_EG';
        this.exampleValues = { ...(preset.examples || {}) };
        this.varLabels = { ...(preset.labels || {}) };
        this.templateMode = 'general';
        this.workshopId = '';
        this.nameLocked = <?php echo \Illuminate\Support\Js::from((bool) $lockName)->toHtml() ?>;
    },
    usedVars() {
        const set = new Set();
        const matches = [...(this.bodyText || '').matchAll(/\{\{(\d+)\}\}/g)];
        matches.forEach(m => set.add(String(m[1])));
        return [...set].sort((a, b) => parseInt(a) - parseInt(b));
    },
    previewText() {
        let text = this.bodyText || '';
        this.usedVars().forEach(n => {
            const val = this.exampleValues[n] || ('[' + (this.varLabels[n] || ('متغير ' + n)) + ']');
            text = text.split('{{' + n + '}}').join(val);
        });
        return text;
    }
}" x-init="bodyEl = $refs.bodyTextarea; if (templateMode === 'workshop' && workshopId) applyWorkshopPreset()">

    <?php if($showWorkshopPicker): ?>
        <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4 space-y-4">
            <p class="text-sm font-bold text-slate-900"><i class="fas fa-people-arrows text-violet-600 ml-1"></i> نوع القالب</p>
            <div class="flex flex-wrap gap-3">
                <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-sm font-semibold transition-colors"
                       :class="templateMode === 'general' ? 'bg-white border-emerald-300 text-emerald-800 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-white'">
                    <input type="radio" name="template_mode" value="general" class="text-emerald-600" x-model="templateMode" @change="setGeneralMode()">
                    قالب عام / سيلز
                </label>
                <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-sm font-semibold transition-colors"
                       :class="templateMode === 'workshop' ? 'bg-white border-violet-400 text-violet-800 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-white'">
                    <input type="radio" name="template_mode" value="workshop" class="text-violet-600" x-model="templateMode" @change="setWorkshopMode()">
                    مرتبط بورشة
                </label>
            </div>

            <div x-show="templateMode === 'workshop'" x-cloak class="space-y-3">
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">اختر الورشة</label>
                    <select x-model="workshopId" @change="applyWorkshopPreset()" class="<?php echo e($waSelectClass); ?>" :required="templateMode === 'workshop'">
                        <option value="">— اختر ورشة —</option>
                        <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ws->id); ?>"><?php echo e($ws->title); ?><?php if($ws->starts_at): ?> · <?php echo e($ws->starts_at->format('Y-m-d')); ?><?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="hidden" name="workshop_id" :value="templateMode === 'workshop' && workshopId ? workshopId : ''">
                    <?php $__errorArgs = ['workshop_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="rounded-lg border border-violet-100 bg-white/80 p-3 text-xs text-slate-700 space-y-2">
                    <p class="font-bold text-slate-900">اضغط لإدراج متغير الورشة</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(label, num) in workshopLabels" :key="'wvar-'+num">
                            <button type="button" @click="insertVar(num, label)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-violet-200 bg-violet-50 hover:bg-violet-100 text-violet-900 text-[11px] font-semibold">
                                <code dir="ltr" x-text="'{{'+num+'}}'"></code>
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <p x-show="groupLinkHint" x-cloak class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                    رابط الجروب: <span class="font-mono break-all" x-text="groupLinkHint"></span>
                </p>
                <p x-show="loadingPreset" x-cloak class="text-xs text-violet-700"><i class="fas fa-spinner fa-spin ml-1"></i> جاري تحميل بيانات الورشة…</p>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 space-y-3" x-show="templateMode === 'general'" x-cloak>
        <div>
            <p class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-emerald-600"></i>
                قوالب جاهزة بنقرة واحدة
            </p>
            <p class="text-xs text-slate-600 mt-0.5">اضغط لملء النص والمتغيرات تلقائياً — عدّل بعدين زي ما تحب.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <template x-for="preset in presets" :key="preset.key">
                <button type="button" @click="applyPreset(preset)"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold border transition-colors"
                        :class="activePreset === preset.key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50'">
                    <i class="fas fa-file-alt"></i>
                    <span x-text="preset.label"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="<?php echo e($waLabelClass); ?>">تسمية القالب (للعرض داخل النظام)</label>
            <input type="text" name="display_name" maxlength="255" x-model="displayName"
                   placeholder="مثال: متابعة حضور ورشة الفرونت"
                   class="<?php echo e($waInputClass); ?>">
            <p class="text-xs text-slate-500 mt-1">اسم واضح للموظفين — منفصل عن كود Meta التقني أدناه</p>
            <?php $__errorArgs = ['display_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">اسم القالب في Meta (Name) *</label>
            <?php if($lockName): ?>
                <input type="text" value="<?php echo e($lockedName); ?>" readonly dir="ltr"
                       class="<?php echo e($waInputClass); ?> font-mono bg-slate-50 text-slate-600">
                <input type="hidden" name="name" value="<?php echo e($lockedName); ?>">
                <p class="text-xs text-slate-500 mt-1">اسم ثابت مرتبط بالورشة — يُستخدم في Meta API</p>
            <?php else: ?>
                <input type="text" name="name" x-model="templateName" required
                       pattern="[a-z0-9_]+" dir="ltr"
                       placeholder="workshop_attendance_check"
                       class="<?php echo e($waInputClass); ?> font-mono"
                       :readonly="nameLocked"
                       :class="nameLocked ? 'bg-slate-50 text-slate-600' : ''">
                <p class="text-xs text-slate-500 mt-1" x-show="!nameLocked">أحرف إنجليزية صغيرة، أرقام، و _ فقط — يُستخدم في Meta API</p>
                <p class="text-xs text-violet-600 mt-1" x-show="nameLocked" x-cloak>اسم تلقائي مرتبط بالورشة — يُستخدم في Meta API</p>
            <?php endif; ?>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">اللغة *</label>
            <select name="language" required class="<?php echo e($waSelectClass); ?>" x-model="language">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::languageOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>"><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">الفئة *</label>
            <select name="category" required class="<?php echo e($waSelectClass); ?>" x-model="category">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::categoryLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>"><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-slate-500 mt-1">رسائل المتابعة/السيلز عادة <strong>Marketing</strong> — Utility للإشعارات والمعاملات</p>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">Header (اختياري)</label>
            <select name="header_type" x-model="headerType" class="<?php echo e($waSelectClass); ?>">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::headerTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>"><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div x-show="headerType !== ''" x-cloak>
        <label class="<?php echo e($waLabelClass); ?>">
            <span x-text="headerType === 'text' ? 'نص الـ Header' : 'رابط مثال للوسائط (Example URL)'"></span>
        </label>
        <input type="text" name="header_content" value="<?php echo e(old('header_content', $template?->header_content)); ?>"
               class="<?php echo e($waInputClass); ?>" dir="ltr"
               :placeholder="headerType === 'text' ? 'عنوان الرسالة' : 'https://example.com/image.jpg'">
        <p class="text-xs text-slate-500 mt-1" x-show="headerType !== 'text'">Meta تطلب رابط مثال للصورة/فيديو/مستند عند المراجعة</p>
    </div>

    <div class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label class="<?php echo e($waLabelClass); ?> mb-0">محتوى الرسالة (Body) *</label>
            <button type="button" @click="insertNextVar('متغير جديد', '')"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-2.5 py-1.5 hover:bg-emerald-100">
                <i class="fas fa-plus"></i>
                إضافة متغير
                <code dir="ltr" x-text="'{{'+nextVarIndex()+'}}'"></code>
            </button>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 space-y-2">
            <p class="text-[11px] font-bold text-slate-700">اضغط لإدراج Placeholder في النص</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="chip in varChips" :key="'chip-'+chip.n">
                    <button type="button" @click="insertVar(chip.n, chip.label, chip.example)"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50 text-[11px] font-semibold text-slate-800 transition-colors">
                        <code class="text-emerald-700" dir="ltr" x-text="'{{'+chip.n+'}}'"></code>
                        <span x-text="chip.label"></span>
                    </button>
                </template>
                <button type="button" @click="insertAtCursor('https://chat.whatsapp.com/{{3}}')"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-[11px] font-semibold text-amber-900">
                    <i class="fab fa-whatsapp"></i>
                    رابط جروب + كود
                </button>
            </div>
            <p class="text-[11px] text-slate-500">مثال: اسماء زي «إسراء» وموضوع زي «الفرونت اند» تبقى متغيرات — مش نص ثابت.</p>
        </div>

        <textarea name="body_text" rows="7" required class="<?php echo e($waTextareaClass); ?>" x-model="bodyText"
                  x-ref="bodyTextarea"
                  placeholder="ازي حضرتك ايه الاخبار معاكي {{1}} من مايندليتكس. كنت عايزة اعرف حضرتك حضرت الوركشوب بتاعة {{2}}؟"></textarea>
        <?php $__errorArgs = ['body_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3" x-show="usedVars().length" x-cloak>
            <div class="rounded-xl border border-sky-100 bg-sky-50/50 p-3 space-y-2">
                <p class="text-xs font-bold text-sky-900"><i class="fas fa-tags ml-1"></i> معاني المتغيرات + أمثلة Meta</p>
                <template x-for="n in usedVars()" :key="'ex-'+n">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <code class="col-span-2 text-[11px] font-mono text-sky-800" dir="ltr" x-text="'{{'+n+'}}'"></code>
                        <input type="text" class="col-span-5 <?php echo e($waInputClass); ?> !text-xs !py-1.5"
                               :placeholder="'معنى المتغير '+n"
                               x-model="varLabels[n]">
                        <input type="text" class="col-span-5 <?php echo e($waInputClass); ?> !text-xs !py-1.5"
                               :name="'example_values['+n+']'"
                               x-model="exampleValues[n]"
                               :placeholder="'مثال: '+(n==='1'?'إسراء':'الفرونت اند')">
                    </div>
                </template>
                <p class="text-[10px] text-sky-800/80">الأمثلة دي بتتبعت لـ Meta مع القالب للمراجعة — مش بتظهر للعميل.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <p class="text-xs font-bold text-slate-800 mb-2"><i class="fas fa-eye ml-1 text-emerald-600"></i> معاينة كما يراها العميل</p>
                <pre class="whitespace-pre-wrap text-sm text-slate-700 leading-relaxed" x-text="previewText()"></pre>
            </div>
        </div>
    </div>

    <div>
        <label class="<?php echo e($waLabelClass); ?>">Footer (اختياري — 60 حرف)</label>
        <input type="text" name="footer_text" maxlength="60" x-model="footerText"
               class="<?php echo e($waInputClass); ?>" placeholder="Mindlytics Academy">
    </div>

    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="<?php echo e($waLabelClass); ?> mb-0">الأزرار (Buttons) — اختياري</label>
            <button type="button" @click="buttons.push({type:'QUICK_REPLY',text:'',url:'',url_example:'',phone:''})"
                    class="text-xs font-bold text-emerald-700 hover:underline">+ إضافة زر</button>
        </div>
        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-3">
            Meta لا يقبل روابط الجروب في أزرار URL. الصيغة الصحيحة في النص:
            <code dir="ltr" class="bg-white px-1 rounded">https://chat.whatsapp.com/{{3}}</code>
            — عند الإرسال يُملأ {{3}} بكود الدعوة فقط. لو مش محتاج أزرار سيبها فاضي.
        </p>
        <div class="space-y-3">
            <template x-for="(btn, i) in buttons" :key="i">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="sm:col-span-3">
                        <select :name="'buttons['+i+'][type]'" x-model="btn.type" class="<?php echo e($waSelectClass); ?> !text-xs">
                            <option value="QUICK_REPLY">رد سريع</option>
                            <option value="URL">رابط URL</option>
                            <option value="PHONE_NUMBER">اتصال</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <input type="text" :name="'buttons['+i+'][text]'" x-model="btn.text" placeholder="نص الزر" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'URL'">
                        <input type="text" :name="'buttons['+i+'][url]'" x-model="btn.url" placeholder="https://example.com" dir="ltr" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'URL' && /\{\{\d+\}\}/.test(btn.url || '')">
                        <input type="text" :name="'buttons['+i+'][url_example]'" x-model="btn.url_example" placeholder="قيمة المثال للمتغير في الرابط" dir="ltr" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'PHONE_NUMBER'">
                        <input type="text" :name="'buttons['+i+'][phone]'" x-model="btn.phone" placeholder="+2010..." dir="ltr" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-1 flex items-center justify-end">
                        <button type="button" @click="buttons.splice(i,1)" class="text-rose-600 hover:text-rose-800 p-1" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\templates\_form.blade.php ENDPATH**/ ?>