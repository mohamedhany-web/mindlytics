@extends('layouts.student-dashboard')

@php
    $statusPill = fn (string $status) => match ($status) {
        'pending' => 'sp-pill--upcoming',
        'approved' => 'sp-pill--done',
        'rejected' => 'sp-pill',
        default => '',
    };
    $statusLabel = fn (string $status) => match ($status) {
        'pending' => __('student.order_status_pending'),
        'approved' => __('student.order_status_approved'),
        'rejected' => __('student.order_status_rejected'),
        default => __('student.order_status_unknown'),
    };
    $paymentLabel = fn (?string $method) => match ($method) {
        'bank_transfer' => __('student.bank_transfer'),
        'cash' => __('student.cash_label'),
        default => __('student.other_label'),
    };
@endphp

@section('title', __('student.orders_page_title'))
@section('header', __('student.orders_page_title'))

@push('styles')
<style>
    .sp-order-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-order-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.28), transparent 70%);
        pointer-events: none;
    }
    .sp-order-row { transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease; }
    .sp-order-row:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,0.1); border-color: var(--sp-accent); }
</style>
@endpush

@section('content')
<div class="space-y-5">
    {{-- Hero --}}
    <section class="sp-order-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.orders_index_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ __('student.orders_page_title') }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 max-w-2xl leading-relaxed">{{ __('student.orders_subtitle') }}</p>
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('academic-years') }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                        <x-student.figma-icon name="icon-search.svg" box="size-4" class="me-2" />
                        {{ __('student.browse_courses_btn') }}
                    </a>
                </div>
            </div>
            @if(isset($stats))
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 shrink-0 w-full lg:w-auto lg:min-w-[320px]">
                    @foreach([
                        ['key' => 'total', 'label' => __('student.order_stat_total')],
                        ['key' => 'pending', 'label' => __('student.order_stat_pending')],
                        ['key' => 'approved', 'label' => __('student.order_stat_approved')],
                        ['key' => 'rejected', 'label' => __('student.order_stat_rejected')],
                    ] as $stat)
                        <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                            <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats[$stat['key']] ?? 0 }}</p>
                            <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide leading-tight">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if(isset($stats))
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['key' => 'total', 'label' => __('student.order_stat_total'), 'icon' => 'icon-orders.svg', 'bg' => 'var(--sp-sky)'],
                ['key' => 'pending', 'label' => __('student.order_stat_pending'), 'icon' => 'icon-calendar.svg', 'bg' => 'var(--sp-amber-soft)'],
                ['key' => 'approved', 'label' => __('student.order_stat_approved'), 'icon' => 'icon-star.svg', 'bg' => 'var(--sp-mint)'],
                ['key' => 'rejected', 'label' => __('student.order_stat_rejected'), 'icon' => 'icon-settings.svg', 'bg' => 'var(--sp-peach)'],
            ] as $stat)
                <div class="sp-card p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats[$stat['key']] ?? 0 }}</p>
                        </div>
                        <span class="sp-icon-bubble shrink-0" style="background:{{ $stat['bg'] }}">
                            <x-student.figma-icon :name="$stat['icon']" />
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($orders->count() > 0)
        <section class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-orders.svg" />
                </span>
                <div>
                    <h3 class="sp-section-title m-0">{{ __('student.orders_tracking') }}</h3>
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.orders_list_hint') }}</p>
                </div>
            </div>

            @foreach($orders as $order)
                @php
                    $title = $order->academic_year_id && $order->learningPath
                        ? ($order->learningPath->name ?? __('student.learning_path_label'))
                        : ($order->course?->title ?? \Illuminate\Support\Str::before($order->notes ?? __('student.course_undefined'), "\n") ?: __('student.course_undefined'));
                @endphp
                <article class="sp-order-row sp-card p-4 sm:p-5 border border-[#f0f0ec]">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <span class="sp-icon-bubble shrink-0 !w-11 !h-11" style="background:var(--sp-sky)">
                                <x-student.figma-icon name="{{ $order->academic_year_id ? 'icon-path.svg' : 'icon-courses.svg' }}" box="size-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="font-extrabold text-sm sm:text-base m-0 line-clamp-2">{{ $title }}</h4>
                                    <span class="sp-pill {{ $statusPill($order->status) }} shrink-0">{{ $statusLabel($order->status) }}</span>
                                </div>
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 flex flex-wrap gap-x-2 gap-y-1">
                                    <span>{{ __('student.order_number_label', ['id' => $order->id]) }}</span>
                                    <span class="opacity-40">·</span>
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                    @if($order->course?->academicYear)
                                        <span class="opacity-40">·</span>
                                        <span>{{ $order->course->academicYear->name }}</span>
                                    @endif
                                    @if($order->course?->academicSubject)
                                        <span>· {{ $order->course->academicSubject->name }}</span>
                                    @endif
                                </p>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.amount_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ number_format($order->amount, 2) }} {{ __('public.currency_egp') }}</p>
                                    </div>
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.payment_method_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5 truncate">{{ $paymentLabel($order->payment_method) }}</p>
                                    </div>
                                    <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.order_date_label') }}</p>
                                        <p class="text-sm font-extrabold m-0 mt-0.5">{{ $order->created_at->format('Y/m/d') }}</p>
                                    </div>
                                    @if($order->approved_at)
                                        <div class="rounded-[14px] px-3 py-2.5" style="background:var(--sp-mint)">
                                            <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.approved_date_label') }}</p>
                                            <p class="text-sm font-extrabold m-0 mt-0.5">{{ $order->approved_at->format('Y/m/d') }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($order->notes)
                                    <div class="rounded-[14px] bg-[#fafaf8] border border-black/5 px-3 py-2.5 mt-3">
                                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.your_notes') }}</p>
                                        <p class="text-sm m-0 whitespace-pre-line">{{ $order->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-row lg:flex-col gap-2 shrink-0 w-full lg:w-auto">
                            <a href="{{ route('orders.show', $order) }}" class="sp-promo-btn !mt-0 flex-1 lg:flex-none !text-[var(--sp-accent-text)] justify-center">
                                {{ __('student.view_details') }}
                            </a>
                            @if($order->status === 'approved' && $order->course)
                                <a href="{{ route('courses.show', $order->course) }}" class="inline-flex flex-1 lg:flex-none items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-5 py-3.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                                    {{ __('student.enter_course') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        @if($orders->hasPages())
            <div class="flex justify-center pt-2">{{ $orders->links() }}</div>
        @endif
    @else
        <div class="sp-card p-10 sm:p-12 text-center">
            <span class="sp-icon-bubble mx-auto mb-4 !w-16 !h-16" style="background:var(--sp-sky)">
                <x-student.figma-icon name="icon-orders.svg" box="size-8" />
            </span>
            <h3 class="text-lg font-extrabold m-0">{{ __('student.no_orders') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 max-w-md mx-auto leading-relaxed">{{ __('student.no_orders_desc') }}</p>
            <a href="{{ route('academic-years') }}" class="sp-promo-btn !mt-6 inline-flex !text-[var(--sp-accent-text)]">
                <x-student.figma-icon name="icon-search.svg" box="size-4" class="me-2" />
                {{ __('student.browse_courses_btn') }}
            </a>
        </div>
    @endif
</div>
@endsection
