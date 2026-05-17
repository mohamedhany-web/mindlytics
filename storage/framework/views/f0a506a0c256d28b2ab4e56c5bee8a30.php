

<?php $__env->startSection('title', 'لوحة التقسيط المحاسبية'); ?>
<?php $__env->startSection('header', 'لوحة التقسيط المحاسبية'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-8 pb-10">
    <section class="rounded-3xl bg-gradient-to-br from-violet-900 via-slate-900 to-sky-900 text-white shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 border-b border-white/10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-violet-200/90 mb-2">المحاسبة — التقسيط والكورسات</p>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">لوحة تحكم التقسيط</h1>
                    <p class="mt-3 text-sm text-white/75 max-w-2xl leading-relaxed">
                        متابعة مركزية لاتفاقيات تقسيط الكورسات (أونلاين وأوفلاين)، الأقساط المستحقة، التحصيل الشهري، والربط مع الفواتير والمدفوعات عند تسجيل سداد القسط.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2.5">
                    <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-th-large"></i>
                        مركز المحاسبة
                    </a>
                    <a href="<?php echo e(route('admin.accounting.chart')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-sitemap"></i>
                        شجرة الحسابات
                    </a>
                    <a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-layer-group"></i>
                        خطط التقسيط
                    </a>
                    <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-emerald-400">
                        <i class="fas fa-handshake"></i>
                        اتفاقيات التقسيط
                    </a>
                    <a href="<?php echo e(route('admin.installments.agreements.manual-booking')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-lg hover:bg-amber-300">
                        <i class="fas fa-user-plus"></i>
                        حجز يدوي + تقسيط
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-px bg-white/10">
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">اتفاقيات (الكل)</p>
                <p class="mt-1 text-2xl font-black text-white"><?php echo e(number_format($stats['agreements_total'])); ?></p>
            </div>
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">نشطة</p>
                <p class="mt-1 text-2xl font-black text-emerald-300"><?php echo e(number_format($stats['agreements_active'])); ?></p>
            </div>
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">متأخرة (اتفاقية)</p>
                <p class="mt-1 text-2xl font-black text-amber-300"><?php echo e(number_format($stats['agreements_overdue'])); ?></p>
            </div>
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">مكتملة</p>
                <p class="mt-1 text-2xl font-black text-violet-200"><?php echo e(number_format($stats['agreements_completed'])); ?></p>
            </div>
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">أونلاين</p>
                <p class="mt-1 text-2xl font-black text-sky-200"><?php echo e(number_format($stats['online_linked'])); ?></p>
            </div>
            <div class="bg-slate-900/80 p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-white/55">أوفلاين</p>
                <p class="mt-1 text-2xl font-black text-teal-200"><?php echo e(number_format($stats['offline_linked'])); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">إجمالي مبالغ الاتفاقيات</p>
                <p class="text-lg font-bold text-white"><?php echo e(number_format($stats['total_financed'], 2)); ?> <span class="text-xs text-white/50">ج.م</span></p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">أقساط معلقة (مجموع)</p>
                <p class="text-lg font-bold text-violet-200"><?php echo e(number_format($stats['pending_installments_sum'], 2)); ?></p>
                <p class="text-[10px] text-white/45"><?php echo e(number_format($stats['pending_count'])); ?> قسط</p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">أقساط متأخرة (مجموع)</p>
                <p class="text-lg font-bold text-rose-200"><?php echo e(number_format($stats['overdue_installments_sum'], 2)); ?></p>
                <p class="text-[10px] text-white/45"><?php echo e(number_format($stats['overdue_payments_count'])); ?> قسط</p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">محصّل أقساط الشهر</p>
                <p class="text-lg font-bold text-emerald-200"><?php echo e(number_format($stats['paid_installments_month'], 2)); ?></p>
                <p class="text-[10px] text-white/45"><?php echo e($stats['month_label']); ?> — <?php echo e(number_format($stats['paid_count_month'])); ?> عملية</p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-l from-violet-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-violet-600"></i>
                        أقساط خلال 14 يوماً
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">معلقة أو متأخرة — رتّب أولوية المتابعة</p>
                </div>
                <a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="text-sm font-semibold text-violet-600 hover:text-violet-800">كل الاتفاقيات ←</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $upcomingDues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $a = $p->agreement; ?>
                    <div class="px-4 py-3 flex flex-wrap items-center gap-3 hover:bg-slate-50/80">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($a?->student?->name ?? '—'); ?></p>
                            <p class="text-xs text-slate-500 truncate"><?php echo e($a?->display_course_title ?? '—'); ?></p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-bold text-slate-800"><?php echo e(number_format((float) $p->amount, 2)); ?> ج.م</p>
                            <p class="text-[11px] text-slate-500">استحقاق <?php echo e($p->due_date?->format('Y-m-d')); ?> — قسط <?php echo e($p->sequence_number); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-bold
                            <?php echo e($p->status === \App\Models\InstallmentPayment::STATUS_OVERDUE ? 'bg-rose-100 text-rose-800' : 'bg-amber-50 text-amber-800'); ?>">
                            <?php echo e($p->status === \App\Models\InstallmentPayment::STATUS_OVERDUE ? 'متأخر' : 'مستحق'); ?>

                        </span>
                        <?php if($a): ?>
                            <a href="<?php echo e(route('admin.installments.agreements.show', $a)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-violet-100 hover:text-violet-700" title="فتح">
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-12 text-center text-sm text-slate-500">لا توجد أقساط مستحقة في هذه النافذة.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-l from-emerald-50 to-white flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-600"></i>
                        آخر عمليات السداد
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">تسجيل «مدفوع» على الأقساط — يُنشئ فاتورة/دفعة/معاملة عند الضبط</p>
                </div>
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="text-sm font-semibold text-emerald-600 hover:text-emerald-800">المدفوعات ←</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $a = $p->agreement; ?>
                    <div class="px-4 py-3 flex flex-wrap items-center gap-3 hover:bg-slate-50/80">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($a?->student?->name ?? '—'); ?></p>
                            <p class="text-xs text-slate-500 truncate"><?php echo e($a?->display_course_title ?? '—'); ?></p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-bold text-emerald-700">+<?php echo e(number_format((float) $p->amount, 2)); ?> ج.م</p>
                            <p class="text-[11px] text-slate-500"><?php echo e(optional($p->paid_at)->format('Y-m-d H:i') ?? '—'); ?></p>
                        </div>
                        <?php if($p->payment_id): ?>
                            <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 px-2 py-1 rounded-lg">مرتبط بمدفوعة</span>
                        <?php endif; ?>
                        <?php if($a): ?>
                            <a href="<?php echo e(route('admin.installments.agreements.show', $a)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700" title="فتح الاتفاقية">
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-12 text-center text-sm text-slate-500">لا توجد أقساط مسددة مسجلة بعد.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
            <i class="fas fa-route text-slate-500"></i>
            مسار العمل الموصى به
        </h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-slate-600 leading-relaxed mr-2">
            <li>إنشاء أو مراجعة <strong>خطط التقسيط</strong> (مدة، دفعة مقدمة، تكرار الأقساط).</li>
            <li>ربط الطالب بالكورس عبر <strong>حجز يدوي + تقسيط</strong> (أونلاين أو أوفلاين مع المجموعة) أو اتفاقية من تسجيل أونلاين موجود.</li>
            <li>متابعة الجدول من صفحة الاتفاقية وتسجيل السداد — يظهر في <strong>الفواتير / المدفوعات / المعاملات</strong>.</li>
            <li>استخدم <strong>شجرة الحسابات</strong> لفهم موضع التقسيط في هيكل الأصول والإيرادات.</li>
        </ol>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/installments.blade.php ENDPATH**/ ?>