<?php $__env->startSection('title', $course->localized('title') . ' - ' . __('student.my_courses')); ?>

<?php
    $nextLessonId = null;
    $nextLessonTitle = null;
    if (isset($sections) && $sections->count() > 0) {
        foreach ($sections as $section) {
            foreach ($section->activeItems as $curriculumItem) {
                $entity = $curriculumItem->item;
                if (! $entity instanceof \App\Models\CourseLesson) {
                    continue;
                }
                $lp = $entity->progress->first();
                if (! $lp || ! $lp->is_completed) {
                    $nextLessonId = $entity->id;
                    $nextLessonTitle = $entity->title;
                    break 2;
                }
            }
        }
    } else {
        foreach ($course->lessons->sortBy('order') as $lesson) {
            $lp = $lesson->progress->first();
            if (! $lp || ! $lp->is_completed) {
                $nextLessonId = $lesson->id;
                $nextLessonTitle = $lesson->title;
                break;
            }
        }
    }
    $learnUrl = $nextLessonId
        ? route('my-courses.learn', $course).'?lesson='.$nextLessonId
        : route('my-courses.learn', $course);
?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
<style>
    .cs {
        --ml-teal: #49A4A2;
        --ml-teal-deep: #2f7f7d;
        --ml-yellow: #FFD23F;
        --ml-yellow-ink: #5c4500;
        --ml-bg: #F7F9FC;
        --ml-surface: #FFFFFF;
        --ml-well: #EEF2F7;
        --ml-ink: #1A2238;
        --ml-muted: #475569;
        --ml-line: rgba(26, 34, 56, 0.08);
        --ml-r: 14px;
        --ml-fast: 140ms;
        --ml-slow: 400ms;
        --ml-ease: cubic-bezier(0.22, 1, 0.36, 1);
        font-family: 'IBM Plex Sans Arabic', 'Tajawal', 'Cairo', sans-serif;
        color: var(--ml-ink);
        width: 100%;
        max-width: none;
        padding-block: 4px 32px;
    }
    .cs-reveal { animation: csRise var(--ml-slow) var(--ml-ease) both; animation-delay: var(--reveal-delay, 0ms); }
    @keyframes csRise {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: none; }
    }
    .cs-chrome {
        display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
        gap: 12px; padding: 8px 0 14px; border-bottom: 1px solid var(--ml-line); margin-bottom: 20px;
    }
    .cs-crumb { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--ml-muted); margin-bottom: 6px; }
    .cs-crumb a { color: var(--ml-teal-deep); font-weight: 600; text-decoration: none; }
    .cs-crumb a:hover { text-decoration: underline; }
    .cs-chrome h1 { margin: 0; font-size: clamp(1.2rem, 2vw, 1.5rem); font-weight: 700; letter-spacing: -0.015em; line-height: 1.3; max-width: 28ch; }
    .cs-chrome .sub { margin: 4px 0 0; font-size: 13px; color: var(--ml-muted); }
    .cs-signals { display: flex; flex-wrap: wrap; gap: 8px; }
    .cs-signal {
        display: inline-flex; align-items: center; gap: 6px; min-height: 28px;
        padding: 0 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .cs-signal-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
    .cs-signal-hot { background: rgba(255, 210, 63, 0.35); color: var(--ml-yellow-ink); }

    .cs-stage {
        position: relative; display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(180px, 0.55fr);
        gap: 20px; align-items: stretch; padding: 0; margin-bottom: 20px;
        background: var(--ml-surface); border-radius: calc(var(--ml-r) + 4px);
        border: 1px solid var(--ml-line);
        box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 10px 30px rgba(26, 34, 56, 0.04);
        overflow: hidden;
    }
    .cs-stage::before {
        content: ''; position: absolute; inset-block: 16px; inset-inline-start: 0; width: 3px; z-index: 2;
        border-radius: 999px; background: linear-gradient(180deg, var(--ml-teal), rgba(73,164,162,0.2));
    }
    .cs-stage-body { padding: 20px 22px; min-width: 0; }
    .cs-eyebrow {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;
        font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
    }
    .cs-eyebrow em {
        font-style: normal; padding: 2px 8px; border-radius: 6px;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
    .cs-stage h2 {
        margin: 0 0 6px; font-size: clamp(1.15rem, 1.8vw, 1.4rem); font-weight: 700;
        line-height: 1.35; letter-spacing: -0.01em;
    }
    .cs-copy { margin: 0; font-size: 13px; line-height: 1.65; color: var(--ml-muted); max-width: 52ch; }
    .cs-meter {
        height: 4px; width: 100%; max-width: 260px; margin-top: 14px; border-radius: 999px;
        background: var(--ml-well); overflow: hidden;
    }
    .cs-meter > i { display: block; height: 100%; background: var(--ml-teal); border-radius: inherit; }
    .cs-stage-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
    .cs-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; padding: 0 18px; border-radius: 12px; background: var(--ml-teal);
        color: #fff !important; font-size: 14px; font-weight: 700; text-decoration: none !important;
        border: 0; box-shadow: 0 8px 18px rgba(73, 164, 162, 0.22);
        transition: background var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .cs-btn:hover { background: var(--ml-teal-deep); transform: translateY(-1px); }
    .cs-btn-quiet {
        background: transparent; color: var(--ml-ink) !important; box-shadow: none;
        border: 1px solid var(--ml-line);
    }
    .cs-btn-quiet:hover { background: var(--ml-well); transform: none; }
    .cs-cover {
        position: relative; min-height: 180px;
        background: linear-gradient(145deg, rgba(73,164,162,0.18), var(--ml-well));
        display: flex; align-items: center; justify-content: center; color: var(--ml-teal-deep);
    }
    .cs-cover img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .cs-cover .ph { position: relative; z-index: 1; text-align: center; padding: 16px; }
    .cs-cover .ph i { font-size: 2rem; display: block; margin-bottom: 8px; }
    .cs-cover .pct {
        position: absolute; z-index: 2; top: 12px; inset-inline-start: 12px;
        min-height: 28px; padding: 0 10px; border-radius: 8px;
        background: rgba(255,255,255,0.92); border: 1px solid var(--ml-line);
        font-size: 12px; font-weight: 700; color: var(--ml-teal-deep);
        display: inline-flex; align-items: center;
    }

    .cs-pulse {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1px; margin-bottom: 20px; background: var(--ml-line);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r); overflow: hidden;
    }
    .cs-pulse > div {
        background: var(--ml-surface); padding: 14px 16px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .cs-pulse .lbl { font-size: 11px; font-weight: 700; color: var(--ml-muted); }
    .cs-pulse .val { font-size: 1.25rem; font-weight: 700; color: var(--ml-ink); letter-spacing: -0.02em; }
    .cs-pulse .val.teal { color: var(--ml-teal-deep); }
    .cs-pulse .val.hot { color: var(--ml-yellow-ink); }

    .cs-split {
        display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 0.85fr);
        gap: 16px; margin-bottom: 20px; align-items: start;
    }
    .cs-panel {
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); padding: 16px 18px;
    }
    .cs-label {
        margin: 0 0 10px; font-size: 11px; font-weight: 700; color: var(--ml-muted);
        letter-spacing: 0.02em;
    }
    .cs-panel p.body {
        margin: 0; font-size: 13px; line-height: 1.7; color: var(--ml-ink);
    }
    .cs-facts { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
    .cs-facts li {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 10px 12px; border-radius: 10px; background: var(--ml-well);
        font-size: 13px;
    }
    .cs-facts .k { color: var(--ml-muted); font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
    .cs-facts .k i { color: var(--ml-teal-deep); width: 1rem; text-align: center; }
    .cs-facts .v { font-weight: 700; color: var(--ml-ink); }

    .cs-curriculum { margin-top: 4px; }
    .cs-curriculum > .cs-label { margin-bottom: 12px; font-size: 12px; color: var(--ml-ink); }
    .cs-section {
        margin-bottom: 12px; background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); overflow: hidden;
    }
    .cs-section-h {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 12px 14px; background: var(--ml-well); border-bottom: 1px solid var(--ml-line);
    }
    .cs-section-h strong { font-size: 13px; font-weight: 700; }
    .cs-section-h span { font-size: 11px; font-weight: 700; color: var(--ml-muted); }
    .cs-section-d { margin: 0; padding: 8px 14px 0; font-size: 12px; color: var(--ml-muted); line-height: 1.5; }
    .cs-items { list-style: none; margin: 0; padding: 8px; display: flex; flex-direction: column; gap: 6px; }
    .cs-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 12px;
        border-radius: 10px; text-decoration: none !important; color: inherit !important;
        border: 1px solid transparent; transition: background var(--ml-fast) ease, border-color var(--ml-fast) ease;
    }
    .cs-item:hover { background: rgba(73, 164, 162, 0.08); border-color: rgba(73, 164, 162, 0.2); }
    .cs-item.is-locked { opacity: 0.55; pointer-events: none; }
    .cs-item.is-done { background: rgba(16, 185, 129, 0.06); }
    .cs-item.is-now { border-color: rgba(73, 164, 162, 0.35); background: rgba(73, 164, 162, 0.1); }
    .cs-ico {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 12px; color: #fff;
        background: #94a3b8;
    }
    .cs-ico.done { background: #10b981; }
    .cs-ico.now { background: var(--ml-teal); }
    .cs-ico.lock { background: #64748b; }
    .cs-ico.task { background: #8b5cf6; }
    .cs-ico.exam { background: #6366f1; }
    .cs-item .t { flex: 1; min-width: 0; }
    .cs-item .t strong { display: block; font-size: 13px; font-weight: 700; line-height: 1.35; }
    .cs-item .t small { display: block; margin-top: 2px; font-size: 11px; color: var(--ml-muted); font-weight: 600; }
    .cs-item .go { font-size: 11px; font-weight: 700; color: var(--ml-teal-deep); white-space: nowrap; }

    .cs-empty {
        text-align: center; padding: 36px 20px; border: 1px dashed rgba(26,34,56,0.14);
        border-radius: var(--ml-r); background: var(--ml-surface); color: var(--ml-muted); font-size: 13px;
    }

    @media (max-width: 900px) {
        .cs-stage { grid-template-columns: 1fr; }
        .cs-cover { min-height: 160px; order: -1; }
        .cs-split { grid-template-columns: 1fr; }
        .cs-pulse { grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) {
        .cs-reveal, .cs-btn, .cs-item { animation: none !important; transition: none !important; }
    }

    /* —— Focus mode (preserved) —— */
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .animate-shimmer { animation: shimmer 2s infinite; }
    .border-b-3 { border-bottom-width: 3px; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .lesson-item, .lecture-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .lesson-item:hover, .lecture-item:hover {
        transform: translateX(-5px);
    }
    /* Focus Mode - وضع التركيز المتقدم */
    .focus-mode {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f8fafc;
        z-index: 99999;
        overflow: hidden;
        padding: 0;
        animation: focusFadeIn 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
    }
    
    /* سايدبار المنهج - على اليمين */
    .focus-sidebar {
        width: 380px;
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        border-left: 1px solid rgba(59, 130, 246, 0.2);
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        transition: transform 0.3s ease, width 0.3s ease;
        order: 2;
        flex-shrink: 0;
    }
    
    /* السايدبار مغلق */
    .focus-sidebar.closed {
        width: 0;
        transform: translateX(100%);
        border: none;
        overflow: hidden;
    }
    
    .focus-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .focus-sidebar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }
    
    .focus-sidebar::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.5);
        border-radius: 3px;
    }
    
    .focus-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.7);
    }
    
    .focus-sidebar-header {
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.8);
        border-bottom: 2px solid rgba(59, 130, 246, 0.3);
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(10px);
    }
    
    .focus-sidebar-content {
        padding: 1rem;
    }
    
    /* المحتوى الرئيسي - على اليسار */
    .focus-main-content {
        flex: 1;
        overflow-y: auto;
        background: #ffffff;
        position: relative;
        order: 1;
        min-height: 0;
        width: 100%;
        transition: margin-left 0.3s ease;
    }
    
    /* عندما يكون السايدبار مغلق */
    .focus-sidebar.closed {
        width: 0 !important;
        min-width: 0 !important;
        padding: 0 !important;
        border: none !important;
        overflow: hidden !important;
        opacity: 0;
        pointer-events: none;
    }
    
    /* المحتوى يملأ الصفحة عندما يكون السايدبار مغلق */
    .curriculum-wrapper:has(.focus-sidebar.closed) .focus-main-content,
    .focus-sidebar.closed ~ .focus-main-content {
        width: 100% !important;
        flex: 1 1 100% !important;
        margin: 0 !important;
    }
    
    /* زر التبديل */
    .sidebar-toggle-btn {
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1000;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2px solid rgba(59, 130, 246, 0.3);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .sidebar-toggle-btn:hover {
        background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
        border-color: rgba(59, 130, 246, 0.5);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    }
    
    /* عندما يكون السايدبار مفتوح، الزر يتحرك */
    .focus-sidebar:not(.closed) ~ .focus-main-content .sidebar-toggle-btn,
    .focus-sidebar:not(.closed) + .focus-main-content .sidebar-toggle-btn {
        right: 400px;
    }
    
    /* ضمان أن المحتوى يملأ الصفحة */
    .curriculum-wrapper {
        width: 100%;
        display: flex;
    }
    
    .curriculum-wrapper .focus-main-content {
        flex: 1;
        min-width: 0;
    }
    
    .focus-main-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .focus-main-content::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    .focus-main-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .focus-main-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* عناصر المنهج في السايدبار */
    .curriculum-item {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    
    .curriculum-item:hover {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(59, 130, 246, 0.5);
        transform: translateX(-5px);
    }
    
    .curriculum-item.active {
        background: rgba(59, 130, 246, 0.2);
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .curriculum-item.completed {
        border-color: rgba(16, 185, 129, 0.5);
    }
    
    .curriculum-item.locked {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* زر إغلاق/فتح السايدبار */
    .sidebar-toggle-btn {
        position: fixed;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1000;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2px solid rgba(59, 130, 246, 0.3);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .sidebar-toggle-btn:hover {
        background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
        border-color: rgba(59, 130, 246, 0.5);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
    }
    
    /* عندما يكون السايدبار مغلق، الزر يظهر على اليمين */
    .focus-sidebar.closed ~ .focus-main-content .sidebar-toggle-btn {
        right: 20px;
    }
    
    /* زر في السايدبار لإغلاقه */
    .sidebar-close-btn {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.5);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 20;
    }
    
    .sidebar-close-btn:hover {
        background: rgba(239, 68, 68, 0.4);
        border-color: #ef4444;
        transform: scale(1.1);
    }
    
    .focus-sidebar.closed .sidebar-close-btn {
        display: none;
    }
    
    @media (max-width: 1024px) {
        .focus-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 100001;
            transform: translateX(100%);
        }
        
        .focus-sidebar.open {
            transform: translateX(0);
        }
        
        .focus-main-content {
            width: 100%;
        }
        
        .sidebar-toggle-btn {
            display: block;
        }
    }
    
    @keyframes focusFadeIn {
        from {
            opacity: 0;
            backdrop-filter: blur(0px);
        }
        to {
            opacity: 1;
            backdrop-filter: blur(10px);
        }
    }
    
    .focus-mode .curriculum-wrapper {
        display: flex;
        flex-direction: row;
        height: 100vh;
        overflow: hidden;
        width: 100%;
    }
    
    /* شريط التحكم العلوي */
    .focus-mode .focus-control-bar {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-bottom: 2px solid #e2e8f0;
        padding: 0.75rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .focus-mode .focus-control-bar .controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .focus-mode .focus-control-bar .search-box {
        flex: 1;
        min-width: 250px;
        max-width: 400px;
    }
    
    .focus-mode .focus-control-bar .search-box input {
        width: 100%;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.9rem;
    }
    
    .focus-mode .focus-control-bar .search-box input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    
    .focus-mode .focus-control-bar .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .focus-mode .focus-control-bar .btn-control {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }
    
    .focus-mode .focus-control-bar .btn-control:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-2px);
    }
    
    .focus-mode .focus-control-bar .btn-control.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .focus-mode .focus-control-bar .btn-close {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
    }
    
    .focus-mode .focus-control-bar .btn-close:hover {
        background: rgba(239, 68, 68, 0.3);
        border-color: #ef4444;
    }
    
    
    /* المحتوى الرئيسي */
    .focus-main-content-wrapper {
        padding: 1rem 1.5rem;
        width: 100%;
        max-width: 100%;
        margin: 0;
        min-height: auto;
        box-sizing: border-box;
    }
    
    /* عند عدم وجود محتوى محدد، لا تأخذ مساحة كبيرة */
    .focus-main-content-wrapper:has(.empty-content-state) {
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
    }
    
    /* ضمان أن جميع العناصر الداخلية تملأ العرض */
    .focus-main-content-wrapper > * {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .focus-content-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .focus-content-header h2 {
        color: #1e293b;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .focus-content-header .course-meta {
        color: #64748b;
        font-size: 0.9rem;
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    
    /* محتوى الدرس */
    .lesson-content-viewer {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 100%;
        margin: 0;
        box-sizing: border-box;
    }
    
    .lesson-content-viewer > div {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .lecture-viewer {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* ضمان أن جميع العناصر داخل المحتوى تملأ العرض */
    .lecture-viewer > * {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .empty-content-state {
        text-align: center;
        padding: 1.5rem 1rem;
        color: #64748b;
        min-height: auto;
        width: 100%;
    }
    
    .empty-content-state i {
        font-size: 2.5rem;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
    }
    
    .empty-content-state h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .empty-content-state p {
        font-size: 0.875rem;
    }
    
    /* الأقسام */
    .curriculum-section {
        margin-bottom: 3rem;
        animation: slideInUp 0.5s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .curriculum-section.collapsed .section-content {
        display: none;
    }
    
    .curriculum-section-title {
        color: #60a5fa;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: rgba(59, 130, 246, 0.1);
        border-right: 4px solid #3b82f6;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .curriculum-section-title:hover {
        background: rgba(59, 130, 246, 0.2);
        transform: translateX(-5px);
    }
    
    .curriculum-section-title .section-toggle {
        color: #94a3b8;
        transition: transform 0.3s;
    }
    
    .curriculum-section.collapsed .curriculum-section-title .section-toggle {
        transform: rotate(-90deg);
    }
    
    /* عناصر المنهج في السايدبار - محسّنة */
    .curriculum-item {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    
    .curriculum-item:hover {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(59, 130, 246, 0.5);
        transform: translateX(-5px);
    }
    
    .curriculum-item.active {
        background: rgba(59, 130, 246, 0.2);
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .curriculum-item.completed {
        border-color: rgba(16, 185, 129, 0.5);
    }
    
    .curriculum-item.locked {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .curriculum-item-title {
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .curriculum-item-meta {
        color: #94a3b8;
        font-size: 0.75rem;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .curriculum-section-header {
        color: #60a5fa;
        font-size: 1rem;
        font-weight: 700;
        margin: 1.5rem 0 1rem 0;
        padding: 0.75rem 1rem;
        background: rgba(59, 130, 246, 0.1);
        border-right: 3px solid #3b82f6;
        border-radius: 0.5rem;
    }
    
    .lesson-item::before, .lecture-item::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: transparent;
        transition: all 0.3s;
    }
    
    .lesson-item:hover, .lecture-item:hover {
        border-color: #3b82f6;
        transform: translateX(-10px) scale(1.02);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }
    
    .lesson-item:hover::before, .lecture-item:hover::before {
        background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
        width: 6px;
    }
    
    .lesson-item.completed {
        border-color: rgba(16, 185, 129, 0.5);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(30, 41, 59, 0.8) 100%);
    }
    
    .lesson-item.completed::before {
        background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        width: 4px;
    }
    
    .lesson-item.current {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3), 0 10px 40px rgba(59, 130, 246, 0.2);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3), 0 10px 40px rgba(59, 130, 246, 0.2);
        }
        50% {
            box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.4), 0 15px 50px rgba(59, 130, 246, 0.3);
        }
    }
    
    .lecture-item.scheduled {
        border-color: rgba(59, 130, 246, 0.5);
    }
    
    .lecture-item.completed {
        border-color: rgba(16, 185, 129, 0.5);
    }
    
    .lecture-item.in-progress {
        border-color: rgba(245, 158, 11, 0.5);
        animation: pulse 2s infinite;
    }
    
    /* فلترة */
    .lesson-item.hidden, .lecture-item.hidden {
        display: none;
    }
    
    /* شريط التقدم */
    .focus-progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: rgba(30, 41, 59, 0.5);
        z-index: 100001;
    }
    
    .focus-progress-bar .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
        transition: width 0.3s ease;
    }
    
    /* إعدادات العرض */
    .focus-settings-panel {
        position: fixed;
        top: 50%;
        left: 2rem;
        transform: translateY(-50%);
        background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(20px);
        border: 2px solid rgba(59, 130, 246, 0.5);
        border-radius: 1rem;
        padding: 1.5rem;
        z-index: 100002;
        min-width: 280px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
        display: none;
    }
    
    .focus-settings-panel::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(139, 92, 246, 0.3));
        border-radius: 1rem;
        z-index: -1;
        animation: borderGlow 3s ease-in-out infinite;
    }
    
    @keyframes borderGlow {
        0%, 100% {
            opacity: 0.5;
        }
        50% {
            opacity: 1;
        }
    }
    
    /* تحسينات البحث */
    .search-box {
        position: relative;
    }
    
    .search-box input::placeholder {
        color: rgba(148, 163, 184, 0.6);
    }
    
    /* تحسينات الخط */
    .focus-mode[data-font-size='small'] .curriculum-content {
        font-size: 0.875rem;
    }
    
    .focus-mode[data-font-size='medium'] .curriculum-content {
        font-size: 1rem;
    }
    
    .focus-mode[data-font-size='large'] .curriculum-content {
        font-size: 1.125rem;
    }
    
    .focus-settings-panel.active {
        display: block;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateY(-50%) translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
    }
    
    /* طباعة */
    @media print {
        .focus-mode .focus-control-bar,
        .focus-mode .focus-stats,
        .focus-mode .btn-control {
            display: none !important;
        }
        
        .focus-mode {
            background: white;
            color: black;
        }
        
        .lesson-item, .lecture-item {
            background: white;
            border: 1px solid #ccc;
            page-break-inside: avoid;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="cs"
     x-data="courseFocusMode()"
     @scroll.window="updateProgressBar()">

    <header class="cs-chrome cs-reveal">
        <div>
            <nav class="cs-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo e(route('my-courses.index')); ?>"><?php echo e(__('student.my_courses')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(\Illuminate\Support\Str::limit($course->localized('title'), 40)); ?></span>
            </nav>
            <h1><?php echo e($course->localized('title')); ?></h1>
            <p class="sub">
                <?php echo e(collect([
                    $course->academicYear->name ?? null,
                    $course->teacher->name ?? null,
                ])->filter()->implode(' · ') ?: 'مقرر مفعّل'); ?>

            </p>
        </div>
        <div class="cs-signals" aria-label="حالة المقرر">
            <span class="cs-signal cs-signal-live">مفعّل</span>
            <span class="cs-signal"><?php echo e($completedLessons); ?>/<?php echo e($totalLessons); ?> مكتمل</span>
            <span class="cs-signal cs-signal-hot"><?php echo e(number_format((float) ($coursePoints ?? 0), 0)); ?> نقطة</span>
        </div>
    </header>

    <section class="cs-stage cs-reveal" style="--reveal-delay:50ms" aria-label="تابع التعلّم">
        <div class="cs-stage-body">
            <div class="cs-eyebrow">
                خطوتك التالية
                <em><?php echo e($nextLessonTitle ? 'درس' : 'المنهج'); ?></em>
            </div>
            <h2><?php echo e($nextLessonTitle ?? 'ابدأ رحلتك في هذا المقرر'); ?></h2>
            <p class="cs-copy">
                <?php if($nextLessonTitle): ?>
                    أكمل من حيث توقفت — التقدّم الحالي <?php echo e($progress); ?>٪.
                <?php else: ?>
                    <?php echo e($course->description ? \Illuminate\Support\Str::limit(strip_tags($course->description), 140) : 'كل عناصر المنهج جاهزة. ادخل مساحة التعلّم للمتابعة.'); ?>

                <?php endif; ?>
            </p>
            <div class="cs-meter" role="progressbar" aria-valuenow="<?php echo e((int) $progress); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="تقدّم المقرر">
                <i style="width:<?php echo e(min(100, (float) $progress)); ?>%"></i>
            </div>
            <div class="cs-stage-actions">
                <a class="cs-btn" href="<?php echo e($learnUrl); ?>">
                    <i class="fas fa-play text-xs"></i>
                    <?php echo e($progress > 0 ? __('student.continue_learning') : 'ابدأ التعلّم'); ?>

                </a>
                <a class="cs-btn cs-btn-quiet" href="<?php echo e(route('my-courses.index')); ?>">العودة لمقرراتي</a>
            </div>
        </div>
        <div class="cs-cover" aria-hidden="true">
            <?php if($course->thumbnail): ?>
                <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="">
            <?php else: ?>
                <div class="ph">
                    <i class="fas fa-graduation-cap"></i>
                    <span style="font-size:12px;font-weight:700">مقرر</span>
                </div>
            <?php endif; ?>
            <span class="pct"><?php echo e($progress); ?>٪</span>
        </div>
    </section>

    <div class="cs-pulse cs-reveal" style="--reveal-delay:90ms" aria-label="ملخص التقدّم">
        <div>
            <span class="lbl">التقدّم</span>
            <span class="val teal"><?php echo e($progress); ?>٪</span>
        </div>
        <div>
            <span class="lbl">دروس مكتملة</span>
            <span class="val"><?php echo e($completedLessons); ?> / <?php echo e($totalLessons); ?></span>
        </div>
        <div>
            <span class="lbl">النقاط</span>
            <span class="val hot"><?php echo e(number_format((float) ($coursePoints ?? 0), 0)); ?></span>
        </div>
    </div>

    <div class="cs-split cs-reveal" style="--reveal-delay:120ms">
        <section class="cs-panel" aria-label="وصف المقرر">
            <p class="cs-label">عن المقرر</p>
            <p class="body"><?php echo e($course->description ?: 'لا يوجد وصف متاح لهذا المقرر بعد.'); ?></p>
        </section>
        <aside class="cs-panel" aria-label="معلومات المقرر">
            <p class="cs-label">تفاصيل</p>
            <ul class="cs-facts">
                <li>
                    <span class="k"><i class="fas fa-layer-group"></i> المستوى</span>
                    <span class="v"><?php echo e($course->level ?? '—'); ?></span>
                </li>
                <li>
                    <span class="k"><i class="fas fa-clock"></i> المدة</span>
                    <span class="v"><?php echo e($course->duration_hours ?? '—'); ?> ساعة</span>
                </li>
                <li>
                    <span class="k"><i class="fas fa-user-tie"></i> المدرّب</span>
                    <span class="v"><?php echo e($course->teacher->name ?? '—'); ?></span>
                </li>
            </ul>
        </aside>
    </div>

    <section class="cs-curriculum cs-reveal" style="--reveal-delay:160ms" aria-label="منهج المقرر">
        <p class="cs-label">منهج المقرر</p>

        <?php if(isset($sections) && $sections->count() > 0): ?>
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $visibleItems = $section->activeItems->filter(function ($curriculumItem) {
                        $item = $curriculumItem->item;
                        if (! $item) {
                            return false;
                        }
                        if ($item instanceof \App\Models\Lecture) {
                            return false;
                        }

                        return true;
                    });
                ?>
                <?php if($visibleItems->isEmpty()): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <div class="cs-section">
                    <div class="cs-section-h">
                        <strong><?php echo e($section->title); ?></strong>
                        <span><?php echo e($visibleItems->count()); ?> عناصر</span>
                    </div>
                    <?php if($section->description): ?>
                        <p class="cs-section-d"><?php echo e($section->description); ?></p>
                    <?php endif; ?>
                    <ul class="cs-items">
                        <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculumItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $item = $curriculumItem->item;
                                $isCompleted = false;
                                $isCurrent = false;
                                $isLocked = false;
                                $href = null;
                                $kind = 'عنصر';
                                $ico = 'lock';

                                if ($item instanceof \App\Models\CourseLesson) {
                                    $kind = 'درس';
                                    $lessonProgress = $item->progress->first();
                                    $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                    $previousItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                                    $allPreviousCompleted = true;
                                    foreach ($previousItems as $prevItem) {
                                        if ($prevItem->item instanceof \App\Models\CourseLesson) {
                                            $prevProgress = $prevItem->item->progress->first();
                                            if (! $prevProgress || ! $prevProgress->is_completed) {
                                                $allPreviousCompleted = false;
                                                break;
                                            }
                                        }
                                    }
                                    $isCurrent = ! $isCompleted && ($curriculumItem->order == 1 || $allPreviousCompleted);
                                    $isLocked = ! $isCurrent && ! $isCompleted;
                                    $ico = $isCompleted ? 'done' : ($isCurrent ? 'now' : 'lock');
                                    if (! $isLocked) {
                                        $href = route('my-courses.learn', $course).'?lesson='.$item->id;
                                    }
                                } elseif ($item instanceof \App\Models\Assignment) {
                                    $kind = 'واجب';
                                    $ico = 'task';
                                    $href = route('my-courses.learn', $course);
                                } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                                    $kind = 'امتحان';
                                    $ico = 'exam';
                                    $href = route('my-courses.learn', $course);
                                }
                                $rowClass = trim(($isCompleted ? 'is-done ' : '').($isCurrent ? 'is-now ' : '').($isLocked ? 'is-locked' : ''));
                            ?>
                            <?php if($href): ?>
                                <li>
                                    <a class="cs-item <?php echo e($rowClass); ?>" href="<?php echo e($href); ?>">
                                        <span class="cs-ico <?php echo e($ico); ?>">
                                            <?php if($ico === 'done'): ?>
                                                <i class="fas fa-check"></i>
                                            <?php elseif($ico === 'now'): ?>
                                                <i class="fas fa-play"></i>
                                            <?php elseif($ico === 'task'): ?>
                                                <i class="fas fa-tasks"></i>
                                            <?php elseif($ico === 'exam'): ?>
                                                <i class="fas fa-clipboard-check"></i>
                                            <?php else: ?>
                                                <i class="fas fa-lock"></i>
                                            <?php endif; ?>
                                        </span>
                                        <span class="t">
                                            <strong><?php echo e($item->title); ?></strong>
                                            <small>
                                                <?php echo e($kind); ?>

                                                <?php if($item instanceof \App\Models\CourseLesson && $item->duration_minutes): ?>
                                                    · <?php echo e($item->duration_minutes); ?> دقيقة
                                                <?php endif; ?>
                                            </small>
                                        </span>
                                        <span class="go"><?php echo e($isCompleted ? 'مراجعة' : 'فتح'); ?></span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div class="cs-item <?php echo e($rowClass); ?>">
                                        <span class="cs-ico <?php echo e($ico); ?>"><i class="fas fa-lock"></i></span>
                                        <span class="t">
                                            <strong><?php echo e($item->title); ?></strong>
                                            <small><?php echo e($kind); ?> · مقفل حتى إكمال السابق</small>
                                        </span>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php elseif($course->lessons->count() > 0): ?>
            <div class="cs-section">
                <div class="cs-section-h">
                    <strong>الدروس</strong>
                    <span><?php echo e($totalLessons); ?></span>
                </div>
                <ul class="cs-items">
                    <?php $__currentLoopData = $course->lessons->sortBy('order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $lessonProgress = $lesson->progress->first();
                            $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                            $isCurrentLesson = ! $isCompleted && ($index == 0 || $course->lessons->take($index)->every(function ($prevLesson) {
                                return $prevLesson->progress->isNotEmpty() && $prevLesson->progress->first()->is_completed;
                            }));
                            $isLocked = ! $isCurrentLesson && ! $isCompleted;
                            $ico = $isCompleted ? 'done' : ($isCurrentLesson ? 'now' : 'lock');
                            $rowClass = trim(($isCompleted ? 'is-done ' : '').($isCurrentLesson ? 'is-now ' : '').($isLocked ? 'is-locked' : ''));
                        ?>
                        <?php if(! $isLocked): ?>
                            <li>
                                <a class="cs-item <?php echo e($rowClass); ?>" href="<?php echo e(route('my-courses.learn', $course)); ?>?lesson=<?php echo e($lesson->id); ?>">
                                    <span class="cs-ico <?php echo e($ico); ?>">
                                        <i class="fas <?php echo e($isCompleted ? 'fa-check' : 'fa-play'); ?>"></i>
                                    </span>
                                    <span class="t">
                                        <strong><?php echo e($lesson->title); ?></strong>
                                        <small>درس · <?php echo e($lesson->duration_minutes ?? 0); ?> دقيقة</small>
                                    </span>
                                    <span class="go"><?php echo e($isCompleted ? 'مراجعة' : 'فتح'); ?></span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <div class="cs-item <?php echo e($rowClass); ?>">
                                    <span class="cs-ico lock"><i class="fas fa-lock"></i></span>
                                    <span class="t">
                                        <strong><?php echo e($lesson->title); ?></strong>
                                        <small>درس · مقفل</small>
                                    </span>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="cs-empty">لا يوجد منهج منشور لهذا المقرر بعد.</div>
        <?php endif; ?>
    </section>

    
    <div x-show="focusMode" 
         x-cloak
         class="focus-mode"
         :data-font-size="fontSize"
         @keydown.escape.window="focusMode = false"
         @keydown.ctrl.f.window.prevent="document.querySelector('.search-box input')?.focus()"
         @keydown.ctrl.p.window.prevent="printCurriculum()"
         @keydown.ctrl.comma.window.prevent="showSettings = !showSettings"
         x-init="
             $watch('searchQuery', () => filterItems());
             updateProgressBar();
             setInterval(() => updateProgressBar(), 100);
             document.body.style.overflow = 'hidden';
             $watch('focusMode', (value) => {
                 if (!value) {
                     document.body.style.overflow = '';
                 }
             });
         ">
        <!-- شريط التقدم -->
        <div class="focus-progress-bar">
            <div class="progress-fill" style="width: 0%"></div>
        </div>
        
        <div class="curriculum-wrapper">
            <!-- شريط التحكم العلوي -->
            <div class="focus-control-bar">
                <div class="controls">
                    <div class="flex items-center gap-4 flex-1">
                        <!-- زر السايدبار (للشاشات الصغيرة) -->
                        <button @click="sidebarOpen = !sidebarOpen" class="sidebar-toggle btn-control">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <!-- عنوان الكورس -->
                        <div class="flex items-center gap-3">
                            <h1 class="text-xl font-black text-gray-900"><?php echo e($course->localized('title')); ?></h1>
                            <span class="text-sm text-gray-500">|</span>
                            <span class="text-sm text-gray-600"><?php echo e($course->academicYear->name ?? 'غير محدد'); ?></span>
                        </div>
                    </div>
                    
                    <!-- أزرار التحكم -->
                    <div class="action-buttons">
                        <button @click="showSettings = !showSettings" 
                                :class="showSettings ? 'active' : ''"
                                class="btn-control"
                                title="إعدادات (Ctrl+,)">
                            <i class="fas fa-cog"></i>
                            <span class="hidden md:inline">إعدادات</span>
                        </button>
                        <button @click="toggleFullscreen()" class="btn-control">
                            <i class="fas fa-expand"></i>
                            <span class="hidden md:inline">ملء الشاشة</span>
                        </button>
                        <button @click="focusMode = false" class="btn-control btn-close">
                            <i class="fas fa-times"></i>
                            <span class="hidden md:inline">إغلاق</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- المحتوى الرئيسي -->
            <div class="flex flex-1 overflow-hidden relative" style="width: 100%;">
                <!-- المحتوى الرئيسي - على اليسار -->
                <div class="focus-main-content" style="width: 100%; flex: 1;">
                    <!-- زر إغلاق/فتح السايدبار -->
                    <button @click="sidebarClosed = !sidebarClosed" 
                            class="sidebar-toggle-btn"
                            :style="sidebarClosed ? 'right: 20px;' : 'right: 400px;'">
                        <i class="fas" :class="sidebarClosed ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                    <div class="focus-main-content-wrapper">
                        <!-- حالة فارغة -->
                        <div x-show="!selectedLesson" class="empty-content-state">
                            <i class="fas fa-graduation-cap"></i>
                            <h3 class="text-xl font-black text-gray-900 mb-2 mt-4">مرحباً في <?php echo e($course->localized('title')); ?></h3>
                            <p class="text-sm text-gray-600">اختر درساً من القائمة الجانبية أو استخدم «ابدأ التعلم» من الصفحة الرئيسية للكورس</p>
                        </div>
                        
                        <div x-show="selectedLesson" x-transition class="lesson-content-viewer">
                            <div x-html="lessonContent"></div>
                        </div>
                    </div>
                </div>
                
                <!-- السايدبار - المنهج الكامل على اليمين -->
                <div class="focus-sidebar" :class="{ 'closed': sidebarClosed, 'open': sidebarOpen }">
                    <button @click="sidebarClosed = true" class="sidebar-close-btn" title="إغلاق السايدبار">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="focus-sidebar-header">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-white font-black text-lg">المنهج الكامل</h3>
                            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- البحث -->
                        <div class="search-box relative">
                            <input type="text" 
                                   x-model="searchQuery"
                                   placeholder="ابحث في الدروس..."
                                   class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-400 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-[#2CA9BD] focus:bg-white/20"
                                   @keydown.escape="searchQuery = ''">
                            <div class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="focus-sidebar-content">
                        <?php if(isset($sections) && $sections->count() > 0): ?>
                            <!-- عرض المنهج من الأقسام -->
                            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-6">
                                    <div class="curriculum-section-header mb-3">
                                        <i class="fas fa-folder ml-2"></i>
                                        <?php echo e($section->title); ?>

                                    </div>
                                    <?php if($section->description): ?>
                                        <p class="text-xs text-gray-400 mb-3 px-2"><?php echo e($section->description); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php $__currentLoopData = $section->activeItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculumItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $item = $curriculumItem->item;
                                            // تخطي العناصر المحذوفة
                                            if (!$item) continue;
                                            // المحاضرات لا تُعرض للطلاب من هذه الصفحة (التعلم عبر «ابدأ التعلم» فقط)
                                            if ($item instanceof \App\Models\Lecture) continue;
                                            
                                            $isCompleted = false;
                                            $isCurrent = false;
                                            $isLocked = false;
                                            
                                            if ($item instanceof \App\Models\CourseLesson) {
                                                $lessonProgress = $item->progress->first();
                                                $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                                // التحقق من الدروس السابقة
                                                $previousItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                                                $allPreviousCompleted = true;
                                                foreach ($previousItems as $prevItem) {
                                                    if ($prevItem->item instanceof \App\Models\CourseLesson) {
                                                        $prevProgress = $prevItem->item->progress->first();
                                                        if (!$prevProgress || !$prevProgress->is_completed) {
                                                            $allPreviousCompleted = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                                $isCurrent = !$isCompleted && ($curriculumItem->order == 1 || $allPreviousCompleted);
                                                $isLocked = !$isCurrent && !$isCompleted;
                                            }
                                        ?>
                                        
                                        <div class="curriculum-item <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isCurrent ? 'active' : ''); ?> <?php echo e($isLocked ? 'locked' : ''); ?>"
                                             <?php if($item instanceof \App\Models\CourseLesson): ?>
                                                 @click="if (<?php echo e($isLocked ? 'true' : 'false'); ?>) return; selectedLesson = <?php echo e($item->id); ?>; loadLesson(<?php echo e($item->id); ?>)"
                                             <?php elseif($item instanceof \App\Models\Assignment): ?>
                                                 @click="loadAssignment(<?php echo e($item->id); ?>)"
                                             <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                                                 @click="loadExam(<?php echo e($item->id); ?>)"
                                             <?php endif; ?>
                                             x-show="!searchQuery || '<?php echo e(strtolower($item->title)); ?>'.includes(searchQuery.toLowerCase())">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-1">
                                                    <?php if($item instanceof \App\Models\CourseLesson): ?>
                                                        <?php if($isCompleted): ?>
                                                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-check text-white text-xs"></i>
                                                            </div>
                                                        <?php elseif($isCurrent): ?>
                                                            <div class="w-8 h-8 bg-[#2CA9BD] rounded-lg flex items-center justify-center animate-pulse">
                                                                <i class="fas fa-play text-white text-xs"></i>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-lock text-white text-xs"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php elseif($item instanceof \App\Models\Assignment): ?>
                                                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-tasks text-white text-xs"></i>
                                                        </div>
                                                    <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                                                        <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-clipboard-check text-white text-xs"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="curriculum-item-title"><?php echo e($item->title); ?></div>
                                                    <div class="curriculum-item-meta">
                                                        <?php if($item instanceof \App\Models\CourseLesson): ?>
                                                            <span><i class="fas fa-video ml-1"></i> درس</span>
                                                            <?php if($item->duration_minutes): ?>
                                                                <span><i class="fas fa-clock ml-1"></i> <?php echo e($item->duration_minutes); ?> دقيقة</span>
                                                            <?php endif; ?>
                                                        <?php elseif($item instanceof \App\Models\Assignment): ?>
                                                            <span><i class="fas fa-tasks ml-1"></i> واجب</span>
                                                            <?php if($item->due_date): ?>
                                                                <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->due_date->format('Y/m/d')); ?></span>
                                                            <?php endif; ?>
                                                        <?php elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam): ?>
                                                            <span><i class="fas fa-clipboard-check ml-1"></i> امتحان</span>
                                                            <?php if($item->start_date): ?>
                                                                <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->start_date->format('Y/m/d')); ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <!-- عرض الدروس القديمة (للتوافق) -->
                            <div class="curriculum-section-header">
                                <i class="fas fa-book ml-2"></i>
                                الدروس (<?php echo e($totalLessons); ?>)
                            </div>
                            <?php $__currentLoopData = $course->lessons->sortBy('order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $lessonProgress = $lesson->progress->first();
                                    $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                    $isCurrentLesson = !$isCompleted && ($index == 0 || $course->lessons->take($index)->every(function($prevLesson) {
                                        return $prevLesson->progress->isNotEmpty() && $prevLesson->progress->first()->is_completed;
                                    }));
                                ?>
                                <div class="curriculum-item <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isCurrentLesson ? 'active' : ''); ?> <?php echo e(!$isCurrentLesson && !$isCompleted ? 'locked' : ''); ?>"
                                     @click="if (!$isCurrentLesson && !$isCompleted) return; selectedLesson = <?php echo e($lesson->id); ?>; loadLesson(<?php echo e($lesson->id); ?>)"
                                     x-show="!searchQuery || '<?php echo e(strtolower($lesson->title)); ?>'.includes(searchQuery.toLowerCase())">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <?php if($isCompleted): ?>
                                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            <?php elseif($isCurrentLesson): ?>
                                                <div class="w-8 h-8 bg-[#2CA9BD] rounded-lg flex items-center justify-center animate-pulse">
                                                    <i class="fas fa-play text-white text-xs"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-lock text-white text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="curriculum-item-title"><?php echo e($lesson->title); ?></div>
                                            <div class="curriculum-item-meta">
                                                <span><i class="fas fa-clock ml-1"></i> <?php echo e($lesson->duration_minutes ?? 0); ?> دقيقة</span>
                                                <?php if($lesson->video_url): ?>
                                                    <span><i class="fas fa-video ml-1"></i> فيديو</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
        
        <!-- لوحة الإعدادات -->
        <div class="focus-settings-panel" :class="{ 'active': showSettings }">
            <div class="mb-4 pb-4 border-b border-gray-700">
                <h3 class="text-white font-bold text-lg mb-2">
                    <i class="fas fa-cog ml-2"></i>
                    إعدادات العرض
                </h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-gray-300 text-sm mb-2 block flex items-center gap-2">
                        <i class="fas fa-font"></i>
                        حجم الخط
                    </label>
                    <div class="flex gap-2">
                        <button @click="fontSize = 'small'" 
                                :class="fontSize === 'small' ? 'bg-blue-600 border-blue-400' : 'bg-gray-700 border-gray-600'"
                                class="px-3 py-1.5 rounded text-white text-sm border transition-all">صغير</button>
                        <button @click="fontSize = 'medium'" 
                                :class="fontSize === 'medium' ? 'bg-blue-600 border-blue-400' : 'bg-gray-700 border-gray-600'"
                                class="px-3 py-1.5 rounded text-white text-sm border transition-all">متوسط</button>
                        <button @click="fontSize = 'large'" 
                                :class="fontSize === 'large' ? 'bg-blue-600 border-blue-400' : 'bg-gray-700 border-gray-600'"
                                class="px-3 py-1.5 rounded text-white text-sm border transition-all">كبير</button>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-700">
                    <p class="text-gray-400 text-xs mb-2">اختصارات لوحة المفاتيح:</p>
                    <div class="space-y-1 text-xs text-gray-400">
                        <div class="flex justify-between">
                            <span>البحث:</span>
                            <kbd class="px-2 py-0.5 bg-gray-700 rounded text-gray-300">Ctrl+F</kbd>
                        </div>
                        <div class="flex justify-between">
                            <span>الطباعة:</span>
                            <kbd class="px-2 py-0.5 bg-gray-700 rounded text-gray-300">Ctrl+P</kbd>
                        </div>
                        <div class="flex justify-between">
                            <span>الإعدادات:</span>
                            <kbd class="px-2 py-0.5 bg-gray-700 rounded text-gray-300">Ctrl+,</kbd>
                        </div>
                        <div class="flex justify-between">
                            <span>إغلاق:</span>
                            <kbd class="px-2 py-0.5 bg-gray-700 rounded text-gray-300">ESC</kbd>
                        </div>
                    </div>
                </div>
                <button @click="showSettings = false" 
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded mt-4">
                    <i class="fas fa-times ml-2"></i>
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function courseFocusMode() {
    return {
        focusMode: false,
        searchQuery: '',
        fontSize: 'medium',
        showSettings: false,
        collapsedSections: [],
        sidebarOpen: false,
        sidebarClosed: false,
        selectedLesson: null,
        lessonContent: '',
        loadLesson(lessonId) {
            const lessonUrl = '<?php echo e(route('my-courses.lesson.watch', [$course, ':lessonId'])); ?>'.replace(':lessonId', lessonId);
            window.open(lessonUrl, '_blank');
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
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        },
        updateProgressBar() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = (scrollTop / scrollHeight) * 100;
            const progressBar = document.querySelector('.progress-fill');
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }
    };
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\show.blade.php ENDPATH**/ ?>