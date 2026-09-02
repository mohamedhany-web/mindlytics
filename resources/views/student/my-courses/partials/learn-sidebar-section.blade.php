@php
    $depth = $depth ?? 0;
    $course = $course ?? null;
    $progressService = $progressService ?? app(\App\Services\CourseProgressService::class);
    $sectionItemCount = $section->activeItems->filter(fn($ci) => (bool) $ci->item)->count();
    $isSectionLocked = (!empty($course->admin_unlock_all_videos)) ? false : ($section->is_locked ?? false);
@endphp
<div class="learn-section-block {{ $depth > 0 ? 'learn-section-block--nested' : '' }} {{ $isSectionLocked ? 'opacity-80' : '' }}"
     style="{{ $depth > 0 ? 'margin-inline-start: ' . ($depth * 0.65) . 'rem;' : '' }}">
    <div class="curriculum-section-header {{ $isSectionLocked ? 'section-locked' : '' }}"
         :class="{ 'collapsed': isSectionCollapsed({{ $section->id }}) }"
         @click="toggleSection({{ $section->id }})"
         role="button"
         tabindex="0"
         @keydown.enter.prevent="toggleSection({{ $section->id }})"
         @keydown.space.prevent="toggleSection({{ $section->id }})">
        <span class="flex items-center gap-2 min-w-0">
            @if($isSectionLocked)
                <i class="fas fa-lock text-amber-500 text-xs shrink-0" title="{{ __('student.learn_section_locked') }}"></i>
            @endif
            <span class="truncate font-extrabold">{{ $section->title }}</span>
            @if($sectionItemCount > 0)
                <span class="text-[var(--learn-text-muted)] text-[11px] font-bold shrink-0">({{ $sectionItemCount }})</span>
            @endif
        </span>
        <i class="fas fa-chevron-down curriculum-section-chevron"></i>
    </div>
    <div class="curriculum-section-body"
         :class="{ 'is-collapsed': isSectionCollapsed({{ $section->id }}) }">
        @foreach($section->activeItems as $curriculumItem)
            @php
                $item = $curriculumItem->item;
                if (!$item) continue;

                $isCompleted = false;
                $isCurrent = false;
                $isLocked = $isSectionLocked;
                $typeLabel = __('student.curriculum_type_other');

                if ($item instanceof \App\Models\CourseLesson) {
                    $typeLabel = __('student.curriculum_type_lesson');
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
                    $typeLabel = __('student.curriculum_type_lecture');
                    $watchProgress = $item->watchProgress->first();
                    $minPercent = $item->min_watch_percent_to_unlock_next;
                    $threshold = $minPercent !== null ? (int) $minPercent : 90;
                    $progressService = $progressService ?? app(\App\Services\CourseProgressService::class);
                    $isCompleted = $progressService->lectureWatchUnlocksNext($item, $watchProgress);
                    if (empty($course->admin_unlock_all_videos)) {
                        $prevLecturesInSection = $section->activeItems->where('order', '<', $curriculumItem->order)->filter(fn($i) => $i->item instanceof \App\Models\Lecture);
                        $lastPrevLecture = $prevLecturesInSection->sortByDesc('order')->first();
                        if ($lastPrevLecture) {
                            $prevLec = $lastPrevLecture->item;
                            $prevWp = $prevLec->watchProgress->first();
                            $isLocked = $isLocked || ! $progressService->lectureWatchUnlocksNext($prevLec, $prevWp);
                        }
                    } else {
                        $isLocked = false;
                    }
                    $isCurrent = !$isCompleted && !$isLocked;
                } elseif ($item instanceof \App\Models\LearningPattern) {
                    $typeLabel = __('student.curriculum_type_pattern');
                    $bestAttempt = $item->getUserBestAttempt(auth()->id());
                    $isCompleted = $bestAttempt && $bestAttempt->status === 'completed';
                    $isCurrent = !$isCompleted && !$isSectionLocked;
                    $isLocked = $isSectionLocked;
                } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                    $typeLabel = __('student.curriculum_type_exam');
                    $passing = (float) ($item->passing_marks ?? 0);
                    $bestAttempt = $item->attempts->sortByDesc('submitted_at')->first();
                    $isCompleted = $bestAttempt && $bestAttempt->score !== null && (float) $bestAttempt->score >= $passing;

                    $prevItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                    foreach ($prevItems as $prevItem) {
                        $prev = $prevItem->item;
                        if (!$prev) continue;

                        if ($prev instanceof \App\Models\Lecture) {
                            $wp = $prev->watchProgress->first();
                            if (! $progressService->lectureWatchUnlocksNext($prev, $wp)) {
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
                } elseif ($item instanceof \App\Models\Assignment) {
                    $typeLabel = __('student.curriculum_type_assignment');
                }

                $filterState = $isLocked ? 'locked' : ($isCompleted ? 'completed' : 'unlocked');
                if ($item instanceof \App\Models\Lecture && !$isLocked && !$isCompleted) {
                    $wpF = $item->watchProgress->first();
                    if ($wpF && (int) $wpF->progress_percent > 0 && (int) $wpF->progress_percent < $threshold) {
                        $filterState = 'progress';
                    }
                }

                $statusClass = $isCompleted ? 'curriculum-status--done' : ($isLocked ? 'curriculum-status--locked' : ($isCurrent ? 'curriculum-status--current' : 'curriculum-status--ready'));
                $typeIcon = 'icon-courses.svg';
                if ($item instanceof \App\Models\CourseLesson) {
                    $typeIcon = 'icon-courses.svg';
                } elseif ($item instanceof \App\Models\Lecture) {
                    $typeIcon = 'icon-classes.svg';
                } elseif ($item instanceof \App\Models\LearningPattern) {
                    $typeIcon = 'icon-path.svg';
                } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                    $typeIcon = 'icon-exams.svg';
                } elseif ($item instanceof \App\Models\Assignment) {
                    $typeIcon = 'icon-messages.svg';
                }
            @endphp

            <div class="curriculum-item {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'active' : '' }} {{ $isLocked ? 'locked' : '' }}"
                 data-section-id="{{ $section->id }}"
                 data-filter-state="{{ $filterState }}"
                 @if($item instanceof \App\Models\CourseLesson)
                     data-item-type="lesson"
                     data-item-id="{{ $item->id }}"
                     data-item-locked="{{ $isLocked ? '1' : '0' }}"
                     data-item-completed="{{ $isCompleted ? '1' : '0' }}"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; selectedLesson = {{ $item->id }}; loadLesson({{ $item->id }}); syncActivePanel('lesson', {{ $item->id }}); mobileCurriculumOpen = false"
                 @elseif($item instanceof \App\Models\Lecture)
                     data-item-type="lecture"
                     data-item-id="{{ $item->id }}"
                     data-item-locked="{{ $isLocked ? '1' : '0' }}"
                     data-item-completed="{{ $isCompleted ? '1' : '0' }}"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; loadLecture({{ $item->id }}); mobileCurriculumOpen = false"
                 @elseif($item instanceof \App\Models\Assignment)
                     data-item-type="assignment"
                     data-item-id="{{ $item->id }}"
                     data-item-locked="0"
                     data-item-completed="0"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; loadAssignment({{ $item->id }}); syncActivePanel('assignment', {{ $item->id }}); mobileCurriculumOpen = false"
                 @elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam)
                     data-item-type="exam"
                     data-item-id="{{ $item->id }}"
                     data-item-locked="{{ $isLocked ? '1' : '0' }}"
                     data-item-completed="{{ $isCompleted ? '1' : '0' }}"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; loadExam({{ $item->id }}); syncActivePanel('exam', {{ $item->id }}); mobileCurriculumOpen = false"
                 @elseif($item instanceof \App\Models\LearningPattern)
                     data-item-type="pattern"
                     data-item-id="{{ $item->id }}"
                     data-item-locked="{{ $isLocked ? '1' : '0' }}"
                     data-item-completed="{{ $isCompleted ? '1' : '0' }}"
                     @click="currentSectionDescription = (window.learnSectionDescriptions || {})[$event.currentTarget.dataset.sectionId] || ''; if (($event.currentTarget.dataset.itemLocked || '0') === '1') return; loadPattern({{ $item->id }}); syncActivePanel('pattern', {{ $item->id }}); mobileCurriculumOpen = false"
                 @endif>
                <div class="curriculum-item-inner">
                    <span class="curriculum-status {{ $statusClass }}" aria-hidden="true">
                        @if($isCompleted)
                            <i class="fas fa-check"></i>
                        @elseif($isLocked)
                            <i class="fas fa-lock"></i>
                        @elseif($isCurrent)
                            <i class="fas fa-play"></i>
                        @else
                            <x-student.figma-icon :name="$typeIcon" box="size-3.5" />
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="curriculum-item-title">{{ $item->title }}</div>
                        <div class="curriculum-item-meta">
                            <span class="curriculum-type-row">
                                <x-student.figma-icon :name="$typeIcon" box="size-3" class="curriculum-type-ico" />
                                <span>{{ $typeLabel }}</span>
                            </span>
                            @if($isCompleted)
                                <span class="curriculum-meta-pill curriculum-meta-pill--done">{{ __('student.completed_badge') }}</span>
                            @elseif($isLocked)
                                <span class="curriculum-meta-pill">{{ __('student.learn_locked') }}</span>
                            @elseif($isCurrent)
                                <span class="curriculum-meta-pill curriculum-meta-pill--now">{{ __('student.learn_playing_now') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($section->children && $section->children->count() > 0)
            @foreach($section->children as $child)
                @include('student.my-courses.partials.learn-sidebar-section', ['section' => $child, 'depth' => $depth + 1, 'course' => $course])
            @endforeach
        @endif
    </div>
</div>
