<?php $__env->startSection('title', __('instructor.review_title') . ': ' . $project->title); ?>
<?php $__env->startSection('header', __('instructor.review_project')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            <span class="font-bold text-red-800"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <a href="<?php echo e(route('instructor.portfolio.index')); ?>" class="inline-flex items-center gap-2 text-[#2CA9BD] hover:underline font-bold">
        <i class="fas fa-arrow-right"></i>
        <?php echo e(__('instructor.back_to_projects')); ?>

    </a>

    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-lg">
        <?php if($project->image_path): ?>
            <div class="aspect-video bg-gray-100">
                <img src="<?php echo e(asset($project->image_path)); ?>" alt="<?php echo e($project->title); ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>
        <div class="p-8">
            <h1 class="text-2xl font-black text-gray-900 mb-4"><?php echo e($project->title); ?></h1>
            <?php if($project->description): ?>
                <div class="prose text-gray-600 mb-6"><?php echo nl2br(e($project->description)); ?></div>
            <?php endif; ?>
            <?php if($project->project_url): ?>
                <p class="mb-4"><a href="<?php echo e($project->project_url); ?>" target="_blank" rel="noopener" class="text-[#2CA9BD] hover:underline font-bold"><?php echo e($project->project_url); ?></a></p>
            <?php endif; ?>
            <p class="text-sm text-gray-500 mb-6"><strong><?php echo e(__('instructor.student')); ?>:</strong> <?php echo e($project->user->name ?? '—'); ?> | <strong><?php echo e(__('instructor.path_name')); ?>:</strong> <?php echo e($project->academicYear->name ?? '—'); ?></p>

            <?php if($project->status === 'pending_review'): ?>
                <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-200">
                    <form action="<?php echo e(route('instructor.portfolio.approve', $project)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1"><?php echo e(__('instructor.notes_optional')); ?></label>
                            <textarea name="instructor_notes" rows="2" class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-green-700"><?php echo e(__('instructor.approve')); ?></button>
                    </form>
                    <form action="<?php echo e(route('instructor.portfolio.reject', $project)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1"><?php echo e(__('instructor.rejection_reason_optional')); ?></label>
                            <input type="text" name="rejected_reason" class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 text-sm" placeholder="<?php echo e(__('instructor.reject_reason_placeholder')); ?>">
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 bg-red-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-red-700"><?php echo e(__('instructor.reject')); ?></button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if($project->status === 'approved'): ?>
                <form action="<?php echo e(route('instructor.portfolio.publish', $project)); ?>" method="POST" class="inline mt-4">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#2CA9BD] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#1F3A56]"><?php echo e(__('instructor.publish_to_portfolio_btn')); ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/portfolio/show.blade.php ENDPATH**/ ?>