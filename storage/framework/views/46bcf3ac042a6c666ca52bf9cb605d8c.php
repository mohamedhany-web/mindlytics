

<?php $__env->startSection('title', 'تعديل فرع'); ?>
<?php $__env->startSection('header', 'تعديل الفرع — ' . $branch->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-none min-h-full -mx-3 sm:-mx-6 px-2 sm:px-4 lg:px-6 xl:px-8 pb-4 space-y-6" style="background: #f8fafc;">
    <div class="w-full max-w-[min(100%,88rem)] mx-auto space-y-6">
        <div class="bg-gradient-to-br from-indigo-600 via-sky-600 to-sky-700 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-sm font-semibold">
                        <i class="fas fa-pen"></i>
                        تعديل فرع
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold"><?php echo e($branch->name); ?></h1>
                    <p class="text-sm text-white/85 font-mono" dir="ltr"><?php echo e($branch->slug); ?></p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="<?php echo e(route('admin.branches.show', $branch)); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2.5 text-sm font-semibold hover:bg-white/10 transition">
                        <i class="fas fa-eye"></i>
                        عرض التفاصيل
                    </a>
                    <a href="<?php echo e(route('admin.branches.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2.5 text-sm font-semibold hover:bg-white/10 transition">
                        <i class="fas fa-arrow-right"></i>
                        كل الفروع
                    </a>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                <h2 class="text-xl font-bold text-gray-900">تحديث بيانات الفرع</h2>
                <p class="text-sm text-gray-500 mt-1">تعديل slug أو الدومين قد يتطلب تحديث DNS أو إبطال كاش الدومين تلقائياً بعد الحفظ.</p>
            </div>
            <form action="<?php echo e(route('admin.branches.update', $branch)); ?>" method="POST" class="p-6 sm:p-8 space-y-8">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <?php echo $__env->make('admin.branches._form', ['branch' => $branch], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="<?php echo e(route('admin.branches.show', $branch)); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-2xl border-2 border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition">
                        عرض فقط
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-2xl bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-600 text-white font-bold shadow-lg shadow-sky-600/25 hover:from-sky-700 hover:via-blue-700 hover:to-indigo-700 transition">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/branches/edit.blade.php ENDPATH**/ ?>