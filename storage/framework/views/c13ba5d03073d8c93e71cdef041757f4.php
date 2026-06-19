<?php $__env->startSection('title', 'التقارير اليومية — المبيعات'); ?>
<?php $__env->startSection('header', 'التقارير اليومية — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي التقارير', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-clipboard-list', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'في الفترة المحددة'],
        ['label' => 'مسلّمة', 'value' => number_format($stats['submitted'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم تسليمها'],
        ['label' => 'بخصم تلقائي', 'value' => number_format($stats['with_penalty'] ?? 0), 'icon' => 'fas fa-gavel', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تأخرت عن الموعد'],
    ];
    $statusBadges = [
        'submitted' => ['label' => 'مسلّم', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقارير موظفي المبيعات اليومية</h2>
                    <p class="text-xs text-slate-600">نشاط، إنتاجية، ومشاكل العملاء — تصدير Excel لتحليل الأنماط.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.daily-reports.settings')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-cog text-slate-500"></i>
                    إعدادات وخصم
                </a>
                <a href="<?php echo e(route('admin.sales.daily-reports.export', request()->query())); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="px-4 pb-4">
            <p class="text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                الفترة: <strong><?php echo e($from->format('Y-m-d')); ?></strong> — <strong><?php echo e($to->format('Y-m-d')); ?></strong>
                <?php if($userId): ?>
                    · الموظف: <strong><?php echo e($reps->firstWhere('id', $userId)?->name ?? $userId); ?></strong>
                <?php endif; ?>
                <?php if($status): ?>
                    · الحالة: <strong><?php echo e($statusBadges[$status]['label'] ?? $status); ?></strong>
                <?php endif; ?>
            </p>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">تصفية حسب التاريخ أو الموظف أو حالة التسليم.</p>
        </div>
        <div class="p-4">
            <form method="get" action="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ</label>
                    <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rep->id); ?>" <?php if($userId == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">الكل</option>
                        <option value="submitted" <?php if($status === 'submitted'): echo 'selected'; endif; ?>>مسلّم</option>
                        <option value="draft" <?php if($status === 'draft'): echo 'selected'; endif; ?>>مسودة</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    <?php if(request()->hasAny(['from', 'to', 'user_id', 'status'])): ?>
                        <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">التقارير</h3>
                <p class="text-xs text-slate-600">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200"><?php echo e($reports->count()); ?> تقرير</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                        <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-center font-semibold">رسائل</th>
                        <th class="px-4 py-3 text-center font-semibold">مكالمات</th>
                        <th class="px-4 py-3 text-center font-semibold">تواصل</th>
                        <th class="px-4 py-3 text-center font-semibold w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusKey = $r->isSubmitted() ? 'submitted' : 'draft';
                            $statusMeta = $statusBadges[$statusKey];
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-800 tabular-nums"><?php echo e($r->report_date->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($r->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold <?php echo e($statusMeta['classes']); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?php echo e($statusMeta['label']); ?>

                                </span>
                                <?php if($r->auto_deduction_id): ?>
                                    <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200 mr-1">خصم</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700"><?php echo e($r->messages_replied ?? '—'); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700"><?php echo e($r->calls_made ?? '—'); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums text-slate-700"><?php echo e($r->contacts->count()); ?></td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?php echo e(route('admin.sales.daily-reports.show', $r->id)); ?>"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-sky-600 hover:bg-sky-50 text-sm"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا توجد تقارير</p>
                                <p class="text-xs text-slate-500 mt-1">لم يتم تسجيل تقارير في هذه الفترة أو لا توجد نتائج للفلتر.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\daily-reports\index.blade.php ENDPATH**/ ?>