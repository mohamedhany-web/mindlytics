

<?php $__env->startSection('title', 'فرق المبيعات'); ?>
<?php $__env->startSection('header', 'المبيعات — فرق المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي الفرق', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-users-cog', 'bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'description' => 'كل الفرق المسجّلة'],
        ['label' => 'فرق نشطة', 'value' => number_format($stats['active'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تعمل حالياً'],
        ['label' => 'أعضاء السيلز', 'value' => number_format($stats['members'] ?? 0), 'icon' => 'fas fa-user-friends', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'موظفو مبيعات في فرق'],
        ['label' => 'مديرو مبيعات', 'value' => number_format($stats['managers'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'مديرون نشطون'],
    ];
    $hasFilters = request()->filled('search') || request()->filled('status');
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">فرق المبيعات</h2>
                    <p class="text-xs text-slate-600">تنظيم موظفي السيلز ومديري المبيعات في فرق — حضور، leads، وتقارير الفريق.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.team-daily-reports.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-clipboard-list text-sky-600"></i>
                    تقارير الفرق
                </a>
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="<?php echo e(route('admin.sales.sales-teams.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-plus"></i>
                    فريق جديد
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($hasFilters): ?>
            <div class="px-4 pb-4">
                <p class="text-xs text-slate-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    <i class="fas fa-filter text-amber-600 ml-1"></i>
                    فلتر نشط — يعرض <strong><?php echo e(number_format($teams->total())); ?></strong> فريقاً مطابقاً.
                </p>
            </div>
        <?php endif; ?>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-search text-teal-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.sales.sales-teams.index')); ?>" class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم الفريق أو المدير..."
                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">كل الحالات</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>موقوف</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl bg-teal-600 hover:bg-teal-700">
                    <i class="fas fa-filter"></i>
                    تطبيق
                </button>
                <?php if($hasFilters): ?>
                    <a href="<?php echo e(route('admin.sales.sales-teams.index')); ?>" class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-semibold text-slate-600 rounded-xl border border-slate-300 hover:bg-slate-50" title="مسح">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">قائمة الفرق</h3>
            <span class="text-xs text-slate-500 tabular-nums"><?php echo e($teams->total()); ?> فريق</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="px-4 py-3 text-right font-semibold">الفريق</th>
                        <th class="px-4 py-3 text-right font-semibold">مدير المبيعات</th>
                        <th class="px-4 py-3 text-right font-semibold">الأعضاء</th>
                        <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-left font-semibold w-28">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900"><?php echo e($team->name); ?></p>
                                <?php if($team->description): ?>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"><?php echo e($team->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-slate-800">
                                    <i class="fas fa-user-tie text-teal-600 text-xs"></i>
                                    <?php echo e($team->manager->name ?? '—'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-sky-50 text-sky-800 text-xs font-bold tabular-nums">
                                    <i class="fas fa-users"></i>
                                    <?php echo e($team->members_count); ?>

                                </span>
                                <?php if($team->members->isNotEmpty()): ?>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                        <?php echo e($team->members->pluck('user.name')->filter()->take(3)->implode('، ')); ?>

                                        <?php if($team->members_count > 3): ?>
                                            ...
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($team->is_active): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        نشط
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        موقوف
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('admin.sales.sales-teams.edit', $team)); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition-colors">
                                    <i class="fas fa-pen"></i>
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <i class="fas fa-users-cog text-2xl text-slate-400"></i>
                                </div>
                                <p class="font-bold text-slate-900 mb-1">لا توجد فرق مبيعات</p>
                                <p class="text-sm text-slate-500 mb-4">أنشئ أول فريق واربط مدير مبيعات بأعضاء السيلز</p>
                                <a href="<?php echo e(route('admin.sales.sales-teams.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                                    <i class="fas fa-plus"></i>
                                    إنشاء فريق
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($teams->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50"><?php echo e($teams->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\sales-teams\index.blade.php ENDPATH**/ ?>