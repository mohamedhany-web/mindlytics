@extends('layouts.admin')

@section('title', 'HR — قوالب التقييم')
@section('header', 'HR — قوالب التقييم')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900">قوالب التقييم (Rubrics)</h2>
            <p class="text-xs text-slate-600 mt-1">أنشئ معايير التقييم (key/label/weight/max) لاستخدامها في احتساب Score.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hr.applications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-inbox"></i>
                طلبات التوظيف
            </a>
            <a href="{{ route('admin.hr.rubrics.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">
                <i class="fas fa-plus"></i>
                قالب جديد
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold">الاسم</th>
                        <th class="px-4 py-3 text-center text-xs font-bold w-28">افتراضي</th>
                        <th class="px-4 py-3 text-right text-xs font-bold">المنشئ</th>
                        <th class="px-4 py-3 text-left text-xs font-bold w-28">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rubrics as $rubric)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $rubric->name }}</div>
                                <div class="text-xs text-slate-500">معايير: {{ is_array($rubric->criteria_json) ? count($rubric->criteria_json) : 0 }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($rubric->is_default)
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">نعم</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $rubric->creator?->name ?: '—' }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.hr.rubrics.edit', $rubric) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-semibold">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-600">لا توجد قوالب بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $rubrics->links() }}</div>
    </section>
</div>
@endsection

