<?php $__env->startSection('title', 'الأسعار - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden" style="margin-top: 0; padding-top: 8rem; background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            الأسعار والخطط
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            اختر الخطة المناسبة لك
        </p>
    </div>
</section>

<!-- Pricing Plans -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <?php if(isset($packages) && $packages->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!-- Package Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 border-2 <?php echo e($package->is_popular ? 'border-sky-400 transform scale-105' : 'border-gray-200'); ?> card-hover relative <?php echo e($package->is_popular ? 'bg-gradient-to-br from-sky-500 to-blue-600' : ''); ?>">
                <?php if($package->is_popular): ?>
                <div class="absolute top-0 left-0 right-0 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-2 rounded-t-xl text-center">الأكثر شعبية</div>
                <?php endif; ?>
                
                <div class="text-center mb-6 <?php echo e($package->is_popular ? 'mt-4' : ''); ?>">
                    <?php if($package->thumbnail): ?>
                    <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-4 feature-icon-hover">
                        <img src="<?php echo e(asset('storage/' . $package->thumbnail)); ?>" alt="<?php echo e($package->name); ?>" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <?php else: ?>
                    <div class="w-20 h-20 <?php echo e($package->is_popular ? 'bg-white/20' : 'bg-gradient-to-br from-sky-400 to-sky-600'); ?> rounded-full flex items-center justify-center mx-auto mb-4 feature-icon-hover">
                        <?php if($package->is_featured): ?>
                            <i class="fas fa-crown <?php echo e($package->is_popular ? 'text-white' : 'text-white'); ?> text-2xl"></i>
                        <?php elseif($package->is_popular): ?>
                            <i class="fas fa-star text-white text-2xl"></i>
                        <?php else: ?>
                            <i class="fas fa-box text-white text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="text-2xl font-bold <?php echo e($package->is_popular ? 'text-white' : 'text-gray-900'); ?> mb-2"><?php echo e($package->name); ?></h3>
                    
                    <?php if($package->original_price && $package->original_price > $package->price): ?>
                    <div class="mb-2">
                        <span class="text-lg <?php echo e($package->is_popular ? 'text-blue-200' : 'text-gray-400'); ?> line-through"><?php echo e(number_format($package->original_price, 2)); ?> ج.م</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="text-5xl font-bold <?php echo e($package->is_popular ? 'text-white' : 'text-sky-600'); ?> mb-2">
                        <?php if($package->price > 0): ?>
                            <?php echo e(number_format($package->price, 2)); ?> <span class="text-2xl">ج.م</span>
                        <?php else: ?>
                            <span class="text-2xl">مجاني</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($package->description): ?>
                    <p class="<?php echo e($package->is_popular ? 'text-blue-100' : 'text-gray-600'); ?>"><?php echo e(Str::limit($package->description, 50)); ?></p>
                    <?php endif; ?>
                    
                    <?php if($package->courses_count > 0): ?>
                    <p class="text-sm <?php echo e($package->is_popular ? 'text-blue-200' : 'text-gray-500'); ?> mt-2">
                        <i class="fas fa-graduation-cap ml-1"></i>
                        <?php echo e($package->courses_count); ?> كورس
                    </p>
                    <?php endif; ?>
                </div>
                
                <!-- Features -->
                <?php if($package->features && count($package->features) > 0): ?>
                <ul class="space-y-4 mb-8">
                    <?php $__currentLoopData = $package->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center <?php echo e($package->is_popular ? 'text-white' : 'text-gray-700'); ?>">
                        <i class="fas fa-check-circle <?php echo e($package->is_popular ? 'text-yellow-300' : 'text-sky-500'); ?> ml-3"></i>
                        <?php echo e($feature); ?>

                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php else: ?>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center <?php echo e($package->is_popular ? 'text-white' : 'text-gray-700'); ?>">
                        <i class="fas fa-check-circle <?php echo e($package->is_popular ? 'text-yellow-300' : 'text-sky-500'); ?> ml-3"></i>
                        وصول لجميع الكورسات في الباقة
                    </li>
                    <?php if($package->courses_count > 0): ?>
                    <li class="flex items-center <?php echo e($package->is_popular ? 'text-white' : 'text-gray-700'); ?>">
                        <i class="fas fa-check-circle <?php echo e($package->is_popular ? 'text-yellow-300' : 'text-sky-500'); ?> ml-3"></i>
                        <?php echo e($package->courses_count); ?> كورس برمجي شامل
                    </li>
                    <?php endif; ?>
                    <li class="flex items-center <?php echo e($package->is_popular ? 'text-white' : 'text-gray-700'); ?>">
                        <i class="fas fa-check-circle <?php echo e($package->is_popular ? 'text-yellow-300' : 'text-sky-500'); ?> ml-3"></i>
                        دعم فني متواصل
                    </li>
                </ul>
                <?php endif; ?>
                
                <!-- CTA Button -->
                <?php if($package->price > 0): ?>
                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>" class="<?php echo e($package->is_popular ? 'bg-white text-sky-600 hover:bg-gray-100' : 'btn-primary'); ?> font-bold py-3 px-6 rounded-lg transition-colors w-full text-center block">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    اشتر الآن
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>" class="<?php echo e($package->is_popular ? 'bg-white text-sky-600 hover:bg-gray-100' : 'btn-primary'); ?> font-bold py-3 px-6 rounded-lg transition-colors w-full text-center block">
                    <i class="fas fa-eye ml-2"></i>
                    عرض التفاصيل
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-box text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">لا توجد باقات متاحة حالياً</h3>
                <p class="text-gray-600 mb-6">نعمل على إضافة باقات جديدة قريباً</p>
                <a href="<?php echo e(route('public.courses')); ?>" class="btn-primary inline-block">
                    <i class="fas fa-arrow-left ml-2"></i>
                    تصفح الكورسات
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/pricing.blade.php ENDPATH**/ ?>