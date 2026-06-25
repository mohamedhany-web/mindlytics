<?php $__env->startSection('title', 'خصومات الموظفين - Mindlytics'); ?>
<?php $__env->startSection('header', 'خصومات الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
            <span class="font-semibold"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 flex items-center gap-3 shadow-sm">
            <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
            <span class="font-semibold"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('delete_preview') !== null): ?>
        <div class="rounded-xl bg-amber-50 border border-amber-300 text-amber-950 px-5 py-4 shadow-sm">
            <p class="font-bold"><i class="fas fa-info-circle ml-1"></i> معاينة الحذف: يوجد <strong><?php echo e(number_format(session('delete_preview'))); ?></strong> خصم مطابق للنطاق المحدد.</p>
            <p class="text-sm mt-1 text-amber-800">راجع الفترة ثم فعّل «تأكيد الحذف» واضغط حذف نهائي.</p>
        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">خصومات الموظفين</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة خصومات الرواتب (ضريبة، تأمين، قرض، غرامة، أخرى)</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.employee-deductions.daily-report-penalty-settings')); ?>" class="inline-flex items-center gap-2 rounded-xl border-2 border-amber-300 bg-amber-50 hover:bg-amber-100 px-5 py-3 text-sm font-semibold text-amber-900 transition-all">
                    <i class="fas fa-clipboard-check"></i>
                    خصم التقرير اليومي (مبيعات)
                </a>
                <a href="<?php echo e(route('admin.employee-deductions.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-700 hover:to-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    إضافة خصم جديد
                </a>
            </div>
        </div>
    </section>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border-2 border-slate-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-slate-600 mb-1">إجمالي الخصومات</p>
            <p class="text-2xl font-black text-slate-900"><?php echo e($stats['total']); ?></p>
        </div>
        <div class="rounded-2xl bg-amber-50 border-2 border-amber-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-amber-800 mb-1">معلقة</p>
            <p class="text-2xl font-black text-amber-700"><?php echo e($stats['pending']); ?></p>
        </div>
        <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-emerald-800 mb-1">مطبقة</p>
            <p class="text-2xl font-black text-emerald-700"><?php echo e($stats['applied']); ?></p>
        </div>
        <div class="rounded-2xl bg-rose-50 border-2 border-rose-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-rose-800 mb-1">إجمالي المبالغ المطبقة</p>
            <p class="text-2xl font-black text-rose-700"><?php echo e(number_format($stats['total_amount'], 2)); ?> ج.م</p>
        </div>
    </div>

    <!-- فلترة وبحث -->
    <section class="rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-filter text-rose-600"></i> البحث والفلترة</h3>
        </div>
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="رقم الخصم، العنوان، الموظف..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                    <select name="employee_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">جميع الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">الكل</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="applied" <?php echo e(request('status') == 'applied' ? 'selected' : ''); ?>>مطبقة</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الخصم</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">الكل</option>
                        <option value="tax" <?php echo e(request('type') == 'tax' ? 'selected' : ''); ?>>ضريبة</option>
                        <option value="insurance" <?php echo e(request('type') == 'insurance' ? 'selected' : ''); ?>>تأمين</option>
                        <option value="loan" <?php echo e(request('type') == 'loan' ? 'selected' : ''); ?>>قرض</option>
                        <option value="penalty" <?php echo e(request('type') == 'penalty' ? 'selected' : ''); ?>>غرامة</option>
                        <option value="other" <?php echo e(request('type') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div class="md:col-span-2 lg:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm"><i class="fas fa-search ml-1"></i> بحث</button>
                    <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <!-- حذف بنطاق تاريخ -->
    <section class="rounded-2xl bg-white border-2 border-rose-200/80 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-rose-100 bg-rose-50/60">
            <h3 class="text-lg font-bold text-rose-900 flex items-center gap-2">
                <i class="fas fa-calendar-times text-rose-600"></i>
                حذف خصومات بنطاق تاريخ
            </h3>
            <p class="text-xs text-rose-800/80 mt-1">احذف خصومات محددة بين تاريخين — مع إمكانية تقييد الموظف أو الحالة أو النوع. يُنصح بالمعاينة أولاً.</p>
        </div>
        <div class="p-5">
            <form method="POST" action="<?php echo e(route('admin.employee-deductions.bulk-delete-by-date')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                  onsubmit="return confirmBulkDelete(this);">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ <span class="text-rose-600">*</span></label>
                    <input type="date" name="date_from" required value="<?php echo e(old('date_from')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ <span class="text-rose-600">*</span></label>
                    <input type="date" name="date_to" required value="<?php echo e(old('date_to')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف (اختياري)</label>
                    <select name="employee_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                        <option value="">جميع الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة (اختياري)</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                        <option value="">الكل</option>
                        <option value="pending" <?php if(old('status') === 'pending'): echo 'selected'; endif; ?>>معلقة</option>
                        <option value="applied" <?php if(old('status') === 'applied'): echo 'selected'; endif; ?>>مطبقة</option>
                        <option value="cancelled" <?php if(old('status') === 'cancelled'): echo 'selected'; endif; ?>>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الخصم (اختياري)</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                        <option value="">الكل</option>
                        <option value="tax" <?php if(old('type') === 'tax'): echo 'selected'; endif; ?>>ضريبة</option>
                        <option value="insurance" <?php if(old('type') === 'insurance'): echo 'selected'; endif; ?>>تأمين</option>
                        <option value="loan" <?php if(old('type') === 'loan'): echo 'selected'; endif; ?>>قرض</option>
                        <option value="penalty" <?php if(old('type') === 'penalty'): echo 'selected'; endif; ?>>غرامة</option>
                        <option value="other" <?php if(old('type') === 'other'): echo 'selected'; endif; ?>>أخرى</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-rose-900 font-semibold cursor-pointer">
                        <input type="checkbox" name="confirmed" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                        <span>أؤكد حذف السجلات المطابقة نهائياً</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3 flex flex-wrap gap-2 pt-1">
                    <button type="submit" name="preview_only" value="1" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm">
                        <i class="fas fa-eye ml-1"></i> معاينة العدد
                    </button>
                    <button type="submit" class="px-4 py-2.5 bg-rose-700 hover:bg-rose-800 text-white rounded-xl font-semibold text-sm">
                        <i class="fas fa-trash-alt ml-1"></i> حذف نهائي للنطاق
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- الجدول -->
    <section class="rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الخصم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?php echo e($d->deduction_number); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e($d->employee->name ?? '—'); ?></td>
                            <td class="px-6 py-4 text-sm text-slate-900"><?php echo e(Str::limit($d->title, 40)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $typeLabels = ['tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض', 'penalty' => 'غرامة', 'other' => 'أخرى'];
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800"><?php echo e($typeLabels[$d->type] ?? $d->type); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600"><?php echo e(number_format($d->amount, 2)); ?> ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e($d->deduction_date->format('Y-m-d')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($d->status === 'pending'): ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">معلقة</span>
                                <?php elseif($d->status === 'applied'): ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">مطبقة</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">ملغاة</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="<?php echo e(route('admin.employee-deductions.show', $d)); ?>" class="text-rose-600 hover:text-rose-800 font-medium ml-2">عرض</a>
                                <a href="<?php echo e(route('admin.employee-deductions.edit', $d)); ?>" class="text-sky-600 hover:text-sky-800 font-medium ml-2">تعديل</a>
                                <form action="<?php echo e(route('admin.employee-deductions.destroy', $d)); ?>" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذا الخصم؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">لا توجد خصومات. <a href="<?php echo e(route('admin.employee-deductions.create')); ?>" class="text-rose-600 hover:underline font-medium">إضافة خصم جديد</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($deductions->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200">
                <?php echo e($deductions->withQueryString()->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmBulkDelete(form) {
        const isPreview = document.activeElement && document.activeElement.name === 'preview_only';
        if (isPreview) return true;
        const confirmed = form.querySelector('input[name="confirmed"]')?.checked;
        if (!confirmed) {
            alert('فعّل «أؤكد حذف السجلات المطابقة نهائياً» أو اضغط معاينة العدد أولاً.');
            return false;
        }
        const from = form.querySelector('input[name="date_from"]')?.value;
        const to = form.querySelector('input[name="date_to"]')?.value;
        return confirm('حذف كل الخصومات من ' + from + ' إلى ' + to + '؟\n\nلا يمكن التراجع عن هذا الإجراء.');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/employee-deductions/index.blade.php ENDPATH**/ ?>