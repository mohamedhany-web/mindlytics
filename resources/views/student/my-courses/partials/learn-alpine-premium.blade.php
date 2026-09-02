{{-- Panel navigation + premium UX extensions for courseFocusMode --}}
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
    _learnPageInited: false,
    _initialItemOpened: false,
    _progressBarTimer: null,
    _loadLectureLock: null,

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
        const alreadyActive = String(this.activePanelType) === panelType && String(this.activePanelId) === String(panelId);
        this.activePanelType = panelType;
        this.activePanelId = panelId;
        if (!alreadyActive) {
            document.querySelectorAll('#learn-curriculum-sidebar .curriculum-item').forEach(el => el.classList.remove('active'));
            const sideEl = document.querySelector('#learn-curriculum-sidebar .curriculum-item[data-item-type="' + panelType + '"][data-item-id="' + panelId + '"]');
            if (sideEl) sideEl.classList.add('active');
            // لا نزل panel-active ثم نعيده لنفس اللوحة — display:none→block يسبب رعشة
            document.querySelectorAll('.learn-curriculum-panel').forEach(el => el.classList.remove('panel-active'));
            const panel = document.getElementById('learn-panel-' + panelType + '-' + panelId);
            if (panel) panel.classList.add('panel-active');
        }
        const panel = document.getElementById('learn-panel-' + panelType + '-' + panelId);
        if (panel && !title) {
            const titleEl = panel.querySelector('.learn-panel-title');
            title = titleEl ? titleEl.textContent.trim() : '';
        }
        if (title) this.activeLessonTitle = title;
        if (options.scroll === true && panel) {
            this.$nextTick(() => {
                try { panel.scrollIntoView({ behavior: 'auto', block: 'nearest' }); } catch (e) {}
            });
        }
    },

    initLearnPage() {
        if (this._learnPageInited) return;
        this._learnPageInited = true;

        const descEl = document.getElementById('learn-section-descriptions');
        if (descEl) try { window.learnSectionDescriptions = JSON.parse(descEl.textContent); } catch(e) { window.learnSectionDescriptions = {}; }
        else window.learnSectionDescriptions = {};
        try {
            const stored = localStorage.getItem('learn-notes-{{ $course->id ?? 0 }}');
            if (stored) this.lessonNotes = JSON.parse(stored) || {};
        } catch (e) { this.lessonNotes = {}; }

        this.focusMode = true;
        document.body.classList.add('learn-immersive-active');
        this.$watch('searchQuery', () => this.filterCurriculum());
        this.$watch('curriculumFilter', () => this.filterCurriculum());
        this.updateProgressBar();
        if (this._progressBarTimer) clearInterval(this._progressBarTimer);
        this._progressBarTimer = setInterval(() => this.updateProgressBar(), 2500);
        document.addEventListener('fullscreenchange', () => { this.isFullscreen = !!document.fullscreenElement; });

        const _learnComp = this;

        window.addEventListener('video-progress-report', (e) => {
            const d = e.detail || {};
            if (d.lectureId != null && String(_learnComp.selectedLecture) !== String(d.lectureId)) return;
            const nowTs = Date.now();
            const isLecture = !!_learnComp.selectedLecture;
            // للمحاضرات: حدّث منطق المشاهدة كل ثانية لكن لا تدفع Alpine إلا كل ~2ث
            if (isLecture) {
                const t = Number(d.currentSec) || 0;
                const dur = Number(d.durationSec) || 0;
                if (dur > 0) {
                    if (_learnComp.lastReportedTime === null) _learnComp.lastReportedTime = t;
                    else if (d.isPlaying) {
                        const delta = t - _learnComp.lastReportedTime;
                        if (delta >= 0 && delta <= _learnComp.SEEK_THRESHOLD) {
                            _learnComp.watchedSeconds = Math.min(dur, (_learnComp.watchedSeconds || 0) + delta);
                        }
                        _learnComp.lastReportedTime = t;
                    } else {
                        _learnComp.lastReportedTime = t;
                    }
                    const pct = Math.min(100, ((_learnComp.watchedSeconds || 0) / dur) * 100);
                    _learnComp.lastVideoProgressPercent = pct;
                    _learnComp.lastVideoWatchTimeSec = _learnComp.watchedSeconds;
                    _learnComp.lastVideoDurationSec = dur;
                    if (!_learnComp._lastAlpinePctAt || (nowTs - _learnComp._lastAlpinePctAt) > 2000 || pct >= 99) {
                        _learnComp.videoProgressPercent = pct;
                        _learnComp.lectureProgressPercent = pct;
                        _learnComp._lastAlpinePctAt = nowTs;
                    }
                }
                return;
            }
            _learnComp.reportVideoProgressFromPlayer(d.currentSec, d.durationSec, d.isPlaying);
        });
        window.addEventListener('learn-lecture-progress', (e) => {
            const d = e.detail || {};
            if (d.lectureId != null && String(_learnComp.selectedLecture) !== String(d.lectureId)) return;
            if (typeof d.progress_percent !== 'number') return;
            const pct = d.progress_percent;
            const prev = Number(_learnComp.lectureProgressPercent) || 0;
            if (pct >= prev + 1 || pct >= 99 || !_learnComp._lastAlpinePctAt || (Date.now() - _learnComp._lastAlpinePctAt) > 2000) {
                _learnComp.lectureProgressPercent = pct;
                _learnComp._lastAlpinePctAt = Date.now();
            }
        });
        window.addEventListener('learn-open-next-item', (e) => {
            const d = e.detail || {};
            const hasId = d.id !== undefined && d.id !== null && d.id !== '';
            if (!hasId || !d.type) return;
            const openId = (/^\d+$/.test(String(d.id))) ? parseInt(d.id, 10) : d.id;
            // تجنّب إعادة فتح نفس العنصر (رعشة المشغّل)
            if (!d.force) {
                if (d.type === 'lecture' && String(_learnComp.selectedLecture) === String(openId)) return;
                if (d.type === 'lesson' && String(_learnComp.selectedLesson) === String(openId)) return;
            }
            if (d.type === 'lecture') {
                _learnComp.loadLecture(openId, { autoAdvance: !!d.autoAdvance, force: !!d.force });
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
            await this.openInitialCurriculumItem();
        });
    },

    async openInitialCurriculumItem() {
        if (this._initialItemOpened) return;
        this._initialItemOpened = true;
        try {
            const params = new URLSearchParams(window.location.search);
            const deepType = params.get('type');
            const deepId = params.get('id');
            const lectureParam = params.get('lecture');

            // مسار واحد فقط — يمنع فتح محاضرة ثم استبدالها (رعشة)
            if (deepType && deepId) {
                const el = document.querySelector(
                    '#learn-curriculum-sidebar .curriculum-item[data-item-type="' + deepType + '"][data-item-id="' + deepId + '"]'
                );
                if (el) {
                    const sectionId = el.getAttribute('data-section-id');
                    if (sectionId && typeof this.isSectionCollapsed === 'function' && this.isSectionCollapsed(sectionId)) {
                        this.toggleSection(sectionId);
                    }
                }
                window.dispatchEvent(new CustomEvent('learn-open-next-item', {
                    detail: { type: deepType, id: deepId, force: true }
                }));
                return;
            }
            if (lectureParam && typeof this.loadLecture === 'function') {
                await this.loadLecture(parseInt(lectureParam, 10) || lectureParam, { force: true });
                return;
            }
            const first = document.querySelector('#learn-curriculum-sidebar .curriculum-item[data-item-type="lecture"]:not(.locked):not(.curriculum-item--filtered)');
            if (first && first.dataset.itemId && typeof this.loadLecture === 'function') {
                await this.loadLecture(first.dataset.itemId, { force: true });
            }
        } catch (e) {
            console.warn('openInitialCurriculumItem failed', e);
        }
    },

    saveLessonNote(key, value) {
        this.lessonNotes[key] = value;
        try {
            localStorage.setItem('learn-notes-{{ $course->id ?? 0 }}', JSON.stringify(this.lessonNotes));
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
