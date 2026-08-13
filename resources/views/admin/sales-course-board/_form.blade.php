@php
    $highlightsText = old('highlights_text', isset($entry) && is_array($entry->highlights) ? implode("\n", $entry->highlights) : '');
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
        <label class="block text-xs font-semibold text-slate-500 mb-1">موعد البداية</label>
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
        <label class="block text-xs font-semibold text-slate-500 mb-1">ربط بكورس LMS (اختياري)</label>
        <select name="advanced_course_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
            <option value="">— بدون ربط —</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('advanced_course_id', $entry->advanced_course_id) === (string) $course->id)>{{ $course->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">ملخص قصير</label>
        <textarea name="summary" rows="2" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">{{ old('summary', $entry->summary) }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">نقاط Landing (سطر لكل نقطة)</label>
        <textarea name="highlights_text" rows="4" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm" placeholder="ميزة 1&#10;ميزة 2">{{ $highlightsText }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-slate-500 mb-1">تفاصيل Landing (للعميل)</label>
        <textarea name="landing_details" rows="8" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-mono">{{ old('landing_details', $entry->landing_details) }}</textarea>
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

@if($entry->exists && $entry->landingUrl())
    <div class="mt-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm">
        <span class="font-bold text-emerald-900">رابط Landing:</span>
        <a href="{{ $entry->landingUrl() }}" target="_blank" class="text-emerald-700 hover:underline break-all mr-2" dir="ltr">{{ $entry->landingUrl() }}</a>
    </div>
@endif
