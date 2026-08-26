@extends('layouts.employee')

@section('title', $plan->title)
@section('header', 'خطة التسويق: ' . $plan->title)

@section('content')
@php
    $planStatus = match($plan->status) {
        'draft' => ['مسودة', 'bg-gray-100 text-gray-800'],
        'active' => ['نشط', 'bg-emerald-100 text-emerald-800'],
        'paused' => ['متوقف', 'bg-amber-100 text-amber-800'],
        'completed' => ['مكتمل', 'bg-slate-200 text-slate-800'],
        default => [$plan->status, 'bg-gray-100'],
    };
    $evtStatus = fn ($s) => match($s) {
        'idea' => 'فكرة',
        'draft' => 'مسودة',
        'scheduled' => 'مجدول',
        'published' => 'منشور',
        'skipped' => 'تم التخطي',
        default => $s,
    };
@endphp
<div class="space-y-8" x-data="marketingPlanPage()">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    @if(($isBusinessDeveloper ?? false))
        @php $executionStats = $executionStats ?? ['total' => 0, 'published' => 0, 'scheduled' => 0, 'overdue_confirm' => 0]; @endphp
        <section class="rounded-2xl overflow-hidden border border-teal-200 shadow-lg">
            <div class="bg-gradient-to-l from-teal-800 to-slate-900 text-white px-5 py-5 sm:px-7">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold tracking-widest uppercase text-teal-200/90">مراقبة Business Developer</p>
                        <h2 class="text-xl font-black mt-1">{{ $plan->title }}</h2>
                        <p class="text-sm text-teal-100/85 mt-1">
                            المشرف: <strong>{{ $plan->moderator?->name ?? '—' }}</strong>
                            @if($plan->start_date || $plan->end_date)
                                · {{ $plan->start_date?->format('Y-m-d') ?? '—' }} → {{ $plan->end_date?->format('Y-m-d') ?? '—' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('employee.business-developer.marketing') }}" class="inline-flex items-center gap-2 rounded-xl bg-white text-slate-900 px-4 py-2 text-sm font-bold">المركز التنفيذي</a>
                        <a href="{{ route('employee.marketing-plans.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">كل الخطط</a>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-xl bg-white/10 border border-white/15 p-3">
                        <p class="text-[11px] text-teal-100/80">أحداث</p>
                        <p class="text-2xl font-black tabular-nums">{{ $executionStats['total'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/15 p-3">
                        <p class="text-[11px] text-teal-100/80">مجدول</p>
                        <p class="text-2xl font-black tabular-nums">{{ $executionStats['scheduled'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/15 p-3">
                        <p class="text-[11px] text-teal-100/80">منشور</p>
                        <p class="text-2xl font-black tabular-nums">{{ $executionStats['published'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/15 p-3">
                        <p class="text-[11px] text-rose-200/90">تأكيدات متأخرة</p>
                        <p class="text-2xl font-black tabular-nums">{{ $executionStats['overdue_confirm'] }}</p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold {{ $planStatus[1] }}">{{ $planStatus[0] }}</span>
                @if($plan->designTaskCycle)
                    <a href="{{ route('employee.design-cycles.show', $plan->designTaskCycle) }}" class="text-sm font-semibold text-fuchsia-700 hover:text-fuchsia-900">
                        <i class="fas fa-link ml-1"></i> مرتبطة بدورة تصميم #{{ $plan->designTaskCycle->id }}
                    </a>
                @endif
            </div>
            @if($plan->start_date || $plan->end_date)
                <p class="text-sm text-gray-600">
                    @if($plan->start_date)<span>من {{ $plan->start_date->format('Y-m-d') }}</span>@endif
                    @if($plan->end_date)<span class="mr-2">إلى {{ $plan->end_date->format('Y-m-d') }}</span>@endif
                </p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.marketing-plans.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-800">القائمة</a>
            <a href="{{ route('employee.marketing-plans.edit', $plan) }}" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">تعديل الخطة</a>
            <form method="post" action="{{ route('employee.marketing-plans.destroy', $plan) }}" onsubmit="return confirm('حذف الخطة وجميع المنصات والأحداث المرتبطة؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 text-sm font-semibold">حذف</button>
            </form>
        </div>
    </div>

    @if($plan->summary)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">الملخص</h2>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $plan->summary }}</p>
        </div>
    @endif
    @if($plan->goals)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">الأهداف والاستراتيجية</h2>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $plan->goals }}</p>
        </div>
    @endif

    <!-- المنصات -->
    <div class="rounded-2xl border border-pink-100 bg-gradient-to-b from-pink-50/40 to-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-pink-100 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-bold text-gray-900"><i class="fas fa-share-alt text-pink-600 ml-2"></i> منصات السوشيال ميديا</h2>
            <p class="text-xs text-gray-600">لون كل منصة يُستخدم في عرض أحداثها على التقويم.</p>
        </div>
        <div class="p-5 space-y-6">
            <form method="post" action="{{ route('employee.marketing-plans.platforms.store', $plan) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end rounded-xl border border-dashed border-pink-200 bg-white/80 p-4">
                @csrf
                <div class="md:col-span-6">
                    <label class="block text-xs font-medium text-gray-600 mb-2">المنصات (اختيار متعدد)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach($platformLabels as $key => $label)
                            <label class="inline-flex items-center gap-2 rounded-lg border border-pink-100 bg-white px-3 py-2 text-sm">
                                <input type="checkbox" name="platform_keys[]" value="{{ $key }}" class="rounded border-gray-300">
                                <span class="font-semibold text-gray-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2">يمكنك اختيار Facebook + Instagram + TikTok مرة واحدة. (ملاحظة: «أخرى» تُضاف وحدها).</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">اسم مخصص (أخرى)</label>
                    <input type="text" name="custom_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="إن اخترت أخرى">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">رابط البروفايل</label>
                    <input type="url" name="profile_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="https://">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">لون (#RRGGBB)</label>
                    <input type="text" name="color_hex" value="#6366f1" pattern="#[0-9A-Fa-f]{6}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono">
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-2">التوصيف / الوظائف المسؤولة عن هذه المنصة</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($employeeJobs as $job)
                            <label class="inline-flex items-center gap-2 rounded-lg border border-violet-100 bg-violet-50/50 px-3 py-1.5 text-xs">
                                <input type="checkbox" name="employee_job_ids[]" value="{{ $job->id }}" class="rounded border-gray-300 text-violet-600">
                                <span class="font-semibold text-gray-800">{{ $job->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">يمكن اختيار أكثر من توصيف — يُطبَّق على كل المنصات المضافة في هذه الدفعة.</p>
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">استراتيجية المنصة</label>
                    <textarea name="strategy_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="نبرة المحتوى، الجمهور، أنواع المنشورات..."></textarea>
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">إيقاع النشر / التكرار</label>
                    <textarea name="cadence_notes" rows="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: ٣ ريلز أسبوعياً، ستوري يومي..."></textarea>
                </div>
                <div class="md:col-span-12">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-semibold hover:bg-pink-700"><i class="fas fa-plus ml-1"></i> إضافة منصة</button>
                </div>
            </form>

            <div class="space-y-4">
                @forelse($plan->platforms as $plat)
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-full shrink-0 border border-gray-200" style="background-color: {{ $plat->color_hex }}"></span>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $plat->displayName() }}</p>
                                    @if($plat->profile_url)
                                        <a href="{{ $plat->profile_url }}" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline break-all">{{ \Illuminate\Support\Str::limit($plat->profile_url, 56) }}</a>
                                    @endif
                                    @if($plat->employeeJobs->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($plat->employeeJobs as $job)
                                                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-100 text-violet-800 border border-violet-200">{{ $job->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <form method="post" action="{{ route('employee.marketing-plans.platforms.destroy', [$plan, $plat]) }}" onsubmit="return confirm('حذف هذه المنصة؟');" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">حذف</button>
                            </form>
                        </div>
                        @if($plat->strategy_notes || $plat->cadence_notes)
                            <div class="mt-3 grid md:grid-cols-2 gap-3 text-sm">
                                @if($plat->strategy_notes)<div class="text-gray-700"><span class="font-semibold text-gray-500">استراتيجية:</span> {{ $plat->strategy_notes }}</div>@endif
                                @if($plat->cadence_notes)<div class="text-gray-700"><span class="font-semibold text-gray-500">الإيقاع:</span> {{ $plat->cadence_notes }}</div>@endif
                            </div>
                        @endif
                        <details class="mt-3 group">
                            <summary class="cursor-pointer text-sm font-semibold text-pink-700">تعديل المنصة</summary>
                            <form method="post" action="{{ route('employee.marketing-plans.platforms.update', [$plan, $plat]) }}" class="mt-3 grid grid-cols-1 md:grid-cols-12 gap-3 border-t border-gray-100 pt-3">
                                @csrf
                                @method('PUT')
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-600 mb-1">المنصة</label>
                                    <select name="platform_key" class="w-full rounded-lg border px-3 py-2 text-sm">
                                        @foreach($platformLabels as $key => $label)
                                            <option value="{{ $key }}" {{ $plat->platform_key === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">اسم مخصص</label>
                                    <input type="text" name="custom_label" value="{{ $plat->custom_label }}" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-600 mb-1">رابط</label>
                                    <input type="url" name="profile_url" value="{{ $plat->profile_url }}" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">لون</label>
                                    <input type="text" name="color_hex" value="{{ $plat->color_hex }}" pattern="#[0-9A-Fa-f]{6}" class="w-full rounded-lg border px-3 py-2 text-sm font-mono">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">ترتيب</label>
                                    <input type="number" name="sort_order" value="{{ $plat->sort_order }}" min="0" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-12">
                                    <label class="block text-xs text-gray-600 mb-2">التوصيف / الوظائف</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($employeeJobs as $job)
                                            <label class="inline-flex items-center gap-2 rounded-lg border px-2 py-1 text-xs">
                                                <input type="checkbox" name="employee_job_ids[]" value="{{ $job->id }}" class="rounded" @checked($plat->employeeJobs->contains('id', $job->id))>
                                                {{ $job->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="md:col-span-12">
                                    <label class="block text-xs text-gray-600 mb-1">استراتيجية</label>
                                    <textarea name="strategy_notes" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm">{{ $plat->strategy_notes }}</textarea>
                                </div>
                                <div class="md:col-span-12">
                                    <label class="block text-xs text-gray-600 mb-1">إيقاع النشر</label>
                                    <textarea name="cadence_notes" rows="1" class="w-full rounded-lg border px-3 py-2 text-sm">{{ $plat->cadence_notes }}</textarea>
                                </div>
                                <div class="md:col-span-12">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ</button>
                                </div>
                            </form>
                        </details>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6">لم تُضف منصات بعد. أضف منصة لربط الأحداث بها ولون التقويم.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- أحداث التقويم -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-gray-900"><i class="fas fa-calendar-alt text-slate-600 ml-2"></i> التقويم والمحتوى المجدول</h2>
            <p class="text-sm text-gray-600 mt-1">الأحداث تظهر في <a href="{{ route('employee.calendar') }}" class="text-pink-600 font-semibold underline">تقويمك</a>. يمكن ربط حدث بدورة تصميم أو بمنصة.</p>
        </div>
        <div class="p-5 space-y-6">
            <form method="post" action="{{ route('employee.marketing-plans.events.store', $plan) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                @csrf
                <div class="md:col-span-6">
                    <label class="block text-xs font-medium text-gray-600 mb-1">عنوان الحدث <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: إطلاق حملة الريلز">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">البداية <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="starts_at" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">النهاية</label>
                    <input type="datetime-local" name="ends_at" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-2">المنصات (اختيار متعدد)</label>
                    <div class="rounded-lg border border-gray-300 bg-white p-3 space-y-2 max-h-40 overflow-auto">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" x-model="isGeneralEvent" class="rounded border-gray-300">
                            <span class="font-semibold text-gray-800">عام للخطة (بدون منصات)</span>
                        </label>
                        <div class="border-t border-gray-100 pt-2 grid grid-cols-1 gap-2">
                            @foreach($plan->platforms as $plat)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="platform_ids[]" value="{{ $plat->id }}" class="rounded border-gray-300" :disabled="isGeneralEvent">
                                    <span class="font-semibold text-gray-800">{{ $plat->displayName() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2">لو اخترت “عام”، سيتم إنشاء حدث واحد غير مربوط بمنصة. لو اخترت منصات، سيتم إنشاء حدث لكل منصة بنفس المحتوى.</p>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">نوع المحتوى *</label>
                    <select name="content_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach(\App\Services\MarketingPlanEventAutomationService::contentTypeLabels() as $k => $l)
                            <option value="{{ $k }}" @selected($k === 'post')>{{ $l }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-violet-600 mt-1">تصميم → مصمم · مونتاج → مونتير · بوست → تأكيد تنفيذ يومي</p>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach(['idea' => 'فكرة', 'draft' => 'مسودة', 'scheduled' => 'مجدول', 'published' => 'منشور', 'skipped' => 'تم التخطي'] as $v => $l)
                            <option value="{{ $v }}" {{ $v === 'draft' ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">ربط بدورة تصميم</label>
                    <select name="design_task_cycle_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">— لا —</option>
                        @foreach($cycles as $c)
                            <option value="{{ $c->id }}">#{{ $c->id }} — {{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">تفاصيل / سكربت / ملاحظات</label>
                    <textarea name="body" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="md:col-span-12">
                    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="requires_confirmation" value="1" checked class="rounded border-gray-300"> يتطلب تأكيد «تم التنفيذ/الرفع» (غرامة عند التأخير)</label>
                </div>
                <div class="md:col-span-12">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900"><i class="fas fa-calendar-plus ml-1"></i> إضافة للتقويم</button>
                </div>
            </form>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-right px-3 py-2 font-semibold">الوقت</th>
                            <th class="text-right px-3 py-2 font-semibold">العنوان</th>
                            <th class="text-right px-3 py-2 font-semibold">النوع</th>
                            <th class="text-right px-3 py-2 font-semibold">المنصة</th>
                            <th class="text-right px-3 py-2 font-semibold">المسؤول</th>
                            <th class="text-right px-3 py-2 font-semibold">مهمة</th>
                            <th class="text-right px-3 py-2 font-semibold">التأكيد</th>
                            <th class="text-right px-3 py-2 font-semibold">الحالة</th>
                            <th class="text-right px-3 py-2 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($plan->calendarEvents as $ev)
                            <tr class="align-top hover:bg-slate-50/80">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $ev->starts_at->format('Y-m-d H:i') }}
                                    @if($ev->ends_at)<div class="text-xs text-gray-500">→ {{ $ev->ends_at->format('H:i') }}</div>@endif
                                </td>
                                <td class="px-3 py-2">
                                    <span class="font-medium text-gray-900">{{ $ev->title }}</span>
                                    @if($ev->body)<p class="text-xs text-gray-600 mt-1 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($ev->body, 120) }}</p>@endif
                                </td>
                                <td class="px-3 py-2 text-xs font-medium">{{ $contentTypes[$ev->content_type] ?? $ev->content_type ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    @if($ev->platform)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold">
                                            <span class="w-2 h-2 rounded-full" style="background: {{ $ev->platform->color_hex }}"></span>
                                            {{ $ev->platform->displayName() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs">{{ $ev->assignee?->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-xs">
                                    @if($ev->employeeTask)
                                        <a href="{{ route('employee.tasks.show', $ev->employeeTask) }}" class="text-violet-700 font-bold">#{{ $ev->employeeTask->id }}</a>
                                    @else — @endif
                                </td>
                                <td class="px-3 py-2 text-xs">
                                    @if($ev->isConfirmed())
                                        <span class="text-emerald-700 font-bold"><i class="fas fa-check"></i> تم</span>
                                    @elseif($ev->execution_penalty_deduction_id)
                                        <span class="text-rose-700 font-bold">غرامة</span>
                                    @elseif($ev->requires_confirmation)
                                        <span class="text-amber-700 font-bold">معلق</span>
                                    @else —
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs font-medium">{{ $evtStatus($ev->status) }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <details>
                                        <summary class="cursor-pointer text-xs font-semibold text-pink-700">تعديل</summary>
                                        <form method="post" action="{{ route('employee.marketing-plans.events.update', [$plan, $ev]) }}" class="mt-2 space-y-2 p-3 bg-white border border-gray-200 rounded-lg min-w-[240px]">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="title" value="{{ $ev->title }}" required class="w-full rounded border px-2 py-1 text-xs">
                                            <input type="datetime-local" name="starts_at" value="{{ $ev->starts_at->format('Y-m-d\TH:i') }}" required class="w-full rounded border px-2 py-1 text-xs">
                                            <input type="datetime-local" name="ends_at" value="{{ $ev->ends_at ? $ev->ends_at->format('Y-m-d\TH:i') : '' }}" class="w-full rounded border px-2 py-1 text-xs">
                                            <select name="platform_id" class="w-full rounded border px-2 py-1 text-xs">
                                                <option value="">— عام —</option>
                                                @foreach($plan->platforms as $plat)
                                                    <option value="{{ $plat->id }}" {{ (int)$ev->platform_id === (int)$plat->id ? 'selected' : '' }}>{{ $plat->displayName() }}</option>
                                                @endforeach
                                            </select>
                                            <select name="content_type" class="w-full rounded border px-2 py-1 text-xs">
                                                @foreach($contentTypes as $k => $l)
                                                    <option value="{{ $k }}" @selected($ev->content_type === $k)>{{ $l }}</option>
                                                @endforeach
                                            </select>
                                            <select name="status" class="w-full rounded border px-2 py-1 text-xs">
                                                @foreach(['idea', 'draft', 'scheduled', 'published', 'skipped'] as $v)
                                                    <option value="{{ $v }}" {{ $ev->status === $v ? 'selected' : '' }}>{{ $evtStatus($v) }}</option>
                                                @endforeach
                                            </select>
                                            <select name="design_task_cycle_id" class="w-full rounded border px-2 py-1 text-xs">
                                                <option value="">— لا —</option>
                                                @foreach($cycles as $c)
                                                    <option value="{{ $c->id }}" {{ (int)$ev->design_task_cycle_id === (int)$c->id ? 'selected' : '' }}>#{{ $c->id }}</option>
                                                @endforeach
                                            </select>
                                            <textarea name="body" rows="2" class="w-full rounded border px-2 py-1 text-xs">{{ $ev->body }}</textarea>
                                            <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="requires_confirmation" value="1" @checked($ev->requires_confirmation) class="rounded"> يتطلب تأكيد تنفيذ</label>
                                            <button type="submit" class="w-full py-1.5 rounded bg-slate-800 text-white text-xs font-semibold">حفظ</button>
                                        </form>
                                    </details>
                                    <form method="post" action="{{ route('employee.marketing-plans.events.destroy', [$plan, $ev]) }}" class="mt-1" onsubmit="return confirm('حذف الحدث؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 font-semibold">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">لا توجد أحداث بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('marketingPlanPage', () => ({
            isGeneralEvent: false,
        }));
    });
</script>
@endpush
