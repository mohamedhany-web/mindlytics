<?php
    $depth = $depth ?? 0;
    $course = $course ?? null;
?>

<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $section->activeItems->sortBy('order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculumItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $item = $curriculumItem->item;
            if (!$item || $item instanceof \App\Models\CourseLesson) continue;

            $isSectionLocked = $section->is_locked ?? false;
            $isLocked = $isSectionLocked;
            $isCompleted = false;
            $isCurrent = false;
            $durationMin = null;
            $typeLabel = '';
            $typeBadgeClass = 'learn-type-video';
            $typeIcon = 'fa-video';

            if ($item instanceof \App\Models\Lecture) {
                $typeLabel = 'محاضرة فيديو';
                $typeBadgeClass = 'learn-type-video';
                $typeIcon = 'fa-chalkboard-teacher';
                $durationMin = $item->duration_minutes ?? 60;
                $watchProgress = $item->watchProgress->first();
                $minPercent = $item->min_watch_percent_to_unlock_next;
                $threshold = $minPercent !== null ? (int) $minPercent : 90;
                $isCompleted = $watchProgress && (int) $watchProgress->progress_percent >= $threshold;
                $isCurrent = !$isCompleted && !$isLocked;
                $prevLecturesInSection = $section->activeItems->where('order', '<', $curriculumItem->order)->filter(fn($i) => $i->item instanceof \App\Models\Lecture);
                $lastPrevLecture = $prevLecturesInSection->sortByDesc('order')->first();
                if ($lastPrevLecture) {
                    $prevLec = $lastPrevLecture->item;
                    $prevWp = $prevLec->watchProgress->first();
                    $prevMin = $prevLec->min_watch_percent_to_unlock_next;
                    $prevThreshold = $prevMin !== null ? (int) $prevMin : 90;
                    $isLocked = $isLocked || !$prevWp || (int) $prevWp->progress_percent < $prevThreshold;
                }
            } elseif ($item instanceof \App\Models\LearningPattern) {
                $typeLabel = 'تمرين تفاعلي';
                $typeBadgeClass = 'learn-type-pattern';
                $typeIcon = 'fa-puzzle-piece';
                $bestAttempt = $item->getUserBestAttempt(auth()->id());
                $isCompleted = $bestAttempt && $bestAttempt->status === 'completed';
                $isLocked = $isSectionLocked;
                $isCurrent = !$isCompleted && !$isLocked;
            } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                $typeLabel = 'امتحان';
                $typeBadgeClass = 'learn-type-exam';
                $typeIcon = 'fa-clipboard-check';
                $durationMin = $item->duration_minutes ?? null;
                $passing = (float) ($item->passing_marks ?? 0);
                $bestAttempt = $item->attempts->sortByDesc('submitted_at')->first();
                $isCompleted = $bestAttempt && $bestAttempt->score !== null && (float) $bestAttempt->score >= $passing;
            } elseif ($item instanceof \App\Models\Assignment) {
                $typeLabel = 'واجب';
                $typeBadgeClass = 'learn-type-assignment';
                $typeIcon = 'fa-tasks';
                $isCompleted = $item->submissions->where('student_id', auth()->id())->isNotEmpty();
            }

            $panelType = match (true) {
                $item instanceof \App\Models\Lecture => 'lecture',
                $item instanceof \App\Models\Assignment => 'assignment',
                $item instanceof \App\Models\AdvancedExam, $item instanceof \App\Models\Exam => 'exam',
                $item instanceof \App\Models\LearningPattern => 'pattern',
                default => null,
            };
        ?>

        <?php if($panelType): ?>
            <?php
                $wp = ($item instanceof \App\Models\Lecture) ? $item->watchProgress->first() : null;
                $pct = $wp ? (int) $wp->progress_percent : 0;
                $watchSec = $wp ? (int) $wp->watch_time_seconds : 0;
                $lastPos = $watchSec > 0 ? floor($watchSec / 60) . ':' . str_pad($watchSec % 60, 2, '0', STR_PAD_LEFT) : '—';
                $hasVideo = ($item instanceof \App\Models\Lecture) && $item->recording_url && trim($item->recording_url) !== '';
                $materials = ($item instanceof \App\Models\Lecture)
                    ? $item->materials()->where('is_visible_to_student', true)->orderBy('sort_order')->get()
                    : collect();
                $filterState = $isLocked ? 'locked' : ($isCompleted ? 'completed' : ($pct > 0 && $pct < 90 ? 'progress' : 'unlocked'));
                $noteKey = $panelType . '-' . $item->id;
            ?>
            <section id="learn-panel-<?php echo e($panelType); ?>-<?php echo e($item->id); ?>"
                     class="learn-curriculum-panel scroll-mt-28 <?php echo e($isLocked ? 'opacity-90' : ''); ?>"
                     data-panel-type="<?php echo e($panelType); ?>"
                     data-panel-id="<?php echo e($item->id); ?>"
                     data-panel-locked="<?php echo e($isLocked ? '1' : '0'); ?>"
                     data-filter-state="<?php echo e($filterState); ?>">
                <?php if($isLocked): ?>
                    <div class="learn-panel-locked-msg m-5 rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-6 py-10 text-center text-[#92400E]"
                         data-panel-lock-overlay="<?php echo e($panelType); ?>-<?php echo e($item->id); ?>">
                        <i class="fas fa-lock text-4xl mb-3 opacity-60"></i>
                        <p class="font-bold text-lg">محتوى مقفل</p>
                        <p class="text-sm mt-1">أكمل الدرس السابق للمتابعة</p>
                    </div>
                <?php endif; ?>

                <div class="learn-panel-body" data-panel-body="<?php echo e($panelType); ?>-<?php echo e($item->id); ?>" <?php if($isLocked): ?> hidden <?php endif; ?>>
                    <?php if($panelType === 'pattern'): ?>
                        <div class="learn-video-hero" style="background:#fff;margin-top:1.25rem">
                            <div class="learn-video-aspect" style="min-height:360px">
                                <iframe src="<?php echo e(route('my-courses.learning-patterns.show', [$course, $item->id])); ?>?embed=1"
                                        class="absolute inset-0 w-full h-full border-0"
                                        title="<?php echo e($item->title); ?>" loading="lazy"></iframe>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="learn-lesson-info">
                        <p class="text-xs font-bold uppercase tracking-wider text-[#2563EB] mb-1"><?php echo e($section->title); ?></p>
                        <h2 class="learn-panel-title"><?php echo e($item->title); ?></h2>
                        <?php if($item instanceof \App\Models\Lecture && $item->description): ?>
                            <p class="learn-lesson-desc"><?php echo e($item->description); ?></p>
                        <?php elseif($panelType === 'assignment'): ?>
                            <p class="learn-lesson-desc"><?php echo e(Str::limit(strip_tags($item->description ?? ''), 280)); ?></p>
                        <?php endif; ?>
                        <div class="learn-lesson-meta-grid">
                            <?php if($durationMin): ?>
                                <span class="learn-lesson-meta-item"><i class="fas fa-clock"></i> <?php echo e($durationMin); ?> دقيقة</span>
                            <?php endif; ?>
                            <span class="learn-lesson-meta-item">
                                <span class="learn-type-badge <?php echo e($typeBadgeClass); ?>"><i class="fas <?php echo e($typeIcon); ?>"></i> <?php echo e($typeLabel); ?></span>
                            </span>
                            <?php if($isCompleted): ?>
                                <span class="learn-status-pill learn-status-done"><i class="fas fa-check-circle"></i> مكتمل</span>
                            <?php elseif($isLocked): ?>
                                <span class="learn-status-pill learn-status-locked"><i class="fas fa-lock"></i> مقفل</span>
                            <?php else: ?>
                                <span class="learn-status-pill learn-status-progress"><i class="fas fa-spinner"></i> قيد التقدم</span>
                            <?php endif; ?>
                            <?php if($panelType === 'lecture' && $watchSec > 0): ?>
                                <span class="learn-lesson-meta-item"><i class="fas fa-history"></i> آخر موضع: <?php echo e($lastPos); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="learn-nav-row">
                        <button type="button" class="learn-nav-btn" @click="goNavPrev()" :disabled="!hasNavPrev()">
                            <i class="fas fa-chevron-right text-[#64748B]"></i>
                            <div>
                                <div class="learn-nav-label">الدرس السابق</div>
                                <div class="learn-nav-title">رجوع للمحتوى السابق</div>
                            </div>
                        </button>
                        <button type="button" class="learn-nav-btn next-primary" @click="goNavNext()" :disabled="!navNextAllowed()">
                            <div class="text-start flex-1">
                                <div class="learn-nav-label">الدرس التالي</div>
                                <div class="learn-nav-title"><?php echo e($isLocked ? 'أكمل للمتابعة' : 'متابعة التعلم'); ?></div>
                            </div>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>

                    
                    <nav class="learn-tabs-nav" role="tablist">
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'overview' }" @click="setPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>, 'overview')">نظرة عامة</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'resources' }" @click="setPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>, 'resources')">الموارد</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'notes' }" @click="setPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>, 'notes')">ملاحظاتي</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'discussion' }" @click="setPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>, 'discussion')">نقاش</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'qa' }" @click="setPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>, 'qa')">أسئلة وأجوبة</button>
                    </nav>

                    <div class="learn-tab-panel learn-tab-content" x-show="getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'overview'">
                        <h4>ملخص الدرس</h4>
                        <p><?php echo e($item instanceof \App\Models\Lecture ? ($item->description ?: 'استكشف محتوى هذا الدرس وطبّق ما تتعلمه خطوة بخطوة.') : 'راجع تعليمات هذا العنصر وأكمله للانتقال للتالي.'); ?></p>
                        <?php if($item instanceof \App\Models\Lecture && $item->notes): ?>
                            <h4>ملاحظات المدرب</h4>
                            <p class="whitespace-pre-wrap"><?php echo e($item->notes); ?></p>
                        <?php endif; ?>
                        <h4>أهداف التعلم</h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li>فهم المفاهيم الأساسية للدرس</li>
                            <li>تطبيق المعرفة عملياً</li>
                            <li>إكمال <?php echo e(isset($threshold) ? $threshold : 90); ?>% من المحتوى للمتابعة</li>
                        </ul>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'resources'">
                        <?php if($materials->isNotEmpty()): ?>
                            <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('my-courses.lectures.material.download', [$course, $item->id, $mat->id])); ?>"
                                   target="_blank" rel="noopener" class="learn-resource-card">
                                    <span class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]"><i class="fas fa-file-download"></i></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block font-semibold text-[#0F172A] truncate"><?php echo e($mat->title ?: $mat->file_name); ?></span>
                                        <span class="block text-xs text-[#64748B] truncate"><?php echo e($mat->file_name); ?></span>
                                    </span>
                                    <i class="fas fa-external-link-alt text-[#94A3B8]"></i>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif($panelType === 'assignment'): ?>
                            <a href="/student/assignments/<?php echo e($item->id); ?>" class="learn-btn-primary inline-flex mt-2"><i class="fas fa-tasks ml-1"></i> فتح الواجب</a>
                        <?php elseif($panelType === 'exam'): ?>
                            <a href="<?php echo e(route('student.exams.show', $item->id)); ?>" class="learn-btn-primary inline-flex mt-2"><i class="fas fa-play ml-1"></i> بدء الامتحان</a>
                        <?php else: ?>
                            <p class="learn-discussion-placeholder">لا توجد موارد إضافية لهذا الدرس.</p>
                        <?php endif; ?>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'notes'">
                        <textarea class="learn-notes-editor"
                                  placeholder="اكتب ملاحظاتك الشخصية هنا... (يتم الحفظ تلقائياً)"
                                  x-model="lessonNotes['<?php echo e($noteKey); ?>']"
                                  @input.debounce.500ms="saveLessonNote('<?php echo e($noteKey); ?>', $event.target.value)"></textarea>
                        <p class="text-xs text-[#64748B] mt-2"><i class="fas fa-save ml-1"></i> الحفظ التلقائي مفعّل</p>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'discussion'">
                        <div class="learn-discussion-placeholder">
                            <i class="fas fa-comments text-3xl text-[#CBD5E1] mb-3 block"></i>
                            <p>انضم للنقاش مع زملائك — قريباً</p>
                        </div>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('<?php echo e($panelType); ?>', <?php echo e($item->id); ?>) === 'qa'">
                        <div class="learn-discussion-placeholder">
                            <i class="fas fa-question-circle text-3xl text-[#CBD5E1] mb-3 block"></i>
                            <p>اطرح سؤالاً على المدرب — قريباً</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($section->children && $section->children->isNotEmpty()): ?>
        <?php echo $__env->make('student.my-courses.partials.learn-curriculum-panels', [
            'sections' => $section->children,
            'course' => $course,
            'depth' => $depth + 1,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/my-courses/partials/learn-curriculum-panels.blade.php ENDPATH**/ ?>