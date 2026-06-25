

<?php $__env->startSection('title', 'تفاصيل برنامج الولاء'); ?>
<?php $__env->startSection('header', 'تفاصيل برنامج الولاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($loyaltyProgram->name); ?></h1>
                <p class="text-gray-600 mt-1"><?php echo e($loyaltyProgram->description ?? ''); ?></p>
            </div>
            <div>
                <button onclick="document.getElementById('editProgramModal').classList.remove('hidden')" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition-colors mr-2">
                    تعديل
                </button>
                <a href="<?php echo e(route('admin.loyalty.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                    رجوع
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">إعدادات البرنامج</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-600">نقاط لكل شراء:</span>
                        <span class="font-medium text-gray-900 text-lg mr-2"><?php echo e($loyaltyProgram->points_per_purchase ?? 0); ?> نقاط</span>
                    </div>
                    <?php if($loyaltyProgram->points_per_referral): ?>
                    <div>
                        <span class="text-sm text-gray-600">نقاط لكل إحالة:</span>
                        <span class="font-medium text-gray-900 text-lg mr-2"><?php echo e($loyaltyProgram->points_per_referral); ?> نقاط</span>
                    </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-sm text-gray-600">الحالة:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php echo e($loyaltyProgram->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> mr-2">
                            <?php echo e($loyaltyProgram->is_active ? 'نشط' : 'معطل'); ?>

                        </span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">الإحصائيات</h3>
                <div class="space-y-3">
                    <div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl p-4 border border-sky-200">
                        <div class="text-sm text-gray-600 mb-1">عدد المستخدمين</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e($loyaltyProgram->users_count ?? $loyaltyProgram->users->count() ?? 0); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($loyaltyProgram->redemption_rules): ?>
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">قواعد الاستبدال</h3>
            <div class="text-gray-600 whitespace-pre-line"><?php echo e(is_array($loyaltyProgram->redemption_rules) ? json_encode($loyaltyProgram->redemption_rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $loyaltyProgram->redemption_rules); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/loyalty/show.blade.php ENDPATH**/ ?>