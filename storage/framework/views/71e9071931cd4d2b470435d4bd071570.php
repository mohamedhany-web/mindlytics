

<?php $__env->startSection('title', 'لوحة المكان'); ?>
<?php $__env->startSection('header', 'لوحة المكان'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(44, 169, 189, 0.2);
        border-color: rgba(44, 169, 189, 0.4);
    }
    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 32px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }
    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if($mustCloseSoon): ?>
        <div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-amber-900">
            <p class="font-bold"><i class="fas fa-exclamation-triangle ml-2"></i>تذكير: يتبقى <?php echo e($daysLeftInMonth); ?> يوم على نهاية الشهر</p>
            <p class="text-sm mt-1">يجب إرسال المخالصة الشهرية وإقفال حساب <?php echo e($period); ?> قبل نهاية الشهر.</p>
        </div>
    <?php endif; ?>

    <div class="welcome-section dashboard-card relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2 text-gray-900">مرحباً، <?php echo e($user->name); ?></h2>
                    <p class="text-gray-600 text-base sm:text-lg font-medium">لوحة إدارة المكان — <?php echo e($location->name); ?></p>
                    <p class="text-gray-500 text-sm mt-2 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>سجّل الساعات وأرسل المخالصة الشهرية للموافقة</span>
                    </p>
                    <?php if($location->hourly_rate): ?>
                        <p class="text-sm text-blue-700 font-semibold mt-2">سعر الساعة: <?php echo e(number_format((float) $location->hourly_rate, 2)); ?> ج.م</p>
                    <?php endif; ?>
                </div>
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-building text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-blue-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">ساعات معتمدة — <?php echo e($period); ?></p>
                    <p class="text-3xl font-black text-blue-700"><?php echo e(number_format((float) $approvedHoursThisMonth, 2)); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-yellow-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">سجلات في الانتظار</p>
                    <p class="text-3xl font-black text-yellow-700"><?php echo e($pendingLogs); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-hourglass-half text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-green-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">حالة مخالصة الشهر</p>
                    <p class="text-xl font-black text-green-700"><?php echo e($currentSettlement?->status_label ?? 'لم تُفتح'); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('place.office.usage-logs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
            <i class="fas fa-plus"></i> تسجيل ساعات
        </a>
        <a href="<?php echo e(route('place.office.settlements.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border-2 border-blue-200 text-blue-800 text-sm font-semibold hover:bg-blue-50 transition-colors">
            <i class="fas fa-file-invoice"></i> المخالصة الشهرية
        </a>
        <a href="<?php echo e(route('place.office.invoices.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border-2 border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-receipt"></i> الفواتير
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/place-office/dashboard.blade.php ENDPATH**/ ?>