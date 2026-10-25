@extends('layouts.admin')

@section('title', 'عرض الرسالة - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">عرض الرسالة</h1>
                    <p class="mt-2 text-gray-600">{{ $contactMessage->subject }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.contact-messages.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">الاسم</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contactMessage->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">البريد الإلكتروني</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contactMessage->email }}</p>
                    </div>
                    @if($contactMessage->phone)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">رقم الهاتف</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contactMessage->phone }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 mb-1">الموضوع</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contactMessage->subject }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">الرسالة</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $contactMessage->message }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">الحالة</p>
                        <p class="text-sm font-medium text-gray-900">
                            @if($contactMessage->read_at)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                مقروءة
                            </span>
                            @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                غير مقروءة
                            </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ الإرسال</p>
                        <p class="text-sm font-medium text-gray-900">{{ $contactMessage->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($contactMessage->read_at)
                    <div>
                        <p class="text-sm text-gray-500">تاريخ القراءة</p>
                        <p class="text-sm font-medium text-gray-900">{{ $contactMessage->read_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



