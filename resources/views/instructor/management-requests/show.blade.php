@extends('layouts.app')

@section('title', 'تفاصيل الطلب - Mindlytics')
@section('header', 'تفاصيل الطلب')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('instructor.management-requests.index') }}"
           class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h1 class="text-2xl font-black text-gray-900">{{ $request->subject }}</h1>
            <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                <span>التاريخ: {{ $request->created_at->format('Y-m-d H:i') }}</span>
                @if($request->status == 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">قيد المراجعة</span>
                @elseif($request->status == 'approved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">موافق عليه</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">مرفوض</span>
                @endif
            </div>
        </div>

        <div class="px-6 py-6">
            <h2 class="text-sm font-bold text-gray-500 mb-2">نص الطلب</h2>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $request->message }}</p>
        </div>

        @if($request->admin_reply)
            <div class="px-6 py-6 border-t border-gray-200 bg-indigo-50/50">
                <h2 class="text-sm font-bold text-indigo-800 mb-2">رد الإدارة</h2>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $request->admin_reply }}</p>
                <p class="text-sm text-gray-500 mt-3">
                    {{ $request->replied_at?->format('Y-m-d H:i') }}
                    @if($request->repliedByUser)
                        — {{ $request->repliedByUser->name }}
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
