@php
    use App\Support\StudentFigmaAssets;
    $sp = StudentFigmaAssets::urls();
    $logoUrl = \App\Support\SiteBranding::logoUrl();
    $totalSectionsCount = isset($sections) ? $sections->count() : 0;
    $progressPct = min(100, (float) ($progress ?? 0));
    $learnRtl = app()->getLocale() === 'ar';
    $ringDeg = (int) round($progressPct * 3.6);
    $remaining = max(0, (int) ($totalLessons ?? 0) - (int) ($completedLessons ?? 0));
@endphp
@if(session('payment_success_modal'))
    @include('components.payment-success-modal', [
        'message' => session('success'),
        'redirectUrl' => session('payment_success_redirect_url'),
        'seconds' => 5,
    ])
@endif
<script type="application/json" id="learn-lectures-data">{!! $lecturesDataJson ?? '{}' !!}</script>
<script type="application/json" id="learn-next-item-map">{!! json_encode($nextItemByLectureId ?? []) !!}</script>
<script type="application/json" id="learn-next-lesson-map">{!! json_encode($nextItemByLessonId ?? []) !!}</script>

<div class="learn-page learn-dash {{ $learnRtl ? 'learn-rtl' : '' }}"
     dir="{{ $learnRtl ? 'rtl' : 'ltr' }}"
     data-course-id="{{ $course->id }}"
     data-course-progress="{{ $progressPct }}"
     data-total-items="{{ $totalLessons ?? 0 }}"
     data-completed-items="{{ $completedLessons ?? 0 }}"
     data-lectures-url="{{ route('my-courses.lectures.show', [$course, '_LID_']) }}"
     x-data="courseFocusMode"
     @keydown.escape.window="if (mobileCurriculumOpen) { mobileCurriculumOpen = false } else { window.location.href='{{ route('my-courses.show', $course) }}' }"
     x-init="initLearnPage()">

    <div id="autoplay-next-overlay" dir="{{ $learnRtl ? 'rtl' : 'ltr' }}" role="dialog" aria-modal="true" aria-labelledby="autoplay-title">
        <div class="learn-autoplay-card">
            <p class="learn-autoplay-title" id="autoplay-title">{{ __('student.learn_item_completed') }}</p>
            <p class="learn-autoplay-next-label">{{ __('student.learn_next_item') }}</p>
            <p id="autoplay-next-title" class="learn-autoplay-next-title"></p>
            <div class="learn-countdown-ring">
                <svg width="44" height="44" style="transform:rotate(-90deg)" aria-hidden="true">
                    <circle id="autoplay-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="#E8E8E4" stroke-width="3"/>
                    <circle id="autoplay-ring" cx="22" cy="22" r="18" fill="none" stroke="#aed9ea" stroke-width="3" stroke-dasharray="113" stroke-dashoffset="0"/>
                </svg>
                <span id="autoplay-countdown-num">5</span>
                <span style="font-size:0.8125rem;color:#6b6b76">{{ __('student.learn_seconds') }}</span>
            </div>
            <div class="learn-autoplay-actions">
                <button type="button" id="autoplay-btn-now" onclick="window._autoplayNow && window._autoplayNow()">{{ __('student.learn_start_now') }}</button>
                <button type="button" id="autoplay-btn-cancel" onclick="window._autoplayCancel && window._autoplayCancel()">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>

    {{-- Dashboard-like shell: dark curriculum sidebar + main --}}
    <div class="learn-dash-shell">
        {{-- DARK SIDEBAR (portal twin) --}}
        <aside class="learn-dash-sidebar"
               id="learn-curriculum-drawer"
               :class="{ 'is-open': mobileCurriculumOpen }">
            <div class="learn-dash-brand">
                <button type="button" class="learn-dash-close learn-dash-only-mobile" @click="mobileCurriculumOpen = false" aria-label="{{ __('common.close') }}">
                    <span class="learn-dash-close-x" aria-hidden="true">&times;</span>
                </button>
                <a href="{{ route('my-courses.show', $course) }}" class="learn-dash-brand-link">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="" onerror="this.style.display='none'">
                    @endif
                    <span>{{ __('student.learn_curriculum_title') }}</span>
                </a>
                <p class="learn-dash-course-name">{{ $course->localized('title') }}</p>
            </div>

            <div class="learn-dash-search">
                <x-student.figma-icon name="icon-search.svg" box="size-4" />
                <input type="search"
                       class="learn-search-input"
                       x-model="searchQuery"
                       placeholder="{{ __('student.learn_search_placeholder') }}"
                       @keydown.escape="searchQuery = ''"
                       aria-label="{{ __('student.learn_search_placeholder') }}">
            </div>

            <div id="learn-curriculum-sidebar" class="learn-dash-nav">
                @if(isset($sidebarExams) && $sidebarExams->count() > 0)
                    <div class="learn-dash-section">
                        <button type="button"
                                class="learn-dash-section-btn"
                                :class="{ 'is-collapsed': isSectionCollapsed('sidebar-exams') }"
                                @click="toggleSection('sidebar-exams')">
                            <span>{{ __('student.exams_page_title') }}</span>
                            <span class="learn-dash-count">{{ $sidebarExams->count() }}</span>
                        </button>
                        <div class="learn-dash-section-body" :class="{ 'is-collapsed': isSectionCollapsed('sidebar-exams') }">
                            @foreach($sidebarExams as $exam)
                                <div class="curriculum-item"
                                     data-item-type="exam"
                                     data-item-id="{{ $exam->id }}"
                                     data-item-locked="0"
                                     data-item-completed="0"
                                     @click="loadExam({{ $exam->id }}); syncActivePanel('exam', {{ $exam->id }}); mobileCurriculumOpen = false">
                                    <div class="curriculum-item-inner">
                                        <span class="curriculum-status curriculum-status--ready" aria-hidden="true">
                                            <x-student.figma-icon name="icon-exams.svg" box="size-3.5" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="curriculum-item-title">{{ $exam->title }}</div>
                                            <div class="curriculum-item-meta">
                                                <span class="curriculum-type-row">
                                                    <x-student.figma-icon name="icon-exams.svg" box="size-3" class="curriculum-type-ico" />
                                                    <span>{{ __('student.curriculum_type_exam') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($sections) && $sections->count() > 0)
                    <script type="application/json" id="learn-section-descriptions">@json($sectionDescriptions ?? [])</script>
                    @foreach($sections as $section)
                        @include('student.my-courses.partials.learn-sidebar-section', ['section' => $section, 'depth' => 0, 'course' => $course])
                    @endforeach
                @else
                    <p class="learn-dash-empty">{{ __('student.curriculum_empty_desc') }}</p>
                @endif
            </div>

            <div class="learn-dash-sidebar-foot">
                <a href="{{ route('my-courses.show', $course) }}" class="learn-dash-exit">
                    <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="{{ $learnRtl ? '' : 'rotate-180' }}" />
                    {{ __('student.back_to_course') }}
                </a>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="learn-dash-main">
            <header class="learn-dash-header">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button type="button" class="learn-dash-menu learn-dash-only-mobile" @click="mobileCurriculumOpen = true" aria-label="{{ __('student.learn_curriculum_title') }}">
                        <x-student.figma-icon name="icon-courses.svg" box="size-5" />
                    </button>
                    <div class="min-w-0">
                        <p class="learn-dash-eyebrow">{{ __('student.course_hub_eyebrow') }}</p>
                        <h1 class="learn-dash-title truncate" x-text="activeLessonTitle || '{{ __('student.learn_pick_item') }}'">{{ __('student.learn_pick_item') }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-student.language-switcher />
                    <a href="{{ route('my-courses.show', $course) }}" class="sp-promo-btn !mt-0 !py-2.5 !px-4 !text-sm hidden sm:inline-flex">{{ __('student.exit_learn') }}</a>
                </div>
            </header>

            <div class="learn-dash-grid">
                {{-- PRIMARY: video first on all breakpoints --}}
                <div class="learn-dash-primary space-y-4 min-w-0" id="learn-main-scroll">
                    <div class="sp-card learn-empty-pick p-8 sm:p-10 text-center"
                         x-show="!selectedLesson && !selectedLecture && !activePanelType"
                         x-cloak>
                        <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-mint);width:56px;height:56px;color:var(--sp-accent-text)">
                            <x-student.figma-icon name="icon-courses.svg" box="size-7" />
                        </span>
                        <h2 class="font-extrabold text-lg m-0 mb-2">{{ __('student.learn_empty_title') }}</h2>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.learn_empty_desc') }}</p>
                        <button type="button" class="sp-promo-btn !mt-0 learn-dash-only-mobile" @click="mobileCurriculumOpen = true">{{ __('student.learn_open_curriculum') }}</button>
                    </div>

                    {{-- VIDEO — always first in primary column --}}
                    <section class="sp-card learn-video-card overflow-hidden learn-dash-video-first"
                             x-show="selectedLecture || (selectedLesson && showVideoPlayer)"
                             x-cloak>
                        <div class="learn-video-card-head">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.learn_now_watching') }}</p>
                                <h2 class="text-base sm:text-lg font-extrabold m-0 truncate" x-text="activeLessonTitle"></h2>
                            </div>
                            <span class="sp-pill sp-pill--progress !text-xs shrink-0">{{ __('student.learn_watch_percent') }}</span>
                        </div>

                        <div class="learn-video-hero learn-video-hero-main lesson-video-viewer">
                            <div class="learn-video-aspect w-full relative bg-black">
                                <div id="learn-video-embed"
                                     class="absolute inset-0 w-full h-full lecture-video-mount"
                                     :class="selectedLecture && showVideoPlayer ? '' : 'invisible pointer-events-none'"></div>
                                <div x-show="selectedLesson && showVideoPlayer" class="absolute inset-0 w-full h-full">
                                    @include('student.my-courses.partials.video-player')
                                </div>
                                @include('student.my-courses.partials.learn-video-shield')
                            </div>
                        </div>

                        <div class="learn-video-card-foot" id="learn-watch-percent-bar">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-extrabold text-[var(--sp-muted)]">{{ __('student.learn_watch_percent') }}</span>
                                <span id="lecture-watch-pct-text"
                                      x-show="selectedLecture"
                                      class="text-sm font-black text-[var(--sp-accent-text)] tabular-nums">0.0%</span>
                                <span x-show="selectedLesson && showVideoPlayer"
                                      x-text="(Math.round((videoProgressPercent || 0) * 10) / 10).toFixed(1) + '%'"
                                      class="text-sm font-black text-[var(--sp-accent-text)] tabular-nums">0.0%</span>
                            </div>
                            <div class="learn-dash-watch-track">
                                <div id="lecture-watch-pct-fill"
                                     x-show="selectedLecture"
                                     class="learn-dash-watch-fill"
                                     style="width: 0%"></div>
                                <div x-show="selectedLesson && showVideoPlayer"
                                     class="learn-dash-watch-fill"
                                     :style="'width: ' + Math.min(100, Math.max(0, videoProgressPercent || 0)) + '%'"></div>
                            </div>
                        </div>
                    </section>

                    <div class="learn-simple-nav"
                         x-show="selectedLesson || selectedLecture || activePanelType"
                         x-cloak>
                        <button type="button" class="learn-simple-nav-btn" @click="goNavPrev()" :disabled="!hasNavPrev()">
                            <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="{{ $learnRtl ? '' : 'rotate-180' }}" />
                            <span>{{ __('student.learn_nav_prev') }}</span>
                        </button>
                        <button type="button" class="learn-simple-nav-btn learn-simple-nav-btn--primary" @click="goNavNext()" :disabled="!navNextAllowed()">
                            <span>{{ __('student.learn_nav_next') }}</span>
                            <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="{{ $learnRtl ? 'rotate-180' : '' }}" />
                        </button>
                    </div>
                    <p class="text-center text-xs font-bold text-[var(--sp-muted)] m-0"
                       x-show="selectedLecture && hasNavNext() && !navNextAllowed()" x-cloak>
                        {{ __('student.learn_nav_next_hint_lecture') }}
                    </p>

                    <div x-show="currentSectionDescription" x-transition class="sp-card p-4 learn-dash-desc-card">
                        <p class="whitespace-pre-wrap m-0 text-sm text-[var(--sp-muted)]" x-text="currentSectionDescription"></p>
                    </div>

                    <div x-show="selectedLecture && !showVideoPlayer" x-transition class="sp-card p-6">
                        <div x-html="lectureContent"></div>
                    </div>

                    <div x-show="selectedLecture && lectureMaterials && lectureMaterials.length" x-transition class="sp-card overflow-hidden">
                        <div class="px-5 py-4 border-b border-[#ecece8] flex items-center justify-between gap-3">
                            <h3 class="m-0 text-base font-extrabold">{{ __('student.learn_lecture_materials') }}</h3>
                            <span class="sp-pill sp-pill--progress !py-1 !px-2 !text-xs" x-text="lectureMaterials.length"></span>
                        </div>
                        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="mat in lectureMaterials" :key="mat.id">
                                <a :href="mat.download_url" target="_blank" rel="noopener" class="learn-material-row">
                                    <span class="sp-icon-bubble !w-11 !h-11" style="background:var(--sp-sky)">
                                        <i class="fas text-sm" :class="getMaterialIconClass(mat)"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-extrabold text-sm truncate" x-text="mat.title || mat.file_name"></div>
                                        <div class="text-xs text-[var(--sp-muted)] truncate" x-text="mat.file_name"></div>
                                    </div>
                                    <i class="fas fa-download text-[var(--sp-accent-text)] opacity-50"></i>
                                </a>
                            </template>
                        </div>
                    </div>

                    <div id="learn-curriculum-panels" class="learn-panels-scroll">
                        @if(isset($sections) && $sections->count() > 0)
                            @include('student.my-courses.partials.learn-curriculum-panels', [
                                'sections' => $sections,
                                'course' => $course,
                            ])
                        @endif
                    </div>
                </div>

                {{-- RAIL: after video on phone --}}
                <aside class="learn-dash-rail space-y-4">
                    <section class="sp-card p-5 text-center learn-rail-progress">
                        <p class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0 mb-4">{{ __('student.your_stats') }}</p>
                        <div class="learn-dash-ring mx-auto" style="background:conic-gradient(var(--sp-accent) {{ $ringDeg }}deg, #ecece8 0deg)"
                             role="img" aria-label="{{ __('student.percent_complete', ['pct' => (int) $progressPct]) }}">
                            <div class="learn-dash-ring-inner">
                                <span class="text-2xl font-black text-[var(--sp-accent-text)] leading-none learn-progress-pct">{{ (int) $progressPct }}%</span>
                                <span class="text-[10px] font-bold text-[var(--sp-muted)] mt-1">{{ __('student.progress') }}</span>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mt-4">
                            {{ __('student.section_progress_count', ['done' => $completedLessons ?? 0, 'total' => $totalLessons ?? 0]) }}
                        </p>
                    </section>

                    <section class="sp-card p-5 learn-rail-next learn-dash-only-desktop">
                        <h3 class="sp-section-title mb-3">{{ __('student.next_step') }}</h3>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mb-4" x-show="!selectedLecture && !selectedLesson">{{ __('student.learn_rail_pick_hint') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mb-4"
                           x-show="selectedLecture && hasNavNext() && !navNextAllowed()" x-cloak>
                            {{ __('student.learn_nav_next_hint_lecture') }}
                        </p>
                        <button type="button"
                                class="sp-promo-btn !mt-0 w-full !text-[var(--sp-accent-text)] border-0 cursor-pointer"
                                @click="goNavNext()"
                                :disabled="!navNextAllowed()">
                            {{ __('student.learn_nav_next') }}
                        </button>
                        <button type="button"
                                class="mt-2 w-full rounded-[16px] bg-[#f5f5f5] px-4 py-3 text-sm font-extrabold text-[var(--sp-text)] border-0 cursor-pointer"
                                @click="goNavPrev()"
                                :disabled="!hasNavPrev()">
                            {{ __('student.learn_nav_prev') }}
                        </button>
                    </section>

                    <section class="sp-card p-5 learn-rail-snapshot">
                        <h3 class="sp-section-title mb-3">{{ __('student.learn_course_snapshot') }}</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-[16px] bg-[var(--sp-mint)] p-3 text-center">
                                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0 learn-progress-count">{{ (int) ($completedLessons ?? 0) }}</p>
                                <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.completed') }}</p>
                            </div>
                            <div class="rounded-[16px] bg-[var(--sp-lilac)] p-3 text-center">
                                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ (int) ($totalLessons ?? 0) }}</p>
                                <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.lessons_count') }}</p>
                            </div>
                            <div class="rounded-[16px] bg-[var(--sp-peach)] p-3 text-center">
                                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $totalSectionsCount }}</p>
                                <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.learn_sections_label') }}</p>
                            </div>
                            <div class="rounded-[16px] bg-[var(--sp-amber-soft)] p-3 text-center">
                                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $remaining }}</p>
                                <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.remaining_items') }}</p>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>

    <div class="learn-drawer-backdrop" :class="{ 'open': mobileCurriculumOpen }" @click="mobileCurriculumOpen = false" x-show="mobileCurriculumOpen" x-cloak></div>

    <nav class="learn-mobile-bar" aria-label="{{ __('student.learn_quick_nav') }}">
        <button type="button" @click="goNavPrev()" :disabled="!hasNavPrev()">
            <x-student.figma-icon name="icon-chevron.svg" box="size-5" class="{{ $learnRtl ? '' : 'rotate-180' }}" />
            <span>{{ __('student.learn_nav_prev') }}</span>
        </button>
        <button type="button" class="active" @click="mobileCurriculumOpen = true">
            <x-student.figma-icon name="icon-courses.svg" box="size-5" />
            <span>{{ __('student.learn_curriculum_title') }}</span>
        </button>
        <button type="button" @click="goNavNext()" :disabled="!navNextAllowed()">
            <x-student.figma-icon name="icon-chevron.svg" box="size-5" class="{{ $learnRtl ? 'rotate-180' : '' }}" />
            <span>{{ __('student.learn_nav_next') }}</span>
        </button>
    </nav>
</div>
