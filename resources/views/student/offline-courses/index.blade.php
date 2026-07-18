@extends('layouts.student-dashboard')

@section('title', ($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : __('student.offline_courses_title'))

@php
    $isOnline = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $pageTitle = $isOnline ? 'كورساتي الأونلاين' : __('student.offline_courses_title');
    $pageSub = $isOnline
        ? 'تظهر هنا فقط الكورسات الأونلاين المفعّلة في بوابة الطالب.'
        : __('student.offline_courses_subtitle');
    $badgeLabel = $isOnline ? 'أونلاين' : __('student.offline_badge');
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="مسار التنقل">
                <a href="{{ route('dashboard') }}">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ $pageTitle }}</span>
            </nav>
            <h1>{{ $pageTitle }}</h1>
            <p class="sub">{{ $pageSub }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ $stats['total_offline'] }} {{ __('student.courses_count_label') }}</span>
            <span class="oc-signal oc-signal-hot">{{ $stats['total_activities'] }} {{ __('student.activities_label') }}</span>
        </div>
    </header>

    <div class="oc-pulse" aria-label="ملخص">
        <div>
            <span class="lbl">{{ __('student.courses_count_label') }}</span>
            <span class="val teal">{{ $stats['total_offline'] }}</span>
        </div>
        <div>
            <span class="lbl">{{ __('student.activities_label') }}</span>
            <span class="val hot">{{ $stats['total_activities'] }}</span>
        </div>
        @if(($bookings ?? collect())->isNotEmpty())
            <div>
                <span class="lbl">حجوزات معلّقة</span>
                <span class="val">{{ ($bookings ?? collect())->count() }}</span>
            </div>
        @endif
    </div>

    <div class="oc-list" role="list">
        @forelse($enrollments as $enrollment)
            @php $course = $enrollment->course; @endphp
            <a href="{{ route($sg . '.show', $course->id) }}" class="oc-row" role="listitem">
                <div class="oc-ico" aria-hidden="true">
                    <i class="fas {{ $isOnline ? 'fa-laptop-house' : 'fa-chalkboard-teacher' }}"></i>
                </div>
                <div class="oc-body">
                    <h3>{{ $course->title }}</h3>
                    <p class="meta">
                        {{ $course->instructor->name ?? '—' }}
                        @if($course->locationModel || $course->location)
                            · {{ $course->locationModel->name ?? $course->location ?? '—' }}
                        @endif
                        @if($enrollment->group)
                            · {{ $enrollment->group->name }}
                        @endif
                    </p>
                    <div class="oc-prog">
                        <div class="bar"><i style="width:{{ min(100, (float) $enrollment->progress) }}%"></i></div>
                        <span class="pct">{{ number_format($enrollment->progress, 0) }}٪</span>
                        <span class="oc-badge oc-badge-live">{{ $badgeLabel }}</span>
                        @if((float) $enrollment->total_amount > 0)
                            @php
                                $pMap = ['paid' => 'oc-badge-ok', 'partial' => 'oc-badge-warn', 'unpaid' => 'oc-badge-bad'];
                                $pLabels = ['paid' => 'مدفوع', 'partial' => 'جزئي', 'unpaid' => 'غير مدفوع'];
                            @endphp
                            <span class="oc-badge {{ $pMap[$enrollment->payment_status] ?? 'oc-badge-warn' }}">
                                {{ $pLabels[$enrollment->payment_status] ?? '' }}
                            </span>
                        @endif
                    </div>
                </div>
                <span class="oc-side">عرض <i class="fas fa-arrow-left text-[10px]"></i></span>
            </a>
        @empty
            @if(($bookings ?? collect())->isEmpty())
                <div class="oc-empty">
                    <div class="icon"><i class="fas {{ $isOnline ? 'fa-laptop-house' : 'fa-chalkboard-teacher' }}"></i></div>
                    <h3>{{ __('student.no_offline_courses') }}</h3>
                    <p>{{ __('student.no_offline_courses_desc') }}</p>
                </div>
            @endif
        @endforelse

        @foreach(($bookings ?? collect()) as $booking)
            @php
                $course = $booking->course;
                $bookingOk = $booking->status === 'approved';
            @endphp
            @if($course)
                <div class="oc-row is-static" role="listitem">
                    <div class="oc-ico warn" aria-hidden="true"><i class="fas fa-hourglass-half"></i></div>
                    <div class="oc-body">
                        <h3>{{ $course->title }}</h3>
                        <p class="meta">
                            {{ $course->instructor->name ?? '—' }}
                            · تاريخ الحجز: {{ optional($booking->created_at)->format('Y-m-d') }}
                            @if($booking->assignedGroup || $booking->requestedGroup)
                                · {{ $booking->assignedGroup->name ?? $booking->requestedGroup->name }}
                            @endif
                        </p>
                        <span class="oc-badge {{ $bookingOk ? 'oc-badge-ok' : 'oc-badge-warn' }}">
                            {{ $bookingOk ? 'حجز مقبول' : 'حجز قيد المراجعة' }}
                        </span>
                        <p class="meta" style="margin-top:8px">سيظهر الكورس كمفعّل بعد اعتماد التسجيل النهائي من الإدارة.</p>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($enrollments->hasPages())
        <div style="margin-top:20px;display:flex;justify-content:center">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection
