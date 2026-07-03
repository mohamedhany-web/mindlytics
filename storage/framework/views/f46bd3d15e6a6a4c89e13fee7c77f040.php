<?php $__env->startSection('title', $course->localized('title') . ' - ' . __('student.learn')); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startPush('meta'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.my-courses.partials.learn-premium-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .lecture-video-mount, .learn-video-aspect { -webkit-user-select: none; user-select: none; }
    .lesson-video-viewer { display: flex; flex-direction: column; }
    .lesson-video-viewer * { -webkit-user-select: none; user-select: none; }
    #learn-video-embed iframe,
    #learn-video-embed video,
    #learn-video-embed #lecture-yt-player-box,
    #learn-video-embed #lecture-yt-player-box iframe {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php
    // تحضير بيانات المحاضرات للـ JavaScript (مع المواد الظاهرة للطالب + تقدم المشاهدة + نسبة فتح التالي)
    $currentUser = auth()->user();
    $lecturesData = $course->lectures->map(function($lecture) use ($course, $currentUser) {
        $lecture->refresh();
        $recordingUrl = \DB::table('lectures')->where('id', $lecture->id)->value('recording_url');
        $videoPlatform = \DB::table('lectures')->where('id', $lecture->id)->value('video_platform');
        $recordingUrlFinal = $recordingUrl ? trim($recordingUrl) : ($lecture->recording_url ? trim($lecture->recording_url) : null);
        $videoPlatformFinal = $videoPlatform ? trim(strtolower($videoPlatform)) : ($lecture->video_platform ? trim(strtolower($lecture->video_platform)) : null);
        // لا نوقّع روابط Bunny هنا — التوقيع يتم عند فتح المحاضرة (مع كاش سيرفر 55 دقيقة) لتقليل التكلفة
        $materials = $lecture->materials()->where('is_visible_to_student', true)->orderBy('sort_order')->get()->map(function($m) use ($course, $lecture) {
            return [
                'id' => $m->id,
                'title' => $m->title ?: $m->file_name,
                'file_name' => $m->file_name,
                'download_url' => route('my-courses.lectures.material.download', [$course->id, $lecture->id, $m->id]),
            ];
        })->values()->all();
        $videoQuestions = $lecture->videoQuestions()->with('question')->orderBy('timestamp_seconds')->get()->filter(function($vq) use ($currentUser) {
            $showCount = $vq->show_count;
            if ($showCount === null || $showCount == 0) return true;
            $answered = \App\Models\LectureVideoQuestionAnswer::where('lecture_video_question_id', $vq->id)->where('user_id', $currentUser->id)->count();
            return $answered < $showCount;
        })->map(function($vq) {
            $payload = $vq->getPayloadForStudent();
            $showEveryTime = $vq->show_count === null || $vq->show_count == 0;
            return [
                'id' => $vq->id,
                'timestamp_seconds' => (int) $vq->timestamp_seconds,
                'show_at_end' => (bool) $vq->show_at_end,
                'text' => $payload['text'] ?? '',
                'options' => $payload['options'] ?? [],
                'type' => $payload['type'] ?? 'multiple_choice',
                'points' => $vq->points,
                'on_wrong' => $vq->on_wrong,
                'rewind_seconds' => $vq->rewind_seconds,
                'show_every_time' => $showEveryTime,
            ];
        })->values()->all();
        $watchProgress = \App\Models\LectureWatchProgress::where('lecture_id', $lecture->id)->where('user_id', $currentUser->id)->first();
        $progressData = $watchProgress ? [
            'progress_percent' => (int) $watchProgress->progress_percent,
            'is_completed' => (bool) $watchProgress->is_completed,
            'watch_time_seconds' => (int) $watchProgress->watch_time_seconds,
            'video_duration_seconds' => (int) $watchProgress->video_duration_seconds,
        ] : null;
        return [
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'scheduled_at' => $lecture->scheduled_at ? $lecture->scheduled_at->toIso8601String() : null,
            'scheduled_at_formatted' => $lecture->scheduled_at ? $lecture->scheduled_at->format('Y/m/d H:i') : null,
            'duration_minutes' => $lecture->duration_minutes ?? 60,
            'min_watch_percent_to_unlock_next' => $lecture->min_watch_percent_to_unlock_next,
            'recording_url' => $recordingUrlFinal,
            'video_platform' => $videoPlatformFinal,
            'notes' => $lecture->notes ?? null,
            'materials' => $materials,
            'video_questions' => $videoQuestions,
            'progress' => $progressData,
        ];
    })->keyBy('id');
    
    $lecturesDataJson = json_encode(
        $lecturesData->isEmpty() ? (object) [] : $lecturesData,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
    );
?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('student.my-courses.partials.learn-page-shell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    if (typeof window.learnNavGetNextForButton !== 'function') {
        window.learnNavGetNextForButton = function () { return null; };
    }
    if (typeof window.learnNavGetPrevForButton !== 'function') {
        window.learnNavGetPrevForButton = function () { return null; };
    }
})();
</script>
<?php echo $__env->make('student.my-courses.partials.learn-alpine-premium', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
function courseFocusMode() {
    // قراءة بيانات المحاضرات من عنصر script (أدق من data attribute مع روابط طويلة)
    let lecturesData = {};
    const scriptEl = document.getElementById('learn-lectures-data');
    if (scriptEl && scriptEl.textContent) {
        try {
            lecturesData = JSON.parse(scriptEl.textContent);
        } catch (e) {
            console.error('Error parsing lectures data:', e);
        }
    }
    
    const base = {
        searchQuery: '',
        showLessons: true,
        showLectures: true,
        fontSize: 'medium',
        focusMode: false,
        collapsedSections: [],
        currentSectionDescription: '',
        sidebarOpen: false,
        sidebarClosed: false,
        selectedLesson: null,
        selectedLecture: null,
        selectedPattern: null,
        lessonContent: '',
        lectureContent: '',
        lectureMaterials: [],
        lecturesData: lecturesData,
        progressInterval: null,
        isFullscreen: false,
        showVideoPlayer: false,
        currentLessonVideoUrl: null,
        currentLessonId: null,
        currentLessonTitle: '',
        currentLessonThumbnail: '',
        currentLessonDuration: null,
        currentLessonCompleted: false,
        videoProgressPercent: 0,
        lectureProgressPercent: 0,
        videoTimeCurrent: '0:00',
        videoTimeTotal: '0:00',
        lastVideoProgressPercent: 0,
        lastVideoWatchTimeSec: 0,
        lastVideoDurationSec: 0,
        watchedSeconds: 0,
        lastReportedTime: null,
        SEEK_THRESHOLD: 2.5,
        lectureVideoEndedThisClip: false,
        lessonVideoEndedThisClip: false,
        autoAdvanceFiredForLessonId: null,
        autoAdvanceFiredForLectureId: null,
        isFlushingProgress: false,
        _autoAdvancing: false,
        _handlingVideoEnded: false,
        allowLectureAutoAdvance: true,
        lectureReplayMode: false,
        startLectureFromBeginning: false,
        isLectureCompleted(lecture) {
            if (!lecture) return false;
            const minP = (lecture.min_watch_percent_to_unlock_next != null && lecture.min_watch_percent_to_unlock_next !== '')
                ? parseInt(lecture.min_watch_percent_to_unlock_next, 10) : 90;
            const prog = lecture.progress || {};
            const pct = prog.progress_percent != null ? parseInt(prog.progress_percent, 10) : 0;
            return !!(prog.is_completed || pct >= minP);
        },
        getCurrentNavTypeId() {
            if (this.selectedLecture) return { type: 'lecture', id: this.selectedLecture };
            if (this.selectedLesson) return { type: 'lesson', id: this.selectedLesson };
            if (this.selectedPattern) return { type: 'pattern', id: this.selectedPattern };
            var active = document.querySelector('#learn-curriculum-sidebar .curriculum-item.active');
            if (active && active.dataset.itemType && active.dataset.itemId) {
                return { type: active.dataset.itemType, id: active.dataset.itemId };
            }
            return null;
        },
        hasNavPrev() {
            var cur = this.getCurrentNavTypeId();
            if (!cur || typeof window.learnNavGetPrevForButton !== 'function') return false;
            var prev = window.learnNavGetPrevForButton(cur.type, cur.id);
            return !!(prev && prev.type && prev.id != null && prev.id !== '');
        },
        hasNavNext() {
            var cur = this.getCurrentNavTypeId();
            if (!cur || typeof window.learnNavGetNextForButton !== 'function') return false;
            var next = window.learnNavGetNextForButton(cur.type, cur.id);
            if (!next || !next.type || next.id == null || next.id === '') return false;
            /* لا نعتمد data-item-locked هنا — وإلا يبقى «التالي» معطّلاً دائماً رغم انتهاء الفيديو/شروط المشاهدة */
            return true;
        },
        getCurrentLectureMinWatchPercent() {
            var id = this.selectedLecture;
            var lectures = this.lecturesData || {};
            var lec = lectures[id] || lectures[String(id)];
            if (!lec) return 90;
            var m = lec.min_watch_percent_to_unlock_next;
            return (m != null && m !== '') ? parseInt(m, 10) : 90;
        },
        navNextAllowed() {
            if (!this.hasNavNext()) return false;
            if (this.selectedLecture && this.showVideoPlayer) {
                var minP = this.getCurrentLectureMinWatchPercent();
                var pct = Number(this.lectureProgressPercent) || 0;
                return pct >= minP || !!this.lectureVideoEndedThisClip;
            }
            if (this.selectedLesson && this.showVideoPlayer) {
                var pctL = Number(this.videoProgressPercent) || 0;
                return pctL >= 90 || !!this.currentLessonCompleted || !!this.lessonVideoEndedThisClip;
            }
            return true;
        },
        goNavPrev() {
            var cur = this.getCurrentNavTypeId();
            if (!cur || typeof window.learnNavGetPrevForButton !== 'function') return;
            var prev = window.learnNavGetPrevForButton(cur.type, cur.id);
            if (!prev || !prev.type || prev.id == null) return;
            var nid = parseInt(prev.id, 10);
            if (isNaN(nid)) nid = prev.id;
            window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: prev.type, id: nid } }));
        },
        goNavNext() {
            if (!this.navNextAllowed()) return;
            var cur = this.getCurrentNavTypeId();
            if (!cur || typeof window.learnNavGetNextForButton !== 'function') return;
            var next = window.learnNavGetNextForButton(cur.type, cur.id);
            if (!next || !next.type || next.id == null) return;
            var nid = parseInt(next.id, 10);
            if (isNaN(nid)) nid = next.id;
            window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: next.type, id: nid } }));
        },
        async loadLesson(lessonId) {
            if (window._autoplayCancel) window._autoplayCancel();
            this.lessonVideoEndedThisClip = false;
            this.lectureVideoEndedThisClip = false;
            this.autoAdvanceFiredForLessonId = null;
            this.selectedLesson = lessonId;
            this.selectedLecture = null;
            this.selectedPattern = null;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
            this.currentLessonId = lessonId;
            this.lessonContent = '<div class="text-center p-8"><i class="fas fa-spinner fa-spin text-4xl text-sky-500 mb-4"></i><p class="text-gray-600">جاري تحميل الدرس...</p></div>';
            
            try {
                window.__learnLessonCache = window.__learnLessonCache || {};
                const cacheTtlMs = 50 * 60 * 1000;
                const cached = window.__learnLessonCache[lessonId];
                let lesson;
                if (cached && (Date.now() - cached.at) < cacheTtlMs) {
                    lesson = cached.data;
                } else {
                    const response = await fetch(`/api/lessons/${lessonId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.error || 'فشل تحميل الدرس');
                    }

                    lesson = await response.json();
                    window.__learnLessonCache[lessonId] = { at: Date.now(), data: lesson };
                }
                this.currentLessonTitle = lesson.title || '';
                this.currentLessonDuration = lesson.duration_minutes || null;
                const videoSrc = lesson.video_url || null;
                this.currentLessonThumbnail = (lesson.video_url && this.getYoutubeThumb(lesson.video_url)) ? this.getYoutubeThumb(lesson.video_url) : '';
                this.currentLessonCompleted = !!(lesson.progress && lesson.progress.is_completed);
                this.watchedSeconds = (lesson.progress && lesson.progress.watch_time != null) ? lesson.progress.watch_time : 0;
                this.lastReportedTime = null;
                const pct = (lesson.progress && lesson.progress.progress_percent != null) ? lesson.progress.progress_percent : 0;
                const watchSec = this.watchedSeconds;
                const durSec = (lesson.duration_minutes && lesson.duration_minutes > 0) ? lesson.duration_minutes * 60 : 0;
                this.reportVideoProgress(pct, watchSec, durSec);
                
                if (videoSrc) {
                    this.showVideoPlayer = true;
                    this.currentLessonVideoUrl = videoSrc;
                    let platform = lesson.video_platform ? String(lesson.video_platform).toLowerCase() : null;
                    if (!platform) {
                        if (videoSrc.includes('youtube.com') || videoSrc.includes('youtu.be')) platform = 'youtube';
                        else if (videoSrc.includes('vimeo.com')) platform = 'vimeo';
                        else if (videoSrc.includes('drive.google.com')) platform = 'google_drive';
                        else if (videoSrc.includes('mediadelivery.net')) platform = 'bunny';
                        else if (videoSrc.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) platform = 'direct';
                    }
                    [100, 250, 500].forEach(delay => {
                        setTimeout(() => {
                            const videoContainer = document.querySelector('#video-container');
                            if (videoContainer && videoContainer.__x) {
                                const v = videoContainer.__x.$data;
                                if (v && v.loadVideo && (v.currentLessonVideoUrl !== videoSrc || !v.currentSourceType)) {
                                    v.currentLessonVideoUrl = videoSrc;
                                    v.loadVideo(videoSrc, platform);
                                }
                            }
                        }, delay);
                    });
                    this.trackLessonProgress(lessonId);
                    this.activeLessonTitle = lesson.title || '';
                    if (typeof this.syncActivePanel === 'function') this.syncActivePanel('lesson', lessonId, lesson.title);
                    return;
                }
                
                // بناء محتوى HTML للدرس (بدون فيديو)
                let html = '<div class="lesson-viewer space-y-6 w-full">';
                
                // العنوان والوصف
                html += '<div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border-2 border-blue-200 w-full">';
                html += '<h2 class="text-3xl font-black text-gray-900 mb-4">' + this.escapeHtml(lesson.title) + '</h2>';
                if (lesson.description) {
                    html += '<p class="text-gray-700 leading-relaxed mb-4">' + this.escapeHtml(lesson.description) + '</p>';
                }
                html += '<div class="grid grid-cols-2 gap-4 text-sm">';
                if (lesson.duration_minutes) {
                    html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-clock text-sky-500"></i><span class="font-semibold">المدة:</span> ' + lesson.duration_minutes + ' دقيقة</div>';
                }
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-' + (lesson.type === 'video' ? 'video' : lesson.type === 'quiz' ? 'question-circle' : 'file-alt') + ' text-sky-500"></i><span class="font-semibold">النوع:</span> ' + (lesson.type === 'video' ? 'فيديو' : lesson.type === 'quiz' ? 'كويز' : 'مستند') + '</div>';
                html += '</div></div>';
                
                // المحتوى النصي
                if (lesson.content) {
                    html += '<div class="bg-white border-2 border-gray-200 rounded-xl p-6 w-full">';
                    html += '<div class="prose max-w-none text-gray-700 leading-relaxed">' + lesson.content + '</div>';
                    html += '</div>';
                }
                
                // المرفقات
                if (lesson.attachments && Array.isArray(lesson.attachments) && lesson.attachments.length > 0) {
                    html += '<div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 w-full">';
                    html += '<h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-paperclip text-sky-500"></i><span>المرفقات</span></h3>';
                    html += '<div class="space-y-2">';
                    lesson.attachments.forEach(attachment => {
                        const fileName = attachment.name || attachment.url || 'مرفق';
                        const fileUrl = attachment.url || attachment;
                        html += '<a href="' + this.escapeHtml(fileUrl) + '" target="_blank" class="block bg-white border-2 border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition-all hover:shadow-lg w-full"><div class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="fas fa-file text-sky-500 text-xl"></i><div><div class="font-bold text-gray-900">' + this.escapeHtml(fileName) + '</div></div></div><i class="fas fa-external-link-alt text-gray-400"></i></div></a>';
                    });
                    html += '</div></div>';
                }
                
                html += '</div>';
                this.lessonContent = html;
                
                // تحديث تقدم المشاهدة (حتى بدون فيديو)
                this.trackLessonProgress(lessonId);
                
            } catch (error) {
                console.error('Error loading lesson:', error);
                this.lessonContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="text-xl font-bold">حدث خطأ أثناء تحميل الدرس</p><p class="text-sm text-gray-600 mt-2">' + this.escapeHtml(error.message) + '</p></div>';
            }
        },
        reportVideoProgress(percent, currentSec, durationSec) {
            this.videoProgressPercent = percent;
            this.lastVideoProgressPercent = percent;
            this.lastVideoWatchTimeSec = currentSec;
            this.lastVideoDurationSec = durationSec;
            this.videoTimeCurrent = this.formatVideoTime(currentSec);
            this.videoTimeTotal = durationSec > 0 ? this.formatVideoTime(durationSec) : (this.currentLessonDuration ? this.currentLessonDuration + ' د' : '0:00');
        },
        reportVideoProgressFromPlayer(currentSec, durationSec, isPlaying) {
            const t = Number(currentSec) || 0;
            const dur = Number(durationSec) || 0;
            const playing = !!isPlaying;
            this.videoTimeCurrent = this.formatVideoTime(t);
            if (dur > 0) this.videoTimeTotal = this.formatVideoTime(dur);
            if (!Number.isFinite(dur) || dur <= 0) {
                this.lastReportedTime = t;
                return;
            }
            if (this.lastReportedTime === null) {
                this.lastReportedTime = t;
            } else if (playing) {
                const delta = t - this.lastReportedTime;
                if (delta >= 0 && delta <= this.SEEK_THRESHOLD) {
                    this.watchedSeconds = Math.min(dur, this.watchedSeconds + delta);
                }
                this.lastReportedTime = t;
            } else {
                this.lastReportedTime = t;
            }
            const pct = Math.min(100, (this.watchedSeconds / dur) * 100);
            this.lastVideoProgressPercent = pct;
            this.lastVideoWatchTimeSec = this.watchedSeconds;
            this.lastVideoDurationSec = dur;
            this.videoProgressPercent = pct;

            // درس الفيديو: بمجرد بلوغ 90% افتح التالي بعد حفظ التقدم فوراً (بدون انتظار interval/refresh)
            if (this.currentLessonId && !this.selectedLecture && this.showVideoPlayer) {
                if (pct >= 90 && this.autoAdvanceFiredForLessonId !== this.currentLessonId) {
                    this.autoAdvanceFiredForLessonId = this.currentLessonId;
                    this.autoAdvanceAfterVideoEnd('lesson', this.currentLessonId);
                }
            }
        },
        async autoAdvanceAfterVideoEnd(sourceType, sourceId) {
            if (this._autoAdvancing) return;
            this._autoAdvancing = true;
            try {
                if (sourceType === 'lesson') {
                    await this.flushLessonProgressNow(sourceId, true);
                } else if (sourceType === 'lecture') {
                    await this.flushLectureProgressNow(sourceId, true);
                    this.lectureVideoEndedThisClip = true;
                    this.lectureProgressPercent = Math.max(
                        Number(this.lectureProgressPercent) || 0,
                        this.getCurrentLectureMinWatchPercent()
                    );
                }
                if (typeof this.refreshSidebarLocks === 'function') {
                    await this.refreshSidebarLocks();
                }
                if (typeof window.openNextAfterComplete === 'function') {
                    await window.openNextAfterComplete(sourceType, sourceId, { skipFlush: true });
                } else if (typeof window.showAutoAdvanceToNext === 'function') {
                    await window.showAutoAdvanceToNext(sourceType, sourceId);
                }
            } catch (e) {
                console.warn('autoAdvanceAfterVideoEnd failed', e);
            } finally {
                setTimeout(() => { this._autoAdvancing = false; }, 2000);
            }
        },
        async flushLessonProgressNow(lessonId, forceCompleted = false) {
            try {
                if (!lessonId) return;
                if (this.isFlushingProgress) return;
                this.isFlushingProgress = true;
                const pct = Number(this.lastVideoProgressPercent || 0);
                const watchTime = Number(this.lastVideoWatchTimeSec || 0);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`<?php echo e(route('my-courses.lesson.progress', [$course, ':lessonId'])); ?>`.replace(':lessonId', lessonId), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        watch_time: watchTime,
                        completed: forceCompleted ? true : (pct >= 90),
                        progress_percent: pct
                    })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success) {
                        const wrapper = document.querySelector('.learn-page');
                        if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
                        if (wrapper && data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                        if (wrapper && data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                        if (forceCompleted || pct >= 90) this.currentLessonCompleted = true;
                        if (typeof updateProgressBar === 'function') updateProgressBar();
                        if (typeof this.refreshSidebarLocks === 'function') await this.refreshSidebarLocks();
                    }
                }
            } catch (e) {
                console.warn('flushLessonProgressNow failed', e);
            } finally {
                this.isFlushingProgress = false;
            }
        },
        async refreshSidebarLocks() {
            try {
                const res = await fetch(`<?php echo e(route('my-courses.curriculum.locks', [$course])); ?>`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json().catch(() => null);
                if (!data || !data.success || !data.locks) return;
                const locks = data.locks || {};

                Object.keys(locks).forEach((key) => {
                    const locked = String(locks[key]) === '1';
                    const parts = String(key).split(':');
                    const type = parts[0];
                    const id = parts[1];
                    const root = document.getElementById('learn-curriculum-sidebar');
                    const el = root
                        ? root.querySelector('.curriculum-item[data-item-type="' + type + '"][data-item-id="' + id + '"]')
                        : document.querySelector('.curriculum-item[data-item-type="' + type + '"][data-item-id="' + id + '"]');
                    if (el) {
                        el.dataset.itemLocked = locked ? '1' : '0';
                        el.classList.toggle('locked', locked);
                        if (!locked && typeof window.learnSidebarUnlockItem === 'function') {
                            window.learnSidebarUnlockItem(type, id);
                        }
                    }
                    const panel = document.querySelector('.learn-curriculum-panel[data-panel-type="' + type + '"][data-panel-id="' + id + '"]');
                    if (panel) {
                        panel.dataset.panelLocked = locked ? '1' : '0';
                        if (!locked) {
                            const overlay = document.querySelector('[data-panel-lock-overlay="' + type + '-' + id + '"]');
                            const body = document.querySelector('[data-panel-body="' + type + '-' + id + '"]');
                            if (overlay) overlay.remove();
                            if (body) body.hidden = false;
                        }
                    }
                });
            } catch (e) {
                console.warn('refreshSidebarLocks failed', e);
            }
        },
        async flushLectureProgressNow(lectureId, forceComplete = false) {
            try {
                if (!lectureId) return;
                let dur = Number(this.lastVideoDurationSec || 0);
                if (!Number.isFinite(dur) || dur <= 0) {
                    const lec = (this.lecturesData || {})[lectureId] || (this.lecturesData || {})[String(lectureId)];
                    const mins = lec && lec.duration_minutes ? parseInt(lec.duration_minutes, 10) : 0;
                    if (mins > 0) dur = mins * 60;
                }
                if ((!Number.isFinite(dur) || dur <= 0) && forceComplete) {
                    const watched = Math.max(
                        Number(this.lastVideoWatchTimeSec || 0),
                        Number(this.watchedSeconds || 0),
                        1
                    );
                    dur = watched;
                }
                if (!Number.isFinite(dur) || dur <= 0) return;
                let currentSec = forceComplete
                    ? dur
                    : Math.min(dur, Number(this.lastVideoWatchTimeSec || this.watchedSeconds || 0));
                if (forceComplete) currentSec = dur;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/my-courses/<?php echo e($course->id); ?>/lectures/' + lectureId + '/progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ current_sec: currentSec, duration_sec: dur })
                });
                if (res.ok) {
                    const data = await res.json().catch(() => null);
                    if (data && data.success) {
                        const wrapper = document.querySelector('.learn-page');
                        if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
                        if (wrapper && data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                        if (wrapper && data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                        if (typeof updateProgressBar === 'function') updateProgressBar();
                        if (typeof data.progress_percent === 'number') this.lectureProgressPercent = data.progress_percent;
                        if (typeof this.refreshSidebarLocks === 'function') await this.refreshSidebarLocks();
                    }
                }
            } catch (e) {
                console.warn('flushLectureProgressNow failed', e);
            }
        },
        formatVideoTime(seconds) {
            const s = Math.floor(Number(seconds) || 0);
            const m = Math.floor(s / 60);
            const h = Math.floor(m / 60);
            if (h > 0) return h + ':' + String(m % 60).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
            return m + ':' + String(s % 60).padStart(2, '0');
        },
        trackLessonProgress(lessonId) {
            if (this.progressInterval) clearInterval(this.progressInterval);
            this.progressInterval = setInterval(async () => {
                const pct = this.lastVideoProgressPercent || 0;
                const watchTime = this.lastVideoWatchTimeSec || 0;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const res = await fetch(`<?php echo e(route('my-courses.lesson.progress', [$course, ':lessonId'])); ?>`.replace(':lessonId', lessonId), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            watch_time: watchTime,
                            completed: pct >= 90,
                            progress_percent: pct
                        })
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.success && data.course_progress != null) {
                            const wrapper = document.querySelector('.learn-page');
                            if (wrapper) wrapper.dataset.courseProgress = data.course_progress;
                            if (data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                            if (data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                            if (pct >= 90) this.currentLessonCompleted = true;
                            updateProgressBar();
                        }
                    }
                } catch (e) { console.error('Error tracking progress:', e); }
            }, 15000);
        },
        trackLectureProgress(lectureId) {
            // تتبع تقدم المحاضرة (يمكن ربطه لاحقاً بـ API إن وُجد)
            if (this.progressInterval) clearInterval(this.progressInterval);
            this.progressInterval = null;
        },
        async loadLecture(lectureId, options) {
            options = options || {};
            if (typeof window.__learnActiveLecturePlayerCleanup === 'function') {
                try { window.__learnActiveLecturePlayerCleanup(); } catch (e) {}
            }
            if (window._autoplayCancel) window._autoplayCancel();
            this.lectureVideoEndedThisClip = false;
            this.lessonVideoEndedThisClip = false;
            this.autoAdvanceFiredForLectureId = null;
            this._lectureAdvanceLockKey = null;
            this.selectedLecture = lectureId;
            this.selectedLesson = null;
            this.selectedPattern = null;
            this.currentLessonId = null;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
            this.lectureMaterials = [];
            
            const lectures = this.lecturesData || {};
            const lectureIdStr = String(lectureId);
            const lectureIdNum = parseInt(lectureId);
            let lecture = lectures[lectureIdStr] || lectures[lectureIdNum] || lectures[lectureId];
            
            if (!lecture) {
                Object.keys(lectures).forEach(key => {
                    const l = lectures[key];
                    if (l && (l.id == lectureId || String(l.id) === String(lectureId))) lecture = l;
                });
            }
            
            const courseId = this.$el.closest('[data-course-id]')?.dataset?.courseId;
            const lecturesUrlTemplate = this.$el.closest('[data-lectures-url]')?.dataset?.lecturesUrl;
            if ((!lecture || !(lecture.recording_url && lecture.recording_url.trim())) && courseId && lecturesUrlTemplate) {
                try {
                    const url = lecturesUrlTemplate.replace('_LID_', lectureId);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (res.ok) {
                        const fromApi = await res.json();
                        if (fromApi && fromApi.id) {
                            lecture = fromApi;
                            if (!this.lecturesData) this.lecturesData = {};
                            this.lecturesData[lectureIdStr] = fromApi;
                            this.lecturesData[lectureIdNum] = fromApi;
                        }
                    }
                } catch (e) { console.warn('Fetch lecture data failed:', e); }
            }
            
            if (!lecture) {
                this.lectureContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="text-xl font-bold">المحاضرة غير موجودة</p><p class="text-sm mt-2">ID: ' + lectureId + '</p></div>';
                return;
            }

            if (courseId) {
                lecture = await this.ensureLectureRecordingReady(lecture, lectureId, courseId);
            }

            this.lectureMaterials = lecture.materials || [];
            const isAlreadyCompleted = this.isLectureCompleted(lecture);
            this.startLectureFromBeginning = options.autoAdvance === true || (isAlreadyCompleted && options.autoAdvance !== true);
            this.lectureReplayMode = isAlreadyCompleted && options.autoAdvance !== true;
            this.allowLectureAutoAdvance = options.autoAdvance === true || !isAlreadyCompleted;
            this.lectureProgressPercent = (lecture.progress && lecture.progress.progress_percent != null) ? lecture.progress.progress_percent : 0;
            if (this.startLectureFromBeginning) {
                this.watchedSeconds = 0;
                this.lastVideoWatchTimeSec = 0;
                this.lastVideoProgressPercent = 0;
                this.videoProgressPercent = 0;
                this.lastReportedTime = null;
                if (this.lectureReplayMode) {
                    this.lectureProgressPercent = 0;
                }
            } else {
                this.watchedSeconds = (lecture.progress && lecture.progress.watch_time_seconds != null) ? Number(lecture.progress.watch_time_seconds) : 0;
                this.lastReportedTime = null;
            }
            this.activeLessonTitle = lecture.title || '';
            if (typeof this.syncActivePanel === 'function') {
                this.syncActivePanel('lecture', lectureId, lecture.title);
            }
            
            if (lecture.recording_url && lecture.recording_url.trim() !== '') {
                this.showVideoPlayer = true;
                this.currentLessonVideoUrl = lecture.recording_url;
                let platform = (lecture.video_platform && String(lecture.video_platform).trim()) ? String(lecture.video_platform).trim().toLowerCase() : null;
                if (!platform) {
                    const u = lecture.recording_url;
                    if (u.includes('youtube.com') || u.includes('youtu.be')) platform = 'youtube';
                    else if (u.includes('vimeo.com')) platform = 'vimeo';
                    else if (u.includes('drive.google.com')) platform = 'google_drive';
                    else if (u.includes('mediadelivery.net')) platform = 'bunny';
                    else if (u.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) platform = 'direct';
                }
                const url = lecture.recording_url.trim();
                const canControl = (platform === 'youtube' || platform === 'vimeo' || platform === 'bunny');
                if (canControl && courseId) {
                    let lecturePlayerInitDone = false;
                    const playbackKey = String(lectureId) + '|' + url;
                    const initLecturePlayer = () => {
                        if (lecturePlayerInitDone) return true;
                        const container = document.getElementById('learn-video-embed');
                        if (!container || typeof window.initLectureVideoWithQuestions !== 'function') return false;
                        const replayRequested = this.startLectureFromBeginning === true;
                        const existingIframe = document.getElementById('lecture-yt-player-box')?.querySelector('iframe');
                        if (!replayRequested && this._activeLecturePlaybackKey === playbackKey && existingIframe) {
                            lecturePlayerInitDone = true;
                            return true;
                        }
                        this._activeLecturePlaybackKey = playbackKey;
                        lecturePlayerInitDone = true;
                        container.innerHTML = '';
                        window.initLectureVideoWithQuestions(container, lecture, platform, url, courseId, lectureId);
                        return true;
                    };
                    await this.$nextTick();
                    requestAnimationFrame(function() { initLecturePlayer(); });
                    setTimeout(function() {
                        var c = document.getElementById('learn-video-embed');
                        if (!lecturePlayerInitDone && c && !c.querySelector('#lecture-yt-player-box')) {
                            initLecturePlayer();
                        }
                    }, 350);
                    [700, 1200, 2000, 3500].forEach(function(ms) {
                        setTimeout(function() {
                            if (!lecturePlayerInitDone) initLecturePlayer();
                        }, ms);
                    });
                } else {
                    const embedHtml = this.buildLectureVideoEmbedHtml(url, platform);
                    const inject = () => {
                        const container = document.getElementById('learn-video-embed');
                        if (container && embedHtml) container.innerHTML = embedHtml;
                        else if (container) container.innerHTML = '<div class="flex items-center justify-center text-white h-full min-h-[200px]"><p>لا يمكن عرض الفيديو</p></div>';
                        if (container && platform === 'direct') {
                            const vid = container.querySelector('video');
                            if (vid) {
                                vid.addEventListener('ended', function onLectureDirectEnded() {
                                    window.dispatchEvent(new CustomEvent('learn-video-ended', { detail: { lectureId: lectureId } }));
                                });
                            }
                        }
                    };
                    this.$nextTick(inject);
                    setTimeout(inject, 50);
                    setTimeout(inject, 200);
                    setTimeout(inject, 500);
                }
                this.trackLectureProgress(lectureId);
                return;
            }
            
            // بناء محتوى HTML (بدون فيديو)
            let html = '<div class="lecture-viewer space-y-6 w-full">';
            
            // العنوان والوصف
            html += '<div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border-2 border-blue-200 w-full">';
            html += '<h2 class="text-3xl font-black text-gray-900 mb-4">' + this.escapeHtml(lecture.title) + '</h2>';
            if (lecture.description) {
                html += '<p class="text-gray-700 leading-relaxed mb-4">' + this.escapeHtml(lecture.description) + '</p>';
            }
            html += '<div class="grid grid-cols-2 gap-4 text-sm">';
            html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-calendar text-sky-500"></i><span class="font-semibold">التاريخ:</span> ' + (lecture.scheduled_at_formatted || '') + '</div>';
            html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-clock text-sky-500"></i><span class="font-semibold">المدة:</span> ' + (lecture.duration_minutes || 60) + ' دقيقة</div>';
            html += '</div></div>';
            
            // رسالة عدم وجود فيديو
            html += '<div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 text-center w-full">';
            html += '<i class="fas fa-video text-gray-400 text-3xl mb-3"></i>';
            html += '<p class="text-gray-600 font-semibold">لا يوجد فيديو متاح لهذه المحاضرة</p></div>';
            
            // الملاحظات
            if (lecture.notes) {
                html += '<div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 w-full">';
                html += '<h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-sticky-note text-sky-500"></i><span>ملاحظات</span></h3>';
                html += '<div class="text-gray-700 leading-relaxed whitespace-pre-wrap">' + this.escapeHtml(lecture.notes) + '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            this.lectureContent = html;
        },
        loadAssignment(assignmentId) {
            this.lectureVideoEndedThisClip = false;
            this.lessonVideoEndedThisClip = false;
            this.selectedLesson = null;
            this.selectedLecture = null;
            this.selectedPattern = null;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
            this.lectureContent = '<div class="text-center p-8"><i class="fas fa-spinner fa-spin text-4xl text-sky-500 mb-4"></i><p class="text-gray-600">جاري تحميل الواجب...</p></div>';

            fetch('/student/assignments/' + assignmentId, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('فشل تحميل الواجب');
                return r.json();
            })
            .then(data => {
                let html = '<div class="assignment-viewer space-y-6 w-full">';
                html += '<div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-6 border-2 border-amber-200 w-full">';
                html += '<h2 class="text-3xl font-black text-gray-900 mb-3">' + this.escapeHtml(data.title || 'الواجب') + '</h2>';
                if (data.description) html += '<p class="text-gray-700 leading-relaxed mb-3">' + this.escapeHtml(data.description) + '</p>';
                html += '<div class="grid grid-cols-2 gap-4 text-sm">';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-star text-amber-600"></i><span class="font-semibold">الدرجة:</span> ' + (data.max_score ?? '-') + '</div>';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-calendar text-amber-600"></i><span class="font-semibold">آخر موعد:</span> ' + (data.due_date || 'غير محدد') + '</div>';
                html += '</div></div>';

                if (data.instructions) {
                    html += '<div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 w-full">';
                    html += '<h3 class="font-bold text-blue-900 mb-2">تعليمات الواجب:</h3>';
                    html += '<p class="text-blue-800 whitespace-pre-wrap">' + this.escapeHtml(data.instructions) + '</p>';
                    html += '</div>';
                }

                if (data.submission) {
                    html += '<div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-6 w-full">';
                    html += '<h3 class="font-bold text-emerald-900 mb-2"><i class="fas fa-check-circle ml-1"></i> تم التسليم</h3>';
                    html += '<p class="text-emerald-800 text-sm">الحالة: ' + this.escapeHtml(data.submission.status || '') + '</p>';
                    if (data.submission.score !== null && data.submission.score !== undefined) {
                        html += '<p class="text-emerald-800 text-sm mt-1">الدرجة: <span class="font-bold">' + data.submission.score + '</span> / ' + (data.max_score ?? '-') + '</p>';
                    }
                    if (data.submission.feedback) {
                        html += '<div class="mt-3 p-3 bg-white border border-emerald-200 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">' + this.escapeHtml(data.submission.feedback) + '</div>';
                    }
                    html += '</div>';
                }

                html += '<div class="text-center mt-6">';
                html += '<a href="/student/assignments/' + assignmentId + '" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:scale-105">';
                html += '<i class="fas fa-upload"></i><span>فتح صفحة الواجب والتسليم</span></a>';
                html += '</div></div>';
                this.lectureContent = html;
            })
            .catch(() => {
                this.lectureContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-triangle text-4xl mb-4"></i><p class="text-xl font-bold">فشل تحميل الواجب</p></div>';
            });
        },
        loadPattern(patternId) {
            this.lectureVideoEndedThisClip = false;
            this.lessonVideoEndedThisClip = false;
            this.selectedLesson = null;
            this.selectedLecture = null;
            this.selectedPattern = patternId;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
        },
        async loadExam(examId) {
            this.lectureVideoEndedThisClip = false;
            this.lessonVideoEndedThisClip = false;
            this.selectedLesson = null;
            this.selectedLecture = null;
            this.selectedPattern = null;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
            this.lectureContent = '<div class="text-center p-8"><i class="fas fa-spinner fa-spin text-4xl text-sky-500 mb-4"></i><p class="text-gray-600">جاري تحميل الاختبار...</p></div>';

            try {
                const response = await fetch(`/student/exams/${examId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                if (!response.ok) {
                    throw new Error('فشل تحميل الاختبار');
                }

                const exam = await response.json();

                let html = '<div class="exam-viewer space-y-6 w-full">';
                html += '<div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border-2 border-indigo-200 w-full">';
                html += '<h2 class="text-3xl font-black text-gray-900 mb-4">' + this.escapeHtml(exam.title) + '</h2>';
                if (exam.description) {
                    html += '<p class="text-gray-700 leading-relaxed mb-4">' + this.escapeHtml(exam.description) + '</p>';
                }
                html += '<div class="grid grid-cols-2 gap-4 text-sm">';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-clock text-indigo-600"></i><span class="font-semibold">المدة:</span> ' + exam.duration_minutes + ' دقيقة</div>';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-star text-indigo-600"></i><span class="font-semibold">الدرجة الكلية:</span> ' + exam.total_marks + '</div>';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-indigo-600"></i><span class="font-semibold">درجة النجاح:</span> ' + exam.passing_marks + '</div>';
                html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-redo text-indigo-600"></i><span class="font-semibold">المحاولات:</span> ' + (exam.attempts_allowed == 0 ? 'غير محدود' : exam.attempts_allowed) + '</div>';
                html += '</div></div>';

                if (exam.instructions) {
                    html += '<div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 w-full">';
                    html += '<h3 class="font-bold text-blue-900 mb-2">تعليمات الاختبار:</h3>';
                    html += '<p class="text-blue-800 whitespace-pre-wrap">' + this.escapeHtml(exam.instructions) + '</p>';
                    html += '</div>';
                }

                html += '<div class="text-center mt-6 space-y-3">';
                html += '<a href="/student/exams/' + examId + '" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">';
                html += '<i class="fas fa-play"></i>';
                html += '<span>بدء الاختبار</span>';
                html += '</a>';
                html += '<div class="text-sm text-gray-600 font-medium">';
                html += '<p><i class="fas fa-info-circle text-indigo-600 ml-1"></i> سيتم فتح صفحة الاختبار في نافذة جديدة</p>';
                html += '</div>';
                html += '</div>';

                html += '</div>';
                this.lectureContent = html;

            } catch (error) {
                console.error('Error loading exam:', error);
                this.lectureContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-triangle text-4xl mb-4"></i><p class="text-xl font-bold">فشل تحميل الاختبار</p></div>';
            }
        },
        getMaterialIconClass(mat) {
            const n = (mat && mat.file_name ? mat.file_name : '').toLowerCase();
            if (/\.(xlsx|xls)$/.test(n)) return 'fa-file-excel text-emerald-600 dark:text-emerald-400';
            if (n.endsWith('.pdf')) return 'fa-file-pdf text-red-600 dark:text-red-400';
            if (/\.(docx?|doc)$/.test(n)) return 'fa-file-word text-blue-600 dark:text-blue-400';
            if (/\.(pptx?|ppt)$/.test(n)) return 'fa-file-powerpoint text-orange-600 dark:text-orange-400';
            if (/\.(zip|rar|7z)$/.test(n)) return 'fa-file-archive text-amber-600 dark:text-amber-400';
            return 'fa-file-alt text-sky-600 dark:text-sky-400';
        },
        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        bunnySignedUrlIsFresh(url) {
            try {
                const u = new URL(String(url), window.location.origin);
                const exp = parseInt(u.searchParams.get('expires') || '0', 10);
                return exp > Math.floor(Date.now() / 1000) + 120;
            } catch (e) {
                return false;
            }
        },
        async ensureLectureRecordingReady(lecture, lectureId, courseId) {
            if (!lecture) return lecture;
            const url = (lecture.recording_url || '').trim();
            if (!url) return lecture;
            const isBunny = String(lecture.video_platform || '').toLowerCase() === 'bunny' || url.includes('mediadelivery.net');
            if (!isBunny) return lecture;
            if (url.includes('token=') && this.bunnySignedUrlIsFresh(url)) return lecture;
            const lecturesUrlTemplate = this.$el.closest('[data-lectures-url]')?.dataset?.lecturesUrl;
            if (!courseId || !lecturesUrlTemplate) return lecture;
            try {
                const res = await fetch(lecturesUrlTemplate.replace('_LID_', lectureId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return lecture;
                const fromApi = await res.json();
                if (!fromApi || !fromApi.recording_url) return lecture;
                if (!this.lecturesData) this.lecturesData = {};
                const sid = String(lectureId);
                this.lecturesData[sid] = fromApi;
                this.lecturesData[parseInt(lectureId, 10)] = fromApi;
                return fromApi;
            } catch (e) {
                console.warn('ensureLectureRecordingReady failed', e);
                return lecture;
            }
        },
        /** نفس أسلوب معاينة الفيديو في بوب أب إضافة المحاضرة بالمنهج — بناء HTML الـ iframe/video حسب المنصة */
        buildLectureVideoEmbedHtml(url, platform) {
            if (!url || !platform) return '';
            const u = String(url).trim();
            let html = '';
            if (platform === 'youtube') {
                let videoId = (u.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || [])[1] || (u.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || [])[1] || (u.match(/embed\/([a-zA-Z0-9_-]{11})/) || [])[1];
                if (videoId) {
                    const origin = encodeURIComponent(window.location.origin);
                    html = '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '&autoplay=0" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                }
            } else if (platform === 'vimeo') {
                const m = u.match(/vimeo\.com\/(?:.*\/)?(\d+)/);
                if (m && m[1]) html = '<iframe src="https://player.vimeo.com/video/' + m[1] + '?title=0&byline=0&portrait=0&controls=1" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            } else if (platform === 'google_drive') {
                const m = u.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (m && m[1]) html = '<iframe src="https://drive.google.com/file/d/' + m[1] + '/preview" width="100%" height="100%" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
            } else if (platform === 'direct') {
                if (/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i.test(u)) {
                    const esc = u.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    html = '<video controls width="100%" height="100%" style="max-height: 100%; border-radius: 0.75rem;" class="w-full h-full"><source src="' + esc + '" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>';
                }
            } else if (platform === 'bunny') {
                const m = u.match(/mediadelivery\.net\/(?:play|embed)\/(\d+)\/([a-zA-Z0-9_-]+)/);
                if (m && m[1] && m[2]) {
                    const qIdx = u.indexOf('?');
                    const q = qIdx >= 0 ? u.substring(qIdx) : '';
                    const src = ('https://iframe.mediadelivery.net/embed/' + m[1] + '/' + m[2] + q).replace(/"/g, '&quot;');
                    html = '<iframe src="' + src + '" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                }
            }
            return html;
        },
        getYoutubeThumb(url) {
            if (!url) return '';
            const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            return m ? 'https://img.youtube.com/vi/' + m[1] + '/default.jpg' : '';
        },
        async markLessonComplete() {
            const lessonId = this.selectedLesson || this.currentLessonId;
            if (!lessonId || this.currentLessonCompleted) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch('/my-courses/<?php echo e($course->id); ?>/lessons/' + lessonId + '/progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ completed: true, watch_time: 0 })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success) {
                        this.currentLessonCompleted = true;
                        if (this.$el.dataset.courseProgress !== undefined && data.course_progress != null)
                            this.$el.dataset.courseProgress = data.course_progress;
                        if (data.total_items != null) this.$el.dataset.totalItems = data.total_items;
                        if (data.completed_items != null) this.$el.dataset.completedItems = data.completed_items;
                        updateProgressBar();
                    }
                }
            } catch (e) { console.error(e); }
        },
        generateVideoHtml(url, platform) {
            if (!url) return null;
            
            // YouTube
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                let videoId = null;
                const watchMatch = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
                if (watchMatch && watchMatch[1]) {
                    videoId = watchMatch[1];
                } else {
                    const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
                    if (shortMatch && shortMatch[1]) {
                        videoId = shortMatch[1];
                    }
                }
                if (videoId) {
                    const origin = encodeURIComponent(window.location.origin);
                    return '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                }
            }
            
            // Vimeo
            if (url.includes('vimeo.com')) {
                const vimeoMatch = url.match(/vimeo\.com\/(?:.*\/)?(\d+)/);
                if (vimeoMatch && vimeoMatch[1]) {
                    const videoId = vimeoMatch[1];
                    return '<iframe src="https://player.vimeo.com/video/' + videoId + '?title=0&byline=0&portrait=0&controls=1" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                }
            }
            
            // Google Drive
            if (url.includes('drive.google.com')) {
                const driveMatch = url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (driveMatch && driveMatch[1]) {
                    const fileId = driveMatch[1];
                    return '<iframe src="https://drive.google.com/file/d/' + fileId + '/preview" width="100%" height="100%" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
                }
            }
            
            // Direct video
            if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
                return '<video width="100%" height="100%" controls style="border-radius: 0.75rem;"><source src="' + this.escapeHtml(url) + '" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>';
            }
            
            // Bunny.net (Bunny Stream) - نفس صيغة صفحة المنهج
            if (url.includes('mediadelivery.net')) {
                const bunnyMatch = url.match(/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/);
                if (bunnyMatch && bunnyMatch[1] && bunnyMatch[2]) {
                    const embedUrl = url.split('?')[0];
                    const src = embedUrl.startsWith('http') ? embedUrl : ('https://' + embedUrl.replace(/^\/+/, ''));
                    return '<iframe src="' + this.escapeHtml(src) + '" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                }
            }
            
            return null;
        },
        toggleSection(section) {
            const key = String(section);
            const index = this.collapsedSections.indexOf(key);
            if (index > -1) {
                this.collapsedSections.splice(index, 1);
            } else {
                this.collapsedSections.push(key);
            }
        },
        isSectionCollapsed(section) {
            return this.collapsedSections.includes(String(section));
        },
        filterItems() {
            const query = this.searchQuery.toLowerCase();
            const items = document.querySelectorAll('.lesson-item, .lecture-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        },
        printCurriculum() {
            window.print();
        },
        toggleFocusMode() {
            this.focusMode = !this.focusMode;
            document.body.classList.toggle('learn-focus-mode', this.focusMode);
        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    this.isFullscreen = true;
                }).catch(err => {
                    console.error('Error entering fullscreen:', err);
                });
            } else {
                document.exitFullscreen().then(() => {
                    this.isFullscreen = false;
                }).catch(err => {
                    console.error('Error exiting fullscreen:', err);
                });
            }
        },
        updateProgressBar() {
            const wrapper = document.querySelector('.learn-page');
            if (!wrapper) return;
            const pct = Math.min(100, parseFloat(wrapper.dataset.courseProgress) || 0);
            document.querySelectorAll('.learn-progress-fill').forEach(el => { el.style.width = pct + '%'; });
            const total = wrapper.dataset.totalItems;
            const completed = wrapper.dataset.completedItems;
            if (total !== undefined && completed !== undefined) {
                document.querySelectorAll('.learn-progress-count').forEach(el => { el.textContent = completed; });
                document.querySelectorAll('.learn-progress-total').forEach(el => { el.textContent = total; });
                document.querySelectorAll('.learn-progress-pct').forEach(el => { el.textContent = Math.round(pct) + '%'; });
            }
        },
        isExternalVideo(url) {
            if (!url) return false;
            return url.includes('youtube.com') || 
                   url.includes('youtu.be') || 
                   url.includes('vimeo.com') ||
                   url.includes('drive.google.com') ||
                   url.includes('mediadelivery.net');
        },
        async loadProtectedVideo(lessonId, videoUrl) {
            try {
                // للفيديوهات المحلية المحمية، نستخدم المشغل المدمج مع حماية
                // الفيديو يتم بثه عبر route محمي
                this.showVideoPlayer = true;
                
                // إذا كان الفيديو محلي (ليس YouTube/Vimeo)، استخدم route محمي
                if (!this.isExternalVideo(videoUrl)) {
                    // استخدام route محمي للفيديو
                    this.currentLessonVideoUrl = `/api/video/stream/${lessonId}?token=${encodeURIComponent(this.generateSessionToken())}`;
                } else {
                    // فيديو خارجي - استخدم الرابط مباشرة
                    this.currentLessonVideoUrl = videoUrl;
                }
                
            } catch (error) {
                console.error('Error loading protected video:', error);
                this.lessonContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="text-xl font-bold">فشل في تحميل الفيديو المحمي</p><p class="text-sm text-gray-600 mt-2">' + this.escapeHtml(error.message) + '</p></div>';
            }
        },
        generateSessionToken() {
            // توليد token بسيط للجلسة (يمكن تطويره لاحقاً)
            return btoa(Date.now().toString() + Math.random().toString()).substring(0, 32);
        }
    };
    return Object.assign(base, window.__learnPremiumMixin || {});
}

// مشغل الفيديو - عرض رابط الفيديو فقط (iframe / video بالتحكم الأصلي للمنصة)
function videoPlayer() {
    return {
        currentLessonVideoUrl: null,
        watchersSetup: false,
        vimeoPlayer: null,
        bunnyPlayer: null,
        vimeoProgressInterval: null,
        bunnyProgressInterval: null,
        bunnyMessageHandler: null,
        get currentVideoUrl() {
            return this.currentLessonVideoUrl;
        },
        set currentVideoUrl(value) {
            this.currentLessonVideoUrl = value;
            if (value) this.loadVideo(value);
        },
        init() {
            this.setupParentWatcher();
            setTimeout(() => this.setupParentWatcher(), 150);
            setTimeout(() => this.setupParentWatcher(), 400);
        },
        setupParentWatcher() {
            const parent = this.$el.closest('[x-data*="courseFocusMode"]');
            if (!parent || !parent.__x) return;
            const parentData = parent.__x.$data;
            if (parentData.showVideoPlayer && parentData.currentLessonVideoUrl) {
                this.currentLessonVideoUrl = parentData.currentLessonVideoUrl;
                this.loadVideo(parentData.currentLessonVideoUrl, this.detectPlatform(parentData.currentLessonVideoUrl));
            }
            if (!this.watchersSetup) {
                parent.__x.$watch('currentLessonVideoUrl', (value) => {
                    if (value && value !== this.currentLessonVideoUrl) {
                        this.currentLessonVideoUrl = value;
                        this.loadVideo(value, this.detectPlatform(value));
                    }
                });
                parent.__x.$watch('showVideoPlayer', (value) => {
                    if (value && parentData.currentLessonVideoUrl) {
                        this.currentLessonVideoUrl = parentData.currentLessonVideoUrl;
                        this.loadVideo(parentData.currentLessonVideoUrl, this.detectPlatform(parentData.currentLessonVideoUrl));
                    }
                });
                this.watchersSetup = true;
            }
        },
        getSurface() {
            const s = this.$el && this.$el.querySelector('#video-surface');
            return s || document.querySelector('#video-container #video-surface');
        },
        detectPlatform(url) {
            if (!url) return null;
            if (url.includes('youtube.com') || url.includes('youtu.be')) return 'youtube';
            if (url.includes('vimeo.com')) return 'vimeo';
            if (url.includes('drive.google.com')) return 'google_drive';
            if (url.includes('iframe.mediadelivery.net') || url.includes('mediadelivery.net')) return 'bunny';
            if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i) || url.includes('/api/video/stream/')) return 'direct';
            return null;
        },
        getYoutubeVideoId(url) {
            const m = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || url.match(/embed\/([a-zA-Z0-9_-]{11})/);
            return m ? m[1] : null;
        },
        getVimeoVideoId(url) {
            const m = url.match(/vimeo\.com\/(?:.*\/)?(\d+)/) || url.match(/player\.vimeo\.com\/video\/(\d+)/);
            return m ? m[1] : null;
        },
        getDriveFileId(url) {
            const m = url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/) || url.match(/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/);
            return m ? m[1] : null;
        },
        getBunnyEmbedUrl(url) {
            if (!url || !url.includes('mediadelivery.net')) return null;
            const trimmed = String(url).trim();
            // نفس منطق صفحة المنهج: أي رابط يحتوي embed/libraryId/videoId
            const m = trimmed.match(/mediadelivery\.net\/(?:play|embed)\/(\d+)\/([a-zA-Z0-9_-]+)/);
            if (m && m[1] && m[2]) {
                const qIdx = trimmed.indexOf('?');
                const q = qIdx >= 0 ? trimmed.substring(qIdx) : '';
                return 'https://iframe.mediadelivery.net/embed/' + m[1] + '/' + m[2] + q;
            }
            // رابط Bunny بدون نمط embed (نادر): نعيده كما هو بعد إزالة الـ query
            if (trimmed.startsWith('http')) return trimmed;
            return 'https://' + trimmed.replace(/^\/+/, '');
        },
        loadVideo(videoUrl, platform = null) {
            if (this.ytProgressInterval) { clearInterval(this.ytProgressInterval); this.ytProgressInterval = null; }
            if (this.vimeoProgressInterval) { clearInterval(this.vimeoProgressInterval); this.vimeoProgressInterval = null; }
            if (this.bunnyProgressInterval) { clearInterval(this.bunnyProgressInterval); this.bunnyProgressInterval = null; }
            if (!videoUrl) {
                this.currentLessonVideoUrl = null;
                this._loadedPlaybackKey = null;
                return;
            }
            platform = platform || this.detectPlatform(videoUrl);
            const normalizedUrl = platform === 'bunny' ? (this.getBunnyEmbedUrl(videoUrl) || videoUrl) : videoUrl;
            const playbackKey = (platform || 'unknown') + '|' + normalizedUrl;
            const surfaceEarly = this.getSurface();
            if (this._loadedPlaybackKey === playbackKey && surfaceEarly && surfaceEarly.querySelector('iframe, video')) {
                this.currentLessonVideoUrl = videoUrl;
                return;
            }
            this._loadedPlaybackKey = playbackKey;
            this.vimeoPlayer = null;
            this.bunnyPlayer = null;
            if (this.bunnyMessageHandler) {
                try { window.removeEventListener('message', this.bunnyMessageHandler); } catch (e) {}
                this.bunnyMessageHandler = null;
            }
            this.currentLessonVideoUrl = videoUrl;
            const surface = surfaceEarly || this.getSurface();
            if (!surface) {
                this.$nextTick && this.$nextTick(() => this.loadVideo(videoUrl, platform));
                setTimeout(() => this.loadVideo(videoUrl, platform), 200);
                return;
            }
            platform = platform || this.detectPlatform(videoUrl);
            surface.innerHTML = '';

            if (platform === 'youtube') {
                const vid = this.getYoutubeVideoId(videoUrl);
                if (!vid) return;
                const iframe = document.createElement('iframe');
                iframe.id = 'yt-player-' + Date.now();
                iframe.src = 'https://www.youtube.com/embed/' + vid + '?rel=0&modestbranding=1&enablejsapi=1&origin=' + encodeURIComponent(window.location.origin);
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                iframe.allowFullscreen = true;
                surface.appendChild(iframe);
                this.setupYoutubeProgressTracking(surface, vid, iframe.id);
            } else if (platform === 'vimeo') {
                const vid = this.getVimeoVideoId(videoUrl);
                if (!vid) return;
                const iframe = document.createElement('iframe');
                iframe.src = 'https://player.vimeo.com/video/' + vid + '?title=0&byline=0&portrait=0';
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                iframe.allow = 'autoplay; fullscreen; picture-in-picture';
                iframe.allowFullscreen = true;
                surface.appendChild(iframe);
                this.setupVimeoProgressTracking(iframe);
            } else if (platform === 'direct') {
                const video = document.createElement('video');
                video.className = 'absolute inset-0 w-full h-full object-contain';
                video.controls = true;
                video.setAttribute('playsinline', '');
                const src = this.escapeHtml(videoUrl);
                video.innerHTML = '<source src="' + src + '" type="video/mp4">';
                surface.appendChild(video);
                this.attachVideoProgressTracking(video);
            } else if (platform === 'google_drive') {
                const fileId = this.getDriveFileId(videoUrl);
                if (!fileId) return;
                const iframe = document.createElement('iframe');
                iframe.src = 'https://drive.google.com/file/d/' + fileId + '/preview';
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                surface.appendChild(iframe);
            } else if (platform === 'bunny') {
                const embedUrl = this.getBunnyEmbedUrl(videoUrl);
                if (!embedUrl) return;
                const iframe = document.createElement('iframe');
                iframe.src = embedUrl;
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture');
                iframe.allowFullscreen = true;
                surface.appendChild(iframe);
                this.setupBunnyProgressTracking(iframe);
            }
        },
        setupVimeoProgressTracking(iframe) {
            const self = this;
            const init = () => {
                try {
                    if (!window.Vimeo || !window.Vimeo.Player) return false;
                    self.vimeoPlayer = new window.Vimeo.Player(iframe);
                    self.vimeoPlayer.on('timeupdate', function(data) {
                        try {
                            const ct = Number(data && data.seconds) || 0;
                            const dur = Number(data && data.duration) || 0;
                            if (dur > 0) {
                                window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: ct, durationSec: dur, isPlaying: true } }));
                            }
                        } catch (e) {}
                    });
                    self.vimeoPlayer.on('ended', function() {
                        window.dispatchEvent(new CustomEvent('learn-video-ended'));
                    });
                    // fallback poll (in case timeupdate throttled)
                    self.vimeoProgressInterval = setInterval(async function() {
                        try {
                            if (!self.vimeoPlayer) return;
                            const ct = await self.vimeoPlayer.getCurrentTime();
                            const dur = await self.vimeoPlayer.getDuration();
                            if (Number(dur) > 0) {
                                window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: ct, durationSec: dur, isPlaying: true } }));
                            }
                        } catch (e) {}
                    }, 1200);
                    return true;
                } catch (e) {
                    return false;
                }
            };
            if (window.Vimeo && window.Vimeo.Player) {
                init();
                return;
            }
            const s = document.createElement('script');
            s.src = 'https://player.vimeo.com/api/player.js';
            s.onload = function() { init(); };
            document.head.appendChild(s);
        },
        setupBunnyProgressTracking(iframe) {
            const self = this;
            // Fallback #1: listen to postMessage (works even if PlayerJS events don't fire in some embeds)
            self.bunnyMessageHandler = function(event) {
                try {
                    const origin = String(event.origin || '');
                    if (!origin.includes('mediadelivery.net')) return;
                    const data = event.data;

                    // Common formats vary; handle string/object heuristically
                    const asString = (typeof data === 'string') ? data : '';
                    const evt = (data && typeof data === 'object') ? (data.event || data.type || data.name) : null;

                    const isEnded =
                        (typeof evt === 'string' && /(^|_)(end|ended|finish|finished|complete|completed)($|_)/i.test(evt)) ||
                        (typeof asString === 'string' && /(end|ended|finish|finished|complete|completed)/i.test(asString));

                    if (isEnded) {
                        window.dispatchEvent(new CustomEvent('learn-video-ended'));
                    }
                } catch (e) {}
            };
            window.addEventListener('message', self.bunnyMessageHandler);

            const init = () => {
                try {
                    if (!window.playerjs || !window.playerjs.Player) return false;
                    self.bunnyPlayer = new window.playerjs.Player(iframe);
                    self.bunnyPlayer.on('ended', function() { window.dispatchEvent(new CustomEvent('learn-video-ended')); });
                    try { self.bunnyPlayer.on('finish', function() { window.dispatchEvent(new CustomEvent('learn-video-ended')); }); } catch (eFin) {}
                    try { self.bunnyPlayer.on('complete', function() { window.dispatchEvent(new CustomEvent('learn-video-ended')); }); } catch (eCmp) {}
                    self.bunnyProgressInterval = setInterval(function() {
                        try {
                            if (!self.bunnyPlayer) return;
                            if (typeof self.bunnyPlayer.getCurrentTime !== 'function') return;
                            self.bunnyPlayer.getCurrentTime(function(sec) {
                                try {
                                    const ct = Number(sec) || 0;
                                    if (typeof self.bunnyPlayer.getDuration === 'function') {
                                        self.bunnyPlayer.getDuration(function(dur) {
                                            const ds = Number(dur) || 0;
                                            if (ds > 0) window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: ct, durationSec: ds, isPlaying: true } }));
                                        });
                                    }
                                } catch (e) {}
                            });
                        } catch (e) {}
                    }, 1200);
                    return true;
                } catch (e) {
                    return false;
                }
            };
            if (window.playerjs && window.playerjs.Player) {
                init();
                return;
            }
            const s = document.createElement('script');
            s.src = '//assets.mediadelivery.net/playerjs/playerjs-latest.min.js';
            s.onload = function() { init(); };
            document.head.appendChild(s);
        },
        attachVideoProgressTracking(video) {
            const report = () => {
                if (!video) return;
                const ct = video.currentTime || 0, dur = video.duration;
                if (Number.isFinite(ct) && Number.isFinite(dur) && dur > 0) {
                    const isPlaying = !video.paused;
                    window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: ct, durationSec: dur, isPlaying } }));
                }
            };
            video.addEventListener('loadedmetadata', report);
            video.addEventListener('timeupdate', report);
            video.addEventListener('durationchange', report);
            video.addEventListener('play', report);
            video.addEventListener('pause', report);
            video.addEventListener('progress', () => { if (video.duration && isFinite(video.duration)) report(); });
            video.addEventListener('ended', function() { window.dispatchEvent(new CustomEvent('learn-video-ended')); });
            if (video.readyState >= 1 && video.duration && isFinite(video.duration)) report();
        },
        setupYoutubeProgressTracking(surface, vid, iframeId) {
            const self = this;
            if (self.ytProgressInterval) { clearInterval(self.ytProgressInterval); self.ytProgressInterval = null; }
            const loadYT = () => {
                if (!window.YT || !window.YT.Player) return;
                const el = document.getElementById(iframeId);
                if (!el) return;
                try {
                    const player = new window.YT.Player(iframeId, {
                        events: {
                            onStateChange: function(e) {
                                if (e.data === 1) {
                                    if (self.ytProgressInterval) clearInterval(self.ytProgressInterval);
                                    self.ytProgressInterval = setInterval(function poll() {
                                        try {
                                            const p = e.target;
                                            if (p && typeof p.getCurrentTime === 'function') {
                                                const ct = p.getCurrentTime();
                                                const dur = p.getDuration();
                                                if (typeof ct === 'number' && typeof dur === 'number' && dur > 0) {
                                                    window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: ct, durationSec: dur, isPlaying: true } }));
                                                }
                                            }
                                        } catch (err) {}
                                    }, 800);
                                }
                                if (e.data === 0 || e.data === 2) {
                                    if (self.ytProgressInterval) { clearInterval(self.ytProgressInterval); self.ytProgressInterval = null; }
                                    if (e.data === 0) {
                                        window.dispatchEvent(new CustomEvent('learn-video-ended'));
                                    }
                                }
                            }
                        }
                    });
                    self.ytPlayer = player;
                } catch (err) { console.warn('YT Player init:', err); }
            };
            if (window.YT && window.YT.Player) {
                setTimeout(loadYT, 800);
                return;
            }
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const first = document.getElementsByTagName('script')[0];
            first.parentNode.insertBefore(tag, first);
            const prevReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function() {
                if (prevReady) prevReady();
                setTimeout(loadYT, 500);
            };
        }
    };
}
</script>
<script>
(function() {
    function getYoutubeVideoId(url) {
        if (!url) return null;
        var u = String(url).trim();
        return (u.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || [])[1] || (u.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || [])[1] || (u.match(/embed\/([a-zA-Z0-9_-]{11})/) || [])[1] || null;
    }
    function getVimeoVideoId(url) {
        if (!url) return null;
        var m = String(url).trim().match(/vimeo\.com\/(?:.*\/)?(\d+)/);
        return m && m[1] ? m[1] : null;
    }
    function normalizeBunnyEmbedUrl(raw) {
        if (!raw) return raw;
        var s = String(raw).trim();
        var m = s.match(/mediadelivery\.net\/(?:play|embed)\/(\d+)\/([a-zA-Z0-9_-]+)/);
        if (!m) return s;
        var qIdx = s.indexOf('?');
        var q = qIdx >= 0 ? s.substring(qIdx) : '';
        return 'https://iframe.mediadelivery.net/embed/' + m[1] + '/' + m[2] + q;
    }
    window.initLectureVideoWithQuestions = function(container, lecture, platform, url, courseId, lectureId) {
        if (!container || !lecture) return;
        if (typeof window.__learnActiveLecturePlayerCleanup === 'function') {
            try { window.__learnActiveLecturePlayerCleanup(); } catch (e) {}
        }
        if (platform === 'bunny') url = normalizeBunnyEmbedUrl(url);
        var questions = (lecture.video_questions && lecture.video_questions.length) ? lecture.video_questions : [];
        var shownIds = new Set();
        var currentQuestion = null;
        var player = null;
        var checkInterval = null;
        var lastProgressSentAt = 0;
        var overlay = null;
        var submitBtn = null;
        var optionsEl = null;
        var textEl = null;
        var compAtInit = window.__learnPageComponent;
        var isReplaySession = !!(compAtInit && compAtInit.lectureReplayMode);
        var startFromBeginning = !!(compAtInit && compAtInit.startLectureFromBeginning);
        var allowAutoAdvance = !(compAtInit && compAtInit.allowLectureAutoAdvance === false);
        var savedDurationSec = (lecture.progress && lecture.progress.video_duration_seconds > 0) ? lecture.progress.video_duration_seconds : 0;
        var durationMinutesFromLecture = (lecture.duration_minutes && parseInt(lecture.duration_minutes, 10) > 0) ? parseInt(lecture.duration_minutes, 10) : 0;
        var fallbackDurationSec = durationMinutesFromLecture > 0 ? durationMinutesFromLecture * 60 : savedDurationSec;
        var startFromSec = 0;
        if (!startFromBeginning && !isReplaySession && lecture.progress && lecture.progress.watch_time_seconds > 0) {
            startFromSec = Math.floor(lecture.progress.watch_time_seconds);
            var durHint = savedDurationSec > 0 ? savedDurationSec : fallbackDurationSec;
            if (durHint > 0 && startFromSec >= durHint - 5) {
                startFromSec = 0;
            }
        }
        var hasOpenedNext = false;
        var hasUnlockedNextInSidebar = false;
        var minPercentToUnlock = (lecture.min_watch_percent_to_unlock_next != null && lecture.min_watch_percent_to_unlock_next !== '') ? parseInt(lecture.min_watch_percent_to_unlock_next, 10) : 90;
        var lectureLearnEndedDispatched = false;
        var bunnyMessageHandler = null;
        function cleanupLecturePlayer() {
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }
            if (bunnyMessageHandler) {
                window.removeEventListener('message', bunnyMessageHandler);
                bunnyMessageHandler = null;
            }
        }
        window.__learnActiveLecturePlayerCleanup = cleanupLecturePlayer;
        function resolveDurationSec(durationSec) {
            var d = Number(durationSec) || 0;
            if (d > 0) return d;
            if (savedDurationSec > 0) return savedDurationSec;
            if (fallbackDurationSec > 0) return fallbackDurationSec;
            return 0;
        }
        function markCurrentLectureCompletedInSidebar() {
            var root = document.getElementById('learn-curriculum-sidebar');
            if (!root) return;
            var el = root.querySelector('.curriculum-item[data-item-type="lecture"][data-item-id="' + String(lectureId) + '"]');
            if (!el) return;
            el.classList.add('completed');
            el.classList.remove('locked');
            el.dataset.itemLocked = '0';
            el.dataset.filterState = 'completed';
        }
        function handleProgressSaved(data) {
            if (!data || !data.success) return;
            var wrapper = document.querySelector('.learn-page');
            if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
            if (wrapper && data.total_items != null) wrapper.dataset.totalItems = data.total_items;
            if (wrapper && data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
            if (typeof updateProgressBar === 'function') updateProgressBar();
            if (typeof data.progress_percent === 'number') {
                window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: data.progress_percent, lectureId: lectureId } }));
            }
            if (data.is_completed || (typeof data.progress_percent === 'number' && data.progress_percent >= minPercentToUnlock)) {
                markCurrentLectureCompletedInSidebar();
            }
            var comp = window.__learnPageComponent;
            if (comp && typeof comp.refreshSidebarLocks === 'function') {
                comp.refreshSidebarLocks();
            }
            var reachedUnlockThreshold = data.is_completed || (typeof data.progress_percent === 'number' && data.progress_percent >= minPercentToUnlock);
            if (reachedUnlockThreshold) {
                unlockNextLectureInSidebar(lectureId);
            }
        }
        function unlockNextLectureInSidebar(lid) {
            if (hasUnlockedNextInSidebar) return;
            hasUnlockedNextInSidebar = true;
            var comp = window.__learnPageComponent;
            if (comp) {
                comp.lectureProgressPercent = Math.max(Number(comp.lectureProgressPercent) || 0, minPercentToUnlock);
            }
            var applyUnlock = function() {
                var nextItem = getNextItemForLectureFromPageMap(lid);
                if (nextItem && nextItem.type && nextItem.id != null && typeof window.learnSidebarUnlockItem === 'function') {
                    window.learnSidebarUnlockItem(nextItem.type, nextItem.id);
                }
            };
            if (comp && typeof comp.refreshSidebarLocks === 'function') {
                comp.refreshSidebarLocks().then(applyUnlock).catch(applyUnlock);
            } else {
                applyUnlock();
            }
        }
        function postLectureProgressServer(currentSec, durationSec, onDone) {
            var dur = resolveDurationSec(durationSec);
            var cs = Math.max(0, Number(currentSec) || 0);
            if (!dur && cs > 0) dur = cs;
            if (!dur) { if (onDone) onDone(null); return; }
            cs = Math.min(cs, dur);
            var pct = Math.min(100, Math.round((cs / dur) * 100));
            window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: pct, lectureId: lectureId } }));
            window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: cs, durationSec: dur, isPlaying: false, lectureId: lectureId } }));
            var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch('/my-courses/' + courseId + '/lectures/' + lectureId + '/progress', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ current_sec: cs, duration_sec: dur })
            }).then(function(r) { return r.json(); }).then(function(data) {
                handleProgressSaved(data);
                if (onDone) onDone(data);
            }).catch(function() { if (onDone) onDone(null); });
        }
        function forceSaveProgressAtEnd(callback) {
            var finish = function(cs, ds) {
                var dur = resolveDurationSec(ds);
                if (!dur && cs > 0) dur = cs;
                if (!dur && fallbackDurationSec > 0) { dur = fallbackDurationSec; cs = dur; }
                if (!dur) { if (callback) callback(null); return; }
                postLectureProgressServer(Math.max(cs, dur), dur, callback);
            };
            if (platform === 'youtube' && player && player.getCurrentTime) {
                finish(player.getCurrentTime(), (player.getDuration && player.getDuration()) || 0);
                return;
            }
            if (platform === 'vimeo' && player && player.getCurrentTime) {
                player.getCurrentTime().then(function(sec) {
                    if (player.getDuration) {
                        player.getDuration().then(function(d) { finish(sec || 0, d || 0); }).catch(function() { finish(sec || 0, 0); });
                    } else {
                        finish(sec || 0, 0);
                    }
                }).catch(function() { finish(fallbackDurationSec, fallbackDurationSec); });
                return;
            }
            if (platform === 'bunny' && player && player.getCurrentTime) {
                player.getCurrentTime(function(sec) {
                    if (player.getDuration) {
                        player.getDuration(function(d) { finish(sec || 0, d || 0); });
                    } else {
                        finish(sec || 0, 0);
                    }
                });
                return;
            }
            finish(fallbackDurationSec || savedDurationSec, fallbackDurationSec || savedDurationSec);
        }
        function dispatchLectureLearnVideoEnded() {
            if (lectureLearnEndedDispatched) return;
            lectureLearnEndedDispatched = true;
            window.dispatchEvent(new CustomEvent('learn-video-ended', { detail: { lectureId: lectureId } }));
        }
        function tryAdvanceLectureFromProgress(lid) {
            if (hasOpenedNext) return;
            hasOpenedNext = true;
            var comp = window.__learnPageComponent;
            if (comp) {
                comp.lectureProgressPercent = Math.max(Number(comp.lectureProgressPercent) || 0, minPercentToUnlock);
                comp.lectureVideoEndedThisClip = true;
            }
            unlockNextLectureInSidebar(lid);
            var openNext = function() {
                if (typeof window.showAutoAdvanceToNext === 'function') {
                    window.showAutoAdvanceToNext('lecture', lid);
                    return;
                }
                var nextItem = getNextItemForLectureFromPageMap(lid);
                if (!nextItem || !nextItem.type || nextItem.id == null) return;
                var raw = nextItem.id;
                var nid = parseInt(String(raw), 10);
                var openId = (String(nid) === String(raw) && !isNaN(nid)) ? nid : raw;
                window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: nextItem.type, id: openId, autoAdvance: true } }));
            };
            if (comp && typeof comp.refreshSidebarLocks === 'function') {
                comp.refreshSidebarLocks().then(openNext).catch(openNext);
            } else {
                openNext();
            }
        }
        function progressDetail(currentSec, durationSec, isPlaying) {
            return { currentSec: currentSec, durationSec: durationSec, isPlaying: !!isPlaying, lectureId: lectureId };
        }
        function getNextItemForLectureFromPageMap(lid) {
            try {
                var nextMap = document.getElementById('learn-next-item-map');
                var nextByLecture = nextMap && nextMap.textContent ? JSON.parse(nextMap.textContent) : {};
                var cand = [String(lid), lid];
                var n = parseInt(String(lid), 10);
                if (!isNaN(n)) cand.push(String(n));
                for (var ci = 0; ci < cand.length; ci++) {
                    var k = String(cand[ci]);
                    if (Object.prototype.hasOwnProperty.call(nextByLecture, k)) return nextByLecture[k];
                }
                return null;
            } catch (err) {
                console.warn('learn-open-next map', err);
                return null;
            }
        }

        function updateLectureBar(pct) {
            var elText = document.getElementById('lecture-watch-pct-text');
            var elFill = document.getElementById('lecture-watch-pct-fill');
            if (elText) elText.textContent = (Math.round((pct || 0) * 10) / 10).toFixed(1) + '%';
            if (elFill) elFill.style.width = Math.min(100, Math.max(0, pct || 0)) + '%';
        }
        var initialPct = (lecture.progress && lecture.progress.progress_percent != null) ? lecture.progress.progress_percent : 0;
        setTimeout(function() { updateLectureBar(initialPct); }, 400);

        function seekToStartPosition() {
            if (startFromSec <= 0 || !player) {
                if (startFromBeginning && player) {
                    if (platform === 'youtube' && player.seekTo) player.seekTo(0, true);
                    else if (platform === 'vimeo' && player.setCurrentTime) player.setCurrentTime(0);
                    else if (platform === 'bunny' && player.setCurrentTime) player.setCurrentTime(0);
                }
                return;
            }
            if (platform === 'youtube' && player.seekTo) {
                player.seekTo(startFromSec, true);
            } else if (platform === 'vimeo' && player.setCurrentTime) {
                player.setCurrentTime(startFromSec);
            } else if (platform === 'bunny' && player.setCurrentTime) {
                player.setCurrentTime(startFromSec);
            }
        }

        container.innerHTML = '<div id="lecture-yt-player-box" class="absolute inset-0 w-full h-full"></div>' +
            '<div id="lecture-vq-overlay" class="hidden absolute inset-0 bg-black/85 flex items-center justify-center p-4 z-20" style="direction:rtl">' +
            '<div id="lecture-vq-card" class="bg-white rounded-2xl p-6 max-w-lg w-full max-h-[90%] overflow-y-auto shadow-xl">' +
            '<div id="lecture-vq-question-view">' +
            '<h3 class="text-lg font-bold text-slate-800 mb-2">سؤال</h3>' +
            '<p id="lecture-vq-text" class="text-slate-700 mb-4"></p>' +
            '<div id="lecture-vq-options" class="space-y-2 mb-4"></div>' +
            '<button type="button" id="lecture-vq-submit" class="w-full py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">إرسال</button>' +
            '</div>' +
            '<div id="lecture-vq-feedback-view" class="hidden text-center">' +
            '<p id="lecture-vq-result-label" class="text-xl font-bold mb-2"></p>' +
            '<p id="lecture-vq-result-emoji" class="text-4xl mb-3"></p>' +
            '<p id="lecture-vq-result-message" class="text-slate-600 mb-4"></p>' +
            '<button type="button" id="lecture-vq-continue-btn" class="w-full py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">متابعة</button>' +
            '</div></div></div>';
        overlay = document.getElementById('lecture-vq-overlay');
        submitBtn = document.getElementById('lecture-vq-submit');
        optionsEl = document.getElementById('lecture-vq-options');
        textEl = document.getElementById('lecture-vq-text');
        var questionView = document.getElementById('lecture-vq-question-view');
        var feedbackView = document.getElementById('lecture-vq-feedback-view');
        var resultLabel = document.getElementById('lecture-vq-result-label');
        var resultEmoji = document.getElementById('lecture-vq-result-emoji');
        var resultMessage = document.getElementById('lecture-vq-result-message');
        var continueBtn = document.getElementById('lecture-vq-continue-btn');
        var isEnglishUi = '<?php echo e(app()->getLocale()); ?>' === 'en';
        var correctMessagesAr = [
            'عاش جدا! إجابتك صح وممتازة 🔥👏',
            'برافو عليك يا بطل، شغلك عالي اوي 💪✨',
            'صح 100%.. كمل بنفس التركيز 🧠✅',
            'إجابة ممتازة، واضح إنك مركز جدا 🎯',
            'فل الفل! أنت ماشي صح 👌',
            'تسلم إيدك، إجابة مظبوطة جدا 🌟',
            'جامد! كده أنت فاهم النقطة صح 🏆',
            'ممتاز، إجابة في الجون ⚽✅',
            'يا سلام! إجابة قوية جدا 🔥',
            'برافو، أنت متابع الفيديو باحتراف 👏',
            'إجابة تحفة.. كمل كده 🙌',
            'صح يا نجم، أداء جميل جدا ⭐'
        ];
        var wrongMessagesAr = [
            'ولا يهمك، دي بسيطة.. جرب تاني 💙',
            'قربت! ركز ثانية كمان وهتجيبها ✅',
            'مش مشكلة خالص، الغلط جزء من التعلم 🌱',
            'ارجع كام ثانية وراجع النقطة دي وهتزبط 👀',
            'لأ بس تمام، المحاولة الجاية أحسن 💪',
            'خد نفس وجرّب تاني، أنت قدها 🔥',
            'إجابة مش مظبوطة، بس أنت ماشي صح 👌',
            'قربت جدا.. محتاجة تركيز بسيط بس 🎯',
            'تمام، خلينا نحاول مرة كمان ✨',
            'مش صح المرة دي.. بس أكيد هتظبط معاك 💯',
            'ولا تزعل، راجع الجزء اللي فات بسرعة 🎬',
            'يلا بينا محاولة جديدة، وأنت قدها يا بطل 🚀'
        ];
        var correctMessagesEn = [
            'Awesome! That is exactly right 🔥👏',
            'Great job, you are really focused 💪✨',
            '100% correct. Keep it up 🧠✅',
            'Excellent answer, you nailed it 🎯',
            'Perfect! You are doing great 👌',
            'Well done, that was spot on 🌟',
            'Fantastic work, keep going 🏆',
            'Brilliant! Right on target ⚽✅',
            'Nice one! Super strong answer 🔥',
            'Great focus, you are learning fast 👏',
            'Amazing answer. Keep moving 🙌',
            'Correct, champion. Beautiful performance ⭐'
        ];
        var wrongMessagesEn = [
            'No worries, that one was close 💙',
            'Almost there. Try one more time ✅',
            'It is okay, mistakes help you learn 🌱',
            'Replay a few seconds and try again 👀',
            'Not this time, but you are improving 💪',
            'Take a breath and go again 🔥',
            'Not quite right yet, keep pushing 👌',
            'Very close. Just a little more focus 🎯',
            'Good attempt. Let us try again ✨',
            'Incorrect this time, you can do it 💯',
            'Quick review and retry 🎬',
            'One more try, you have got this 🚀'
        ];
        var labels = isEnglishUi
            ? { correct: 'Correct Answer ✓', wrong: 'Wrong Answer', pick: 'Choose an answer' }
            : { correct: 'إجابتك صح ✓', wrong: 'إجابتك غلط', pick: 'اختر إجابة' };
        var continueHandler = null;

        function showQuestion(q) {
            currentQuestion = q;
            if (questionView) questionView.classList.remove('hidden');
            if (feedbackView) feedbackView.classList.add('hidden');
            if (textEl) textEl.textContent = q.text || '';
            if (optionsEl) {
                optionsEl.innerHTML = '';
                (q.options || []).forEach(function(opt, i) {
                    var optStr = (opt != null && typeof opt === 'object') ? (opt.text || opt.label || opt.value || opt.option || String(opt)) : String(opt);
                    if (!optStr || !optStr.trim()) return;
                    var label = document.createElement('label');
                    label.className = 'flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 cursor-pointer';
                    var radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'lecture_vq_answer';
                    radio.value = optStr;
                    radio.className = 'text-sky-500';
                    label.appendChild(radio);
                    label.appendChild(document.createTextNode(optStr));
                    optionsEl.appendChild(label);
                });
            }
            if (overlay) overlay.classList.remove('hidden');
        }
        function showFeedback(correct, data) {
            if (questionView) questionView.classList.add('hidden');
            if (feedbackView) feedbackView.classList.remove('hidden');
            if (resultLabel) {
                resultLabel.textContent = correct ? labels.correct : labels.wrong;
                resultLabel.className = 'text-xl font-bold mb-2 ' + (correct ? 'text-emerald-600' : 'text-amber-600');
            }
            if (resultEmoji) resultEmoji.textContent = correct ? '🎉' : '💪';
            if (resultMessage) {
                var arr = correct
                    ? (isEnglishUi ? correctMessagesEn : correctMessagesAr)
                    : (isEnglishUi ? wrongMessagesEn : wrongMessagesAr);
                resultMessage.textContent = arr[Math.floor(Math.random() * arr.length)];
            }
            if (continueHandler && continueBtn) continueBtn.removeEventListener('click', continueHandler);
            continueHandler = function() {
                hideOverlay();
                if (submitBtn) submitBtn.disabled = false;
                // أي أسئلة أخرى تظهر في نهاية الفيديو؟
                for (var j = 0; j < questions.length; j++) {
                    var qq = questions[j];
                    if (qq.show_at_end && !shownIds.has(qq.id)) {
                        if (player && player.pauseVideo) player.pauseVideo();
                        if (player && player.pause) player.pause();
                        showQuestion(qq);
                        return;
                    }
                }
                if (data.on_wrong === 'rewind' && !data.correct && data.rewind_seconds) {
                    doRewind(data.rewind_seconds || 0);
                    return;
                }
                // انتهت كل أسئلة نهاية الفيديو → الانتقال للعنصر التالي دون انتظار إعادة تحميل الصفحة
                var hasEnd = false, allEndDone = true;
                for (var k = 0; k < questions.length; k++) {
                    if (questions[k].show_at_end) {
                        hasEnd = true;
                        if (!shownIds.has(questions[k].id)) allEndDone = false;
                    }
                }
                if (hasEnd && allEndDone) {
                    forceSaveProgressAtEnd(function() {
                        if (!allowAutoAdvance || hasOpenedNext) return;
                        tryAdvanceLectureFromProgress(lectureId);
                    });
                    return;
                }
                doContinue();
            };
            if (continueBtn) continueBtn.addEventListener('click', continueHandler);
        }
        function hideOverlay() {
            if (overlay) overlay.classList.add('hidden');
            currentQuestion = null;
        }
        function doRewind(rewindSec) {
            if (!player) return;
            if (platform === 'youtube' && player.getCurrentTime && player.seekTo && player.playVideo) {
                var t = player.getCurrentTime();
                player.seekTo(Math.max(0, t - rewindSec), true);
                player.playVideo();
                return;
            }
            if (platform === 'vimeo' && player.getCurrentTime) {
                player.getCurrentTime().then(function(sec) {
                    var t = sec || 0;
                    player.setCurrentTime(Math.max(0, t - rewindSec)).then(function() { player.play(); });
                });
                return;
            }
            if (platform === 'bunny' && player.getCurrentTime && player.setCurrentTime && player.play) {
                player.getCurrentTime(function(sec) {
                    var t = sec || 0;
                    player.setCurrentTime(Math.max(0, t - rewindSec));
                    player.play();
                });
                return;
            }
        }
        function doContinue() {
            if (player && platform === 'youtube' && player.playVideo) player.playVideo();
            if (player && platform === 'vimeo' && player.play) player.play();
            if (player && platform === 'bunny' && player.play) player.play();
        }
        function onSubmit() {
            if (!currentQuestion) return;
            var selected = document.querySelector('input[name="lecture_vq_answer"]:checked');
            var answer = selected ? selected.value : '';
            if (!answer) { alert(labels.pick); return; }
            if (submitBtn) submitBtn.disabled = true;
            var answerUrl = '/my-courses/' + courseId + '/lectures/' + lectureId + '/video-questions/' + currentQuestion.id + '/answer';
            var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch(answerUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ answer: answer })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (!(currentQuestion && currentQuestion.show_every_time)) shownIds.add(currentQuestion.id);
                showFeedback(!!data.correct, data);
            }).catch(function() {
                if (submitBtn) submitBtn.disabled = false;
                hideOverlay();
                doContinue();
            });
        }
        if (submitBtn) submitBtn.addEventListener('click', onSubmit);

        function startTimeCheck() {
            if (checkInterval) return;
            checkInterval = setInterval(function() {
                if (currentQuestion) return;
                var t = 0;
                if (platform === 'youtube' && player && player.getCurrentTime) t = player.getCurrentTime();
                else if (platform === 'vimeo' && player && player.getCurrentTime) {
                    player.getCurrentTime().then(function(sec) {
                        t = sec;
                        for (var i = 0; i < questions.length; i++) {
                            var q = questions[i];
                            if (q.show_at_end) continue;
                            if (t >= q.timestamp_seconds && !shownIds.has(q.id)) {
                                if (player.pause) player.pause();
                                showQuestion(q);
                                break;
                            }
                        }
                        var currentSec = t;
                        var durForBar = fallbackDurationSec;
                        if (player.getDuration) {
                            player.getDuration().then(function(d) {
                                durForBar = (d && d > 0) ? d : fallbackDurationSec;
                                if (durForBar > 0 && typeof currentSec === 'number' && currentSec >= 0) {
                                    window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: currentSec, durationSec: durForBar, isPlaying: true } }));
                                    window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: Math.min(100, Math.round((currentSec / durForBar) * 100)) } }));
                                    updateLectureBar(Math.min(100, Math.round((currentSec / durForBar) * 100)));
                                }
                                var now = Date.now();
                                if (!lastProgressSentAt || now - lastProgressSentAt > 5000) {
                                    lastProgressSentAt = now;
                                    var send = function(cs, ds) {
                                        postLectureProgressServer(cs, ds);
                                    };
                                    send(currentSec, durForBar);
                                }
                            });
                        } else if (durForBar > 0 && typeof currentSec === 'number' && currentSec >= 0) {
                            window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: currentSec, durationSec: durForBar, isPlaying: true } }));
                            window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: Math.min(100, Math.round((currentSec / durForBar) * 100)) } }));
                            updateLectureBar(Math.min(100, Math.round((currentSec / durForBar) * 100)));
                        }
                    });
                    return;
                } else if (platform === 'bunny' && player && player.getCurrentTime) {
                    player.getCurrentTime(function(sec) {
                        t = sec || 0;
                        for (var i = 0; i < questions.length; i++) {
                            var q = questions[i];
                            if (q.show_at_end) continue;
                            if (t >= q.timestamp_seconds && !shownIds.has(q.id)) {
                                if (player.pause) player.pause();
                                showQuestion(q);
                                break;
                            }
                        }
                        var currentSec = t;
                        var durForBar = fallbackDurationSec;
                        if (player.getDuration) {
                            player.getDuration(function(d) {
                                durForBar = (d && d > 0) ? d : fallbackDurationSec;
                                if (durForBar > 0 && typeof currentSec === 'number' && currentSec >= 0) {
                                    window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: currentSec, durationSec: durForBar, isPlaying: true } }));
                                    window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: Math.min(100, Math.round((currentSec / durForBar) * 100)) } }));
                                    updateLectureBar(Math.min(100, Math.round((currentSec / durForBar) * 100)));
                                }
                                var now = Date.now();
                                if (!lastProgressSentAt || now - lastProgressSentAt > 5000) {
                                    lastProgressSentAt = now;
                                    var send = function(cs, ds) {
                                        postLectureProgressServer(cs, ds);
                                    };
                                    send(currentSec, durForBar);
                                }
                            });
                        } else if (durForBar > 0 && typeof currentSec === 'number' && currentSec >= 0) {
                            window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: currentSec, durationSec: durForBar, isPlaying: true } }));
                            window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: Math.min(100, Math.round((currentSec / durForBar) * 100)) } }));
                            updateLectureBar(Math.min(100, Math.round((currentSec / durForBar) * 100)));
                        }
                    });
                    return;
                }
                // تحديث شريط النسبة باستمرار (كل ثانية) ثم إرسال للسيرفر كل 5 ثوانٍ — نفس آلية الدروس: video-progress-report
                if (player && typeof t === 'number' && t >= 0) {
                    var durForBar = (platform === 'youtube' && player.getDuration) ? player.getDuration() : null;
                    if (durForBar === 0 || !durForBar) durForBar = fallbackDurationSec;
                    if (durForBar > 0) {
                        var pctBar = Math.min(100, Math.round((t / durForBar) * 100));
                        window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: pctBar } }));
                        var isPlaying = (platform === 'youtube' && player.getPlayerState && player.getPlayerState() === 1);
                        window.dispatchEvent(new CustomEvent('video-progress-report', { detail: { currentSec: t, durationSec: durForBar, isPlaying: !!isPlaying } }));
                        updateLectureBar(pctBar);
                    }
                    var now = Date.now();
                    if (!lastProgressSentAt || now - lastProgressSentAt > 5000) {
                        lastProgressSentAt = now;
                        var send = function(currentSec, durationSec) {
                            postLectureProgressServer(currentSec, durationSec);
                        };
                        if (platform === 'youtube') {
                            var d = (player.getDuration && player.getDuration()) || savedDurationSec || fallbackDurationSec;
                            send(t, d);
                        } else if (platform === 'vimeo' && player.getDuration) {
                            player.getDuration().then(function(d) { send(t, d || savedDurationSec || fallbackDurationSec); });
                        } else if (platform === 'bunny' && player.getDuration) {
                            player.getDuration(function(d) { send(t, d || savedDurationSec || fallbackDurationSec); });
                        } else {
                            send(t, fallbackDurationSec || savedDurationSec);
                        }
                    }
                }
                for (var i = 0; i < questions.length; i++) {
                    var q = questions[i];
                    if (q.show_at_end) continue;
                    if (t >= q.timestamp_seconds && !shownIds.has(q.id)) {
                        if (player && player.pauseVideo) player.pauseVideo();
                        showQuestion(q);
                        break;
                    }
                }
            }, 1000);
        }

        function showEndOfVideoQuestions() {
            var comp = window.__learnPageComponent;
            if (comp && comp.selectedLecture != null && String(comp.selectedLecture) !== String(lectureId)) return;
            for (var i = 0; i < questions.length; i++) {
                var q = questions[i];
                if (q.show_at_end && !shownIds.has(q.id)) {
                    if (player && player.pauseVideo) player.pauseVideo();
                    if (player && player.pause) player.pause();
                    showQuestion(q);
                    return;
                }
            }
            forceSaveProgressAtEnd(function() {
                if (!allowAutoAdvance || hasOpenedNext) return;
                tryAdvanceLectureFromProgress(lectureId);
            });
        }

        if (platform === 'youtube') {
            var videoId = getYoutubeVideoId(url);
            if (!videoId) { container.innerHTML = '<div class="flex items-center justify-center text-white h-full"><p>رابط يوتيوب غير صالح</p></div>'; return; }
            function createYT() {
                if (player) return;
                player = new YT.Player('lecture-yt-player-box', {
                    videoId: videoId,
                    width: '100%',
                    height: '100%',
                    playerVars: { enablejsapi: 1, origin: window.location.origin, rel: 0 },
                    events: {
                        onReady: function() {
                            startTimeCheck();
                            setTimeout(seekToStartPosition, 300);
                        },
                        onStateChange: function(ev) {
                            if (ev.data === 0) showEndOfVideoQuestions();
                        }
                    }
                });
            }
            if (window.YT && window.YT.Player) {
                createYT();
            } else {
                window.onYouTubeIframeAPIReady = function() {
                    createYT();
                };
                var tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                var first = document.getElementsByTagName('script')[0];
                first.parentNode.insertBefore(tag, first);
            }
        } else if (platform === 'vimeo') {
            var vimeoId = getVimeoVideoId(url);
            if (!vimeoId) { container.innerHTML = '<div class="flex items-center justify-center text-white h-full"><p>رابط فيميوه غير صالح</p></div>'; return; }
            if (!window.Vimeo) {
                var s = document.createElement('script');
                s.src = 'https://player.vimeo.com/api/player.js';
                s.onload = function() {
                    player = new Vimeo.Player(document.getElementById('lecture-yt-player-box'), { id: parseInt(vimeoId, 10), width: '100%', height: '100%' });
                    player.on('ended', showEndOfVideoQuestions);
                    startTimeCheck();
                    setTimeout(seekToStartPosition, 500);
                };
                document.head.appendChild(s);
            } else {
                player = new Vimeo.Player(document.getElementById('lecture-yt-player-box'), { id: parseInt(vimeoId, 10), width: '100%', height: '100%' });
                player.on('ended', showEndOfVideoQuestions);
                startTimeCheck();
                setTimeout(seekToStartPosition, 500);
            }
        } else if (platform === 'bunny') {
            var root = document.getElementById('lecture-yt-player-box');
            if (!root) return;
            var iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.width = '100%';
            iframe.height = '100%';
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allowfullscreen', 'allowfullscreen');
            iframe.allow = 'autoplay; fullscreen; picture-in-picture';
            root.appendChild(iframe);

            bunnyMessageHandler = function(event) {
                try {
                    var comp = window.__learnPageComponent;
                    if (!comp || String(comp.selectedLecture) !== String(lectureId)) return;
                    var origin = String(event.origin || '');
                    if (!origin.includes('mediadelivery.net')) return;
                    var data = event.data;
                    var evt = (data && typeof data === 'object') ? (data.event || data.type || data.name) : null;
                    var isEnded = typeof evt === 'string' && /^(ended|finish|finished|complete|completed)$/i.test(evt.trim());
                    if (isEnded) showEndOfVideoQuestions();
                } catch (e) {}
            };
            window.addEventListener('message', bunnyMessageHandler);

            function createBunnyPlayer() {
                if (player) return;
                if (!window.playerjs || !window.playerjs.Player) return;
                player = new window.playerjs.Player(iframe);
                player.on('ready', function() {
                    startTimeCheck();
                    setTimeout(seekToStartPosition, 500);
                });
                player.on('ended', showEndOfVideoQuestions);
                try { player.on('finish', showEndOfVideoQuestions); } catch (eFin) {}
                try { player.on('complete', showEndOfVideoQuestions); } catch (eCmp) {}
            }
            window.addEventListener('beforeunload', function() {
                if (!player) return;
                var t = 0;
                if (platform === 'youtube' && player.getCurrentTime) t = player.getCurrentTime();
                var dur = (platform === 'youtube' && player.getDuration) ? player.getDuration() : savedDurationSec;
                if (t > 0 && dur > 0) {
                    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    var payload = JSON.stringify({ current_sec: t, duration_sec: dur, _token: csrf });
                    navigator.sendBeacon('/my-courses/' + courseId + '/lectures/' + lectureId + '/progress', new Blob([payload], { type: 'application/json' }));
                }
            });

            if (window.playerjs && window.playerjs.Player) {
                createBunnyPlayer();
            } else {
                var s = document.createElement('script');
                s.src = '//assets.mediadelivery.net/playerjs/playerjs-latest.min.js';
                s.onload = function() { createBunnyPlayer(); };
                document.head.appendChild(s);
            }
        }
    };
})();

// ===== Auto-advance to next curriculum item =====
(function() {
    var _countdownTimer = null;
    var _nextPending = null;

    function resolveNextItem(currentType, currentId) {
        var next = getNextCurriculumItem(currentType, currentId);
        if (next) return next;
        if (currentType !== 'lecture') return null;
        try {
            var mapEl = document.getElementById('learn-next-item-map');
            var map = mapEl && mapEl.textContent ? JSON.parse(mapEl.textContent) : {};
            var raw = getNextFromNavMap(map, currentId);
            if (!raw || !raw.type || raw.id == null) return null;
            return {
                type: raw.type,
                id: String(raw.id),
                el: findCurriculumItemEl(raw.type, raw.id)
            };
        } catch (e) {
            return null;
        }
    }

    async function openNextAfterComplete(currentType, currentId, opts) {
        opts = opts || {};
        var comp = window.__learnPageComponent;
        if (!opts.skipFlush && comp) {
            if (currentType === 'lecture' && typeof comp.flushLectureProgressNow === 'function') {
                try { await comp.flushLectureProgressNow(currentId, true); } catch (e) {}
            } else if (currentType === 'lesson' && typeof comp.flushLessonProgressNow === 'function') {
                try { await comp.flushLessonProgressNow(currentId, true); } catch (e) {}
            }
        }
        if (comp && typeof comp.refreshSidebarLocks === 'function') {
            try { await comp.refreshSidebarLocks(); } catch (e) {}
        }
        return showAutoAdvance(currentType, currentId);
    }

    function parseLearnNextItemMap() {
        try {
            var el = document.getElementById('learn-next-item-map');
            if (!el || !el.textContent) return {};
            return JSON.parse(el.textContent);
        } catch (e) {
            return {};
        }
    }

    function parseLearnNextLessonMap() {
        try {
            var el = document.getElementById('learn-next-lesson-map');
            if (!el || !el.textContent) return {};
            return JSON.parse(el.textContent);
        } catch (e) {
            return {};
        }
    }

    function getNextFromNavMap(map, currentId) {
        if (!map || currentId == null || currentId === '') return undefined;
        var sid = String(currentId);
        if (Object.prototype.hasOwnProperty.call(map, sid)) return map[sid];
        var n = parseInt(sid, 10);
        if (!isNaN(n) && Object.prototype.hasOwnProperty.call(map, String(n))) return map[String(n)];
        for (var k in map) {
            if (!Object.prototype.hasOwnProperty.call(map, k)) continue;
            if (String(k) === sid) return map[k];
            if (!isNaN(n) && parseInt(String(k), 10) === n) return map[k];
        }
        return undefined;
    }

    function findCurriculumItemEl(type, id) {
        if (!type || id == null || id === '') return null;
        var root = document.getElementById('learn-curriculum-sidebar');
        var sel = '.curriculum-item[data-item-type="' + String(type) + '"][data-item-id="' + String(id) + '"]';
        return root ? root.querySelector(sel) : document.querySelector(sel);
    }

    function getNextCurriculumItem(currentType, currentId) {
        // درس تالي من خريطة ترتيب دروس الكورس (الدروس غير مُدرجة في سايدبار المنهج)
        if (currentType === 'lesson' && currentId != null && currentId !== '') {
            var lmap = parseLearnNextLessonMap();
            var nextLesson = getNextFromNavMap(lmap, currentId);
            if (nextLesson !== undefined) {
                if (nextLesson === null) return null;
                if (!nextLesson.type || nextLesson.id == null) return null;
                return {
                    type: nextLesson.type,
                    id: String(nextLesson.id),
                    el: findCurriculumItemEl(nextLesson.type, nextLesson.id)
                };
            }
        }
        // ترتيب موثوق من السيرفر (نفس flattenCurriculumItems) — يمنع القفز لعناصر «عشوائية» بسبب تخطي المقفل في الـ DOM
        if (currentType === 'lecture' && currentId != null && currentId !== '') {
            var map = parseLearnNextItemMap();
            var nextFromMap = getNextFromNavMap(map, currentId);
            if (nextFromMap !== undefined) {
                if (nextFromMap === null) return null;
                if (!nextFromMap.type || nextFromMap.id == null) return null;
                return {
                    type: nextFromMap.type,
                    id: String(nextFromMap.id),
                    el: findCurriculumItemEl(nextFromMap.type, nextFromMap.id)
                };
            }
        }

        var root = document.getElementById('learn-curriculum-sidebar');
        var items = Array.from((root || document).querySelectorAll('.curriculum-item[data-item-type][data-item-id]'));
        for (var i = 0; i < items.length; i++) {
            if (items[i].dataset.itemType === currentType && String(items[i].dataset.itemId) === String(currentId)) {
                // العنصر التالي مباشرة في ترتيب المنهج (بما في ذلك المقفل) — لا نتخطى المقفل لأن ذلك كان يغيّر التسلسل
                if (i + 1 < items.length) {
                    var n = items[i + 1];
                    return { type: n.dataset.itemType, id: n.dataset.itemId, el: n };
                }
                return null;
            }
        }
        return null;
    }

    function getPrevCurriculumItem(currentType, currentId) {
        if (!currentType || currentId == null || currentId === '') return null;
        var root = document.getElementById('learn-curriculum-sidebar');
        var items = Array.from((root || document).querySelectorAll('.curriculum-item[data-item-type][data-item-id]'));
        for (var i = 0; i < items.length; i++) {
            if (items[i].dataset.itemType === currentType && String(items[i].dataset.itemId) === String(currentId)) {
                if (i > 0) {
                    var p = items[i - 1];
                    return { type: p.dataset.itemType, id: p.dataset.itemId, el: p };
                }
                return null;
            }
        }
        return null;
    }

    function learnNavIsTargetLocked(item) {
        if (!item || !item.el) return false;
        return String(item.el.dataset.itemLocked || '') === '1';
    }

    function unlockCurriculumItemEl(el) {
        if (!el) return;
        try {
            el.dataset.itemLocked = '0';
            el.classList.remove('locked');
            // لو كان يظهر أيقونة قفل داخل مربع الأيقونة، نحاول استبدالها (تحسين UX فقط)
            var iconBox = el.querySelector('.w-6.h-6');
            if (iconBox) {
                iconBox.classList.remove('bg-gray-600');
                if (!iconBox.classList.contains('bg-sky-500') && !iconBox.classList.contains('bg-green-500')) {
                    iconBox.classList.add('bg-sky-500');
                }
                var i = iconBox.querySelector('i.fas.fa-lock');
                if (i) {
                    i.classList.remove('fa-lock');
                    i.classList.add('fa-play');
                }
            }
        } catch (e) {}
    }

    window.learnNavGetNextForButton = getNextCurriculumItem;
    window.learnNavGetPrevForButton = getPrevCurriculumItem;
    window.learnNavIsTargetLocked = learnNavIsTargetLocked;
    window.learnSidebarUnlockItem = function(type, id) {
        var el = findCurriculumItemEl(type, id);
        unlockCurriculumItemEl(el);
    };

    function getItemTitle(el) {
        var titleEl = el && el.querySelector('.curriculum-item-title');
        return titleEl ? titleEl.textContent.trim() : '';
    }

    function hideOverlay() {
        var overlay = document.getElementById('autoplay-next-overlay');
        if (overlay) { overlay.classList.remove('is-visible'); overlay.style.display = 'none'; }
        if (_countdownTimer) { clearInterval(_countdownTimer); _countdownTimer = null; }
        _nextPending = null;
    }

    async function loadNextItem(item) {
        hideOverlay();
        if (!item) return;
        var comp = window.__learnPageComponent;
        if (comp && typeof comp.refreshSidebarLocks === 'function') {
            try { await comp.refreshSidebarLocks(); } catch (e) {}
        }
        if (item.el && String(item.el.dataset.itemLocked || '') === '1') {
            unlockCurriculumItemEl(item.el);
        }
        var raw = item.id;
        var nid = parseInt(String(raw), 10);
        var openId = (String(nid) === String(raw) && !isNaN(nid)) ? nid : raw;
        window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: item.type, id: openId, autoAdvance: true } }));
        var sidebar = document.getElementById('learn-curriculum-sidebar');
        if (item.el && sidebar) {
            var sbRect = sidebar.getBoundingClientRect();
            var elRect = item.el.getBoundingClientRect();
            if (elRect.top < sbRect.top || elRect.bottom > sbRect.bottom) {
                item.el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
        var videoMount = document.querySelector('.learn-video-aspect') || document.getElementById('learn-video-embed');
        if (videoMount) {
            videoMount.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    async function showAutoAdvance(currentType, currentId) {
        if (_countdownTimer) { clearInterval(_countdownTimer); _countdownTimer = null; }
        _nextPending = null;
        var overlay = document.getElementById('autoplay-next-overlay');
        if (overlay) {
            overlay.classList.remove('is-visible');
            overlay.style.display = 'none';
        }

        var comp = window.__learnPageComponent;
        if (comp && typeof comp.refreshSidebarLocks === 'function') {
            try { await comp.refreshSidebarLocks(); } catch (e) {}
        }

        var next = resolveNextItem(currentType, currentId);
        if (!next) {
            console.warn('showAutoAdvance: no next item for', currentType, currentId);
            return false;
        }
        await loadNextItem(next);
        return true;
    }

    window._autoplayNow = function() { if (_nextPending) loadNextItem(_nextPending); };
    window._autoplayCancel = function() { hideOverlay(); };
    window.showAutoAdvanceToNext = showAutoAdvance;
    window.openNextAfterComplete = openNextAfterComplete;
})();

document.addEventListener('alpine:init', function() {
    if (typeof courseFocusMode === 'function') {
        Alpine.data('courseFocusMode', courseFocusMode);
    }
    if (typeof videoPlayer === 'function') {
        Alpine.data('videoPlayer', videoPlayer);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.learn-immersive', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\learn.blade.php ENDPATH**/ ?>