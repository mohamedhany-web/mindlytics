@extends('layouts.admin')

@section('title', 'التقارير الشاملة')
@section('header', 'التقارير الشاملة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-excel text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">التقارير الشاملة</h2>
                    <p class="text-sm text-slate-600 mt-1">تقارير شاملة لجميع جوانب المنصة مع إمكانية التصدير إلى Excel</p>
                </div>
            </div>
        </div>
    </section>

    <!-- إحصائيات سريعة -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-users text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المستخدمين</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($quickStats['total_users']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-graduate text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الطلاب</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($quickStats['total_students']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-graduation-cap text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الكورسات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($quickStats['total_courses']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-file-invoice-dollar text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الفواتير</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($quickStats['total_invoices']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-history text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">النشاطات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($quickStats['total_activities']) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- فئات التقارير -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($reportCategories as $category)
            @php
                $colorClasses = [
                    'blue' => [
                        'bg' => 'bg-blue-100',
                        'text' => 'text-blue-600',
                        'border' => 'border-blue-200',
                        'icon' => 'text-blue-500',
                        'button' => 'bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600',
                        'header' => 'bg-gradient-to-br from-blue-50 to-white',
                    ],
                    'emerald' => [
                        'bg' => 'bg-emerald-100',
                        'text' => 'text-emerald-600',
                        'border' => 'border-emerald-200',
                        'icon' => 'text-emerald-500',
                        'button' => 'bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600',
                        'header' => 'bg-gradient-to-br from-emerald-50 to-white',
                    ],
                    'amber' => [
                        'bg' => 'bg-amber-100',
                        'text' => 'text-amber-600',
                        'border' => 'border-amber-200',
                        'icon' => 'text-amber-500',
                        'button' => 'bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-700 hover:to-amber-600',
                        'header' => 'bg-gradient-to-br from-amber-50 to-white',
                    ],
                    'purple' => [
                        'bg' => 'bg-purple-100',
                        'text' => 'text-purple-600',
                        'border' => 'border-purple-200',
                        'icon' => 'text-purple-500',
                        'button' => 'bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600',
                        'header' => 'bg-gradient-to-br from-purple-50 to-white',
                    ],
                    'indigo' => [
                        'bg' => 'bg-indigo-100',
                        'text' => 'text-indigo-600',
                        'border' => 'border-indigo-200',
                        'icon' => 'text-indigo-500',
                        'button' => 'bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600',
                        'header' => 'bg-gradient-to-br from-indigo-50 to-white',
                    ],
                    'rose' => [
                        'bg' => 'bg-rose-100',
                        'text' => 'text-rose-600',
                        'border' => 'border-rose-200',
                        'icon' => 'text-rose-500',
                        'button' => 'bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600',
                        'header' => 'bg-gradient-to-br from-rose-50 to-white',
                    ],
                ];
                $colors = $colorClasses[$category['color']] ?? $colorClasses['blue'];
            @endphp
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
                <div class="px-6 py-5 {{ $colors['header'] }} border-b border-slate-200">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 rounded-xl {{ $colors['bg'] }} flex items-center justify-center {{ $colors['text'] }} shadow-md">
                            <i class="{{ $category['icon'] }} text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-black text-slate-900">{{ $category['name'] }}</h3>
                            <p class="text-xs text-slate-600 mt-1">{{ $category['description'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-2 mb-4">
                        @foreach ($category['reports'] as $report)
                            <li class="flex items-center gap-2 text-sm text-slate-700">
                                <i class="fas fa-check-circle {{ $colors['icon'] }} text-xs"></i>
                                <span>{{ $report }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $category['route'] }}" class="block w-full text-center rounded-xl {{ $colors['button'] }} px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-arrow-left ml-2"></i>
                        عرض التقارير
                    </a>
                </div>
            </div>
        @endforeach
    </section>
</div>
@endsection
