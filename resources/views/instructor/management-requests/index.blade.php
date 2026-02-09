@extends('layouts.app')

@section('title', 'طلباتي للإدارة - Mindlytics')
@section('header', 'تقديم طلبات للإدارة')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex justify-end">
        <a href="{{ route('instructor.management-requests.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
            <i class="fas fa-plus"></i>
            تقديم طلب جديد
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <i class="fas fa-inbox text-indigo-600"></i>
                طلباتي للإدارة
            </h2>
            <p class="text-gray-500 mt-1">عرض الطلبات المقدمة للإدارة ومتابعة الردود</p>
        </div>

        <form method="GET" class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-4">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">الموضوع</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">التاريخ</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $req->subject }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($req->message, 60) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">قيد المراجعة</span>
                            @elseif($req->status == 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">موافق عليه</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">مرفوض</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $req->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('instructor.management-requests.show', $req) }}"
                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                <i class="fas fa-eye"></i>
                                عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="font-medium">لا توجد طلبات حتى الآن</p>
                            <a href="{{ route('instructor.management-requests.create') }}" class="mt-2 inline-block text-indigo-600 font-semibold">تقديم طلب جديد</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
