<?php $__env->startSection('title', __('student.achievements_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .ach-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: 12px;
    }
    .ach-card {
        display: flex; flex-direction: column; align-items: center; text-align: center;
        gap: 10px; padding: 20px 16px;
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    a.ach-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .ach-card .ico {
        width: 64px; height: 64px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 210, 63, 0.22); color: var(--ml-yellow-ink);
        font-size: 1.75rem;
    }
    .ach-card h3 {
        margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35;
    }
    .ach-card .desc {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.55;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        max-width: 36ch;
    }
    .ach-pts {
        display: inline-flex; align-items: center; min-height: 28px; padding: 0 10px;
        border-radius: 8px; font-size: 12px; font-weight: 700;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.achievements_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.achievements_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.achievements_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.achievements_subtitle')); ?></p>
        </div>
        <?php if(isset($stats)): ?>
            <div class="oc-signals">
                <span class="oc-signal oc-signal-hot"><?php echo e(__('student.total_points')); ?>: <?php echo e($stats['total_points'] ?? 0); ?></span>
                <?php if(isset($achievements)): ?>
                    <span class="oc-signal oc-signal-live"><?php echo e($achievements->total()); ?> <?php echo e(__('student.achievements_count_label')); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if(isset($stats)): ?>
        <div class="oc-pulse" aria-label="<?php echo e(__('student.achievements_title')); ?>">
            <div>
                <span class="lbl"><?php echo e(__('student.total_points')); ?></span>
                <span class="val hot"><?php echo e($stats['total_points'] ?? 0); ?></span>
            </div>
            <?php if(isset($achievements)): ?>
                <div>
                    <span class="lbl"><?php echo e(__('student.achievements_count_label')); ?></span>
                    <span class="val teal"><?php echo e($achievements->total()); ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($achievements) && $achievements->count() > 0): ?>
        <p class="oc-section-title"><?php echo e(__('student.achievements_count_label')); ?></p>
        <div class="ach-grid">
            <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $name = $achievement->achievement->name ?? __('student.achievement_default');
                    $desc = $achievement->achievement->description ?? '';
                    $icon = $achievement->achievement->icon ?? null;
                    $hasShow = Route::has('student.achievements.show');
                ?>
                <?php if($hasShow): ?>
                    <a href="<?php echo e(route('student.achievements.show', $achievement)); ?>" class="ach-card">
                <?php else: ?>
                    <div class="ach-card">
                <?php endif; ?>
                    <div class="ico" aria-hidden="true">
                        <?php if($icon): ?>
                            <i class="<?php echo e($icon); ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-trophy"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo e($name); ?></h3>
                    <?php if($desc): ?>
                        <p class="desc"><?php echo e($desc); ?></p>
                    <?php endif; ?>
                    <?php if($achievement->points_earned): ?>
                        <span class="ach-pts">+<?php echo e($achievement->points_earned); ?> <?php echo e(__('student.points_earned')); ?></span>
                    <?php endif; ?>
                <?php if($hasShow): ?>
                    </a>
                <?php else: ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($achievements->hasPages()): ?>
            <div style="margin-top:20px;display:flex;justify-content:center">
                <?php echo e($achievements->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-trophy"></i></div>
            <h3><?php echo e(__('student.no_achievements')); ?></h3>
            <p><?php echo e(__('student.no_achievements_desc')); ?></p>
            <div style="margin-top:16px">
                <a href="<?php echo e(route('my-courses.index')); ?>" class="oc-btn">
                    <i class="fas fa-book-open text-xs"></i>
                    <?php echo e(__('student.view_my_courses')); ?>

                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\achievements\index.blade.php ENDPATH**/ ?>