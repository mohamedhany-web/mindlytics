<?php
    $s = $settings;
    $layout = $layout ?? 'compact';
    $cancelUrl = $cancelUrl ?? route('admin.sales.daily-reports.index');
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $penaltyInputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400';
?>

<form method="POST" action="<?php echo e($formAction); ?>" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php if(($method ?? 'POST') === 'PUT'): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <?php if($layout === 'sections'): ?>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-sky-600"></i>
                    الإعدادات العامة
                </h3>
                <p class="text-xs text-slate-600 mt-0.5">تفعيل التقرير، أيام العمل، الموعد النهائي، وهدف الالتزام.</p>
            </div>
            <div class="p-4 sm:p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer hover:border-sky-200">
                        <input type="checkbox" name="enabled" value="1" class="rounded border-slate-300 mt-1 text-sky-600 focus:ring-sky-500" <?php if($s['enabled'] ?? true): echo 'checked'; endif; ?>>
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">تفعيل التقرير اليومي الإلزامي</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-relaxed">يتطلب من موظفي المبيعات تسليم تقرير يومي قبل الموعد المحدد.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer hover:border-sky-200">
                        <input type="checkbox" name="work_days_only" value="1" class="rounded border-slate-300 mt-1 text-sky-600 focus:ring-sky-500" <?php if($s['work_days_only'] ?? true): echo 'checked'; endif; ?>>
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">أيام العمل فقط</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-relaxed">يُستثنى يوم إجازة الموظف والإجازات المعتمدة من الإلزام.</span>
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            <i class="fas fa-clock text-sky-500 ml-0.5"></i>
                            آخر موعد للتسليم
                        </label>
                        <input type="time" name="deadline_time" value="<?php echo e(old('deadline_time', $s['deadline_time'] ?? '23:59')); ?>" required class="<?php echo e($inputClass); ?>">
                        <p class="text-[11px] text-slate-500 mt-1">بعد هذا الوقت يُعتبر التقرير متأخراً.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            <i class="fas fa-bullseye text-violet-500 ml-0.5"></i>
                            هدف KPI — نسبة التسليم الشهرية
                        </label>
                        <div class="relative">
                            <input type="number" name="kpi_submission_target_pct" min="50" max="100" step="1"
                                   value="<?php echo e(old('kpi_submission_target_pct', $s['kpi_submission_target_pct'] ?? 95)); ?>" required
                                   class="<?php echo e($inputClass); ?> pl-3">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">من 50% إلى 100% — يُستخدم في عمود الالتزام.</p>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden h-full">
            <div class="px-4 py-3 border-b border-rose-200 bg-rose-50/70">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-gavel text-rose-600"></i>
                    الخصم التلقائي
                </h3>
                <p class="text-xs text-slate-600 mt-0.5">يُنشأ خصم تلقائياً عند عدم تسليم التقرير في الموعد.</p>
            </div>
            <div class="p-4 sm:p-6 space-y-5">
                <label class="inline-flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50/40 px-4 py-3 cursor-pointer">
                    <input type="checkbox" name="penalty_enabled" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-400" <?php if($s['penalty_enabled'] ?? true): echo 'checked'; endif; ?>>
                    <span class="text-sm font-semibold text-slate-900">تفعيل الخصم عند عدم التسليم</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">مبلغ الخصم (ج.م)</label>
                        <input type="number" name="penalty_amount" step="0.01" min="0.01"
                               value="<?php echo e(old('penalty_amount', $s['penalty_amount'] ?? 50)); ?>" required class="<?php echo e($penaltyInputClass); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">حالة الخصم عند الإنشاء</label>
                        <select name="penalty_status" class="<?php echo e($penaltyInputClass); ?>">
                            <?php $__currentLoopData = ['pending' => 'معلّق', 'applied' => 'مطبّق', 'cancelled' => 'ملغى']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if(old('penalty_status', $s['penalty_status'] ?? 'pending') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">عنوان الخصم</label>
                        <input type="text" name="penalty_title"
                               value="<?php echo e(old('penalty_title', $s['penalty_title'] ?? '')); ?>" required class="<?php echo e($penaltyInputClass); ?>">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">وصف الخصم</label>
                        <textarea name="penalty_description" rows="3" class="<?php echo e($penaltyInputClass); ?>"><?php echo e(old('penalty_description', $s['penalty_description'] ?? '')); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">نوع الخصم</label>
                        <select name="penalty_type" class="<?php echo e($penaltyInputClass); ?>">
                            <?php $__currentLoopData = ['penalty' => 'غرامة', 'other' => 'أخرى', 'tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if(old('penalty_type', $s['penalty_type'] ?? 'penalty') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </section>
        </div>

        
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-slate-500">
                    <i class="fas fa-save text-emerald-600 ml-0.5"></i>
                    احفظ التغييرات لتطبيقها على سياسة التقارير اليومية.
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="<?php echo e($cancelUrl); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 text-sm">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                </div>
            </div>
        </section>
    <?php else: ?>
        
        <div>
            <h3 class="text-sm font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-sliders-h text-sky-600"></i>
                الإعدادات العامة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" class="rounded border-slate-300 mt-0.5" <?php if($s['enabled'] ?? true): echo 'checked'; endif; ?>>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">تفعيل التقرير اليومي الإلزامي</span>
                        <span class="block text-xs text-slate-500 mt-0.5">يتطلب من موظفي المبيعات تسليم تقرير يومي.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer">
                    <input type="checkbox" name="work_days_only" value="1" class="rounded border-slate-300 mt-0.5" <?php if($s['work_days_only'] ?? true): echo 'checked'; endif; ?>>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">أيام العمل فقط</span>
                        <span class="block text-xs text-slate-500 mt-0.5">حسب يوم إجازة كل موظف والإجازات المعتمدة.</span>
                    </span>
                </label>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">آخر موعد للتسليم (ساعة)</label>
                    <input type="time" name="deadline_time" value="<?php echo e(old('deadline_time', $s['deadline_time'] ?? '23:59')); ?>" required class="<?php echo e($inputClass); ?>">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">هدف KPI — نسبة التسليم الشهرية %</label>
                    <input type="number" name="kpi_submission_target_pct" min="50" max="100" step="1"
                           value="<?php echo e(old('kpi_submission_target_pct', $s['kpi_submission_target_pct'] ?? 95)); ?>" required class="<?php echo e($inputClass); ?>">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-rose-200 bg-rose-50/40 p-4 sm:p-5 space-y-4">
            <div>
                <h3 class="text-sm font-black text-rose-900 flex items-center gap-2">
                    <i class="fas fa-gavel"></i>
                    الخصم التلقائي
                </h3>
                <p class="text-xs text-rose-800/80 mt-1">يُسجّل في خصومات الموظفين عند عدم التسليم في الموعد.</p>
            </div>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="penalty_enabled" value="1" class="rounded border-slate-300" <?php if($s['penalty_enabled'] ?? true): echo 'checked'; endif; ?>>
                <span class="text-sm font-semibold text-slate-800">تفعيل الخصم عند عدم التسليم</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">مبلغ الخصم (ج.م)</label>
                    <input type="number" name="penalty_amount" step="0.01" min="0.01"
                           value="<?php echo e(old('penalty_amount', $s['penalty_amount'] ?? 50)); ?>" required class="<?php echo e($penaltyInputClass); ?>">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">حالة الخصم عند الإنشاء</label>
                    <select name="penalty_status" class="<?php echo e($penaltyInputClass); ?>">
                        <?php $__currentLoopData = ['pending' => 'معلّق', 'applied' => 'مطبّق', 'cancelled' => 'ملغى']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(old('penalty_status', $s['penalty_status'] ?? 'pending') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الخصم</label>
                    <input type="text" name="penalty_title" value="<?php echo e(old('penalty_title', $s['penalty_title'] ?? '')); ?>" required class="<?php echo e($penaltyInputClass); ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">وصف الخصم</label>
                    <textarea name="penalty_description" rows="2" class="<?php echo e($penaltyInputClass); ?>"><?php echo e(old('penalty_description', $s['penalty_description'] ?? '')); ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الخصم</label>
                    <select name="penalty_type" class="<?php echo e($penaltyInputClass); ?>">
                        <?php $__currentLoopData = ['penalty' => 'غرامة', 'other' => 'أخرى', 'tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(old('penalty_type', $s['penalty_type'] ?? 'penalty') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 text-sm">
                <i class="fas fa-save"></i>
                حفظ الإعدادات
            </button>
            <a href="<?php echo e($cancelUrl); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    <?php endif; ?>
</form>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\daily-reports\_settings_form.blade.php ENDPATH**/ ?>