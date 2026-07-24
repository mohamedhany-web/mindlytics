@php
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
                if (empty($course->admin_unlock_all_videos)) {
                    $prevLecturesInSection = $section->activeItems->where('order', '<', $curriculumItem->order)->filter(fn($i) => $i->item instanceof \App\Models\Lecture);
                    $lastPrevLecture = $prevLecturesInSection->sortByDesc('order')->first();
                    if ($lastPrevLecture) {
                        $prevLec = $lastPrevLecture->item;
                        $prevWp = $prevLec->watchProgress->first();
                        $prevMin = $prevLec->min_watch_percent_to_unlock_next;
                        $prevThreshold = $prevMin !== null ? (int) $prevMin : 90;
                        $isLocked = $isLocked || !$prevWp || (int) $prevWp->progress_percent < $prevThreshold;
                    }
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
        @endphp

        @if($panelType)
            @php
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
            @endphp
            <section id="learn-panel-{{ $panelType }}-{{ $item->id }}"
                     class="learn-curriculum-panel scroll-mt-28 {{ $isLocked ? 'opacity-90' : '' }}"
                     data-panel-type="{{ $panelType }}"
                     data-panel-id="{{ $item->id }}"
                     data-panel-locked="{{ $isLocked ? '1' : '0' }}"
                     data-filter-state="{{ $filterState }}">
                @if($isLocked)
                    <div class="learn-panel-locked-msg m-5 rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-6 py-10 text-center text-[#92400E]"
                         data-panel-lock-overlay="{{ $panelType }}-{{ $item->id }}">
                        <i class="fas fa-lock text-4xl mb-3 opacity-60"></i>
                        <p class="font-bold text-lg">محتوى مقفل</p>
                        <p class="text-sm mt-1">أكمل الدرس السابق للمتابعة</p>
                    </div>
                @endif

                <div class="learn-panel-body" data-panel-body="{{ $panelType }}-{{ $item->id }}" @if($isLocked) hidden @endif>
                    @if($panelType === 'pattern')
                        <div class="learn-video-hero" style="background:#fff;margin-top:1.25rem">
                            <div class="learn-video-aspect" style="min-height:360px">
                                <iframe src="{{ route('my-courses.learning-patterns.show', [$course, $item->id]) }}?embed=1"
                                        class="absolute inset-0 w-full h-full border-0"
                                        title="{{ $item->title }}" loading="lazy"></iframe>
                            </div>
                        </div>
                    @endif

                    {{-- Lesson information --}}
                    <div class="learn-lesson-info">
                        <p class="text-xs font-bold uppercase tracking-wider text-[#2563EB] mb-1">{{ $section->title }}</p>
                        <h2 class="learn-panel-title">{{ $item->title }}</h2>
                        @if($item instanceof \App\Models\Lecture && $item->description)
                            <p class="learn-lesson-desc">{{ $item->description }}</p>
                        @elseif($panelType === 'assignment')
                            <p class="learn-lesson-desc">{{ Str::limit(strip_tags($item->description ?? ''), 280) }}</p>
                        @endif
                        <div class="learn-lesson-meta-grid">
                            @if($durationMin)
                                <span class="learn-lesson-meta-item"><i class="fas fa-clock"></i> {{ $durationMin }} دقيقة</span>
                            @endif
                            <span class="learn-lesson-meta-item">
                                <span class="learn-type-badge {{ $typeBadgeClass }}"><i class="fas {{ $typeIcon }}"></i> {{ $typeLabel }}</span>
                            </span>
                            @if($isCompleted)
                                <span class="learn-status-pill learn-status-done"><i class="fas fa-check-circle"></i> مكتمل</span>
                            @elseif($isLocked)
                                <span class="learn-status-pill learn-status-locked"><i class="fas fa-lock"></i> مقفل</span>
                            @else
                                <span class="learn-status-pill learn-status-progress"><i class="fas fa-spinner"></i> قيد التقدم</span>
                            @endif
                            @if($panelType === 'lecture' && $watchSec > 0)
                                <span class="learn-lesson-meta-item"><i class="fas fa-history"></i> آخر موضع: {{ $lastPos }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Prev / Next --}}
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
                                <div class="learn-nav-title">{{ $isLocked ? 'أكمل للمتابعة' : 'متابعة التعلم' }}</div>
                            </div>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <nav class="learn-tabs-nav" role="tablist">
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'overview' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'overview')">نظرة عامة</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'resources' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'resources')">الموارد</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'notes' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'notes')">ملاحظاتي</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'discussion' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'discussion')">نقاش</button>
                        <button type="button" role="tab" class="learn-tab-btn" :class="{ 'active': getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'qa' }" @click="setPanelTab('{{ $panelType }}', {{ $item->id }}, 'qa')">أسئلة وأجوبة</button>
                    </nav>

                    <div class="learn-tab-panel learn-tab-content" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'overview'">
                        <h4>ملخص الدرس</h4>
                        <p>{{ $item instanceof \App\Models\Lecture ? ($item->description ?: 'استكشف محتوى هذا الدرس وطبّق ما تتعلمه خطوة بخطوة.') : 'راجع تعليمات هذا العنصر وأكمله للانتقال للتالي.' }}</p>
                        @if($item instanceof \App\Models\Lecture && $item->notes)
                            <h4>ملاحظات المدرب</h4>
                            <p class="whitespace-pre-wrap">{{ $item->notes }}</p>
                        @endif
                        <h4>أهداف التعلم</h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li>فهم المفاهيم الأساسية للدرس</li>
                            <li>تطبيق المعرفة عملياً</li>
                            <li>إكمال {{ isset($threshold) ? $threshold : 90 }}% من المحتوى للمتابعة</li>
                        </ul>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'resources'">
                        @if($materials->isNotEmpty())
                            @foreach($materials as $mat)
                                <a href="{{ route('my-courses.lectures.material.download', [$course, $item->id, $mat->id]) }}"
                                   target="_blank" rel="noopener" class="learn-resource-card">
                                    <span class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]"><i class="fas fa-file-download"></i></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block font-semibold text-[#0F172A] truncate">{{ $mat->title ?: $mat->file_name }}</span>
                                        <span class="block text-xs text-[#64748B] truncate">{{ $mat->file_name }}</span>
                                    </span>
                                    <i class="fas fa-external-link-alt text-[#94A3B8]"></i>
                                </a>
                            @endforeach
                        @elseif($panelType === 'assignment')
                            <a href="/student/assignments/{{ $item->id }}" class="learn-btn-primary inline-flex mt-2"><i class="fas fa-tasks ml-1"></i> فتح الواجب</a>
                        @elseif($panelType === 'exam')
                            <a href="{{ route('student.exams.show', $item->id) }}" class="learn-btn-primary inline-flex mt-2"><i class="fas fa-play ml-1"></i> بدء الامتحان</a>
                        @else
                            <p class="learn-discussion-placeholder">لا توجد موارد إضافية لهذا الدرس.</p>
                        @endif
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'notes'">
                        <textarea class="learn-notes-editor"
                                  placeholder="اكتب ملاحظاتك الشخصية هنا... (يتم الحفظ تلقائياً)"
                                  x-model="lessonNotes['{{ $noteKey }}']"
                                  @input.debounce.500ms="saveLessonNote('{{ $noteKey }}', $event.target.value)"></textarea>
                        <p class="text-xs text-[#64748B] mt-2"><i class="fas fa-save ml-1"></i> الحفظ التلقائي مفعّل</p>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'discussion'">
                        <div class="learn-discussion-placeholder">
                            <i class="fas fa-comments text-3xl text-[#CBD5E1] mb-3 block"></i>
                            <p>انضم للنقاش مع زملائك — قريباً</p>
                        </div>
                    </div>

                    <div class="learn-tab-panel" x-show="getPanelTab('{{ $panelType }}', {{ $item->id }}) === 'qa'">
                        <div class="learn-discussion-placeholder">
                            <i class="fas fa-question-circle text-3xl text-[#CBD5E1] mb-3 block"></i>
                            <p>اطرح سؤالاً على المدرب — قريباً</p>
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
