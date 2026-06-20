<?php
    /** @var \App\Models\HrJobPosting|null $job */
    $job = $job ?? null;
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
    <div class="md:col-span-2">
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">عنوان الوظيفة *</label>
        <input name="title" value="<?php echo e(old('title', $job->title ?? '')); ?>" required class="<?php echo e($hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm'); ?>">
    </div>
    <div>
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">القسم</label>
        <input name="department" value="<?php echo e(old('department', $job->department ?? '')); ?>" class="<?php echo e($hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm'); ?>">
    </div>
    <div>
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">المكان</label>
        <input name="location" value="<?php echo e(old('location', $job->location ?? '')); ?>" class="<?php echo e($hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm'); ?>">
    </div>
    <div>
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">نوع التوظيف</label>
        <input name="employment_type" value="<?php echo e(old('employment_type', $job->employment_type ?? '')); ?>" placeholder="دوام كامل / جزئي / عن بُعد…" class="<?php echo e($hrInputClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm'); ?>">
    </div>
    <div class="flex items-center gap-3 pt-2 md:pt-6">
        <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500/20"
               <?php if(old('is_published', $job->is_published ?? false)): echo 'checked'; endif; ?>>
        <label for="is_published" class="text-sm font-semibold text-slate-800">نشر الوظيفة في صفحة التوظيف</label>
    </div>
    <div class="md:col-span-2">
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">الوصف</label>
        <textarea name="description" rows="6" class="<?php echo e($hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y min-h-[100px]'); ?>"><?php echo e(old('description', $job->description ?? '')); ?></textarea>
    </div>
    <div class="md:col-span-2">
        <label class="<?php echo e($hrLabelClass ?? 'block text-xs font-semibold text-slate-700 mb-1.5'); ?>">المتطلبات</label>
        <textarea name="requirements" rows="5" class="<?php echo e($hrTextareaClass ?? 'w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm resize-y min-h-[100px]'); ?>"><?php echo e(old('requirements', $job->requirements ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\hr\jobs\_form.blade.php ENDPATH**/ ?>