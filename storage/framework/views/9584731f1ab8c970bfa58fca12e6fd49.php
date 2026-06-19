

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'العملاء المحتملون'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .leads-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
    .leads-panel input, .leads-panel select {
        border: 1px solid #cbd5e1; border-radius: 6px;
    }
    .leads-panel input:focus, .leads-panel select:focus {
        outline: none; border-color: #64748b;
        box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.12);
    }
    .preset-link {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600;
        border: 1px solid #e2e8f0; color: #475569; background: #fff;
    }
    .preset-link.active { background: #1e293b; color: #fff; border-color: #1e293b; }
    .preset-link .cnt { font-size: 0.7rem; opacity: 0.85; font-weight: 700; }
    .act-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 6px; border: 1px solid #e2e8f0;
        color: #475569; background: #fff; font-size: 0.8rem;
    }
    .act-btn:hover { background: #f8fafc; border-color: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $req = request();
    $activePreset = 'all';
    if ($req->boolean('stale')) {
        $activePreset = 'stale';
    } elseif ($req->get('follow_up') === 'today') {
        $activePreset = 'today';
    } elseif ($req->get('follow_up') === 'overdue') {
        $activePreset = 'overdue';
    } elseif ($req->get('stage') === 'new' && ! $req->hasAny(['follow_up', 'priority', 'search', 'category_id'])) {
        $activePreset = 'new';
    }

    $presets = [
        'all' => ['label' => 'الكل', 'url' => route('employee.sales.leads.index'), 'count' => null],
        'today' => ['label' => 'متابعات اليوم', 'url' => route('employee.sales.leads.index', ['follow_up' => 'today', 'sort' => 'follow_up']), 'count' => $quickCounts['today'] ?? 0],
        'overdue' => ['label' => 'متأخرة', 'url' => route('employee.sales.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up']), 'count' => $quickCounts['overdue'] ?? 0],
        'stale' => ['label' => 'بلا تواصل', 'url' => route('employee.sales.leads.index', ['stale' => 1, 'sort' => 'last_contact']), 'count' => $quickCounts['stale'] ?? 0],
        'new' => ['label' => 'جديد', 'url' => route('employee.sales.leads.index', ['stage' => 'new']), 'count' => $quickCounts['new'] ?? 0],
    ];

    $redirectTo = url()->full();

    $waPhone = function (?string $phone): ?string {
        if (! $phone) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '20'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '20')) {
            $digits = '20'.$digits;
        }

        return 'https://wa.me/'.$digits;
    };
?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">العملاء المحتملون</h2>
            <p class="text-sm text-slate-500 mt-0.5">فلاتر فورية — إجراءات من الجدول مباشرة</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="text-sm text-slate-600 hover:text-slate-900">مركز المبيعات</a>
            <a href="<?php echo e(route('employee.sales.leads.create')); ?>"
               class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900">
                + عميل جديد
            </a>
        </div>
    </div>

    
    <div class="flex flex-wrap gap-2">
        <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($preset['url']); ?>"
               class="preset-link <?php echo e($activePreset === $key ? 'active' : ''); ?>">
                <?php echo e($preset['label']); ?>

                <?php if($preset['count'] !== null): ?>
                    <span class="cnt">(<?php echo e($preset['count']); ?>)</span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="leads-panel p-4" x-data="{ showMore: <?php echo e($req->hasAny(['category_id','import_batch','stage','priority','stale']) && ! in_array($activePreset, ['today','overdue','stale','new']) ? 'true' : 'false'); ?> }">
        <form method="get" id="leads-filter-form" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5 lg:col-span-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1">بحث</label>
                    <input type="search" name="search" value="<?php echo e($req->search); ?>"
                           placeholder="اسم، هاتف، بريد..."
                           class="w-full px-3 py-2 text-sm"
                           @keydown.enter.prevent="$el.form.submit()">
                </div>
                <div class="md:col-span-3 lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">المتابعة</label>
                    <select name="follow_up" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="overdue" <?php if($req->follow_up === 'overdue'): echo 'selected'; endif; ?>>متأخرة</option>
                        <option value="today" <?php if($req->follow_up === 'today'): echo 'selected'; endif; ?>>اليوم</option>
                        <option value="week" <?php if($req->follow_up === 'week'): echo 'selected'; endif; ?>>خلال أسبوع</option>
                        <option value="none" <?php if($req->follow_up === 'none'): echo 'selected'; endif; ?>>بدون موعد</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">ترتيب</label>
                    <select name="sort" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="" <?php if(! $req->sort): echo 'selected'; endif; ?>>آخر تحديث</option>
                        <option value="follow_up" <?php if($req->sort === 'follow_up'): echo 'selected'; endif; ?>>أقرب متابعة</option>
                        <option value="priority" <?php if($req->sort === 'priority'): echo 'selected'; endif; ?>>الأولوية</option>
                        <option value="last_contact" <?php if($req->sort === 'last_contact'): echo 'selected'; endif; ?>>آخر تواصل</option>
                        <option value="value" <?php if($req->sort === 'value'): echo 'selected'; endif; ?>>القيمة</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">بحث</button>
                    <?php if($req->hasAny(['stage','priority','follow_up','sort','stale','search','category_id','import_batch'])): ?>
                        <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="px-3 py-2 text-sm text-slate-600">مسح</a>
                    <?php endif; ?>
                    <button type="button" @click="showMore = !showMore" class="px-3 py-2 text-sm text-slate-600 border border-slate-200 rounded-lg">
                        المزيد
                    </button>
                </div>
            </div>

            <div x-show="showMore" x-cloak class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">المرحلة</label>
                    <select name="stage" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if($req->stage === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">الأولوية</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if($req->priority === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">التصنيف</label>
                    <select name="category_id" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php if($req->category_id == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php if(($groups ?? collect())->isNotEmpty()): ?>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">المجموعة</label>
                    <select name="group_id" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($grp->id); ?>" <?php if($req->group_id == $grp->id): echo 'selected'; endif; ?>><?php echo e($grp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if($importBatches->isNotEmpty()): ?>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">دفعة استيراد</label>
                    <select name="import_batch" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $importBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($batch); ?>" <?php if($req->import_batch === $batch): echo 'selected'; endif; ?>><?php echo e($batch); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="stale" value="1" class="rounded border-slate-300"
                               <?php if($req->boolean('stale')): echo 'checked'; endif; ?> onchange="this.form.submit()">
                        بلا تواصل <?php echo e(\App\Models\SalesLead::STALE_CONTACT_DAYS); ?>+ يوم
                    </label>
                </div>
            </div>
        </form>
    </div>

    <div class="leads-panel overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-right py-2.5 px-3 font-semibold">العميل</th>
                    <th class="text-right py-2.5 px-3 font-semibold">تواصل</th>
                    <th class="text-right py-2.5 px-3 font-semibold hidden lg:table-cell">المرحلة</th>
                    <th class="text-right py-2.5 px-3 font-semibold">متابعة</th>
                    <th class="text-right py-2.5 px-3 font-semibold hidden md:table-cell">آخر تواصل</th>
                    <th class="text-right py-2.5 px-3 font-semibold w-44">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $rowBg = '';
                    if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                        $rowBg = 'bg-red-50/70';
                    } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                        $rowBg = 'bg-amber-50/50';
                    }
                    $wa = $waPhone($lead->phone);
                ?>
                <tr class="<?php echo e($rowBg); ?>">
                    <td class="py-2.5 px-3">
                        <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="font-semibold text-slate-900 hover:underline">
                            <?php echo e($lead->name); ?>

                        </a>
                        <?php if($lead->category): ?>
                            <span class="block text-[11px] text-slate-500 mt-0.5"><?php echo e($lead->category->name); ?></span>
                        <?php endif; ?>
                        <span class="lg:hidden block text-[11px] text-slate-500"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span>
                    </td>
                    <td class="py-2.5 px-3 text-slate-700">
                        <?php if($lead->phone): ?>
                            <a href="tel:<?php echo e(preg_replace('/\s+/', '', $lead->phone)); ?>" class="font-medium hover:underline" dir="ltr"><?php echo e($lead->phone); ?></a>
                        <?php else: ?>
                            <span class="text-slate-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-2.5 px-3 hidden lg:table-cell text-slate-700"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></td>
                    <td class="py-2.5 px-3 text-xs whitespace-nowrap <?php if($lead->isFollowUpOverdue()): ?> text-red-700 font-semibold <?php else: ?> text-slate-600 <?php endif; ?>">
                        <?php echo e($lead->next_follow_up_at?->format('m-d H:i') ?? '—'); ?>

                    </td>
                    <td class="py-2.5 px-3 hidden md:table-cell text-xs text-slate-500 whitespace-nowrap">
                        <?php echo e($lead->last_contacted_at?->format('m-d H:i') ?? '—'); ?>

                    </td>
                    <td class="py-2.5 px-3">
                        <div class="flex flex-wrap items-center gap-1">
                            <?php if($lead->phone): ?>
                                <a href="tel:<?php echo e(preg_replace('/\s+/', '', $lead->phone)); ?>" class="act-btn" title="اتصال"><i class="fas fa-phone"></i></a>
                            <?php endif; ?>
                            <?php if($wa): ?>
                                <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="act-btn" title="واتساب"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                            <form method="post" action="<?php echo e(route('employee.sales.leads.quick-activity', $lead)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="type" value="call">
                                <input type="hidden" name="redirect_to" value="<?php echo e($redirectTo); ?>">
                                <button type="submit" class="act-btn" title="سجّل مكالمة"><i class="fas fa-phone-volume"></i></button>
                            </form>
                            <form method="post" action="<?php echo e(route('employee.sales.leads.quick-activity', $lead)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="type" value="follow_up">
                                <input type="hidden" name="redirect_to" value="<?php echo e($redirectTo); ?>">
                                <button type="submit" class="act-btn" title="متابعة غداً 10:00"><i class="fas fa-redo"></i></button>
                            </form>
                            <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="act-btn" title="عرض"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo e(route('employee.sales.leads.edit', $lead)); ?>" class="act-btn" title="تعديل"><i class="fas fa-pen"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-500">
                        لا توجد سجلات —
                        <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="text-slate-800 font-semibold underline">أضف عميلاً</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($leads->hasPages()): ?>
        <div><?php echo e($leads->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/leads/index.blade.php ENDPATH**/ ?>