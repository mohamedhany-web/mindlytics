<?php $__env->startSection('title', 'اتفاقيات التقسيط'); ?>
<?php $__env->startSection('header', 'اتفاقيات التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $summary = $summary ?? [];
?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
    <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-sky-900 to-violet-900 shadow-xl text-white p-6 sm:p-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">اتفاقيات تقسيط الكورسات</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 border border-white/20">
                        <i class="fas fa-filter text-[10px]"></i>
                        <?php echo e(number_format($summary['total_count'] ?? 0)); ?> نتيجة بحسب التصفية
                    </span>
                </div>
                <p class="mt-3 text-sm text-white/75 max-w-2xl leading-relaxed">
                    إدارة منظمة لخطط السداد المرتبطة بـ <strong>كورسات الأونلاين</strong> أو <strong>الأوفلاين</strong>، مع متابعة الأقساط والربط المحاسبي عند التحصيل.
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5 justify-end">
                <?php if(Route::has('admin.accounting.installments')): ?>
                <a href="<?php echo e(route('admin.accounting.installments')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 text-white text-sm font-semibold border border-white/25 hover:bg-white/25">
                    <i class="fas fa-tachometer-alt"></i>
                    لوحة التقسيط
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 text-white text-sm font-semibold border border-white/25 hover:bg-white/25">
                    <i class="fas fa-layer-group"></i>
                    الخطط
                </a>
                <a href="<?php echo e(route('admin.installments.agreements.manual-booking')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-400 text-slate-900 text-sm font-bold hover:bg-amber-300">
                    <i class="fas fa-user-plus"></i>
                    حجز + تقسيط
                </a>
                <a href="<?php echo e(route('admin.installments.agreements.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-sky-800 text-sm font-bold shadow-lg hover:bg-sky-50">
                    <i class="fas fa-plus"></i>
                    اتفاقية من تسجيل أونلاين
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-sm border border-sky-100 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wide">نشطة</p>
                    <p class="mt-1 text-2xl font-black text-slate-900"><?php echo e(number_format($summary['active'] ?? 0)); ?></p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                    <i class="fas fa-bolt"></i>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">اتفاقيات تتطلب متابعة دورية للأقساط.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">إجمالي الممول (تصفية)</p>
                    <p class="mt-1 text-2xl font-black text-slate-900"><?php echo e(number_format($summary['total_amount'] ?? 0, 2)); ?></p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-coins"></i>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">ج.م — حسب الفلاتر الحالية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-sm border border-amber-100 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide">دفعات مقدمة (مجموع)</p>
                    <p class="mt-1 text-2xl font-black text-slate-900"><?php echo e(number_format($summary['deposit_amount'] ?? 0, 2)); ?></p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-hand-holding-usd"></i>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">ج.م — مجمّع في النتائج المصفّاة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-sm border border-rose-100 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-rose-600 uppercase tracking-wide">متأخرة</p>
                    <p class="mt-1 text-2xl font-black text-slate-900"><?php echo e(number_format($summary['overdue'] ?? 0)); ?></p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                    <i class="fas fa-exclamation-circle"></i>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">مكتملة: <?php echo e(number_format($summary['completed'] ?? 0)); ?></p>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900">قائمة الاتفاقيات</h2>
                <p class="text-sm text-slate-500 mt-1">بحث بالطالب، تصفية بالحالة ونوع الكورس.</p>
            </div>
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم الطالب، البريد، الجوال…"
                       class="min-w-[200px] flex-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <select name="course_type" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 min-w-[200px]">
                    <?php $__currentLoopData = $courseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('course_type', '') === (string) $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 min-w-[160px]">
                    <option value="">كل الحالات</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                    <i class="fas fa-search"></i>
                    تطبيق
                </button>
                <?php if(request()->hasAny(['search', 'status', 'course_type'])): ?>
                    <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">إعادة ضبط</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if($agreements->count()): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50/80 to-white p-5 flex flex-col gap-4 shadow-sm hover:border-sky-200 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate"><?php echo e($agreement->student->name ?? 'طالب غير معروف'); ?></p>
                                <p class="text-xs text-sky-700 mt-0.5 truncate"><?php echo e($agreement->display_course_title); ?></p>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    <?php if($agreement->student_course_enrollment_id): ?>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-medium">أونلاين</span>
                                    <?php elseif($agreement->offline_course_enrollment_id): ?>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 font-medium">أوفلاين</span>
                                    <?php else: ?>
                                        <span class="text-slate-400">—</span>
                                    <?php endif; ?>
                                    · بداية <?php echo e(optional($agreement->start_date)->format('Y-m-d')); ?>

                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'bg-emerald-100 text-emerald-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_ACTIVE,
                                    'bg-amber-100 text-amber-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_OVERDUE,
                                    'bg-violet-100 text-violet-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_COMPLETED,
                                    'bg-rose-100 text-rose-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_CANCELLED,
                                    'bg-slate-100 text-slate-700' => ! in_array($agreement->status, [\App\Models\InstallmentAgreement::STATUS_ACTIVE, \App\Models\InstallmentAgreement::STATUS_OVERDUE, \App\Models\InstallmentAgreement::STATUS_COMPLETED, \App\Models\InstallmentAgreement::STATUS_CANCELLED])
                                ]); ?>"">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                <?php echo e($statuses[$agreement->status] ?? $agreement->status); ?>

                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">إجمالي الاتفاقية</p>
                                <p class="mt-0.5 font-bold text-slate-900"><?php echo e(number_format($agreement->total_amount ?? 0, 2)); ?> ج.م</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">دفعة مقدمة</p>
                                <p class="mt-0.5 font-bold text-slate-900"><?php echo e(number_format($agreement->deposit_amount ?? 0, 2)); ?> ج.م</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">عدد الأقساط</p>
                                <p class="mt-0.5 font-bold text-slate-900"><?php echo e($agreement->installments_count); ?></p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">القسط التالي</p>
                                <p class="mt-0.5 font-bold text-slate-900">
                                    <?php
                                        $next = $agreement->payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date')->first();
                                    ?>
                                    <?php echo e($next?->due_date?->format('Y-m-d') ?? '—'); ?>

                                </p>
                            </div>
                        </div>

                        <?php if($agreement->notes): ?>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2"><?php echo e($agreement->notes); ?></p>
                        <?php endif; ?>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <a href="<?php echo e(route('admin.installments.agreements.show', $agreement)); ?>" class="flex-1 min-w-[140px] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                                <i class="fas fa-eye"></i>
                                التفاصيل والجدول
                            </a>
                            <a href="<?php echo e(route('admin.installments.agreements.edit', $agreement)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="pt-2">
                <?php echo e($agreements->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center text-slate-500 py-16 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50">
                <i class="fas fa-folder-open text-4xl mb-3 text-slate-300"></i>
                <p class="font-bold text-slate-700">لا توجد اتفاقيات ضمن هذه التصفية.</p>
                <p class="text-sm text-slate-500 mt-2">جرّب تغيير البحث أو أضف اتفاقية جديدة.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/installments/agreements/index.blade.php ENDPATH**/ ?>