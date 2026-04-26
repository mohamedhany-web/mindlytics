<?php
    $locale = app()->getLocale();
    $currentUrl = request()->fullUrlWithQuery([]);
    $urlAr = request()->fullUrlWithQuery(['lang' => 'ar']);
    $urlEn = request()->fullUrlWithQuery(['lang' => 'en']);
?>
<div class="inline-flex items-center gap-1 <?php echo e($attributes->get('class')); ?>" dir="ltr">
    <a href="<?php echo e($urlAr); ?>" class="px-2 py-1 rounded text-sm font-medium <?php echo e($locale === 'ar' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'); ?>"><?php echo e(__('landing.language_switcher.ar')); ?></a>
    <span class="text-gray-400">|</span>
    <a href="<?php echo e($urlEn); ?>" class="px-2 py-1 rounded text-sm font-medium <?php echo e($locale === 'en' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'); ?>"><?php echo e(__('landing.language_switcher.en')); ?></a>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/components/language-switcher.blade.php ENDPATH**/ ?>