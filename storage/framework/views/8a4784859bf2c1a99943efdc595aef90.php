<?php $__env->startSection('title', 'تقارير المبيعات الشاملة'); ?>
<?php $__env->startSection('header', 'تقارير المبيعات الشاملة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $periodDays = ($start && $end) ? max(1, (int) $start->diffInDays($end) + 1) : 0;
    $hasPreview = $start && $end && !$error;

    if ($hasPreview && $selectedRep && $periodReport) {
        $statCards = [
            ['label' => 'المؤشر المركّب', 'value' => $periodReport['composite'] ?? '—', 'icon' => 'fas fa-chart-line', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => $selectedRep->name],
            ['label' => 'إيرادات فوز', 'value' => number_format((float) ($periodReport['metrics']['revenue_closed'] ?? 0), 2) . ' ج.م', 'icon' => 'fas fa-coins', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'description' => 'إيرادات مغلقة'],
            ['label' => 'عملاء في التقرير', 'value' => number_format($counts['leads'] ?? 0), 'icon' => 'fas fa-user-tag', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'Leads ذات صلة'],
            ['label' => 'أنشطة CRM', 'value' => number_format($counts['activities'] ?? 0), 'icon' => 'fas fa-tasks', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'أنشطة مسجّلة'],
        ];
    } elseif ($hasPreview) {
        $statCards = [
            ['label' => 'عملاء (فريق)', 'value' => number_format($counts['leads'] ?? 0), 'icon' => 'fas fa-users', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'Leads ذات صلة بالفترة'],
            ['label' => 'أنشطة CRM', 'value' => number_format($counts['activities'] ?? 0), 'icon' => 'fas fa-tasks', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'أنشطة الفريق'],
            ['label' => 'سجل النظام', 'value' => number_format($counts['audit'] ?? 0), 'icon' => 'fas fa-history', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'description' => 'أنشطة المبيعات'],
            ['label' => 'مدة الفترة', 'value' => $periodDays . ' يوم', 'icon' => 'fas fa-calendar-alt', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'جميع موظفي المبيعات'],
        ];
    } else {
        $statCards = [];
    }
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تقارير المبيعات الشاملة</h2>
                    <p class="text-xs text-slate-600">KPIs، العملاء المحتملون، أنشطة CRM، وسجل المبيعات — تصدير Excel جاهز للطباعة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="<?php echo e(route('admin.sales.kpi.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-chart-bar text-sky-600"></i>
                    KPIs
                </a>
            </div>
        </div>

        <?php if($hasPreview && !empty($statCards)): ?>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
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
                    الفترة: <strong><?php echo e($start->format('Y-m-d')); ?></strong> — <strong><?php echo e($end->format('Y-m-d')); ?></strong>
                    (<?php echo e($periodDays); ?> يوماً)
                    <?php if($selectedRep): ?>
                        · الموظف: <strong><?php echo e($selectedRep->name); ?></strong>
                    <?php else: ?>
                        · النطاق: <strong>جميع موظفي المبيعات</strong>
                    <?php endif; ?>
                </p>
                <?php if($selectedRep && $periodReport): ?>
                    <p class="text-xs text-slate-500 mt-2">
                        Leads أنشأها الموظف: <span class="font-bold text-slate-700"><?php echo e($counts['leads_created_by'] ?? 0); ?></span>
                        — Leads محوّلة من الإدارة: <span class="font-bold text-slate-700"><?php echo e($counts['leads_transferred_from_admin'] ?? 0); ?></span>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if($error): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i><?php echo e($error); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                إعدادات التقرير
            </h3>
            <p class="text-xs text-slate-600">اختر الفترة والموظف، ثم حدّث المعاينة أو صدّر إلى Excel.</p>
        </div>
        <div class="p-4">
            <form method="get"
                  action="<?php echo e(route('admin.sales.reports.index')); ?>"
                  x-data="{ userId: '<?php echo e((string) ($userId ?? '')); ?>' }"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ</label>
                    <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف (اختياري)</label>
                    <select name="user_id" x-model="userId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">— كل الفريق —</option>
                        <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($r->id); ?>" <?php if((string)$userId === (string)$r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">فلتر Leads</label>
                    <select name="lead_scope"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="touched" <?php if(($leadScope ?? 'touched') === 'touched'): echo 'selected'; endif; ?>>كل Leads ذات صلة بالفترة (Touched)</option>
                        <option value="new" <?php if(($leadScope ?? 'touched') === 'new'): echo 'selected'; endif; ?>>Leads مسجلة جديداً بواسطة الموظف</option>
                        <option value="transferred_from_admin" <?php if(($leadScope ?? 'touched') === 'transferred_from_admin'): echo 'selected'; endif; ?>>Leads محوّلة من الإدارة</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">يؤثر على التقرير اليومي ومعاينة Leads عند اختيار موظف.</p>
                </div>
                <div class="lg:col-span-4 flex flex-wrap items-center gap-2 pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-sync-alt"></i>
                        تحديث المعاينة
                    </button>
                    <a href="<?php echo e(route('admin.sales.reports.export', request()->query())); ?>"
                       class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-file-excel"></i>
                        تصدير Excel كامل
                    </a>
                    <button type="submit"
                            formaction="<?php echo e(route('admin.sales.reports.daily-export')); ?>"
                            formmethod="get"
                            :disabled="!userId"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-900 disabled:bg-slate-300 disabled:text-slate-600 disabled:cursor-not-allowed">
                        <i class="fas fa-calendar-day"></i>
                        تقرير يومي للموظف
                    </button>
                    <span class="text-xs text-slate-500" x-show="!userId" x-cloak>اختر موظفاً لتفعيل التقرير اليومي.</span>
                </div>
            </form>
        </div>
    </section>

    <?php if($hasPreview): ?>
        <?php if(!empty($periodReport['alert_flags'] ?? [])): ?>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-xs font-bold text-amber-900 mb-2"><i class="fas fa-bell ml-1"></i> تنبيهات</p>
                <ul class="text-sm text-amber-900 space-y-1 list-disc list-inside">
                    <?php $__currentLoopData = $periodReport['alert_flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($f); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if($selectedRep && $periodReport): ?>
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-black text-slate-900">تفصيل KPIs</h3>
                        <p class="text-xs text-slate-600">مقارنة الفعلي بالهدف للفترة المحددة.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200"><?php echo e($selectedRep->name); ?></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[640px] w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <th class="px-4 py-3 text-right font-semibold">المؤشر</th>
                                <th class="px-4 py-3 text-center font-semibold">الفعلي</th>
                                <th class="px-4 py-3 text-center font-semibold">الهدف (فترة)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__currentLoopData = $periodReport['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($line['label'] ?? ''); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums text-slate-900"><?php echo e($line['actual'] ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums text-slate-600"><?php echo e($line['target'] ?? '—'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php elseif($repSummaries->isNotEmpty()): ?>
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-black text-slate-900">ملخص الفريق</h3>
                        <p class="text-xs text-slate-600">أداء موظفي المبيعات خلال الفترة.</p>
                    </div>
                    <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200"><?php echo e($repSummaries->count()); ?> موظف</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[720px] w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                                <th class="px-4 py-3 text-center font-semibold">مركّب</th>
                                <th class="px-4 py-3 text-center font-semibold">إيراد فوز</th>
                                <th class="px-4 py-3 text-center font-semibold">فوز</th>
                                <th class="px-4 py-3 text-center font-semibold">Leads</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__currentLoopData = $repSummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $m = $row['report']['metrics'] ?? []; ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($row['user']->name); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums font-bold text-emerald-700"><?php echo e($row['report']['composite'] ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format((float) ($m['revenue_closed'] ?? 0), 2)); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums"><?php echo e((int) ($m['won_closed'] ?? 0)); ?></td>
                                    <td class="px-4 py-3 text-center tabular-nums"><?php echo e((int) ($m['new_leads'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">معاينة سريعة</h3>
                <p class="text-xs text-slate-600">عينة من Leads وأنشطة CRM وسجل النظام.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x lg:divide-x-reverse divide-slate-200">
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                            <i class="fas fa-user-tag text-xs"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">عملاء محتملون</h4>
                        <span class="text-xs text-slate-500 mr-auto"><?php echo e($leadsSample->count()); ?></span>
                    </div>
                    <?php if($leadsSample->isEmpty()): ?>
                        <p class="text-xs text-slate-500 py-4 text-center">لا توجد صفوف في المعاينة.</p>
                    <?php else: ?>
                        <ul class="space-y-2">
                            <?php $__currentLoopData = $leadsSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs">
                                    <span class="font-semibold text-slate-900"><?php echo e($l->name); ?></span>
                                    <span class="text-slate-500"> — <?php echo e(\App\Models\SalesLead::stageLabel($l->stage)); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-tasks text-xs"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">أنشطة CRM</h4>
                        <span class="text-xs text-slate-500 mr-auto"><?php echo e($activitiesSample->count()); ?></span>
                    </div>
                    <?php if($activitiesSample->isEmpty()): ?>
                        <p class="text-xs text-slate-500 py-4 text-center">لا توجد صفوف في المعاينة.</p>
                    <?php else: ?>
                        <ul class="space-y-2">
                            <?php $__currentLoopData = $activitiesSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs">
                                    <span class="font-semibold text-slate-900"><?php echo e(\App\Models\SalesActivity::typeLabel($a->type)); ?></span>
                                    <?php if($a->lead): ?>
                                        <span class="text-slate-500"> — <?php echo e($a->lead->name); ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fas fa-history text-xs"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">سجل النظام</h4>
                        <span class="text-xs text-slate-500 mr-auto"><?php echo e($auditSample->count()); ?></span>
                    </div>
                    <?php if($auditSample->isEmpty()): ?>
                        <p class="text-xs text-slate-500 py-4 text-center">لا توجد صفوف في المعاينة.</p>
                    <?php else: ?>
                        <ul class="space-y-2">
                            <?php $__currentLoopData = $auditSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-700 line-clamp-2">
                                    <?php echo e(\Illuminate\Support\Str::limit($log->description ?? $log->action, 80)); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="p-10 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">اختر الفترة واضغط «تحديث المعاينة»</p>
                <p class="text-xs text-slate-500 mt-1">ستظهر الإحصائيات والجداول والعينات هنا.</p>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\reports\index.blade.php ENDPATH**/ ?>