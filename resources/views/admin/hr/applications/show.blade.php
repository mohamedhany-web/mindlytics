@extends('layouts.admin')

@section('title', 'تفاصيل التقديم — HR')
@section('header', 'تفاصيل التقديم — HR')

@section('content')
@include('admin.hr._shared')

@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $score = $application->score;
    $selectedRubricId = (int) (request('rubric_id')
        ?? old('rubric_id')
        ?? $score?->rubric_id
        ?? ($rubrics->firstWhere('is_default', true)?->id ?? ($rubrics->first()?->id)));
    $selectedRubric = $rubrics->firstWhere('id', (int) $selectedRubricId);
    $criteria = $selectedRubric?->criteria_json ?? [];
    $existingScores = is_array($score?->scores_json) ? $score->scores_json : [];
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._nav', ['active' => 'applications'])
    @include('admin.hr._alerts')

    @include('admin.hr._page-header', [
        'title' => $application->full_name,
        'subtitle' => 'وظيفة: ' . ($application->job?->title ?: '—') . ' — تاريخ التقديم: ' . ($application->submitted_at?->format('Y-m-d H:i') ?: '—'),
        'icon' => 'fas fa-user',
        'actions' => '<a href="' . route('admin.hr.applications.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع للقائمة</a>',
        'statCards' => [
            ['label' => 'الحالة', 'value' => $statusLabels[$application->status] ?? $application->status, 'icon' => 'fas fa-flag', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'التقييم', 'value' => $score?->total_score !== null ? number_format((float) $score->total_score, 2) : '—', 'icon' => 'fas fa-star', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'الملفات', 'value' => $application->files->count(), 'icon' => 'fas fa-paperclip', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ],
    ])

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 items-start">
        <section class="{{ $hrSectionClass }} xl:col-span-7">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-id-card text-pink-600"></i>
                    بيانات المتقدم
                </h3>
                <form method="post" action="{{ route('admin.hr.applications.status', $application) }}" class="flex flex-wrap items-center gap-2">
                    @csrf @method('PUT')
                    <select name="status" class="{{ $hrSelectClass }} !py-2 !w-auto min-w-[140px]">
                        @foreach($statusLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('status', $application->status) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="{{ $hrBtnDark }} !py-2">
                        <i class="fas fa-save"></i>
                        حفظ الحالة
                    </button>
                </form>
            </div>
            <div class="p-5 sm:p-6 space-y-5">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">الاسم</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->full_name }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">الهاتف</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->phone ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">البريد</p>
                        <p class="text-sm font-bold text-slate-900 break-all">{{ $application->email ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">المصدر</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->source ?: '—' }}</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">LinkedIn</p>
                        @if($application->linkedin_url)
                            <a target="_blank" href="{{ $application->linkedin_url }}" class="text-sm font-semibold text-sky-700 hover:underline">فتح الملف</a>
                        @else
                            <p class="text-sm font-bold text-slate-900">—</p>
                        @endif
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">Portfolio</p>
                        @if($application->portfolio_url)
                            <a target="_blank" href="{{ $application->portfolio_url }}" class="text-sm font-semibold text-sky-700 hover:underline">فتح الرابط</a>
                        @else
                            <p class="text-sm font-bold text-slate-900">—</p>
                        @endif
                    </div>
                </div>

                @if($application->cover_letter)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-2">رسالة التقديم</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 whitespace-pre-line leading-relaxed">{{ $application->cover_letter }}</div>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-paperclip text-pink-600"></i>
                        الملفات المرفقة
                    </p>
                    <div class="space-y-2">
                        @forelse($application->files as $file)
                            <div class="flex items-center justify-between gap-3 rounded-xl border-2 border-slate-200 bg-white px-4 py-3 shadow-sm">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $file->original_name ?: $file->path }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">نوع: {{ $file->kind }} — {{ $file->mime ?: '—' }} — {{ $file->size ? number_format($file->size/1024, 1) . ' KB' : '—' }}</p>
                                </div>
                                <a href="{{ stored_upload_file_url($file->asStoredUploadArray()) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-sm transition-colors">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">لا توجد ملفات.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <aside class="xl:col-span-5 space-y-4 sm:space-y-6">
            <section class="{{ $hrSectionClass }}">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-rose-50 to-pink-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-star text-rose-600"></i>
                        التقييم
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">قالب التقييم + درجات لكل معيار. يُحسب المجموع تلقائياً.</p>
                </div>
                <div class="p-5 space-y-4">
                    @if($rubrics->isEmpty())
                        <div class="rounded-xl border-2 border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                            لا يوجد قوالب تقييم. أنشئ واحداً من
                            <a class="underline font-semibold" href="{{ route('admin.hr.rubrics.create') }}">قوالب التقييم</a>.
                        </div>
                    @else
                        <form method="post" action="{{ route('admin.hr.applications.score', $application) }}" class="space-y-4">
                            @csrf @method('PUT')

                            <div>
                                <label class="{{ $hrLabelClass }}">قالب التقييم</label>
                                <select name="rubric_id" class="{{ $hrSelectClass }}"
                                        onchange="window.location.search = new URLSearchParams({ rubric_id: this.value }).toString();">
                                    @foreach($rubrics as $r)
                                        <option value="{{ $r->id }}" @selected((int) $selectedRubricId === (int) $r->id)>{{ $r->name }}@if($r->is_default) (افتراضي) @endif</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">عند تغيير القالب سيتم إعادة تحميل الصفحة لعرض المعايير.</p>
                            </div>

                            <div class="space-y-3">
                                @forelse($criteria as $c)
                                    @php
                                        $key = (string) ($c['key'] ?? '');
                                        $label = (string) ($c['label'] ?? $key);
                                        $weight = (float) ($c['weight'] ?? 1);
                                        $max = (float) ($c['max'] ?? 10);
                                    @endphp
                                    @if($key !== '')
                                        <div class="rounded-xl border-2 border-slate-200 bg-slate-50/80 p-4">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <p class="text-sm font-bold text-slate-900">{{ $label }}</p>
                                                <p class="text-xs text-slate-500">وزن: {{ $weight }} · الحد: {{ $max }}</p>
                                            </div>
                                            <input type="number" name="scores[{{ $key }}]" step="0.1" min="0" max="{{ $max }}"
                                                   value="{{ old('scores.'.$key, $existingScores[$key] ?? 0) }}"
                                                   class="{{ $hrInputClass }}">
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-sm text-slate-600 rounded-xl border border-dashed border-slate-300 p-4 text-center">لا توجد معايير في هذا القالب.</div>
                                @endforelse
                            </div>

                            <div>
                                <label class="{{ $hrLabelClass }}">ملاحظات</label>
                                <textarea name="notes" rows="3" class="{{ $hrTextareaClass }}">{{ old('notes', $score?->notes ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-700 hover:to-red-600 text-white text-sm font-semibold shadow-lg transition-all">
                                <i class="fas fa-save"></i>
                                حفظ التقييم
                            </button>
                        </form>
                    @endif

                    <div class="rounded-xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-5 text-center">
                        <p class="text-xs font-semibold text-slate-500 mb-1">النتيجة الحالية</p>
                        <p class="text-3xl font-black text-slate-900 tabular-nums">{{ $score?->total_score !== null ? number_format((float) $score->total_score, 2) : '—' }}</p>
                        <p class="text-[11px] text-slate-500 mt-2">آخر تقييم: {{ $score?->scored_at?->format('Y-m-d H:i') ?: '—' }}</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
