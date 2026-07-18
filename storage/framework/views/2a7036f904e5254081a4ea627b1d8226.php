

<?php $__env->startSection('title', $achievement->achievement->name ?? __('student.achievement_details')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .ach-hero {
        display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
    }
    .ach-hero .ico {
        width: 72px; height: 72px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 210, 63, 0.22); color: var(--ml-yellow-ink);
        font-size: 2rem; flex-shrink: 0;
    }
    .ach-hero h2 { margin: 0 0 4px; font-size: 1.25rem; font-weight: 700; line-height: 1.3; }
    .ach-hero .meta { margin: 0; font-size: 13px; color: var(--ml-muted); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.achievements_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route('student.achievements.index')); ?>"><?php echo e(__('student.achievements_title')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.achievement_details')); ?></span>
            </nav>
            <h1><?php echo e(__('student.achievement_details')); ?></h1>
            <p class="sub"><?php echo e($achievement->achievement->name ?? __('student.achievement_default')); ?></p>
        </div>
    </header>

    <section class="oc-panel">
        <div class="ach-hero">
            <div class="ico" aria-hidden="true">
                <?php if($achievement->achievement && $achievement->achievement->icon): ?>
                    <i class="<?php echo e($achievement->achievement->icon); ?>"></i>
                <?php else: ?>
                    <i class="fas fa-trophy"></i>
                <?php endif; ?>
            </div>
            <div class="min-w-0">
                <h2><?php echo e($achievement->achievement->name ?? __('student.achievement_default')); ?></h2>
                <p class="meta"><?php echo e($achievement->achievement->category ?? $achievement->achievement->type ?? '—'); ?></p>
            </div>
        </div>

        <?php if($achievement->achievement && $achievement->achievement->description): ?>
            <p class="oc-label" style="margin-top:18px"><?php echo e(__('student.assignment_description')); ?></p>
            <p style="margin:0;font-size:14px;line-height:1.7;color:var(--ml-ink)"><?php echo e($achievement->achievement->description); ?></p>
        <?php endif; ?>

        <ul class="oc-facts" style="margin-top:16px">
            <li>
                <span class="k"><?php echo e(__('student.earned_at_label')); ?></span>
                <span class="v"><?php echo e($achievement->earned_at ? $achievement->earned_at->format('Y-m-d') : '—'); ?></span>
            </li>
            <?php if($achievement->points_earned): ?>
                <li>
                    <span class="k"><?php echo e(__('student.points_earned_label')); ?></span>
                    <span class="v"><?php echo e($achievement->points_earned); ?> <?php echo e(__('student.points_earned')); ?></span>
                </li>
            <?php endif; ?>
        </ul>

        <div class="oc-nav" style="margin-top:16px">
            <a href="<?php echo e(route('student.achievements.index')); ?>" class="oc-btn oc-btn-quiet">
                <?php echo e(__('student.achievement_back')); ?>

            </a>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\achievements\show.blade.php ENDPATH**/ ?>