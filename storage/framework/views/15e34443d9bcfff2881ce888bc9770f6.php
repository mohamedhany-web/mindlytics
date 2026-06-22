<?php $__env->startSection('title', 'إضافة فرع'); ?>
<?php $__env->startSection('header', 'إضافة فرع جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-16">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-code-branch text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="<?php echo e(route('admin.branches.index')); ?>" class="text-blue-600 hover:text-blue-700">الفروع</a>
                        <span>/</span>
                        <span class="text-slate-600">إضافة فرع</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">إنشاء فرع جديد</h2>
                    <p class="text-sm text-slate-600 mt-1">حدّد الاسم وslug النطاق الفرعي والعملة والمنطقة الزمنية. الحقول المطلوبة مُعلّمة بـ <span class="text-rose-500 font-bold">*</span>.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.branches.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shrink-0">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <form action="<?php echo e(route('admin.branches.store')); ?>" method="POST" class="space-y-0">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <div class="lg:col-span-2 space-y-6">
                    <?php echo $__env->make('admin.branches._form', ['branch' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            إرشادات سريعة
                        </h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>يجب أن يكون <strong class="text-slate-800">slug</strong> فريداً وأحرف لاتينية صغيرة.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>الدومين المخصص اختياري؛ عند تعبئته يجب أن يطابق سجل DNS لاحقاً.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>رابط النطاق الفرعي يُبنى من <code class="text-xs bg-slate-100 px-1 rounded">APP_URL</code> في البيئة.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <p class="text-xs text-slate-600"><span class="text-rose-500 font-bold">*</span> حقول مطلوبة قبل الحفظ.</p>
                        <div class="flex flex-col gap-3">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="fas fa-save"></i>
                                <span>حفظ الفرع</span>
                            </button>
                            <a href="<?php echo e(route('admin.branches.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition-all">
                                <i class="fas fa-times"></i>
                                <span>إلغاء والعودة</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\branches\create.blade.php ENDPATH**/ ?>