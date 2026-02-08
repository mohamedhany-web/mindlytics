<?php $__env->startSection('title', $course->title . ' - كورساتي'); ?>
<?php $__env->startSection('header', $course->title); ?>

<?php $__env->startPush('styles'); ?>
<style>
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
    
    /* تحسينات إضافية */
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
<?php
    $lecturesData = $course->lectures->map(function($lecture) {
        return [
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'recording_url' => $lecture->recording_url,
            'video_platform' => $lecture->video_platform,
            'scheduled_at' => $lecture->scheduled_at->format('Y-m-d H:i:s'),
            'scheduled_at_formatted' => $lecture->scheduled_at->format('Y/m/d'),
            'scheduled_at_time' => $lecture->scheduled_at->format('H:i'),
            'duration_minutes' => $lecture->duration_minutes ?? 60,
            'teams_meeting_link' => $lecture->teams_meeting_link,
            'teams_registration_link' => $lecture->teams_registration_link,
            'notes' => $lecture->notes
        ];
    })->keyBy('id');
    $lecturesDataJson = json_encode($lecturesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="min-h-screen bg-gray-50 py-6" 
     data-lectures='<?php echo $lecturesDataJson; ?>'
     x-data="courseFocusMode()"
     @scroll.window="updateProgressBar()">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- العودة -->
        <div class="mb-4">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة إلى كورساتي
            </a>
        </div>

        <!-- معلومات الكورس - عرض كامل -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- صورة الكورس -->
                <div class="lg:w-2/5 h-52 lg:h-72 bg-sky-100 flex items-center justify-center relative overflow-hidden flex-shrink-0">
                    <?php if($course->thumbnail): ?>
                        <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="text-sky-600 text-center">
                            <i class="fas fa-graduation-cap text-4xl"></i>
                            <p class="text-sm font-medium mt-2 text-sky-700"><?php echo e($course->academicSubject->name ?? 'كورس'); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-3 left-3 bg-white rounded-lg px-3 py-1.5 shadow-sm border border-gray-100">
                        <span class="text-sm font-bold text-sky-600"><?php echo e($progress); ?>%</span>
                    </div>
                </div>

                <!-- تفاصيل الكورس -->
                <div class="lg:flex-1 p-5 sm:p-6 lg:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 leading-tight"><?php echo e($course->title); ?></h1>
                            <p class="text-sm text-gray-500">
                                <?php echo e($course->academicYear->name ?? '—'); ?> · <?php echo e($course->academicSubject->name ?? '—'); ?> · <?php echo e($course->teacher->name ?? '—'); ?>

                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-white">
                            <i class="fas fa-check-circle"></i> مفعل
                        </span>
                    </div>

                    <?php if($course->description): ?>
                        <p class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-2"><?php echo e(Str::limit($course->description, 180)); ?></p>
                    <?php endif; ?>

                    <!-- التقدم والإحصائيات -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 mb-5">
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="font-medium text-gray-600">التقدم</span>
                                <span class="font-bold text-sky-600"><?php echo e($progress); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: <?php echo e(min($progress, 100)); ?>%;"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($completedLessons); ?> من <?php echo e($totalLessons); ?> درس</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-center px-4 py-2 bg-sky-50 rounded-lg border border-sky-100">
                                <span class="text-lg font-bold text-sky-600 block"><?php echo e($totalLessons); ?></span>
                                <span class="text-xs text-gray-600">دروس</span>
                            </div>
                            <div class="text-center px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                <span class="text-lg font-bold text-emerald-600 block"><?php echo e($completedLessons); ?></span>
                                <span class="text-xs text-gray-600">مكتمل</span>
                            </div>
                            <div class="text-center px-4 py-2 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-lg font-bold text-gray-700 block"><?php echo e($course->lectures->count()); ?></span>
                                <span class="text-xs text-gray-600">محاضرة</span>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo e(route('my-courses.learn', $course)); ?>" 
                       class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-lg font-semibold text-sm transition-colors">
                        <i class="fas fa-play"></i>
                        ابدأ التعلم
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs - عرض كامل -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <nav class="flex border-b border-gray-200 overflow-x-auto scrollbar-hide">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'border-sky-500 text-sky-600 bg-sky-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-info-circle"></i>
                    نظرة عامة
                </button>
                <button @click="activeTab = 'lessons'" 
                        :class="activeTab === 'lessons' ? 'border-sky-500 text-sky-600 bg-sky-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-book"></i>
                    الدروس
                    <span class="bg-sky-500 text-white text-xs px-2 py-0.5 rounded-full font-medium"><?php echo e($totalLessons); ?></span>
                </button>
                <button @click="activeTab = 'lectures'" 
                        :class="activeTab === 'lectures' ? 'border-sky-500 text-sky-600 bg-sky-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-chalkboard-teacher"></i>
                    المحاضرات
                    <span class="bg-sky-500 text-white text-xs px-2 py-0.5 rounded-full font-medium"><?php echo e($course->lectures->count()); ?></span>
                </button>
            </nav>

            <div class="px-3 py-5 sm:px-6 sm:py-6 lg:p-8">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-sky-500"></i>
                                وصف الكورس
                            </h3>
                            <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($course->description ?? 'لا يوجد وصف متاح'); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-list-ul text-sky-500"></i>
                                معلومات الكورس
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-layer-group text-sky-500 w-4"></i> المستوى</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo e($course->level ?? '—'); ?></span>
                                </div>
                                <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-clock text-sky-500 w-4"></i> المدة</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo e($course->duration_hours); ?> ساعة</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-book text-sky-500 w-4"></i> الدروس</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo e($totalLessons); ?></span>
                                </div>
                                <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-sky-500 w-4"></i> المحاضرات</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo e($course->lectures->count()); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lessons Tab -->
                <div x-show="activeTab === 'lessons'" x-transition>
                    <?php echo $__env->make('student.my-courses.partials.lessons-list', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Lectures Tab -->
                <div x-show="activeTab === 'lectures'" x-transition>
                    <?php echo $__env->make('student.my-courses.partials.lectures-list', ['course' => $course, 'lecturesByLesson' => $lecturesByLesson ?? collect()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Focus Mode Modal - وضع التركيز المتقدم -->
    <div x-show="focusMode" 
         x-cloak
         class="focus-mode"
         :data-font-size="fontSize"
         @keydown.escape.window="focusMode = false"
         @keydown.ctrl.f.window.prevent="document.querySelector('.search-box input')?.focus()"
         @keydown.ctrl.p.window.prevent="printCurriculum()"
         @keydown.ctrl.comma.window.prevent="showSettings = !showSettings"
         x-init="
             console.log('Focus mode initialized');
             $watch('searchQuery', () => filterItems());
             updateProgressBar();
             setInterval(() => updateProgressBar(), 100);
             // منع التمرير في body عند فتح وضع التركيز
             document.body.style.overflow = 'hidden';
             $watch('focusMode', (value) => {
                 if (value) {
                     console.log('Focus mode opened, lecturesData:', window.lecturesData);
                 } else {
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
                            <h1 class="text-xl font-black text-gray-900"><?php echo e($course->title); ?></h1>
                            <span class="text-sm text-gray-500">|</span>
                            <span class="text-sm text-gray-600"><?php echo e($course->academicYear->name ?? 'غير محدد'); ?> - <?php echo e($course->academicSubject->name ?? 'غير محدد'); ?></span>
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
                        <!-- حالة فارغة - اختر درساً أو محاضرة من السايدبار -->
                        <div x-show="!selectedLesson && !selectedLecture" class="empty-content-state">
                            <i class="fas fa-graduation-cap"></i>
                            <h3 class="text-xl font-black text-gray-900 mb-2 mt-4">مرحباً في <?php echo e($course->title); ?></h3>
                            <p class="text-sm text-gray-600">اختر درساً أو محاضرة من القائمة الجانبية لبدء التعلم</p>
                        </div>
                        
                        <!-- محتوى الدرس المحدد -->
                        <div x-show="selectedLesson && !selectedLecture" x-transition class="lesson-content-viewer">
                            <div x-html="lessonContent"></div>
                        </div>
                        
                        <!-- محتوى المحاضرة المحددة -->
                        <div x-show="selectedLecture" x-transition class="lesson-content-viewer">
                            <div x-html="lectureContent"></div>
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
                                             <?php elseif($item instanceof \App\Models\Lecture): ?>
                                                 @click="loadLecture(<?php echo e($item->id); ?>)"
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
                                                    <?php elseif($item instanceof \App\Models\Lecture): ?>
                                                        <div class="w-8 h-8 <?php echo e($item->status === 'completed' ? 'bg-green-500' : ($item->status === 'in_progress' ? 'bg-yellow-500' : 'bg-blue-500')); ?> rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-chalkboard-teacher text-white text-xs"></i>
                                                        </div>
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
                                                        <?php elseif($item instanceof \App\Models\Lecture): ?>
                                                            <span><i class="fas fa-chalkboard-teacher ml-1"></i> محاضرة</span>
                                                            <?php if($item->scheduled_at): ?>
                                                                <span><i class="fas fa-calendar ml-1"></i> <?php echo e($item->scheduled_at->format('Y/m/d')); ?></span>
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
                <div>
                    <label class="text-gray-300 text-sm mb-2 block">عرض العناصر</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-gray-300 text-sm">
                            <input type="checkbox" x-model="showLessons" class="rounded">
                            <span>إظهار الدروس</span>
                        </label>
                        <label class="flex items-center gap-2 text-gray-300 text-sm">
                            <input type="checkbox" x-model="showLectures" class="rounded">
                            <span>إظهار المحاضرات</span>
                        </label>
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
    // قراءة البيانات من data attribute
    const element = document.querySelector('[data-lectures]');
    let lecturesData = {};
    
    if (element && element.dataset.lectures) {
        try {
            lecturesData = JSON.parse(element.dataset.lectures);
            console.log('Lectures data loaded:', Object.keys(lecturesData).length, 'lectures');
        } catch (e) {
            console.error('Error parsing lectures data:', e);
            lecturesData = {};
        }
    }
    
    return {
        focusMode: false,
        searchQuery: '',
        showLessons: true,
        showLectures: true,
        fontSize: 'medium',
        showSettings: false,
        collapsedSections: [],
        sidebarOpen: false,
        sidebarClosed: false,
        selectedLesson: null,
        selectedLecture: null,
        lessonContent: '',
        lectureContent: '',
        lecturesData: lecturesData,
        activeTab: (() => {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            return tab && ['overview', 'lessons', 'lectures'].includes(tab) ? tab : 'overview';
        })(),
        loadLesson(lessonId) {
            const lessonUrl = '<?php echo e(route('my-courses.lesson.watch', [$course, ':lessonId'])); ?>'.replace(':lessonId', lessonId);
            window.open(lessonUrl, '_blank');
        },
        loadLecture(lectureId) {
            this.selectedLecture = lectureId;
            this.selectedLesson = null;
            
            const lectures = this.lecturesData || {};
            const lectureIdStr = String(lectureId);
            const lectureIdNum = parseInt(lectureId);
            let lecture = lectures[lectureIdStr] || lectures[lectureIdNum] || lectures[lectureId];
            
            if (!lecture) {
                console.error('Lecture not found:', lectureId, 'Available:', Object.keys(lectures));
                this.lectureContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="text-xl font-bold">المحاضرة غير موجودة</p></div>';
                return;
            }
            
            // بناء محتوى HTML
            let html = '<div class="lecture-viewer space-y-6 w-full">';
            
            // العنوان والوصف
            html += '<div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border-2 border-blue-200 w-full">';
            html += '<h2 class="text-3xl font-black text-gray-900 mb-4">' + this.escapeHtml(lecture.title) + '</h2>';
            if (lecture.description) {
                html += '<p class="text-gray-700 leading-relaxed mb-4">' + this.escapeHtml(lecture.description) + '</p>';
            }
            html += '<div class="grid grid-cols-2 gap-4 text-sm">';
            html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-calendar text-[#2CA9BD]"></i><span class="font-semibold">التاريخ:</span> ' + (lecture.scheduled_at_formatted || '') + '</div>';
            html += '<div class="flex items-center gap-2 text-gray-600"><i class="fas fa-clock text-[#2CA9BD]"></i><span class="font-semibold">المدة:</span> ' + (lecture.duration_minutes || 60) + ' دقيقة</div>';
            html += '</div></div>';
            
            // الفيديو
            if (lecture.recording_url) {
                const videoHtml = this.generateVideoHtml(lecture.recording_url, lecture.video_platform);
                if (videoHtml) {
                    html += '<div class="bg-black rounded-xl overflow-hidden w-full" style="position: relative; padding-bottom: 56.25%; height: 0;">';
                    html += '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">';
                    html += videoHtml;
                    html += '</div></div>';
                } else {
                    html += '<div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 text-center w-full">';
                    html += '<i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-3"></i>';
                    html += '<p class="text-yellow-800 font-semibold">لا يمكن تحميل الفيديو</p></div>';
                }
            } else {
                html += '<div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 text-center w-full">';
                html += '<i class="fas fa-video text-gray-400 text-3xl mb-3"></i>';
                html += '<p class="text-gray-600 font-semibold">لا يوجد فيديو متاح لهذه المحاضرة</p></div>';
            }
            
            // روابط Teams
            if (lecture.teams_meeting_link || lecture.teams_registration_link) {
                html += '<div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 w-full">';
                html += '<h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-video text-[#2CA9BD]"></i><span>روابط Microsoft Teams</span></h3>';
                html += '<div class="space-y-3">';
                if (lecture.teams_meeting_link) {
                    html += '<a href="' + this.escapeHtml(lecture.teams_meeting_link) + '" target="_blank" class="block bg-white border-2 border-blue-300 rounded-lg p-4 hover:bg-blue-50 transition-all hover:shadow-lg w-full"><div class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="fas fa-video text-[#2CA9BD] text-xl"></i><div><div class="font-bold text-gray-900">رابط الاجتماع</div><div class="text-sm text-gray-600">انقر للانضمام</div></div></div><i class="fas fa-external-link-alt text-gray-400"></i></div></a>';
                }
                if (lecture.teams_registration_link) {
                    html += '<a href="' + this.escapeHtml(lecture.teams_registration_link) + '" target="_blank" class="block bg-white border-2 border-blue-300 rounded-lg p-4 hover:bg-blue-50 transition-all hover:shadow-lg w-full"><div class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="fas fa-user-plus text-[#2CA9BD] text-xl"></i><div><div class="font-bold text-gray-900">رابط التسجيل</div><div class="text-sm text-gray-600">سجل للانضمام</div></div></div><i class="fas fa-external-link-alt text-gray-400"></i></div></a>';
                }
                html += '</div></div>';
            }
            
            // الملاحظات
            if (lecture.notes) {
                html += '<div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 w-full">';
                html += '<h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-sticky-note text-[#2CA9BD]"></i><span>ملاحظات</span></h3>';
                html += '<div class="text-gray-700 leading-relaxed whitespace-pre-wrap">' + this.escapeHtml(lecture.notes) + '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            this.lectureContent = html;
        },
        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/my-courses/show.blade.php ENDPATH**/ ?>