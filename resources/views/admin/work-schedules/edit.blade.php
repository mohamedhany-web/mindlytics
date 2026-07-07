@extends('layouts.admin')

@section('title', 'تعديل موعد عمل')
@section('header', 'تعديل موعد عمل')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل موعد عمل</h1>
                <p class="text-gray-600 mt-1">{{ $schedule->name }}</p>
            </div>
            <a href="{{ route('admin.work-schedules.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form method="post" action="{{ route('admin.work-schedules.update', $schedule) }}" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">بيانات الموعد</h2>
                @include('admin.work-schedules._form', ['schedule' => $schedule, 'dayOptions' => $dayOptions])
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.work-schedules.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث الموعد
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
