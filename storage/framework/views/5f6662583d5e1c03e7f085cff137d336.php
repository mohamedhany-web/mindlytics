<?php $__env->startSection('title', 'العملاء المحتملون — المبيعات'); ?>
<?php $__env->startSection('header', 'المبيعات — العملاء المحتملون'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي النتائج', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-users', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'حسب الفلتر الحالي'],
        ['label' => 'قيد المتابعة', 'value' => number_format($stats['open'] ?? 0), 'icon' => 'fas fa-briefcase', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'Leads مفتوحة'],
        ['label' => 'متابعات متأخرة', 'value' => number_format($stats['overdue_followups'] ?? 0), 'icon' => 'fas fa-clock', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تحتاج إجراء'],
        ['label' => 'صفقات فوز', 'value' => number_format($stats['won'] ?? 0), 'icon' => 'fas fa-trophy', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'مرحلة Won'],
    ];

    $priorityBadges = [
        'urgent' => ['label' => null, 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'high' => ['label' => null, 'classes' => 'bg-orange-100 text-orange-700 border border-orange-200'],
        'low' => ['label' => null, 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
        'normal' => ['label' => null, 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
    ];

    $hasFilters = request()->hasAny(['assigned_to', 'stage', 'priority', 'follow_up', 'sort', 'stale', 'search', 'category_id', 'import_batch']);
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(!empty(session('import_errors'))): ?>
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 max-h-48 overflow-y-auto space-y-1">
            <p class="font-bold"><i class="fas fa-exclamation-triangle ml-1"></i> تفاصيل التخطي (<?php echo e(count(session('import_errors'))); ?>)</p>
            <?php $__currentLoopData = array_slice(session('import_errors'), 0, 20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="text-xs"><?php echo e($err); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(count(session('import_errors')) > 20): ?>
                <p class="text-xs font-semibold">... و <?php echo e(count(session('import_errors')) - 20); ?> أخرى</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">العملاء المحتملون</h2>
                    <p class="text-xs text-slate-600">إدارة Leads، المتابعات، الأولويات، وإسناد موظفي المبيعات.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.audit-log.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-history text-slate-500"></i>
                    سجل الأنشطة
                </a>
                <a href="<?php echo e(route('admin.sales.transfer.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-exchange-alt text-violet-600"></i>
                    تحويل بيانات
                </a>
                <a href="<?php echo e(route('admin.sales.leads.import')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-violet-600 hover:bg-violet-700">
                    <i class="fas fa-file-upload"></i>
                    استيراد دفعة
                </a>
                <a href="<?php echo e(route('admin.sales.leads.export', request()->query())); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-sky-600 hover:bg-sky-700">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </a>
                <a href="<?php echo e(route('admin.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-plus"></i>
                    عميل جديد
                </a>
            </div>
        </div>

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

        <?php if($hasFilters): ?>
            <div class="px-4 pb-4">
                <p class="text-xs text-slate-600 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    <i class="fas fa-filter text-amber-600 ml-1"></i>
                    فلتر نشط — يعرض <strong><?php echo e(number_format($leads->total())); ?></strong> نتيجة مطابقة.
                </p>
            </div>
        <?php endif; ?>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">تصفية حسب الموظف، المرحلة، الأولوية، المتابعة، أو البحث النصي.</p>
        </div>
        <div class="p-4">
            <form method="get" action="<?php echo e(route('admin.sales.leads.index')); ?>" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">موظف المبيعات</label>
                        <select name="assigned_to" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الكل</option>
                            <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($rep->id); ?>" <?php if(request('assigned_to') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف</label>
                        <select name="category_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الكل</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php if(request('category_id') == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">دفعة الاستيراد</label>
                        <input type="text" name="import_batch" value="<?php echo e(request('import_batch')); ?>"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="IMP-...">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المرحلة</label>
                        <select name="stage" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الكل</option>
                            <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php if(request('stage') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الأولوية</label>
                        <select name="priority" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الكل</option>
                            <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php if(request('priority') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المتابعة</label>
                        <select name="follow_up" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الكل</option>
                            <option value="overdue" <?php if(request('follow_up') === 'overdue'): echo 'selected'; endif; ?>>متأخرة</option>
                            <option value="today" <?php if(request('follow_up') === 'today'): echo 'selected'; endif; ?>>اليوم</option>
                            <option value="week" <?php if(request('follow_up') === 'week'): echo 'selected'; endif; ?>>خلال أسبوع</option>
                            <option value="none" <?php if(request('follow_up') === 'none'): echo 'selected'; endif; ?>>بدون موعد</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">ترتيب</label>
                        <select name="sort" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="" <?php if(!request('sort')): echo 'selected'; endif; ?>>آخر تحديث</option>
                            <option value="priority" <?php if(request('sort') === 'priority'): echo 'selected'; endif; ?>>الأولوية</option>
                            <option value="follow_up" <?php if(request('sort') === 'follow_up'): echo 'selected'; endif; ?>>متابعة</option>
                            <option value="last_contact" <?php if(request('sort') === 'last_contact'): echo 'selected'; endif; ?>>آخر تواصل</option>
                            <option value="value" <?php if(request('sort') === 'value'): echo 'selected'; endif; ?>>قيمة متوقعة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                        <input type="search" name="search" value="<?php echo e(request('search')); ?>"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="اسم، هاتف، بريد...">
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-slate-100">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="stale" value="1" id="stale_ad" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" <?php if(request()->boolean('stale')): echo 'checked'; endif; ?>>
                        <span>بلا تواصل <?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">
                            <i class="fas fa-search"></i>
                            تطبيق
                        </button>
                        <?php if($hasFilters): ?>
                            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" title="مسح الفلتر">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">قائمة العملاء</h3>
                <p class="text-xs text-slate-600">الصفوف المميزة: متابعة متأخرة (وردي) · بلا تواصل (كهرماني).</p>
            </div>
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200"><?php echo e(number_format($leads->total())); ?> عميل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الاسم</th>
                        <th class="px-4 py-3 text-right font-semibold">مسند إلى</th>
                        <th class="px-4 py-3 text-right font-semibold">التصنيف</th>
                        <th class="px-4 py-3 text-right font-semibold">المرحلة</th>
                        <th class="px-4 py-3 text-right font-semibold">أولوية</th>
                        <th class="px-4 py-3 text-right font-semibold">متابعة</th>
                        <th class="px-4 py-3 text-right font-semibold">آخر تواصل</th>
                        <th class="px-4 py-3 text-right font-semibold">أنشئ</th>
                        <th class="px-4 py-3 text-center font-semibold w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rowClass = 'hover:bg-slate-50';
                            if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                                $rowClass .= ' bg-rose-50/60';
                            } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                                $rowClass .= ' bg-amber-50/50';
                            }
                            $pr = $lead->priority ?? 'normal';
                            $priorityMeta = $priorityBadges[$pr] ?? $priorityBadges['normal'];
                        ?>
                        <tr class="<?php echo e($rowClass); ?>">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900"><?php echo e($lead->name); ?></p>
                                <?php if($lead->phone || $lead->email): ?>
                                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e($lead->phone ?: $lead->email); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($lead->assignee->name ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($lead->category): ?>
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold border" style="color: <?php echo e($lead->category->color); ?>; border-color: <?php echo e($lead->category->color); ?>33; background: <?php echo e($lead->category->color); ?>15"><?php echo e($lead->category->name); ?></span>
                                    <?php if($lead->import_batch): ?>
                                        <p class="text-[10px] text-slate-400 mt-0.5"><?php echo e($lead->import_batch); ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?php echo e($priorityMeta['classes']); ?>">
                                    <?php echo e(\App\Models\SalesLead::priorityLabel($pr)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs tabular-nums <?php if($lead->isFollowUpOverdue()): ?> text-rose-700 font-semibold <?php else: ?> text-slate-600 <?php endif; ?>">
                                <?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 tabular-nums"><?php echo e($lead->last_contacted_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-500 tabular-nums"><?php echo e($lead->created_at->format('Y-m-d')); ?></td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?php echo e(route('admin.sales.leads.show', $lead)); ?>"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-emerald-600 hover:bg-emerald-50 text-sm"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-user-tag text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا توجد سجلات</p>
                                <p class="text-xs text-slate-500 mt-1">لا يوجد عملاء محتملون أو لا توجد نتائج للفلتر.</p>
                                <a href="<?php echo e(route('admin.sales.leads.create')); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">
                                    <i class="fas fa-plus"></i>
                                    إضافة عميل
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leads->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($leads->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/leads/index.blade.php ENDPATH**/ ?>