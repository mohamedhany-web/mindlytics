<?php $__env->startSection('title', __('public.media_page_title') . ' - ' . __('public.site_suffix')); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            معرض الصور والفيديوهات
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            اكتشف أجمل اللحظات والأحداث
        </p>
    </div>
</section>

<!-- Media Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border border-gray-200 card-hover">
                <i class="fas fa-image text-4xl text-blue-600 mb-2"></i>
                <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['images'] ?? 0); ?></div>
                <div class="text-gray-600">صورة</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border border-gray-200 card-hover">
                <i class="fas fa-video text-4xl text-red-600 mb-2"></i>
                <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['videos'] ?? 0); ?></div>
                <div class="text-gray-600">فيديو</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border border-gray-200 card-hover">
                <i class="fas fa-file text-4xl text-green-600 mb-2"></i>
                <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['documents'] ?? 0); ?></div>
                <div class="text-gray-600">مستند</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200 card-hover">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value="">الكل</option>
                        <option value="image" <?php echo e(request('type') == 'image' ? 'selected' : ''); ?>>صور</option>
                        <option value="video" <?php echo e(request('type') == 'video' ? 'selected' : ''); ?>>فيديوهات</option>
                        <option value="document" <?php echo e(request('type') == 'document' ? 'selected' : ''); ?>>مستندات</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category); ?>" <?php echo e(request('category') == $category ? 'selected' : ''); ?>><?php echo e($category); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="ابحث..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <i class="fas fa-search ml-2"></i>بحث
                    </button>
                </div>
            </form>
        </div>

        <!-- Media Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('public.media.show', $item)); ?>" class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all border border-gray-200 card-hover">
                <?php if($item->type == 'image'): ?>
                <img src="<?php echo e(asset('storage/' . $item->file_path)); ?>" alt="<?php echo e($item->title); ?>" class="w-full h-48 object-cover" loading="lazy" decoding="async">
                <?php elseif($item->type == 'video'): ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center relative">
                    <?php if($item->thumbnail_path): ?>
                    <img src="<?php echo e(asset('storage/' . $item->thumbnail_path)); ?>" alt="<?php echo e($item->title); ?>" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    <?php endif; ?>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-play-circle text-6xl text-white opacity-80"></i>
                    </div>
                </div>
                <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-file-alt text-6xl text-gray-400"></i>
                </div>
                <?php endif; ?>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 mb-2"><?php echo e($item->title); ?></h3>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-eye ml-2"></i>
                        <span><?php echo e($item->views_count); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-image text-gray-400 text-5xl"></i>
                </div>
                <p class="text-gray-600 text-xl">لا توجد ملفات متاحة حالياً</p>
            </div>
            <?php endif; ?>
        </div>

        <?php echo e($media->links()); ?>

    </div>
</section>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/media/index.blade.php ENDPATH**/ ?>