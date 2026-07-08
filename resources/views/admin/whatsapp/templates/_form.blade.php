@php
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
@endphp

<div class="space-y-6" x-data="{
    headerType: @js(old('header_type', $template?->header_type ?? '')),
    buttons: @js($buttons),
    templateMode: @js(old('template_mode', $initialMode)),
    workshopId: @js((string) ($initialWorkshopId ?: '')),
    templateName: @js(old('name', $lockName ? $lockedName : ($template?->name ?? ''))),
    nameLocked: @js((bool) $lockName),
    bodyText: @js(old('body_text', $defaultBody ?? $template?->body_text ?? '')),
    footerText: @js(old('footer_text', $template?->footer_text ?? '')),
    category: @js(old('category', $template?->category ?? 'UTILITY')),
    language: @js(old('language', $template?->language ?? 'ar')),
    workshopPresetUrl: @js($workshopPresetUrl),
    groupLinkHint: '',
    loadingPreset: false,
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
            this.language = data.language || 'ar';
            this.buttons = (data.buttons && data.buttons.length)
                ? data.buttons
                : [{ type: 'QUICK_REPLY', text: '', url: '', url_example: '', phone: '' }];
            this.groupLinkHint = data.group_link || '';
        } catch (e) {
            alert('تعذّر تحميل بيانات الورشة — حاول مرة أخرى.');
        } finally {
            this.loadingPreset = false;
        }
    },
    setGeneralMode() {
        this.templateMode = 'general';
        this.workshopId = '';
        this.nameLocked = @js((bool) $lockName);
        this.groupLinkHint = '';
    },
    setWorkshopMode() {
        this.templateMode = 'workshop';
        if (this.workshopId) this.applyWorkshopPreset();
    }
}" x-init="if (templateMode === 'workshop' && workshopId) applyWorkshopPreset()">

    @if($showWorkshopPicker)
        <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4 space-y-4">
            <p class="text-sm font-bold text-slate-900"><i class="fas fa-people-arrows text-violet-600 ml-1"></i> نوع القالب</p>
            <div class="flex flex-wrap gap-3">
                <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-sm font-semibold transition-colors"
                       :class="templateMode === 'general' ? 'bg-white border-emerald-300 text-emerald-800 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-white'">
                    <input type="radio" name="template_mode" value="general" class="text-emerald-600" x-model="templateMode" @change="setGeneralMode()">
                    قالب عام
                </label>
                <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-sm font-semibold transition-colors"
                       :class="templateMode === 'workshop' ? 'bg-white border-violet-400 text-violet-800 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-white'">
                    <input type="radio" name="template_mode" value="workshop" class="text-violet-600" x-model="templateMode" @change="setWorkshopMode()">
                    مرتبط بورشة
                </label>
            </div>

            <div x-show="templateMode === 'workshop'" x-cloak class="space-y-3">
                <div>
                    <label class="{{ $waLabelClass }}">اختر الورشة</label>
                    <select x-model="workshopId" @change="applyWorkshopPreset()" class="{{ $waSelectClass }}" :required="templateMode === 'workshop'">
                        <option value="">— اختر ورشة —</option>
                        @foreach($workshops as $ws)
                            <option value="{{ $ws->id }}">{{ $ws->title }}@if($ws->starts_at) · {{ $ws->starts_at->format('Y-m-d') }}@endif</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="workshop_id" :value="templateMode === 'workshop' && workshopId ? workshopId : ''">
                    @error('workshop_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-lg border border-violet-100 bg-white/80 p-3 text-xs text-slate-700 space-y-1">
                    <p class="font-bold text-slate-900">متغيرات الورشة (Meta)</p>
                    @foreach($workshopVariableLabels as $num => $label)
                        <p><code dir="ltr" class="bg-slate-50 px-1 rounded border">@{{{{ $num }}}}</code> — {{ $label }}</p>
                    @endforeach
                </div>

                <p x-show="groupLinkHint" x-cloak class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                    رابط الجروب: <span class="font-mono break-all" x-text="groupLinkHint"></span>
                </p>
                <p x-show="loadingPreset" x-cloak class="text-xs text-violet-700"><i class="fas fa-spinner fa-spin ml-1"></i> جاري تحميل بيانات الورشة…</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="{{ $waLabelClass }}">اسم القالب (Name) *</label>
            @if($lockName)
                <input type="text" value="{{ $lockedName }}" readonly dir="ltr"
                       class="{{ $waInputClass }} font-mono bg-slate-50 text-slate-600">
                <input type="hidden" name="name" value="{{ $lockedName }}">
                <p class="text-xs text-slate-500 mt-1">اسم ثابت مرتبط بالورشة — يُستخدم في Meta API</p>
            @else
                <input type="text" name="name" x-model="templateName" required
                       pattern="[a-z0-9_]+" dir="ltr"
                       placeholder="order_confirmation"
                       class="{{ $waInputClass }} font-mono"
                       :readonly="nameLocked"
                       :class="nameLocked ? 'bg-slate-50 text-slate-600' : ''">
                <p class="text-xs text-slate-500 mt-1" x-show="!nameLocked">أحرف إنجليزية صغيرة، أرقام، و _ فقط — يُستخدم في Meta API</p>
                <p class="text-xs text-violet-600 mt-1" x-show="nameLocked" x-cloak>اسم تلقائي مرتبط بالورشة — يُستخدم في Meta API</p>
            @endif
            @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $waLabelClass }}">اللغة *</label>
            <select name="language" required class="{{ $waSelectClass }}" x-model="language">
                @foreach(\App\Models\WhatsAppMetaTemplate::languageOptions() as $code => $label)
                    <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $waLabelClass }}">الفئة *</label>
            <select name="category" required class="{{ $waSelectClass }}" x-model="category">
                @foreach(\App\Models\WhatsAppMetaTemplate::categoryLabels() as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Marketing يتطلب موافقة أطول — Utility للإشعارات والمعاملات</p>
        </div>
        <div>
            <label class="{{ $waLabelClass }}">Header (اختياري)</label>
            <select name="header_type" x-model="headerType" class="{{ $waSelectClass }}">
                @foreach(\App\Models\WhatsAppMetaTemplate::headerTypeLabels() as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div x-show="headerType !== ''" x-cloak>
        <label class="{{ $waLabelClass }}">
            <span x-text="headerType === 'text' ? 'نص الـ Header' : 'رابط مثال للوسائط (Example URL)'"></span>
        </label>
        <input type="text" name="header_content" value="{{ old('header_content', $template?->header_content) }}"
               class="{{ $waInputClass }}" dir="ltr"
               :placeholder="headerType === 'text' ? 'عنوان الرسالة' : 'https://example.com/image.jpg'">
        <p class="text-xs text-slate-500 mt-1" x-show="headerType !== 'text'">Meta تطلب رابط مثال للصورة/فيديو/مستند عند المراجعة</p>
    </div>

    <div>
        <label class="{{ $waLabelClass }}">محتوى الرسالة (Body) *</label>
        <textarea name="body_text" rows="6" required class="{{ $waTextareaClass }}" x-model="bodyText"
                  placeholder="مرحباً @{{1}}، طلبك رقم @{{2}} تم تأكيده."></textarea>
        <p class="text-xs text-slate-500 mt-1">استخدم متغيرات Meta: <code class="bg-slate-100 px-1 rounded" dir="ltr">@{{1}}</code> <code class="bg-slate-100 px-1 rounded" dir="ltr">@{{2}}</code> … — لجروب واتساب اكتب في النص: <code dir="ltr" class="bg-slate-100 px-1 rounded">https://chat.whatsapp.com/@{{3}}</code> (كود الدعوة فقط في @{{3}}، ليس الرابط الكامل).</p>
        @error('body_text')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $waLabelClass }}">Footer (اختياري — 60 حرف)</label>
        <input type="text" name="footer_text" maxlength="60" x-model="footerText"
               class="{{ $waInputClass }}" placeholder="شكراً لثقتك بنا">
    </div>

    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="{{ $waLabelClass }} mb-0">الأزرار (Buttons)</label>
            <button type="button" @click="buttons.push({type:'QUICK_REPLY',text:'',url:'',url_example:'',phone:''})"
                    class="text-xs font-bold text-emerald-700 hover:underline">+ إضافة زر</button>
        </div>
        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-3">
            Meta لا يقبل روابط الجروب في أزرار URL. الصيغة الصحيحة في النص:
            <code dir="ltr" class="bg-white px-1 rounded">https://chat.whatsapp.com/@{{3}}</code>
            — عند الإرسال يُملأ @{{3}} بكود الدعوة فقط.
        </p>
        <div class="space-y-3">
            <template x-for="(btn, i) in buttons" :key="i">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="sm:col-span-3">
                        <select :name="'buttons['+i+'][type]'" x-model="btn.type" class="{{ $waSelectClass }} !text-xs">
                            <option value="QUICK_REPLY">رد سريع</option>
                            <option value="URL">رابط URL</option>
                            <option value="PHONE_NUMBER">اتصال</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <input type="text" :name="'buttons['+i+'][text]'" x-model="btn.text" placeholder="نص الزر" class="{{ $waInputClass }} !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'URL'">
                        <input type="text" :name="'buttons['+i+'][url]'" x-model="btn.url" placeholder="https://chat.whatsapp.com/@{{3}}" dir="ltr" class="{{ $waInputClass }} !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'URL' && /\{\{\d+\}\}/.test(btn.url || '')">
                        <input type="text" :name="'buttons['+i+'][url_example]'" x-model="btn.url_example" placeholder="كود الدعوة فقط (مثل Ld0j8PUAprmCnDi65uUqTC)" dir="ltr" class="{{ $waInputClass }} !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'PHONE_NUMBER'">
                        <input type="text" :name="'buttons['+i+'][phone]'" x-model="btn.phone" placeholder="+2010..." dir="ltr" class="{{ $waInputClass }} !text-xs">
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
