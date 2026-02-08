@extends('layouts.admin')

@section('title', 'تفاصيل المراجعة')
@section('header', 'تفاصيل المراجعة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تفاصيل المراجعة</h1>
                <p class="text-gray-600 mt-1">مراجعة #{{ $review->id }}</p>
            </div>
            <a href="{{ route('admin.reviews.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                رجوع
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الطالب</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">الاسم:</span> <span class="font-medium text-gray-900 mr-2">{{ $review->user->name ?? 'غير معروف' }}</span></div>
                    <div><span class="text-gray-600">البريد:</span> <span class="font-medium text-gray-900 mr-2">{{ $review->user->email ?? '-' }}</span></div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الكورس</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">اسم الكورس:</span> <span class="font-medium text-gray-900 mr-2">{{ $review->course->title ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">التقييم</h3>
            <div class="flex items-center gap-2 mb-4">
                @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-2xl"></i>
                @endfor
                <span class="mr-4 text-lg font-bold text-gray-900">({{ $review->rating }}/5)</span>
            </div>
        </div>

        @if($review->review || $review->comment)
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">المراجعة</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $review->review ?? $review->comment ?? '-' }}</p>
        </div>
        @endif

        <div class="border-t border-gray-200 pt-6">
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">تغيير الحالة</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" {{ (!$review->is_approved && !$review->status) || $review->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="approved" {{ ($review->is_approved || $review->status == 'approved') ? 'selected' : '' }}>مقبولة</option>
                        <option value="rejected" {{ $review->status == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الحالة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

