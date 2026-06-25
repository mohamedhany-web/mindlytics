<?php $__env->startSection('title', $project->title . ' - Mindlytics Portfolio'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-8 md:py-12 bg-gradient-to-b from-slate-50 to-white" style="padding-top: 6rem;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="<?php echo e(route('public.portfolio.index')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-gray-900 font-medium mb-8 transition-colors">
            <i class="fas fa-arrow-right"></i>
            <?php echo e(__('public.back_to_gallery')); ?>

        </a>

        <article class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <?php if($project->image_path): ?>
                <div class="aspect-video bg-gray-100">
                    <img src="<?php echo e(asset($project->image_path)); ?>" alt="<?php echo e($project->title); ?>" class="w-full h-full object-cover" loading="lazy">
                </div>
            <?php else: ?>
                <div class="aspect-video bg-gradient-to-br from-blue-500/20 to-green-500/20 flex items-center justify-center">
                    <i class="fas fa-code text-6xl text-blue-500/50"></i>
                </div>
            <?php endif; ?>
            <div class="p-8 md:p-10">
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php if($project->academicYear): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-sm font-bold bg-blue-600/10 text-gray-900">
                            <i class="fas fa-route text-blue-600"></i>
                            <?php echo e($project->academicYear->name); ?>

                        </span>
                    <?php endif; ?>
                    <?php if($project->advancedCourse): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-sm font-medium bg-gray-100 text-gray-700">
                            <i class="fas fa-book text-gray-500"></i>
                            <?php echo e($project->advancedCourse->title); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-4"><?php echo e($project->title); ?></h1>
                <?php if($project->description): ?>
                    <div class="prose prose-lg text-gray-600 mb-6 max-w-none">
                        <?php echo nl2br(e($project->description)); ?>

                    </div>
                <?php endif; ?>
                <?php if($project->project_url): ?>
                    <a href="<?php echo e($project->project_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-external-link-alt"></i>
                        <?php echo e(__('public.view_project')); ?>

                    </a>
                <?php endif; ?>

                <!-- الطالب -->
                <div class="mt-8 pt-8 border-t border-gray-200 flex items-center gap-4">
                    <?php if($project->user->profile_image): ?>
                        <img src="<?php echo e($project->user->profile_image_url); ?>" alt="" class="w-14 h-14 rounded-full object-cover border-2 border-blue-600/20">
                    <?php else: ?>
                        <span class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-600 to-green-500 text-white flex items-center justify-center text-xl font-black"><?php echo e(mb_substr($project->user->name ?? 'ط', 0, 1)); ?></span>
                    <?php endif; ?>
                    <div>
                        <p class="font-bold text-gray-900"><?php echo e($project->user->name ?? __('public.student_fallback')); ?></p>
                        <p class="text-sm text-gray-500"><?php echo e(__('public.project_from_portfolio')); ?></p>
                    </div>
                </div>
            </div>
        </article>

        <?php if($related->count() > 0): ?>
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6"><?php echo e(__('public.other_projects_same_path')); ?></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('public.portfolio.show', $r->id)); ?>" class="flex gap-4 bg-white rounded-xl shadow border border-gray-200 p-4 hover:shadow-lg hover:border-blue-500/30 transition-all">
                            <?php if($r->image_path): ?>
                                <img src="<?php echo e(asset($r->image_path)); ?>" alt="<?php echo e($r->title); ?>" class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                            <?php else: ?>
                                <div class="w-24 h-24 rounded-lg bg-blue-600/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-code text-2xl text-blue-600"></i>
                                </div>
                            <?php endif; ?>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-gray-900 truncate"><?php echo e($r->title); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($r->user->name ?? __('public.student_fallback')); ?></p>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/portfolio/show.blade.php ENDPATH**/ ?>