<?php $__env->startSection('title', __('student.my_certificates_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
        gap: 12px;
    }
    .cert-card {
        display: flex; flex-direction: column; gap: 10px;
        padding: 16px; background: var(--ml-surface);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r);
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .cert-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .cert-card .ico {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 1.15rem;
    }
    .cert-card h3 {
        margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .cert-card .course {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.45;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .cert-meta {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        font-size: 11px; color: var(--ml-muted);
    }
    .cert-meta .num {
        font-family: ui-monospace, monospace; padding: 2px 7px; border-radius: 6px;
        background: var(--ml-well); font-weight: 700; color: var(--ml-ink);
    }
    .cert-go {
        margin-top: auto; padding-top: 4px;
        font-size: 12px; font-weight: 700; color: var(--ml-teal-deep);
        display: inline-flex; align-items: center; gap: 6px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.my_certificates_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.my_certificates_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.my_certificates_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.certificates_subtitle')); ?></p>
        </div>
        <?php if(isset($stats)): ?>
            <div class="oc-signals">
                <span class="oc-signal oc-signal-live"><?php echo e(__('student.total_certificates')); ?>: <?php echo e($stats['total'] ?? 0); ?></span>
                <span class="oc-signal oc-signal-hot"><?php echo e(__('student.issued_label')); ?>: <?php echo e($stats['issued'] ?? 0); ?></span>
            </div>
        <?php endif; ?>
    </header>

    <?php if(isset($stats)): ?>
        <div class="oc-pulse" aria-label="<?php echo e(__('student.my_certificates_title')); ?>">
            <div>
                <span class="lbl"><?php echo e(__('student.total_certificates')); ?></span>
                <span class="val teal"><?php echo e($stats['total'] ?? 0); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.issued_label')); ?></span>
                <span class="val"><?php echo e($stats['issued'] ?? 0); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($certificates) && $certificates->count() > 0): ?>
        <p class="oc-section-title"><?php echo e(__('student.issued_certificates')); ?></p>
        <div class="cert-grid">
            <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('student.certificates.show', $certificate)); ?>" class="cert-card">
                    <div class="ico" aria-hidden="true"><i class="fas fa-certificate"></i></div>
                    <h3><?php echo e($certificate->title ?? $certificate->course_name ?? __('student.completion_certificate')); ?></h3>
                    <?php if($certificate->course): ?>
                        <p class="course"><?php echo e($certificate->course->title); ?></p>
                    <?php endif; ?>
                    <div class="cert-meta">
                        <span>
                            <i class="fas fa-calendar" style="color:var(--ml-teal-deep);margin-inline-end:4px"></i>
                            <?php echo e($certificate->issued_at?->format('Y-m-d')
                                ?? $certificate->issue_date?->format('Y-m-d')
                                ?? '—'); ?>

                        </span>
                        <?php if($certificate->certificate_number): ?>
                            <span class="num">#<?php echo e(substr($certificate->certificate_number, -6)); ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="cert-go">
                        <?php echo e(__('student.view_certificate')); ?>

                        <i class="fas fa-arrow-left text-[10px]"></i>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($certificates->hasPages()): ?>
            <div style="margin-top:20px;display:flex;justify-content:center">
                <?php echo e($certificates->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-certificate"></i></div>
            <h3><?php echo e(__('student.no_certificates')); ?></h3>
            <p><?php echo e(__('student.no_certificates_desc')); ?></p>
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

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/certificates/index.blade.php ENDPATH**/ ?>