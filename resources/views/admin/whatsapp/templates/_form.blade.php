@php
    $template = $template ?? null;
    $buttons = old('buttons', $template?->buttons ?? [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'phone' => '']]);
    if (! is_array($buttons) || $buttons === []) {
        $buttons = [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'phone' => '']];
    }
@endphp

<div class="space-y-6" x-data="{ headerType: '{{ old('header_type', $template?->header_type ?? '') }}', buttons: @js($buttons) }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="{{ $waLabelClass }}">اسم القالب (Name) *</label>
            <input type="text" name="name" value="{{ old('name', $template?->name) }}" required
                   pattern="[a-z0-9_]+" dir="ltr"
                   placeholder="order_confirmation"
                   class="{{ $waInputClass }} font-mono">
            <p class="text-xs text-slate-500 mt-1">أحرف إنجليزية صغيرة، أرقام، و _ فقط — يُستخدم في Meta API</p>
            @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $waLabelClass }}">اللغة *</label>
            <select name="language" required class="{{ $waSelectClass }}">
                @foreach(\App\Models\WhatsAppMetaTemplate::languageOptions() as $code => $label)
                    <option value="{{ $code }}" @selected(old('language', $template?->language ?? 'ar') === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $waLabelClass }}">الفئة *</label>
            <select name="category" required class="{{ $waSelectClass }}">
                @foreach(\App\Models\WhatsAppMetaTemplate::categoryLabels() as $val => $lbl)
                    <option value="{{ $val }}" @selected(old('category', $template?->category ?? 'UTILITY') === $val)>{{ $lbl }}</option>
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
        <textarea name="body_text" rows="6" required class="{{ $waTextareaClass }}"
                  placeholder="مرحباً @{{1}}، طلبك رقم @{{2}} تم تأكيده.">{{ old('body_text', $template?->body_text) }}</textarea>
        <p class="text-xs text-slate-500 mt-1">استخدم متغيرات Meta: <code class="bg-slate-100 px-1 rounded" dir="ltr">@{{1}}</code> <code class="bg-slate-100 px-1 rounded" dir="ltr">@{{2}}</code> …</p>
        @error('body_text')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $waLabelClass }}">Footer (اختياري — 60 حرف)</label>
        <input type="text" name="footer_text" maxlength="60" value="{{ old('footer_text', $template?->footer_text) }}"
               class="{{ $waInputClass }}" placeholder="شكراً لثقتك بنا">
    </div>

    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="{{ $waLabelClass }} mb-0">الأزرار (Buttons)</label>
            <button type="button" @click="buttons.push({type:'QUICK_REPLY',text:'',url:'',phone:''})"
                    class="text-xs font-bold text-emerald-700 hover:underline">+ إضافة زر</button>
        </div>
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
                        <input type="url" :name="'buttons['+i+'][url]'" x-model="btn.url" placeholder="https://..." dir="ltr" class="{{ $waInputClass }} !text-xs">
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
