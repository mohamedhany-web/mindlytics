<?php $__env->startSection('title', __('student.order_details_title') . ' #' . $order->id); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .od-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 999px) {
        .od-layout { grid-template-columns: 1fr; }
    }
    .od-aside { display: flex; flex-direction: column; gap: 12px; }
    @media (min-width: 1000px) {
        .od-aside-sticky { position: sticky; top: 12px; }
    }
    .od-product {
        display: flex; gap: 14px; align-items: flex-start;
    }
    .od-thumb {
        width: 72px; height: 72px; border-radius: 14px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(145deg, #49A4A2, #2f7f7d); color: #fff; font-size: 1.5rem;
        overflow: hidden; border: 1px solid var(--ml-line);
    }
    .od-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .od-product h3 { margin: 0 0 6px; font-size: 1.05rem; font-weight: 700; line-height: 1.35; }
    .od-product p { margin: 0 0 10px; font-size: 13px; color: var(--ml-muted); line-height: 1.55; }
    .od-status-box { text-align: center; padding: 8px 0 4px; }
    .od-status-box .ico {
        width: 56px; height: 56px; margin: 0 auto 10px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
    }
    .od-status-box .ico.warn { background: rgba(245, 158, 11, 0.16); color: #92400e; }
    .od-status-box .ico.ok { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .od-status-box .ico.bad { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .od-status-box strong { display: block; font-size: 1.05rem; margin-bottom: 4px; }
    .od-status-box p { margin: 0; font-size: 12px; color: var(--ml-muted); }
    .od-hint {
        margin-top: 12px; padding: 10px 12px; border-radius: 10px;
        font-size: 12px; line-height: 1.55;
    }
    .od-hint.warn { background: rgba(245, 158, 11, 0.12); color: #92400e; border: 1px solid rgba(245, 158, 11, 0.25); }
    .od-hint.ok { background: rgba(16, 185, 129, 0.1); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25); }
    .od-hint.bad { background: rgba(239, 68, 68, 0.1); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.25); }
    .od-proof {
        display: block; max-width: 100%; margin: 0 auto; border-radius: 12px;
        border: 1px solid var(--ml-line); cursor: zoom-in;
    }
    .od-modal {
        position: fixed; inset: 0; z-index: 80;
        background: rgba(26, 34, 56, 0.88);
        display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .od-modal.is-open { display: flex; }
    .od-modal img { max-width: min(960px, 100%); max-height: 90vh; border-radius: 12px; }
    .od-modal button {
        position: absolute; top: 16px; inset-inline-end: 16px;
        width: 40px; height: 40px; border: 0; border-radius: 10px;
        background: rgba(255,255,255,0.12); color: #fff; cursor: pointer; font-size: 18px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isPath = (bool) $order->academic_year_id;
    $statusKey = match ($order->status) {
        'approved' => 'ok',
        'rejected' => 'bad',
        default => 'warn',
    };
    $statusMsg = match ($order->status) {
        'approved' => __('student.order_status_approved_msg'),
        'rejected' => __('student.order_status_rejected_msg'),
        default => __('student.order_status_reviewing'),
    };
    $statusHint = match ($order->status) {
        'approved' => __('student.order_approved_hint'),
        'rejected' => __('student.order_rejected_hint'),
        default => __('student.order_pending_hint'),
    };
    $pay = match ($order->payment_method) {
        'bank_transfer' => __('student.bank_transfer'),
        'cash' => __('student.cash_label'),
        default => __('student.other_label'),
    };
?>

<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.order_details_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route('orders.index')); ?>"><?php echo e(__('student.orders_page_title')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">#<?php echo e($order->id); ?></span>
            </nav>
            <h1><?php echo e(__('student.order_details_title')); ?> #<?php echo e($order->id); ?></h1>
            <p class="sub"><?php echo e($order->created_at->format('d/m/Y - H:i')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal <?php echo e($statusKey === 'ok' ? 'oc-signal-live' : ($statusKey === 'bad' ? 'oc-signal-warn' : 'oc-signal-hot')); ?>">
                <?php echo e($order->status_text); ?>

            </span>
        </div>
    </header>

    <div class="od-layout">
        <div>
            <section class="oc-panel">
                <p class="oc-label"><?php echo e($isPath ? __('student.learning_path_info') : __('student.course_info')); ?></p>
                <div class="od-product">
                    <div class="od-thumb" aria-hidden="true">
                        <?php if($isPath && $order->learningPath?->thumbnail): ?>
                            <img src="<?php echo e(asset('storage/' . $order->learningPath->thumbnail)); ?>" alt="">
                        <?php elseif($order->course?->thumbnail): ?>
                            <img src="<?php echo e(asset('storage/' . $order->course->thumbnail)); ?>" alt="">
                        <?php else: ?>
                            <i class="fas <?php echo e($isPath ? 'fa-route' : 'fa-book-open'); ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <?php if($isPath && $order->learningPath): ?>
                            <h3><?php echo e($order->learningPath->name ?? __('student.learning_path_label')); ?></h3>
                            <span class="oc-badge oc-badge-live"><?php echo e(__('student.learning_path_label')); ?></span>
                            <?php if($order->learningPath->description): ?>
                                <p style="margin-top:8px"><?php echo e(\Illuminate\Support\Str::limit($order->learningPath->description, 140)); ?></p>
                            <?php endif; ?>
                            <div class="oc-nav" style="margin-top:10px">
                                <a class="oc-btn oc-btn-quiet" href="<?php echo e(route('public.learning-path.show', \Illuminate\Support\Str::slug($order->learningPath->name))); ?>">
                                    <?php echo e(__('student.order_view_path')); ?>

                                </a>
                            </div>
                        <?php elseif($order->course): ?>
                            <h3><?php echo e($order->course->title ?? __('student.course_undefined')); ?></h3>
                            <p>
                                <?php if($order->course->academicYear): ?><?php echo e($order->course->academicYear->name); ?><?php endif; ?>
                                <?php if($order->course->academicSubject): ?>
                                    <?php if($order->course->academicYear): ?> · <?php endif; ?><?php echo e($order->course->academicSubject->name); ?>

                                <?php endif; ?>
                            </p>
                            <?php if($order->course->description): ?>
                                <p><?php echo e(\Illuminate\Support\Str::limit($order->course->description, 140)); ?></p>
                            <?php endif; ?>
                            <div class="oc-nav">
                                <a class="oc-btn oc-btn-quiet" href="<?php echo e(route('courses.show', $order->course)); ?>">
                                    <?php echo e(__('student.order_view_course')); ?>

                                </a>
                            </div>
                        <?php else: ?>
                            <h3><?php echo e(\Illuminate\Support\Str::before($order->notes ?? __('student.order_untitled'), "\n") ?: __('student.order_untitled')); ?></h3>
                            <p><?php echo e(__('student.order_offline_note')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.order_payment_details')); ?></p>
                <ul class="oc-facts">
                    <li>
                        <span class="k"><?php echo e(__('student.amount_label')); ?></span>
                        <span class="v"><?php echo e(number_format($order->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                    </li>
                    <li>
                        <span class="k"><?php echo e(__('student.payment_method_label')); ?></span>
                        <span class="v"><?php echo e($pay); ?></span>
                    </li>
                    <li>
                        <span class="k"><?php echo e(__('student.order_date_label')); ?></span>
                        <span class="v"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
                    </li>
                    <?php if($order->approved_at): ?>
                        <li>
                            <span class="k"><?php echo e(__('student.approved_date_label')); ?></span>
                            <span class="v"><?php echo e($order->approved_at->format('d/m/Y H:i')); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if($order->notes): ?>
                    <p class="oc-label" style="margin-top:14px"><?php echo e(__('student.your_notes')); ?></p>
                    <p style="margin:0;font-size:13px;line-height:1.65;white-space:pre-wrap;color:var(--ml-ink)"><?php echo e($order->notes); ?></p>
                <?php endif; ?>
            </section>

            <?php if($order->payment_proof): ?>
                <?php
                    $fullPath = storage_path('app/public/' . $order->payment_proof);
                    $imageExists = file_exists($fullPath);
                    $imageUrl = null;
                    if ($imageExists) {
                        $imageUrl = asset('storage/' . $order->payment_proof);
                        if (! file_exists(public_path('storage/' . $order->payment_proof))) {
                            try {
                                $imageUrl = route('storage.file', ['path' => $order->payment_proof]);
                            } catch (\Throwable $e) {
                                $imageUrl = url('/storage/' . $order->payment_proof);
                            }
                        }
                    }
                ?>
                <section class="oc-panel">
                    <p class="oc-label"><?php echo e(__('student.order_payment_proof')); ?></p>
                    <?php if($imageExists && $imageUrl): ?>
                        <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e(__('student.order_payment_proof')); ?>" class="od-proof"
                             onclick="openImageModal(this.src)"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <p class="oc-empty" style="display:none;padding:16px;font-size:13px;color:#92400e"><?php echo e(__('student.order_proof_unavailable')); ?></p>
                        <p style="margin:10px 0 0;font-size:12px;color:var(--ml-muted);text-align:center"><?php echo e(__('student.order_proof_click')); ?></p>
                    <?php else: ?>
                        <p style="margin:0;font-size:13px;color:#92400e"><?php echo e(__('student.order_proof_missing')); ?></p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>

        <aside class="od-aside">
            <div class="oc-panel od-aside-sticky">
                <p class="oc-label"><?php echo e(__('student.order_status_label')); ?></p>
                <div class="od-status-box">
                    <div class="ico <?php echo e($statusKey); ?>">
                        <i class="fas <?php echo e($order->status === 'approved' ? 'fa-check' : ($order->status === 'rejected' ? 'fa-times' : 'fa-clock')); ?>"></i>
                    </div>
                    <strong><?php echo e($order->status_text); ?></strong>
                    <p><?php echo e($statusMsg); ?></p>
                </div>
                <div class="od-hint <?php echo e($statusKey); ?>"><?php echo e($statusHint); ?></div>

                <div class="oc-nav" style="margin-top:14px;flex-direction:column">
                    <?php if($order->status === 'approved' && $order->course): ?>
                        <a href="<?php echo e(route('courses.show', $order->course)); ?>" class="oc-btn" style="width:100%"><?php echo e(__('student.enter_course')); ?></a>
                    <?php elseif($order->status === 'rejected' && $order->course): ?>
                        <a href="<?php echo e(route('courses.show', $order->course)); ?>" class="oc-btn" style="width:100%"><?php echo e(__('student.order_retry')); ?></a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('orders.index')); ?>" class="oc-btn oc-btn-quiet" style="width:100%"><?php echo e(__('student.back_to_orders')); ?></a>
                </div>

                <?php if($order->approver): ?>
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--ml-line)">
                        <p class="oc-label"><?php echo e(__('student.order_reviewed_by')); ?></p>
                        <p style="margin:0;font-size:14px;font-weight:700"><?php echo e($order->approver->name ?? __('student.not_specified_short')); ?></p>
                        <?php if($order->approved_at): ?>
                            <p style="margin:4px 0 0;font-size:12px;color:var(--ml-muted)"><?php echo e($order->approved_at->format('d/m/Y - H:i')); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<div id="imageModal" class="od-modal" onclick="closeImageModal()">
    <button type="button" onclick="closeImageModal()" aria-label="Close"><i class="fas fa-times"></i></button>
    <img id="modalImage" src="" alt="<?php echo e(__('student.order_payment_proof')); ?>" onclick="event.stopPropagation()">
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}
function closeImageModal() {
    document.getElementById('imageModal').classList.remove('is-open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeImageModal();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\orders\show.blade.php ENDPATH**/ ?>