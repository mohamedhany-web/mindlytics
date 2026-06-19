

<?php $__env->startSection('title', 'تعديل مكان'); ?>
<?php $__env->startSection('header', 'تعديل مكان'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تعديل مكان</h1>
                <p class="text-gray-600 mt-1">تحديث معلومات المكان</p>
            </div>
            <a href="<?php echo e(route('admin.offline-locations.show', $offlineLocation)); ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتفاصيل
            </a>
        </div>
    </div>

    <form action="<?php echo e(route('admin.offline-locations.update', $offlineLocation)); ?>" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- اسم المكان -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم المكان *</label>
                <input type="text" name="name" value="<?php echo e(old('name', $offlineLocation->name)); ?>" required 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- العنوان -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                <textarea name="address" rows="2" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('address', $offlineLocation->address)); ?></textarea>
            </div>

            <!-- المدينة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                <input type="text" name="city" value="<?php echo e(old('city', $offlineLocation->city)); ?>" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $offlineLocation->phone)); ?>" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- السعة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعة</label>
                <input type="number" name="capacity" value="<?php echo e(old('capacity', $offlineLocation->capacity)); ?>" min="0" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="is_active" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="1" <?php echo e(old('is_active', $offlineLocation->is_active) ? 'selected' : ''); ?>>نشط</option>
                    <option value="0" <?php echo e(!old('is_active', $offlineLocation->is_active) ? 'selected' : ''); ?>>غير نشط</option>
                </select>
            </div>

            <!-- الوصف -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('description', $offlineLocation->description)); ?></textarea>
            </div>

            <div class="md:col-span-2 pt-4 border-t">
                <h3 class="text-lg font-bold text-gray-900 mb-4">إعدادات الفوترة والمخالصة</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">سعر الساعة (ج.م)</label>
                <input type="number" name="hourly_rate" value="<?php echo e(old('hourly_rate', $offlineLocation->hourly_rate)); ?>" step="0.01" min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المحفظة الافتراضية للخصم</label>
                <select name="default_wallet_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($wallet->id); ?>" <?php if(old('default_wallet_id', $offlineLocation->default_wallet_id) == $wallet->id): echo 'selected'; endif; ?>><?php echo e($wallet->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم جهة الاتصال (المورد)</label>
                <input type="text" name="vendor_contact_name" value="<?php echo e(old('vendor_contact_name', $offlineLocation->vendor_contact_name)); ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الرقم الضريبي</label>
                <input type="text" name="vendor_tax_id" value="<?php echo e(old('vendor_tax_id', $offlineLocation->vendor_tax_id)); ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">بيانات الحساب البنكي</label>
                <textarea name="vendor_bank_details" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg"><?php echo e(old('vendor_bank_details', $offlineLocation->vendor_bank_details)); ?></textarea>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="<?php echo e(route('admin.offline-locations.show', $offlineLocation)); ?>" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ التغييرات
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-locations/edit.blade.php ENDPATH**/ ?>