<?php $__env->startSection('title', 'عميل محتمل جديد'); ?>
<?php $__env->startSection('header', 'إضافة عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">إضافة عميل محتمل</h2>
                    <p class="text-xs text-slate-600">تسجيل Lead جديد وإسناده لموظف مبيعات.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </section>

    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء في النموذج:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($salesReps->isEmpty()): ?>
        <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-user-slash text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">لا يوجد موظف مبيعات نشط</p>
                <p class="text-xs text-slate-600 mt-2 max-w-md mx-auto">
                    أضف وظيفة برمز <code class="bg-amber-100 px-1.5 py-0.5 rounded text-amber-900">sales</code>
                    وعيّنها لموظف من قسم «الموظفين» قبل إضافة عملاء محتملين.
                </p>
            </div>
        </section>
    <?php else: ?>
        <form method="post" action="<?php echo e(route('admin.sales.leads.store')); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.sales.leads._lead_fields', ['lead' => null, 'salesReps' => $salesReps, 'categories' => $categories], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-500">
                        <i class="fas fa-info-circle text-sky-600 ml-0.5"></i>
                        بعد الحفظ يمكنك إضافة أنشطة ومتابعة العميل من صفحة التفاصيل.
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            إلغاء
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">
                            <i class="fas fa-save"></i>
                            حفظ العميل
                        </button>
                    </div>
                </div>
            </section>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/create.blade.php ENDPATH**/ ?>