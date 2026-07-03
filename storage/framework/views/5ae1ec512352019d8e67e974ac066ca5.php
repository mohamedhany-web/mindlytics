<?php $__env->startSection('title', 'تذكرة دعم'); ?>

<?php $__env->startSection('page_title'); ?>
    تذكرة #<?php echo e($ticket->id); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-5">
        <?php if(session('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 font-bold">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-slate-900 font-black text-lg truncate"><?php echo e($ticket->subject); ?></div>
                    <div class="text-sm text-slate-600 mt-1">
                        <?php echo e($ticket->user?->name ?? '—'); ?> · <?php echo e($ticket->user?->email ?? '—'); ?> · <?php echo e($ticket->role); ?>

                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full <?php echo e($ticket->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                        <?php echo e($ticket->status === 'open' ? 'مفتوحة' : 'مغلقة'); ?>

                    </span>
                    <?php if($ticket->status === 'open'): ?>
                        <form method="POST" action="<?php echo e(route('admin.support-tickets.close', $ticket)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="px-3 py-2 rounded-xl bg-slate-900 text-white font-black hover:bg-slate-800">
                                إغلاق
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200/60">
                <div class="text-slate-800 font-black">المحادثة</div>
            </div>

            <div class="p-4 sm:p-5 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $ticket->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isAdmin = $m->sender && ($m->sender->isAdmin() || $m->sender->isSuperAdmin());
                    ?>
                    <div class="flex <?php echo e($isAdmin ? 'justify-end' : 'justify-start'); ?>">
                        <div class="max-w-[780px] w-full sm:w-auto rounded-2xl border px-4 py-3 <?php echo e($isAdmin ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200'); ?>">
                            <div class="text-xs text-slate-500 mb-1 flex items-center justify-between gap-3">
                                <span class="font-bold">
                                    <?php echo e($m->sender?->name ?? '—'); ?>

                                </span>
                                <span class="whitespace-nowrap"><?php echo e($m->created_at->diffForHumans()); ?></span>
                            </div>
                            <div class="text-slate-800 whitespace-pre-wrap leading-relaxed"><?php echo e($m->body); ?></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-slate-600">لا توجد رسائل.</div>
                <?php endif; ?>
            </div>

            <?php if($ticket->status === 'open'): ?>
                <div class="p-4 sm:p-5 border-t border-slate-200/60">
                    <form method="POST" action="<?php echo e(route('admin.support-tickets.reply', $ticket)); ?>" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <textarea name="body" rows="4" class="w-full rounded-2xl border-slate-200 focus:border-blue-400 focus:ring-blue-400" placeholder="اكتب رد الإدارة..."></textarea>
                        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-sm text-red-600 font-bold"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="flex justify-end">
                            <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">
                                إرسال الرد
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\support-tickets\show.blade.php ENDPATH**/ ?>