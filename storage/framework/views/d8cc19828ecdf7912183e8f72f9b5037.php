<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
    ?>
    <details class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm group" <?php if($loop->first && !($section->parent_id)): ?> open <?php endif; ?>>
        <summary class="px-4 py-3 bg-gradient-to-l from-sky-50 to-indigo-50 border-b border-slate-100 cursor-pointer list-none">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-900 text-base"><?php echo e($section->title); ?></h3>
                    <?php if($section->description && ! $section->parent_id): ?>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line"><?php echo e($section->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[11px] px-2 py-1 rounded-full bg-white border border-slate-200 text-slate-600 font-semibold">
                        <?php echo e($itemsCount); ?> عنصر
                    </span>
                    <?php if($childrenCount > 0): ?>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white border border-slate-200 text-slate-600 font-semibold">
                            <?php echo e($childrenCount); ?> قسم فرعي
                        </span>
                    <?php endif; ?>
                    <span class="w-7 h-7 rounded-md bg-white border border-slate-200 text-slate-600 inline-flex items-center justify-center transition-transform group-open:rotate-180">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>
        </summary>
        <div class="p-4 space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $m = $cItem->item; ?>
                <?php if(!$m): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <div class="flex flex-wrap items-start gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:border-sky-200 transition-colors">
                    <?php if($m instanceof \App\Models\OfflineCurriculumNote): ?>
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center"><i class="fas fa-align-right text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                            <?php if($m->body): ?>
                                <div class="text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line"><?php echo e($m->body); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php elseif($m instanceof \App\Models\OfflineLecture): ?>
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                            <?php if($m->description): ?>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2"><?php echo e(Str::limit(strip_tags($m->description), 120)); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.lectures', $offlineCourse)); ?>#offline-lecture-<?php echo e($m->id); ?>" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-violet-100 text-violet-700 border border-violet-200 hover:bg-violet-200">
                                عرض المحاضرة <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    <?php elseif($m instanceof \App\Models\OfflineCourseResource): ?>
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-file-alt text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                            <?php if($m->description): ?>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2"><?php echo e(Str::limit(strip_tags($m->description), 120)); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.resources', $offlineCourse)); ?>#offline-resource-<?php echo e($m->id); ?>" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700 border border-sky-200 hover:bg-sky-200">
                                فتح المورد <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    <?php elseif($m instanceof \App\Models\OfflineActivity): ?>
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fas fa-tasks text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                            <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($m->type); ?></p>
                            <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.show', [$offlineCourse, $m])); ?>" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200">
                                عرض / تسليم <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    <?php elseif($m instanceof \App\Models\AdvancedExam): ?>
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fas fa-clipboard-check text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                            <?php if($m->description): ?>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2"><?php echo e(Str::limit(strip_tags($m->description), 100)); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo e(route('student.exams.show', $m->id)); ?>" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200">
                                صفحة الاختبار <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-400 text-center py-4">لا عناصر في هذا القسم.</p>
            <?php endif; ?>
        </div>
        <?php if($section->children && $section->children->isNotEmpty()): ?>
            <div class="px-3 pb-3 space-y-3 border-t border-slate-100 bg-slate-50/30">
                <?php echo $__env->make('student.offline-courses.partials.curriculum-sections', ['sections' => $section->children, 'offlineCourse' => $offlineCourse, 'channel' => $channel ?? 'offline', 'studentRouteGroup' => $studentRouteGroup ?? 'student.offline-courses'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endif; ?>
    </details>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/partials/curriculum-sections.blade.php ENDPATH**/ ?>