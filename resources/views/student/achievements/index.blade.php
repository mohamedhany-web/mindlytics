@extends('layouts.app')

@section('title', 'إنجازاتي')
@section('header', 'إنجازاتي')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">إنجازاتي</h1>
        @if(isset($stats))
        <div class="mt-4">
            <div class="text-sm text-gray-600">إجمالي النقاط</div>
            <div class="text-3xl font-bold text-sky-600 mt-2">{{ $stats['total_points'] ?? 0 }}</div>
        </div>
        @endif
    </div>

    @if(isset($achievements) && $achievements->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($achievements as $achievement)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-center">
                @if($achievement->achievement && $achievement->achievement->icon)
                <i class="{{ $achievement->achievement->icon }} text-6xl text-yellow-600 mb-4"></i>
                @else
                <i class="fas fa-trophy text-6xl text-yellow-600 mb-4"></i>
                @endif
                <h3 class="text-lg font-bold text-gray-900">{{ $achievement->achievement->name ?? 'إنجاز' }}</h3>
                <p class="text-sm text-gray-600 mt-2">{{ $achievement->achievement->description ?? '' }}</p>
                @if($achievement->points_earned)
                <div class="mt-4 text-sky-600 font-bold">+{{ $achievement->points_earned }} نقاط</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $achievements->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-trophy text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600">لا توجد إنجازات</p>
    </div>
    @endif
</div>
@endsection
