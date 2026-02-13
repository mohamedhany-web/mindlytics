

<?php $__env->startSection('title', 'إنشاء مصروف جديد'); ?>
<?php $__env->startSection('header', 'إنشاء مصروف جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-receipt text-sky-600 ml-3"></i>
                    <?php echo e(__('إنشاء مصروف جديد')); ?>

                </h1>
                <p class="text-gray-600"><?php echo e(__('إضافة مصروف جديد للموافقة عليه')); ?></p>
            </div>
            <a href="<?php echo e(route('admin.expenses.index')); ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span><?php echo e(__('العودة')); ?></span>
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <form action="<?php echo e(route('admin.expenses.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- العنوان -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-heading text-sky-600 ml-2"></i>
                        <?php echo e(__('العنوان')); ?> *
                    </label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="<?php echo e(__('مثال: شراء معدات للقاعة')); ?>">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الفئة -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-tags text-sky-600 ml-2"></i>
                        <?php echo e(__('الفئة')); ?> *
                    </label>
                    <select name="category" required 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value=""><?php echo e(__('اختر الفئة')); ?></option>
                        <option value="operational" <?php echo e(old('category') == 'operational' ? 'selected' : ''); ?>><?php echo e(__('تشغيلي')); ?></option>
                        <option value="marketing" <?php echo e(old('category') == 'marketing' ? 'selected' : ''); ?>><?php echo e(__('تسويق')); ?></option>
                        <option value="salaries" <?php echo e(old('category') == 'salaries' ? 'selected' : ''); ?>><?php echo e(__('رواتب')); ?></option>
                        <option value="utilities" <?php echo e(old('category') == 'utilities' ? 'selected' : ''); ?>><?php echo e(__('مرافق')); ?></option>
                        <option value="equipment" <?php echo e(old('category') == 'equipment' ? 'selected' : ''); ?>><?php echo e(__('معدات')); ?></option>
                        <option value="maintenance" <?php echo e(old('category') == 'maintenance' ? 'selected' : ''); ?>><?php echo e(__('صيانة')); ?></option>
                        <option value="other" <?php echo e(old('category') == 'other' ? 'selected' : ''); ?>><?php echo e(__('أخرى')); ?></option>
                    </select>
                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- المبلغ -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-sky-600 ml-2"></i>
                        <?php echo e(__('المبلغ')); ?> * (ج.م)
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="0.00">
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- تاريخ المصروف -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-sky-600 ml-2"></i>
                        <?php echo e(__('تاريخ المصروف')); ?> *
                    </label>
                    <input type="date" name="expense_date" value="<?php echo e(old('expense_date', date('Y-m-d'))); ?>" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- طريقة الدفع -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-credit-card text-sky-600 ml-2"></i>
                        <?php echo e(__('طريقة الدفع')); ?> *
                    </label>
                    <select name="payment_method" id="payment_method" required 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value=""><?php echo e(__('اختر طريقة الدفع')); ?></option>
                        <option value="cash" <?php echo e(old('payment_method') == 'cash' ? 'selected' : ''); ?>><?php echo e(__('نقدي')); ?></option>
                        <option value="bank_transfer" <?php echo e(old('payment_method') == 'bank_transfer' ? 'selected' : ''); ?>><?php echo e(__('تحويل بنكي')); ?></option>
                        <option value="card" <?php echo e(old('payment_method') == 'card' ? 'selected' : ''); ?>><?php echo e(__('بطاقة')); ?></option>
                        <option value="wallet" <?php echo e(old('payment_method') == 'wallet' ? 'selected' : ''); ?>><?php echo e(__('محفظة إلكترونية')); ?></option>
                        <option value="other" <?php echo e(old('payment_method') == 'other' ? 'selected' : ''); ?>><?php echo e(__('أخرى')); ?></option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- المحفظة الإلكترونية -->
                <div id="wallet_field" style="display: none;">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-wallet text-sky-600 ml-2"></i>
                        <?php echo e(__('المحفظة الإلكترونية')); ?>

                    </label>
                    <select name="wallet_id" id="wallet_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                        <option value=""><?php echo e(__('اختر محفظة')); ?></option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wallet->id); ?>" <?php echo e(old('wallet_id') == $wallet->id ? 'selected' : ''); ?>>
                                <?php echo e($wallet->name); ?> (<?php echo e($wallet->type_name); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- رقم المرجع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag text-sky-600 ml-2"></i>
                        <?php echo e(__('رقم المرجع')); ?> (اختياري)
                    </label>
                    <input type="text" name="reference_number" value="<?php echo e(old('reference_number')); ?>" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                           placeholder="<?php echo e(__('رقم الفاتورة، رقم الشيك، إلخ')); ?>">
                    <?php $__errorArgs = ['reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- المرفق -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-paperclip text-sky-600 ml-2"></i>
                        <?php echo e(__('صورة الفاتورة/الإيصال')); ?> (اختياري)
                    </label>
                    <input type="file" name="attachment" accept="image/*" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <p class="text-xs text-gray-500 mt-1"><?php echo e(__('يُسمح بالصور فقط (JPEG, PNG, JPG) - الحد الأقصى 2 ميجابايت')); ?></p>
                    <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-align-right text-sky-600 ml-2"></i>
                    <?php echo e(__('الوصف')); ?> (اختياري)
                </label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                          placeholder="<?php echo e(__('وصف تفصيلي للمصروف...')); ?>"><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الملاحظات -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note text-sky-600 ml-2"></i>
                    <?php echo e(__('ملاحظات')); ?> (اختياري)
                </label>
                <textarea name="notes" rows="2" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                          placeholder="<?php echo e(__('ملاحظات إضافية...')); ?>"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-200">
                <button type="submit" 
                        class="bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span><?php echo e(__('إنشاء المصروف')); ?></span>
                </button>
                <a href="<?php echo e(route('admin.expenses.index')); ?>" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span><?php echo e(__('إلغاء')); ?></span>
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const walletField = document.getElementById('wallet_field');
    const walletId = document.getElementById('wallet_id');

    paymentMethod.addEventListener('change', function() {
        if (this.value === 'wallet' || this.value === 'bank_transfer') {
            walletField.style.display = 'block';
            if (this.value === 'wallet') {
                walletId.setAttribute('required', 'required');
            } else {
                walletId.removeAttribute('required');
            }
        } else {
            walletField.style.display = 'none';
            walletId.removeAttribute('required');
            walletId.value = '';
        }
    });

    // Check on page load if payment method is already selected
    if (paymentMethod.value === 'wallet' || paymentMethod.value === 'bank_transfer') {
        walletField.style.display = 'block';
        if (paymentMethod.value === 'wallet') {
            walletId.setAttribute('required', 'required');
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/expenses/create.blade.php ENDPATH**/ ?>