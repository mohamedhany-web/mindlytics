<?php $__env->startSection('title', 'فروع الأكاديمية'); ?>
<?php $__env->startSection('header', 'فروع الأكاديمية'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .table-row { transition: background-color 0.15s ease; }
    .table-row:hover { background: rgba(59, 130, 246, 0.05); }
    .avatar-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statsCards = [
        ['label' => 'إجمالي الفروع', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-code-branch', 'description' => 'كل السجلات في النظام'],
        ['label' => 'فروع نشطة', 'value' => number_format($stats['active'] ?? 0), 'icon' => 'fas fa-check-circle', 'description' => 'متاحة لحل الدومين والعرض'],
        ['label' => 'بدومين مخصص', 'value' => number_format($stats['with_custom_domain'] ?? 0), 'icon' => 'fas fa-globe', 'description' => 'مربوطة بنطاق خاص'],
    ];
?>

<div class="space-y-6 pb-12">
    <div class="bg-gradient-to-r from-slate-50 to-white rounded-2xl p-6 border border-slate-200 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-code-branch text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-1">فروع الأكاديمية</h1>
                    <p class="text-sm sm:text-base text-slate-600 font-medium">إدارة النطاقات الفرعية، الدومينات المخصصة، وربط المحتوى بكل فرع</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="<?php echo e(route('admin.branches.rollout-plan')); ?>"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-book-open"></i>
                    خطة التوسع
                </a>
                <a href="<?php echo e(route('admin.branches.create')); ?>"
                   class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    <span>إضافة فرع</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <?php $__currentLoopData = $statsCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 mb-2"><?php echo e($stat['label']); ?></p>
                        <p class="text-3xl sm:text-4xl font-black text-slate-900 tabular-nums"><?php echo e($stat['value']); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 mr-3 sm:mr-0">
                        <i class="<?php echo e($stat['icon']); ?> text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-slate-600"><?php echo e($stat['description']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-list text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة الفروع</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        <span class="font-bold text-blue-600"><?php echo e($branches->total()); ?></span> فرع — مرتبة حسب ترتيب العرض ثم الاسم
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building text-blue-600"></i>
                                <span>الفرع</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-globe text-blue-600"></i>
                                <span>دومين مخصص</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-flag text-blue-600"></i>
                                <span>دولة / عملة</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-sort-numeric-down text-blue-600"></i>
                                <span>الترتيب</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-toggle-on text-blue-600"></i>
                                <span>الحالة</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-cog text-blue-600"></i>
                                <span>الإجراءات</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md shrink-0">
                                        <?php echo e(mb_substr(trim($branch->name), 0, 1, 'UTF-8')); ?>

                                    </div>
                                    <div class="space-y-1 min-w-0">
                                        <p class="font-bold text-slate-900 text-base truncate"><?php echo e($branch->name); ?></p>
                                        <p class="text-xs text-slate-600 font-mono truncate" dir="ltr"><?php echo e($branch->slug); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-[14rem] sm:max-w-xs">
                                <span class="font-mono text-xs text-slate-700 break-all" dir="ltr"><?php echo e($branch->custom_domain ?: '—'); ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-800">
                                <span class="font-semibold"><?php echo e($branch->country_code ?? '—'); ?></span>
                                <span class="text-slate-400 mx-1">·</span>
                                <span class="font-mono text-xs"><?php echo e($branch->currency ? strtoupper($branch->currency) : '—'); ?></span>
                            </td>
                            <td class="px-6 py-4 font-bold tabular-nums text-slate-900"><?php echo e($branch->sort_order); ?></td>
                            <td class="px-6 py-4">
                                <?php if($branch->is_active): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        نشط
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-slate-200 text-slate-700 border border-slate-300">
                                        موقوف
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <a href="<?php echo e(route('admin.branches.show', $branch)); ?>"
                                       class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.branches.edit', $branch)); ?>"
                                       class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.branches.destroy', $branch)); ?>" method="POST" class="inline" onsubmit="return confirm('أرشفة هذا الفرع؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="w-9 h-9 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                                title="أرشفة">
                                            <i class="fas fa-archive text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-code-branch text-3xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg mb-1">لا توجد فروع بعد</p>
                                        <p class="text-sm text-slate-600 font-medium mb-4">أنشئ فرعاً رئيسياً (مثل <code class="bg-slate-100 px-2 py-0.5 rounded text-xs font-mono">main</code>) للبدء.</p>
                                        <a href="<?php echo e(route('admin.branches.create')); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                                            <i class="fas fa-plus"></i>
                                            إضافة فرع
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($branches->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <?php echo e($branches->withQueryString()->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\branches\index.blade.php ENDPATH**/ ?>