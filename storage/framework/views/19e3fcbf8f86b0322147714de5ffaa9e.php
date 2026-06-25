<?php $__env->startSection('title', 'تعديل دورة #'.$designTaskCycle->id); ?>
<?php $__env->startSection('header', 'تعديل دورة تصميم #'.$designTaskCycle->id); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500';
    $textareaClass = $inputClass.' resize-y';
    $locked = in_array($designTaskCycle->status, [\App\Models\DesignTaskCycle::STATUS_COMPLETED, \App\Models\DesignTaskCycle::STATUS_CANCELLED], true);
?>

<div class="space-y-6">
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <?php if($locked): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-lock ml-1"></i> هذه الدورة <strong><?php echo e(\App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status)); ?></strong> — يمكن تعديل المحتوى والملاحظات فقط، وليس تغيير المشرف أو المصمم.
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.design-task-cycles.show', $designTaskCycle)); ?>" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تعديل: <?php echo e($designTaskCycle->title); ?></h2>
                    <p class="text-xs text-slate-600">#<?php echo e($designTaskCycle->id); ?> — <?php echo e(\App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status)); ?></p>
                </div>
            </div>
        </div>

        <form method="post" action="<?php echo e(route('admin.design-task-cycles.update', $designTaskCycle)); ?>" class="p-4 sm:p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <div class="xl:col-span-8 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الطلب *</label>
                        <input type="text" name="title" value="<?php echo e(old('title', $designTaskCycle->title)); ?>" required class="<?php echo e($inputClass); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">وصف مختصر</label>
                        <textarea name="description" rows="3" class="<?php echo e($textareaClass); ?>"><?php echo e(old('description', $designTaskCycle->description)); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">تفاصيل التصميم *</label>
                        <textarea name="specifications" rows="12" required class="<?php echo e($textareaClass); ?> min-h-[16rem]"><?php echo e(old('specifications', $designTaskCycle->specifications)); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات الإدارة</label>
                        <textarea name="admin_notes" rows="3" class="<?php echo e($textareaClass); ?>"><?php echo e(old('admin_notes', $designTaskCycle->admin_notes)); ?></textarea>
                    </div>
                </div>

                <div class="xl:col-span-4 space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-4">
                        <h3 class="text-sm font-black text-slate-900">الإسناد والحالة</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المشرف *</label>
                            <select name="moderator_id" <?php echo e($locked ? 'disabled' : 'required'); ?> class="<?php echo e($inputClass); ?> <?php echo e($locked ? 'bg-slate-100' : ''); ?>">
                                <?php $__currentLoopData = $moderators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($m->id); ?>" <?php if(old('moderator_id', $designTaskCycle->moderator_id) == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($locked): ?><input type="hidden" name="moderator_id" value="<?php echo e($designTaskCycle->moderator_id); ?>"><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المصمم *</label>
                            <select name="designer_employee_id" <?php echo e($locked ? 'disabled' : 'required'); ?> class="<?php echo e($inputClass); ?> <?php echo e($locked ? 'bg-slate-100' : ''); ?>">
                                <?php $__currentLoopData = $designers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php if(old('designer_employee_id', $designTaskCycle->designer_employee_id) == $d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($locked): ?><input type="hidden" name="designer_employee_id" value="<?php echo e($designTaskCycle->designer_employee_id); ?>"><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الأولوية *</label>
                            <select name="priority" required class="<?php echo e($inputClass); ?>">
                                <?php $__currentLoopData = ['low', 'medium', 'high', 'urgent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($v); ?>" <?php if(old('priority', $designTaskCycle->priority) === $v): echo 'selected'; endif; ?>><?php echo e(\App\Models\DesignTaskCycle::priorityLabel($v)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">حد التسليم *</label>
                            <input type="datetime-local" name="deadline_at" value="<?php echo e(old('deadline_at', $designTaskCycle->deadline_at?->format('Y-m-d\TH:i'))); ?>" required class="<?php echo e($inputClass); ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة *</label>
                            <select name="status" required class="<?php echo e($inputClass); ?>">
                                <?php $__currentLoopData = ['pending_design','design_in_progress','design_submitted','moderator_delivery_pending','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st); ?>" <?php if(old('status', $designTaskCycle->status) === $st): echo 'selected'; endif; ?>><?php echo e(\App\Models\DesignTaskCycle::statusLabel($st)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-sm font-bold">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="<?php echo e(route('admin.design-task-cycles.show', $designTaskCycle)); ?>" class="block text-center text-sm text-slate-600 hover:text-slate-900">إلغاء</a>
                </div>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/design-task-cycles/edit.blade.php ENDPATH**/ ?>