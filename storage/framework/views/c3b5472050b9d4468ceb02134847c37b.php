<?php $__env->startSection('title', 'Practice #' . $pattern->id); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <a href="<?php echo e(route('admin.practice.index')); ?>" class="text-sm font-bold text-blue-700 hover:text-blue-800">← الرجوع لقائمة التمارين</a>
                <h1 class="text-2xl font-black text-slate-900 mt-2"><?php echo e($pattern->title ?: 'Practice'); ?></h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-bold text-sm">
                        <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?>"></i>
                        <?php echo e($typeInfo['name'] ?? $pattern->type); ?>

                    </span>
                    <?php if($pattern->is_active): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs">نشط</span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-black text-xs">غير نشط</span>
                    <?php endif; ?>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-black text-xs">ID: <?php echo e($pattern->id); ?></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h2 class="font-black text-slate-900 mb-3">الوصف</h2>
                <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($pattern->description ?: '—'); ?></p>

                <h2 class="font-black text-slate-900 mt-6 mb-3">التعليمات</h2>
                <div class="text-slate-800 whitespace-pre-wrap bg-slate-50 border border-slate-100 rounded-2xl p-4"><?php echo e($pattern->instructions ?: '—'); ?></div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h2 class="font-black text-slate-900 mb-3">بيانات سريعة</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Course</span>
                        <span class="text-slate-900 font-black text-end"><?php echo e($pattern->course?->title ?? '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Course ID</span>
                        <span class="text-slate-900 font-black"><?php echo e($pattern->advanced_course_id); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Instructor</span>
                        <span class="text-slate-900 font-black text-end"><?php echo e($pattern->instructor?->name ?? '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Points</span>
                        <span class="text-slate-900 font-black"><?php echo e($pattern->points ?? 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Time Limit</span>
                        <span class="text-slate-900 font-black"><?php echo e($pattern->time_limit_minutes ? ($pattern->time_limit_minutes . ' min') : '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-500 font-bold">Difficulty</span>
                        <span class="text-slate-900 font-black"><?php echo e($pattern->difficulty_level ?? '—'); ?></span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h3 class="font-black text-slate-900 mb-2">Pattern Data</h3>
                    <pre class="text-xs bg-slate-900 text-slate-100 rounded-2xl p-4 overflow-auto"><?php echo e(json_encode($pattern->pattern_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); ?></pre>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\practice\show.blade.php ENDPATH**/ ?>