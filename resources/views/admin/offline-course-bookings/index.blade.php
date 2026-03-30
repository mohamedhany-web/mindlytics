@extends('layouts.admin')

@section('title', 'طلبات حجز الكورسات الأوفلاين')
@section('header', 'طلبات حجز الكورسات الأوفلاين')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-amber-200 p-5 shadow-sm">
            <p class="text-sm text-gray-600">قيد المراجعة</p>
            <p class="text-3xl font-black text-amber-700">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form method="get" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">الكل</option>
                    <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                    <option value="approved" @selected(request('status') === 'approved')>مقبول</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الكورس</label>
                <select name="offline_course_id" class="rounded-lg border-gray-300 text-sm min-w-[200px]">
                    <option value="">الكل</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected((string) request('offline_course_id') === (string) $c->id)>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">تصفية</button>
            <a href="{{ route('admin.offline-course-bookings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">إعادة تعيين</a>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">#</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الطالب</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الكورس</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">المجموعة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الطريقة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-700"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-gray-600">{{ $b->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $b->user->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $b->user->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-800">{{ $b->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                @if($b->requestedGroup)
                                    {{ $b->requestedGroup->name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $b->payment_method === 'wallet' ? 'محفظة' : 'تحويل' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($b->status === 'pending')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">قيد المراجعة</span>
                                @elseif($b->status === 'approved')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مقبول</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">مرفوض</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $b->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.offline-course-bookings.show', $b) }}" class="text-blue-600 hover:text-blue-800 font-medium">تفاصيل</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
