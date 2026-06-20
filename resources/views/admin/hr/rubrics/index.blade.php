@extends('layouts.admin')

@section('title', 'قوالب التقييم — HR')
@section('header', 'قوالب التقييم — HR')

@section('content')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._alerts')
    @include('admin.hr._nav', ['active' => 'rubrics'])

    @include('admin.hr._page-header', [
        'title' => 'قوالب التقييم',
        'subtitle' => 'أنشئ معايير التقييم (key / label / weight / max) لاستخدامها في احتساب درجة المتقدم.',
        'icon' => 'fas fa-star-half-alt',
        'actions' => '<a href="' . route('admin.hr.rubrics.create') . '" class="' . $hrBtnPrimary . '"><i class="fas fa-plus"></i> قالب جديد</a>',
        'statCards' => [
            ['label' => 'إجمالي القوالب', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-list', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'قالب افتراضي', 'value' => ($stats['default'] ?? 0) > 0 ? 'نعم' : 'لا', 'icon' => 'fas fa-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ],
    ])

    <section class="{{ $hrSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-table text-pink-600"></i>
                قائمة القوالب
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">افتراضي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المنشئ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rubrics as $rubric)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $rubric->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">معايير: {{ is_array($rubric->criteria_json) ? count($rubric->criteria_json) : 0 }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($rubric->is_default)
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">افتراضي</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $rubric->creator?->name ?: '—' }}</td>
                            <td class="px-6 py-4 text-left">
                                <a href="{{ route('admin.hr.rubrics.edit', $rubric) }}" class="{{ $hrBtnPrimary }} !px-3 !py-2 text-xs">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">لا توجد قوالب بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">{{ $rubrics->links() }}</div>
    </section>
</div>
@endsection
