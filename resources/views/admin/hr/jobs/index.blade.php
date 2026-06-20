@extends('layouts.admin')

@section('title', 'HR — الوظائف')
@section('header', 'HR — الوظائف')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">الوظائف</h2>
            <p class="text-xs text-slate-600 mt-1">أنشئ وظيفة، انشرها، ثم راقب التقديمات والـ Score داخل ATS.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hr.applications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-inbox"></i>
                طلبات التوظيف
            </a>
            <a href="{{ route('admin.hr.jobs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">
                <i class="fas fa-plus"></i>
                وظيفة جديدة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    <form class="rounded-2xl bg-white border border-slate-200 p-4 flex flex-col lg:flex-row lg:items-end gap-3" method="get">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
            <input name="search" value="{{ request('search') }}" placeholder="عنوان/قسم/مكان…" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="w-full lg:w-56">
            <label class="block text-xs font-semibold text-slate-700 mb-1">النشر</label>
            <select name="published" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="1" @selected(request('published') === '1')>منشور</option>
                <option value="0" @selected(request('published') === '0')>غير منشور</option>
            </select>
        </div>
        <button class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
            <i class="fas fa-filter"></i>
            تطبيق
        </button>
    </form>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold">الوظيفة</th>
                        <th class="px-4 py-3 text-right text-xs font-bold">القسم/المكان</th>
                        <th class="px-4 py-3 text-center text-xs font-bold w-28">منشور</th>
                        <th class="px-4 py-3 text-center text-xs font-bold w-28">طلبات</th>
                        <th class="px-4 py-3 text-left text-xs font-bold w-40">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $job->title }}</div>
                                <div class="text-xs text-slate-500">{{ $job->employment_type ?: '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <div>{{ $job->department ?: '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $job->location ?: '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($job->is_published)
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">نعم</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">لا</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800 tabular-nums">{{ $job->applications_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('careers.show', $job) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                                        <i class="fas fa-external-link-alt"></i>
                                        صفحة عامة
                                    </a>
                                    <a href="{{ route('admin.hr.jobs.edit', $job) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-semibold">
                                        <i class="fas fa-edit"></i>
                                        تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-600">لا توجد وظائف حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $jobs->links() }}</div>
    </section>
</div>
@endsection

