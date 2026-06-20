@extends('layouts.admin')

@section('title', 'طلبات التوظيف — HR')
@section('header', 'طلبات التوظيف — HR')

@section('content')
@include('admin.hr._shared')

@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $statusBadges = [
        'new' => 'bg-sky-100 text-sky-700 border-sky-200',
        'screening' => 'bg-amber-100 text-amber-700 border-amber-200',
        'interview' => 'bg-violet-100 text-violet-700 border-violet-200',
        'offer' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'hired' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
    ];
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._alerts')
    @include('admin.hr._nav', ['active' => 'applications'])

    @include('admin.hr._page-header', [
        'title' => 'طلبات التوظيف',
        'subtitle' => 'فلترة حسب الوظيفة، الحالة، والحد الأدنى للتقييم.',
        'icon' => 'fas fa-inbox',
        'statCards' => [
            ['label' => 'إجمالي التقديمات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'جديد', 'value' => number_format($stats['new'] ?? 0), 'icon' => 'fas fa-star', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مقابلة', 'value' => number_format($stats['interview'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'تم التعيين', 'value' => number_format($stats['hired'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
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
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="{{ $hrLabelClass }}">بحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="اسم / إيميل / هاتف…" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">الوظيفة</label>
                    <select name="job_id" class="{{ $hrSelectClass }}">
                        <option value="">الكل</option>
                        @foreach($jobs as $job)
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
                    <label class="{{ $hrLabelClass }}">الحد الأدنى للتقييم</label>
                    <input name="min_score" value="{{ request('min_score') }}" type="number" step="0.01" class="{{ $hrInputClass }}">
                </div>
                <div class="md:col-span-2 lg:col-span-4 flex flex-wrap gap-2">
                    <button type="submit" class="{{ $hrBtnDark }}"><i class="fas fa-search"></i> بحث</button>
                    <a href="{{ route('admin.hr.applications.index') }}" class="{{ $hrBtnSecondary }}">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $hrSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-users text-pink-600"></i>
                قائمة المتقدمين
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المتقدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الوظيفة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">التقييم</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $app->full_name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $app->email ?: '—' }}@if($app->phone) · {{ $app->phone }}@endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-800 font-semibold">{{ $app->job?->title ?: '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusBadges[$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $statusLabels[$app->status] ?? $app->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums">
                                {{ $app->score?->total_score !== null ? number_format((float) $app->score->total_score, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-left">
                                <a href="{{ route('admin.hr.applications.show', $app) }}" class="{{ $hrBtnPrimary }} !px-3 !py-2 text-xs">
                                    <i class="fas fa-eye"></i>
                                    فتح
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">لا توجد تقديمات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">{{ $applications->links() }}</div>
    </section>
</div>
@endsection
