
<script>
window.__learnPremiumMixin = {
    activePanelType: null,
    activePanelId: null,
    activeLessonTitle: '',
    mobileCurriculumOpen: false,
    curriculumFilter: 'all',
    lessonNotes: {},
    streakDays: 0,
    xpPoints: 0,

    panelTabs: {},

    panelTabKey(type, id) {
        return String(type) + '-' + String(id);
    },
    getPanelTab(type, id) {
        return this.panelTabs[this.panelTabKey(type, id)] || 'overview';
    },
    setPanelTab(type, id, tab) {
        this.panelTabs[this.panelTabKey(type, id)] = tab;
    },

    syncActivePanel(type, id, title, options) {
        options = options || {};
        const panelType = String(type);
        const panelId = (/^\d+$/.test(String(id))) ? parseInt(id, 10) : id;
        this.activePanelType = panelType;
        this.activePanelId = panelId;
        document.querySelectorAll('#learn-curriculum-sidebar .curriculum-item').forEach(el => el.classList.remove('active'));
        const sideEl = document.querySelector('#learn-curriculum-sidebar .curriculum-item[data-item-type="' + panelType + '"][data-item-id="' + panelId + '"]');
        if (sideEl) sideEl.classList.add('active');
        document.querySelectorAll('.learn-curriculum-panel').forEach(el => el.classList.remove('panel-active'));
        const panel = document.getElementById('learn-panel-' + panelType + '-' + panelId);
        if (panel) {
            panel.classList.add('panel-active');
            if (!title) {
                const titleEl = panel.querySelector('.learn-panel-title');
                title = titleEl ? titleEl.textContent.trim() : '';
            }
        }
        if (title) this.activeLessonTitle = title;
        if (options.scroll === true && panel) {
            this.$nextTick(() => {
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    },

    initLearnPage() {
        const descEl = document.getElementById('learn-section-descriptions');
        if (descEl) try { window.learnSectionDescriptions = JSON.parse(descEl.textContent); } catch(e) { window.learnSectionDescriptions = {}; }
        else window.learnSectionDescriptions = {};
        try {
            const stored = localStorage.getItem('learn-notes-<?php echo e($course->id ?? 0); ?>');
            if (stored) this.lessonNotes = JSON.parse(stored) || {};
        } catch (e) { this.lessonNotes = {}; }
        this.focusMode = true;
        document.body.classList.add('learn-immersive-active');
        this.$watch('searchQuery', () => this.filterCurriculum());
        this.$watch('curriculumFilter', () => this.filterCurriculum());
        this.updateProgressBar();
        setInterval(() => this.updateProgressBar(), 100);
        document.addEventListener('fullscreenchange', () => { this.isFullscreen = !!document.fullscreenElement; });
        const _learnComp = this;
        window.addEventListener('video-progress-report', (e) => {
            const d = e.detail || {};
            if (d.lectureId != null && String(_learnComp.selectedLecture) !== String(d.lectureId)) return;
            _learnComp.reportVideoProgressFromPlayer(d.currentSec, d.durationSec, d.isPlaying);
            if (_learnComp.selectedLecture) {
                _learnComp.lectureProgressPercent = _learnComp.videoProgressPercent;
                if (Number(d.durationSec) > 0) _learnComp.lastVideoDurationSec = Number(d.durationSec);
            }
        });
        window.addEventListener('learn-lecture-progress', (e) => {
            const d = e.detail || {};
            if (d.lectureId != null && String(_learnComp.selectedLecture) !== String(d.lectureId)) return;
            if (typeof d.progress_percent === 'number') _learnComp.lectureProgressPercent = d.progress_percent;
        });
        window.addEventListener('learn-open-next-item', (e) => {
            const d = e.detail || {};
            const hasId = d.id !== undefined && d.id !== null && d.id !== '';
            if (!hasId || !d.type) return;
            const openId = (/^\d+$/.test(String(d.id))) ? parseInt(d.id, 10) : d.id;
            if (d.type === 'lecture') {
                _learnComp.loadLecture(openId, { autoAdvance: !!d.autoAdvance });
            } else if (d.type === 'lesson') {
                _learnComp.loadLesson(openId);
                if (typeof _learnComp.syncActivePanel === 'function') _learnComp.syncActivePanel('lesson', openId);
            } else if (d.type === 'exam') {
                _learnComp.loadExam(openId);
                if (typeof _learnComp.syncActivePanel === 'function') _learnComp.syncActivePanel('exam', openId);
            } else if (d.type === 'assignment') {
                _learnComp.loadAssignment(openId);
                if (typeof _learnComp.syncActivePanel === 'function') _learnComp.syncActivePanel('assignment', openId);
            } else if (d.type === 'pattern') {
                _learnComp.loadPattern(openId);
                if (typeof _learnComp.syncActivePanel === 'function') _learnComp.syncActivePanel('pattern', openId);
            }
            _learnComp.mobileCurriculumOpen = false;
        });
        window.addEventListener('learn-video-ended', (e) => {
            if (_learnComp._handlingVideoEnded) return;
            const endedLectureId = (e.detail && e.detail.lectureId) || _learnComp.selectedLecture;
            const endedLessonId = _learnComp.currentLessonId;
            if (e.detail && e.detail.lectureId && _learnComp.selectedLecture != null
                && String(e.detail.lectureId) !== String(_learnComp.selectedLecture)) {
                return;
            }
            _learnComp._handlingVideoEnded = true;
            const wasLecture = !!endedLectureId && !!(_learnComp.selectedLecture || (e.detail && e.detail.lectureId));
            const wasLesson = !!endedLessonId && !_learnComp.selectedLecture && _learnComp.showVideoPlayer;
            if (wasLecture) _learnComp.lectureVideoEndedThisClip = true;
            if (wasLesson) _learnComp.lessonVideoEndedThisClip = true;
            (async () => {
                try {
                    if (wasLecture && endedLectureId && typeof _learnComp.flushLectureProgressNow === 'function') {
                        try { await _learnComp.flushLectureProgressNow(endedLectureId, true); } catch (err) {}
                    } else if (wasLesson && endedLessonId && typeof _learnComp.flushLessonProgressNow === 'function') {
                        try { await _learnComp.flushLessonProgressNow(endedLessonId, true); } catch (err) {}
                    }
                    if (typeof window.showAutoAdvanceToNext === 'function') {
                        if (wasLesson && endedLessonId) {
                            await window.showAutoAdvanceToNext('lesson', endedLessonId);
                        } else if (wasLecture && endedLectureId
                            && String(_learnComp.selectedLecture) === String(endedLectureId)
                            && _learnComp.allowLectureAutoAdvance !== false) {
                            await window.showAutoAdvanceToNext('lecture', endedLectureId);
                        }
                    }
                } finally {
                    setTimeout(() => { _learnComp._handlingVideoEnded = false; }, 1500);
                }
            })();
        });
        window.__learnPageComponent = _learnComp;
        this.$nextTick(async () => {
            this.curriculumFilter = 'all';
            this.searchQuery = '';
            this.filterCurriculum();
            const params = new URLSearchParams(window.location.search);
            const lectureParam = params.get('lecture');
            if (lectureParam && typeof this.loadLecture === 'function') {
                await this.loadLecture(parseInt(lectureParam, 10) || lectureParam);
                return;
            }
            const first = document.querySelector('#learn-curriculum-sidebar .curriculum-item[data-item-type="lecture"]:not(.locked):not(.curriculum-item--filtered)');
            if (first && first.dataset.itemId && typeof this.loadLecture === 'function') {
                await this.loadLecture(first.dataset.itemId);
            }
        });
    },

    saveLessonNote(key, value) {
        this.lessonNotes[key] = value;
        try {
            localStorage.setItem('learn-notes-<?php echo e($course->id ?? 0); ?>', JSON.stringify(this.lessonNotes));
        } catch (e) {}
    },

    filterCurriculum() {
        const q = (this.searchQuery || '').toLowerCase().trim();
        const f = this.curriculumFilter || 'all';
        document.querySelectorAll('#learn-curriculum-sidebar .curriculum-item').forEach(el => {
            const title = (el.querySelector('.curriculum-item-title')?.textContent || '').toLowerCase();
            const matchQ = !q || title.includes(q);
            const state = el.dataset.filterState || 'unlocked';
            let matchF = true;
            if (f === 'completed') matchF = state === 'completed';
            else if (f === 'progress') matchF = state === 'progress';
            else if (f === 'locked') matchF = state === 'locked' || el.classList.contains('locked');
            else if (f === 'unlocked') matchF = state !== 'locked' && !el.classList.contains('locked');
            const visible = matchQ && matchF;
            el.classList.toggle('curriculum-item--filtered', !visible);
            el.style.removeProperty('display');
        });
    },

    async navigateToPanel(type, id, sectionId = null) {
        if (window._autoplayCancel) window._autoplayCancel();
        const panelType = String(type);
        const panelId = (/^\d+$/.test(String(id))) ? parseInt(id, 10) : id;
        if (sectionId != null) {
            this.currentSectionDescription = (window.learnSectionDescriptions || {})[String(sectionId)] || '';
        }
        const panel = document.getElementById('learn-panel-' + panelType + '-' + panelId);
        if (panel && panel.dataset.panelLocked === '1') return;
        if (panelType === 'lecture' && typeof this.loadLecture === 'function') {
            await this.loadLecture(panelId);
            return;
        }
        if (panelType === 'lesson' && typeof this.loadLesson === 'function') {
            await this.loadLesson(panelId);
            this.syncActivePanel('lesson', panelId, null, { scroll: true });
            this.mobileCurriculumOpen = false;
            return;
        }
        if (panelType === 'assignment' && typeof this.loadAssignment === 'function') {
            this.loadAssignment(panelId);
            this.syncActivePanel('assignment', panelId, null, { scroll: true });
            this.mobileCurriculumOpen = false;
            return;
        }
        if (panelType === 'exam' && typeof this.loadExam === 'function') {
            this.loadExam(panelId);
            this.syncActivePanel('exam', panelId, null, { scroll: true });
            this.mobileCurriculumOpen = false;
            return;
        }
        if (panelType === 'pattern' && typeof this.loadPattern === 'function') {
            this.loadPattern(panelId);
            this.syncActivePanel('pattern', panelId, null, { scroll: true });
            this.mobileCurriculumOpen = false;
            return;
        }
        if (panel) {
            this.syncActivePanel(panelType, panelId, null, { scroll: true });
            this.mobileCurriculumOpen = false;
        }
    },

    getCurrentNavTypeId() {
        if (this.selectedLecture) return { type: 'lecture', id: this.selectedLecture };
        if (this.selectedLesson) return { type: 'lesson', id: this.selectedLesson };
        if (this.selectedPattern) return { type: 'pattern', id: this.selectedPattern };
        if (this.activePanelType && this.activePanelId != null) {
            return { type: this.activePanelType, id: this.activePanelId };
        }
        const active = document.querySelector('#learn-curriculum-sidebar .curriculum-item.active');
        if (active && active.dataset.itemType && active.dataset.itemId) {
            return { type: active.dataset.itemType, id: active.dataset.itemId };
        }
        return null;
    },

    navNextAllowed() {
        if (!this.hasNavNext()) return false;
        if (this.selectedLecture) {
            const minP = this.getCurrentLectureMinWatchPercent();
            const pct = Number(this.lectureProgressPercent) || 0;
            return pct >= minP || !!this.lectureVideoEndedThisClip;
        }
        if (this.selectedLesson && this.showVideoPlayer) {
            const pctL = Number(this.videoProgressPercent) || 0;
            return pctL >= 90 || !!this.currentLessonCompleted || !!this.lessonVideoEndedThisClip;
        }
        return true;
    },
};
</script>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\partials\learn-alpine-premium.blade.php ENDPATH**/ ?>