<?php $__env->startSection('title', 'تذاكر الدعم'); ?>

<?php $__env->startSection('page_title', 'تذاكر الدعم'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-5">
        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm p-4 sm:p-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-slate-700 mb-1">بحث</label>
                    <input name="q" value="<?php echo e(request('q')); ?>" class="w-full rounded-xl border-slate-200 focus:border-blue-400 focus:ring-blue-400" placeholder="اسم / بريد / موضوع" />
                </div>
                <div class="w-full sm:w-56">
                    <label class="block text-sm font-bold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border-slate-200 focus:border-blue-400 focus:ring-blue-400">
                        <option value="">الكل</option>
                        <option value="open" <?php if(request('status')==='open'): echo 'selected'; endif; ?>>مفتوحة</option>
                        <option value="closed" <?php if(request('status')==='closed'): echo 'selected'; endif; ?>>مغلقة</option>
                    </select>
                </div>
                <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">تصفية</button>
            </form>
        </div>

        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200/60">
                <div class="text-slate-800 font-black">كل التذاكر</div>
                <div class="text-xs text-slate-500 mt-1">تذاكر قادمة من التطبيق أو الموقع</div>
            </div>

            <div class="divide-y divide-slate-200/60">
                <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.support-tickets.show', $t)); ?>" class="block p-4 sm:p-5 hover:bg-slate-50/80 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-800 truncate"><?php echo e($t->subject); ?></span>
                                    <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($t->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                                        <?php echo e($t->status === 'open' ? 'مفتوحة' : 'مغلقة'); ?>

                                    </span>
                                </div>
                                <div class="text-sm text-slate-600 mt-1 truncate">
                                    <?php echo e($t->user?->name ?? '—'); ?> · <?php echo e($t->user?->email ?? '—'); ?> · <?php echo e($t->role); ?>

                                </div>
                            </div>
                            <div class="text-xs text-slate-500 whitespace-nowrap">
                                <?php echo e(optional($t->last_message_at)->diffForHumans() ?? $t->created_at->diffForHumans()); ?>

                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-6 text-slate-600">لا توجد تذاكر حالياً.</div>
                <?php endif; ?>
            </div>

            <div class="p-4 sm:p-5 border-t border-slate-200/60">
                <?php echo e($tickets->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\support-tickets\index.blade.php ENDPATH**/ ?>