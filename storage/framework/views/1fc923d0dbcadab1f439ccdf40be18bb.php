<?php $__env->startSection('title', 'دورة تصميم جديدة'); ?>
<?php $__env->startSection('header', 'إنشاء دورة تصميم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500';
    $textareaClass = $inputClass.' resize-y';
?>

<div class="space-y-6">
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($moderators->isEmpty() || $designers->isEmpty()): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle ml-1"></i> تنبيه</p>
            <?php if($moderators->isEmpty()): ?><p>لا يوجد مشرفون نشطون — أنشئ وظيفة مشرف وعيّنها لموظف.</p><?php endif; ?>
            <?php if($designers->isEmpty()): ?><p>لا يوجد مصممون نشطون — أنشئ وظيفة مصمم وعيّنها لموظف.</p><?php endif; ?>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-gradient-to-l from-fuchsia-50 to-white border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.design-task-cycles.index')); ?>" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">دورة تصميم جديدة</h2>
                    <p class="text-xs text-slate-600">اختر المشرف والمصمم — ستُنشأ مهمة تلقائياً للمصمم مع إشعار.</p>
                </div>
            </div>
        </div>

        <form method="post" action="<?php echo e(route('admin.design-task-cycles.store')); ?>" class="p-4 sm:p-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <div class="xl:col-span-8 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الطلب *</label>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" required maxlength="255" placeholder="مثال: غلاف إعلان لكورس X" class="<?php echo e($inputClass); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">وصف مختصر</label>
                        <textarea name="description" rows="3" placeholder="سياق الطلب..." class="<?php echo e($textareaClass); ?>"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">تفاصيل التصميم المطلوب *</label>
                        <textarea name="specifications" rows="12" required placeholder="الأبعاد، الألوان، الخطوط، النصوص، المراجع..." class="<?php echo e($textareaClass); ?> min-h-[16rem]"><?php echo e(old('specifications')); ?></textarea>
                    </div>
                </div>

                <div class="xl:col-span-4 space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-4">
                        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2"><i class="fas fa-users text-fuchsia-600"></i> الإسناد</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المشرف *</label>
                            <select name="moderator_id" required class="<?php echo e($inputClass); ?>" <?php echo e($moderators->isEmpty() ? 'disabled' : ''); ?>>
                                <option value="">— اختر المشرف —</option>
                                <?php $__currentLoopData = $moderators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($m->id); ?>" <?php if(old('moderator_id') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?><?php if($m->employeeJob): ?> — <?php echo e($m->employeeJob->name); ?><?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المصمم *</label>
                            <select name="designer_employee_id" required class="<?php echo e($inputClass); ?>" <?php echo e($designers->isEmpty() ? 'disabled' : ''); ?>>
                                <option value="">— اختر المصمم —</option>
                                <?php $__currentLoopData = $designers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id); ?>" <?php if(old('designer_employee_id') == $d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?><?php if($d->employeeJob): ?> — <?php echo e($d->employeeJob->name); ?><?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الأولوية *</label>
                            <select name="priority" required class="<?php echo e($inputClass); ?>">
                                <?php $__currentLoopData = ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($v); ?>" <?php if(old('priority', 'medium') === $v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">حد تسليم المصمم *</label>
                            <input type="datetime-local" name="deadline_at" value="<?php echo e(old('deadline_at')); ?>" required class="<?php echo e($inputClass); ?>">
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-sm font-bold shadow-sm disabled:opacity-50"
                        <?php echo e(($moderators->isEmpty() || $designers->isEmpty()) ? 'disabled' : ''); ?>>
                        <i class="fas fa-paper-plane"></i>
                        إنشاء الدورة وإسناد المهمة
                    </button>
                </div>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/design-task-cycles/create.blade.php ENDPATH**/ ?>