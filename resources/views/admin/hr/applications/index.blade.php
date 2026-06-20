@extends('layouts.admin')

@section('title', 'HR — طلبات التوظيف')
@section('header', 'HR — طلبات التوظيف')

@section('content')
@php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
@endphp
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">طلبات التوظيف</h2>
            <p class="text-xs text-slate-600 mt-1">فلترة حسب الوظيفة/الحالة/الحد الأدنى للـ Score.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hr.jobs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-briefcase"></i>
                الوظائف
            </a>
            <a href="{{ route('admin.hr.rubrics.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-star-half-alt"></i>
                قوالب التقييم
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    <form class="rounded-2xl bg-white border border-slate-200 p-4 grid grid-cols-1 lg:grid-cols-12 gap-3 items-end" method="get">
        <div class="lg:col-span-4">
            <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
            <input name="search" value="{{ request('search') }}" placeholder="اسم/إيميل/هاتف…" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="lg:col-span-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">الوظيفة</label>
            <select name="job_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($jobs as $job)
                    <option value="{{ $job->id }}" @selected((string) request('job_id') === (string) $job->id)>{{ $job->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="lg:col-span-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($statusLabels as $k => $label)
                    <option value="{{ $k }}" @selected((string) request('status') === (string) $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="lg:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Min Score</label>
            <input name="min_score" value="{{ request('min_score') }}" type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="lg:col-span-12 flex gap-2">
            <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                <i class="fas fa-filter"></i>
                تطبيق
            </button>
            <a href="{{ route('admin.hr.applications.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                مسح
            </a>
        </div>
    </form>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold">المتقدم</th>
                        <th class="px-4 py-3 text-right text-xs font-bold">الوظيفة</th>
                        <th class="px-4 py-3 text-center text-xs font-bold w-32">الحالة</th>
                        <th class="px-4 py-3 text-center text-xs font-bold w-28">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-bold w-28">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $app->full_name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $app->email ?: '—' }} @if($app->phone) · {{ $app->phone }} @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-800 font-semibold">{{ $app->job?->title ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $statusLabels[$app->status] ?? $app->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-black text-slate-900 tabular-nums">
                                {{ $app->score?->total_score !== null ? number_format((float) $app->score->total_score, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.hr.applications.show', $app) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-semibold">
                                    <i class="fas fa-eye"></i>
                                    فتح
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-600">لا توجد تقديمات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $applications->links() }}</div>
    </section>
</div>
@endsection

