@extends('layouts.employee')

@section('title', 'طلبات التصميم')
@section('header', 'طلبات التصميم (مشرف → مصمم)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.design-cycles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-plus"></i>
            طلب تصميم جديد
        </a>
        <form method="get" class="flex flex-wrap items-center gap-2">
            <select name="status" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                @foreach([
                    'pending_design' => 'بانتظار المصمم',
                    'design_in_progress' => 'قيد التنفيذ',
                    'design_submitted' => 'تم تسليم التصميم',
                    'moderator_delivery_pending' => 'بانتظار تسليمك النهائي',
                    'completed' => 'مكتملة',
                    'cancelled' => 'ملغاة',
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">المصمم</th>
                        <th class="text-right px-4 py-3 font-semibold">حد التسليم</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cycles as $c)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs">{{ $c->id }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $c->title }}</td>
                            <td class="px-4 py-3">{{ $c->designer->name ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $c->deadline_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold bg-fuchsia-50 text-fuchsia-800 border border-fuchsia-100">
                                    {{ \App\Models\DesignTaskCycle::statusLabel($c->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('employee.design-cycles.show', $c) }}" class="text-fuchsia-700 hover:text-fuchsia-900 font-semibold">تفاصيل</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cycles->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $cycles->links() }}</div>
        @endif
    </div>
</div>
@endsection
