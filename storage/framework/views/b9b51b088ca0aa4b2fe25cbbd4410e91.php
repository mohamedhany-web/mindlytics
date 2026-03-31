<?php $__env->startSection('title', __('student.orders_page_title')); ?>
<?php $__env->startSection('header', __('student.orders_page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1"><?php echo e(__('student.orders_page_title')); ?></h1>
                <p class="text-sm text-gray-500"><?php echo e(__('student.orders_subtitle')); ?></p>
            </div>
            <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-search"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    </div>

    <?php if($orders->count() > 0): ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900">
                                    <?php if($order->academic_year_id && $order->learningPath): ?>
                                        <?php echo e($order->learningPath->name ?? __('student.learning_path_label')); ?>

                                    <?php elseif($order->course): ?>
                                        <?php echo e($order->course->title ?? __('student.course_undefined')); ?>

                                    <?php else: ?>
                                        <?php echo e(\Illuminate\Support\Str::before($order->notes ?? __('student.course_undefined'), "\n") ?: __('student.course_undefined')); ?>

                                    <?php endif; ?>
                                </h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($order->status == 'pending' ? 'bg-amber-100 text-amber-800' : ($order->status == 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800')); ?>">
                                    <?php if($order->status == 'pending'): ?><i class="fas fa-clock"></i>
                                    <?php elseif($order->status == 'approved'): ?><i class="fas fa-check-circle"></i>
                                    <?php else: ?><i class="fas fa-times-circle"></i>
                                    <?php endif; ?>
                                    <?php echo e($order->status_text); ?>

                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-3">
                                <?php if($order->academic_year_id && $order->learningPath): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-medium"><?php echo e(__('student.learning_path_label')); ?></span>
                                    <?php if($order->learningPath->price): ?>
                                        <span><?php echo e(number_format($order->learningPath->price, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                                    <?php endif; ?>
                                <?php elseif($order->course && ($order->course->academicYear || $order->course->academicSubject)): ?>
                                    <?php if($order->course->academicYear): ?>
                                        <span><?php echo e($order->course->academicYear->name); ?></span>
                                    <?php endif; ?>
                                    <?php if($order->course->academicSubject): ?>
                                        <span>· <?php echo e($order->course->academicSubject->name); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <span>· <?php echo e($order->created_at->diffForHumans()); ?></span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mb-3">
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.amount_label')); ?></p>
                                    <p class="text-sm font-bold text-gray-900"><?php echo e(number_format($order->amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                                </div>
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.payment_method_label')); ?></p>
                                    <p class="text-xs font-semibold text-gray-900">
                                        <?php if($order->payment_method == 'bank_transfer'): ?> <?php echo e(__('student.bank_transfer')); ?>

                                        <?php elseif($order->payment_method == 'cash'): ?> <?php echo e(__('student.cash_label')); ?>

                                        <?php else: ?> <?php echo e(__('student.other_label')); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.order_date_label')); ?></p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($order->created_at->format('d/m/Y')); ?></p>
                                </div>
                                <?php if($order->approved_at): ?>
                                <div class="py-2 px-3 bg-emerald-50 rounded-lg border border-emerald-100">
                                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.approved_date_label')); ?></p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($order->approved_at->format('d/m/Y')); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if($order->notes): ?>
                                <div class="p-3 bg-sky-50 rounded-lg border border-sky-100 mb-3">
                                    <p class="text-xs font-medium text-gray-500 mb-1"><?php echo e(__('student.your_notes')); ?></p>
                                    <p class="text-sm text-gray-700"><?php echo e($order->notes); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-row sm:flex-col gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="<?php echo e(route('orders.show', $order)); ?>" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                                <i class="fas fa-eye"></i>
                                <?php echo e(__('student.view_details')); ?>

                            </a>
                            <?php if($order->status == 'approved' && $order->course): ?>
                                <a href="<?php echo e(route('courses.show', $order->course)); ?>" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                                    <i class="fas fa-play"></i>
                                    <?php echo e(__('student.enter_course')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($orders->hasPages()): ?>
            <div class="flex justify-center mt-6">
                <?php echo e($orders->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo e(__('student.no_orders')); ?></h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto"><?php echo e(__('student.no_orders_desc')); ?></p>
            <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/orders/index.blade.php ENDPATH**/ ?>