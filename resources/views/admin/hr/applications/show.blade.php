@extends('layouts.admin')

@section('title', 'تفاصيل التقديم — HR')
@section('header', 'تفاصيل التقديم — HR')

@section('content')

@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $breakdown = is_array($application->scoring_notes) ? $application->scoring_notes : [];
    $matchedSkills = $breakdown['matched_skills'] ?? [];
    $weights = $breakdown['weights'] ?? config('hr.scoring_weights', []);
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._nav', ['active' => 'applications'])
    @include('admin.hr._alerts')

    @include('admin.hr._page-header', [
        'title' => $application->full_name,
        'subtitle' => 'وظيفة: ' . ($application->job?->title ?: '—') . ' — تاريخ التقديم: ' . ($application->submitted_at?->format('Y-m-d H:i') ?: '—'),
        'icon' => 'fas fa-user',
        'actions' => '
            <a href="' . route('admin.hr.applications.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع</a>
            <form method="post" action="' . route('admin.hr.applications.rescore', $application) . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $hrBtnDark . '"><i class="fas fa-sync-alt"></i> إعادة حساب السكور</button>
            </form>
            <form method="post" action="' . route('admin.hr.applications.destroy', $application) . '" class="inline" onsubmit="return confirm(\'حذف هذا التقديم؟\');">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">
                    <i class="fas fa-trash-alt"></i> حذف
                </button>
            </form>',
        'statCards' => [
            ['label' => 'الحالة', 'value' => $statusLabels[$application->status] ?? $application->status, 'icon' => 'fas fa-flag', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'السكور الإجمالي', 'value' => $application->displayScore() !== null ? number_format($application->displayScore(), 0) : '—', 'icon' => 'fas fa-star', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'سنوات الخبرة', 'value' => $application->parsed_experience_years !== null ? number_format((float) $application->parsed_experience_years, 1) : '—', 'icon' => 'fas fa-briefcase', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
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
                    <button type="submit" class="{{ $hrBtnDark }} !py-2"><i class="fas fa-save"></i> حفظ</button>
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
                        <p class="text-xs font-semibold text-slate-500 mb-1">المؤهل (من السيرة)</p>
                        <p class="text-sm font-bold text-slate-900">{{ $application->parsedEducationLabel() }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-2">المهارات المستخرجة من السيرة</p>
                    @if($application->normalizedParsedSkills() !== [])
                        <div class="flex flex-wrap gap-2">
                            @foreach($application->normalizedParsedSkills() as $sk)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ in_array($sk, $matchedSkills, true) ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">{{ $sk }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">لم تُستخرج مهارات — جرّب «إعادة حساب السكور» بعد التأكد من رفع CV.</p>
                    @endif
                </div>

                @if($application->cover_letter)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-2">رسالة التقديم</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 whitespace-pre-line">{{ $application->cover_letter }}</div>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-3">الملفات</p>
                    <div class="space-y-2">
                        @forelse($application->files as $file)
                            <div class="flex items-center justify-between gap-3 rounded-xl border-2 border-slate-200 bg-white px-4 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $file->original_name ?: $file->path }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $file->kind }} — {{ $file->mime ?: '—' }}</p>
                                </div>
                                <a href="{{ stored_upload_file_url($file->asStoredUploadArray()) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold">
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">لا توجد ملفات.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <aside class="xl:col-span-5 space-y-4">
            <section class="{{ $hrSectionClass }}">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-rose-50 to-pink-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-calculator text-rose-600"></i>
                        التقييم التلقائي (Rule-Based)
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">مهارات 60% · خبرة 30% · تعليم 10%</p>
                </div>
                <div class="p-5 space-y-4">
                    <div class="rounded-xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-5 text-center">
                        <p class="text-xs font-semibold text-slate-500 mb-1">النتيجة الإجمالية</p>
                        <p class="text-4xl font-black text-slate-900 tabular-nums">{{ $application->displayScore() !== null ? number_format($application->displayScore(), 0) : '—' }}</p>
                        <p class="text-[11px] text-slate-500 mt-2">آخر حساب: {{ $application->scored_at?->format('Y-m-d H:i') ?: '—' }}</p>
                    </div>

                    @php
                        $scoreRows = [
                            ['label' => 'المهارات', 'value' => $application->skills_score, 'weight' => ($weights['skills'] ?? 0.6) * 100],
                            ['label' => 'الخبرة', 'value' => $application->experience_score, 'weight' => ($weights['experience'] ?? 0.3) * 100],
                            ['label' => 'التعليم', 'value' => $application->education_score, 'weight' => ($weights['education'] ?? 0.1) * 100],
                        ];
                    @endphp

                    @foreach($scoreRows as $row)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-slate-900">{{ $row['label'] }}</span>
                                <span class="text-xs text-slate-500">وزن {{ number_format($row['weight'], 0) }}%</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-pink-500 to-rose-500" style="width: {{ min(100, (float) ($row['value'] ?? 0)) }}%"></div>
                                </div>
                                <span class="text-sm font-black text-slate-900 tabular-nums w-10 text-left">{{ $row['value'] !== null ? number_format((float) $row['value'], 0) : '—' }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($application->job)
                        <div class="rounded-xl border border-dashed border-slate-300 p-4 text-xs text-slate-600 space-y-2">
                            <p><strong class="text-slate-800">متطلبات الوظيفة:</strong></p>
                            <p>مهارات: {{ implode(', ', $application->job->normalizedRequiredSkills()) ?: '—' }}</p>
                            <p>خبرة: {{ $application->job->required_experience !== null ? $application->job->required_experience.' سنة' : '—' }}</p>
                            <p>تعليم: {{ $application->job->requiredEducationLabel() }}</p>
                            @if($matchedSkills !== [])
                                <p class="text-emerald-700 font-semibold">مطابقة: {{ implode(', ', $matchedSkills) }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
