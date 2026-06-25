

<?php $__env->startSection('title', 'سجل الإحالات - Mindlytics'); ?>
<?php $__env->startSection('header', 'سجل الإحالات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'list'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <section class="rounded-2xl bg-white/95 border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-indigo-50 via-sky-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 via-sky-600 to-blue-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-friends text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">سجل الإحالات</h2>
                    <p class="text-sm text-slate-600 mt-1">تتبع المحيلين والمحالين والخصومات والمكافآت</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-800 hover:bg-sky-100 transition-all">
                <i class="fas fa-gift"></i> إدارة البرامج
            </a>
        </div>
    </section>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="rounded-2xl bg-white border border-sky-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-sky-700/80 mb-1">إجمالي الإحالات</p>
                    <p class="text-3xl font-black text-slate-900"><?php echo e(number_format($stats['total'])); ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-emerald-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-700/80 mb-1">مكتملة</p>
                    <p class="text-3xl font-black text-emerald-700"><?php echo e(number_format($stats['completed'])); ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-amber-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-700/80 mb-1">قيد الانتظار</p>
                    <p class="text-3xl font-black text-amber-700"><?php echo e(number_format($stats['pending'])); ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-purple-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-purple-700/80 mb-1">إجمالي المكافآت</p>
                    <p class="text-2xl font-black text-purple-700"><?php echo e(number_format($stats['total_rewards'], 0)); ?> <span class="text-sm font-bold">ج.م</span></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center"><i class="fas fa-gift"></i></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white border border-rose-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-700/80 mb-1">إجمالي الخصومات</p>
                    <p class="text-2xl font-black text-rose-700"><?php echo e(number_format($stats['total_discounts'], 0)); ?> <span class="text-sm font-bold">ج.م</span></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><i class="fas fa-tag"></i></div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" action="<?php echo e(route('admin.referrals.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، هاتف، كود..."
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                    <option value="">الكل</option>
                    <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>قيد الانتظار</option>
                    <option value="completed" <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>مكتملة</option>
                    <option value="cancelled" <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>>ملغاة</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">البرنامج</label>
                <select name="program_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                    <option value="">كل البرامج</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($program->id); ?>" <?php if((string) request('program_id') === (string) $program->id): echo 'selected'; endif; ?>><?php echo e($program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 text-sm font-bold transition-colors">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
                <a href="<?php echo e(route('admin.referrals.index')); ?>" class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 text-sm font-semibold" title="إعادة تعيين"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>

    
    <?php if($referrals->count() > 0): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                <h3 class="font-black text-slate-900 text-sm"><i class="fas fa-list text-sky-600 ml-2"></i>قائمة الإحالات (<?php echo e($referrals->total()); ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-bold">المحيل</th>
                            <th class="px-4 py-3 text-right font-bold">المحال</th>
                            <th class="px-4 py-3 text-right font-bold">البرنامج</th>
                            <th class="px-4 py-3 text-right font-bold">الكود</th>
                            <th class="px-4 py-3 text-right font-bold">الحالة</th>
                            <th class="px-4 py-3 text-right font-bold">الخصم</th>
                            <th class="px-4 py-3 text-right font-bold">المكافأة</th>
                            <th class="px-4 py-3 text-right font-bold">التاريخ</th>
                            <th class="px-4 py-3 text-center font-bold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600 text-white flex items-center justify-center text-xs font-black shrink-0">
                                            <?php echo e(mb_substr($referral->referrer->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900"><?php echo e($referral->referrer->name ?? '—'); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo e($referral->referrer->phone ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xs font-black shrink-0">
                                            <?php echo e(mb_substr($referral->referred->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900"><?php echo e($referral->referred->name ?? '—'); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo e($referral->referred->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700"><?php echo e($referral->referralProgram->name ?? '—'); ?></td>
                                <td class="px-4 py-3 font-mono text-xs font-bold text-violet-700"><?php echo e($referral->referral_code ?? $referral->code ?? '—'); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($referral->status === 'completed'): ?>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مكتملة</span>
                                    <?php elseif($referral->status === 'pending'): ?>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">انتظار</span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">ملغاة</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800"><?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> ج.م</td>
                                <td class="px-4 py-3 font-bold text-emerald-700"><?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> ج.م</td>
                                <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($referral->created_at->format('Y-m-d')); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="<?php echo e(route('admin.referrals.show', $referral)); ?>" class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100" title="التفاصيل">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($referrals->hasPages()): ?>
                <div class="px-5 py-3 border-t border-slate-100"><?php echo e($referrals->links()); ?></div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="rounded-2xl bg-white border border-slate-200 p-14 text-center shadow-sm">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-3xl"><i class="fas fa-user-friends"></i></div>
            <p class="text-lg font-bold text-slate-700 mb-1">لا توجد إحالات</p>
            <p class="text-sm text-slate-500 mb-5">أنشئ برنامج إحالة أو شارك أكواد الورش مع الطلاب</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white font-bold text-sm">برنامج إحالة</a>
                <a href="<?php echo e(route('admin.workshop-promo-codes.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm">كود ورشة</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/referrals/index.blade.php ENDPATH**/ ?>