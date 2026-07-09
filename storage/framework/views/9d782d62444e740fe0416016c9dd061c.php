

<?php $__env->startSection('title', 'تعديل قالب مقترح — ' . $suggested->title); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'تعديل: ' . $suggested->title,
        'subtitle' => $suggested->categoryLabel() . ' · ' . strtoupper($suggested->language),
        'icon' => 'fas fa-wand-magic-sparkles',
        'actions' => '
            <a href="' . route('admin.whatsapp.templates.index', ['tab' => 'suggested']) . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> المكتبة</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($suggested->metaTemplate): ?>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sm text-sky-900 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-bold">مسودة Meta مرتبطة</p>
                <p class="text-xs mt-1 font-mono dir-ltr"><?php echo e($suggested->metaTemplate->name); ?> · <?php echo e($suggested->metaTemplate->statusLabel()); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.whatsapp.templates.show', $suggested->metaTemplate)); ?>" class="<?php echo e($waBtnSecondary); ?> text-xs">عرض Meta</a>
                <?php if($suggested->metaTemplate->isEditable()): ?>
                    <a href="<?php echo e(route('admin.whatsapp.templates.edit', $suggested->metaTemplate)); ?>" class="<?php echo e($waBtnSecondary); ?> text-xs">تعديل Meta</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.suggested.update', $suggested)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <section class="<?php echo e($waSectionClass); ?> p-5 sm:p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">العنوان</label>
                    <input type="text" name="title" value="<?php echo e(old('title', $suggested->title)); ?>" required class="<?php echo e($waInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">التصنيف</label>
                    <select name="category" class="<?php echo e($waSelectClass); ?>">
                        <?php $__currentLoopData = \App\Models\WhatsAppSuggestedTemplate::categoryLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(old('category', $suggested->category) === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">اللغة</label>
                    <select name="language" class="<?php echo e($waSelectClass); ?>">
                        <option value="ar" <?php if(old('language', $suggested->language) === 'ar'): echo 'selected'; endif; ?>>العربية</option>
                        <option value="en" <?php if(old('language', $suggested->language) === 'en'): echo 'selected'; endif; ?>>English</option>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $suggested->sort_order)); ?>" class="<?php echo e($waInputClass); ?>">
                </div>
            </div>

            <div>
                <label class="<?php echo e($waLabelClass); ?>">نص الرسالة</label>
                <p class="text-[11px] text-slate-500 mb-2">استخدم متغيرات بصيغة <code>{{name}}</code> — سيتم تحويلها تلقائياً إلى <code>{{1}}</code> عند إرسال Meta.</p>
                <textarea name="body" rows="8" required class="<?php echo e($waInputClass); ?> text-sm leading-relaxed"><?php echo e(old('body', $suggested->body)); ?></textarea>
            </div>

            <div>
                <label class="<?php echo e($waLabelClass); ?>">شرح الاستخدام (للسيلز)</label>
                <textarea name="help" rows="5" class="<?php echo e($waInputClass); ?> text-sm leading-relaxed"><?php echo e(old('help', $suggested->help)); ?></textarea>
            </div>

            <div>
                <label class="<?php echo e($waLabelClass); ?>">المتغيرات (مفصولة بفاصلة)</label>
                <input type="text" name="variables_text"
                       value="<?php echo e(old('variables_text', implode(', ', $suggested->variables ?? []))); ?>"
                       placeholder="name, agent, topic"
                       class="<?php echo e($waInputClass); ?> dir-ltr text-left font-mono text-sm">
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $suggested->is_active)): echo 'checked'; endif; ?> class="rounded text-emerald-600">
                نشط في المكتبة
            </label>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="<?php echo e($waBtnPrimary); ?>"><i class="fas fa-save"></i> حفظ</button>
                <?php if($suggested->metaTemplate?->isEditable()): ?>
                    <button type="submit" name="sync_meta_draft" value="1" class="<?php echo e($waBtnSecondary); ?>">
                        <i class="fas fa-sync"></i> حفظ + تحديث مسودة Meta
                    </button>
                <?php endif; ?>
            </div>
        </section>
    </form>

    <section class="<?php echo e($waSectionClass); ?> p-5 space-y-3">
        <h3 class="font-bold text-slate-900">إرسال إلى Meta</h3>
        <p class="text-sm text-slate-600">حوّل هذا القالب إلى مسودة Meta (متغيرات مرقّمة) ثم أرسله للاعتماد.</p>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.suggested.meta-draft', $suggested)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="<?php echo e($waBtnSecondary); ?>">
                    <i class="fas fa-file-export"></i>
                    <?php echo e($suggested->metaTemplate ? 'تحديث مسودة Meta' : 'إنشاء مسودة Meta'); ?>

                </button>
            </form>
            <form method="POST" action="<?php echo e(route('admin.whatsapp.templates.suggested.submit-meta', $suggested)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="<?php echo e($waBtnPrimary); ?>">
                    <i class="fab fa-meta"></i> إرسال إلى Meta للاعتماد
                </button>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\templates\suggested-edit.blade.php ENDPATH**/ ?>