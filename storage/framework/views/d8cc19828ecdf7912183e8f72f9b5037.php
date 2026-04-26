<?php
    $depth = $depth ?? 0;
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $indentRem = min($depth, 8) * 0.5;
?>
<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
        $isRoot = ! $section->parent_id;
    ?>
    <article
        class="curriculum-section-card rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4 last:mb-0"
        style="margin-inline-start: <?php echo e($indentRem); ?>rem"
    >
        <details class="group/section" <?php if($loop->first && $isRoot && $depth === 0): ?> open <?php endif; ?>>
            <summary class="curriculum-section-summary flex cursor-pointer list-none items-center gap-3 px-4 py-3.5 sm:px-5 sm:py-4 bg-gradient-to-l from-white to-slate-50/90 hover:from-sky-50/30 hover:to-slate-50 border-b border-slate-100 transition-colors">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 ring-1 ring-sky-200/60 shadow-sm">
                    <i class="fas <?php echo e($isRoot ? 'fa-folder-open' : 'fa-folder'); ?> text-lg"></i>
                </span>
                <div class="min-w-0 flex-1 text-start">
                    <?php if($isRoot): ?>
                        <span class="mb-0.5 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-600">قسم رئيسي</span>
                    <?php else: ?>
                        <span class="mb-0.5 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-600">قسم فرعي</span>
                    <?php endif; ?>
                    <h3 class="text-base sm:text-lg font-bold leading-snug text-slate-900"><?php echo e($section->title); ?></h3>
                    <?php if($section->description && $isRoot): ?>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600 line-clamp-3 sm:line-clamp-none whitespace-pre-line"><?php echo e($section->description); ?></p>
                    <?php elseif($section->description && ! $isRoot): ?>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600 line-clamp-2"><?php echo e($section->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1.5 sm:flex-row sm:items-center sm:gap-2">
                    <?php if($itemsCount > 0): ?>
                        <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 shadow-sm">
                            <i class="fas fa-layer-group text-sky-500 text-[10px]"></i>
                            <?php echo e($itemsCount); ?>

                        </span>
                    <?php endif; ?>
                    <?php if($childrenCount > 0): ?>
                        <span class="inline-flex items-center gap-1 rounded-lg border border-violet-100 bg-violet-50 px-2.5 py-1 text-[11px] font-bold text-violet-800">
                            <i class="fas fa-sitemap text-[10px]"></i>
                            <?php echo e($childrenCount); ?>

                        </span>
                    <?php endif; ?>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-transform duration-200 group-open/section:rotate-180" aria-hidden="true">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </summary>

            <div class="bg-slate-50/40">
                <?php if($section->items->isNotEmpty()): ?>
                    <ul class="divide-y divide-slate-100 border-b border-slate-100/80" role="list">
                        <?php $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $m = $cItem->item; ?>
                            <?php if(!$m): ?>
                                <?php continue; ?>
                            <?php endif; ?>

                            <?php if($m instanceof \App\Models\OfflineCurriculumNote): ?>
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-slate-300" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-700 ring-1 ring-slate-300/40">
                                        <i class="fas fa-align-right text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-slate-500">ملاحظة</span>
                                        <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                                        <?php if($m->body): ?>
                                            <div class="mt-1.5 text-sm leading-relaxed text-slate-600 whitespace-pre-line"><?php echo e($m->body); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php elseif($m instanceof \App\Models\OfflineLecture): ?>
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-violet-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 ring-1 ring-violet-200/60">
                                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-violet-700">محاضرة</span>
                                                <p class="text-base font-bold text-slate-900"><?php echo e($m->title); ?></p>
                                            </div>
                                            <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>#offline-lecture-<?php echo e($m->id); ?>" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-violet-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700">
                                                عرض
                                                <i class="fas fa-chevron-left text-[10px] opacity-90"></i>
                                            </a>
                                        </div>
                                        <?php if($m->relationLoaded('groupSession') && $m->groupSession): ?>
                                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold text-violet-800">
                                                <i class="far fa-calendar-check text-violet-500"></i>
                                                <span><?php echo e($m->groupSession->session_date->translatedFormat('l j F Y')); ?></span>
                                                <?php $sgt = $m->groupSession->start_time; ?>
                                                <span class="text-slate-500">· <?php echo e(is_string($sgt) ? substr($sgt, 0, 5) : $sgt); ?></span>
                                                <?php if($m->groupSession->group): ?>
                                                    <span class="text-slate-500">· <?php echo e($m->groupSession->group->name); ?></span>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if($m->scheduled_at): ?>
                                            <p class="text-xs text-slate-500"><i class="far fa-clock ml-1 text-slate-400"></i><?php echo e($m->scheduled_at->translatedFormat('l j F Y — H:i')); ?></p>
                                        <?php endif; ?>
                                        <?php if(filled($m->session_agenda)): ?>
                                            <?php
                                                $agLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $m->session_agenda))));
                                                $agShow = array_slice($agLines, 0, 5);
                                                $agMore = max(0, count($agLines) - 5);
                                            ?>
                                            <?php if(count($agShow)): ?>
                                                <div class="rounded-xl border border-slate-100 bg-white/90 px-3 py-2.5">
                                                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">برنامج اليوم</p>
                                                    <ul class="space-y-1.5 text-xs text-slate-700">
                                                        <?php $__currentLoopData = $agShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex gap-2 leading-relaxed">
                                                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                                                <span><?php echo e($line); ?></span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                    <?php if($agMore > 0): ?>
                                                        <p class="mt-2 text-[11px] font-medium text-slate-400">+ <?php echo e($agMore); ?> نقطة في صفحة المحاضرة</p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($m->description): ?>
                                            <p class="text-xs leading-relaxed text-slate-600 line-clamp-3"><?php echo e(Str::limit($m->description, 220)); ?></p>
                                        <?php endif; ?>
                                        <?php echo $__env->make('partials.offline-mindmap-visual', ['text' => $m->offline_attendee_mindmap], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>
                                </li>
                            <?php elseif($m instanceof \App\Models\OfflineCourseResource): ?>
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-sky-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 ring-1 ring-sky-200/60">
                                        <i class="fas fa-file-alt text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-sky-700">مورد</span>
                                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                                            <?php if($m->description): ?>
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2"><?php echo e(Str::limit(strip_tags($m->description), 140)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>#offline-resource-<?php echo e($m->id); ?>" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:bg-sky-50 sm:self-center">
                                            فتح
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php elseif($m instanceof \App\Models\OfflineActivity): ?>
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-amber-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 ring-1 ring-amber-200/60">
                                        <i class="fas fa-tasks text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-amber-800">نشاط</span>
                                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                                            <p class="text-[11px] text-slate-500"><?php echo e($m->type); ?></p>
                                        </div>
                                        <a href="<?php echo e(route($sg . '.activities.show', [$offlineCourse, $m])); ?>" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600 sm:self-center">
                                            عرض / تسليم
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php elseif($m instanceof \App\Models\AdvancedExam): ?>
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200/60">
                                        <i class="fas fa-clipboard-check text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-emerald-800">اختبار</span>
                                            <p class="font-semibold text-slate-900"><?php echo e($m->title); ?></p>
                                            <?php if($m->description): ?>
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2"><?php echo e(Str::limit(strip_tags($m->description), 120)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo e(route('student.exams.show', $m)); ?>" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-50 sm:self-center">
                                            الاختبار
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="border-b border-slate-100 px-4 py-6 text-center text-sm text-slate-400">لا عناصر في هذا القسم.</div>
                <?php endif; ?>

                <?php if($section->children && $section->children->isNotEmpty()): ?>
                    <div class="bg-slate-50/80 px-3 py-4 sm:px-4">
                        <?php echo $__env->make('student.offline-courses.partials.curriculum-sections', [
                            'sections' => $section->children,
                            'offlineCourse' => $offlineCourse,
                            'channel' => $channel ?? 'offline',
                            'studentRouteGroup' => $sg,
                            'depth' => $depth + 1,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>
    </article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/partials/curriculum-sections.blade.php ENDPATH**/ ?>