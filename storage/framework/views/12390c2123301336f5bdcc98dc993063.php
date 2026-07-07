<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'course' => null,
    'originalPrice' => null,
    'effectivePrice' => null,
    'isFree' => false,
    'size' => 'md',
    'currency' => 'ج.م',
    'showCurrency' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'course' => null,
    'originalPrice' => null,
    'effectivePrice' => null,
    'isFree' => false,
    'size' => 'md',
    'currency' => 'ج.م',
    'showCurrency' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<?php if($free || ($effective <= 0 && $original <= 0)): ?>
    <span <?php echo e($attributes->merge(['class' => $sizeClasses['wrapper'] . ' font-black text-green-600 flex items-center gap-1.5'])); ?>>
        <?php if($size !== 'lg'): ?>
            <span class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-md">
                <i class="fas fa-gift text-white text-[8px]"></i>
            </span>
        <?php else: ?>
            <i class="fas fa-gift text-2xl"></i>
        <?php endif; ?>
        <span><?php echo e(__('public.free_price')); ?></span>
    </span>
<?php elseif($hasDiscount): ?>
    <span <?php echo e($attributes->merge(['class' => 'inline-flex flex-col items-start gap-0.5'])); ?>>
        <span class="<?php echo e($sizeClasses['old']); ?> text-gray-400 line-through font-semibold">
            <?php echo e(number_format($original, 0)); ?>

            <?php if($showCurrency): ?>
                <span class="font-normal"><?php echo e($currency); ?></span>
            <?php endif; ?>
        </span>
        <span class="<?php echo e($sizeClasses['wrapper']); ?> font-black text-blue-600 flex items-center gap-1">
            <span><?php echo e(number_format($effective, 0)); ?></span>
            <?php if($showCurrency): ?>
                <span class="<?php echo e($sizeClasses['currency']); ?> text-gray-500 font-normal"><?php echo e($currency); ?></span>
            <?php endif; ?>
        </span>
    </span>
<?php else: ?>
    <span <?php echo e($attributes->merge(['class' => $sizeClasses['wrapper'] . ' font-black text-blue-600 flex items-center gap-1'])); ?>>
        <span><?php echo e(number_format($effective, 0)); ?></span>
        <?php if($showCurrency): ?>
            <span class="<?php echo e($sizeClasses['currency']); ?> text-gray-500 font-normal"><?php echo e($currency); ?></span>
        <?php endif; ?>
    </span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/components/course-price-display.blade.php ENDPATH**/ ?>