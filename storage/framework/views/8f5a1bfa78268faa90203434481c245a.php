<?php $__env->startSection('title', 'Mindlytics Portfolio - معرض أعمال الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-8 md:py-12 bg-gradient-to-b from-slate-50 to-white" style="padding-top: 6rem;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                Mindlytics <span class="bg-gradient-to-r from-blue-600 to-green-500 bg-clip-text text-transparent">Portfolio</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                معرض أعمال طلابنا — مشاريع حقيقية بعد إتمام الكورسات، حيث ترى الشركات الموهبة
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- التصنيفات = المسارات التعليمية -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-route text-blue-600"></i>
                        المسارات التعليمية
                    </h2>
                    <ul class="space-y-1">
                        <li>
                            <a href="<?php echo e(route('public.portfolio.index')); ?>" class="block px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo e(!$categoryId ? 'bg-blue-600/10 text-blue-900 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>">
                                <i class="fas fa-th-large ml-2 text-blue-600"></i>
                                الكل
                            </a>
                        </li>
                        <?php $__currentLoopData = $learningPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('public.portfolio.index', ['path' => $path->id])); ?>" class="block px-4 py-3 rounded-xl text-sm font-medium transition-all <?php echo e($categoryId == $path->id ? 'bg-blue-600/10 text-blue-900 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>">
                                    <i class="fas fa-folder ml-2 text-gray-400"></i>
                                    <?php echo e($path->name); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </aside>

            <!-- المشاريع -->
            <div class="flex-1 min-w-0">
                <?php if($projects->count() > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('public.portfolio.show', $project->id)); ?>" class="group block bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl hover:border-blue-500/30 transition-all duration-300">
                                <?php if($project->image_path): ?>
                                    <div class="aspect-video bg-gray-100 overflow-hidden">
                                        <img src="<?php echo e(asset($project->image_path)); ?>" alt="<?php echo e($project->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                    </div>
                                <?php else: ?>
                                    <div class="aspect-video bg-gradient-to-br from-blue-500/20 to-green-500/20 flex items-center justify-center">
                                        <i class="fas fa-code text-4xl text-blue-500/60"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2"><?php echo e($project->title); ?></h3>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?php echo e(Str::limit(strip_tags($project->description ?? ''), 80)); ?></p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <?php if($project->user->profile_image): ?>
                                                <img src="<?php echo e($project->user->profile_image_url); ?>" alt="" class="w-8 h-8 rounded-full object-cover">
                                            <?php else: ?>
                                                <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold"><?php echo e(mb_substr($project->user->name ?? 'ط', 0, 1)); ?></span>
                                            <?php endif; ?>
                                            <span class="text-sm font-medium text-gray-700"><?php echo e($project->user->name ?? 'طالب'); ?></span>
                                        </div>
                                        <?php if($project->academicYear): ?>
                                            <span class="text-xs font-medium text-blue-600 bg-blue-600/10 px-2.5 py-1 rounded-lg"><?php echo e($project->academicYear->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-8">
                        <?php echo e($projects->withQueryString()->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500/20 to-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-4xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد مشاريع منشورة بعد</h3>
                        <p class="text-gray-600 mb-6">ستظهر هنا مشاريع طلابنا بعد مراجعتها ونشرها من المدربين</p>
                        <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                            <i class="fas fa-book"></i>
                            استعرض الكورسات
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/portfolio/index.blade.php ENDPATH**/ ?>