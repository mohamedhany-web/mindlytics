@props([
    'course' => null,
    'originalPrice' => null,
    'effectivePrice' => null,
    'isFree' => false,
    'size' => 'md',
    'currency' => 'ج.م',
    'showCurrency' => true,
])

@php
    $isModel = is_object($course);
    $free = $isFree || ($isModel ? (bool) ($course->is_free ?? false) : false);
    $original = $originalPrice ?? ($isModel ? $course->originalPrice() : 0);
    $effective = $effectivePrice ?? ($isModel ? $course->effectivePrice() : 0);
    $hasDiscount = $isModel
        ? $course->hasCourseDiscount()
        : ($original > $effective && $effective > 0);

    $sizeClasses = match ($size) {
        'lg' => ['wrapper' => 'text-4xl', 'old' => 'text-lg', 'currency' => 'text-sm'],
        'sm' => ['wrapper' => 'text-base', 'old' => 'text-xs', 'currency' => 'text-[10px]'],
        default => ['wrapper' => 'text-lg', 'old' => 'text-sm', 'currency' => 'text-[10px]'],
    };
@endphp

@if($free || ($effective <= 0 && $original <= 0))
    <span {{ $attributes->merge(['class' => $sizeClasses['wrapper'] . ' font-black text-green-600 flex items-center gap-1.5']) }}>
        @if($size !== 'lg')
            <span class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-md">
                <i class="fas fa-gift text-white text-[8px]"></i>
            </span>
        @else
            <i class="fas fa-gift text-2xl"></i>
        @endif
        <span>{{ __('public.free_price') }}</span>
    </span>
@elseif($hasDiscount)
    <span {{ $attributes->merge(['class' => 'inline-flex flex-col items-start gap-0.5']) }}>
        <span class="{{ $sizeClasses['old'] }} text-gray-400 line-through font-semibold">
            {{ number_format($original, 0) }}
            @if($showCurrency)
                <span class="font-normal">{{ $currency }}</span>
            @endif
        </span>
        <span class="{{ $sizeClasses['wrapper'] }} font-black text-blue-600 flex items-center gap-1">
            <span>{{ number_format($effective, 0) }}</span>
            @if($showCurrency)
                <span class="{{ $sizeClasses['currency'] }} text-gray-500 font-normal">{{ $currency }}</span>
            @endif
        </span>
    </span>
@else
    <span {{ $attributes->merge(['class' => $sizeClasses['wrapper'] . ' font-black text-blue-600 flex items-center gap-1']) }}>
        <span>{{ number_format($effective, 0) }}</span>
        @if($showCurrency)
            <span class="{{ $sizeClasses['currency'] }} text-gray-500 font-normal">{{ $currency }}</span>
        @endif
    </span>
@endif
