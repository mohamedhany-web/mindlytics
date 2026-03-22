<?php $__env->startSection('title', $course->localized('title') . ' - ' . __('student.learn')); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startPush('meta'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    [x-cloak] {
        display: none !important;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
    
    .border-b-3 {
        border-bottom-width: 3px;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* منع التمرير الأفقي على الجوال */
    @media (max-width: 1024px) {
        body {
            overflow-x: hidden !important;
        }
        
        * {
            max-width: 100%;
        }
    }
    
    /* أزرار تنقل المنهج في السايدبار */
    .learn-sidebar-nav-btn:focus-visible {
        outline: 2px solid #0ea5e9;
        outline-offset: 2px;
    }

    /* عناصر المنهج - بطاقات مثل لوحة التحكم */
    .curriculum-item {
        background: #ffffff;
        border: 1px solid rgb(226 232 240);
        border-radius: 12px;
        padding: 0.6rem 0.75rem;
        margin-bottom: 0.4rem;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        touch-action: manipulation;
    }
    @media (max-width: 640px) {
        .curriculum-item { padding: 0.55rem 0.7rem; margin-bottom: 0.35rem; border-radius: 10px; }
    }
    .curriculum-item:hover {
        background: rgb(248 250 252);
        border-color: rgb(186 230 253);
        transform: translateX(-2px);
    }
    .curriculum-item.active {
        background: rgb(224 242 254);
        border-color: rgb(14 165 233);
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.15);
    }
    .curriculum-item.completed {
        border-color: rgb(167 243 208);
        background: rgb(236 253 245);
    }
    .curriculum-item.locked {
        opacity: 0.6;
        cursor: not-allowed;
        background: rgb(248 250 252);
    }
    .curriculum-item.locked:hover {
        transform: none;
    }
    .curriculum-section-header.section-locked {
        color: rgb(100 116 139);
    }
    .curriculum-section-header.section-locked .curriculum-section-chevron {
        opacity: 0.7;
    }
    
    /* شريط تفاصيل الدرس */
    .lesson-details-bar {
        background: #ffffff;
        border-bottom: 1px solid rgb(226 232 240);
        padding: 0.875rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem 1rem;
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .lesson-details-bar {
            padding: 0.625rem 0.75rem;
            gap: 0.5rem 0.75rem;
        }
    }
    .lesson-details-bar .lesson-thumb {
        width: 56px;
        height: 32px;
        border-radius: 8px;
        object-fit: cover;
        background: rgb(241 245 249);
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .lesson-details-bar .lesson-thumb {
            width: 48px;
            height: 28px;
        }
    }
    .lesson-details-bar .lesson-title-text {
        color: rgb(17 24 39);
        font-weight: 600;
        font-size: 0.875rem;
        flex: 1;
        min-width: 0;
    }
    @media (max-width: 640px) {
        .lesson-details-bar .lesson-title-text {
            font-size: 0.8125rem;
            order: -1;
            width: 100%;
        }
    }
    .lesson-details-bar .lesson-meta {
        color: rgb(100 116 139);
        font-size: 0.75rem;
    }
    @media (max-width: 640px) {
        .lesson-details-bar .lesson-meta {
            font-size: 0.6875rem;
        }
    }
    .btn-lesson-complete {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgb(14 165 233);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
        touch-action: manipulation;
    }
    @media (max-width: 640px) {
        .btn-lesson-complete {
            min-height: 40px;
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
        }
    }
    .btn-lesson-complete:hover {
        background: rgb(2 132 199);
    }
    .btn-lesson-complete:disabled,
    .btn-lesson-complete.completed {
        background: rgb(16 185 129);
        cursor: default;
    }
    .lesson-details-bar .btn-share {
        color: rgb(100 116 139);
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: none;
        border: none;
        cursor: pointer;
    }
    .lesson-details-bar .btn-share:hover {
        color: rgb(14 165 233);
    }
    
    .curriculum-section-header {
        color: rgb(71 85 105);
        font-weight: 600;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.5rem 0.75rem;
        background: rgb(248 250 252);
        border-radius: 10px;
        margin-bottom: 0.5rem;
        margin-top: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid rgb(226 232 240);
        cursor: pointer;
        user-select: none;
        transition: background 0.2s, border-color 0.2s;
    }
    .curriculum-section-header:hover {
        background: rgb(241 245 249);
        border-color: rgb(186 230 253);
    }
    .curriculum-section-header:first-of-type { margin-top: 0; }
    .curriculum-section-chevron {
        transition: transform 0.2s ease;
        color: rgb(100 116 139);
        font-size: 0.6rem;
    }
    .curriculum-section-header.collapsed .curriculum-section-chevron {
        transform: rotate(-90deg);
    }
    @media (max-width: 640px) {
        .curriculum-section-header {
            font-size: 0.6rem;
            padding: 0.45rem 0.65rem;
            border-radius: 8px;
        }
    }
    
    .curriculum-item-title {
        color: rgb(17 24 39);
        font-weight: 600;
        font-size: 0.8rem;
        margin-bottom: 0.15rem;
        line-height: 1.35;
        word-break: break-word;
    }
    @media (max-width: 640px) {
        .curriculum-item-title { font-size: 0.75rem; }
    }
    .curriculum-item-meta {
        color: rgb(100 116 139);
        font-size: 0.65rem;
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        line-height: 1.3;
    }
    
    @media (max-width: 640px) {
        .curriculum-item-meta {
            font-size: 0.6rem;
            gap: 0.3rem;
        }
    }
    
    .focus-main-content-wrapper {
        padding: 0;
        width: 100%;
        flex: 1;
        min-height: 0;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }
    
    .lesson-content-viewer,
    .lecture-viewer {
        width: 100%;
        flex: 1;
        min-height: 0;
        box-sizing: border-box;
        padding: 1.5rem;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
    }
    
    @media (max-width: 768px) {
        .lesson-content-viewer,
        .lecture-viewer {
            padding: 1rem;
        }
    }
    
    .lesson-content-viewer > div,
    .lecture-viewer > div {
        width: 100%;
        max-width: 100%;
    }
    
    .lesson-content-viewer::-webkit-scrollbar,
    .lecture-viewer::-webkit-scrollbar {
        width: 6px;
    }
    .lesson-content-viewer::-webkit-scrollbar-track,
    .lecture-viewer::-webkit-scrollbar-track {
        background: rgb(241 245 249);
    }
    .lesson-content-viewer::-webkit-scrollbar-thumb,
    .lecture-viewer::-webkit-scrollbar-thumb {
        background: rgb(203 213 225);
        border-radius: 3px;
    }
    
    /* الحاوية الخارجية للنمط التعليمي - توسيط البطاقة في الشاشة */
    .pattern-embed-outer {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 0;
        box-sizing: border-box;
    }
    .pattern-embed-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 0;
        width: 100%;
        max-width: 80rem;
        box-sizing: border-box;
    }
    .pattern-embed-iframe-container {
        flex: 1 1 0%;
        min-height: 0;
        min-width: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }
    .pattern-embed-iframe {
        flex: 1 1 0%;
        min-height: 0;
        width: 100%;
        max-width: 100%;
        display: block;
        box-sizing: border-box;
    }
    
    .empty-content-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        width: 100%;
        text-align: center;
        padding: 2.5rem 1.5rem;
        min-height: 400px;
        color: rgb(71 85 105);
        box-sizing: border-box;
    }
    
    @media (max-width: 640px) {
        .empty-content-state {
            padding: 2rem 1rem;
            min-height: 320px;
        }
        .empty-content-state h3 {
            font-size: 1.25rem !important;
        }
        .empty-content-state .lg\\:hidden {
            min-height: 48px;
            padding: 0.75rem 1.25rem;
        }
    }
    
    /* وضع التركيز: إخفاء سايدبار ونافبار لوحة التحكم */
    body.learn-focus-mode .student-sidebar,
    body.learn-focus-mode .student-header { display: none !important; }
    body.learn-focus-mode main .w-full.max-w-full { padding: 0 !important; }
    body.learn-focus-mode main { height: 100vh; overflow: hidden; }
    body.learn-focus-mode .learn-page { min-height: 100vh; height: 100%; display: flex; flex-direction: column; }
    body.learn-focus-mode .learn-focus-wrapper { flex: 1; display: flex; flex-direction: column; min-height: 0; }
    body.learn-focus-mode .learn-focus-wrapper .learn-focus-grid { flex: 1; min-height: 0; display: flex; flex-direction: row; gap: 0; }
    body.learn-focus-mode .learn-focus-sidebar { flex-shrink: 0; width: 280px; min-width: 240px; max-height: 100%; overflow: hidden; display: flex; flex-direction: column; }
    body.learn-focus-mode .learn-focus-sidebar .bg-white.rounded-2xl { flex: 1; display: flex; flex-direction: column; min-height: 0; max-height: none; }
    body.learn-focus-mode .learn-focus-sidebar .focus-sidebar-content { flex: 1; min-height: 0; overflow-y: auto; max-height: none; }
    body.learn-focus-mode .learn-focus-content { flex: 1; min-height: 0; min-width: 0; display: flex; flex-direction: column; }
    @media (max-width: 639px) {
        body.learn-focus-mode .learn-focus-sidebar { width: 220px; min-width: 200px; }
    }
    @media (max-width: 1023px) {
        body.learn-focus-mode .learn-focus-wrapper { padding-left: 0; padding-right: 0; }
        body.learn-focus-mode .learn-focus-content .rounded-2xl { border-radius: 0; border-right: none; border-left: none; }
    }
    
    .learn-page[data-font-size='small'] .curriculum-content {
        font-size: 0.875rem;
    }
    
    .learn-page[data-font-size='medium'] .curriculum-content {
        font-size: 1rem;
    }
    
    .learn-page[data-font-size='large'] .curriculum-content {
        font-size: 1.125rem;
    }
    
    @media print {
        .learn-page .btn-control,
        .learn-page [class*="learn-progress"] {
            display: none;
        }
        .learn-page { position: static; }
    }
    
    /* مشغل الفيديو داخل حاوية 16:9 */
    .lesson-video-viewer {
        position: relative;
        width: 100%;
        background: #000;
        display: flex;
        flex-direction: column;
    }
    
    #video-container {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #000;
    }
    
    /* منطقة الفيديو تملأ الحاوية بالكامل */
    #video-container .video-player-area,
    #video-container #video-player {
        position: relative;
        flex: 1;
        min-height: 0;
        width: 100%;
        height: 100%;
        display: block;
        overflow: hidden;
    }
    
    /* الوعاء الذي نملؤه من JS (video-surface) وعناصر الفيديو */
    #video-container .video-display-wrapper,
    #video-container #video-surface {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #video-container #yt-player-box {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    #video-container #yt-player-box iframe {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }
    
    /* iframe الفيديو يملأ الوعاء بحجم طبيعي (يوتيوب/فيميو/غيره) */
    #video-container .video-display-wrapper iframe,
    #video-container iframe {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        margin: 0 !important;
    }
    
    /* فيديو مباشر (mp4) يملأ المساحة */
    #video-container video {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain;
    }
    
    /* عنصر مشغل YouTube بعد الاستبدال (YT.Player) - يملأ المساحة بالكامل */
    #video-container [id^="youtube-player-"],
    #video-container .youtube-player-wrapper {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    /* iframe الذي ينشئه YT.Player داخل الـ div يملأ الـ div */
    #video-container [id^="youtube-player-"] iframe,
    #video-container .youtube-player-wrapper iframe {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }
    
    /* منع تحديد النص في مشغل الفيديو */
    .lesson-video-viewer * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }
    
    /* منع السحب */
    .lesson-video-viewer * {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
    }
    
    /* حماية من التصوير */
    .screenshot-protection {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        pointer-events: none;
    }
    
    .screenshot-blocker {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: black !important;
        z-index: 9999 !important;
        pointer-events: none !important;
        opacity: 0 !important;
        transition: opacity 0.1s ease !important;
    }
    
    .screenshot-blocker.active {
        opacity: 1 !important;
    }
    
    /* حماية Canvas من التصوير */
    #video-container canvas {
        image-rendering: pixelated !important;
        image-rendering: -moz-crisp-edges !important;
        image-rendering: crisp-edges !important;
    }
    
    /* إخفاء أدوات التحكم في الفيديو المدمج */
    #video-container iframe {
        pointer-events: auto !important;
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
    
    $lecturesDataJson = json_encode($lecturesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<?php $__env->startSection('content'); ?>
<script type="application/json" id="learn-lectures-data"><?php echo $lecturesDataJson; ?></script>
<script type="application/json" id="learn-next-item-map"><?php echo json_encode($nextItemByLectureId ?? []); ?></script>
<div class="learn-page bg-slate-50/80 min-h-screen pb-8"
     data-course-id="<?php echo e($course->id); ?>"
     data-course-progress="<?php echo e(min(100, (float)($progress ?? 0))); ?>"
     data-total-items="<?php echo e($totalLessons ?? 0); ?>"
     data-completed-items="<?php echo e($completedLessons ?? 0); ?>"
     data-lectures-url="<?php echo e(route('my-courses.lectures.show', [$course, '_LID_'])); ?>"
     :data-font-size="fontSize"
     x-data="courseFocusMode()"
     @keydown.escape.window="if (focusMode) { focusMode = false } else { window.location.href='<?php echo e(route('my-courses.show', $course)); ?>' }"
     @keydown.ctrl.f.window.prevent="document.querySelector('.search-box input')?.focus()"
     @keydown.ctrl.p.window.prevent="printCurriculum()"
     x-init="
         const descEl = document.getElementById('learn-section-descriptions');
         if (descEl) try { window.learnSectionDescriptions = JSON.parse(descEl.textContent); } catch(e) { window.learnSectionDescriptions = {}; }
         else window.learnSectionDescriptions = {};
         $watch('searchQuery', () => filterItems());
         $watch('focusMode', v => { document.body.classList.toggle('learn-focus-mode', !!v); });
         updateProgressBar();
         setInterval(() => updateProgressBar(), 100);
         document.addEventListener('fullscreenchange', () => { isFullscreen = !!document.fullscreenElement; });
         const _learnComp = this;
         window.addEventListener('video-progress-report', (e) => {
             const d = e.detail || {};
             _learnComp.reportVideoProgressFromPlayer(d.currentSec, d.durationSec, d.isPlaying);
             if (_learnComp.selectedLecture) _learnComp.lectureProgressPercent = _learnComp.videoProgressPercent;
         });
         window.addEventListener('learn-lecture-progress', (e) => {
             if (e.detail && typeof e.detail.progress_percent === 'number') _learnComp.lectureProgressPercent = e.detail.progress_percent;
         });
         window.addEventListener('learn-open-next-item', (e) => {
             var d = e.detail || {};
             if (d.type === 'lecture' && d.id) _learnComp.loadLecture(d.id);
             else if (d.type === 'lesson' && d.id) _learnComp.loadLesson(d.id);
             else if (d.type === 'exam' && d.id) _learnComp.loadExam(d.id);
             else if (d.type === 'assignment' && d.id) _learnComp.loadAssignment(d.id);
             else if (d.type === 'pattern' && d.id) _learnComp.loadPattern(d.id);
         });
         window.addEventListener('learn-video-ended', () => {
             if (_learnComp.selectedLecture && _learnComp.showVideoPlayer) {
                 _learnComp.lectureVideoEndedThisClip = true;
             }
             if (_learnComp.currentLessonId && !_learnComp.selectedLecture && _learnComp.showVideoPlayer) {
                 _learnComp.lessonVideoEndedThisClip = true;
             }
             if (typeof window.showAutoAdvanceToNext !== 'function') return;
             // درس (فيديو الدرس): currentLessonId يُحدَّد عند loadLesson
             if (_learnComp.currentLessonId && !_learnComp.selectedLecture) {
                 window.showAutoAdvanceToNext('lesson', _learnComp.currentLessonId);
                 return;
             }
             // محاضرة (فيديو المنهج): بعد loadLecture يكون currentLessonId = null وselectedLecture = id المحاضرة
             if (_learnComp.selectedLecture) {
                 window.showAutoAdvanceToNext('lecture', _learnComp.selectedLecture);
             }
         });
     ">
    
    <div id="autoplay-next-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.78);align-items:flex-end;justify-content:center;padding-bottom:80px;" dir="rtl">
        <div style="background:#1e293b;border:1px solid #334155;border-radius:16px;padding:20px 28px;min-width:320px;max-width:480px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,0.5);text-align:center;">
            <div style="font-size:13px;color:#94a3b8;margin-bottom:6px;">الفيديو التالي</div>
            <div id="autoplay-next-title" style="font-size:16px;font-weight:700;color:#f1f5f9;margin-bottom:14px;line-height:1.4;"></div>
            <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:16px;">
                <svg style="width:36px;height:36px;transform:rotate(-90deg)">
                    <circle id="autoplay-ring-bg" cx="18" cy="18" r="14" fill="none" stroke="#334155" stroke-width="3"/>
                    <circle id="autoplay-ring" cx="18" cy="18" r="14" fill="none" stroke="#38bdf8" stroke-width="3" stroke-dasharray="88" stroke-dashoffset="0" style="transition:stroke-dashoffset 1s linear;"/>
                </svg>
                <span id="autoplay-countdown-num" style="font-size:26px;font-weight:800;color:#38bdf8;min-width:28px;text-align:center;">5</span>
                <span style="font-size:13px;color:#94a3b8;">ثانية</span>
            </div>
            <div style="display:flex;gap:10px;justify-content:center;">
                <button id="autoplay-btn-now" onclick="window._autoplayNow && window._autoplayNow()" style="flex:1;padding:9px 16px;background:#0ea5e9;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">
                    <i class="fas fa-forward" style="margin-left:5px;"></i> تشغيل الآن
                </button>
                <button id="autoplay-btn-cancel" onclick="window._autoplayCancel && window._autoplayCancel()" style="flex:1;padding:9px 16px;background:#334155;color:#cbd5e1;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">
                    <i class="fas fa-times" style="margin-left:5px;"></i> إلغاء
                </button>
            </div>
        </div>
    </div>

    
    <nav x-show="!focusMode" class="bg-white border-b border-slate-200 px-4 py-2 lg:px-6" aria-label="Breadcrumb">
        <ol class="w-full flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('auth.dashboard')); ?></a></li>
            <li class="flex items-center gap-2"><i class="fas fa-chevron-left text-slate-400 text-xs"></i></li>
            <li><a href="<?php echo e(route('my-courses.index')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('student.my_courses')); ?></a></li>
            <li class="flex items-center gap-2"><i class="fas fa-chevron-left text-slate-400 text-xs"></i></li>
            <li><a href="<?php echo e(route('my-courses.show', $course)); ?>" class="hover:text-sky-600 transition-colors truncate max-w-[180px]"><?php echo e($course->localized('title')); ?></a></li>
            <li class="flex items-center gap-2"><i class="fas fa-chevron-left text-slate-400 text-xs"></i></li>
            <li class="text-sky-600 font-medium"><?php echo e(__('student.learn')); ?></li>
        </ol>
    </nav>

    
    <div x-show="!focusMode" class="w-full px-4 py-4 lg:px-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 lg:p-5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <a href="<?php echo e(route('my-courses.show', $course)); ?>" 
                       class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 border border-slate-200 hover:border-sky-300 transition-all"
                       title="<?php echo e(__('common.back')); ?>">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-lg lg:text-xl font-bold text-gray-900 truncate"><?php echo e($course->localized('title')); ?></h1>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <div class="h-2 flex-1 max-w-[140px] bg-slate-200 rounded-full overflow-hidden">
                                <div class="learn-progress-fill h-full bg-gradient-to-l from-sky-400 to-sky-500 rounded-full transition-all duration-500" style="width: <?php echo e(min(100, (float)($progress ?? 0))); ?>%"></div>
                            </div>
                            <span class="learn-progress-count text-xs font-semibold text-slate-600 whitespace-nowrap"><?php echo e($completedLessons ?? 0); ?>/<?php echo e($totalLessons ?? 0); ?></span>
                            <span class="learn-progress-pct text-xs font-bold text-sky-600"><?php echo e(number_format((float)($progress ?? 0), 0)); ?>%</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="toggleFocusMode()" :class="focusMode ? 'bg-sky-100 border-sky-300 text-sky-700' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-sky-300'" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-sm font-medium transition-all" :title="focusMode ? 'خروج من وضع التركيز' : 'وضع التركيز'"><i class="fas" :class="focusMode ? 'fa-compress-arrows-alt' : 'fa-expand-arrows-alt'"></i><span class="hidden sm:inline" x-text="focusMode ? 'خروج من التركيز' : 'وضع التركيز'"></span></button>
                    <button @click="toggleFullscreen()" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 hover:border-sky-300 text-slate-600 hover:text-sky-600 text-sm font-medium transition-all" title="ملء الشاشة"><i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i><span class="hidden sm:inline">ملء الشاشة</span></button>
                </div>
            </div>
        </div>
    </div>

    
    <div x-show="focusMode" class="flex items-center justify-between px-3 py-2 border-b border-slate-200 bg-white flex-shrink-0">
        <button @click="focusMode = false" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 hover:border-sky-300 text-slate-600 hover:text-sky-600 text-sm font-medium transition-all">
            <i class="fas fa-compress-arrows-alt"></i>
            <span>خروج من وضع التركيز</span>
        </button>
        <div class="flex items-center gap-2">
            <div class="h-2 w-24 bg-slate-200 rounded-full overflow-hidden">
                <div class="learn-progress-fill h-full bg-gradient-to-l from-sky-400 to-sky-500 rounded-full transition-all duration-500" style="width: <?php echo e(min(100, (float)($progress ?? 0))); ?>%"></div>
            </div>
            <span class="learn-progress-count text-xs font-semibold text-slate-600"><?php echo e($completedLessons ?? 0); ?>/<?php echo e($totalLessons ?? 0); ?></span>
            <span class="learn-progress-pct text-xs font-bold text-sky-600"><?php echo e(number_format((float)($progress ?? 0), 0)); ?>%</span>
        </div>
    </div>

    
    <div class="learn-focus-wrapper w-full px-4 lg:px-6 flex-1 flex flex-col min-h-0">
    <div class="learn-focus-grid w-full grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 flex-1 min-h-0">
        
        <div class="learn-focus-sidebar lg:col-span-4 xl:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-4">
                <div class="p-4 border-b border-slate-200 bg-white">
                    
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <h3 class="text-gray-900 font-bold text-sm flex items-center gap-2 min-w-0 flex-1">
                            <span class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center shrink-0"><i class="fas fa-list text-sky-500 text-xs"></i></span>
                            <span class="truncate"><?php echo e(__('student.learn_curriculum_title')); ?></span>
                        </h3>
                        <div class="flex items-center gap-1 shrink-0" role="group" aria-label="<?php echo e(__('student.learn_nav_prev')); ?> / <?php echo e(__('student.learn_nav_next')); ?>">
                            <button type="button"
                                    class="learn-sidebar-nav-btn inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white shadow-sm"
                                    @click="goNavPrev()"
                                    :disabled="!hasNavPrev()"
                                    title="<?php echo e(__('student.learn_nav_prev')); ?>"
                                    aria-label="<?php echo e(__('student.learn_nav_prev')); ?>">
                                <i class="fas fa-chevron-right text-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button"
                                    class="learn-sidebar-nav-btn inline-flex items-center justify-center w-9 h-9 rounded-lg border border-sky-200 bg-sky-600 text-white hover:bg-sky-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-sky-600 shadow-sm"
                                    @click="goNavNext()"
                                    :disabled="!navNextAllowed()"
                                    title="<?php echo e(__('student.learn_nav_next')); ?>"
                                    aria-label="<?php echo e(__('student.learn_nav_next')); ?>">
                                <i class="fas fa-chevron-left text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-[10px] text-amber-700 font-medium leading-snug mb-2"
                       x-show="selectedLecture && showVideoPlayer && hasNavNext() && !navNextAllowed()"
                       x-cloak>
                        <?php echo e(__('student.learn_nav_next_hint_lecture')); ?>

                    </p>
                    <p class="text-[10px] text-amber-700 font-medium leading-snug mb-2"
                       x-show="selectedLesson && showVideoPlayer && hasNavNext() && !navNextAllowed()"
                       x-cloak>
                        <?php echo e(__('student.learn_nav_next_hint_lesson')); ?>

                    </p>
                    <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 mb-3">
                        <div class="h-1.5 flex-1 rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full bg-gradient-to-l from-sky-400 to-sky-500 rounded-full" style="width: <?php echo e(min(100, (float)($progress ?? 0))); ?>%"></div>
                        </div>
                        <span class="learn-progress-count text-[10px] font-bold text-gray-600 whitespace-nowrap"><?php echo e($completedLessons ?? 0); ?>/<?php echo e($totalLessons ?? 0); ?></span>
                        <span class="learn-progress-pct text-[10px] font-bold text-sky-600"><?php echo e(number_format((float)($progress ?? 0), 0)); ?>%</span>
                    </div>
                    <div class="search-box relative">
                        <input type="text" 
                               x-model="searchQuery"
                               placeholder="ابحث..."
                               class="w-full bg-slate-50 border border-slate-200 text-gray-900 placeholder-gray-400 px-3 py-2 pr-9 rounded-xl text-xs focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all"
                               @keydown.escape="searchQuery = ''">
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fas fa-search text-xs"></i></div>
                    </div>
                </div>
                <div id="learn-curriculum-sidebar" class="focus-sidebar-content max-h-[60vh] overflow-y-auto p-3">
                    <!-- الاختبارات في السايدبار -->
                    <?php if(isset($sidebarExams) && $sidebarExams->count() > 0): ?>
                        <div class="mb-4">
                            <div class="curriculum-section-header mb-2"
                                 :class="{ 'collapsed': isSectionCollapsed('sidebar-exams') }"
                                 @click="toggleSection('sidebar-exams')"
                                 role="button"
                                 tabindex="0"
                                 @keydown.enter.prevent="toggleSection('sidebar-exams')"
                                 @keydown.space.prevent="toggleSection('sidebar-exams')">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-clipboard-check text-sky-400/90 text-[10px]"></i>
                                    <span>الاختبارات</span>
                                    <span class="text-gray-500 text-[10px]">(<?php echo e($sidebarExams->count()); ?>)</span>
                                </span>
                                <i class="fas fa-chevron-down curriculum-section-chevron"></i>
                            </div>
                            <div x-show="!isSectionCollapsed('sidebar-exams')" x-transition>
                            <?php $__currentLoopData = $sidebarExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="curriculum-item" 
                                     @click="loadExam(<?php echo e($exam->id); ?>)"
                                     x-show="!searchQuery || '<?php echo e(strtolower($exam->title)); ?>'.includes(searchQuery.toLowerCase())">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="w-6 h-6 bg-indigo-500 rounded-md flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-white text-[10px]"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="curriculum-item-title"><?php echo e($exam->title); ?></div>
                                            <div class="curriculum-item-meta">
                                                <span><i class="fas fa-clock text-[10px] ml-0.5"></i> <?php echo e($exam->duration_minutes); ?> د</span>
                                                <span><i class="fas fa-star text-[10px] ml-0.5"></i> <?php echo e($exam->total_marks); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($sections) && $sections->count() > 0): ?>
                        <!-- عرض المنهج من الأقسام (جذور + أقسام فرعية متداخلة) -->
                        <script type="application/json" id="learn-section-descriptions"><?php echo json_encode($sectionDescriptions ?? [], 15, 512) ?></script>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('student.my-courses.partials.learn-sidebar-section', ['section' => $section, 'depth' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <!-- لا يوجد منهج (تم إلغاء عرض الدروس) -->
                        <div class="py-6 px-4 text-center">
                            <p class="text-gray-600 text-sm">لا توجد عناصر في المنهج بعد.</p>
                            <p class="text-gray-500 text-xs mt-1">المحاضرات والواجبات والامتحانات تظهر هنا عند إضافتها من المدرب.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="learn-focus-content lg:col-span-8 xl:col-span-9">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[400px]">
                <div class="focus-main-content-wrapper p-4 lg:p-6">
                    <!-- حالة ترحيب -->
                    <div x-show="!selectedLesson && !selectedLecture && !selectedPattern" 
                         x-transition
                         class="empty-content-state">
                        <div class="relative mb-8">
                            <div class="w-28 h-28 md:w-36 md:h-36 rounded-3xl bg-gradient-to-br from-sky-500/20 to-emerald-500/20 border border-sky-500/30 flex items-center justify-center mx-auto shadow-xl shadow-sky-500/10">
                                <i class="fas fa-book-open text-sky-400 text-5xl md:text-6xl"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-2 w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center">
                                <i class="fas fa-play text-emerald-400 text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">مرحباً في <?php echo e($course->localized('title')); ?></h3>
                        <p class="text-gray-600 text-base md:text-lg mb-2 max-w-md mx-auto">اختر محاضرة أو واجباً أو امتحاناً من القائمة لبدء التعلم</p>
                        <p class="text-gray-500 text-sm mb-8">التقدم: <?php echo e($completedLessons ?? 0); ?> من <?php echo e($totalLessons ?? 0); ?> — <?php echo e(number_format((float)($progress ?? 0), 0)); ?>%</p>
                    </div>
                    
                    <!-- وصف القسم (يظهر في منطقة المحتوى عند اختيار عنصر من قسم له وصف — وليس في السايدبار) -->
                    <div x-show="currentSectionDescription" x-transition
                         class="mb-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm leading-relaxed">
                        <p class="whitespace-pre-wrap" x-text="currentSectionDescription"></p>
                    </div>

                    <!-- محتوى الدرس المحدد -->
                    <div x-show="selectedLesson && !selectedLecture && !showVideoPlayer" x-transition class="lesson-content-viewer">
                        <div x-html="lessonContent"></div>
                    </div>
                    
                    <!-- مشغل الفيديو داخل حاوية 16:9 -->
                    <div x-show="(selectedLesson && showVideoPlayer) || (selectedLecture && showVideoPlayer)" 
                         x-transition
                         class="lesson-video-viewer w-full flex flex-col rounded-xl overflow-hidden border border-slate-200 bg-black">
                        <div x-show="selectedLesson && !selectedLecture" class="lesson-details-bar">
                            <span class="lesson-meta">التقدم: <span x-text="videoProgressPercent || 0">0</span>%</span>
                            <span class="lesson-meta">الوقت: <span x-text="videoTimeCurrent || '0:00'">0:00</span> / <span x-text="currentLessonDuration ? (currentLessonDuration + ' د') : (videoTimeTotal || '0:00')">0:00</span></span>
                            <img x-show="currentLessonThumbnail" :src="currentLessonThumbnail" alt="" class="lesson-thumb" />
                            <span class="lesson-title-text truncate" x-text="currentLessonTitle || 'الدرس'">الدرس</span>
                            <button type="button"
                                    @click="markLessonComplete()"
                                    :disabled="currentLessonCompleted"
                                    :class="currentLessonCompleted ? 'btn-lesson-complete completed' : 'btn-lesson-complete'">
                                <i class="fas fa-check text-white"></i>
                                <span x-text="currentLessonCompleted ? 'تم إكمال الدرس بنجاح!' : 'تم إكمال الدرس بنجاح!'">تم إكمال الدرس بنجاح!</span>
                            </button>
                            <button type="button" class="btn-share" title="مشاركة"><i class="fas fa-share-alt"></i> مشاركة</button>
                        </div>
                        <!-- شريط تقدم المشاهدة — للمحاضرة: تحديث مباشر من سكربت الفيديو (بدون Alpine). للدرس: Alpine -->
                        <div class="flex-shrink-0 px-3 py-2.5 bg-slate-800 border-b border-slate-600 min-h-[52px] flex flex-col justify-center" id="learn-watch-percent-bar">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-sm font-semibold text-sky-300">نسبة المشاهدة</span>
                                <template x-if="selectedLecture">
                                    <span id="lecture-watch-pct-text" class="text-sm font-bold text-white tabular-nums">0.0%</span>
                                </template>
                                <span x-show="selectedLesson && showVideoPlayer" x-text="(Math.round((videoProgressPercent || 0) * 10) / 10).toFixed(1) + '%'" class="text-sm font-bold text-white tabular-nums">0.0%</span>
                            </div>
                            <div class="h-2.5 bg-slate-700 rounded-full overflow-hidden">
                                <template x-if="selectedLecture">
                                    <div id="lecture-watch-pct-fill" class="h-full bg-gradient-to-r from-sky-400 to-sky-500 rounded-full transition-all duration-300 min-w-[2px]" style="width: 0%;"></div>
                                </template>
                                <div x-show="selectedLesson && showVideoPlayer" class="h-full bg-gradient-to-r from-sky-400 to-sky-500 rounded-full transition-all duration-300 min-w-[2px]" :style="'width: ' + Math.min(100, Math.max(0, videoProgressPercent || 0)) + '%'"></div>
                            </div>
                        </div>
                        <div class="aspect-video w-full relative bg-black flex-1 min-h-0" x-show="(selectedLesson && showVideoPlayer) || (selectedLecture && showVideoPlayer)">
                            
                            <div x-show="selectedLecture && showVideoPlayer" class="absolute inset-0 w-full h-full" id="learn-video-embed"></div>
                            
                            <div x-show="selectedLesson && showVideoPlayer" class="absolute inset-0 w-full h-full">
                                <?php echo $__env->make('student.my-courses.partials.video-player', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- محتوى المحاضرة (بدون فيديو) -->
                    <div x-show="selectedLecture && !showVideoPlayer" x-transition class="lesson-content-viewer">
                        <div x-html="lectureContent"></div>
                    </div>

                    <!-- مواد المحاضرة (ظاهرة عند اختيار محاضرة ولديها مواد) -->
                    <div x-show="selectedLecture && lectureMaterials && lectureMaterials.length" x-transition
                         class="mt-5 rounded-2xl border border-slate-200 dark:border-slate-600 overflow-hidden bg-white dark:bg-slate-800/50 shadow-sm">
                        <div class="px-4 sm:px-5 py-3.5 bg-gradient-to-l from-sky-50 to-white dark:from-slate-800 dark:to-slate-800/80 border-b border-slate-100 dark:border-slate-600 flex items-center justify-between gap-3 flex-wrap">
                            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center">
                                    <i class="fas fa-paperclip text-sky-600 dark:text-sky-400"></i>
                                </span>
                                مواد المحاضرة
                                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-100 dark:bg-sky-900/40 px-2.5 py-0.5 rounded-full" x-text="lectureMaterials.length"></span>
                            </h3>
                        </div>
                        <div class="p-4 sm:p-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="mat in lectureMaterials" :key="mat.id">
                                    <a :href="mat.download_url" target="_blank" rel="noopener"
                                       class="group flex items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 hover:border-sky-200 dark:hover:border-sky-700 transition-all duration-200">
                                        <span class="w-12 h-12 rounded-xl bg-white dark:bg-slate-600 shadow-sm border border-slate-200 dark:border-slate-500 flex items-center justify-center shrink-0 group-hover:bg-sky-100 dark:group-hover:bg-sky-900/30 transition-colors">
                                            <i class="fas text-lg" :class="getMaterialIconClass(mat)"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <span class="block font-semibold text-slate-800 dark:text-white truncate group-hover:text-sky-700 dark:group-hover:text-sky-300 transition-colors" x-text="mat.title"></span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 block truncate" x-text="mat.file_name"></span>
                                        </div>
                                        <span class="w-10 h-10 rounded-lg bg-sky-500 text-white flex items-center justify-center shrink-0 group-hover:bg-sky-600 transition-colors">
                                            <i class="fas fa-download text-sm"></i>
                                        </span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- النمط التعليمي -->
                    <div x-show="selectedPattern" x-transition class="pattern-embed-outer flex w-full items-center justify-center overflow-auto p-2 sm:p-3 min-h-[400px]">
                        <div class="pattern-embed-wrapper flex flex-col w-full max-w-4xl min-h-[360px] rounded-xl overflow-hidden border border-slate-200 shadow-lg bg-white">
                            <div class="flex items-center justify-between gap-3 px-3 py-2.5 sm:px-4 sm:py-3 bg-white border-b border-slate-200 rounded-t-xl shrink-0">
                                <span class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-puzzle-piece text-sky-500"></i>
                                    النمط التعليمي
                                </span>
                                <button type="button" @click="selectedPattern = null"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-gray-700 text-sm font-medium transition-colors border border-slate-200">
                                    <i class="fas fa-arrow-right ml-1"></i>
                                    إغلاق والعودة
                                </button>
                            </div>
                            <div class="pattern-embed-iframe-container flex-1 min-h-[300px] bg-white rounded-b-xl overflow-hidden border-t-0 border-slate-200">
                                <iframe :src="selectedPattern ? '<?php echo e(route('my-courses.learning-patterns.show', [$course, '_PID_'])); ?>'.replace('_PID_', selectedPattern) + '?embed=1' : ''"
                                        class="pattern-embed-iframe w-full h-full min-h-[300px] border-0"
                                        title="النمط التعليمي"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
    
    return {
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
            if (!cur || !window.learnNavGetPrevForButton) return false;
            var prev = window.learnNavGetPrevForButton(cur.type, cur.id);
            return !!(prev && prev.type && prev.id != null && prev.id !== '');
        },
        hasNavNext() {
            var cur = this.getCurrentNavTypeId();
            if (!cur || !window.learnNavGetNextForButton) return false;
            var next = window.learnNavGetNextForButton(cur.type, cur.id);
            if (!next || !next.type || next.id == null || next.id === '') return false;
            if (window.learnNavIsTargetLocked && window.learnNavIsTargetLocked(next)) return false;
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
            if (!cur || !window.learnNavGetPrevForButton) return;
            var prev = window.learnNavGetPrevForButton(cur.type, cur.id);
            if (!prev || !prev.type || prev.id == null) return;
            var nid = parseInt(prev.id, 10);
            if (isNaN(nid)) nid = prev.id;
            window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: prev.type, id: nid } }));
        },
        goNavNext() {
            if (!this.navNextAllowed()) return;
            var cur = this.getCurrentNavTypeId();
            if (!cur || !window.learnNavGetNextForButton) return;
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
            
            // إذا لم توجد المحاضرة محلياً أو لا يوجد فيها رابط فيديو، جلبها من الخادم
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
            
            // إذا كان هناك فيديو: نفس أسلوب البوب أب في المنهج — بناء HTML المعاينة ووضعه في حاوية واحدة
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
                const courseId = this.$el.closest('[data-course-id]')?.dataset?.courseId;
                const canControl = (platform === 'youtube' || platform === 'vimeo' || platform === 'bunny');
                if (canControl && courseId) {
                    const container = document.getElementById('learn-video-embed');
                    if (container) window.initLectureVideoWithQuestions(container, lecture, platform, url, courseId, lectureId);
                } else {
                    const embedHtml = this.buildLectureVideoEmbedHtml(url, platform);
                    const inject = () => {
                        const container = document.getElementById('learn-video-embed');
                        if (container && embedHtml) container.innerHTML = embedHtml;
                        else if (container) container.innerHTML = '<div class="flex items-center justify-center text-white h-full"><p>لا يمكن عرض الفيديو</p></div>';
                        // فيديو مباشر (mp4…): لا يمر بـ initLectureVideoWithQuestions — نربط انتهاء التشغيل بالانتقال التلقائي مثل باقي المنصات
                        if (container && platform === 'direct') {
                            const vid = container.querySelector('video');
                            if (vid) {
                                vid.addEventListener('ended', function onLectureDirectEnded() {
                                    window.dispatchEvent(new CustomEvent('learn-video-ended'));
                                });
                            }
                        }
                    };
                    this.$nextTick(inject);
                    setTimeout(inject, 50);
                    setTimeout(inject, 200);
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
            const index = this.collapsedSections.indexOf(section);
            if (index > -1) {
                this.collapsedSections.splice(index, 1);
            } else {
                this.collapsedSections.push(section);
            }
        },
        isSectionCollapsed(section) {
            return this.collapsedSections.includes(section);
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
                document.querySelectorAll('.learn-progress-count').forEach(el => { el.textContent = completed + '/' + total; });
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
                // إزالة query string مثل صفحة curriculum ثم استخدام الرابط
                const embedUrl = trimmed.split('?')[0];
                if (!embedUrl.startsWith('http')) return 'https://' + embedUrl.replace(/^\/+/, '');
                return embedUrl;
            }
            // رابط Bunny بدون نمط embed (نادر): نعيده كما هو بعد إزالة الـ query
            const noQuery = trimmed.split('?')[0];
            return noQuery.startsWith('http') ? noQuery : ('https://' + noQuery.replace(/^\/+/, ''));
        },
        loadVideo(videoUrl, platform = null) {
            if (this.ytProgressInterval) { clearInterval(this.ytProgressInterval); this.ytProgressInterval = null; }
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
            }
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
    window.initLectureVideoWithQuestions = function(container, lecture, platform, url, courseId, lectureId) {
        if (!container || !lecture) return;
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
        var startFromSec = (lecture.progress && lecture.progress.watch_time_seconds > 0) ? Math.floor(lecture.progress.watch_time_seconds) : 0;
        var savedDurationSec = (lecture.progress && lecture.progress.video_duration_seconds > 0) ? lecture.progress.video_duration_seconds : 0;
        var durationMinutesFromLecture = (lecture.duration_minutes && parseInt(lecture.duration_minutes, 10) > 0) ? parseInt(lecture.duration_minutes, 10) : 0;
        var fallbackDurationSec = durationMinutesFromLecture > 0 ? durationMinutesFromLecture * 60 : savedDurationSec;
        var hasOpenedNext = false;
        var minPercentToUnlock = (lecture.min_watch_percent_to_unlock_next != null && lecture.min_watch_percent_to_unlock_next !== '') ? parseInt(lecture.min_watch_percent_to_unlock_next, 10) : 90;

        function updateLectureBar(pct) {
            var elText = document.getElementById('lecture-watch-pct-text');
            var elFill = document.getElementById('lecture-watch-pct-fill');
            if (elText) elText.textContent = (Math.round((pct || 0) * 10) / 10).toFixed(1) + '%';
            if (elFill) elFill.style.width = Math.min(100, Math.max(0, pct || 0)) + '%';
        }
        var initialPct = (lecture.progress && lecture.progress.progress_percent != null) ? lecture.progress.progress_percent : 0;
        setTimeout(function() { updateLectureBar(initialPct); }, 400);

        function seekToStartPosition() {
            if (startFromSec <= 0 || !player) return;
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
                if (hasEnd && allEndDone && typeof window.showAutoAdvanceToNext === 'function') {
                    window.showAutoAdvanceToNext('lecture', lectureId);
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
                                        var dur = (ds && ds > 0) ? ds : (savedDurationSec > 0 ? savedDurationSec : fallbackDurationSec);
                                        if (!dur) return;
                                        var pct = Math.min(100, Math.round((cs / dur) * 100));
                                        window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: pct } }));
                                        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                        fetch('/my-courses/' + courseId + '/lectures/' + lectureId + '/progress', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                            body: JSON.stringify({ current_sec: cs, duration_sec: dur })
                                        }).then(function(r) { return r.json(); }).then(function(data) {
                                            if (data && data.success) {
                                                var wrapper = document.querySelector('.learn-page');
                                                if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
                                                if (data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                                                if (data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                                                if (typeof updateProgressBar === 'function') updateProgressBar();
                                                if (typeof data.progress_percent === 'number') {
                                                    window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: data.progress_percent } }));
                                                }
                                                if (!hasOpenedNext && (data.is_completed || (data.progress_percent >= minPercentToUnlock))) {
                                                    hasOpenedNext = true;
                                                    try {
                                                        var nextMap = document.getElementById('learn-next-item-map');
                                                        var nextByLecture = nextMap && nextMap.textContent ? JSON.parse(nextMap.textContent) : {};
                                                        var nextItem = nextByLecture[lectureId];
                                                        if (nextItem && nextItem.type && nextItem.id) {
                                                            window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: nextItem.type, id: nextItem.id } }));
                                                        }
                                                    } catch (err) { console.warn('learn-open-next', err); }
                                                }
                                            }
                                        }).catch(function() {});
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
                                        var dur = (ds && ds > 0) ? ds : (savedDurationSec > 0 ? savedDurationSec : fallbackDurationSec);
                                        if (!dur) return;
                                        var pct = Math.min(100, Math.round((cs / dur) * 100));
                                        window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: pct } }));
                                        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                        fetch('/my-courses/' + courseId + '/lectures/' + lectureId + '/progress', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                            body: JSON.stringify({ current_sec: cs, duration_sec: dur })
                                        }).then(function(r) { return r.json(); }).then(function(data) {
                                            if (data && data.success) {
                                                var wrapper = document.querySelector('.learn-page');
                                                if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
                                                if (data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                                                if (data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                                                if (typeof updateProgressBar === 'function') updateProgressBar();
                                                if (typeof data.progress_percent === 'number') {
                                                    window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: data.progress_percent } }));
                                                }
                                                if (!hasOpenedNext && (data.is_completed || (data.progress_percent >= minPercentToUnlock))) {
                                                    hasOpenedNext = true;
                                                    try {
                                                        var nextMap = document.getElementById('learn-next-item-map');
                                                        var nextByLecture = nextMap && nextMap.textContent ? JSON.parse(nextMap.textContent) : {};
                                                        var nextItem = nextByLecture[lectureId];
                                                        if (nextItem && nextItem.type && nextItem.id) {
                                                            window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: nextItem.type, id: nextItem.id } }));
                                                        }
                                                    } catch (err) { console.warn('learn-open-next', err); }
                                                }
                                            }
                                        }).catch(function() {});
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
                            var dur = (durationSec && durationSec > 0) ? durationSec : (savedDurationSec > 0 ? savedDurationSec : fallbackDurationSec);
                            if (!dur) return;
                            var pct = Math.min(100, Math.round((currentSec / dur) * 100));
                            window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: pct } }));
                            var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch('/my-courses/' + courseId + '/lectures/' + lectureId + '/progress', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({ current_sec: currentSec, duration_sec: dur })
                            }).then(function(r) { return r.json(); }).then(function(data) {
                                if (data && data.success) {
                                    var wrapper = document.querySelector('.learn-page');
                                    if (wrapper && data.course_progress != null) wrapper.dataset.courseProgress = data.course_progress;
                                    if (data.total_items != null) wrapper.dataset.totalItems = data.total_items;
                                    if (data.completed_items != null) wrapper.dataset.completedItems = data.completed_items;
                                    if (typeof updateProgressBar === 'function') updateProgressBar();
                                    if (typeof data.progress_percent === 'number') {
                                        window.dispatchEvent(new CustomEvent('learn-lecture-progress', { detail: { progress_percent: data.progress_percent } }));
                                    }
                                    if (!hasOpenedNext && (data.is_completed || (data.progress_percent >= minPercentToUnlock))) {
                                        hasOpenedNext = true;
                                        try {
                                            var nextMap = document.getElementById('learn-next-item-map');
                                            var nextByLecture = nextMap && nextMap.textContent ? JSON.parse(nextMap.textContent) : {};
                                            var nextItem = nextByLecture[lectureId];
                                            if (nextItem && nextItem.type && nextItem.id) {
                                                window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: nextItem.type, id: nextItem.id } }));
                                            }
                                        } catch (err) { console.warn('learn-open-next', err); }
                                    }
                                }
                            }).catch(function() {});
                        };
                        if (platform === 'youtube') {
                            var d = (player.getDuration && player.getDuration()) || savedDurationSec || fallbackDurationSec;
                            if (d) send(t, d);
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
            for (var i = 0; i < questions.length; i++) {
                var q = questions[i];
                if (q.show_at_end && !shownIds.has(q.id)) {
                    if (player && player.pauseVideo) player.pauseVideo();
                    if (player && player.pause) player.pause();
                    showQuestion(q);
                    return;
                }
            }
            // No questions left — auto-advance to next item
            if (typeof window.showAutoAdvanceToNext === 'function') {
                window.showAutoAdvanceToNext('lecture', lectureId);
            }
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

            function createBunnyPlayer() {
                if (player) return;
                if (!window.playerjs || !window.playerjs.Player) return;
                player = new window.playerjs.Player(iframe);
                player.on('ready', function() {
                    startTimeCheck();
                    setTimeout(seekToStartPosition, 500);
                });
                player.on('ended', showEndOfVideoQuestions);
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

    function parseLearnNextItemMap() {
        try {
            var el = document.getElementById('learn-next-item-map');
            if (!el || !el.textContent) return {};
            return JSON.parse(el.textContent);
        } catch (e) {
            return {};
        }
    }

    function findCurriculumItemEl(type, id) {
        if (!type || id == null || id === '') return null;
        var root = document.getElementById('learn-curriculum-sidebar');
        var sel = '.curriculum-item[data-item-type="' + String(type) + '"][data-item-id="' + String(id) + '"]';
        return root ? root.querySelector(sel) : document.querySelector(sel);
    }

    function getNextCurriculumItem(currentType, currentId) {
        // ترتيب موثوق من السيرفر (نفس flattenCurriculumItems) — يمنع القفز لعناصر «عشوائية» بسبب تخطي المقفل في الـ DOM
        if (currentType === 'lecture' && currentId != null && currentId !== '') {
            var map = parseLearnNextItemMap();
            var sid = String(currentId);
            if (Object.prototype.hasOwnProperty.call(map, sid)) {
                var nextFromMap = map[sid];
                if (nextFromMap === null || nextFromMap === undefined) return null;
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

    window.learnNavGetNextForButton = getNextCurriculumItem;
    window.learnNavGetPrevForButton = getPrevCurriculumItem;
    window.learnNavIsTargetLocked = learnNavIsTargetLocked;

    function getItemTitle(el) {
        var titleEl = el && el.querySelector('.curriculum-item-title');
        return titleEl ? titleEl.textContent.trim() : '';
    }

    function hideOverlay() {
        var overlay = document.getElementById('autoplay-next-overlay');
        if (overlay) overlay.style.display = 'none';
        if (_countdownTimer) { clearInterval(_countdownTimer); _countdownTimer = null; }
        _nextPending = null;
    }

    function loadNextItem(item) {
        hideOverlay();
        if (!item) return;
        window.dispatchEvent(new CustomEvent('learn-open-next-item', { detail: { type: item.type, id: parseInt(item.id, 10) } }));
        // scroll item into view in sidebar
        if (item.el) {
            item.el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    /**
     * الانتقال فوراً للعنصر التالي في المنهج (بدون عدّ تنازلي ولا حاجة لتحديث الصفحة).
     * يبقى زرّا «تشغيل الآن / إلغاء» في الـ DOM لمن يستدعيهما من مكان آخر فقط.
     */
    function showAutoAdvance(currentType, currentId) {
        if (_countdownTimer) { clearInterval(_countdownTimer); _countdownTimer = null; }
        _nextPending = null;
        var autoplayOverlay = document.getElementById('autoplay-next-overlay');
        if (autoplayOverlay) autoplayOverlay.style.display = 'none';

        var next = getNextCurriculumItem(currentType, currentId);
        if (!next) return;
        loadNextItem(next);
    }

    window._autoplayNow = function() { if (_nextPending) loadNextItem(_nextPending); };
    window._autoplayCancel = function() { hideOverlay(); };
    window.showAutoAdvanceToNext = showAutoAdvance;
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/my-courses/learn.blade.php ENDPATH**/ ?>