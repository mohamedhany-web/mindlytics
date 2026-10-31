@extends('layouts.admin')

@section('title', 'عرض السؤال - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">عرض السؤال</h1>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('admin.faq.edit', $faq) }}" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.faq.index') }}" 
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
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">السؤال</h2>
                    <p class="text-lg text-gray-700">{{ $faq->question }}</p>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">الإجابة</h3>
                    <p class="text-gray-600 whitespace-pre-line">{{ $faq->answer }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">الفئة</p>
                        <p class="text-sm font-medium text-gray-900">{{ $faq->category ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الترتيب</p>
                        <p class="text-sm font-medium text-gray-900">{{ $faq->order ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الحالة</p>
                        <p class="text-sm font-medium text-gray-900">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $faq->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800 }}">']
                                {{ $faq->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                        <p class="text-sm font-medium text-gray-900">{{ $faq->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



