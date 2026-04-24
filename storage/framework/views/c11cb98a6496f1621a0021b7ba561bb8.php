

<?php $__env->startSection('title', 'تعديل الفاتورة'); ?>
<?php $__env->startSection('header', 'تعديل الفاتورة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل الفاتورة #<?php echo e($invoice->invoice_number); ?></h1>
        
        <form action="<?php echo e(route('admin.invoices.update', $invoice)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e($invoice->user_id == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الفاتورة *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="course" <?php echo e($invoice->type == 'course' ? 'selected' : ''); ?>>كورس</option>
                        <option value="subscription" <?php echo e($invoice->type == 'subscription' ? 'selected' : ''); ?>>اشتراك</option>
                        <option value="other" <?php echo e($invoice->type == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ الفرعي *</label>
                    <input type="number" name="subtotal" step="0.01" min="0" required value="<?php echo e(old('subtotal', $invoice->subtotal)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الضريبة</label>
                    <input type="number" name="tax_amount" step="0.01" min="0" value="<?php echo e(old('tax_amount', $invoice->tax_amount ?? 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخصم</label>
                    <input type="number" name="discount_amount" step="0.01" min="0" value="<?php echo e(old('discount_amount', $invoice->discount_amount ?? 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" <?php echo e($invoice->status == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="paid" <?php echo e($invoice->status == 'paid' ? 'selected' : ''); ?>>مدفوعة</option>
                        <option value="overdue" <?php echo e($invoice->status == 'overdue' ? 'selected' : ''); ?>>متأخرة</option>
                        <option value="cancelled" <?php echo e($invoice->status == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="<?php echo e(old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description', $invoice->description)); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('notes', $invoice->notes)); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الفاتورة
                </button>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/invoices/edit.blade.php ENDPATH**/ ?>