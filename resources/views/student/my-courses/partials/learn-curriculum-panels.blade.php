@php
    use Illuminate\Support\Str;
    $depth = $depth ?? 0;
    $course = $course ?? null;
@endphp

@foreach($sections as $section)
    @foreach($section->activeItems->sortBy('order') as $curriculumItem)
        @php
            $item = $curriculumItem->item;
            if (!$item || $item instanceof \App\Models\CourseLesson) continue;

            $isSectionLocked = (!empty($course->admin_unlock_all_videos)) ? false : ($section->is_locked ?? false);
            $isLocked = $isSectionLocked;
            $isCompleted = false;
            $durationMin = null;
            $typeLabel = __('student.curriculum_type_other');
            $typeBadgeClass = 'learn-type-video';
            $typeIcon = 'fa-video';
            $threshold = 90;

            if ($item instanceof \App\Models\Lecture) {
                $typeLabel = __('student.curriculum_type_lecture');
                $typeBadgeClass = 'learn-type-video';
                $typeIcon = 'fa-chalkboard-teacher';
                $durationMin = $item->duration_minutes ?? 60;
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
                }
            } elseif ($item instanceof \App\Models\LearningPattern) {
                $typeLabel = __('student.curriculum_type_pattern');
                $typeBadgeClass = 'learn-type-pattern';
                $typeIcon = 'fa-puzzle-piece';
                $bestAttempt = $item->getUserBestAttempt(auth()->id());
                $isCompleted = $bestAttempt && $bestAttempt->status === 'completed';
                $isLocked = $isSectionLocked;
            } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                $typeLabel = __('student.curriculum_type_exam');
                $typeBadgeClass = 'learn-type-exam';
                $typeIcon = 'fa-clipboard-check';
                $durationMin = $item->duration_minutes ?? null;
                $passing = (float) ($item->passing_marks ?? 0);
                $bestAttempt = $item->attempts->sortByDesc('submitted_at')->first();
                $isCompleted = $bestAttempt && $bestAttempt->score !== null && (float) $bestAttempt->score >= $passing;
            } elseif ($item instanceof \App\Models\Assignment) {
                $typeLabel = __('student.curriculum_type_assignment');
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
        @endphp

        @if($panelType)
            @php
                $wp = ($item instanceof \App\Models\Lecture) ? $item->watchProgress->first() : null;
                $pct = $wp ? (int) $wp->progress_percent : 0;
                $watchSec = $wp ? (int) $wp->watch_time_seconds : 0;
                $lastPos = $watchSec > 0 ? floor($watchSec / 60) . ':' . str_pad($watchSec % 60, 2, '0', STR_PAD_LEFT) : null;
                $materials = ($item instanceof \App\Models\Lecture)
                    ? $item->materials()->where('is_visible_to_student', true)->orderBy('sort_order')->get()
                    : collect();
                $filterState = $isLocked ? 'locked' : ($isCompleted ? 'completed' : ($pct > 0 && $pct < $threshold ? 'progress' : 'unlocked'));
                $noteKey = $panelType . '-' . $item->id;
                $summary = $item instanceof \App\Models\Lecture
                    ? ($item->description ?: __('student.learn_panel_default_summary'))
                    : __('student.learn_panel_item_summary');
            @endphp
            <section id="learn-panel-{{ $panelType }}-{{ $item->id }}"
                     class="learn-curriculum-panel sp-card {{ $isLocked ? 'is-locked-panel' : '' }}"
                     data-panel-type="{{ $panelType }}"
                     data-panel-id="{{ $item->id }}"
                     data-panel-locked="{{ $isLocked ? '1' : '0' }}"
                     data-filter-state="{{ $filterState }}">

                @if($isLocked)
                    <div class="learn-panel-locked-msg"
                         data-panel-lock-overlay="{{ $panelType }}-{{ $item->id }}">
                        <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-amber-soft);width:52px;height:52px;color:var(--sp-accent-text)">
                            <i class="fas fa-lock"></i>
                        </span>
                        <p class="font-extrabold text-base m-0 text-[var(--sp-text)]">{{ __('student.learn_content_locked') }}</p>
                        <p class="text-sm mt-2 mb-0 text-[var(--sp-muted)]">{{ __('student.learn_complete_previous') }}</p>
                    </div>
                @endif

                <div class="learn-panel-body" data-panel-body="{{ $panelType }}-{{ $item->id }}" @if($isLocked) hidden @endif>
                    @if($panelType === 'pattern')
                        <div class="learn-pattern-embed">
                            <iframe src="{{ route('my-courses.learning-patterns.show', [$course, $item->id]) }}?embed=1"
                                    class="absolute inset-0 w-full h-full border-0"
                                    title="{{ $item->title }}" loading="lazy"></iframe>
                        </div>
                    @endif

                    <div class="learn-lesson-info">
                        <p class="learn-panel-eyebrow">{{ $section->title }}</p>
                        <h2 class="learn-panel-title">{{ $item->title }}</h2>
                        @if($item instanceof \App\Models\Lecture && $item->description)
                            <p class="learn-lesson-desc">{{ Str::limit($item->description, 220) }}</p>
                        @elseif($panelType === 'assignment' && !empty($item->description))
                            <p class="learn-lesson-desc">{{ Str::limit(strip_tags($item->description), 220) }}</p>
                        @endif
                        <div class="learn-lesson-meta-grid">
                            @if($durationMin)
                                <span class="sp-pill sp-pill--upcoming !py-1.5 !px-2.5 !text-xs">
                                    <i class="fas fa-clock me-1 opacity-70"></i>{{ $durationMin }} {{ __('student.minutes') }}
                                </span>
                            @endif
                            <span class="sp-pill sp-pill--progress !py-1.5 !px-2.5 !text-xs">{{ $typeLabel }}</span>
                            @if($isCompleted)
                                <span class="sp-pill sp-pill--done !py-1.5 !px-2.5 !text-xs">{{ __('student.completed_badge') }}</span>
                            @elseif(!$isLocked)
                                <span class="sp-pill sp-pill--upcoming !py-1.5 !px-2.5 !text-xs">{{ __('student.filter_in_progress') }}</span>
                            @endif
                            @if($panelType === 'lecture' && $lastPos)
                                <span class="text-xs font-bold text-[var(--sp-muted)] self-center">
                                    {{ __('student.learn_last_position', ['time' => $lastPos]) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Kept for non-dash fallback; hidden in learn-dash via CSS --}}
                    <div class="learn-nav-row">
                        <button type="button" class="learn-nav-btn" @click="goNavPrev()" :disabled="!hasNavPrev()">
                            <i class="fas fa-chevron-right text-[var(--sp-muted)]"></i>
                            <div>
                                <div class="learn-nav-label">{{ __('student.learn_nav_prev') }}</div>
                                <div class="learn-nav-title">{{ __('student.learn_prev_hint') }}</div>
                            </div>
                        </button>
                        <button type="button" class="learn-nav-btn next-primary" @click="goNavNext()" :disabled="!navNextAllowed()">
                            <div class="text-start flex-1">
                                <div class="learn-nav-label">{{ __('student.learn_nav_next') }}</div>
                                <div class="learn-nav-title">{{ $isLocked ? __('student.learn_complete_to_continue') : __('student.continue_learning') }}</div>
                            </div>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>

                    <nav class="learn-tabs-nav" role="tablist">
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'overview' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'overview')">{{ __('student.learn_tab_overview') }}</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'resources' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'resources')">{{ __('student.learn_tab_resources') }}</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'notes' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'notes')">{{ __('student.learn_tab_notes') }}</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'discussion' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'discussion')">{{ __('student.learn_tab_discussion') }}</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'qa' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'qa')">{{ __('student.learn_tab_qa') }}</button>
                    </nav>

                    <div class="learn-tab-panel learn-tab-content" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'overview'" x-cloak>
                        <h4>{{ __('student.learn_lesson_summary') }}</h4>
                        <p>{{ $summary }}</p>
                        @if($item instanceof \App\Models\Lecture && $item->notes)
                            <h4>{{ __('student.learn_instructor_notes') }}</h4>
                            <p class="whitespace-pre-wrap">{{ $item->notes }}</p>
                        @endif
                        <h4>{{ __('student.learn_objectives') }}</h4>
                        <ul class="learn-objectives">
                            <li>{{ __('student.learn_objective_1') }}</li>
                            <li>{{ __('student.learn_objective_2') }}</li>
                            <li>{{ __('student.learn_objective_3', ['pct' => $threshold]) }}</li>
                        </ul>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'resources'" x-cloak>
                        @if($materials->isNotEmpty())
                            <div class="learn-resources-grid">
                                @foreach($materials as $mat)
                                    <a href="{{ route('my-courses.lectures.material.download', [$course, $item->id, $mat->id]) }}"
                                       target="_blank" rel="noopener" class="learn-resource-card">
                                        <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-sky)">
                                            <i class="fas fa-file-download text-sm"></i>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-extrabold text-sm truncate">{{ $mat->title ?: $mat->file_name }}</span>
                                            <span class="block text-xs text-[var(--sp-muted)] truncate">{{ $mat->file_name }}</span>
                                        </span>
                                        <i class="fas fa-download text-[var(--sp-muted)]"></i>
                                    </a>
                                @endforeach
                            </div>
                        @elseif($panelType === 'assignment')
                            <a href="/student/assignments/{{ $item->id }}" class="sp-promo-btn !mt-0 inline-flex">{{ __('student.learn_open_assignment') }}</a>
                        @elseif($panelType === 'exam')
                            <a href="{{ route('student.exams.show', $item->id) }}" class="sp-promo-btn !mt-0 inline-flex">{{ __('student.start_exam') }}</a>
                        @else
                            <p class="learn-discussion-placeholder">{{ __('student.learn_no_resources') }}</p>
                        @endif
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'notes'" x-cloak>
                        <textarea class="learn-notes-editor"
                                  placeholder="{{ __('student.learn_notes_placeholder') }}"
                                  x-model="lessonNotes['{{ $noteKey }}']"
                                  @input.debounce.500ms="saveLessonNote('{{ $noteKey }}', $event.target.value)"></textarea>
                        <p class="text-xs text-[var(--sp-muted)] mt-2 mb-0 font-bold">{{ __('student.learn_notes_autosave') }}</p>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'discussion'" x-cloak>
                        <div class="learn-discussion-placeholder">
                            <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-lilac)"><i class="fas fa-comments"></i></span>
                            <p class="m-0 font-bold">{{ __('student.learn_discussion_soon') }}</p>
                        </div>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'qa'" x-cloak>
                        <div class="learn-discussion-placeholder">
                            <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-mint)"><i class="fas fa-question-circle"></i></span>
                            <p class="m-0 font-bold">{{ __('student.learn_qa_soon') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    @if($section->children && $section->children->isNotEmpty())
        @include('student.my-courses.partials.learn-curriculum-panels', [
            'sections' => $section->children,
            'course' => $course,
            'depth' => $depth + 1,
        ])
    @endif
@endforeach
