

<?php $__env->startSection('title', 'تعديل الدفعة'); ?>
<?php $__env->startSection('header', 'تعديل الدفعة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل الدفعة #<?php echo e($payment->payment_number); ?></h1>
        
        <form action="<?php echo e(route('admin.payments.update', $payment)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e($payment->user_id == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفاتورة</label>
                    <select name="invoice_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($invoice->id); ?>" <?php echo e($payment->invoice_id == $invoice->id ? 'selected' : ''); ?>>
                                <?php echo e($invoice->invoice_number); ?> · <?php echo e($invoice->user->name); ?> · متبقي <?php echo e(number_format($invoice->remaining_amount + ($payment->invoice_id === $invoice->id ? $payment->amount : 0), 2)); ?> ج.م
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="" disabled selected>لا توجد فواتير متاحة</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="<?php echo e(old('amount', $payment->amount)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="cash" <?php echo e($payment->payment_method == 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="card" <?php echo e($payment->payment_method == 'card' ? 'selected' : ''); ?>>بطاقة</option>
                        <option value="bank_transfer" <?php echo e($payment->payment_method == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="online" <?php echo e($payment->payment_method == 'online' ? 'selected' : ''); ?>>دفع إلكتروني</option>
                        <option value="wallet" <?php echo e($payment->payment_method == 'wallet' ? 'selected' : ''); ?>>محفظة</option>
                        <option value="other" <?php echo e($payment->payment_method == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" <?php echo e($payment->status == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="completed" <?php echo e($payment->status == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="failed" <?php echo e($payment->status == 'failed' ? 'selected' : ''); ?>>فاشلة</option>
                        <option value="cancelled" <?php echo e($payment->status == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('notes', $payment->notes)); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الدفعة
                </button>
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/payments/edit.blade.php ENDPATH**/ ?>