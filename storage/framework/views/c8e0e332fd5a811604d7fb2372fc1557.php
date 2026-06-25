<?php $__env->startSection('title', $lead->name); ?>
<?php $__env->startSection('header', 'عميل محتمل: ' . $lead->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $pr = $lead->priority ?? 'normal';
    $priorityBadges = [
        'urgent' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'high' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'low' => 'bg-slate-100 text-slate-700 border border-slate-200',
        'normal' => 'bg-slate-100 text-slate-700 border border-slate-200',
    ];
    $priorityClass = $priorityBadges[$pr] ?? $priorityBadges['normal'];

    $infoCards = [
        ['label' => 'قيمة متوقعة', 'value' => $lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—', 'icon' => 'fas fa-coins', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'متابعة تالية', 'value' => $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-calendar-check', 'bg' => $lead->isFollowUpOverdue() ? 'bg-rose-100' : 'bg-sky-100', 'text' => $lead->isFollowUpOverdue() ? 'text-rose-600' : 'text-sky-600'],
        ['label' => 'آخر تواصل', 'value' => $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['label' => 'أنشطة', 'value' => number_format($lead->activities->count()), 'icon' => 'fas fa-list', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(!empty(session('sales_duplicate_warnings'))): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-950 px-4 py-3 text-sm space-y-1">
            <?php $__currentLoopData = session('sales_duplicate_warnings'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p><i class="fas fa-exclamation-triangle ml-1 text-amber-600"></i><?php echo e($w); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-black text-slate-900 truncate"><?php echo e($lead->name); ?></h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?>

                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?php echo e($priorityClass); ?>">
                            <?php echo e(\App\Models\SalesLead::priorityLabel($pr)); ?>

                        </span>
                        <span class="text-xs text-slate-500"><?php echo e(\App\Models\SalesLead::sourceLabel($lead->source)); ?></span>
                    </div>
                    <p class="text-xs text-slate-600 mt-2">
                        مسند إلى: <strong><?php echo e($lead->assignee->name ?? '—'); ?></strong>
                        <?php if($lead->creator): ?>
                            · أنشأ: <?php echo e($lead->creator->name); ?>

                        <?php endif; ?>
                        · <?php echo e($lead->created_at->format('Y-m-d')); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    القائمة
                </a>
                <a href="<?php echo e(route('admin.sales.leads.edit', $lead)); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-edit text-sky-600"></i>
                    تعديل / إعادة إسناد
                </a>
                <form action="<?php echo e(route('admin.sales.leads.destroy', $lead)); ?>" method="post" onsubmit="return confirm('حذف نهائياً؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-700 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100">
                        <i class="fas fa-trash-alt"></i>
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $infoCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-lg font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-xs"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-address-card text-sky-600"></i>
                    بيانات العميل
                </h3>
            </div>
            <div class="p-4 sm:p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الهاتف</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($lead->phone ?? '—'); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">البريد</dt>
                        <dd class="font-semibold text-slate-900 break-all"><?php echo e($lead->email ?? '—'); ?></dd>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 sm:col-span-2">
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الشركة</dt>
                        <dd class="font-semibold text-slate-900"><?php echo e($lead->company ?? '—'); ?></dd>
                    </div>
                </dl>

                <?php if($lead->interest): ?>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">الاهتمام</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($lead->interest); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($lead->notes): ?>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($lead->notes); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($lead->stage === 'lost' && $lead->lost_reason): ?>
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50/50 px-4 py-3">
                        <p class="text-xs font-semibold text-rose-700 mb-1">سبب الخسارة</p>
                        <p class="text-sm text-slate-800"><?php echo e($lead->lost_reason); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        
        <?php if($lead->stage === 'won'): ?>
            <section class="rounded-2xl bg-white border border-emerald-200 shadow-lg overflow-hidden h-full">
                <div class="px-4 py-3 border-b border-emerald-200 bg-emerald-50/70 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-trophy text-emerald-600"></i>
                        اعتماد الفوز وصرف الكوميشن
                    </h3>
                    <?php if($lead->won_confirmed_at): ?>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                            معتمد <?php echo e($lead->won_confirmed_at->format('Y-m-d H:i')); ?>

                        </span>
                    <?php else: ?>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 border border-amber-200">غير معتمد</span>
                    <?php endif; ?>
                </div>
                <div class="p-4 sm:p-5">
                    <?php if($lead->won_confirmed_at): ?>
                        <dl class="space-y-3 text-sm">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50/40 px-3 py-2.5 flex justify-between gap-3">
                                <dt class="text-slate-600">الكوميشن</dt>
                                <dd class="font-bold text-emerald-800 tabular-nums"><?php echo e(number_format((float) ($lead->commission_amount ?? 0), 2)); ?> ج.م</dd>
                            </div>
                            <?php if($lead->commission_transaction_id): ?>
                                <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                                    <dt class="text-slate-600">رقم القيد</dt>
                                    <dd class="font-semibold text-slate-900"><?php echo e($lead->commission_transaction_id); ?></dd>
                                </div>
                            <?php endif; ?>
                        </dl>
                        <?php if($lead->commission_notes): ?>
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold text-slate-600 mb-1">ملاحظات الكوميشن</p>
                                <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($lead->commission_notes); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php
                            $rep = $lead->assignee;
                            $base = (float) ($lead->expected_value ?? 0);
                            $defaultCommission = $rep ? $rep->calculateSalesCommissionAmount($base) : 0;
                        ?>
                        <form method="post" action="<?php echo e(route('admin.sales.leads.confirm-win', $lead)); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة المرجعية</label>
                                    <input type="text" value="<?php echo e(number_format($base, 2)); ?> ج.م" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                    <?php if($rep): ?>
                                        <p class="text-[11px] text-slate-500 mt-1">إعداد الموظف: <strong><?php echo e($rep->salesCommissionLabel()); ?></strong></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">مبلغ الكوميشن</label>
                                    <input type="number" step="0.01" min="0" name="commission_amount" value="<?php echo e(old('commission_amount', $defaultCommission)); ?>"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-[11px] text-slate-500 mt-1">اتركه للحساب الافتراضي.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات</label>
                                    <input type="text" name="commission_notes" value="<?php echo e(old('commission_notes')); ?>"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                           placeholder="اختياري">
                                </div>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                                    <i class="fas fa-check"></i>
                                    اعتماد وصرف الكوميشن
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-violet-600"></i>
                        حالة المتابعة
                    </h3>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    <?php if($lead->isOpen() && $lead->isFollowUpOverdue()): ?>
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            <strong>متابعة متأخرة</strong> — الموعد كان <?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i')); ?>.
                        </div>
                    <?php elseif($lead->isOpen() && $lead->isStaleContact()): ?>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            <i class="fas fa-hourglass-half ml-1"></i>
                            <strong>بلا تواصل منذ فترة</strong> — آخر تواصل: <?php echo e($lead->last_contacted_at?->format('Y-m-d H:i') ?? 'لم يُسجَّل'); ?>.
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <i class="fas fa-check-circle text-emerald-600 ml-1"></i>
                            لا توجد تنبيهات عاجلة على هذا Lead حالياً.
                        </div>
                    <?php endif; ?>
                    <dl class="grid grid-cols-1 gap-3 text-sm">
                        <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                            <dt class="text-slate-600">آخر تحديث</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums"><?php echo e($lead->updated_at->format('Y-m-d H:i')); ?></dd>
                        </div>
                        <?php if($lead->closed_at): ?>
                            <div class="rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 flex justify-between gap-3">
                                <dt class="text-slate-600">تاريخ الإغلاق</dt>
                                <dd class="font-semibold text-slate-900 tabular-nums"><?php echo e($lead->closed_at->format('Y-m-d H:i')); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </section>
        <?php endif; ?>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">سجل النشاط</h3>
                <p class="text-xs text-slate-600">يُسجَّل في سجل مراقبة المبيعات.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200"><?php echo e($lead->activities->count()); ?> نشاط</span>
        </div>

        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/30">
            <form method="post" action="<?php echo e(route('admin.sales.leads.activities.store', $lead)); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">النوع</label>
                        <select name="type" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <?php $__currentLoopData = \App\Models\SalesActivity::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($k !== 'stage_change'): ?>
                                    <option value="<?php echo e($k); ?>"><?php echo e($label); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان</label>
                        <input type="text" name="title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">التفاصيل</label>
                    <textarea name="body" rows="3" placeholder="اكتب تفاصيل النشاط..."
                              class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-plus"></i>
                        إضافة نشاط
                    </button>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-5">
            <?php $__empty_1 = true; $__currentLoopData = $lead->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="relative pr-4 pb-5 mb-5 last:mb-0 last:pb-0 border-r-2 border-emerald-200">
                    <div class="flex flex-wrap justify-between gap-2 text-xs text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                            <i class="fas fa-circle text-[6px]"></i>
                            <?php echo e(\App\Models\SalesActivity::typeLabel($act->type)); ?>

                        </span>
                        <span class="tabular-nums"><?php echo e($act->created_at->format('Y-m-d H:i')); ?> — <?php echo e($act->user?->name ?? '—'); ?></span>
                    </div>
                    <?php if($act->title): ?>
                        <p class="font-semibold text-slate-900 text-sm"><?php echo e($act->title); ?></p>
                    <?php endif; ?>
                    <?php if($act->body): ?>
                        <p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap"><?php echo e($act->body); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="py-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-900">لا أنشطة بعد</p>
                    <p class="text-xs text-slate-500 mt-1">أضف أول نشاط من النموذج أعلاه.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/leads/show.blade.php ENDPATH**/ ?>