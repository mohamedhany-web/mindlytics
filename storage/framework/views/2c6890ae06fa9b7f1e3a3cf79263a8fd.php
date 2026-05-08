

<?php $__env->startSection('title', 'Insights — المبيعات'); ?>
<?php $__env->startSection('header', 'Insights — تقرير أداء موظف المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 md:p-6">
        <form method="get" action="<?php echo e(route('admin.sales.insights.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
                    <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($r->id); ?>" <?php if((string) $rep->id === (string) $r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">الفترة</label>
                <select name="period" id="period_sel" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
                    <option value="day" <?php if(request('period', 'week') === 'day'): echo 'selected'; endif; ?>>يومي</option>
                    <option value="week" <?php if(request('period', 'week') === 'week'): echo 'selected'; endif; ?>>أسبوعي</option>
                    <option value="month" <?php if(request('period', 'week') === 'month'): echo 'selected'; endif; ?>>شهري</option>
                    <option value="custom" <?php if(request('period', 'week') === 'custom'): echo 'selected'; endif; ?>>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">من</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo e($start->toDateString()); ?>" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">إلى</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo e($end->toDateString()); ?>" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex flex-wrap gap-2 lg:col-span-6">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
                <button type="button" onclick="downloadInsightsPdf()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-950 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-file-pdf"></i> PDF للموظف
                </button>
                <a href="<?php echo e(route('admin.sales.commissions.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-coins"></i> كوميشن المبيعات
                </a>
            </div>
            <input type="hidden" name="_preset_period_label" value="<?php echo e($periodLabel); ?>">
        </form>
        <p class="text-xs text-slate-500 mt-3">الفترة الفعلية: <strong><?php echo e($start->format('Y-m-d')); ?></strong> إلى <strong><?php echo e($end->format('Y-m-d')); ?></strong>.</p>
    </div>

    <div id="insights-pdf-root" class="space-y-6">
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm text-gray-700">الموظف: <span class="font-black text-gray-900"><?php echo e($rep->name); ?></span></p>
                    <p class="text-xs text-gray-500 mt-1">Insights — <?php echo e($periodLabel); ?> — <?php echo e($start->format('Y-m-d')); ?> → <?php echo e($end->format('Y-m-d')); ?></p>
                </div>
                <?php
                    $statusClass = match($decision['status'] ?? 'good') {
                        'excellent' => 'bg-emerald-600 text-white',
                        'good' => 'bg-sky-600 text-white',
                        'warning' => 'bg-amber-600 text-white',
                        default => 'bg-rose-600 text-white',
                    };
                ?>
                <div class="px-4 py-2 rounded-xl <?php echo e($statusClass); ?>">
                    <p class="text-xs font-bold opacity-90">القرار</p>
                    <p class="text-sm font-black"><?php echo e($decision['status_label'] ?? '—'); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mt-4">
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">المؤشر المركّب</p>
                    <p class="text-2xl font-black text-emerald-700 tabular-nums"><?php echo e($decision['composite'] ?? ($periodReport['composite'] ?? '—')); ?></p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">Leads في التقرير</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['leads'] ?? 0); ?></p>
                    <p class="text-[11px] text-slate-500 mt-1">أنشأها الموظف: <?php echo e($counts['leads_created_by'] ?? 0); ?></p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($counts['activities'] ?? 0); ?></p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">wins معتمدة (كوميشن)</p>
                    <p class="text-2xl font-black text-emerald-800 tabular-nums"><?php echo e($commission['confirmed_wins'] ?? 0); ?></p>
                    <p class="text-[11px] text-slate-500 mt-1">كوميشن: <?php echo e(number_format((float) ($commission['commission_from_leads'] ?? 0), 2)); ?> ج.م</p>
                </div>
            </div>

            <?php if(!empty($decision['flags'])): ?>
                <ul class="mt-4 text-sm text-amber-900 space-y-1 list-disc list-inside">
                    <?php $__currentLoopData = $decision['flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($f); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>

            <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-bold text-slate-600 mb-2">توصيات النظام</p>
                <ul class="text-sm text-slate-800 space-y-1 list-disc list-inside">
                    <?php $__currentLoopData = ($decision['recommendations'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($rec); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                <h2 class="text-base font-black text-gray-900">تفصيل KPIs (الفترة)</h2>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-[740px] w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-800 text-white">
                            <th class="px-3 py-2 text-right">المؤشر</th>
                            <th class="px-3 py-2 text-center">الفعلي</th>
                            <th class="px-3 py-2 text-center">الهدف (فترة)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $periodReport['kpi_lines'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-emerald-50/50">
                                <td class="px-3 py-2 font-medium text-gray-800"><?php echo e($line['label'] ?? ''); ?></td>
                                <td class="px-3 py-2 text-center tabular-nums"><?php echo e($line['actual'] ?? '—'); ?></td>
                                <td class="px-3 py-2 text-center tabular-nums"><?php echo e($line['target'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">Leads (عينة)</h3>
                <?php if($leadsSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد صفوف في الفترة.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $leadsSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold"><?php echo e($l->name); ?></span>
                                <span class="text-gray-500"> — <?php echo e(\App\Models\SalesLead::stageLabel($l->stage)); ?></span>
                                <?php if($l->email): ?><span class="text-gray-400"> — <?php echo e($l->email); ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">أنشطة CRM (عينة)</h3>
                <?php if($activitiesSample->isEmpty()): ?>
                    <p class="text-xs text-gray-500">لا توجد أنشطة في الفترة.</p>
                <?php else: ?>
                    <ul class="text-xs space-y-2 text-gray-700">
                        <?php $__currentLoopData = $activitiesSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold"><?php echo e(\App\Models\SalesActivity::typeLabel($a->type)); ?></span>
                                <?php if($a->lead): ?><span class="text-gray-500"> — <?php echo e($a->lead->name); ?> (<?php echo e(\App\Models\SalesLead::stageLabel($a->lead->stage)); ?>)</span><?php endif; ?>
                                <span class="text-gray-400"> — <?php echo e($a->created_at?->format('Y-m-d H:i')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-black text-gray-900">الكورسات المرتبطة بعملاء الموظف (حسب الإيميل)</h3>
                <span class="text-xs text-slate-500">حسابات مطابقة: <?php echo e((int) ($courses['matched_users'] ?? 0)); ?></span>
            </div>
            <?php if(!empty($courses['note'])): ?>
                <p class="text-xs text-amber-800"><?php echo e($courses['note']); ?></p>
            <?php endif; ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">تسجيلات كورسات الموقع (Online enrollments)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: <?php echo e((int) data_get($courses, 'online_enrollments.count', 0)); ?></p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = data_get($courses, 'online_enrollments.rows', collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold"><?php echo e($e->user->name ?? '—'); ?></span>
                                <span class="text-slate-500"> — <?php echo e($e->course->title ?? '—'); ?></span>
                                <span class="text-slate-400"> — <?php echo e($e->enrolled_at?->format('Y-m-d')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-slate-500">—</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">طلبات شراء / Orders</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: <?php echo e((int) data_get($courses, 'orders.count', 0)); ?></p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = data_get($courses, 'orders.rows', collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold"><?php echo e($o->user->name ?? '—'); ?></span>
                                <span class="text-slate-500">
                                    — <?php echo e($o->course->title ?? ($o->learningPath->name ?? '—')); ?>

                                </span>
                                <span class="text-slate-400"> — <?php echo e($o->created_at?->format('Y-m-d')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-slate-500">—</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">حجوزات أوفلاين (Offline bookings)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: <?php echo e((int) data_get($courses, 'offline_bookings.count', 0)); ?></p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = data_get($courses, 'offline_bookings.rows', collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold"><?php echo e($b->user->name ?? '—'); ?></span>
                                <span class="text-slate-500"> — <?php echo e($b->course->title ?? '—'); ?></span>
                                <span class="text-slate-400"> — جروب: <?php echo e($b->assignedGroup->name ?? ($b->requestedGroup->name ?? '—')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-slate-500">—</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">تسجيلات أوفلاين (Enrollments)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: <?php echo e((int) data_get($courses, 'offline_enrollments.count', 0)); ?></p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = data_get($courses, 'offline_enrollments.rows', collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $en): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold"><?php echo e($en->student->name ?? '—'); ?></span>
                                <span class="text-slate-500"> — <?php echo e($en->course->title ?? '—'); ?></span>
                                <span class="text-slate-400"> — <?php echo e(($en->enrollment_channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين'); ?> — جروب: <?php echo e($en->group->name ?? '—'); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-slate-500">—</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
            <h3 class="text-sm font-black text-gray-900 mb-2">سجل النظام (عينة)</h3>
            <?php if($auditSample->isEmpty()): ?>
                <p class="text-xs text-gray-500">لا توجد سجلات في الفترة.</p>
            <?php else: ?>
                <ul class="text-xs space-y-2 text-gray-700">
                    <?php $__currentLoopData = $auditSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="border-b border-gray-100 pb-2">
                            <span class="font-bold"><?php echo e($log->user->name ?? '—'); ?></span>
                            <span class="text-gray-400"> — <?php echo e($log->created_at?->format('Y-m-d H:i')); ?></span>
                            <div class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e(\Illuminate\Support\Str::limit($log->description ?? $log->action, 180)); ?></div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadInsightsPdf() {
        const element = document.getElementById('insights-pdf-root');
        const opt = {
            margin: 8,
            filename: 'sales-insights-<?php echo e($rep->id); ?>-<?php echo e($start->format('Y-m-d')); ?>-<?php echo e($end->format('Y-m-d')); ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    (function () {
        const sel = document.getElementById('period_sel');
        const from = document.getElementById('date_from');
        const to = document.getElementById('date_to');
        if (!sel || !from || !to) return;

        function toggleCustom() {
            const isCustom = sel.value === 'custom';
            from.disabled = !isCustom;
            to.disabled = !isCustom;
        }

        sel.addEventListener('change', toggleCustom);
        toggleCustom();
    })();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/insights/index.blade.php ENDPATH**/ ?>