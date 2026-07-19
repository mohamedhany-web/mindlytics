@extends('layouts.app')

@section('title', 'تفاصيل الإنجاز')
@section('header', 'تفاصيل الإنجاز')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="mb-6">
            <a href="{{ route('student.achievements.index') }}" class="text-sky-600 hover:text-sky-900 mb-4 inline-block">
                <i class="fas fa-arrow-right mr-2"></i>رجوع إلى الإنجازات
            </a>
            <div class="flex items-center gap-4">
                @if($achievement->achievement && $achievement->achievement->icon)
                <i class="{{ $achievement->achievement->icon }} text-6xl text-yellow-600"></i>
                @else
                <i class="fas fa-trophy text-6xl text-yellow-600"></i>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $achievement->achievement->name ?? 'إنجاز' }}</h1>
                    <p class="text-gray-600 mt-1">{{ $achievement->achievement->category ?? $achievement->achievement->type ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($achievement->achievement && $achievement->achievement->description)
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">الوصف</h3>
            <p class="text-gray-600">{{ $achievement->achievement->description }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
                <div class="text-sm text-gray-600 mb-1">تاريخ الحصول</div>
                <div class="text-xl font-bold text-gray-900">{{ $achievement->earned_at ? $achievement->earned_at->format('Y-m-d') : '-' }}</div>
            </div>
            @if($achievement->points_earned)
            <div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl p-6 border border-sky-200">
                <div class="text-sm text-gray-600 mb-1">النقاط المكتسبة</div>
                <div class="text-xl font-bold text-sky-600">
                    <i class="fas fa-star mr-1"></i>{{ $achievement->points_earned }} نقاط
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

