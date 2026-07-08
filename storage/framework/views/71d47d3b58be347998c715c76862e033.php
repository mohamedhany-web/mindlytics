

<?php $__env->startSection('title', $template->name . ' — قالب واتساب'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusClass = match($template->status) {
        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => $template->name,
        'subtitle' => $template->language . ' · ' . $template->categoryLabel(),
        'icon' => 'fas fa-file-alt',
        'actions' => '
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold border ' . $statusClass . '">' . $template->statusLabel() . '</span>
            ' . ($template->isEditable() ? '<a href="' . route('admin.whatsapp.templates.edit', $template) . '" class="' . $waBtnSecondary . '"><i class="fas fa-edit"></i> تعديل</a>' : '') . '
            <a href="' . route('admin.whatsapp.templates.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-list"></i> القائمة</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($template->rejection_reason): ?>
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900">
            <p class="font-bold mb-1"><i class="fas fa-times-circle ml-1"></i> سبب الرفض من Meta</p>
            <p><?php echo e($template->rejection_reason); ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <section class="<?php echo e($waSectionClass); ?> p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-align-right text-emerald-600"></i> معاينة المحتوى</h3>
                <?php if($template->header_type && $template->header_content): ?>
                    <div class="text-sm font-bold text-slate-800 mb-2 pb-2 border-b border-slate-100">
                        <?php if($template->header_type === 'text'): ?>
                            <?php echo e($template->header_content); ?>

                        <?php else: ?>
                            <span class="text-xs text-slate-500 uppercase"><?php echo e($template->header_type); ?></span>
                            <span class="block font-mono text-xs dir-ltr"><?php echo e($template->header_content); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <p class="text-slate-700 whitespace-pre-wrap leading-relaxed"><?php echo e($template->body_text); ?></p>
                <?php if($template->footer_text): ?>
                    <p class="text-xs text-slate-500 mt-4 pt-2 border-t"><?php echo e($template->footer_text); ?></p>
                <?php endif; ?>
                <?php if($template->buttons): ?>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php $__currentLoopData = $template->buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $btn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                                <?php if(($btn['type'] ?? '') === 'URL'): ?><i class="fas fa-link"></i>
                                <?php elseif(($btn['type'] ?? '') === 'PHONE_NUMBER'): ?><i class="fas fa-phone"></i>
                                <?php else: ?><i class="fas fa-reply"></i><?php endif; ?>
                                <?php echo e($btn['text'] ?? ''); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>

            <?php if($template->components): ?>
                <section class="<?php echo e($waSectionClass); ?>">
                    <div class="px-5 py-3 border-b font-bold text-slate-900 text-sm">JSON المُرسل لـ Meta</div>
                    <pre class="p-5 text-xs overflow-x-auto dir-ltr text-left bg-slate-50 text-slate-700 max-h-64"><?php echo e(json_encode($template->components, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                </section>
            <?php endif; ?>

            <?php if(($templateAccessMode ?? 'all') === 'restricted'): ?>
                <section class="<?php echo e($waSectionClass); ?> p-5">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-user-shield text-violet-600"></i>
                        الموظفون المصرّح لهم
                    </h3>
                    <?php if(($salesStaff ?? collect())->isEmpty()): ?>
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            لا يوجد موظفو مبيعات نشطون لإسناد القالب إليهم.
                        </p>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.access', $template)); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="grid sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto border border-slate-100 rounded-xl p-3 bg-slate-50/50">
                                <?php $assignedIds = $template->assignedUsers->pluck('id')->all(); ?>
                                <?php $__currentLoopData = $salesStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 text-sm text-slate-800 cursor-pointer rounded-lg px-2 py-1.5 hover:bg-white">
                                        <input type="checkbox" name="user_ids[]" value="<?php echo e($staff->id); ?>"
                                               <?php if(in_array($staff->id, $assignedIds, true)): echo 'checked'; endif; ?>
                                               class="rounded text-violet-600">
                                        <span><?php echo e($staff->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <button type="submit" class="<?php echo e($waBtnPrimary); ?> text-sm w-full sm:w-auto justify-center">
                                <i class="fas fa-save"></i> حفظ الصلاحيات
                            </button>
                        </form>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>

        <aside class="space-y-4">
            <section class="<?php echo e($waSectionClass); ?> p-5 text-sm space-y-3">
                <p><span class="text-slate-500">Meta ID:</span> <span class="font-mono text-xs dir-ltr"><?php echo e($template->meta_template_id ?? '—'); ?></span></p>
                <p><span class="text-slate-500">المتغيرات:</span> <strong><?php echo e($template->body_variable_count); ?></strong></p>
                <p><span class="text-slate-500">أُرسل إلى Meta:</span> <?php echo e($template->submitted_at?->format('Y-m-d H:i') ?? '—'); ?></p>
                <p><span class="text-slate-500">آخر مزامنة:</span> <?php echo e($template->meta_synced_at?->diffForHumans() ?? '—'); ?></p>
                <p><span class="text-slate-500">أنشأه:</span> <?php echo e($template->creator?->name ?? '—'); ?></p>
            </section>

            <div class="space-y-2">
                <?php if(in_array($template->status, ['draft', 'rejected'])): ?>
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.submit', $template)); ?>"><?php echo csrf_field(); ?>
                        <button type="submit" class="<?php echo e($waBtnPrimary); ?> w-full justify-center">
                            <i class="fab fa-meta"></i> Submit to Meta
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($template->status === 'pending'): ?>
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.sync')); ?>"><?php echo csrf_field(); ?>
                        <button type="submit" class="<?php echo e($waBtnSecondary); ?> w-full justify-center">
                            <i class="fas fa-sync"></i> تحديث الحالة من Meta
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($template->isSendable()): ?>
                    <a href="<?php echo e(route('admin.whatsapp.inbox')); ?>" class="<?php echo e($waBtnPrimary); ?> w-full justify-center">
                        <i class="fas fa-paper-plane"></i> استخدام في المحادثات
                    </a>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.destroy', $template)); ?>"
                      onsubmit="return confirm('حذف هذا القالب؟');">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="delete_from_meta" value="1">
                    <button type="submit" class="w-full text-sm text-rose-700 border border-rose-200 rounded-xl py-2.5 hover:bg-rose-50 bg-white">
                        حذف القالب
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\templates\show.blade.php ENDPATH**/ ?>