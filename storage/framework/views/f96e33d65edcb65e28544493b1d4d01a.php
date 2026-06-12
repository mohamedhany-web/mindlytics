

<?php $__env->startSection('title', 'تفاصيل الإحالة - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-user-friends text-sky-600 ml-3"></i>
                        تفاصيل الإحالة
                    </h1>
                    <p class="text-gray-600">معلومات تفصيلية عن الإحالة</p>
                </div>
                <a href="<?php echo e(route('admin.referrals.index')); ?>" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2.5 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Referrer Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-check text-sky-600"></i>
                    معلومات المحيل
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">الاسم</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referrer->name ?? 'غير معروف'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">رقم الهاتف</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referrer->phone ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">البريد الإلكتروني</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referrer->email ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">كود الإحالة</span>
                        <span class="font-mono font-bold text-sky-600"><?php echo e($referral->referrer->referral_code ?? '-'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Referred Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-plus text-emerald-600"></i>
                    معلومات المحال
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">الاسم</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referred->name ?? 'غير معروف'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">رقم الهاتف</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referred->phone ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">البريد الإلكتروني</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referred->email ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">تاريخ التسجيل</span>
                        <span class="font-medium text-gray-900"><?php echo e($referral->referred->created_at->format('d/m/Y')); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Details -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mt-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-purple-600"></i>
                تفاصيل الإحالة
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">البرنامج</span>
                    <span class="font-medium text-gray-900"><?php echo e($referral->referralProgram->name ?? '-'); ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">كود الإحالة</span>
                    <span class="font-mono font-bold text-gray-900"><?php echo e($referral->referral_code ?? $referral->code ?? '-'); ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">الحالة</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        <?php if($referral->status == 'completed'): ?> bg-emerald-100 text-emerald-800
                        <?php elseif($referral->status == 'pending'): ?> bg-amber-100 text-amber-800
                        <?php else: ?> bg-red-100 text-red-800
                        <?php endif; ?>">
                        <?php if($referral->status == 'completed'): ?> مكتملة
                        <?php elseif($referral->status == 'pending'): ?> قيد الانتظار
                        <?php else: ?> ملغاة
                        <?php endif; ?>
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">تاريخ الإحالة</span>
                    <span class="font-medium text-gray-900"><?php echo e($referral->created_at->format('d/m/Y H:i')); ?></span>
                </div>
                <?php if($referral->completed_at): ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">تاريخ الإكمال</span>
                    <span class="font-medium text-gray-900"><?php echo e($referral->completed_at->format('d/m/Y H:i')); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">الخصم المطبق</span>
                    <span class="font-bold text-purple-600"><?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> ج.م</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">عدد مرات استخدام الخصم</span>
                    <span class="font-medium text-gray-900"><?php echo e($referral->discount_used_count ?? 0); ?> / <?php echo e($referral->referralProgram->max_discount_uses_per_referred ?? 1); ?></span>
                </div>
                <?php if($referral->discount_expires_at): ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">انتهاء صلاحية الخصم</span>
                    <span class="font-medium text-gray-900 <?php echo e($referral->discount_expires_at < now() ? 'text-red-600' : ''); ?>">
                        <?php echo e($referral->discount_expires_at->format('d/m/Y H:i')); ?>

                    </span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">المكافأة</span>
                    <span class="font-bold text-emerald-600"><?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> ج.م</span>
                </div>
                <?php if($referral->autoCoupon): ?>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">الكوبون التلقائي</span>
                    <span class="font-mono font-bold text-gray-900"><?php echo e($referral->autoCoupon->code); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if($referral->invoice): ?>
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mt-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-file-invoice text-sky-600"></i>
                الفاتورة المرتبطة
            </h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">رقم الفاتورة</p>
                    <p class="text-lg font-bold text-gray-900"><?php echo e($referral->invoice->invoice_number); ?></p>
                </div>
                <a href="<?php echo e(route('admin.invoices.show', $referral->invoice)); ?>" 
                   class="bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    عرض الفاتورة
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/referrals/show.blade.php ENDPATH**/ ?>