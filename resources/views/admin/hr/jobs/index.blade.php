@extends('layouts.admin')

@section('title', 'الوظائف — HR')
@section('header', 'الوظائف — HR')

@section('content')
@include('admin.hr._shared')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._alerts')

    @include('admin.hr._nav', ['active' => 'jobs'])

    @include('admin.hr._page-header', [
        'title' => 'الوظائف',
        'subtitle' => 'أنشئ وظيفة، انشرها، ثم راقب التقديمات والتقييم داخل نظام ATS.',
        'icon' => 'fas fa-briefcase',
        'actions' => '<a href="' . route('admin.hr.jobs.create') . '" class="' . $hrBtnPrimary . '"><i class="fas fa-plus"></i> وظيفة جديدة</a>',
        'statCards' => [
            ['label' => 'إجمالي الوظائف', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-briefcase', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'description' => 'كل الوظائف المسجلة'],
            ['label' => 'منشورة', 'value' => number_format($stats['published'] ?? 0), 'icon' => 'fas fa-globe', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'ظاهرة في صفحة التوظيف'],
            ['label' => 'طلبات التوظيف', 'value' => number_format($stats['applications'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'إجمالي التقديمات'],
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
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label class="{{ $hrLabelClass }}">بحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="عنوان / قسم / مكان…" class="{{ $hrInputClass }}">
                </div>
                <div>
                    <label class="{{ $hrLabelClass }}">النشر</label>
                    <select name="published" class="{{ $hrSelectClass }}">
                        <option value="">الكل</option>
                        <option value="1" @selected(request('published') === '1')>منشور</option>
                        <option value="0" @selected(request('published') === '0')>غير منشور</option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="{{ $hrBtnDark }}"><i class="fas fa-search"></i> بحث</button>
                    <a href="{{ route('admin.hr.jobs.index') }}" class="{{ $hrBtnSecondary }}">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $hrSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-pink-600"></i>
                قائمة الوظائف
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الوظيفة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">القسم / المكان</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">منشور</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">طلبات</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $job->title }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $job->employment_type ?: '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div>{{ $job->department ?: '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $job->location ?: '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($job->is_published)
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">منشور</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">مسودة</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums">{{ $job->applications_count ?? 0 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('careers.show', $job) }}" target="_blank" class="{{ $hrBtnSecondary }} !px-3 !py-2 text-xs">
                                        <i class="fas fa-external-link-alt"></i>
                                        عامة
                                    </a>
                                    <a href="{{ route('admin.hr.jobs.edit', $job) }}" class="{{ $hrBtnPrimary }} !px-3 !py-2 text-xs">
                                        <i class="fas fa-edit"></i>
                                        تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">لا توجد وظائف حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">{{ $jobs->links() }}</div>
    </section>
</div>
@endsection
