<?php
    $totalSectionsCount = isset($sections) ? $sections->count() : 0;
    $progressPct = min(100, (float)($progress ?? 0));
?>
<?php if(session('payment_success_modal')): ?>
    <?php echo $__env->make('components.payment-success-modal', [
        'message' => session('success'),
        'redirectUrl' => session('payment_success_redirect_url'),
        'seconds' => 5,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<script type="application/json" id="learn-lectures-data"><?php echo $lecturesDataJson ?? '{}'; ?></script>
<script type="application/json" id="learn-next-item-map"><?php echo json_encode($nextItemByLectureId ?? []); ?></script>
<script type="application/json" id="learn-next-lesson-map"><?php echo json_encode($nextItemByLessonId ?? []); ?></script>

<div class="learn-page learn-rtl"
     dir="rtl"
     data-course-id="<?php echo e($course->id); ?>"
     data-course-progress="<?php echo e($progressPct); ?>"
     data-total-items="<?php echo e($totalLessons ?? 0); ?>"
     data-completed-items="<?php echo e($completedLessons ?? 0); ?>"
     data-lectures-url="<?php echo e(route('my-courses.lectures.show', [$course, '_LID_'])); ?>"
     x-data="courseFocusMode"
     @keydown.escape.window="if (mobileCurriculumOpen) { mobileCurriculumOpen = false } else { window.location.href='<?php echo e(route('my-courses.show', $course)); ?>' }"
     @keydown.ctrl.f.window.prevent="document.querySelector('.learn-search-input')?.focus()"
     x-init="initLearnPage()">

    
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

    
    <header class="learn-top-header">
        <div class="learn-top-header-inner">
            <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="learn-back-btn" title="<?php echo e(__('common.back')); ?>">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="learn-header-titles min-w-0">
                <p class="learn-header-course truncate"><?php echo e($course->localized('title')); ?></p>
                <h1 class="learn-header-lesson" x-text="activeLessonTitle || '<?php echo e(__('student.learn')); ?>'"><?php echo e(__('student.learn')); ?></h1>
            </div>
            <div class="learn-header-progress">
                <div class="learn-progress-meta">
                    <span><span class="learn-progress-count"><?php echo e($completedLessons ?? 0); ?></span> / <span class="learn-progress-total"><?php echo e($totalLessons ?? 0); ?></span> مكتمل</span>
                    <span class="learn-progress-pct font-bold text-[#2563EB]"><?php echo e(number_format($progressPct, 0)); ?>%</span>
                </div>
                <div class="learn-progress-track" role="progressbar" :aria-valuenow="Math.round(<?php echo e($progressPct); ?>)" aria-valuemin="0" aria-valuemax="100">
                    <div class="learn-progress-fill" style="width: <?php echo e($progressPct); ?>%"></div>
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
            
            <aside class="learn-sidebar"
                   :class="{ 'drawer-open': mobileCurriculumOpen }"
                   id="learn-curriculum-drawer">
                <div class="learn-sidebar-header">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h2 class="learn-sidebar-title">
                            <i class="fas fa-layer-group"></i>
                            <?php echo e(__('student.learn_curriculum_title')); ?>

                        </h2>
                        <button type="button" class="learn-icon-btn lg:hidden" @click="mobileCurriculumOpen = false" aria-label="إغلاق">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="learn-sidebar-stats">
                        <span class="learn-sidebar-stat"><i class="fas fa-folder"></i> <?php echo e($totalSectionsCount); ?> أقسام</span>
                        <span class="learn-sidebar-stat"><i class="fas fa-play-circle"></i> <?php echo e($totalLessons ?? 0); ?> عنصر</span>
                        <span class="learn-sidebar-stat learn-progress-pct"><?php echo e(number_format($progressPct, 0)); ?>%</span>
                    </div>
                    <div class="learn-sidebar-nav-row hidden lg:flex" role="group" aria-label="تنقل المنهج">
                        <button type="button" class="learn-sidebar-nav-pill" @click="goNavPrev()" :disabled="!hasNavPrev()" title="<?php echo e(__('student.learn_nav_prev')); ?>">
                            <i class="fas fa-chevron-right"></i>
                            <span><?php echo e(__('student.learn_nav_prev')); ?></span>
                        </button>
                        <button type="button" class="learn-sidebar-nav-pill learn-sidebar-nav-pill--primary" @click="goNavNext()" :disabled="!navNextAllowed()" title="<?php echo e(__('student.learn_nav_next')); ?>">
                            <span><?php echo e(__('student.learn_nav_next')); ?></span>
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
                        <?php echo e(__('student.learn_nav_next_hint_lecture')); ?>

                    </p>
                </div>
                <div id="learn-curriculum-sidebar" class="learn-sidebar-scroll">
                    <?php if(isset($sidebarExams) && $sidebarExams->count() > 0): ?>
                        <div class="mb-3">
                            <div class="curriculum-section-header"
                                 :class="{ 'collapsed': isSectionCollapsed('sidebar-exams') }"
                                 @click="toggleSection('sidebar-exams')" role="button" tabindex="0">
                                <span><i class="fas fa-clipboard-check"></i> الاختبارات (<?php echo e($sidebarExams->count()); ?>)</span>
                                <i class="fas fa-chevron-down curriculum-section-chevron"></i>
                            </div>
                            <div class="curriculum-section-body"
                                 :class="{ 'is-collapsed': isSectionCollapsed('sidebar-exams') }">
                                <?php $__currentLoopData = $sidebarExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="curriculum-item"
                                         data-item-type="exam"
                                         data-item-id="<?php echo e($exam->id); ?>"
                                         data-item-locked="0"
                                         data-item-completed="0"
                                         @click="loadExam(<?php echo e($exam->id); ?>); syncActivePanel('exam', <?php echo e($exam->id); ?>); mobileCurriculumOpen = false">
                                        <div class="flex items-start gap-2.5">
                                            <div class="curriculum-item-icon learn-type-exam"><i class="fas fa-clipboard-check"></i></div>
                                            <div class="min-w-0 flex-1">
                                                <div class="curriculum-item-title"><?php echo e($exam->title); ?></div>
                                                <div class="curriculum-item-meta">
                                                    <span class="learn-type-badge learn-type-exam"><i class="fas fa-clipboard-check"></i> امتحان</span>
                                                    <?php if($exam->duration_minutes): ?><span><i class="fas fa-clock"></i> <?php echo e($exam->duration_minutes); ?> د</span><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($sections) && $sections->count() > 0): ?>
                        <script type="application/json" id="learn-section-descriptions"><?php echo json_encode($sectionDescriptions ?? [], 15, 512) ?></script>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('student.my-courses.partials.learn-sidebar-section', ['section' => $section, 'depth' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </aside>

            
            <main class="learn-main" id="learn-main-scroll">
                
                <div class="learn-achievements">
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#EFF6FF;color:#2563EB"><i class="fas fa-chart-line"></i></div>
                        <div class="learn-achievement-value learn-progress-pct"><?php echo e(number_format($progressPct, 0)); ?>%</div>
                        <div class="learn-achievement-label">إكمال الكورس</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#ECFDF5;color:#10B981"><i class="fas fa-check-double"></i></div>
                        <div class="learn-achievement-value learn-progress-count"><?php echo e($completedLessons ?? 0); ?>/<?php echo e($totalLessons ?? 0); ?></div>
                        <div class="learn-achievement-label">دروس مكتملة</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#FFF7ED;color:#F59E0B"><i class="fas fa-fire"></i></div>
                        <div class="learn-achievement-value" x-text="streakDays">0</div>
                        <div class="learn-achievement-label">أيام متتالية</div>
                    </div>
                    <div class="learn-achievement-card">
                        <div class="learn-achievement-icon" style="background:#F0FDFA;color:#06B6D4"><i class="fas fa-star"></i></div>
                        <div class="learn-achievement-value" x-text="xpPoints"><?php echo e((int)(($completedLessons ?? 0) * 50)); ?></div>
                        <div class="learn-achievement-label">نقاط XP</div>
                    </div>
                </div>

                <div x-show="currentSectionDescription" x-transition class="mb-4 p-4 rounded-2xl border border-[#E2E8F0] bg-white text-sm text-[#64748B] leading-relaxed">
                    <p class="whitespace-pre-wrap" x-text="currentSectionDescription"></p>
                </div>

                
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
                            <?php echo $__env->make('student.my-courses.partials.video-player', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <?php if(isset($sections) && $sections->count() > 0): ?>
                        <?php echo $__env->make('student.my-courses.partials.learn-curriculum-panels', [
                            'sections' => $sections,
                            'course' => $course,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php else: ?>
                        <div class="learn-curriculum-panel panel-active p-12 text-center">
                            <i class="fas fa-book-open text-5xl text-[#CBD5E1] mb-4"></i>
                            <p class="text-[#0F172A] font-bold text-lg">لا توجد عناصر في المنهج بعد</p>
                            <p class="text-[#64748B] text-sm mt-2">ستظهر المحاضرات والواجبات هنا عند إضافتها.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <div class="learn-drawer-backdrop" :class="{ 'open': mobileCurriculumOpen }" @click="mobileCurriculumOpen = false" x-show="mobileCurriculumOpen" x-cloak></div>

    
    <nav class="learn-mobile-bar" aria-label="تنقل سريع">
        <button type="button" @click="goNavPrev()" :disabled="!hasNavPrev()"><i class="fas fa-chevron-right"></i><span>السابق</span></button>
        <button type="button" class="active" @click="mobileCurriculumOpen = true"><i class="fas fa-list-ul"></i><span>المنهج</span></button>
        <button type="button" @click="goNavNext()" :disabled="!navNextAllowed()"><i class="fas fa-chevron-left"></i><span>التالي</span></button>
    </nav>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\partials\learn-page-shell.blade.php ENDPATH**/ ?>