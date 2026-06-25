<?php $__env->startSection('title', 'تسجيل يومي'); ?>
<?php $__env->startSection('header', 'تسجيل يومي — ' . $location->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }
    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    .type-card {
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
    }
    .type-card:hover { border-color: #93c5fd; background: #f8fafc; }
    .type-card.active {
        border-color: #2563eb;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
    }
    .form-input:focus {
        box-shadow: 0 4px 14px rgba(44, 169, 189, 0.12);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $hourlyRate = (float) ($location->hourly_rate ?? 0);
    $settlementOpen = $currentSettlement && $currentSettlement->status === \App\Models\PlaceMonthlySettlement::STATUS_OPEN;
    $oldExpenses = old('expenses', [['title' => '', 'category' => 'food', 'amount' => '', 'quantity' => 1]]);
    if (empty($oldExpenses)) {
        $oldExpenses = [['title' => '', 'category' => 'food', 'amount' => '', 'quantity' => 1]];
    }
    $defaultUsageType = old('usage_type', \App\Models\PlaceUsageLog::TYPE_COURSE);
?>

<div class="space-y-6"
     x-data="placeDailyForm({
        usageType: <?php echo json_encode($defaultUsageType, 15, 512) ?>,
        hours: <?php echo json_encode(old('hours', ''), 512) ?>,
        hourlyRate: <?php echo e($hourlyRate); ?>,
        expenses: <?php echo json_encode($oldExpenses, 15, 512) ?>
     })">

    
    <div class="welcome-section dashboard-card">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900">تسجيل اليوم</h2>
                    <p class="text-gray-600 mt-1 font-medium"><?php echo e($location->name); ?> — فترة <?php echo e($period); ?></p>
                    <p class="text-sm text-gray-500 mt-2 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        سجّل ساعات الكورس أو النشاط + مصاريف اليوم (أكل، مشروبات…) في نموذج واحد
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('place.office.usage-logs.index')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border-2 border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 shrink-0">
                <i class="fas fa-arrow-right"></i> العودة للسجل
            </a>
        </div>
    </div>

    <?php if(!$settlementOpen): ?>
        <div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-4 text-amber-900">
            <p class="font-bold"><i class="fas fa-exclamation-triangle ml-2"></i>تنبيه</p>
            <p class="text-sm mt-1">مخالصة شهر <?php echo e($period); ?> غير مفتوحة حالياً. قد لا يُقبل الحفظ حتى تفتح الإدارة الفترة.</p>
        </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="dashboard-card rounded-2xl p-5 border-2 border-blue-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">ساعات معتمدة</p>
                    <p class="text-2xl sm:text-3xl font-black text-blue-700 tabular-nums"><?php echo e(number_format((float) $approvedHoursThisMonth, 2)); ?></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-yellow-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">في الانتظار</p>
                    <p class="text-2xl sm:text-3xl font-black text-yellow-700 tabular-nums"><?php echo e($pendingLogs + $pendingExpenses); ?></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-green-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">سعر الساعة</p>
                    <p class="text-xl sm:text-2xl font-black text-green-700 tabular-nums">
                        <?php if($hourlyRate > 0): ?><?php echo e(number_format($hourlyRate, 2)); ?> ج.م<?php else: ?> — <?php endif; ?>
                    </p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fas fa-coins text-xl"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-card rounded-2xl p-5 border-2 border-violet-200/50 shadow-xl">
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">تقدير الساعات</p>
                    <p class="text-xl sm:text-2xl font-black text-violet-700 tabular-nums" x-text="hourlyRate > 0 && parseFloat(hours) > 0 ? hoursEstimate + ' ج.م' : '—'"></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg shrink-0">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <div class="xl:col-span-8 space-y-6">
            <form action="<?php echo e(route('place.office.usage-logs.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>

                
                <div class="dashboard-card rounded-2xl p-6 shadow-xl">
                    <div class="relative z-10 space-y-5">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">1</span>
                                <i class="fas fa-tags text-blue-600"></i>
                                نوع النشاط
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 mr-10">حدد هل الاستخدام لكورس معيّن أم نشاط آخر في المكان</p>
                        </div>

                        
                        <input type="hidden" name="usage_type" :value="usageType" value="<?php echo e($defaultUsageType); ?>">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php $__currentLoopData = $usageTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button"
                                        @click="usageType = '<?php echo e($value); ?>'"
                                        class="type-card text-right w-full"
                                        :class="usageType === '<?php echo e($value); ?>' ? 'active' : ''">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                                             :class="usageType === '<?php echo e($value); ?>' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'">
                                            <i class="fas <?php echo e($value === 'course' ? 'fa-chalkboard-teacher' : 'fa-tasks'); ?> text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-gray-900"><?php echo e($label); ?></p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <?php if($value === 'course'): ?>
                                                    محاضرة أو جلسة تدريب لكورس مسجّل في هذا المكان
                                                <?php else: ?>
                                                    اجتماع، ورشة، أو أي استخدام بدون كورس محدد
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0"
                                             :class="usageType === '<?php echo e($value); ?>' ? 'border-blue-600 bg-blue-600' : 'border-slate-300'">
                                            <i class="fas fa-check text-white text-[9px]" x-show="usageType === '<?php echo e($value); ?>'"></i>
                                        </div>
                                    </div>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['usage_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        
                        <div x-show="usageType === 'course'" x-transition class="pt-4 border-t border-slate-200/80">
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                الكورس المعطى في المكان <span class="text-red-500">*</span>
                            </label>
                            <select name="offline_course_id"
                                    class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                                <option value="">— اختر الكورس —</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->id); ?>" <?php if(old('offline_course_id') == $course->id): echo 'selected'; endif; ?>><?php echo e($course->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($courses->isEmpty()): ?>
                                <p class="text-sm text-amber-700 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    لا توجد كورسات مرتبطة بهذا المكان. اختر «نشاط آخر» أو اطلب من الإدارة ربط الكورسات.
                                </p>
                            <?php endif; ?>
                            <?php $__errorArgs = ['offline_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="dashboard-card rounded-2xl p-6 shadow-xl">
                    <div class="relative z-10 space-y-5">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">2</span>
                                <i class="fas fa-clock text-emerald-600"></i>
                                ساعات الاستخدام
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 mr-10">اترك الحقل فارغاً إذا كنت تسجّل مصاريف اليوم فقط</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">تاريخ اليوم <span class="text-red-500">*</span></label>
                                <input type="date" name="usage_date" value="<?php echo e(old('usage_date', now()->toDateString())); ?>" max="<?php echo e(now()->toDateString()); ?>" required
                                       class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                                <?php $__errorArgs = ['usage_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">عدد الساعات</label>
                                <input type="number" name="hours" x-model="hours" step="0.25" min="0.25" max="24"
                                       class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20"
                                       placeholder="مثال: 3 أو 3.5">
                                <p class="text-xs text-gray-500 mt-1">من 0.25 إلى 24 ساعة</p>
                                <?php $__errorArgs = ['hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">وصف النشاط (اختياري)</label>
                            <textarea name="description" rows="3" placeholder="مثال: محاضرة الوحدة الثالثة — قاعة A"
                                      class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20"><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="dashboard-card rounded-2xl p-6 shadow-xl">
                    <div class="relative z-10 space-y-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-sm font-bold">3</span>
                                    <i class="fas fa-receipt text-violet-600"></i>
                                    فاتورة مصاريف اليوم
                                </h3>
                                <p class="text-sm text-gray-500 mt-1 mr-10">أكل، مشروبات، مستلزمات… (اختياري)</p>
                            </div>
                            <button type="button" @click="addExpense()"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold">
                                <i class="fas fa-plus"></i> بند جديد
                            </button>
                        </div>

                        <div class="hidden sm:grid grid-cols-12 gap-2 px-2 text-xs font-bold text-gray-500">
                            <div class="col-span-5">البيان</div>
                            <div class="col-span-3">الفئة</div>
                            <div class="col-span-2">المبلغ</div>
                            <div class="col-span-1">الكمية</div>
                            <div class="col-span-1"></div>
                        </div>

                        <template x-for="(row, index) in expenses" :key="index">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start p-3 sm:p-4 rounded-xl bg-white/80 border-2 border-slate-100">
                                <div class="sm:col-span-5">
                                    <label class="sm:hidden text-xs font-semibold text-gray-500 mb-1 block">البيان</label>
                                    <input type="text" :name="'expenses['+index+'][title]'" x-model="row.title"
                                           placeholder="مثال: غداء المتدربين"
                                           class="form-input w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 text-sm">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="sm:hidden text-xs font-semibold text-gray-500 mb-1 block">الفئة</label>
                                    <select :name="'expenses['+index+'][category]'" x-model="row.category"
                                            class="form-input w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 text-sm">
                                        <?php $__currentLoopData = $expenseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $catLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($catLabel); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 sm:contents gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="sm:hidden text-xs font-semibold text-gray-500 mb-1 block">المبلغ (ج.م)</label>
                                        <input type="number" :name="'expenses['+index+'][amount]'" x-model="row.amount" step="0.01" min="0" placeholder="0.00"
                                               class="form-input w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 text-sm tabular-nums">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label class="sm:hidden text-xs font-semibold text-gray-500 mb-1 block">الكمية</label>
                                        <input type="number" :name="'expenses['+index+'][quantity]'" x-model="row.quantity" min="1"
                                               class="form-input w-full rounded-lg border-2 border-gray-200 px-3 py-2.5 text-sm tabular-nums">
                                    </div>
                                </div>
                                <div class="sm:col-span-1 flex sm:justify-center">
                                    <button type="button" @click="removeExpense(index)" x-show="expenses.length > 1"
                                            class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="حذف البند">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-between items-center pt-3 border-t-2 border-slate-200/60">
                            <span class="text-sm font-semibold text-gray-600">إجمالي فاتورة المصاريف</span>
                            <span class="text-xl font-black text-violet-700 tabular-nums" x-text="expenseTotal + ' ج.م'"></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg transition-colors">
                        <i class="fas fa-paper-plane"></i>
                        حفظ وإرسال للمراجعة
                    </button>
                    <a href="<?php echo e(route('place.office.usage-logs.index')); ?>"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        
        <div class="xl:col-span-4 space-y-4">
            <div class="dashboard-card rounded-2xl p-5 shadow-lg">
                <div class="relative z-10">
                    <h4 class="text-sm font-black text-slate-800 mb-3">
                        <i class="fas fa-route text-blue-500 ml-1"></i> خطوات التسجيل
                    </h4>
                    <ol class="text-sm text-slate-600 space-y-2.5 list-decimal list-inside">
                        <li><strong>نوع النشاط:</strong> كورس أو نشاط آخر (القسم الأول بالأعلى)</li>
                        <li><strong>الكورس:</strong> يظهر تلقائياً عند اختيار «كورس»</li>
                        <li><strong>الساعات:</strong> التاريخ وعدد الساعات في القسم الثاني</li>
                        <li><strong>المصاريف:</strong> أضف بنود الفاتورة في القسم الثالث (اختياري)</li>
                    </ol>
                </div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-lg">
                <h4 class="text-sm font-black text-slate-800 mb-3"><i class="fas fa-lightbulb text-amber-500 ml-1"></i> ملاحظات</h4>
                <ul class="text-sm text-slate-600 space-y-2">
                    <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i><span>يمكنك تسجيل ساعات فقط، أو مصاريف فقط، أو الاثنين معاً.</span></li>
                    <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i><span>كل ما تُسجّله يذهب للمراجعة قبل إضافته للمخالصة.</span></li>
                </ul>
            </div>

            <?php if($currentSettlement): ?>
                <div class="bg-white rounded-2xl border-2 border-blue-200/60 p-5 shadow-lg">
                    <h4 class="text-sm font-black text-slate-800 mb-2">مخالصة <?php echo e($period); ?></h4>
                    <p class="text-lg font-black text-blue-700"><?php echo e($currentSettlement->status_label); ?></p>
                    <a href="<?php echo e(route('place.office.settlements.index')); ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:underline mt-3">
                        عرض المخالصات <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                </div>
            <?php endif; ?>

            <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-5 text-white shadow-lg">
                <p class="text-sm font-semibold opacity-90">مدير المكان</p>
                <p class="text-lg font-black mt-1"><?php echo e($user->name); ?></p>
                <p class="text-xs opacity-80 mt-2" dir="ltr"><?php echo e($user->email); ?></p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function placeDailyForm(config) {
    return {
        usageType: config.usageType || 'course',
        hours: config.hours,
        hourlyRate: config.hourlyRate,
        expenses: (config.expenses || []).map(e => ({
            title: e.title || '',
            category: e.category || 'food',
            amount: e.amount || '',
            quantity: e.quantity || 1,
        })),
        get hoursEstimate() {
            const h = parseFloat(this.hours) || 0;
            return (h * this.hourlyRate).toFixed(2);
        },
        get expenseTotal() {
            return this.expenses.reduce((sum, row) => {
                const amt = parseFloat(row.amount) || 0;
                const qty = parseInt(row.quantity, 10) || 1;
                return sum + (amt * qty);
            }, 0).toFixed(2);
        },
        addExpense() {
            this.expenses.push({ title: '', category: 'food', amount: '', quantity: 1 });
        },
        removeExpense(index) {
            if (this.expenses.length > 1) {
                this.expenses.splice(index, 1);
            }
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/place-office/usage-logs/create.blade.php ENDPATH**/ ?>