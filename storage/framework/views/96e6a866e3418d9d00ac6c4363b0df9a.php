
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'url' => '',
    'title' => 'مقدمة',
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
    'url' => '',
    'title' => 'مقدمة',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $resolved = \App\Support\IntroVideoResolver::resolve($url);
?>
<div <?php echo e($attributes->merge(['class' => 'custom-video-player-wrapper'])); ?>>
<?php if(in_array($resolved['type'], ['youtube', 'vimeo', 'bunny_embed'], true) && !empty($resolved['embed'])): ?>
    <div class="intro-video-container">
        <iframe src="<?php echo e($resolved['embed']); ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                allowfullscreen
                loading="lazy"
                title="<?php echo e($title); ?>"></iframe>
    </div>
<?php elseif($resolved['type'] === 'html5' && !empty($resolved['direct'])): ?>
    <div class="intro-video-container" style="padding-bottom: 0; height: auto; min-height: 320px;">
        <video class="w-full rounded-lg" style="max-height: 70vh;" playsinline controls preload="metadata">
            <source src="<?php echo e($resolved['direct']); ?>" type="<?php echo e($resolved['mime'] ?: 'video/mp4'); ?>">
            <?php echo e(__('public.browser_no_video') ?? 'المتصفح لا يدعم تشغيل الفيديو.'); ?>

        </video>
    </div>
<?php else: ?>
    <div class="bg-gray-100 rounded-lg p-6 text-center">
        <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
        <p class="text-gray-700 text-sm font-medium"><?php echo e(__('public.intro_video_unsupported')); ?></p>
    </div>
<?php endif; ?>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/partials/intro-video-embed.blade.php ENDPATH**/ ?>