

<?php $__env->startSection('title', $lead->name); ?>
<?php $__env->startSection('header', $lead->name); ?>

<?php
    $activityStyles = [
        'note' => ['icon' => 'fas fa-note-sticky', 'bubble' => 'bg-amber-100 text-amber-800', 'accent' => 'border-amber-300'],
        'call' => ['icon' => 'fas fa-phone', 'bubble' => 'bg-sky-100 text-sky-800', 'accent' => 'border-sky-300'],
        'meeting' => ['icon' => 'fas fa-users', 'bubble' => 'bg-violet-100 text-violet-800', 'accent' => 'border-violet-300'],
        'whatsapp' => ['icon' => 'fab fa-whatsapp', 'bubble' => 'bg-green-100 text-green-800', 'accent' => 'border-green-400'],
        'email' => ['icon' => 'fas fa-envelope', 'bubble' => 'bg-slate-100 text-slate-800', 'accent' => 'border-slate-300'],
        'stage_change' => ['icon' => 'fas fa-shuffle', 'bubble' => 'bg-indigo-100 text-indigo-800', 'accent' => 'border-indigo-300'],
        'other' => ['icon' => 'fas fa-ellipsis', 'bubble' => 'bg-gray-100 text-gray-800', 'accent' => 'border-gray-300'],
    ];
    $acts = $lead->activities;
    $actCount = $acts->count();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-10">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('employee.sales.leads.edit', $lead)); ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">تعديل بيانات العميل</a>
            <form action="<?php echo e(route('employee.sales.leads.destroy', $lead)); ?>" method="post" onsubmit="return confirm('حذف هذا السجل؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-medium">حذف</button>
            </form>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        <div class="lg:col-span-8 space-y-6 order-1">
            <section class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-br from-emerald-50 via-white to-teal-50/30 shadow-md overflow-hidden" aria-labelledby="activities-heading">
                <div class="px-5 sm:px-8 py-6 border-b border-emerald-100/80 bg-white/60 backdrop-blur-sm">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 id="activities-heading" class="text-2xl font-bold text-gray-900 tracking-tight">سجل النشاطات</h2>
                            <p class="text-sm text-gray-600 mt-1 max-w-xl">كل تواصل أو ملاحظة تُسجَّل هنا يبني تاريخاً واضحاً للعميل. استخدم النموذج أسفل العنوان لإضافة نشاط بسرعة.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-sm">
                                <i class="fas fa-list-ul"></i>
                                <?php echo e($actCount); ?> <?php echo e($actCount === 1 ? 'نشاط' : 'أنشطة'); ?>

                            </span>
                            <?php if($acts->isNotEmpty()): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-900 text-xs font-semibold">
                                    آخر نشاط: <?php echo e($acts->first()->created_at->diffForHumans()); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-8 space-y-6">
                    <form method="post" action="<?php echo e(route('employee.sales.leads.activities.store', $lead)); ?>" class="rounded-xl border border-emerald-200 bg-white p-5 sm:p-6 shadow-sm space-y-4">
                        <?php echo csrf_field(); ?>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white text-sm"><i class="fas fa-plus"></i></span>
                            تسجيل نشاط جديد
                        </h3>
                        <?php if($errors->any()): ?>
                            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm px-3 py-2">
                                <ul class="list-disc list-inside space-y-0.5">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">نوع النشاط</label>
                                <select name="type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                    <?php $__currentLoopData = \App\Models\SalesActivity::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($k !== 'stage_change'): ?>
                                            <option value="<?php echo e($k); ?>"><?php echo e($label); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="sm:col-span-8">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان مختصر <span class="text-gray-400 font-normal">(اختياري)</span></label>
                                <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="مثال: متابعة عرض السعر" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">التفاصيل</label>
                            <textarea name="body" rows="5" placeholder="اكتب ما دار في المكالمة، أو موعد الاجتماع القادم، أو أي ملاحظة مهمة…" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"><?php echo e(old('body')); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1.5">يُنصح بتوثيق النتيجة وخطوة المتابعة التالية في كل نشاط.</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md transition-colors">
                            <i class="fas fa-paper-plane"></i>
                            حفظ النشاط في السجل
                        </button>
                    </form>

                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">الخط الزمني</h3>
                        <?php $__empty_1 = true; $__currentLoopData = $acts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $s = $activityStyles[$act->type] ?? $activityStyles['other'];
                                $isLast = $index === $acts->count() - 1;
                            ?>
                            <div class="flex gap-4 sm:gap-5 <?php echo e($isLast ? '' : 'pb-2'); ?>">
                                <div class="flex flex-col items-center shrink-0 w-12 sm:w-14">
                                    <span class="flex h-11 w-11 sm:h-12 sm:w-12 items-center justify-center rounded-2xl shadow-sm border-2 border-white <?php echo e($s['bubble']); ?>" title="<?php echo e(\App\Models\SalesActivity::typeLabel($act->type)); ?>">
                                        <i class="<?php echo e($s['icon']); ?> text-base sm:text-lg"></i>
                                    </span>
                                    <?php if (! ($isLast)): ?>
                                        <span class="w-0.5 flex-1 min-h-[1.25rem] mt-2 rounded-full bg-gradient-to-b from-emerald-200 to-emerald-100" aria-hidden="true"></span>
                                    <?php endif; ?>
                                </div>
                                <article class="flex-1 min-w-0 rounded-2xl border-2 <?php echo e($s['accent']); ?> bg-white px-4 py-4 sm:px-5 sm:py-5 shadow-sm mb-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 gap-y-1">
                                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-900">
                                            <?php echo e(\App\Models\SalesActivity::typeLabel($act->type)); ?>

                                        </span>
                                        <time class="text-xs text-gray-500 font-medium tabular-nums" datetime="<?php echo e($act->created_at->toIso8601String()); ?>">
                                            <?php echo e($act->created_at->format('Y-m-d H:i')); ?>

                                            <span class="text-gray-400">·</span>
                                            <?php echo e($act->user->name ?? '—'); ?>

                                        </time>
                                    </div>
                                    <?php if($act->title): ?>
                                        <p class="font-semibold text-gray-900 mt-2 text-base leading-snug"><?php echo e($act->title); ?></p>
                                    <?php endif; ?>
                                    <?php if($act->body): ?>
                                        <div class="mt-2 text-sm sm:text-[15px] text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo e($act->body); ?></div>
                                    <?php endif; ?>
                                </article>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/40 px-6 py-14 text-center">
                                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-2xl mb-4">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <p class="text-gray-800 font-semibold text-lg">لا توجد أنشطة بعد</p>
                                <p class="text-gray-600 text-sm mt-2 max-w-md mx-auto">ابدأ بأول مكالمة أو ملاحظة باستخدام النموذج أعلاه — سيظهر كل شيء هنا بترتيب زمني.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        
        <aside class="lg:col-span-4 space-y-4 order-2 lg:sticky lg:top-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span>
                    <span class="text-sm text-gray-500"><?php echo e(\App\Models\SalesLead::sourceLabel($lead->source)); ?></span>
                </div>
                <h2 class="text-lg font-bold text-gray-900"><?php echo e($lead->name); ?></h2>
                <dl class="space-y-3 text-sm border-t border-gray-100 pt-4">
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">الهاتف</dt><dd class="font-medium text-gray-900 text-left sm:text-right break-all"><?php echo e($lead->phone ?? '—'); ?></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">البريد</dt><dd class="font-medium text-gray-900 text-left sm:text-right break-all"><?php echo e($lead->email ?? '—'); ?></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">الشركة</dt><dd class="font-medium text-gray-900"><?php echo e($lead->company ?? '—'); ?></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-gray-500 shrink-0">قيمة متوقعة</dt><dd class="font-medium text-gray-900"><?php echo e($lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—'); ?></dd></div>
                    <div>
                        <dt class="text-gray-500 mb-1">متابعة تالية</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
                    </div>
                </dl>
                <?php if($lead->interest): ?>
                    <div class="rounded-xl bg-amber-50/80 border border-amber-100 p-3">
                        <p class="text-xs font-semibold text-amber-900 mb-1">الاهتمام</p>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($lead->interest); ?></p>
                    </div>
                <?php endif; ?>
                <?php if($lead->notes): ?>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-xs font-semibold text-gray-600 mb-1">ملاحظات</p>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($lead->notes); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/leads/show.blade.php ENDPATH**/ ?>