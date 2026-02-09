@extends('layouts.app')

@section('title', 'تقديم طلب للإدارة - Mindlytics')
@section('header', 'تقديم طلب للإدارة')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <h1 class="text-2xl font-black text-gray-900 mb-2">تقديم طلب جديد للإدارة</h1>
        <p class="text-gray-500 mb-6">اكتب موضوع الطلب والتفاصيل وسيتم مراجعته والرد عليه من قبل الإدارة.</p>

        <form action="{{ route('instructor.management-requests.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">موضوع الطلب *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="مثال: طلب مواد إضافية للكورس">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">تفاصيل الطلب *</label>
                <textarea name="message" rows="6" required
                          class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="اشرح طلبك بالتفصيل...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-4 justify-end">
                <a href="{{ route('instructor.management-requests.index') }}"
                   class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-paper-plane ml-2"></i>
                    إرسال الطلب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
