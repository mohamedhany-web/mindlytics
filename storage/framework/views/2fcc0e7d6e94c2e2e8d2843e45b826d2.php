<?php $__env->startSection('title', 'تحويل بيانات موظف — المبيعات'); ?>
<?php $__env->startSection('header', 'المبيعات — تحويل بيانات موظف'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500';

    $summaryCards = $fromRep && $stats ? [
        ['label' => 'عملاء محتملون', 'value' => number_format($stats['leads_total'] ?? 0), 'icon' => 'fas fa-user-tag', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'Leads مسندة'],
        ['label' => 'أنشطة CRM', 'value' => number_format($stats['activities_total'] ?? 0), 'icon' => 'fas fa-tasks', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'سجل الأنشطة'],
        ['label' => 'سجل المراقبة', 'value' => number_format($stats['audit_total'] ?? 0), 'icon' => 'fas fa-history', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'Audit logs'],
        ['label' => 'أهداف KPI', 'value' => number_format($stats['kpi_targets_total'] ?? 0), 'icon' => 'fas fa-bullseye', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'إن وُجدت'],
    ] : [];
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
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تحويل بيانات موظف مبيعات</h2>
                    <p class="text-xs text-slate-600">نقل Leads، الأنشطة، سجل المراقبة، وأهداف KPI من موظف إلى آخر.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-user-tag text-emerald-600"></i>
                العملاء المحتملون
            </a>
        </div>

        <?php if($fromRep && !empty($summaryCards)): ?>
            <div class="px-4 pt-4 pb-2">
                <p class="text-xs text-slate-600 mb-3">
                    ملخص بيانات: <strong><?php echo e($fromRep->name); ?></strong>
                    · Won معتمد: <strong><?php echo e(number_format($stats['won_confirmed_total'] ?? 0)); ?></strong>
                    · أنشأها: <strong><?php echo e(number_format($stats['created_by_total'] ?? 0)); ?></strong>
                </p>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4 pt-0">
                <?php $__currentLoopData = $summaryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
        <?php endif; ?>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-search text-sky-600"></i>
                معاينة بيانات الموظف
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">اختر موظفاً لعرض ملخص ما سيتم تحويله.</p>
        </div>
        <div class="p-4">
            <form method="get" action="<?php echo e(route('admin.sales.transfer.index')); ?>" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">موظف المصدر (من)</label>
                    <select name="from_user_id" class="<?php echo e($inputClass); ?>">
                        <option value="">— اختر موظفاً —</option>
                        <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rep->id); ?>" <?php if((string)$fromId === (string)$rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white">
                    <i class="fas fa-search"></i>
                    عرض الملخص
                </button>
            </form>
        </div>
    </section>

    <?php if($fromRep && $stats): ?>
        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-slate-900">تفصيل المراحل — <?php echo e($fromRep->name); ?></h3>
                    <p class="text-xs text-slate-600">توزيع Leads حسب مرحلة الصفقة.</p>
                </div>
                <?php if(session('transfer_summary')): ?>
                    <?php $s = session('transfer_summary'); ?>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">آخر تحويل مسجّل</span>
                <?php endif; ?>
            </div>

            <?php if(session('transfer_summary')): ?>
                <?php $s = session('transfer_summary'); ?>
                <div class="px-4 pt-4">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 text-sm text-emerald-900">
                        <p class="font-bold mb-2"><i class="fas fa-check-double ml-1"></i> ملخص آخر تحويل</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
                            <div><span class="text-emerald-700">Leads:</span> <strong class="tabular-nums"><?php echo e((int)($s['leads_assigned'] ?? 0)); ?></strong></div>
                            <div><span class="text-emerald-700">Activities:</span> <strong class="tabular-nums"><?php echo e((int)($s['activities'] ?? 0)); ?></strong></div>
                            <div><span class="text-emerald-700">Audit:</span> <strong class="tabular-nums"><?php echo e((int)($s['audit_logs'] ?? 0)); ?></strong></div>
                            <div><span class="text-emerald-700">KPI moved:</span> <strong class="tabular-nums"><?php echo e((int)($s['kpi_targets_moved'] ?? 0)); ?></strong></div>
                            <div><span class="text-emerald-700">KPI conflicts:</span> <strong class="tabular-nums"><?php echo e((int)($s['kpi_targets_conflicts'] ?? 0)); ?></strong></div>
                            <div><span class="text-emerald-700">Won confirmed:</span> <strong class="tabular-nums"><?php echo e((int)($s['leads_won_confirmed_by'] ?? 0)); ?></strong></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                <?php $__currentLoopData = \App\Models\SalesLead::STAGES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $c = (int) (($stats['leads_by_stage'][$k] ?? 0)); ?>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-center sm:text-right">
                        <p class="text-[11px] font-semibold text-slate-500 truncate"><?php echo e($label); ?></p>
                        <p class="text-xl font-black text-slate-900 tabular-nums mt-1"><?php echo e(number_format($c)); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-random text-violet-600"></i>
                تنفيذ التحويل
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">اختر الموظف المصدر والوجهة، ثم أكّد العملية.</p>
        </div>

        <form method="post" action="<?php echo e(route('admin.sales.transfer.store')); ?>" class="p-4 sm:p-6 space-y-5">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        <i class="fas fa-arrow-right text-rose-500 ml-0.5"></i>
                        من (موظف مبيعات)
                    </label>
                    <select name="from_user_id" required class="<?php echo e($inputClass); ?>">
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rep->id); ?>" <?php if(old('from_user_id', $fromId) == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-2">سيتم نقل كل بيانات المبيعات المرتبطة بهذا الموظف.</p>
                    <?php $__errorArgs = ['from_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        <i class="fas fa-arrow-left text-emerald-500 ml-0.5"></i>
                        إلى (موظف مبيعات)
                    </label>
                    <select name="to_user_id" required class="<?php echo e($inputClass); ?>">
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rep->id); ?>" <?php if(old('to_user_id') == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-2">سيصبح المسؤول عن العملاء والأنشطة بعد التحويل.</p>
                    <?php $__errorArgs = ['to_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4">
                <p class="text-sm font-bold text-amber-900 mb-1">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    تنبيه مهم
                </p>
                <p class="text-sm text-amber-900/90 leading-relaxed">
                    هذا الإجراء يعدّل بيانات قاعدة البيانات بشكل جماعي ولا يمكن التراجع عنه تلقائياً.
                    تأكد من اختيار الموظفين بشكل صحيح قبل التنفيذ.
                </p>
                <label class="mt-3 inline-flex items-start gap-2 text-sm font-semibold text-amber-900 cursor-pointer">
                    <input type="checkbox" name="confirm" value="1" class="rounded border-amber-300 mt-0.5 text-amber-600 focus:ring-amber-400" <?php if(old('confirm')): echo 'checked'; endif; ?>>
                    <span>أؤكد أنني أريد تحويل جميع بيانات الموظف المحدد</span>
                </label>
                <?php $__errorArgs = ['confirm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    <i class="fas fa-shield-alt text-violet-600 ml-0.5"></i>
                    يُسجَّل التحويل في سجل مراقبة المبيعات.
                </p>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-semibold">
                    <i class="fas fa-random"></i>
                    تحويل البيانات الآن
                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/transfer/index.blade.php ENDPATH**/ ?>