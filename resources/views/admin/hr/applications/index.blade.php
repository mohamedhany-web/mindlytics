@extends('layouts.admin')

@section('title', 'طلبات التوظيف — HR')
@section('header', 'طلبات التوظيف — HR')

@section('content')

@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $statusBadges = [
        'applied' => 'bg-sky-100 text-sky-700 border-sky-200',
        'under_review' => 'bg-amber-100 text-amber-700 border-amber-200',
        'interview' => 'bg-violet-100 text-violet-700 border-violet-200',
        'accepted' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
    ];
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._alerts')
    @include('admin.hr._nav', ['active' => 'applications'])

    @include('admin.hr._page-header', [
        'title' => 'طلبات التوظيف — ATS',
        'subtitle' => 'التقديمات مجمّعة حسب الوظيفة ومرتّبة تلقائياً حسب السكور (Rule-Based).',
        'icon' => 'fas fa-inbox',
        'statCards' => [
            ['label' => 'إجمالي التقديمات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'تم التقديم', 'value' => number_format($stats['applied'] ?? 0), 'icon' => 'fas fa-star', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مقابلة', 'value' => number_format($stats['interview'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'مقبول', 'value' => number_format($stats['accepted'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ],
    ])

    <section class="{{ $hrSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-pink-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-5">
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="{{ $hrLabelClass }}">بحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="اسم / إيميل / هاتف…" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">الوظيفة</label>
                    <select name="job_id" class="{{ $hrSelectClass }}">
                        <option value="">الكل</option>
                        @foreach($allJobs as $job)
                            <option value="{{ $job->id }}" @selected((string) request('job_id') === (string) $job->id)>{{ $job->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">الحالة</label>
                    <select name="status" class="{{ $hrSelectClass }}">
                        <option value="">الكل</option>
                        @foreach($statusLabels as $k => $label)
                            <option value="{{ $k }}" @selected((string) request('status') === (string) $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">المهارة</label>
                    <input name="skill" value="{{ request('skill') }}" placeholder="Excel, SQL…" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">الحد الأدنى للسكور</label>
                    <input name="min_score" value="{{ request('min_score') }}" type="number" step="0.01" min="0" max="100" placeholder="80" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">الحد الأدنى للخبرة (سنوات)</label>
                    <input name="min_experience" value="{{ request('min_experience') }}" type="number" step="0.5" min="0" placeholder="2" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">المؤهل الدراسي</label>
                    <select name="education" class="{{ $hrSelectClass }}">
                        <option value="">الكل</option>
                        @foreach($educationLevels as $k => $meta)
                            <option value="{{ $k }}" @selected((string) request('education') === (string) $k)>{{ $meta['label'] ?? $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3 flex flex-wrap gap-2">
                    <button type="submit" class="{{ $hrBtnDark }}"><i class="fas fa-search"></i> بحث</button>
                    <a href="{{ route('admin.hr.applications.index') }}" class="{{ $hrBtnSecondary }}">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <div class="space-y-4">
        @forelse($jobs as $job)
            <section class="{{ $hrSectionClass }} overflow-hidden">
                <details class="group" @if($loop->first) open @endif>
                    <summary class="cursor-pointer list-none px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-pink-50 to-sky-50 hover:from-pink-100/80 hover:to-sky-100/80 transition-colors">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-600 to-rose-500 flex items-center justify-center text-white shadow-md shrink-0">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-black text-slate-900 truncate">{{ $job->title }}</h3>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        @if($job->normalizedRequiredSkills() !== [])
                                            مهارات: {{ implode(' · ', array_slice($job->normalizedRequiredSkills(), 0, 4)) }}
                                        @else
                                            {{ $job->department ?: '—' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-800">
                                    <i class="fas fa-users text-pink-600 text-xs"></i>
                                    {{ $job->applications_count }} متقدم
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                            </div>
                        </div>
                    </summary>

                    <div class="overflow-x-auto">
                        @if($job->applications->isEmpty())
                            <div class="px-6 py-10 text-center text-slate-500 text-sm">لا توجد تقديمات لهذه الوظيفة ضمن الفلتر الحالي.</div>
                        @else
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">#</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المتقدم</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">السكور</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الخبرة</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($job->applications as $rank => $app)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="px-6 py-4 text-slate-400 font-bold tabular-nums">{{ $rank + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900">{{ $app->full_name }}</div>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    {{ $app->email ?: '—' }}@if($app->phone) · {{ $app->phone }}@endif
                                                </div>
                                                @if($app->normalizedParsedSkills() !== [])
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach(array_slice($app->normalizedParsedSkills(), 0, 4) as $sk)
                                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">{{ $sk }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @php $score = $app->displayScore(); @endphp
                                                @if($score !== null)
                                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2.5 py-1 rounded-lg text-sm font-black {{ $score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                                        {{ number_format($score, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center text-slate-700 tabular-nums">
                                                {{ $app->parsed_experience_years !== null ? number_format((float) $app->parsed_experience_years, 1).' سنة' : '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusBadges[$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                    {{ $statusLabels[$app->status] ?? $app->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-left">
                                                <div class="flex flex-wrap items-center justify-end gap-2">
                                                    <a href="{{ route('admin.hr.applications.show', $app) }}" class="{{ $hrBtnPrimary }} !px-3 !py-2 text-xs">
                                                        <i class="fas fa-eye"></i> فتح
                                                    </a>
                                                    <form method="post" action="{{ route('admin.hr.applications.destroy', $app) }}"
                                                          onsubmit="return confirm('حذف تقديم «{{ addslashes($app->full_name) }}»؟');" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">
                                                            <i class="fas fa-trash-alt"></i> حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </details>
            </section>
        @empty
            <section class="{{ $hrSectionClass }}">
                <div class="px-6 py-14 text-center text-slate-500">
                    <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                    <p class="font-semibold">لا توجد وظائف أو تقديمات مطابقة للبحث.</p>
                </div>
            </section>
        @endforelse
    </div>

    @if($jobs->hasPages())
        <div class="flex justify-center">{{ $jobs->links() }}</div>
    @endif
</div>
@endsection
