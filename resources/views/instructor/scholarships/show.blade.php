@extends('layouts.app')

@section('title', $program->name . ' - Mindlytics')
@section('header', 'طلاب المنحة')

@section('content')
@php
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-800',
        'activated' => 'bg-emerald-100 text-emerald-800',
        'rejected' => 'bg-rose-100 text-rose-800',
        'deactivated' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<div class="space-y-6">
    @include('instructor.scholarships._alerts')

    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-3">
            <a href="{{ route('instructor.scholarships.index') }}" class="hover:text-sky-600 transition-colors">المنح الدراسية</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $program->name }}</span>
        </nav>
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">{{ $program->name }}</h1>
                @if($program->description)
                    <p class="text-sm text-slate-600 mb-3">{{ $program->description }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $program->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        <i class="fas {{ $program->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
                        {{ $program->is_active ? 'منحة نشطة' : 'منحة غير نشطة' }}
                    </span>
                    @if($program->course)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                            <i class="fas fa-book-open"></i>
                            {{ $program->course->title }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                @if($program->course)
                    <a href="{{ route('instructor.courses.show', $program->course) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                        <i class="fas fa-book"></i>
                        <span>إدارة الكورس</span>
                    </a>
                @endif
                <a href="{{ route('instructor.scholarships.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>
    </div>

    @include('instructor.scholarships._nav', ['active' => 'programs'])

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مسجّل</p>
                <p class="text-2xl font-bold text-slate-800">{{ $program->registrations_count }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-users text-sky-600"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">بانتظار</p>
                <p class="text-2xl font-bold text-amber-600">{{ $program->pending_count }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مفعّل</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $program->activated_count }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مرفوض</p>
                <p class="text-2xl font-bold text-rose-600">{{ $program->rejected_count }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center">
                <i class="fas fa-times-circle text-rose-600"></i>
            </div>
        </div>
    </div>

    @include('instructor.scholarships._filters', [
        'programs' => collect(),
        'showProgramFilter' => false,
        'filterAction' => route('instructor.scholarships.show', $program),
    ])

    <!-- جدول الطلاب -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 sm:px-6 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-sky-600 text-xs"></i>
                </span>
                طلاب هذه المنحة
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">الاسم</th>
                        <th class="px-5 py-3 text-right font-semibold">البريد</th>
                        <th class="px-5 py-3 text-right font-semibold">الهاتف</th>
                        <th class="px-5 py-3 text-center font-semibold">الحالة</th>
                        <th class="px-5 py-3 text-center font-semibold">تاريخ التسجيل</th>
                        <th class="px-5 py-3 text-left font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $registration->user?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $registration->user?->email }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $registration->user?->phone ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusBadges[$registration->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $registration->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-600">{{ $registration->registered_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3.5">@include('instructor.scholarships._registration-actions', ['registration' => $registration])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-user-graduate text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">لا يوجد طلاب مسجّلون بعد.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 flex justify-center">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
