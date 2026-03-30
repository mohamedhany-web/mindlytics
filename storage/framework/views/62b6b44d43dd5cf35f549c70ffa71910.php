<?php
    $ms = ($depth ?? 0) * 14;
?>
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" style="margin-inline-start: <?php echo e($ms); ?>px">
    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/80">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="text-lg font-bold text-slate-800"><?php echo e($section->title); ?></h3>
                <?php if($section->description && !$section->parent_id): ?>
                    <p class="text-sm text-slate-600 mt-1 whitespace-pre-line"><?php echo e($section->description); ?></p>
                <?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.move', [$offlineCourse, $section])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="up">
                    <button type="submit" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" title="أعلى"><i class="fas fa-chevron-up text-xs"></i></button>
                </form>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.move', [$offlineCourse, $section])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="down">
                    <button type="submit" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" title="أسفل"><i class="fas fa-chevron-down text-xs"></i></button>
                </form>
                <details class="relative">
                    <summary class="list-none cursor-pointer p-2 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-semibold"><i class="fas fa-edit ml-1"></i> تعديل</summary>
                    <div class="absolute left-0 z-20 mt-1 w-72 rounded-xl border border-slate-200 bg-white shadow-lg p-3">
                        <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.update', [$offlineCourse, $section])); ?>" method="POST" class="space-y-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="text" name="title" value="<?php echo e($section->title); ?>" required class="w-full text-sm rounded-lg border-slate-200">
                            <?php if(!$section->parent_id): ?>
                                <textarea name="description" rows="2" class="w-full text-sm rounded-lg border-slate-200" placeholder="وصف"><?php echo e($section->description); ?></textarea>
                            <?php endif; ?>
                            <button type="submit" class="w-full py-2 rounded-lg bg-sky-500 text-white text-xs font-bold">حفظ</button>
                        </form>
                    </div>
                </details>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.destroy', [$offlineCourse, $section])); ?>" method="POST" class="inline" onsubmit="return confirm('حذف القسم وجميع الأقسام الفرعية والعناصر؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-2 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-200/80">
            <p class="text-xs font-bold text-slate-500 mb-2">قسم فرعي</p>
            <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.store', $offlineCourse)); ?>" method="POST" class="flex flex-wrap gap-2 items-end">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="parent_id" value="<?php echo e($section->id); ?>">
                <input type="text" name="title" required placeholder="عنوان القسم الفرعي" class="flex-1 min-w-[160px] text-sm rounded-lg border-slate-200 px-3 py-2">
                <button type="submit" class="px-3 py-2 rounded-lg bg-slate-700 text-white text-xs font-bold">إضافة</button>
            </form>
        </div>
    </div>

    <div class="p-4 sm:p-5 space-y-3">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">عناصر القسم</p>
        <?php $__empty_1 = true; $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $m = $cItem->item;
                $label = $m && isset($m->title) ? $m->title : '(عنصر غير متوفر)';
                [$badgeLabel, $badgeClass] = match ($cItem->item_type) {
                    \App\Models\OfflineLecture::class => ['محاضرة', 'bg-violet-100 text-violet-800'],
                    \App\Models\OfflineCourseResource::class => ['مورد', 'bg-sky-100 text-sky-800'],
                    \App\Models\OfflineActivity::class => ['نشاط', 'bg-amber-100 text-amber-800'],
                    \App\Models\AdvancedExam::class => ['اختبار', 'bg-emerald-100 text-emerald-800'],
                    \App\Models\OfflineCurriculumNote::class => ['نص', 'bg-slate-200 text-slate-800'],
                    default => ['عنصر', 'bg-gray-100 text-gray-800'],
                };
            ?>
            <div class="flex flex-wrap items-center gap-2 p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md <?php echo e($badgeClass); ?>"><?php echo e($badgeLabel); ?></span>
                <span class="flex-1 min-w-0 text-sm font-semibold text-slate-800 truncate"><?php echo e($label); ?></span>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.move', [$offlineCourse, $cItem])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="up">
                    <button type="submit" class="p-1.5 rounded border border-slate-200 bg-white text-slate-500"><i class="fas fa-chevron-up text-[10px]"></i></button>
                </form>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.move', [$offlineCourse, $cItem])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="down">
                    <button type="submit" class="p-1.5 rounded border border-slate-200 bg-white text-slate-500"><i class="fas fa-chevron-down text-[10px]"></i></button>
                </form>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.destroy', [$offlineCourse, $cItem])); ?>" method="POST" class="inline" onsubmit="return confirm('إزالة من المنهج؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-1.5 rounded border border-red-100 bg-red-50 text-red-600 text-xs font-bold">إزالة</button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 py-2">لا عناصر في هذا القسم بعد.</p>
        <?php endif; ?>

        <div class="pt-2 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 mb-2">ملاحظة توضيحية (نص للطلاب)</p>
            <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.notes.store', [$offlineCourse, $section])); ?>" method="POST" class="space-y-2">
                <?php echo csrf_field(); ?>
                <input type="text" name="title" required placeholder="عنوان الملاحظة" class="w-full text-sm rounded-lg border-slate-200 px-3 py-2">
                <textarea name="body" rows="2" placeholder="المحتوى (اختياري)" class="w-full text-sm rounded-lg border-slate-200 px-3 py-2"></textarea>
                <button type="submit" class="px-3 py-2 rounded-lg bg-slate-600 text-white text-xs font-bold">إضافة للقسم</button>
            </form>
        </div>
    </div>

    <?php if($section->children && $section->children->isNotEmpty()): ?>
        <div class="border-t border-slate-100 p-3 space-y-3 bg-white">
            <?php $__currentLoopData = $section->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('instructor.offline-curriculum.partials.section-block', ['section' => $child, 'depth' => ($depth ?? 0) + 1, 'offlineCourse' => $offlineCourse], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-curriculum/partials/section-block.blade.php ENDPATH**/ ?>