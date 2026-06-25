

<?php $__env->startSection('title', 'تفاصيل الإحالة - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل الإحالة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'list'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-6 border-b border-slate-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-sky-600 uppercase tracking-wide mb-1">إحالة #<?php echo e($referral->id); ?></p>
                <h1 class="text-2xl font-black text-slate-900">تفاصيل الإحالة</h1>
                <p class="text-sm text-slate-500 mt-1"><?php echo e($referral->created_at->format('Y-m-d H:i')); ?></p>
            </div>
            <div class="flex gap-2">
                <?php if($referral->status === 'completed'): ?>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مكتملة</span>
                <?php elseif($referral->status === 'pending'): ?>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد الانتظار</span>
                <?php else: ?>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">ملغاة</span>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.referrals.index')); ?>" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                    <i class="fas fa-arrow-right ml-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 bg-slate-50/60 border-b border-slate-100">
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">الخصم المطبق</p>
                <p class="text-xl font-black text-rose-600"><?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> ج.م</p>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">المكافأة</p>
                <p class="text-xl font-black text-emerald-600"><?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> ج.م</p>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">استخدامات الخصم</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($referral->discount_used_count ?? 0); ?> / <?php echo e($referral->referralProgram->max_discount_uses_per_referred ?? 1); ?></p>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">كود الإحالة</p>
                <p class="text-sm font-mono font-black text-violet-700"><?php echo e($referral->referral_code ?? $referral->code ?? '—'); ?></p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-sky-50 to-white">
                <h2 class="font-black text-slate-900"><i class="fas fa-user-check text-sky-600 ml-2"></i>المحيل</h2>
            </div>
            <dl class="p-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">الاسم</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e($referral->referrer->name ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">الهاتف</dt>
                    <dd class="font-semibold text-slate-900" dir="ltr"><?php echo e($referral->referrer->phone ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">البريد</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e($referral->referrer->email ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-slate-500">كود المحيل</dt>
                    <dd class="font-mono font-bold text-sky-700"><?php echo e($referral->referrer->referral_code ?? '—'); ?></dd>
                </div>
            </dl>
        </div>

        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
                <h2 class="font-black text-slate-900"><i class="fas fa-user-plus text-emerald-600 ml-2"></i>المحال</h2>
            </div>
            <dl class="p-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">الاسم</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e($referral->referred->name ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">الهاتف</dt>
                    <dd class="font-semibold text-slate-900" dir="ltr"><?php echo e($referral->referred->phone ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-slate-100">
                    <dt class="text-slate-500">البريد</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e($referral->referred->email ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-slate-500">تاريخ التسجيل</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e($referral->referred->created_at?->format('Y-m-d') ?? '—'); ?></dd>
                </div>
            </dl>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-black text-slate-900"><i class="fas fa-gift text-purple-600 ml-2"></i>تفاصيل البرنامج والخصم</h2>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">البرنامج</span>
                <span class="font-semibold"><?php echo e($referral->referralProgram->name ?? '—'); ?></span>
            </div>
            <?php if($referral->completed_at): ?>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">تاريخ الإكمال</span>
                <span class="font-semibold"><?php echo e($referral->completed_at->format('Y-m-d H:i')); ?></span>
            </div>
            <?php endif; ?>
            <?php if($referral->discount_expires_at): ?>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">انتهاء الخصم</span>
                <span class="font-semibold <?php echo e($referral->discount_expires_at->isPast() ? 'text-red-600' : 'text-slate-900'); ?>">
                    <?php echo e($referral->discount_expires_at->format('Y-m-d H:i')); ?>

                </span>
            </div>
            <?php endif; ?>
            <?php if($referral->autoCoupon): ?>
            <div class="flex justify-between py-2 border-b border-slate-100 md:col-span-2">
                <span class="text-slate-500">الكوبون التلقائي للمحال</span>
                <span class="font-mono font-bold text-violet-700"><?php echo e($referral->autoCoupon->code); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($referral->invoice): ?>
    <div class="rounded-2xl bg-white border border-sky-200 shadow-sm p-5 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold text-sky-600 mb-1">فاتورة مرتبطة</p>
            <p class="text-lg font-black text-slate-900"><?php echo e($referral->invoice->invoice_number); ?></p>
        </div>
        <a href="<?php echo e(route('admin.invoices.show', $referral->invoice)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700">
            <i class="fas fa-file-invoice"></i> عرض الفاتورة
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\referrals\show.blade.php ENDPATH**/ ?>