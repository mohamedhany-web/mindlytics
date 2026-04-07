<?php $__env->startSection('title', __('student.wallet_title')); ?>
<?php $__env->startSection('header', __('student.wallet_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر والرصيد -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4"><?php echo e(__('student.wallet_title')); ?></h1>
            <?php if(isset($wallet)): ?>
            <div class="flex items-center justify-between gap-4 p-4 sm:p-5 bg-sky-50 rounded-xl border border-sky-100">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1"><?php echo e(__('student.current_balance')); ?></p>
                    <p class="text-2xl sm:text-3xl font-bold text-sky-600"><?php echo e(number_format($wallet->balance ?? 0, 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            <?php else: ?>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-600 text-sm"><?php echo e(__('student.no_wallet_message')); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($transactions) && $transactions->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900"><?php echo e(__('student.transactions_log')); ?></h2>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between items-center p-4 sm:p-5 hover:bg-gray-50/50 transition-colors">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 truncate"><?php echo e($transaction->description ?? __('student.transaction_default')); ?></p>
                    <p class="text-sm text-gray-500 mt-0.5"><?php echo e($transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '—'); ?></p>
                </div>
                <p class="text-lg font-bold flex-shrink-0 <?php echo e(($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? 'text-emerald-600' : 'text-red-600'); ?>">
                    <?php echo e(($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? '+' : '−'); ?><?php echo e(number_format($transaction->amount ?? 0, 2)); ?> <?php echo e(__('public.currency_egp')); ?>

                </p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($transactions->hasPages()): ?>
        <div class="p-4 border-t border-gray-100"><?php echo e($transactions->links()); ?></div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3 text-gray-400">
            <i class="fas fa-exchange-alt text-xl"></i>
        </div>
        <p class="text-sm text-gray-500"><?php echo e(__('student.no_transactions')); ?></p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/wallet/index.blade.php ENDPATH**/ ?>