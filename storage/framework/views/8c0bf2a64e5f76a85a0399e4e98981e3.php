<?php $__env->startSection('title', 'تأكيد الحضور — '.$workshop->title); ?>
<?php $__env->startSection('header', 'تأكيد حضور الورشة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-bold text-violet-600 mb-1">تأكيد الحضور والشهادات</p>
                <h2 class="text-2xl font-black text-slate-900"><?php echo e($workshop->title); ?></h2>
                <p class="text-sm text-slate-600 mt-1">كل من أكّد حضوره من الصفحة العامة — بيانات كاملة للمتابعة وإصدار الشهادات</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.workshops.show', $workshop)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> الورشة
                </a>
                <a href="<?php echo e($confirmUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold">
                    <i class="fas fa-external-link-alt"></i> الصفحة العامة
                </a>
                <button type="button"
                        onclick="navigator.clipboard.writeText(<?php echo \Illuminate\Support\Js::from($confirmUrl)->toHtml() ?>); this.querySelector('span').textContent='تم النسخ'; setTimeout(() => this.querySelector('span').textContent='نسخ رابط التأكيد', 2000)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 bg-violet-50 text-violet-800 text-sm font-semibold hover:bg-violet-100">
                    <i class="fas fa-copy"></i><span>نسخ رابط التأكيد</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">مؤكّدو الحضور</p>
                <p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($confirmedAttendees->count())); ?></p>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs text-slate-500">تاريخ الورشة</p>
                <p class="text-sm font-bold text-slate-800 mt-1"><?php echo e($workshop->starts_at?->format('Y-m-d H:i') ?? '—'); ?></p>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 p-4 col-span-2 sm:col-span-1">
                <p class="text-xs text-slate-500">رابط المشاركين</p>
                <p class="text-[11px] font-mono text-violet-700 mt-1 break-all leading-relaxed"><?php echo e($confirmUrl); ?></p>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">#</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الاسم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">الهاتف</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">البريد</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">نوع الحضور</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">وقت التأكيد</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">تاريخ التسجيل</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php $__empty_1 = true; $__currentLoopData = $confirmedAttendees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-emerald-50/30">
                                <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($reg->id); ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                            <?php echo e(mb_substr($reg->name, 0, 1)); ?>

                                        </span>
                                        <span class="font-bold text-slate-900"><?php echo e($reg->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-800 whitespace-nowrap" dir="ltr"><?php echo e($reg->phone ?? '—'); ?></td>
                                <td class="px-4 py-3 text-slate-700"><?php echo e($reg->email ?? '—'); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                        $mode = $reg->attendance_mode === 'offline' ? 'أوفلاين' : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—');
                                    ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold"><?php echo e($mode); ?></span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-xs">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo e($reg->checked_in_at?->format('Y-m-d H:i')); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap"><?php echo e($reg->created_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-600 max-w-[180px] truncate" title="<?php echo e($reg->notes); ?>"><?php echo e($reg->notes ?: '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    <i class="fas fa-user-clock text-4xl text-slate-300 mb-3 block"></i>
                                    <p class="font-semibold">لا يوجد مؤكّدون بعد</p>
                                    <p class="text-xs mt-2 max-w-sm mx-auto">شارك رابط التأكيد مع الحاضرين — ستظهر بياناتهم هنا فور التأكيد</p>
                                    <a href="<?php echo e($confirmUrl); ?>" target="_blank" class="inline-flex items-center gap-2 mt-4 text-sm font-bold text-violet-600 hover:underline">
                                        <i class="fas fa-external-link-alt"></i> فتح الصفحة العامة
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\confirmations.blade.php ENDPATH**/ ?>