<?php $__env->startSection('title', __('student.orders_page_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .od-list { display: flex; flex-direction: column; gap: 10px; }
    .od-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
        padding: 14px 16px;
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease;
    }
    .od-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    }
    .od-head {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        margin-bottom: 6px;
    }
    .od-head h3 { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35; }
    .od-meta { margin: 0 0 10px; font-size: 12px; color: var(--ml-muted); line-height: 1.5; }
    .od-facts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 8px;
    }
    .od-facts > div {
        padding: 8px 10px; border-radius: 10px; background: var(--ml-well);
    }
    .od-facts .k { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 2px; }
    .od-facts .v { font-size: 13px; font-weight: 700; color: var(--ml-ink); }
    .od-notes {
        margin-top: 10px; padding: 10px 12px; border-radius: 10px;
        background: rgba(73, 164, 162, 0.08); border: 1px solid rgba(73, 164, 162, 0.2);
        font-size: 12px; color: var(--ml-ink); line-height: 1.55; white-space: pre-wrap;
    }
    .od-notes .lbl { display: block; font-size: 10px; font-weight: 700; color: var(--ml-muted); margin-bottom: 4px; }
    .od-side {
        display: flex; flex-direction: column; gap: 8px; min-width: 140px;
    }
    .od-side .oc-btn { width: 100%; justify-content: center; }
    @media (max-width: 720px) {
        .od-card { grid-template-columns: 1fr; }
        .od-side { flex-direction: row; min-width: 0; width: 100%; }
        .od-side .oc-btn { flex: 1; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.orders_page_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.orders_page_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.orders_page_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.orders_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($stats['total'] ?? $orders->total()); ?> <?php echo e(__('student.orders_count_label')); ?></span>
            <?php if(($stats['pending'] ?? 0) > 0): ?>
                <span class="oc-signal oc-signal-hot"><?php echo e($stats['pending']); ?> <?php echo e(__('student.orders_pending_label')); ?></span>
            <?php endif; ?>
        </div>
    </header>

    <section class="oc-stage">
        <div class="oc-eyebrow"><?php echo e(__('student.orders_tracking')); ?></div>
        <h2><?php echo e(__('student.orders_page_title')); ?></h2>
        <p class="oc-copy"><?php echo e(__('student.orders_subtitle')); ?></p>
        <div class="oc-nav">
            <a class="oc-btn" href="<?php echo e(route('academic-years')); ?>">
                <i class="fas fa-search text-xs"></i> <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    </section>

    <?php if(isset($stats)): ?>
        <div class="oc-pulse" aria-label="<?php echo e(__('student.orders_page_title')); ?>">
            <div>
                <span class="lbl"><?php echo e(__('student.orders_count_label')); ?></span>
                <span class="val teal"><?php echo e($stats['total']); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.orders_pending_label')); ?></span>
                <span class="val hot"><?php echo e($stats['pending']); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.orders_approved_label')); ?></span>
                <span class="val"><?php echo e($stats['approved']); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.orders_rejected_label')); ?></span>
                <span class="val"><?php echo e($stats['rejected']); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if($orders->count() > 0): ?>
        <div class="od-list">
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $title = $order->academic_year_id && $order->learningPath
                        ? ($order->learningPath->name ?? __('student.learning_path_label'))
                        : ($order->course->title ?? (\Illuminate\Support\Str::before($order->notes ?? __('student.course_undefined'), "\n") ?: __('student.course_undefined')));
                    $badge = match ($order->status) {
                        'approved' => 'oc-badge-ok',
                        'rejected' => 'oc-badge-bad',
                        default => 'oc-badge-warn',
                    };
                    $pay = match ($order->payment_method) {
                        'bank_transfer' => __('student.bank_transfer'),
                        'cash' => __('student.cash_label'),
                        default => __('student.other_label'),
                    };
                ?>
                <article class="od-card">
                    <div class="min-w-0">
                        <div class="od-head">
                            <h3><?php echo e($title); ?></h3>
                            <span class="oc-badge <?php echo e($badge); ?>"><?php echo e($order->status_text); ?></span>
                        </div>
                        <p class="od-meta">
                            <?php if($order->academic_year_id && $order->learningPath): ?>
                                <?php echo e(__('student.learning_path_label')); ?>

                                <?php if($order->learningPath->price): ?>
                                    · <?php echo e(number_format($order->learningPath->price, 2)); ?> <?php echo e(__('public.currency_egp')); ?>

                                <?php endif; ?>
                            <?php elseif($order->course && ($order->course->academicYear || $order->course->academicSubject)): ?>
                                <?php if($order->course->academicYear): ?><?php echo e($order->course->academicYear->name); ?><?php endif; ?>
                                <?php if($order->course->academicSubject): ?>
                                    <?php if($order->course->academicYear): ?> · <?php endif; ?><?php echo e($order->course->academicSubject->name); ?>

                                <?php endif; ?>
                            <?php endif; ?>
                            · <?php echo e($order->created_at->diffForHumans()); ?>

                        </p>
                        <div class="od-facts">
                            <div>
                                <span class="k"><?php echo e(__('student.amount_label')); ?></span>
                                <span class="v"><?php echo e(number_format($order->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                            </div>
                            <div>
                                <span class="k"><?php echo e(__('student.payment_method_label')); ?></span>
                                <span class="v"><?php echo e($pay); ?></span>
                            </div>
                            <div>
                                <span class="k"><?php echo e(__('student.order_date_label')); ?></span>
                                <span class="v"><?php echo e($order->created_at->format('d/m/Y')); ?></span>
                            </div>
                            <?php if($order->approved_at): ?>
                                <div>
                                    <span class="k"><?php echo e(__('student.approved_date_label')); ?></span>
                                    <span class="v"><?php echo e($order->approved_at->format('d/m/Y')); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($order->notes): ?>
                            <div class="od-notes">
                                <span class="lbl"><?php echo e(__('student.your_notes')); ?></span>
                                <?php echo e(\Illuminate\Support\Str::limit($order->notes, 180)); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="od-side">
                        <a href="<?php echo e(route('orders.show', $order)); ?>" class="oc-btn">
                            <i class="fas fa-eye text-xs"></i> <?php echo e(__('student.view_details')); ?>

                        </a>
                        <?php if($order->status == 'approved' && $order->course): ?>
                            <a href="<?php echo e(route('courses.show', $order->course)); ?>" class="oc-btn oc-btn-quiet">
                                <i class="fas fa-play text-xs"></i> <?php echo e(__('student.enter_course')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($orders->hasPages()): ?>
            <div style="margin-top:20px;display:flex;justify-content:center">
                <?php echo e($orders->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <h3><?php echo e(__('student.no_orders')); ?></h3>
            <p><?php echo e(__('student.no_orders_desc')); ?></p>
            <div style="margin-top:16px">
                <a href="<?php echo e(route('academic-years')); ?>" class="oc-btn">
                    <i class="fas fa-plus text-xs"></i> <?php echo e(__('student.browse_courses_btn')); ?>

                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\orders\index.blade.php ENDPATH**/ ?>