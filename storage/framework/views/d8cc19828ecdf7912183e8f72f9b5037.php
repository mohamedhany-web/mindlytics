<?php
    $depth = $depth ?? 0;
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $indentRem = min($depth, 6) * 0.65;
    $dateLocale = app()->getLocale();
?>
<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
        $isRoot = ! $section->parent_id;
    ?>
    <article class="oc-sec" <?php if($depth > 0): ?> style="margin-inline-start: <?php echo e($indentRem); ?>rem" <?php endif; ?>>
        <details <?php if($loop->first && $isRoot && $depth === 0): ?> open <?php endif; ?>>
            <summary class="oc-sec-sum">
                <span class="oc-sec-ico" aria-hidden="true">
                    <i class="fas <?php echo e($isRoot ? 'fa-folder-open' : 'fa-folder'); ?>"></i>
                </span>
                <div class="oc-sec-body">
                    <span class="tag"><?php echo e($isRoot ? __('student.oc_section_main') : __('student.oc_section_sub')); ?></span>
                    <h3><?php echo e($section->title); ?></h3>
                    <?php if($section->description): ?>
                        <p class="<?php echo e($isRoot ? '' : 'line-clamp-2'); ?>"><?php echo e($section->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="oc-sec-meta">
                    <?php if($itemsCount > 0): ?>
                        <span class="pill"><i class="fas fa-layer-group" style="font-size:9px;opacity:.7"></i> <?php echo e($itemsCount); ?></span>
                    <?php endif; ?>
                    <?php if($childrenCount > 0): ?>
                        <span class="pill"><i class="fas fa-sitemap" style="font-size:9px;opacity:.7"></i> <?php echo e($childrenCount); ?></span>
                    <?php endif; ?>
                    <span class="chev" aria-hidden="true"><i class="fas fa-chevron-down text-[10px]"></i></span>
                </div>
            </summary>

            <?php if($section->items->isNotEmpty()): ?>
                <ul class="oc-items" role="list">
                    <?php $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $m = $cItem->item; ?>
                        <?php if(! $m): ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <?php if($m instanceof \App\Models\OfflineCurriculumNote): ?>
                            <li class="oc-item" style="cursor:default">
                                <span class="dot note"><i class="fas fa-align-right"></i></span>
                                <div class="txt">
                                    <span class="kind"><?php echo e(__('student.oc_kind_note')); ?></span>
                                    <strong><?php echo e($m->title); ?></strong>
                                    <?php if($m->body): ?>
                                        <p><?php echo e($m->body); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php elseif($m instanceof \App\Models\OfflineLecture): ?>
                            <li>
                                <a class="oc-item" href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>#offline-lecture-<?php echo e($m->id); ?>">
                                    <span class="dot lecture"><i class="fas fa-chalkboard-teacher"></i></span>
                                    <div class="txt">
                                        <span class="kind"><?php echo e(__('student.oc_kind_lecture')); ?></span>
                                        <strong><?php echo e($m->title); ?></strong>
                                        <?php if($m->relationLoaded('groupSession') && $m->groupSession): ?>
                                            <p>
                                                <?php echo e($m->groupSession->session_date->locale($dateLocale)->translatedFormat('l j F Y')); ?>

                                                <?php $sgt = $m->groupSession->start_time; ?>
                                                · <?php echo e(is_string($sgt) ? substr($sgt, 0, 5) : $sgt); ?>

                                            </p>
                                        <?php elseif($m->scheduled_at): ?>
                                            <p><?php echo e($m->scheduled_at->locale($dateLocale)->translatedFormat('l j F Y — H:i')); ?></p>
                                        <?php endif; ?>
                                        <?php if($m->description): ?>
                                            <p><?php echo e(\Illuminate\Support\Str::limit($m->description, 160)); ?></p>
                                        <?php endif; ?>
                                        <?php echo $__env->make('partials.offline-mindmap-visual', ['text' => $m->offline_attendee_mindmap], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>
                                    <span class="go"><?php echo e(__('student.oc_view')); ?></span>
                                </a>
                            </li>
                        <?php elseif($m instanceof \App\Models\OfflineCourseResource): ?>
                            <li>
                                <a class="oc-item" href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>#offline-resource-<?php echo e($m->id); ?>">
                                    <span class="dot resource"><i class="fas fa-file-alt"></i></span>
                                    <div class="txt">
                                        <span class="kind"><?php echo e(__('student.oc_kind_resource')); ?></span>
                                        <strong><?php echo e($m->title); ?></strong>
                                        <?php if($m->description): ?>
                                            <p><?php echo e(\Illuminate\Support\Str::limit(strip_tags($m->description), 120)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="go"><?php echo e(__('student.oc_open')); ?></span>
                                </a>
                            </li>
                        <?php elseif($m instanceof \App\Models\OfflineActivity): ?>
                            <li>
                                <a class="oc-item" href="<?php echo e(route($sg . '.activities.show', [$offlineCourse, $m])); ?>">
                                    <span class="dot activity"><i class="fas fa-tasks"></i></span>
                                    <div class="txt">
                                        <span class="kind"><?php echo e(__('student.oc_kind_activity')); ?></span>
                                        <strong><?php echo e($m->title); ?></strong>
                                        <p><?php echo e($m->type); ?></p>
                                    </div>
                                    <span class="go"><?php echo e(__('student.oc_submit')); ?></span>
                                </a>
                            </li>
                        <?php elseif($m instanceof \App\Models\AdvancedExam): ?>
                            <li>
                                <a class="oc-item" href="<?php echo e(route('student.exams.show', $m)); ?>">
                                    <span class="dot exam"><i class="fas fa-clipboard-check"></i></span>
                                    <div class="txt">
                                        <span class="kind"><?php echo e(__('student.oc_kind_exam')); ?></span>
                                        <strong><?php echo e($m->title); ?></strong>
                                        <?php if($m->description): ?>
                                            <p><?php echo e(\Illuminate\Support\Str::limit(strip_tags($m->description), 120)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="go"><?php echo e(__('student.oc_open')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                <div style="padding:16px;text-align:center;font-size:13px;color:var(--ml-muted);border-top:1px solid var(--ml-line)">
                    <?php echo e(__('student.oc_section_empty')); ?>

                </div>
            <?php endif; ?>

            <?php if($section->children && $section->children->isNotEmpty()): ?>
                <div class="oc-children">
                    <?php echo $__env->make('student.offline-courses.partials.curriculum-sections', [
                        'sections' => $section->children,
                        'offlineCourse' => $offlineCourse,
                        'channel' => $channel ?? 'offline',
                        'studentRouteGroup' => $sg,
                        'depth' => $depth + 1,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?>
        </details>
    </article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/partials/curriculum-sections.blade.php ENDPATH**/ ?>