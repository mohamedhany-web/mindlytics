<?php $__env->startSection('title', __('student.my_projects_title')); ?>

<?php
    $pendingCount = $pendingTotal ?? $projects->getCollection()->where('status', 'pending_review')->count();
    $statusMap = [
        'pending_review' => ['label' => __('student.portfolio_status_pending'), 'class' => 'oc-badge-warn'],
        'approved' => ['label' => __('student.portfolio_status_approved'), 'class' => 'oc-badge-live'],
        'rejected' => ['label' => __('student.portfolio_status_rejected'), 'class' => 'oc-badge-bad'],
        'published' => ['label' => __('student.portfolio_status_published'), 'class' => 'oc-badge-ok'],
    ];
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .pf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
        gap: 12px;
    }
    .pf-card {
        display: flex; flex-direction: column;
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); overflow: hidden;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease;
    }
    .pf-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    }
    .pf-thumb {
        aspect-ratio: 16 / 9; background: var(--ml-well);
        display: flex; align-items: center; justify-content: center;
        color: var(--ml-teal-deep); font-size: 1.75rem;
    }
    .pf-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .pf-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .pf-body h3 {
        margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .pf-body .desc {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.5;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .pf-body .meta { margin: 0; font-size: 11px; color: var(--ml-muted); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <?php if(session('success')): ?>
        <div class="oc-panel" style="border-color:rgba(16,185,129,0.35);background:rgba(16,185,129,0.08);margin-bottom:16px;color:#047857;font-size:13px;font-weight:600">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px;font-weight:600">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.my_projects_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.my_projects_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.my_projects_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.my_projects_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($projects->total()); ?> <?php echo e(__('student.portfolio_projects_count')); ?></span>
            <?php if($pendingCount > 0): ?>
                <span class="oc-signal oc-signal-hot"><?php echo e($pendingCount); ?> <?php echo e(__('student.portfolio_pending_count')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <section class="oc-stage">
        <div class="oc-eyebrow"><?php echo e(__('student.portfolio')); ?></div>
        <h2><?php echo e(__('student.my_projects_title')); ?></h2>
        <p class="oc-copy"><?php echo e(__('student.my_projects_subtitle')); ?></p>
        <div class="oc-nav">
            <a class="oc-btn" href="<?php echo e(route('student.portfolio.create')); ?>">
                <i class="fas fa-plus text-xs"></i> <?php echo e(__('student.portfolio_upload_new')); ?>

            </a>
        </div>
    </section>

    <?php if($projects->count() > 0): ?>
        <div class="pf-grid">
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $s = $statusMap[$project->status] ?? ['label' => $project->status, 'class' => 'oc-badge-warn']; ?>
                <article class="pf-card">
                    <div class="pf-thumb">
                        <?php if($project->image_path): ?>
                            <img src="<?php echo e(asset($project->image_path)); ?>" alt="<?php echo e($project->title); ?>">
                        <?php else: ?>
                            <i class="fas fa-code" aria-hidden="true"></i>
                        <?php endif; ?>
                    </div>
                    <div class="pf-body">
                        <h3><?php echo e($project->title); ?></h3>
                        <?php if($project->description): ?>
                            <p class="desc"><?php echo e(\Illuminate\Support\Str::limit($project->description, 90)); ?></p>
                        <?php endif; ?>
                        <span class="oc-badge <?php echo e($s['class']); ?>"><?php echo e($s['label']); ?></span>
                        <?php if($project->academicYear): ?>
                            <p class="meta"><?php echo e($project->academicYear->name); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($projects->hasPages()): ?>
            <div style="margin-top:20px;display:flex;justify-content:center">
                <?php echo e($projects->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-briefcase"></i></div>
            <h3><?php echo e(__('student.portfolio_no_projects')); ?></h3>
            <p><?php echo e(__('student.portfolio_no_projects_desc')); ?></p>
            <div style="margin-top:16px">
                <a href="<?php echo e(route('student.portfolio.create')); ?>" class="oc-btn">
                    <i class="fas fa-plus text-xs"></i> <?php echo e(__('student.portfolio_upload')); ?>

                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/portfolio/index.blade.php ENDPATH**/ ?>