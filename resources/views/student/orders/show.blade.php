@extends('layouts.student-dashboard')

@php
    use Illuminate\Support\Str;

    $statusPill = match ($order->status) {
        'pending' => 'sp-pill--upcoming',
        'approved' => 'sp-pill--done',
        'rejected' => 'sp-pill',
        default => '',
    };
    $statusLabel = match ($order->status) {
        'pending' => __('student.order_status_pending'),
        'approved' => __('student.order_status_approved'),
        'rejected' => __('student.order_status_rejected'),
        default => __('student.order_status_unknown'),
    };
    $statusHint = match ($order->status) {
        'pending' => __('student.order_status_pending_hint'),
        'approved' => __('student.order_status_approved_hint'),
        'rejected' => __('student.order_status_rejected_hint'),
        default => '',
    };
    $paymentLabel = match ($order->payment_method) {
        'bank_transfer' => __('student.bank_transfer'),
        'cash' => __('student.cash_label'),
        default => __('student.other_label'),
    };

    $itemTitle = $order->academic_year_id && $order->learningPath
        ? ($order->learningPath->name ?? __('student.learning_path_label'))
        : ($order->course?->title ?? Str::before($order->notes ?? __('student.order_untitled'), "\n") ?: __('student.order_untitled'));

    $imageUrl = null;
    $imageExists = false;
    if ($order->payment_proof) {
        $fullPath = storage_path('app/public/' . $order->payment_proof);
        $imageExists = file_exists($fullPath);
        if ($imageExists) {
            $imageUrl = asset('storage/' . $order->payment_proof);
            if (! file_exists(public_path('storage/' . $order->payment_proof))) {
                try {
                    $imageUrl = route('storage.file', ['path' => $order->payment_proof]);
                } catch (\Throwable $e) {
                    $imageUrl = url('/storage/' . $order->payment_proof);
                }
            }
        }
    }
@endphp

@section('title', __('student.order_details_title') . ' #' . $order->id)
@section('header', __('student.order_details_title'))

@push('styles')
<style>
    .sp-order-show-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-order-show-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -30px;
        top: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.25), transparent 70%);
        pointer-events: none;
    }
    .sp-order-show-sticky { position: sticky; top: 12px; }
    @media (max-width: 1023px) { .sp-order-show-sticky { position: static; } }
    .sp-receipt-img { cursor: zoom-in; transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .sp-receipt-img:hover { transform: scale(1.01); box-shadow: 0 10px 24px rgba(0,0,0,0.12); }
</style>
@endpush

@section('content')
<div class="space-y-5">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('orders.index') }}" class="sp-link">{{ __('student.orders_page_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.order_number_label', ['id' => $order->id]) }}</span>
    </nav>

    {{-- Hero --}}
    <section class="sp-order-show-hero">
        <div class="relative z-[1] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.order_details_title') }}</p>
                <h2 class="text-xl sm:text-2xl font-extrabold m-0 leading-tight">{{ $itemTitle }}</h2>
                <p class="text-sm text-white/65 m-0 mt-2 flex flex-wrap items-center gap-2">
                    <span>{{ __('student.order_number_label', ['id' => $order->id]) }}</span>
                    <span class="opacity-40">·</span>
                    <span>{{ $order->created_at->format('Y/m/d H:i') }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="sp-pill {{ $statusPill }} !text-sm">{{ $statusLabel }}</span>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-4 py-2.5 text-sm font-extrabold text-white transition">
                    {{ __('student.back_to_orders') }}
                </a>
            </div>
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,1fr)]">
        {{-- Main --}}
        <div class="space-y-5 min-w-0">
            {{-- Course / path --}}
            <section class="sp-card overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky)">
                        <x-student.figma-icon name="{{ $order->academic_year_id ? 'icon-path.svg' : 'icon-courses.svg' }}" />
                    </span>
                    <h3 class="font-extrabold text-base m-0">
                        {{ $order->academic_year_id ? __('student.learning_path_info') : __('student.course_info') }}
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-24 h-24 rounded-[20px] overflow-hidden shrink-0 flex items-center justify-center" style="background:var(--sp-lilac)">
                            @if($order->academic_year_id && $order->learningPath?->thumbnail)
                                <img src="{{ asset('storage/' . $order->learningPath->thumbnail) }}" alt="" class="w-full h-full object-cover">
                            @elseif($order->course?->thumbnail)
                                <img src="{{ asset('storage/' . $order->course->thumbnail) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <x-student.figma-icon name="{{ $order->academic_year_id ? 'icon-path.svg' : 'icon-courses.svg' }}" box="size-10" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <h4 class="font-extrabold text-lg m-0">{{ $itemTitle }}</h4>
                                @if($order->academic_year_id && $order->learningPath)
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span class="sp-pill sp-pill--progress">{{ __('student.learning_path_label') }}</span>
                                        @if($order->learningPath->price)
                                            <span class="sp-pill">{{ number_format($order->learningPath->price, 2) }} {{ __('public.currency_egp') }}</span>
                                        @endif
                                    </div>
                                    @if($order->learningPath->description)
                                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ Str::limit($order->learningPath->description, 160) }}</p>
                                    @endif
                                    @if($order->learningPath?->name)
                                        <a href="{{ route('public.learning-path.show', Str::slug($order->learningPath->name)) }}" class="sp-link text-sm font-extrabold inline-block mt-2">
                                            {{ __('student.order_view_path') }}
                                        </a>
                                    @endif
                                @elseif($order->course)
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if($order->course->academicYear)
                                            <span class="sp-pill sp-pill--progress">{{ $order->course->academicYear->name }}</span>
                                        @endif
                                        @if($order->course->academicSubject)
                                            <span class="sp-pill">{{ $order->course->academicSubject->name }}</span>
                                        @endif
                                    </div>
                                    @if($order->course->description)
                                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ Str::limit($order->course->description, 160) }}</p>
                                    @endif
                                    <a href="{{ route('courses.show', $order->course) }}" class="sp-link text-sm font-extrabold inline-block mt-2">
                                        {{ __('student.order_view_course') }}
                                    </a>
                                @else
                                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ __('student.order_offline_booking_hint') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Payment --}}
            <section class="sp-card overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                        <x-student.figma-icon name="icon-wallet.svg" />
                    </span>
                    <h3 class="font-extrabold text-base m-0">{{ __('student.order_payment_details') }}</h3>
                </div>
                <div class="p-5 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-[16px] p-4" style="background:var(--sp-mint)">
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.amount_label') }}</p>
                            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ number_format($order->amount, 2) }} <span class="text-base font-extrabold">{{ __('public.currency_egp') }}</span></p>
                            @if($order->discount_amount > 0)
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">
                                    {{ __('student.order_discount_saved', ['amount' => number_format($order->discount_amount, 2)]) }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.payment_method_label') }}</p>
                            <p class="text-base font-extrabold m-0 mt-2">
                                <span class="sp-pill sp-pill--progress">{{ $paymentLabel }}</span>
                            </p>
                        </div>
                        <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.order_date_label') }}</p>
                            <p class="text-base font-extrabold m-0 mt-2">{{ $order->created_at->format('Y/m/d') }} <span class="text-sm font-bold text-[var(--sp-muted)]">{{ $order->created_at->format('H:i') }}</span></p>
                        </div>
                        @if($order->approved_at)
                            <div class="rounded-[16px] bg-[#f7f7f5] p-4">
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.approved_date_label') }}</p>
                                <p class="text-base font-extrabold m-0 mt-2">{{ $order->approved_at->format('Y/m/d') }} <span class="text-sm font-bold text-[var(--sp-muted)]">{{ $order->approved_at->format('H:i') }}</span></p>
                            </div>
                        @endif
                    </div>

                    @if($order->notes)
                        <div class="rounded-[16px] bg-[#fafaf8] border border-black/5 px-4 py-3">
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.your_notes') }}</p>
                            <p class="text-sm m-0 whitespace-pre-line">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Receipt --}}
            @if($order->payment_proof)
                <section class="sp-card overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                            <x-student.figma-icon name="icon-wallet.svg" />
                        </span>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.order_payment_receipt') }}</h3>
                    </div>
                    <div class="p-5 sm:p-6 text-center">
                        @if($imageExists && $imageUrl)
                            <div class="inline-block p-2 rounded-[20px] bg-[#f7f7f5] border border-black/5 max-w-full">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ __('student.order_receipt_alt') }}"
                                     class="sp-receipt-img max-w-full h-auto rounded-[16px]"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"
                                     onclick="openImageModal(this.src)">
                                <div class="hidden p-4 rounded-[16px] bg-[#f9f0d7] text-sm font-bold text-[#7a3b2e]">
                                    {{ __('student.order_receipt_unavailable') }}
                                </div>
                            </div>
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-3">{{ __('student.order_receipt_click_hint') }}</p>
                        @else
                            <div class="p-4 rounded-[16px] bg-[#f9f0d7] text-sm font-bold text-[#7a3b2e]">
                                {{ __('student.order_receipt_missing') }}
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-4 min-w-0 sp-order-show-sticky">
            <section class="sp-card p-5 text-center">
                <span class="sp-icon-bubble mx-auto mb-3 !w-16 !h-16" style="background:{{ $order->status === 'approved' ? 'var(--sp-mint)' : ($order->status === 'pending' ? 'var(--sp-amber-soft)' : 'var(--sp-peach)') }}">
                    <x-student.figma-icon name="{{ $order->status === 'approved' ? 'icon-star.svg' : ($order->status === 'pending' ? 'icon-calendar.svg' : 'icon-settings.svg') }}" box="size-7" />
                </span>
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.order_status_title') }}</p>
                <p class="text-xl font-extrabold m-0 mt-1">{{ $statusLabel }}</p>
                @if($statusHint)
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 leading-relaxed">{{ $statusHint }}</p>
                @endif

                @if($order->status === 'approved' && $order->course)
                    <a href="{{ route('courses.show', $order->course) }}" class="sp-promo-btn !mt-4 w-full !text-[var(--sp-accent-text)]">
                        {{ __('student.enter_course') }}
                    </a>
                @elseif($order->status === 'rejected' && $order->course)
                    <a href="{{ route('courses.show', $order->course) }}" class="sp-promo-btn !mt-4 w-full !text-[var(--sp-accent-text)]">
                        {{ __('student.order_submit_new') }}
                    </a>
                @endif
            </section>

            <section class="sp-card p-5 space-y-2">
                <h3 class="sp-section-title mb-3">{{ __('student.order_summary') }}</h3>
                <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                    <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.order_id_label') }}</span>
                    <span class="text-sm font-extrabold">#{{ $order->id }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                    <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.amount_label') }}</span>
                    <span class="text-sm font-extrabold">{{ number_format($order->amount, 2) }} {{ __('public.currency_egp') }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                    <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.payment_method_label') }}</span>
                    <span class="text-sm font-extrabold">{{ $paymentLabel }}</span>
                </div>
            </section>

            @if($order->approver)
                <section class="sp-card p-5">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide mb-3">{{ __('student.order_reviewed_by') }}</p>
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-[14px] flex items-center justify-center font-extrabold text-[var(--sp-accent-text)] shrink-0" style="background:var(--sp-accent)">
                            {{ mb_substr($order->approver->name ?? '?', 0, 1) }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-extrabold text-sm m-0 truncate">{{ $order->approver->name ?? __('student.order_unknown_reviewer') }}</p>
                            @if($order->approved_at)
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-0.5">{{ $order->approved_at->format('Y/m/d H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="closeImageModal()">
    <div class="max-w-5xl max-h-[90vh] relative" onclick="event.stopPropagation()">
        <button type="button" onclick="closeImageModal()" class="absolute -top-10 end-0 text-white hover:text-white/70 text-sm font-extrabold">
            {{ __('common.close') }}
        </button>
        <img id="modalImage" src="" alt="{{ __('student.order_receipt_alt') }}" class="max-w-full max-h-[90vh] object-contain rounded-[20px] shadow-2xl mx-auto">
    </div>
</div>
@endsection

@push('scripts')
<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    const modal = document.getElementById('imageModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImageModal();
});
</script>
@endpush
