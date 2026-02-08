@extends('layouts.admin')

@section('title', 'متابعة العمليات')
@section('header', 'متابعة العمليات')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">متابعة العمليات</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                <h3 class="font-semibold text-gray-900 mb-2">تسجيلات أونلاين</h3>
                <p class="text-2xl font-bold text-blue-700">{{ $enrollmentOperations['online_pending'] }} معلقة</p>
                <p class="text-sm text-gray-600">{{ $enrollmentOperations['online_active'] }} نشط</p>
            </div>
            <div class="border border-purple-200 rounded-lg p-4 bg-purple-50">
                <h3 class="font-semibold text-gray-900 mb-2">تسجيلات أوفلاين</h3>
                <p class="text-2xl font-bold text-purple-700">{{ $enrollmentOperations['offline_pending'] }} معلقة</p>
                <p class="text-sm text-gray-600">{{ $enrollmentOperations['offline_active'] }} نشط</p>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                <h3 class="font-semibold text-gray-900 mb-2">مهام الموظفين</h3>
                <p class="text-2xl font-bold text-yellow-700">{{ $taskOperations['pending'] }} معلقة</p>
                <p class="text-sm text-gray-600">{{ $taskOperations['overdue'] }} متأخرة</p>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-4">سجل النشاطات</h2>
        <div class="space-y-3">
            @foreach($activityLog as $activity)
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="font-semibold text-gray-900">{{ $activity->description }}</p>
                    <p class="text-sm text-gray-600">{{ $activity->user->name ?? 'نظام' }} - {{ $activity->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
