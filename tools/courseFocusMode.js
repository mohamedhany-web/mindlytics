
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
                // جلب بيانات الدرس من API
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
                
                const lesson = await response.json();
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
                    let platform = null;
                    if (videoSrc.includes('youtube.com') || videoSrc.includes('youtu.be')) platform = 'youtube';
                    else if (videoSrc.includes('vimeo.com')) platform = 'vimeo';
                    else if (videoSrc.includes('drive.google.com')) platform = 'google_drive';
                    else if (videoSrc.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) platform = 'direct';
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
                    this.flushLessonProgressNow(this.currentLessonId, true).then(() => {
                        if (typeof window.showAutoAdvanceToNext === 'function') {
                            window.showAutoAdvanceToNext('lesson', this.currentLessonId);
                        }
                    });
                }
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
                const res = await fetch(`http://127.0.0.1:8000/my-courses/11/lessons/:lessonId/progress`.replace(':lessonId', lessonId), {
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
                const res = await fetch(`http://127.0.0.1:8000/my-courses/11/curriculum/locks`, {
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
                    const el = document.querySelector('.curriculum-item[data-item-type="' + type + '"][data-item-id="' + id + '"]');
                    if (el) {
                        el.dataset.itemLocked = locked ? '1' : '0';
                        el.classList.toggle('locked', locked);
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
        async flushLectureProgressNow(lectureId) {
            try {
                if (!lectureId) return;
                const dur = Number(this.lastVideoDurationSec || 0);
                if (!Number.isFinite(dur) || dur <= 0) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/my-courses/11/lectures/' + lectureId + '/progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ current_sec: dur, duration_sec: dur })
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
                    const res = await fetch(`http://127.0.0.1:8000/my-courses/11/lessons/:lessonId/progress`.replace(':lessonId', lessonId), {
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
        async loadLecture(lectureId) {
            if (window._autoplayCancel) window._autoplayCancel();
            this.lectureVideoEndedThisClip = false;
            this.lessonVideoEndedThisClip = false;
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

            this.lectureMaterials = lecture.materials || [];
            this.lectureProgressPercent = (lecture.progress && lecture.progress.progress_percent != null) ? lecture.progress.progress_percent : 0;
            this.watchedSeconds = (lecture.progress && lecture.progress.watch_time_seconds != null) ? Number(lecture.progress.watch_time_seconds) : 0;
            this.lastReportedTime = null;
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
                    const initLecturePlayer = () => {
                        if (lecturePlayerInitDone) return true;
                        const container = document.getElementById('learn-video-embed');
                        if (!container || typeof window.initLectureVideoWithQuestions !== 'function') return false;
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
                const m = u.match(/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/);
                if (m && m[1] && m[2]) {
                    const embedUrl = u.split('?')[0];
                    const src = embedUrl.startsWith('http') ? embedUrl : ('https://' + embedUrl.replace(/^\/+/, ''));
                    html = '<iframe src="' + src.replace(/"/g, '&quot;') + '" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
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
                const res = await fetch('/my-courses/11/lessons/' + lessonId + '/progress', {
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
            const m = trimmed.match(/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/);
            if (m && m[1] && m[2]) {
                // مهم: لا نحذف query string (token/expires/autoplay...) حتى لا تنكسر حماية Bunny
                if (!trimmed.startsWith('http')) return 'https://' + trimmed.replace(/^\/+/, '');
                return trimmed;
            }
            // رابط Bunny بدون نمط embed (نادر): نعيده كما هو بعد إزالة الـ query
            if (trimmed.startsWith('http')) return trimmed;
            return 'https://' + trimmed.replace(/^\/+/, '');
        },
        loadVideo(videoUrl, platform = null) {
            if (this.ytProgressInterval) { clearInterval(this.ytProgressInterval); this.ytProgressInterval = null; }
            if (this.vimeoProgressInterval) { clearInterval(this.vimeoProgressInterval); this.vimeoProgressInterval = null; }
            if (this.bunnyProgressInterval) { clearInterval(this.bunnyProgressInterval); this.bunnyProgressInterval = null; }
            this.vimeoPlayer = null;
            this.bunnyPlayer = null;
            if (this.bunnyMessageHandler) {
                try { window.removeEventListener('message', this.bunnyMessageHandler); } catch (e) {}
                this.bunnyMessageHandler = null;
            }
            if (!videoUrl) {
                this.currentLessonVideoUrl = null;
                return;
            }
            this.currentLessonVideoUrl = videoUrl;
            const surface = this.getSurface();
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
        },
        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
    return Object.assign(base, window.__learnPremiumMixin || {});
}
