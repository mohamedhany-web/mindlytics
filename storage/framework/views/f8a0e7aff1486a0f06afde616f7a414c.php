<?php $__env->startSection('title', 'اتفاقيات التقسيط'); ?>
<?php $__env->startSection('header', 'اتفاقيات التقسيط'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $summary = $summary ?? [];
    $pageStats = [
        ['label' => 'نشطة', 'value' => number_format($summary['active'] ?? 0), 'desc' => 'اتفاقيات تتطلب متابعة', 'icon' => 'fas fa-bolt', 'theme' => 'sky'],
        ['label' => 'إجمالي الممول', 'value' => number_format($summary['total_amount'] ?? 0, 2), 'desc' => 'ج.م — حسب التصفية', 'icon' => 'fas fa-coins', 'theme' => 'emerald'],
        ['label' => 'دفعات مقدمة', 'value' => number_format($summary['deposit_amount'] ?? 0, 2), 'desc' => 'ج.م — مجموع التصفية', 'icon' => 'fas fa-hand-holding-usd', 'theme' => 'amber'],
        ['label' => 'متأخرة', 'value' => number_format($summary['overdue'] ?? 0), 'desc' => 'مكتملة: ' . number_format($summary['completed'] ?? 0), 'icon' => 'fas fa-exclamation-circle', 'theme' => 'rose'],
    ];
?>
<div class="space-y-6">
    <?php echo $__env->make('admin.installments.partials.header', [
        'title' => 'اتفاقيات تقسيط الكورسات',
        'description' => 'إدارة خطط السداد للكورسات الأونلاين والأوفلاين مع متابعة الأقساط والربط المحاسبي.',
        'icon' => 'fa-handshake',
        'iconGradient' => 'from-emerald-500 to-teal-600',
        'meta' => number_format($summary['total_count'] ?? 0) . ' نتيجة بحسب التصفية',
        'actions' => [
            ['route' => 'admin.installments.agreements.manual-booking', 'label' => 'حجز + تقسيط', 'icon' => 'fa-user-plus', 'style' => 'warning'],
            ['route' => 'admin.installments.agreements.create', 'label' => 'اتفاقية جديدة', 'icon' => 'fa-plus', 'style' => 'success'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.installments.partials.nav', ['active' => 'agreements'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.installments.partials.stats', ['stats' => $pageStats], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            <div>
                <h2 class="text-base font-black text-slate-900">قائمة الاتفاقيات</h2>
                <p class="text-xs text-slate-500 mt-0.5">بحث بالطالب، تصفية بالحالة ونوع الكورس.</p>
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

        <div class="p-4 space-y-6">
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
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\installments\agreements\index.blade.php ENDPATH**/ ?>