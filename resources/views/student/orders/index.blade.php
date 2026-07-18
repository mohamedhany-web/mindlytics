@extends('layouts.student-dashboard')

@section('title', __('student.orders_page_title'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .od-list { display: flex; flex-direction: column; gap: 10px; }
    .od-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
        padding: 14px 16px;
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease;
    }
    .od-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    }
    .od-head {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        margin-bottom: 6px;
    }
    .od-head h3 { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35; }
    .od-meta { margin: 0 0 10px; font-size: 12px; color: var(--ml-muted); line-height: 1.5; }
    .od-facts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 8px;
    }
    .od-facts > div {
        padding: 8px 10px; border-radius: 10px; background: var(--ml-well);
    }
    .od-facts .k { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 2px; }
    .od-facts .v { font-size: 13px; font-weight: 700; color: var(--ml-ink); }
    .od-notes {
        margin-top: 10px; padding: 10px 12px; border-radius: 10px;
        background: rgba(73, 164, 162, 0.08); border: 1px solid rgba(73, 164, 162, 0.2);
        font-size: 12px; color: var(--ml-ink); line-height: 1.55; white-space: pre-wrap;
    }
    .od-notes .lbl { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 4px; }
    .od-side {
        display: flex; flex-direction: column; gap: 8px; min-width: 140px;
    }
    .od-side .oc-btn { width: 100%; justify-content: center; }
    @media (max-width: 720px) {
        .od-card { grid-template-columns: 1fr; }
        .od-side { flex-direction: row; min-width: 0; width: 100%; }
        .od-side .oc-btn { flex: 1; }
    }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.orders_page_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.orders_page_title') }}</span>
            </nav>
            <h1>{{ __('student.orders_page_title') }}</h1>
            <p class="sub">{{ __('student.orders_subtitle') }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ $stats['total'] ?? $orders->total() }} {{ __('student.orders_count_label') }}</span>
            @if(($stats['pending'] ?? 0) > 0)
                <span class="oc-signal oc-signal-hot">{{ $stats['pending'] }} {{ __('student.orders_pending_label') }}</span>
            @endif
        </div>
    </header>

    <section class="oc-stage">
        <div class="oc-eyebrow">{{ __('student.orders_tracking') }}</div>
        <h2>{{ __('student.orders_page_title') }}</h2>
        <p class="oc-copy">{{ __('student.orders_subtitle') }}</p>
        <div class="oc-nav">
            <a class="oc-btn" href="{{ route('academic-years') }}">
                <i class="fas fa-search text-xs"></i> {{ __('student.browse_courses_btn') }}
            </a>
        </div>
    </section>

    @if(isset($stats))
        <div class="oc-pulse" aria-label="{{ __('student.orders_page_title') }}">
            <div>
                <span class="lbl">{{ __('student.orders_count_label') }}</span>
                <span class="val teal">{{ $stats['total'] }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.orders_pending_label') }}</span>
                <span class="val hot">{{ $stats['pending'] }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.orders_approved_label') }}</span>
                <span class="val">{{ $stats['approved'] }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.orders_rejected_label') }}</span>
                <span class="val">{{ $stats['rejected'] }}</span>
            </div>
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="od-list">
            @foreach($orders as $order)
                @php
                    $title = $order->academic_year_id && $order->learningPath
                        ? ($order->learningPath->name ?? __('student.learning_path_label'))
                        : ($order->course->title ?? (\Illuminate\Support\Str::before($order->notes ?? __('student.course_undefined'), "\n") ?: __('student.course_undefined')));
                    $badge = match ($order->status) {
                        'approved' => 'oc-badge-ok',
                        'rejected' => 'oc-badge-bad',
                        default => 'oc-badge-warn',
                    };
                    $pay = match ($order->payment_method) {
                        'bank_transfer' => __('student.bank_transfer'),
                        'cash' => __('student.cash_label'),
                        default => __('student.other_label'),
                    };
                @endphp
                <article class="od-card">
                    <div class="min-w-0">
                        <div class="od-head">
                            <h3>{{ $title }}</h3>
                            <span class="oc-badge {{ $badge }}">{{ $order->status_text }}</span>
                        </div>
                        <p class="od-meta">
                            @if($order->academic_year_id && $order->learningPath)
                                {{ __('student.learning_path_label') }}
                                @if($order->learningPath->price)
                                    · {{ number_format($order->learningPath->price, 2) }} {{ __('public.currency_egp') }}
                                @endif
                            @elseif($order->course && ($order->course->academicYear || $order->course->academicSubject))
                                @if($order->course->academicYear){{ $order->course->academicYear->name }}@endif
                                @if($order->course->academicSubject)
                                    @if($order->course->academicYear) · @endif{{ $order->course->academicSubject->name }}
                                @endif
                            @endif
                            · {{ $order->created_at->diffForHumans() }}
                        </p>
                        <div class="od-facts">
                            <div>
                                <span class="k">{{ __('student.amount_label') }}</span>
                                <span class="v">{{ number_format($order->amount, 2) }} {{ __('public.currency_egp') }}</span>
                            </div>
                            <div>
                                <span class="k">{{ __('student.payment_method_label') }}</span>
                                <span class="v">{{ $pay }}</span>
                            </div>
                            <div>
                                <span class="k">{{ __('student.order_date_label') }}</span>
                                <span class="v">{{ $order->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($order->approved_at)
                                <div>
                                    <span class="k">{{ __('student.approved_date_label') }}</span>
                                    <span class="v">{{ $order->approved_at->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                        @if($order->notes)
                            <div class="od-notes">
                                <span class="lbl">{{ __('student.your_notes') }}</span>
                                {{ \Illuminate\Support\Str::limit($order->notes, 180) }}
                            </div>
                        @endif
                    </div>
                    <div class="od-side">
                        <a href="{{ route('orders.show', $order) }}" class="oc-btn">
                            <i class="fas fa-eye text-xs"></i> {{ __('student.view_details') }}
                        </a>
                        @if($order->status == 'approved' && $order->course)
                            <a href="{{ route('courses.show', $order->course) }}" class="oc-btn oc-btn-quiet">
                                <i class="fas fa-play text-xs"></i> {{ __('student.enter_course') }}
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($orders->hasPages())
            <div style="margin-top:20px;display:flex;justify-content:center">
                {{ $orders->links() }}
            </div>
        @endif
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <h3>{{ __('student.no_orders') }}</h3>
            <p>{{ __('student.no_orders_desc') }}</p>
            <div style="margin-top:16px">
                <a href="{{ route('academic-years') }}" class="oc-btn">
                    <i class="fas fa-plus text-xs"></i> {{ __('student.browse_courses_btn') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
