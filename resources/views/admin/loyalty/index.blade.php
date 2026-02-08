@extends('layouts.admin')

@section('title', 'برامج الولاء')
@section('header', 'برامج الولاء')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">برامج الولاء</h1>
                <p class="text-gray-600 mt-1">إدارة برامج نقاط الولاء</p>
            </div>
            <button onclick="document.getElementById('createProgramModal').classList.remove('hidden')" 
               class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-plus mr-2"></i>
                إضافة برنامج جديد
            </button>
        </div>
    </div>

    <!-- الإحصائيات -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">إجمالي البرامج</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">النشطة</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['active'] ?? 0 }}</div>
        </div>
    </div>
    @endif

    <!-- قائمة البرامج -->
    @if(isset($programs) && $programs->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($programs as $program)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $program->name }}</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $program->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $program->is_active ? 'نشط' : 'معطل' }}
                </span>
            </div>
            @if($program->description)
            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($program->description, 100) }}</p>
            @endif
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">نقاط لكل شراء:</span>
                    <span class="font-medium text-gray-900">{{ $program->points_per_purchase ?? 0 }}</span>
                </div>
                @if($program->points_per_referral)
                <div class="flex justify-between">
                    <span class="text-gray-600">نقاط لكل إحالة:</span>
                    <span class="font-medium text-gray-900">{{ $program->points_per_referral }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">عدد المستخدمين:</span>
                    <span class="font-medium text-gray-900">{{ $program->users_count ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.loyalty.show', $program) }}" class="text-sky-600 hover:text-sky-900 text-sm font-medium">عرض التفاصيل</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-star text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">لا توجد برامج ولاء</p>
    </div>
    @endif
</div>
@endsection
