@php
    $highlightsText = old('highlights_text', isset($entry) && is_array($entry->highlights) ? implode("\n", $entry->highlights) : '');
    $linked = $linkedCourse ?? ($entry->relationLoaded('advancedCourse') ? $entry->advancedCourse : $entry->advancedCourse);
    $lms = fn (string $field, $default = '') => old('lms.'.$field, $linked?->{$field} ?? $default);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">اسم الكورس *</label>
        <input type="text" name="name" value="{{ old('name', $entry->name) }}" required
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Slug (رابط Landing)</label>
        <input type="text" name="slug" value="{{ old('slug', $entry->slug) }}" dir="ltr"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30"
               placeholder="ai-tools">
        <p class="text-xs text-slate-400 mt-1">اتركه فارغاً للتوليد التلقائي</p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">الفئة المستهدفة</label>
        <input type="text" name="audience" value="{{ old('audience', $entry->audience) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">المدرب</label>
        <input type="text" name="instructor_name" value="{{ old('instructor_name', $entry->instructor_name) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">تاريخ بداية الدفعة</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', optional($entry->starts_at)->format('Y-m-d')) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
        <p class="text-[11px] text-slate-500 mt-1">بيرتّب الدفعات القادمة في الصفحة الرئيسية بالأقرب.</p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">موعد البداية (نص بديل)</label>
        <input type="text" name="start_label" value="{{ old('start_label', $entry->start_label) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm" placeholder="Start of September / 1/10">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">أيام المحاضرات</label>
        <input type="text" name="schedule_days" value="{{ old('schedule_days', $entry->schedule_days) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">المدة</label>
        <input type="text" name="duration" value="{{ old('duration', $entry->duration) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">عدد الساعات</label>
        <input type="text" name="hours" value="{{ old('hours', $entry->hours) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">السعر أونلاين (ج.م)</label>
        <input type="number" step="0.01" min="0" name="price_online" value="{{ old('price_online', $entry->price_online) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
        <p class="text-[11px] text-slate-500 mt-1">لو فيه الكورس المربوط، الموقع بيعرض سعره هو (نفس سعر الدفع) من قسم صفحة العرض تحت.</p>
        @if($entry->exists && ($conflict = $entry->priceConflict()))
            <p class="text-[11px] font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-2 py-1 mt-1">
                السعر هنا {{ number_format($conflict['board'], 0) }} ج.م، لكن سعر «{{ $conflict['linked_name'] }}» {{ number_format($conflict['linked'], 0) }} ج.م. الموقع بيعرض {{ number_format($conflict['linked'], 0) }}.
            </p>
        @endif
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">السعر مسجّل (ج.م)</label>
        <input type="number" step="0.01" min="0" name="price_recorded" value="{{ old('price_recorded', $entry->price_recorded) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">النظام / Format</label>
        <input type="text" name="format" value="{{ old('format', $entry->format) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm" placeholder="Online / Recorded">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">ترتيب العرض</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $entry->sort_order ?? 0) }}"
               class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">ربط بكورس LMS *</label>
        <select name="advanced_course_id" id="advanced_course_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
            <option value="">— بدون ربط —</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('advanced_course_id', $entry->advanced_course_id) === (string) $course->id)>{{ $course->title }}</option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">اربط كورس المنصة عشان تقدر تعدّل كل بيانات صفحة العرض من هنا، وزر «التفاصيل» يفتحها.</p>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">ملخص قصير (للدفعات / الكروت)</label>
        <textarea name="summary" rows="2" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">{{ old('summary', $entry->summary) }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">نقاط Landing (سطر لكل نقطة)</label>
        <textarea name="highlights_text" rows="4" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm" placeholder="ميزة 1&#10;ميزة 2">{{ $highlightsText }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">تفاصيل Landing (للعميل)</label>
        <textarea name="landing_details" rows="6" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-mono">{{ old('landing_details', $entry->landing_details) }}</textarea>
    </div>
    <div class="md:col-span-2 flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $entry->is_active ?? true)) class="rounded border-slate-300 text-emerald-600">
            نشط في لوحة المبيعات
        </label>
        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="landing_published" value="1" @checked(old('landing_published', $entry->landing_published ?? false)) class="rounded border-slate-300 text-emerald-600">
            نشر صفحة Landing للمشاركة
        </label>
    </div>
</div>

{{-- بيانات صفحة عرض الكورس العامة (/course/{id}) --}}
<div class="mt-8 rounded-2xl border-2 border-emerald-200 bg-emerald-50/40 p-5 sm:p-6 space-y-5">
    <div>
        <h2 class="text-lg font-black text-emerald-950">بيانات صفحة عرض الكورس</h2>
        <p class="text-sm text-emerald-900/80 mt-1">الحقول دي بتظهر في `/course/{id}`: الهوك، الوصف، المخرجات، ومعلومات مهمة عن الكورس.</p>
        @unless($linked)
            <p class="mt-3 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                اختار كورس LMS فوق واحفظ، وبعدين هتقدر تعدّل كل بيانات الصفحة من هنا.
            </p>
        @endunless
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 {{ $linked ? '' : 'opacity-60 pointer-events-none' }}">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">عنوان الكورس في الصفحة</label>
            <input type="text" name="lms[title]" value="{{ $lms('title', $entry->name) }}"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">الهوك (جملة الهيرو)</label>
            <textarea name="lms[hook]" rows="2" maxlength="500" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)
                      placeholder="جملة قوية قصيرة في أول الصفحة">{{ $lms('hook') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">عن الكورس / الوصف</label>
            <textarea name="lms[description]" rows="5" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('description') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">أهداف الكورس</label>
            <textarea name="lms[objectives]" rows="4" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('objectives') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">هتطلع من الكورس بإيه؟ (سطر لكل نقطة)</label>
            <textarea name="lms[what_you_learn]" rows="6" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)
                      placeholder="مهارة 1&#10;مهارة 2">{{ $lms('what_you_learn') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">مناسب لمين؟</label>
            <textarea name="lms[suitable_for]" rows="3" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('suitable_for') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">مناسب لحد سن كام؟</label>
            <textarea name="lms[age_suitability]" rows="3" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('age_suitability') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">معلومات عن المحاضر</label>
            <textarea name="lms[instructor_info]" rows="4" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('instructor_info') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">الكورس متاح لإمتى؟</label>
            <textarea name="lms[available_until_info]" rows="3" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('available_until_info') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">المتابعة إزاي؟</label>
            <textarea name="lms[follow_up_info]" rows="3" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>{{ $lms('follow_up_info') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">المستوى</label>
            <select name="lms[level]" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>
                @foreach(['beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم'] as $value => $label)
                    <option value="{{ $value }}" @selected((string) $lms('level', 'beginner') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">ساعات المدة (رقم)</label>
            <input type="number" min="0" step="1" name="lms[duration_hours]" value="{{ $lms('duration_hours') }}"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">سعر صفحة الكورس / الدفع (ج.م)</label>
            <input type="number" min="0" step="0.01" name="lms[price]" value="{{ $lms('price') }}"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">قيمة الخصم (ج.م)</label>
            <input type="number" min="0" step="0.01" name="lms[discount_amount]" value="{{ $lms('discount_amount') }}"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-500 mb-1">رابط فيديو المقدمة</label>
            <input type="url" name="lms[video_url]" value="{{ $lms('video_url') }}" dir="ltr"
                   class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white" @disabled(! $linked)
                   placeholder="https://...">
        </div>
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="lms[is_free]" value="1" @checked(old('lms.is_free', $linked?->is_free ?? false)) class="rounded border-slate-300 text-emerald-600" @disabled(! $linked)>
                كورس مجاني
            </label>
        </div>
    </div>
</div>

@if($entry->exists && $entry->landingUrl())
    <div class="mt-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm">
        <span class="font-bold text-emerald-900">رابط Landing:</span>
        <a href="{{ $entry->landingUrl() }}" target="_blank" class="text-emerald-700 hover:underline break-all mr-2" dir="ltr">{{ $entry->landingUrl() }}</a>
    </div>
@endif

@if($linked)
    <div class="mt-3 p-4 rounded-xl bg-blue-50 border border-blue-200 text-sm">
        <span class="font-bold text-blue-900">صفحة عرض الكورس:</span>
        <a href="{{ route('public.course.show', $linked->id) }}" target="_blank" class="text-blue-700 hover:underline break-all mr-2" dir="ltr">{{ route('public.course.show', $linked->id) }}</a>
    </div>
@endif
