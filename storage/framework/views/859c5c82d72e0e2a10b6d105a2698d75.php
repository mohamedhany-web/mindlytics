<?php $__env->startSection('title', $project->title . ' - البورتفوليو'); ?>
<?php $__env->startSection('header', 'عرض المشروع'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <a href="<?php echo e(route('admin.portfolio.index')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:underline font-bold">
        <i class="fas fa-arrow-right"></i>
        العودة للقائمة
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
                <p class="mb-4"><a href="<?php echo e($project->project_url); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:underline font-bold"><?php echo e($project->project_url); ?></a></p>
            <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <p><strong>الطالب:</strong> <?php echo e($project->user->name ?? '—'); ?></p>
                <p><strong>المسار:</strong> <?php echo e($project->academicYear->name ?? '—'); ?></p>
                <p><strong>الحالة:</strong> <?php echo e($project->status); ?></p>
                <p><strong>ظاهر في المعرض:</strong> <?php echo e($project->is_visible ? 'نعم' : 'لا'); ?></p>
                <?php if($project->reviewer): ?>
                    <p><strong>راجع من:</strong> <?php echo e($project->reviewer->name); ?></p>
                <?php endif; ?>
            </div>

            <form action="<?php echo e(route('admin.portfolio.toggle-visibility', $project)); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-2 <?php echo e($project->is_visible ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-6 py-2.5 rounded-xl font-bold">
                    <?php echo e($project->is_visible ? 'إخفاء من المعرض' : 'إظهار في المعرض'); ?>

                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\portfolio\show.blade.php ENDPATH**/ ?>