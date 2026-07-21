/**
 * Learn page screen protection — isolated from the video player.
 * Blocks capture shortcuts, detects capture signals, resists extension tampering.
 */
(function () {
    'use strict';

    var LSP_TAG = '__mindlytics_lsp_v3__';

    var root = document.getElementById('learn-screen-protection-root');
    if (!root) {
        return;
    }

    var userName = (root.getAttribute('data-user-name') || 'طالب').trim();
    var brand = (root.getAttribute('data-brand') || 'Mindlytics').trim();
    var hideTimer = null;
    var overlay = null;
    var coverUntil = 0;
    var suspectUntil = 0;

    var natives = {
        getDisplayMedia: null,
        MediaRecorder: null,
    };

    function now() {
        return Date.now();
    }

    function markSuspect(ms) {
        suspectUntil = Math.max(suspectUntil, now() + (ms || 12000));
    }

    function isSuspectWindow() {
        return now() < suspectUntil;
    }

    function isPrintScreen(e) {
        var key = e.key || '';
        var code = e.code || '';
        return key === 'PrintScreen' || e.keyCode === 44 || code === 'PrintScreen';
    }

    function isMacScreenshotDigit(e) {
        var key = e.key || '';
        var code = e.code || '';
        return e.metaKey && e.shiftKey && (
            key === '3' || key === '4' || key === '5'
            || code === 'Digit3' || code === 'Digit4' || code === 'Digit5'
        );
    }

    function isWinSnip(e) {
        var key = e.key || '';
        var code = e.code || '';
        return e.shiftKey && (key === 'S' || key === 's' || code === 'KeyS')
            && typeof e.getModifierState === 'function' && e.getModifierState('OS');
    }

    function isWinGameBar(e) {
        var key = e.key || '';
        var code = e.code || '';
        return e.metaKey === false && e.ctrlKey === false && e.altKey === false
            && (key === 'g' || key === 'G' || code === 'KeyG')
            && typeof e.getModifierState === 'function' && e.getModifierState('OS');
    }

    function isCaptureShortcut(e) {
        if (isPrintScreen(e) || isMacScreenshotDigit(e) || isWinSnip(e) || isWinGameBar(e)) {
            return true;
        }
        if (e.altKey && isPrintScreen(e)) {
            return true;
        }
        if (e.ctrlKey && isPrintScreen(e)) {
            return true;
        }
        return false;
    }

    function isCaptureModifier(e) {
        return (e.metaKey && e.shiftKey) || isPrintScreen(e);
    }

    function blockEvent(e) {
        try {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        } catch (err) {
            /* ignore */
        }
        return false;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function buildOverlay() {
        if (overlay && document.body.contains(overlay)) {
            return overlay;
        }

        overlay = document.createElement('div');
        overlay.id = 'learn-screen-protection-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.setAttribute('data-lsp-shield', '1');
        overlay.innerHTML =
            '<div class="lsp-inner">' +
            '  <div class="lsp-mark" aria-hidden="true"><i class="fas fa-graduation-cap"></i></div>' +
            '  <div class="lsp-brand">' + escapeHtml(brand) + '</div>' +
            '  <div class="lsp-sub">محتوى محمي — التصوير والتسجيل غير مسموح</div>' +
            '  <div class="lsp-meta"><span class="lsp-user"></span><span class="lsp-sep">·</span><span class="lsp-time"></span></div>' +
            '</div>';

        document.body.appendChild(overlay);
        return overlay;
    }

    function stampMeta() {
        if (!overlay) {
            return;
        }
        var userEl = overlay.querySelector('.lsp-user');
        var timeEl = overlay.querySelector('.lsp-time');
        if (userEl) {
            userEl.textContent = userName;
        }
        if (timeEl) {
            try {
                timeEl.textContent = new Date().toLocaleString('ar-EG', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true,
                });
            } catch (e) {
                timeEl.textContent = new Date().toISOString();
            }
        }
    }

    function setVideoBlack(active) {
        document.querySelectorAll('.learn-video-black-guard').forEach(function (el) {
            if (active) {
                el.classList.add('is-active');
                el.style.opacity = '1';
            } else {
                el.classList.remove('is-active');
                el.style.opacity = '0';
            }
        });
    }

    function showCover(minMs) {
        var el = buildOverlay();
        stampMeta();
        coverUntil = Math.max(coverUntil, now() + (minMs || 8000));

        el.style.transition = 'none';
        el.style.opacity = '1';
        el.style.visibility = 'visible';
        el.style.pointerEvents = 'auto';
        el.classList.add('is-visible');
        document.documentElement.classList.add('learn-screen-covered', 'learn-precapture-flash');
        setVideoBlack(true);

        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }
        scheduleHide();
    }

    function scheduleHide() {
        if (hideTimer) {
            clearTimeout(hideTimer);
        }

        var wait = Math.max(0, coverUntil - now());
        hideTimer = setTimeout(function () {
            hideTimer = null;
            if (now() < coverUntil) {
                scheduleHide();
                return;
            }
            hideCover();
        }, wait || 30);
    }

    function hideCover() {
        if (overlay) {
            overlay.classList.remove('is-visible');
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
            overlay.style.pointerEvents = 'none';
        }
        document.documentElement.classList.remove('learn-screen-covered', 'learn-precapture-flash');
        setVideoBlack(false);
    }

    function onCaptureDetected(e, minMs) {
        markSuspect(minMs || 12000);
        showCover(minMs || 8000);
        if (e) {
            blockEvent(e);
        }
    }

    function onKeyboard(e) {
        if (isCaptureShortcut(e)) {
            onCaptureDetected(e, 10000);
            return blockEvent(e);
        }
        if (isCaptureModifier(e)) {
            markSuspect(12000);
        }
    }

    function onDetectionSignal(minMs) {
        if (isSuspectWindow()) {
            showCover(minMs || 8000);
        }
    }

    function ensureVideoGuards() {
        document.querySelectorAll('.learn-video-aspect').forEach(function (aspect) {
            var shield = aspect.querySelector('.learn-video-capture-shield');
            if (!shield) {
                shield = document.createElement('div');
                shield.className = 'learn-video-capture-shield';
                shield.setAttribute('aria-hidden', 'true');
                shield.innerHTML = '<div class="learn-video-black-guard"></div>';
                aspect.appendChild(shield);
            } else if (!shield.querySelector('.learn-video-black-guard')) {
                var guard = document.createElement('div');
                guard.className = 'learn-video-black-guard';
                shield.appendChild(guard);
            }
        });
    }

    function wrappedGetDisplayMedia() {
        markSuspect(15000);
        showCover(12000);
        return Promise.reject(new DOMException('Screen recording is not allowed on this page.', 'NotAllowedError'));
    }
    wrappedGetDisplayMedia[LSP_TAG] = true;

    function wrappedMediaRecorder(stream, options) {
        try {
            var hasVideo = stream && typeof stream.getVideoTracks === 'function' && stream.getVideoTracks().length > 0;
            if (hasVideo) {
                markSuspect(15000);
                showCover(12000);
                throw new DOMException('Screen recording is not allowed on this page.', 'NotAllowedError');
            }
        } catch (err) {
            if (err && err.name === 'NotAllowedError') {
                throw err;
            }
        }
        return new natives.MediaRecorder(stream, options);
    }
    wrappedMediaRecorder[LSP_TAG] = true;

    function patchMediaApis() {
        try {
            if (navigator.mediaDevices) {
                if (!natives.getDisplayMedia) {
                    natives.getDisplayMedia = navigator.mediaDevices.getDisplayMedia
                        ? navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices)
                        : null;
                }
                if (!navigator.mediaDevices.getDisplayMedia || !navigator.mediaDevices.getDisplayMedia[LSP_TAG]) {
                    navigator.mediaDevices.getDisplayMedia = wrappedGetDisplayMedia;
                }
            }
        } catch (err) {
            /* ignore */
        }

        try {
            if (typeof window.MediaRecorder !== 'undefined') {
                if (!natives.MediaRecorder) {
                    natives.MediaRecorder = window.MediaRecorder;
                }
                if (!window.MediaRecorder || !window.MediaRecorder[LSP_TAG]) {
                    window.MediaRecorder = wrappedMediaRecorder;
                    if (natives.MediaRecorder) {
                        wrappedMediaRecorder.prototype = natives.MediaRecorder.prototype;
                        try {
                            Object.setPrototypeOf(wrappedMediaRecorder, natives.MediaRecorder);
                        } catch (assignErr) {
                            /* ignore */
                        }
                    }
                }
            }
        } catch (err) {
            /* ignore */
        }
    }

    function startTamperGuard() {
        patchMediaApis();
        ensureVideoGuards();
        buildOverlay();

        if (!document.getElementById('learn-screen-protection-root')) {
            showCover(15000);
        }

        setInterval(function () {
            patchMediaApis();
            ensureVideoGuards();
            if (!document.getElementById('learn-screen-protection-overlay')) {
                buildOverlay();
            }
            if (overlay && !document.body.contains(overlay)) {
                overlay = null;
                buildOverlay();
            }
        }, 1500);

        try {
            var observer = new MutationObserver(function () {
                ensureVideoGuards();
                if (!document.getElementById('learn-screen-protection-overlay')) {
                    buildOverlay();
                }
            });
            observer.observe(document.documentElement, { childList: true, subtree: true });
        } catch (err) {
            /* ignore */
        }
    }

    document.addEventListener('keydown', onKeyboard, true);
    document.addEventListener('keyup', function (e) {
        if (isPrintScreen(e) || isMacScreenshotDigit(e)) {
            onCaptureDetected(e, 10000);
            blockEvent(e);
        }
    }, true);

    window.addEventListener('blur', function () {
        onDetectionSignal(8000);
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            onDetectionSignal(8000);
        }
    });

    window.addEventListener('pagehide', function () {
        onDetectionSignal(8000);
    });

    document.addEventListener('paste', function (e) {
        try {
            var items = e.clipboardData && e.clipboardData.items;
            if (!items) {
                return;
            }
            for (var i = 0; i < items.length; i++) {
                if (items[i].type && items[i].type.indexOf('image') !== -1) {
                    onCaptureDetected(e, 10000);
                    return;
                }
            }
        } catch (err) {
            /* ignore */
        }
    }, true);

    document.addEventListener('copy', function () {
        if (isSuspectWindow()) {
            showCover(6000);
        }
    }, true);

    window.__mindlyticsLearnProtection = {
        showCover: showCover,
        markSuspect: markSuspect,
    };

    startTamperGuard();
    root.setAttribute('data-ready', '1');
})();
