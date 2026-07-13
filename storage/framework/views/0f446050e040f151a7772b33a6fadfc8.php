

<?php $__env->startSection('title', 'مجموعات العملاء'); ?>
<?php $__env->startSection('header', 'المبيعات — مجموعات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي المجموعات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'description' => 'كل مجموعات العملاء'],
        ['label' => 'عملاء في مجموعات', 'value' => number_format($stats['leads'] ?? 0), 'icon' => 'fas fa-user-tag', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'Leads مرتبطة بمجموعة'],
        ['label' => 'موظفون مشاركون', 'value' => number_format($stats['members'] ?? 0), 'icon' => 'fas fa-user-friends', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'موظفو مبيعات في مجموعات'],
        ['label' => 'بدون عملاء', 'value' => number_format($stats['empty'] ?? 0), 'icon' => 'fas fa-folder-open', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'مجموعات فارغة'],
    ];
    $hasFilters = request()->filled('search');
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
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
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">مجموعات العملاء</h2>
                    <p class="text-xs text-slate-600">إنشاء مجموعات مشتركة لموظف واحد أو أكثر — كل موظف يرى عملاءه ضمن المجموعة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="<?php echo e(route('admin.sales.sales-teams.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-users-cog text-teal-600"></i>
                    فرق المبيعات
                </a>
                <a href="<?php echo e(route('admin.sales.groups.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-plus"></i>
                    مجموعة جديدة
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
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-search text-teal-600"></i>
                البحث
            </h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.sales.groups.index')); ?>" class="p-4 flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-slate-700 mb-1">بحث باسم المجموعة أو الوصف</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="مثال: دفعة مارس..." class="<?php echo e($inputClass); ?>">
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl bg-teal-600 hover:bg-teal-700">
                    <i class="fas fa-filter"></i>
                    تطبيق
                </button>
                <?php if($hasFilters): ?>
                    <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-semibold text-slate-600 rounded-xl border border-slate-300 hover:bg-slate-50" title="مسح">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">قائمة المجموعات</h3>
            <span class="text-xs text-slate-500 tabular-nums"><?php echo e($groups->total()); ?> مجموعة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="px-4 py-3 text-right font-semibold">المجموعة</th>
                        <th class="px-4 py-3 text-right font-semibold">الموظفون</th>
                        <th class="px-4 py-3 text-right font-semibold">العملاء</th>
                        <th class="px-4 py-3 text-right font-semibold">آخر تحديث</th>
                        <th class="px-4 py-3 text-left font-semibold w-28">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900"><?php echo e($g->name); ?></p>
                                <?php if($g->description): ?>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"><?php echo e($g->description); ?></p>
                                <?php endif; ?>
                                <?php if($g->creator): ?>
                                    <p class="text-[11px] text-slate-400 mt-0.5">أنشأها: <?php echo e($g->creator->name); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                    $memberNames = $g->members->isNotEmpty()
                                        ? $g->members->pluck('name')
                                        : collect([$g->assignee?->name])->filter();
                                ?>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-sky-50 text-sky-800 text-xs font-bold tabular-nums">
                                    <i class="fas fa-users"></i>
                                    <?php echo e($memberNames->count()); ?>

                                </span>
                                <?php if($memberNames->isNotEmpty()): ?>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?php echo e($memberNames->take(3)->implode('، ')); ?><?php if($memberNames->count() > 3): ?> ...<?php endif; ?></p>
                                <?php else: ?>
                                    <p class="text-xs text-slate-400 mt-1">—</p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-bold tabular-nums border border-emerald-100">
                                    <i class="fas fa-user-tag"></i>
                                    <?php echo e(number_format($g->leads_count)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 tabular-nums">
                                <?php echo e($g->updated_at?->diffForHumans() ?? '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('admin.sales.groups.show', $g)); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition-colors">
                                    <i class="fas fa-cog"></i>
                                    إدارة
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-slate-100 flex items-center justify-center">
                                    <i class="fas fa-layer-group text-2xl text-slate-400"></i>
                                </div>
                                <p class="font-bold text-slate-900 mb-1">لا توجد مجموعات</p>
                                <p class="text-sm text-slate-500 mb-4">أنشئ مجموعة واربط موظفي المبيعات بعملائهم</p>
                                <a href="<?php echo e(route('admin.sales.groups.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                                    <i class="fas fa-plus"></i>
                                    إنشاء مجموعة
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($groups->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50"><?php echo e($groups->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/groups/index.blade.php ENDPATH**/ ?>