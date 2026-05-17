<?php
    $depth = $depth ?? 0;
    $sectionItemCount = $section->activeItems->filter(fn($ci) => $ci->item && !($ci->item instanceof \App\Models\CourseLesson))->count();
    $isSectionLocked = $section->is_locked ?? false;
?>
<div class="mb-4 <?php echo e($depth > 0 ? 'pr-2 border-r-2 border-slate-100' : ''); ?> <?php echo e($isSectionLocked ? 'opacity-80' : ''); ?>" style="<?php echo e($depth > 0 ? 'margin-right: ' . ($depth * 0.5) . 'rem;' : ''); ?>">
    <div class="curriculum-section-header mb-2 <?php echo e($isSectionLocked ? 'section-locked' : ''); ?>"
         :class="{ 'collapsed': isSectionCollapsed(<?php echo e($section->id); ?>) }"
         @click="toggleSection(<?php echo e($section->id); ?>)"
         role="button"
         tabindex="0"
         @keydown.enter.prevent="toggleSection(<?php echo e($section->id); ?>)"
         @keydown.space.prevent="toggleSection(<?php echo e($section->id); ?>)">
        <span class="flex items-center gap-1.5">
            <?php if($isSectionLocked): ?>
                <i class="fas fa-lock text-amber-500 text-[10px]" title="أكمل القسم السابق لفتح هذا القسم"></i>
            <?php else: ?>
                <i class="fas fa-folder text-sky-400/90 text-[10px]"></i>
            <?php endif; ?>
            <span><?php echo e($section->title); ?></span>
            <?php if($sectionItemCount > 0): ?>
                <span class="text-gray-500 text-[10px]">(<?php echo e($sectionItemCount); ?>)</span>
            <?php endif; ?>
        </span>
        <i class="fas fa-chevron-down curriculum-section-chevron"></i>
    </div>
    <div x-show="!isSectionCollapsed(<?php echo e($section->id); ?>)" x-transition>
        <?php $__currentLoopData = $section->activeItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculumItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $item = $curriculumItem->item;
                if (!$item) continue;
                if ($item instanceof \App\Models\CourseLesson) continue;

                $isCompleted = false;
                $isCurrent = false;
                $isLocked = $isSectionLocked;

                if ($item instanceof \App\Models\CourseLesson) {
                    $lessonProgress = $item->progress->first();
                    $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                    $previousItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                    $allPreviousCompleted = true;
                    foreach ($previousItems as $prevItem) {
                        if ($prevItem->item instanceof \App\Models\CourseLesson) {
                            $prevProgress = $prevItem->item->progress->first();
                            if (!$prevProgress || !$prevProgress->is_completed) {
                                $allPreviousCompleted = false;
                                break;
                            }
                        } elseif ($prevItem->item instanceof \App\Models\LearningPattern) {
                            $prevBestAttempt = $prevItem->item->getUserBestAttempt(auth()->id());
                            if (!$prevBestAttempt || $prevBestAttempt->status !== 'completed') {
                                $allPreviousCompleted = false;
                                break;
                            }
                        }
                    }
                    $isCurrent = !$isCompleted && ($curriculumItem->order == 1 || $allPreviousCompleted);
                    $isLocked = $isSectionLocked || (!$isCurrent && !$isCompleted);
                } elseif ($item instanceof \App\Models\Lecture) {
                    $watchProgress = $item->watchProgress->first();
                    $minPercent = $item->min_watch_percent_to_unlock_next;
                    $threshold = $minPercent !== null ? (int) $minPercent : 90;
                    $isCompleted = $watchProgress && (int) $watchProgress->progress_percent >= $threshold;
                    $prevLecturesInSection = $section->activeItems->where('order', '<', $curriculumItem->order)->filter(fn($i) => $i->item instanceof \App\Models\Lecture);
                    $lastPrevLecture = $prevLecturesInSection->sortByDesc('order')->first();
                    if ($lastPrevLecture) {
                        $prevLec = $lastPrevLecture->item;
                        $prevWp = $prevLec->watchProgress->first();
                        $prevMin = $prevLec->min_watch_percent_to_unlock_next;
                        $prevThreshold = $prevMin !== null ? (int) $prevMin : 90;
                        $isLocked = $isLocked || !$prevWp || (int) $prevWp->progress_percent < $prevThreshold;
                    }
                    $isCurrent = !$isCompleted && !$isLocked;
                } elseif ($item instanceof \App\Models\LearningPattern) {
                    $bestAttempt = $item->getUserBestAttempt(auth()->id());
                    $isCompleted = $bestAttempt && $bestAttempt->status === 'completed';
                    $isCurrent = !$isCompleted && !$isSectionLocked;
                    $isLocked = $isSectionLocked;
                } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                    // الامتحان يُعتبر مكتمل إذا كانت أفضل محاولة مساوية أو أعلى من درجة النجاح
                    $passing = (float) ($item->passing_marks ?? 0);
                    $bestAttempt = $item->attempts->sortByDesc('submitted_at')->first();
                    $isCompleted = $bestAttempt && $bestAttempt->score !== null && (float) $bestAttempt->score >= $passing;

                    // فتح الامتحان يعتمد على إكمال العناصر السابقة في نفس القسم (فيديوهات / أنماط / واجبات)
                    $prevItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                    foreach ($prevItems as $prevItem) {
                        $prev = $prevItem->item;
                        if (!$prev) continue;

                        if ($prev instanceof \App\Models\Lecture) {
                            $wp = $prev->watchProgress->first();
                            $minPercent = $prev->min_watch_percent_to_unlock_next;
                            $threshold = $minPercent !== null ? (int) $minPercent : 90;
                            if (!$wp || (int) $wp->progress_percent < $threshold) {
                                $isLocked = true;
                                break;
                            }
                        } elseif ($prev instanceof \App\Models\CourseLesson) {
                            $lp = $prev->progress->first();
                            if (!$lp || !$lp->is_completed) {
                                $isLocked = true;
                                break;
                            }
                        } elseif ($prev instanceof \App\Models\LearningPattern) {
                            $prevBest = $prev->getUserBestAttempt(auth()->id());
                            if (!$prevBest || $prevBest->status !== 'completed') {
                                $isLocked = true;
                                break;
                            }
                        } elseif ($prev instanceof \App\Models\Assignment) {
                            if ($prev->submissions->where('student_id', auth()->id())->isEmpty()) {
                                $isLocked = true;
                                break;
                            }
                        }
                    }

                    $isCurrent = !$isCompleted && !$isLocked && !$isSectionLocked;
                }
            ?>

            <div class="curriculum-item <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isCurrent ? 'active' : ''); ?> <?php echo e($isLocked ? 'locked' : ''); ?>"
                 data-section-id="<?php echo e($section->id); ?>"
                 <?php if($item instanceof \App\Models\CourseLesson): ?>
                     data-item-type="lesson"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-locked="<?php echo e($isLocked ? '1' : '0'); ?>"
                    @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; selectedLesson = <?php echo e($item->id); ?>; loadLesson(<?php echo e($item->id); ?>)"
                 <?php elseif($item instanceof \App\Models\Lecture): ?>
                     data-item-type="lecture"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-locked="<?php echo e($isLocked ? '1' : '0'); ?>"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; loadLecture(<?php echo e($item->id); ?>)"
                 <?php elseif($item instanceof \App\Models\Assignment): ?>
                     data-item-type="assignment"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-locked="0"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; loadAssignment(<?php echo e($item->id); ?>)"
                 <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                     data-item-type="exam"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-locked="0"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; loadExam(<?php echo e($item->id); ?>)"
                 <?php elseif($item instanceof \App\Models\LearningPattern): ?>
                     data-item-type="pattern"
                     data-item-id="<?php echo e($item->id); ?>"
                     data-item-locked="<?php echo e($isLocked ? '1' : '0'); ?>"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; loadPattern(<?php echo e($item->id); ?>)"
                 <?php endif; ?>
                 x-show="!searchQuery || '<?php echo e(strtolower($item->title)); ?>'.includes(searchQuery.toLowerCase())">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 mt-0.5">
                        <?php if($item instanceof \App\Models\CourseLesson): ?>
                            <?php if($isCompleted): ?>
                                <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                            <?php elseif($isCurrent): ?>
                                <div class="w-6 h-6 bg-sky-500 rounded-md flex items-center justify-center animate-pulse">
                                    <i class="fas fa-play text-white text-[10px]"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-lock text-white text-[10px]"></i>
                                </div>
                            <?php endif; ?>
                        <?php elseif($item instanceof \App\Models\Lecture): ?>
                            <?php if($isCompleted): ?>
                                <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                            <?php elseif($isLocked): ?>
                                <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-lock text-white text-[10px]"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-6 h-6 bg-sky-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-white text-[10px]"></i>
                                </div>
                            <?php endif; ?>
                        <?php elseif($item instanceof \App\Models\Assignment): ?>
                            <div class="w-6 h-6 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-tasks text-white text-[10px]"></i>
                            </div>
                        <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                            <?php if($isCompleted): ?>
                                <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                            <?php elseif($isCurrent): ?>
                                <div class="w-6 h-6 bg-indigo-500 rounded-md flex items-center justify-center animate-pulse">
                                    <i class="fas fa-clipboard-check text-white text-[10px]"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-lock text-white text-[10px]"></i>
                                </div>
                            <?php endif; ?>
                        <?php elseif($item instanceof \App\Models\LearningPattern): ?>
                            <?php $typeInfo = $item->getTypeInfo(); ?>
                            <?php if($isCompleted): ?>
                                <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                </div>
                            <?php elseif($isCurrent): ?>
                                <div class="w-6 h-6 bg-orange-500 rounded-md flex items-center justify-center animate-pulse">
                                    <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?> text-white text-[10px]"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-lock text-white text-[10px]"></i>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="curriculum-item-title"><?php echo e($item->title); ?></div>
                        <div class="curriculum-item-meta">
                            <?php if($item instanceof \App\Models\CourseLesson): ?>
                                <span><i class="fas fa-video ml-1"></i> درس</span>
                                <?php if($item->duration_minutes): ?>
                                    <span><i class="fas fa-clock ml-1"></i> <?php echo e($item->duration_minutes); ?> دقيقة</span>
                                <?php endif; ?>
                            <?php elseif($item instanceof \App\Models\Lecture): ?>
                                <span><i class="fas fa-chalkboard-teacher ml-1"></i> محاضرة</span>
                                <?php if($item->scheduled_at): ?>
                                    <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->scheduled_at->format('Y/m/d')); ?></span>
                                <?php endif; ?>
                            <?php elseif($item instanceof \App\Models\Assignment): ?>
                                <span><i class="fas fa-tasks ml-1"></i> واجب</span>
                                <?php if($item->due_date): ?>
                                    <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->due_date->format('Y/m/d')); ?></span>
                                <?php endif; ?>
                            <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                                <span><i class="fas fa-clipboard-check ml-1"></i> امتحان</span>
                                <?php if(isset($item->start_date) && $item->start_date): ?>
                                    <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->start_date->format('Y/m/d')); ?></span>
                                <?php endif; ?>
                            <?php elseif($item instanceof \App\Models\LearningPattern): ?>
                                <?php $typeInfo = $item->getTypeInfo(); ?>
                                <span><i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?> ml-1"></i> <?php echo e($typeInfo['name'] ?? 'نمط تعليمي'); ?></span>
                                <?php if($item->points > 0): ?>
                                    <span><i class="fas fa-star ml-1"></i> <?php echo e($item->points); ?> نقطة</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($section->children && $section->children->count() > 0): ?>
            <?php $__currentLoopData = $section->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('student.my-courses.partials.learn-sidebar-section', ['section' => $child, 'depth' => $depth + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/my-courses/partials/learn-sidebar-section.blade.php ENDPATH**/ ?>