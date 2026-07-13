

<?php $__env->startSection('title', 'قالب ترحيب الورشة'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'إنشاء قالب ترحيب — ' . $workshop->title,
        'subtitle' => 'نفس نموذج قوالب Meta الرسمي — Header، Body، Footer، أزرار، ومتغيرات مثل رابط الجروب.',
        'icon' => 'fas fa-file-alt',
        'actions' => '<a href="' . route('admin.workshops.show', $workshop) . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> العودة للورشة</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(empty(trim((string) $workshop->whatsapp_group_link))): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <strong>تلميح:</strong> لم تُحدّد بعد
            <a href="<?php echo e(route('admin.workshops.edit', $workshop)); ?>" class="font-bold underline">رابط جروب واتساب</a>
            للورشة. يمكنك استخدام <code dir="ltr" class="bg-white px-1 rounded">{{3}}</code> في النص أو زر URL ديناميكي.
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            رابط الجروب المحفوظ:
            <a href="<?php echo e($workshop->whatsapp_group_link); ?>" target="_blank" rel="noopener" class="font-bold underline break-all" dir="ltr"><?php echo e($workshop->whatsapp_group_link); ?></a>
            — يُستخدم تلقائياً مع <code dir="ltr" class="bg-white px-1 rounded">{{3}}</code> أو أزرار URL الديناميكية.
        </div>
    <?php endif; ?>

    <section class="<?php echo e($waSectionClass); ?> p-5 sm:p-6">
        <div class="mb-5 rounded-xl border border-violet-100 bg-violet-50/60 p-4 text-xs text-slate-700 space-y-1">
            <p class="font-bold text-slate-900">متغيرات الورشة (Meta format)</p>
            <?php $__currentLoopData = $workshopVariableLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p><code dir="ltr" class="bg-white px-1.5 py-0.5 rounded border border-slate-200">{{{{ $num }}}}</code> — <?php echo e($label); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <p class="text-slate-500 pt-1">رابط جروب واتساب يُوضَع في <strong>نص الرسالة</strong> كـ <code dir="ltr" class="bg-white px-1 rounded">{{3}}</code> — Meta لا يقبل <code dir="ltr">chat.whatsapp.com</code> في أزرار URL.</p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.create', $workshop)); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="name" value="<?php echo e(old('name', $defaultTemplateName)); ?>">

            <?php echo $__env->make('admin.whatsapp.templates._form', [
                'template' => $template,
                'lockName' => true,
                'lockedName' => $defaultTemplateName,
                'defaultBody' => $defaultBody,
                'defaultButtons' => $defaultButtons,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <a href="<?php echo e(route('admin.workshops.show', $workshop)); ?>" class="<?php echo e($waBtnSecondary); ?>">
                    إلغاء
                </a>
                <button type="submit" name="submit_now" value="0" class="<?php echo e($waBtnSecondary); ?>">
                    <i class="fas fa-save"></i> حفظ كمسودة
                </button>
                <button type="submit" name="submit_now" value="1" class="<?php echo e($waBtnPrimary); ?>">
                    <i class="fab fa-meta"></i> Submit to Meta
                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\templates\workshop-create.blade.php ENDPATH**/ ?>