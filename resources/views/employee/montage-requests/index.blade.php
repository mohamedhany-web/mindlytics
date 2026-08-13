@extends('layouts.employee')

@section('title', 'طلبات المونتاج')
@section('header', 'طلبات المونتاج (مشرف → مونتاج)')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.montage-requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-plus"></i>
            طلب مونتاج جديد
        </a>
        <form method="get" class="flex flex-wrap items-center gap-2">
            <select name="status" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                @foreach(\App\Models\ModeratorMontageRequest::statuses() as $val => $label)
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
                        <th class="text-right px-4 py-3 font-semibold">موظف المونتاج</th>
                        <th class="text-right px-4 py-3 font-semibold">حد التسليم</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $item)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-500">{{ $item->id }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item->title }}</td>
                            <td class="px-4 py-3">{{ $item->montageEmployee->name ?? '—' }}</td>
                            <td class="px-4 py-3 {{ $item->deadline_at && $item->deadline_at->isPast() && $item->isOpen() ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                {{ $item->deadline_at?->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">{{ $item->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('employee.montage-requests.show', $item) }}" class="text-cyan-700 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                لا توجد طلبات مونتاج بعد.
                                <a href="{{ route('employee.montage-requests.create') }}" class="block mt-2 text-cyan-700 font-semibold hover:underline">إنشاء أول طلب</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $requests->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
