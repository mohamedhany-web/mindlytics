@php
    $totalSectionsCount = isset($sections) ? $sections->count() : 0;
    $progressPct = min(100, (float)($progress ?? 0));
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

<div class="learn-page learn-rtl"
     dir="rtl"
     data-course-id="{{ $course->id }}"
     data-course-progress="{{ $progressPct }}"
     data-total-items="{{ $totalLessons ?? 0 }}"
     data-completed-items="{{ $completedLessons ?? 0 }}"
     data-lectures-url="{{ route('my-courses.lectures.show', [$course, '_LID_']) }}"
     x-data="courseFocusMode"
     @keydown.escape.window="if (mobileCurriculumOpen) { mobileCurriculumOpen = false } else { window.location.href='{{ route('my-courses.show', $course) }}' }"
     @keydown.ctrl.f.window.prevent="document.querySelector('.learn-search-input')?.focus()"
     x-init="initLearnPage()">

    {{-- Premium auto-advance overlay --}}
    <div id="autoplay-next-overlay" dir="rtl" role="dialog" aria-modal="true" aria-labelledby="autoplay-title">
        <div class="learn-autoplay-card">
            <div class="learn-celebrate" aria-hidden="true">🎉</div>
            <p class="learn-autoplay-title" id="autoplay-title">تم إكمال الدرس بنجاح!</p>
            <p class="learn-autoplay-next-label">الدرس التالي</p>
            <p id="autoplay-next-title" class="learn-autoplay-next-title"></p>
            <div class="learn-countdown-ring">
                <svg width="44" height="44" style="transform:rotate(-90deg)" aria-hidden="true">
                    <circle id="autoplay-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="#E2E8F0" stroke-width="3"/>
                    <circle id="autoplay-ring" cx="22" cy="22" r="18" fill="none" stroke="#2563EB" stroke-width="3" stroke-dasharray="113" stroke-dashoffset="0"/>
                </svg>
                <span id="autoplay-countdown-num">5</span>
                <span style="font-size:0.8125rem;color:#64748B">ثانية</span>
            </div>
            <div class="learn-autoplay-actions">
                <button type="button" id="autoplay-btn-now" onclick="window._autoplayNow && window._autoplayNow()">
                    <i class="fas fa-play ml-1"></i> ابدأ الآن
                </button>
                <button type="button" id="autoplay-btn-cancel" onclick="window._autoplayCancel && window._autoplayCancel()">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    {{-- Sticky learning header --}}
    <header class="learn-top-header">
        <div class="learn-top-header-inner">
            <a href="{{ route('my-courses.show', $course) }}" class="learn-back-btn" title="{{ __('common.back') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="learn-header-titles min-w-0">
                <p class="learn-header-course truncate">{{ $course->localized('title') }}</p>
                <h1 class="learn-header-lesson" x-text="activeLessonTitle || '{{ __('student.learn') }}'">{{ __('student.learn') }}</h1>
            </div>
            <div class="learn-header-progress">
                <div class="learn-progress-meta">
                    <span><span class="learn-progress-count">{{ $completedLessons ?? 0 }}</span> / <span class="learn-progress-total">{{ $totalLessons ?? 0 }}</span> مكتمل</span>
                    <span class="learn-progress-pct font-bold text-[#2563EB]">{{ number_format($progressPct, 0) }}%</span>
                </div>
                <div class="learn-progress-track" role="progressbar" :aria-valuenow="Math.round({{ $progressPct }})" aria-valuemin="0" aria-valuemax="100">
                    <div class="learn-progress-fill" style="width: {{ $progressPct }}%"></div>
                </div>
            </div>
            <div class="learn-header-actions">
                <button type="button" class="learn-icon-btn lg:hidden" @click="mobileCurriculumOpen = true" aria-label="المنهج">
                    <i class="fas fa-list-ul"></i>
                </button>
                <button type="button" class="learn-icon-btn" @click="document.querySelector('.learn-search-input')?.focus()" aria-label="بحث">
                    <i class="fas fa-search"></i>
                </button>
                <button type="button" class="learn-icon-btn" @click="toggleFullscreen()" :class="{ 'active': isFullscreen }" aria-label="ملء الشاشة">
                    <i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                </button>
                <button type="button" class="learn-btn-primary hidden sm:inline-flex" @click="toggleFocusMode()">
                    <i class="fas fa-bolt"></i>
                    <span x-text="focusMode ? 'وضع عادي' : 'وضع التركيز'">وضع التركيز</span>
                </button>
            </div>
        </div>
    </header>

    <div class="learn-shell">
        <div class="learn-grid">
            {{-- المنهج — العمود الأيمن في RTL --}}
            <aside class="learn-sidebar"
                   :class="{ 'drawer-open': mobileCurriculumOpen }"
                   id="learn-curriculum-drawer">
                <div class="learn-sidebar-header">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h2 class="learn-sidebar-title">
                            <i class="fas fa-layer-group"></i>
                            {{ __('student.learn_curriculum_title') }}
                        </h2>
                        <button type="button" class="learn-icon-btn lg:hidden" @click="mobileCurriculumOpen = false" aria-label="إغلاق">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="learn-sidebar-stats">
                        <span class="learn-sidebar-stat"><i class="fas fa-folder"></i> {{ $totalSectionsCount }} أقسام</span>
                        <span class="learn-sidebar-stat"><i class="fas fa-play-circle"></i> {{ $totalLessons ?? 0 }} عنصر</span>
                        <span class="learn-sidebar-stat learn-progress-pct">{{ number_format($progressPct, 0) }}%</span>
                    </div>
                    <div class="learn-sidebar-nav-row hidden lg:flex" role="group" aria-label="تنقل المنهج">
                        <button type="button" class="learn-sidebar-nav-pill" @click="goNavPrev()" :disabled="!hasNavPrev()" title="{{ __('student.learn_nav_prev') }}">
                            <i class="fas fa-chevron-right"></i>
                            <span>{{ __('student.learn_nav_prev') }}</span>
                        </button>
                        <button type="button" class="learn-sidebar-nav-pill learn-sidebar-nav-pill--primary" @click="goNavNext()" :disabled="!navNextAllowed()" title="{{ __('student.learn_nav_next') }}">
                            <span>{{ __('student.learn_nav_next') }}</span>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                    <div class="learn-search-wrap">
                        <input type="search"
                               class="learn-search-input"
                               x-model="searchQuery"
                               placeholder="ابحث في الدروس..."
                               @keydown.escape="searchQuery = ''"
                               aria-label="بحث في المنهج">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="learn-filter-chips" role="group" aria-label="تصفية">
                        <button type="button" class="learn-filter-chip" :class="{ 'active': curriculumFilter === 'all' }" @click="curriculumFilter = 'all'; filterCurriculum()">الكل</button>
                        <button type="button" class="learn-filter-chip" :class="{ 'active': curriculumFilter === 'completed' }" @click="curriculumFilter = 'completed'; filterCurriculum()">مكتمل</button>
                        <button type="button" class="learn-filter-chip" :class="{ 'active': curriculumFilter === 'progress' }" @click="curriculumFilter = 'progress'; filterCurriculum()">قيد التقدم</button>
                        <button type="button" class="learn-filter-chip" :class="{ 'active': curriculumFilter === 'unlocked' }" @click="curriculumFilter = 'unlocked'; filterCurriculum()">متاح</button>
                        <button type="button" class="learn-filter-chip" :class="{ 'active': curriculumFilter === 'locked' }" @click="curriculumFilter = 'locked'; filterCurriculum()">مقفل</button>
                    </div>
                    <p class="learn-sidebar-hint"
                       x-show="selectedLecture && hasNavNext() && !navNextAllowed()" x-cloak>
                        {{ __('student.learn_nav_next_hint_lecture') }}
                    </p>
                </div>
                <div id="learn-curriculum-sidebar" class="learn-sidebar-scroll">
                    @if(isset($sidebarExams) && $sidebarExams->count() > 0)
                        <div class="mb-3">
                            <div class="curriculum-section-header"
                                 :class="{ 'collapsed': isSectionCollapsed('sidebar-exams') }"
                                 @click="toggleSection('sidebar-exams')" role="button" tabindex="0">
                                <span><i class="fas fa-clipboard-check"></i> الاختبارات ({{ $sidebarExams->count() }})</span>
                                <i class="fas fa-chevron-down curriculum-section-chevron"></i>
                            </div>
                            <div class="curriculum-section-body"
                                 :class="{ 'is-collapsed': isSectionCollapsed('sidebar-exams') }">
                                @foreach($sidebarExams as $exam)
                                    <div class="curriculum-item"
                                         data-item-type="exam"
                                         data-item-id="{{ $exam->id }}"
                                         data-item-locked="0"
                                         data-item-completed="0"
                                         @click="loadExam({{ $exam->id }}); syncActivePanel('exam', {{ $exam->id }}); mobileCurriculumOpen = false">
                                        <div class="flex items-start gap-2.5">
                                            <div class="curriculum-item-icon learn-type-exam"><i class="fas fa-clipboard-check"></i></div>
                                            <div class="min-w-0 flex-1">
                                                <div class="curriculum-item-title">{{ $exam->title }}</div>
                                                <div class="curriculum-item-meta">
                                                    <span class="learn-type-badge learn-type-exam"><i class="fas fa-clipboard-check"></i> امتحان</span>
                                                    @if($exam->duration_minutes)<span><i class="fas fa-clock"></i> {{ $exam->duration_minutes }} د</span>@endif
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
                            @include('student.my-courses.partials.learn-sidebar-section', ['section' => $section, 'depth' => 0])
                        @endforeach
                    @endif
                </div>
            </aside>

            {{-- المحتوى — العمود الأيسر في RTL --}}
            <main class="learn-main" id="learn-main-scroll">
                {{-- Gamification strip --}}
                <div class="learn-achievements">
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#EFF6FF;color:#2563EB"><i class="fas fa-chart-line"></i></div>
                        <div class="learn-achievement-value learn-progress-pct">{{ number_format($progressPct, 0) }}%</div>
                        <div class="learn-achievement-label">إكمال الكورس</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#ECFDF5;color:#10B981"><i class="fas fa-check-double"></i></div>
                        <div class="learn-achievement-value learn-progress-count">{{ $completedLessons ?? 0 }}/{{ $totalLessons ?? 0 }}</div>
                        <div class="learn-achievement-label">دروس مكتملة</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#FFF7ED;color:#F59E0B"><i class="fas fa-fire"></i></div>
                        <div class="learn-achievement-value" x-text="streakDays">0</div>
                        <div class="learn-achievement-label">أيام متتالية</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#F0FDFA;color:#06B6D4"><i class="fas fa-star"></i></div>
                        <div class="learn-achievement-value" x-text="xpPoints">{{ (int)(($completedLessons ?? 0) * 50) }}</div>
                        <div class="learn-achievement-label">نقاط XP</div>
                    </div>
                </div>

                <div x-show="currentSectionDescription" x-transition class="mb-4 p-4 rounded-2xl border border-[#E2E8F0] bg-white text-sm text-[#64748B] leading-relaxed">
                    <p class="whitespace-pre-wrap" x-text="currentSectionDescription"></p>
                </div>

                {{-- مشغّل الفيديو — نفس آلية الصفحة القديمة (#learn-video-embed + شريط التقدم) --}}
                <div x-show="(selectedLesson && showVideoPlayer) || (selectedLecture && showVideoPlayer)"
                     class="learn-video-hero learn-video-hero-main lesson-video-viewer mb-5">
                    <div class="learn-video-progress-bar flex-shrink-0" id="learn-watch-percent-bar">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-sm font-semibold text-sky-300">نسبة المشاهدة</span>
                            <template x-if="selectedLecture">
                                <span id="lecture-watch-pct-text" class="text-sm font-bold text-white tabular-nums">0.0%</span>
                            </template>
                            <span x-show="selectedLesson && showVideoPlayer"
                                  x-text="(Math.round((videoProgressPercent || 0) * 10) / 10).toFixed(1) + '%'"
                                  class="text-sm font-bold text-white tabular-nums">0.0%</span>
                        </div>
                        <div class="h-2.5 bg-white/10 rounded-full overflow-hidden">
                            <template x-if="selectedLecture">
                                <div id="lecture-watch-pct-fill"
                                     class="h-full rounded-full transition-all duration-300 min-w-[2px]"
                                     style="width: 0%; background: linear-gradient(270deg,#38bdf8,#0ea5e9);"></div>
                            </template>
                            <div x-show="selectedLesson && showVideoPlayer"
                                 class="h-full rounded-full transition-all duration-300 min-w-[2px]"
                                 style="background: linear-gradient(270deg,#38bdf8,#0ea5e9);"
                                 :style="'width: ' + Math.min(100, Math.max(0, videoProgressPercent || 0)) + '%'"></div>
                        </div>
                    </div>
                    <div class="learn-video-aspect w-full relative bg-black flex-1 min-h-0">
                        <div id="learn-video-embed"
                             class="absolute inset-0 w-full h-full lecture-video-mount"
                             x-show="selectedLecture && showVideoPlayer"></div>
                        <div x-show="selectedLesson && showVideoPlayer" class="absolute inset-0 w-full h-full">
                            @include('student.my-courses.partials.video-player')
                        </div>
                    </div>
                </div>

                <div x-show="selectedLecture && !showVideoPlayer" x-transition class="learn-curriculum-panel p-6 mb-5">
                    <div x-html="lectureContent"></div>
                </div>

                <div x-show="selectedLecture && lectureMaterials && lectureMaterials.length" x-transition
                     class="mb-5 rounded-2xl border border-[#E2E8F0] bg-white overflow-hidden shadow-sm">
                    <div class="px-4 sm:px-5 py-3.5 bg-gradient-to-l from-sky-50 to-white border-b border-[#E2E8F0] flex items-center justify-between gap-3">
                        <h3 class="text-base font-bold text-[#0F172A] flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-sky-100 flex items-center justify-center">
                                <i class="fas fa-paperclip text-sky-600"></i>
                            </span>
                            مواد المحاضرة
                            <span class="text-xs font-semibold text-sky-600 bg-sky-100 px-2.5 py-0.5 rounded-full" x-text="lectureMaterials.length"></span>
                        </h3>
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="mat in lectureMaterials" :key="mat.id">
                                <a :href="mat.download_url" target="_blank" rel="noopener"
                                   class="group flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-sky-50 hover:border-sky-200 transition-all">
                                    <span class="w-12 h-12 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center shrink-0">
                                        <i class="fas text-lg" :class="getMaterialIconClass(mat)"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-[#0F172A] truncate" x-text="mat.title || mat.file_name"></div>
                                        <div class="text-xs text-[#64748B] truncate" x-text="mat.file_name"></div>
                                    </div>
                                    <i class="fas fa-download text-sky-500"></i>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="learn-curriculum-panels" class="learn-panels-scroll">
                    @if(isset($sections) && $sections->count() > 0)
                        @include('student.my-courses.partials.learn-curriculum-panels', [
                            'sections' => $sections,
                            'course' => $course,
                        ])
                    @else
                        <div class="learn-curriculum-panel panel-active p-12 text-center">
                            <i class="fas fa-book-open text-5xl text-[#CBD5E1] mb-4"></i>
                            <p class="text-[#0F172A] font-bold text-lg">لا توجد عناصر في المنهج بعد</p>
                            <p class="text-[#64748B] text-sm mt-2">ستظهر المحاضرات والواجبات هنا عند إضافتها.</p>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div class="learn-drawer-backdrop" :class="{ 'open': mobileCurriculumOpen }" @click="mobileCurriculumOpen = false" x-show="mobileCurriculumOpen" x-cloak></div>

    {{-- Mobile bottom navigation --}}
    <nav class="learn-mobile-bar" aria-label="تنقل سريع">
        <button type="button" @click="goNavPrev()" :disabled="!hasNavPrev()"><i class="fas fa-chevron-right"></i><span>السابق</span></button>
        <button type="button" class="active" @click="mobileCurriculumOpen = true"><i class="fas fa-list-ul"></i><span>المنهج</span></button>
        <button type="button" @click="goNavNext()" :disabled="!navNextAllowed()"><i class="fas fa-chevron-left"></i><span>التالي</span></button>
    </nav>
</div>
