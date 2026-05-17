<?php $__env->startSection('title', __('public.blog_page_title') . ' - ' . __('public.site_suffix')); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden" style="margin-top: 0; padding-top: 8rem; background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            المدونة
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            أحدث المقالات والأخبار
        </p>
    </div>
</section>

<!-- Blog Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">

        <!-- Featured Posts -->
        <?php if($featuredPosts->count() > 0): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">مقالات مميزة</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $featuredPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('public.blog.show', $post->slug)); ?>" class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all border border-gray-200 card-hover">
                    <?php if($post->featured_image): ?>
                    <img src="<?php echo e(asset($post->featured_image)); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-48 object-cover" loading="lazy" decoding="async" onerror="this.style.display='none'">
                    <?php else: ?>
                    <div class="w-full h-48 bg-gradient-to-br from-sky-100 to-slate-100 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400"></i>
                    </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo e($post->title); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo e(Str::limit($post->excerpt ?? strip_tags($post->content), 100)); ?></p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-user ml-2"></i>
                            <span><?php echo e($post->author->name ?? 'غير معروف'); ?></span>
                            <i class="fas fa-calendar mr-4 ml-6"></i>
                            <span><?php echo e($post->published_at->format('Y-m-d')); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- All Posts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('public.blog.show', $post->slug)); ?>" class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all border border-gray-200 card-hover">
                <?php if($post->featured_image): ?>
                <img src="<?php echo e(asset($post->featured_image)); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-48 object-cover" loading="lazy" decoding="async" onerror="this.style.display='none'">
                <?php else: ?>
                <div class="w-full h-48 bg-gradient-to-br from-sky-100 to-slate-100 flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
                <?php endif; ?>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo e($post->title); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo e(Str::limit($post->excerpt ?? strip_tags($post->content), 120)); ?></p>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-eye ml-2"></i>
                            <span><?php echo e($post->views_count); ?></span>
                        </div>
                        <span><?php echo e($post->published_at->format('Y-m-d')); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-newspaper text-gray-400 text-5xl"></i>
                </div>
                <p class="text-gray-600 text-xl">لا توجد مقالات متاحة حالياً</p>
            </div>
            <?php endif; ?>
        </div>

        <?php echo e($posts->links()); ?>

    </div>
</section>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/blog/index.blade.php ENDPATH**/ ?>