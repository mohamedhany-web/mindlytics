@extends('layouts.app')

@section('title', $course->title . ' - ابدأ التعلم')
@section('header', '')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
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
    
    /* Focus Mode - وضع التركيز المتقدم (ثيم داكن كما في التصميم) */
    .focus-mode {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #0f172a;
        z-index: 99999;
        overflow: hidden;
        padding: 0;
        animation: focusFadeIn 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        width: 100vw;
        height: 100vh;
        box-sizing: border-box;
    }
    
    /* إخفاء السايدبار الرئيسي في وضع التركيز */
    body:has(.focus-mode) .student-sidebar,
    body:has(.focus-mode) aside {
        display: none !important;
    }
    
    body:has(.focus-mode) .flex.h-screen {
        padding: 0 !important;
    }
    
    body:has(.focus-mode) .flex.flex-col.flex-1 {
        width: 100vw !important;
        max-width: 100vw !important;
    }
    
    /* سايدبار المنهج */
    .focus-sidebar {
        width: 340px;
        min-width: 340px;
        max-width: 340px;
        background: linear-gradient(180deg, #0f172a 0%, #0c1222 50%, #020617 100%);
        border-right: 1px solid rgba(14, 165, 233, 0.2);
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        transition: transform 0.25s ease, width 0.25s ease, opacity 0.25s ease;
        order: 1;
        flex-shrink: 0;
        height: 100%;
        box-shadow: 8px 0 32px rgba(0,0,0,0.3);
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }
    
    /* السايدبار مغلق */
    .focus-sidebar.closed {
        width: 0 !important;
        min-width: 0 !important;
        max-width: 0 !important;
        padding: 0;
        border: none;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        margin: 0;
    }
    
    @media (max-width: 1024px) {
        .focus-sidebar.closed {
            width: 0 !important;
            transform: translateX(-100%);
        }
    }
    
    .focus-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .focus-sidebar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }
    
    .focus-sidebar::-webkit-scrollbar-thumb {
        background: rgba(14, 165, 233, 0.4);
        border-radius: 3px;
    }
    .focus-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(14, 165, 233, 0.6);
    }
    .focus-sidebar-header {
        padding: 1.125rem 1.25rem;
        background: rgba(15, 23, 42, 0.98);
        border-bottom: 1px solid rgba(14, 165, 233, 0.15);
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(12px);
    }
    
    @media (max-width: 640px) {
        .focus-sidebar-header {
            padding: 0.875rem 1rem;
        }
    }
    
    .focus-sidebar-content {
        padding: 0.65rem 0.85rem;
    }
    
    @media (max-width: 640px) {
        .focus-sidebar-content {
            padding: 0.6rem 0.75rem;
        }
    }
    
    /* المحتوى الرئيسي - على اليسار (ثيم داكن) */
    .focus-main-content {
        flex: 1;
        overflow: hidden;
        background: #0f172a;
        position: relative;
        order: 1;
        min-height: 0;
        width: 100%;
        height: 100%;
        transition: width 0.3s ease, margin-left 0.3s ease;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }
    
    @media (min-width: 1024px) {
        .focus-sidebar:not(.closed) ~ .focus-main-content,
        .focus-sidebar:not(.closed) + .focus-main-content {
            width: calc(100% - 340px);
        }
    }
    
    /* عندما يكون السايدبار مغلق، المحتوى يملأ الصفحة */
    .curriculum-wrapper {
        width: 100%;
        display: flex;
        height: 100%;
        overflow: hidden;
    }
    
    .curriculum-wrapper .focus-main-content {
        flex: 1;
        min-width: 0;
        transition: width 0.3s ease, margin-left 0.3s ease;
    }
    
    @media (min-width: 1024px) {
        .focus-sidebar.closed ~ .focus-main-content,
        .focus-sidebar.closed + .focus-main-content {
            width: 100% !important;
            margin-left: 0 !important;
            flex: 1 !important;
        }
    }
    
    .focus-main-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .focus-main-content::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
    }
    
    .focus-main-content::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.8);
        border-radius: 4px;
    }
    
    .focus-main-content::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.9);
    }
    
    /* عناصر المنهج في السايدبار */
    .curriculum-item {
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid rgba(51, 65, 85, 0.45);
        border-radius: 8px;
        padding: 0.6rem 0.75rem;
        margin-bottom: 0.4rem;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        touch-action: manipulation;
    }
    @media (max-width: 640px) {
        .curriculum-item { padding: 0.55rem 0.7rem; margin-bottom: 0.35rem; border-radius: 6px; }
    }
    .curriculum-item:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: rgba(14, 165, 233, 0.35);
        transform: translateX(-2px);
    }
    .curriculum-item.active {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(6, 182, 212, 0.1) 100%);
        border-color: rgba(14, 165, 233, 0.6);
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.15);
    }
    .curriculum-item.completed {
        border-color: rgba(16, 185, 129, 0.4);
        background: rgba(16, 185, 129, 0.1);
    }
    .curriculum-item.locked {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .curriculum-item.locked:hover {
        transform: none;
    }
    
    /* زر التبديل - مدمج مع السايدبار (على الحافة اليمنى للسايدبار عندما يكون على اليسار) */
    .focus-sidebar .sidebar-toggle-btn {
        position: absolute;
        top: 50%;
        right: -20px;
        transform: translateY(-50%);
        z-index: 1000;
        background: #0f172a;
        border: 1px solid rgba(14, 165, 233, 0.35);
        border-left: none;
        color: rgb(148 163 184);
        width: 36px;
        height: 56px;
        border-radius: 0 8px 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 2px 0 8px rgba(0,0,0,0.2);
        touch-action: manipulation;
    }
    .focus-sidebar .sidebar-toggle-btn:hover {
        background: #1e293b;
        border-color: rgb(14 165 233);
        color: rgb(56 189 248);
    }
    
    @media (max-width: 1024px) {
        .focus-sidebar .sidebar-toggle-btn {
            display: none;
        }
    }
    
    /* عندما يكون السايدبار مغلق، الزر يظهر على اليسار من المحتوى (لفتح السايدبار) */
    .focus-main-content .sidebar-toggle-btn {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 0 8px 8px 0;
        border-left: 1px solid rgba(14, 165, 233, 0.35);
        border-right: none;
    }
    
    .focus-main-content .sidebar-toggle-btn:hover {
        left: -5px;
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
    
    /* ——— الجوال: سايدبار ملء الشاشة مع انتقالات سلسة ——— */
    @media (max-width: 1024px) {
        .focus-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100001;
            transform: translateX(-100%);
            width: min(100vw, 360px) !important;
            max-width: none;
            min-width: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
            will-change: transform;
            box-shadow: none;
            border-radius: 0;
            /* دعم الشقوق والأجهزة ذات الحواف */
            padding-left: env(safe-area-inset-left, 0);
        }
        .focus-sidebar.open {
            transform: translateX(0);
            box-shadow: 8px 0 32px rgba(0, 0, 0, 0.4);
        }
        
        .focus-main-content {
            width: 100% !important;
            margin-left: 0 !important;
        }
        
        .sidebar-toggle-btn {
            display: block;
        }
        
        .focus-control-bar {
            padding: 0.75rem 1rem !important;
            padding-left: max(1rem, env(safe-area-inset-left));
            padding-right: max(1rem, env(safe-area-inset-right));
        }
        
        .focus-control-bar .controls {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .focus-control-bar h1 {
            font-size: 1rem !important;
        }
        
        .focus-control-bar .action-buttons {
            flex-wrap: wrap;
        }
        
        .focus-control-bar .btn-control span {
            display: none;
        }
        
        .focus-main-content-wrapper {
            padding: 1rem !important;
        }
        
        .lesson-content-viewer,
        .lecture-viewer {
            padding: 1rem !important;
        }
        
        /* منع تمرير الخلفية عند فتح السايدبار على الجوال */
        body.sidebar-mobile-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
        }
    }
    
    @media (max-width: 640px) {
        .focus-sidebar {
            width: 100vw !important;
            max-width: 100vw !important;
        }
        
        .focus-sidebar-header {
            padding-top: max(0.875rem, env(safe-area-inset-top));
        }
        
        .sidebar-close-btn {
            top: max(1rem, env(safe-area-inset-top));
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
        }
        
        .focus-control-bar {
            padding: 0.625rem 0.75rem !important;
            min-height: 52px;
        }
        
        .focus-control-bar h1 {
            font-size: 0.875rem !important;
        }
        
        .curriculum-item {
            padding: 0.875rem 1rem !important;
            min-height: 48px;
        }
        
        .curriculum-item-title {
            font-size: 0.8125rem !important;
        }
        
        .curriculum-item-meta {
            font-size: 0.625rem !important;
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
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
        width: 100vw;
    }
    
    .focus-mode .curriculum-wrapper > .flex {
        flex: 1;
        min-height: 0;
        overflow: hidden;
        width: 100%;
    }
    
    /* منع التمرير الأفقي */
    html:has(.focus-mode),
    body:has(.focus-mode) {
        overflow: hidden !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }
    
    .focus-mode .focus-control-bar {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        border-bottom: 1px solid rgba(14, 165, 233, 0.15);
        padding: 0.875rem 1.25rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        position: relative;
        flex-shrink: 0;
        z-index: 100;
        width: 100%;
        box-sizing: border-box;
        height: auto;
        min-height: 64px;
    }
    
    @media (max-width: 640px) {
        .focus-mode .focus-control-bar {
            padding: 0.75rem 1rem;
            min-height: 58px;
        }
    }
    
    .focus-mode .focus-control-bar .controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        width: 100%;
        flex-wrap: wrap;
    }
    
    @media (max-width: 640px) {
        .focus-mode .focus-control-bar .controls {
            gap: 0.5rem;
        }
    }
    
    .focus-mode .focus-control-bar .search-box {
        position: relative;
        max-width: 300px;
        flex: 1;
        min-width: 200px;
    }
    
    @media (max-width: 768px) {
        .focus-mode .focus-control-bar .search-box {
            display: none;
        }
    }
    
    .focus-mode .focus-control-bar .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 0.5rem;
        background: white;
        font-size: 0.875rem;
    }
    
    .focus-mode .focus-control-bar .search-box input:focus {
        outline: none;
        border-color: rgb(14 165 233);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }
    .focus-mode .focus-control-bar .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    
    .focus-mode .focus-control-bar .btn-control {
        padding: 0.5rem 0.875rem;
        background: rgba(51, 65, 85, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 10px;
        color: rgb(226 232 240);
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }
    @media (max-width: 640px) {
        .focus-mode .focus-control-bar .btn-control {
            padding: 0.5rem 0.65rem;
            min-width: 42px;
            justify-content: center;
            border-radius: 8px;
        }
    }
    .focus-mode .focus-control-bar .btn-close {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #f87171;
    }
    
    .focus-mode .focus-control-bar .btn-close:hover {
        background: rgba(239, 68, 68, 0.25);
        border-color: #ef4444;
    }
    .focus-mode .focus-control-bar .btn-control:hover {
        background: rgba(14, 165, 233, 0.15);
        border-color: rgba(14, 165, 233, 0.4);
        color: rgb(186 230 253);
    }
    .focus-mode .focus-control-bar .btn-control.active {
        background: rgba(14, 165, 233, 0.2);
        border-color: rgba(14, 165, 233, 0.5);
        color: rgb(125 211 252);
    }
    /* شريط تفاصيل الدرس */
    .lesson-details-bar {
        background: linear-gradient(180deg, #1e293b 0%, #172033 100%);
        border-bottom: 1px solid rgba(14, 165, 233, 0.2);
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
        border-radius: 6px;
        object-fit: cover;
        background: #0f172a;
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .lesson-details-bar .lesson-thumb {
            width: 48px;
            height: 28px;
        }
    }
    .lesson-details-bar .lesson-title-text {
        color: rgb(241 245 249);
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
        color: rgba(148, 163, 184, 0.95);
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
        background: #10b981;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        border-radius: 8px;
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
        background: #059669;
    }
    .btn-lesson-complete:disabled,
    .btn-lesson-complete.completed {
        background: #059669;
        cursor: default;
    }
    .lesson-details-bar .btn-share {
        color: rgba(148, 163, 184, 0.9);
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: none;
        border: none;
        cursor: pointer;
    }
    .lesson-details-bar .btn-share:hover {
        color: rgb(226 232 240);
    }
    
    /* شريط تقدم الطالب */
    .focus-progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(15, 23, 42, 0.9);
        z-index: 100000;
    }
    .focus-progress-bar .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, rgb(14 165 233), rgb(16 185 129));
        transition: width 0.5s ease;
        border-radius: 0 2px 2px 0;
    }
    
    .curriculum-section-header {
        color: rgb(226 232 240);
        font-weight: 600;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.5rem 0.75rem;
        background: rgba(30, 41, 59, 0.6);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        margin-top: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid rgba(71, 85, 105, 0.5);
        cursor: pointer;
        user-select: none;
        transition: background 0.2s, border-color 0.2s;
    }
    .curriculum-section-header:hover {
        background: rgba(51, 65, 85, 0.6);
        border-color: rgba(14, 165, 233, 0.3);
    }
    .curriculum-section-header:first-of-type { margin-top: 0; }
    .curriculum-section-chevron {
        transition: transform 0.2s ease;
        color: rgba(148, 163, 184, 0.9);
        font-size: 0.6rem;
    }
    .curriculum-section-header.collapsed .curriculum-section-chevron {
        transform: rotate(-90deg);
    }
    @media (max-width: 640px) {
        .curriculum-section-header {
            font-size: 0.6rem;
            padding: 0.45rem 0.65rem;
            border-radius: 6px;
        }
    }
    
    .curriculum-item-title {
        color: rgb(241 245 249);
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
        color: rgba(148, 163, 184, 0.85);
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
        background: rgba(15, 23, 42, 0.5);
    }
    .lesson-content-viewer::-webkit-scrollbar-thumb,
    .lecture-viewer::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.7);
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
        color: rgb(226 232 240);
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
    
    .focus-settings-panel {
        position: fixed;
        right: -400px;
        top: 0;
        bottom: 0;
        width: 400px;
        max-width: 90vw;
        background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
        border-left: 1px solid rgba(14, 165, 233, 0.3);
        padding: 1.5rem;
        overflow-y: auto;
        transition: right 0.3s ease;
        z-index: 100002;
        -webkit-overflow-scrolling: touch;
    }
    
    .focus-settings-panel.active {
        right: 0;
    }
    
    @media (max-width: 640px) {
        .focus-settings-panel {
            width: 85vw;
            padding: 1rem;
        }
    }
    
    .focus-settings-panel::-webkit-scrollbar {
        width: 6px;
    }
    
    .focus-settings-panel::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }
    
    .focus-settings-panel::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.5);
        border-radius: 3px;
    }
    
    .focus-mode[data-font-size='small'] .curriculum-content {
        font-size: 0.875rem;
    }
    
    .focus-mode[data-font-size='medium'] .curriculum-content {
        font-size: 1rem;
    }
    
    .focus-mode[data-font-size='large'] .curriculum-content {
        font-size: 1.125rem;
    }
    
    @media print {
        .focus-mode .focus-control-bar,
        .focus-mode .focus-stats,
        .focus-mode .btn-control {
            display: none;
        }
        
        .focus-mode {
            position: static;
        }
    }
    
    /* أنماط مشغل الفيديو - المشغل الخاص بنا يتحكم والفيديو يملأ المساحة */
    .lesson-video-viewer {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 70px);
        background: #000;
        display: flex;
        flex-direction: column;
    }
    
    #video-container {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 0;
        flex: 1;
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
@endpush

@php
    // تحضير بيانات المحاضرات للـ JavaScript
    $lecturesData = $course->lectures->map(function($lecture) {
        // إعادة تحميل المحاضرة من قاعدة البيانات للتأكد من أحدث البيانات
        $lecture->refresh();
        
        // جلب البيانات مباشرة من قاعدة البيانات للتأكد
        $recordingUrl = \DB::table('lectures')
            ->where('id', $lecture->id)
            ->value('recording_url');
        
        $videoPlatform = \DB::table('lectures')
            ->where('id', $lecture->id)
            ->value('video_platform');
        
        \Log::info('Preparing lecture data', [
            'lecture_id' => $lecture->id,
            'title' => $lecture->title,
            'recording_url_from_model' => $lecture->recording_url,
            'recording_url_from_db' => $recordingUrl,
            'video_platform_from_model' => $lecture->video_platform,
            'video_platform_from_db' => $videoPlatform,
        ]);
        
        return [
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'scheduled_at' => $lecture->scheduled_at ? $lecture->scheduled_at->toIso8601String() : null,
            'scheduled_at_formatted' => $lecture->scheduled_at ? $lecture->scheduled_at->format('Y/m/d H:i') : null,
            'duration_minutes' => $lecture->duration_minutes ?? 60,
            'recording_url' => $recordingUrl ? trim($recordingUrl) : ($lecture->recording_url ? trim($lecture->recording_url) : null), // استخدام البيانات من DB أولاً
            'video_platform' => $videoPlatform ? trim($videoPlatform) : ($lecture->video_platform ? trim($lecture->video_platform) : null), // استخدام البيانات من DB أولاً
            'teams_meeting_link' => $lecture->teams_meeting_link ?? null,
            'teams_registration_link' => $lecture->teams_registration_link ?? null,
            'notes' => $lecture->notes ?? null
        ];
    })->keyBy('id');
    
    // Log للتحقق من البيانات
    \Log::info('Lectures data for student (final)', [
        'course_id' => $course->id,
        'lectures_count' => $lecturesData->count(),
        'lectures' => $lecturesData->map(function($lecture) {
            return [
                'id' => $lecture['id'],
                'title' => $lecture['title'],
                'recording_url' => $lecture['recording_url'],
                'video_platform' => $lecture['video_platform'],
                'has_recording_url' => !empty($lecture['recording_url']),
            ];
        })->toArray()
    ]);
    
    $lecturesDataJson = json_encode($lecturesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    
    // Log JSON للتحقق
    \Log::info('Lectures JSON', [
        'json_length' => strlen($lecturesDataJson),
        'first_100_chars' => substr($lecturesDataJson, 0, 100),
    ]);
@endphp

@section('content')
<div class="focus-mode" 
     data-lectures='{!! $lecturesDataJson !!}'
     data-course-progress="{{ min(100, (float)($progress ?? 0)) }}"
     :data-font-size="fontSize"
     x-data="courseFocusMode()"
     @keydown.escape.window="window.location.href='{{ route('my-courses.show', $course) }}'"
     @keydown.ctrl.f.window.prevent="document.querySelector('.search-box input')?.focus()"
     @keydown.ctrl.p.window.prevent="printCurriculum()"
     @keydown.ctrl.comma.window.prevent="showSettings = !showSettings"
     x-init="
         console.log('Focus mode initialized');
         $watch('searchQuery', () => filterItems());
         updateProgressBar();
         setInterval(() => updateProgressBar(), 100);
         document.body.style.overflow = 'hidden';
         
         // إخفاء السايدبار الرئيسي
         const mainSidebar = document.querySelector('.student-sidebar');
         if (mainSidebar) {
             mainSidebar.style.display = 'none';
         }
         
         // إخفاء النافبار الرئيسي
         const mainHeader = document.querySelector('header');
         if (mainHeader) {
             mainHeader.style.display = 'none';
         }
         
         // إخفاء الـ overlay للجوال
         const mobileOverlay = document.querySelector('.fixed.inset-0.bg-black\\/50');
         if (mobileOverlay) {
             mobileOverlay.style.display = 'none';
         }
         
         // إدارة fullscreen state
         document.addEventListener('fullscreenchange', () => {
             isFullscreen = !!document.fullscreenElement;
         });
         
         // إغلاق السايدبار على الجوال عند النقر خارجها
         if (window.innerWidth < 1024) {
             document.addEventListener('click', (e) => {
                 if (sidebarOpen && !e.target.closest('.focus-sidebar') && !e.target.closest('.sidebar-toggle')) {
                     sidebarOpen = false;
                 }
             });
         }
     ">
    <!-- شريط تقدم الطالب -->
    <div class="focus-progress-bar" title="تقدمك: {{ $completedLessons ?? 0 }}/{{ $totalLessons ?? 0 }}">
        <div class="progress-fill" style="width: {{ min(100, (float)($progress ?? 0)) }}%"></div>
    </div>
    
    <div class="curriculum-wrapper">
        <!-- شريط التحكم العلوي - تصميم محدث -->
        <div class="focus-control-bar">
            <div class="controls">
                <div class="flex items-center gap-2 md:gap-4 flex-1 min-w-0">
                    <a href="{{ route('my-courses.show', $course) }}" 
                       class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-700/60 hover:bg-sky-500/20 text-slate-200 hover:text-sky-300 border border-slate-600/50 hover:border-sky-500/30 transition-all"
                       title="العودة للكورس">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="sidebar-toggle btn-control flex-shrink-0 lg:hidden min-w-[44px] min-h-[44px] flex items-center justify-center touch-manipulation rounded-xl"
                            title="القائمة" aria-label="فتح قائمة المنهج">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button @click="sidebarClosed = !sidebarClosed" 
                            class="sidebar-toggle btn-control flex-shrink-0 hidden lg:flex rounded-xl"
                            title="إظهار/إخفاء المنهج">
                        <i class="fas" :class="sidebarClosed ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                    <div class="flex-1 min-w-0 overflow-hidden flex items-center gap-3">
                        <div class="min-w-0 flex-1 text-left">
                            <h1 class="text-base md:text-lg font-bold text-white truncate">{{ $course->title }}</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="h-1.5 flex-1 max-w-[120px] bg-slate-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-l from-sky-400 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ min(100, (float)($progress ?? 0)) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-300 whitespace-nowrap">{{ $completedLessons ?? 0 }}/{{ $totalLessons ?? 0 }}</span>
                                <span class="text-xs text-slate-400">·</span>
                                <span class="text-xs font-bold text-sky-300">{{ number_format((float)($progress ?? 0), 0) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="action-buttons flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('my-courses.show', $course) }}" class="btn-control hidden sm:inline-flex" title="العودة"><i class="fas fa-arrow-right ml-1"></i><span class="hidden lg:inline">العودة</span></a>
                    <button @click="showSettings = !showSettings" :class="showSettings ? 'active' : ''" class="btn-control" title="إعدادات"><i class="fas fa-cog ml-1"></i><span class="hidden lg:inline">إعدادات</span></button>
                    <button @click="toggleFullscreen()" class="btn-control" title="ملء الشاشة"><i class="fas ml-1" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i><span class="hidden lg:inline">ملء الشاشة</span></button>
                </div>
            </div>
        </div>
        
        <!-- Overlay للجوال عند فتح السايدبار: إغلاق بالنقر + منع التمرير -->
        <div x-show="sidebarOpen && window.innerWidth < 1024"
             x-effect="document.body.classList.toggle('sidebar-mobile-open', sidebarOpen)"
             @click="sidebarOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[100000] lg:hidden cursor-pointer"
             style="z-index: 100000; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); -webkit-tap-highlight-color: transparent;"
             aria-label="إغلاق القائمة"></div>
        
        <!-- المحتوى الرئيسي + السايدبار (السايدبار على اليسار) -->
        <div class="flex flex-1 overflow-hidden relative" style="width: 100%; height: calc(100vh - 70px);">
            <!-- المحتوى الرئيسي - منطقة التعلم -->
            <div class="focus-main-content" style="width: 100%; flex: 1; height: 100%; position: relative; order: 2;">
                <!-- زر إغلاق/فتح السايدبار - يظهر عندما يكون السايدبار مغلق (للكمبيوتر فقط) -->
                <button x-show="sidebarClosed && window.innerWidth >= 1024" 
                        @click="sidebarClosed = false" 
                        class="sidebar-toggle-btn hidden lg:flex"
                        title="فتح السايدبار">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="focus-main-content-wrapper">
                    <!-- حالة ترحيب - اختر عنصراً لبدء التعلم -->
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
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">مرحباً في {{ $course->title }}</h3>
                        <p class="text-slate-400 text-base md:text-lg mb-2 max-w-md mx-auto">اختر محاضرة أو واجباً أو امتحاناً من القائمة لبدء التعلم</p>
                        <p class="text-slate-500 text-sm mb-8">التقدم: {{ $completedLessons ?? 0 }} من {{ $totalLessons ?? 0 }} — {{ number_format((float)($progress ?? 0), 0) }}%</p>
                        <button @click="sidebarOpen = true; sidebarClosed = false" 
                                class="lg:hidden inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-400 text-white px-6 py-3.5 rounded-xl font-semibold transition-all shadow-lg shadow-sky-500/20 hover:shadow-sky-500/30">
                            <i class="fas fa-list"></i>
                            <span>فتح المنهج</span>
                        </button>
                    </div>
                    
                    <!-- محتوى الدرس المحدد -->
                    <div x-show="selectedLesson && !selectedLecture && !showVideoPlayer" x-transition class="lesson-content-viewer">
                        <div x-html="lessonContent"></div>
                    </div>
                    
                    <!-- مشغل الفيديو الموحد (للدروس والمحاضرات) - عنصر واحد فقط لضمان تحديث الرابط المعروض -->
                    <div x-show="(selectedLesson && showVideoPlayer) || (selectedLecture && showVideoPlayer)" 
                         x-transition
                         class="lesson-video-viewer w-full h-full bg-black flex flex-col"
                         style="position: relative; min-height: calc(100vh - 70px);">
                        <!-- شريط تفاصيل الدرس (يظهر للدروس فقط) -->
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
                        <div class="flex-1 min-h-0 relative" x-show="(selectedLesson && showVideoPlayer) || (selectedLecture && showVideoPlayer)">
                            @include('student.my-courses.partials.video-player')
                        </div>
                    </div>
                    
                    <!-- محتوى المحاضرة المحددة (بدون فيديو) -->
                    <div x-show="selectedLecture && !showVideoPlayer" x-transition class="lesson-content-viewer">
                        <div x-html="lectureContent"></div>
                    </div>
                    
                    <!-- النمط التعليمي / التحدي البرمجي (داخل منطقة التعلم) - بحجم قريب من مشغل الفيديو -->
                    <div x-show="selectedPattern" x-transition class="pattern-embed-outer flex flex-1 min-h-0 min-w-0 w-full items-center justify-center overflow-auto p-2 sm:p-3" style="min-height: calc(100vh - 70px);">
                        <div class="pattern-embed-wrapper flex flex-col w-full max-w-7xl min-h-0 rounded-xl overflow-hidden border border-slate-200 shadow-xl bg-white flex-shrink-0" style="min-height: calc(100vh - 120px);">
                            <div class="flex items-center justify-between gap-3 px-3 py-2.5 sm:px-4 sm:py-3 bg-slate-800/80 border-b border-slate-600/50 rounded-t-xl shrink-0">
                                <span class="text-slate-200 font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-puzzle-piece text-sky-400"></i>
                                    النمط التعليمي
                                </span>
                                <button type="button" @click="selectedPattern = null"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-600/80 hover:bg-slate-500/80 text-slate-200 text-sm font-medium transition-colors">
                                    <i class="fas fa-arrow-right ml-1"></i>
                                    إغلاق والعودة
                                </button>
                            </div>
                            <div class="pattern-embed-iframe-container flex-1 min-h-0 min-w-0 bg-white rounded-b-xl overflow-hidden border-t-0 border-slate-200" style="min-height: calc(100vh - 180px);">
                                <iframe :src="selectedPattern ? '{{ route('my-courses.learning-patterns.show', [$course, '_PID_']) }}'.replace('_PID_', selectedPattern) + '?embed=1' : ''"
                                        class="pattern-embed-iframe w-full h-full min-h-[70vh] border-0"
                                        style="min-height: calc(100vh - 180px);"
                                        title="النمط التعليمي"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- السايدبار - المنهج (أقسام المدرب + محاضرات/دروس) على اليسار -->
            <div class="focus-sidebar" 
                 :class="{ 
                     'closed': sidebarClosed && window.innerWidth >= 1024, 
                     'open': sidebarOpen || (window.innerWidth >= 1024 && !sidebarClosed) 
                 }"
                 x-show="sidebarOpen || window.innerWidth >= 1024"
                 x-cloak
                 style="z-index: 100001; order: 1;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-250"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full">
                <!-- زر إغلاق/فتح السايدبار - مدمج مع السايدبار (للكمبيوتر فقط) -->
                <button @click="sidebarClosed = !sidebarClosed" 
                        class="sidebar-toggle-btn hidden lg:flex"
                        title="إغلاق/فتح السايدبار"
                        x-show="!sidebarClosed || window.innerWidth >= 1024">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <!-- زر إغلاق السايدبار (للجوال) -->
                <button @click="sidebarOpen = false" 
                        class="sidebar-close-btn lg:hidden touch-manipulation" 
                        title="إغلاق القائمة"
                        aria-label="إغلاق القائمة">
                    <i class="fas fa-times"></i>
                </button>
                <div class="focus-sidebar-header">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-white font-bold text-sm flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-sky-500/20 flex items-center justify-center">
                                <i class="fas fa-list text-sky-400 text-xs"></i>
                            </span>
                            المنهج
                        </h3>
                        <button @click="sidebarOpen = false" 
                                class="lg:hidden text-slate-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-slate-800/60 border border-slate-600/40 mb-3">
                        <div class="h-1.5 flex-1 rounded-full bg-slate-700 overflow-hidden">
                            <div class="h-full bg-gradient-to-l from-sky-400 to-emerald-400 rounded-full" style="width: {{ min(100, (float)($progress ?? 0)) }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-300 whitespace-nowrap">{{ $completedLessons ?? 0 }}/{{ $totalLessons ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-sky-300">{{ number_format((float)($progress ?? 0), 0) }}%</span>
                    </div>
                    <div class="search-box relative">
                        <input type="text" 
                               x-model="searchQuery"
                               placeholder="ابحث..."
                               class="w-full bg-slate-800/80 border border-slate-600/60 text-slate-100 placeholder-slate-500 px-3 py-2 pr-9 rounded-lg text-xs focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500/20 transition-all"
                               @keydown.escape="searchQuery = ''">
                        <div class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-slate-500 pointer-events-none">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="focus-sidebar-content">
                    <!-- الاختبارات في السايدبار -->
                    @if(isset($sidebarExams) && $sidebarExams->count() > 0)
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
                                    <span class="text-slate-500 text-[10px]">({{ $sidebarExams->count() }})</span>
                                </span>
                                <i class="fas fa-chevron-down curriculum-section-chevron"></i>
                            </div>
                            <div x-show="!isSectionCollapsed('sidebar-exams')" x-transition>
                            @foreach($sidebarExams as $exam)
                                <div class="curriculum-item" 
                                     @click="loadExam({{ $exam->id }})"
                                     x-show="!searchQuery || '{{ strtolower($exam->title) }}'.includes(searchQuery.toLowerCase())">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="w-6 h-6 bg-indigo-500 rounded-md flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-white text-[10px]"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="curriculum-item-title">{{ $exam->title }}</div>
                                            <div class="curriculum-item-meta">
                                                <span><i class="fas fa-clock text-[10px] ml-0.5"></i> {{ $exam->duration_minutes }} د</span>
                                                <span><i class="fas fa-star text-[10px] ml-0.5"></i> {{ $exam->total_marks }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($sections) && $sections->count() > 0)
                        <!-- عرض المنهج من الأقسام -->
                        @foreach($sections as $section)
                            @php
                                $sectionItemCount = $section->activeItems->filter(fn($ci) => $ci->item && !($ci->item instanceof \App\Models\CourseLesson))->count();
                            @endphp
                            <div class="mb-4">
                                <div class="curriculum-section-header mb-2"
                                     :class="{ 'collapsed': isSectionCollapsed({{ $section->id }}) }"
                                     @click="toggleSection({{ $section->id }})"
                                     role="button"
                                     tabindex="0"
                                     @keydown.enter.prevent="toggleSection({{ $section->id }})"
                                     @keydown.space.prevent="toggleSection({{ $section->id }})">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-folder text-sky-400/90 text-[10px]"></i>
                                        <span>{{ $section->title }}</span>
                                        @if($sectionItemCount > 0)
                                            <span class="text-slate-500 text-[10px]">({{ $sectionItemCount }})</span>
                                        @endif
                                    </span>
                                    <i class="fas fa-chevron-down curriculum-section-chevron"></i>
                                </div>
                                <div x-show="!isSectionCollapsed({{ $section->id }})" x-transition>
                                @if($section->description)
                                    <p class="text-[10px] text-slate-500 mb-2 px-2">{{ $section->description }}</p>
                                @endif
                                
                                @foreach($section->activeItems as $curriculumItem)
                                    @php
                                        $item = $curriculumItem->item;
                                        if (!$item) continue;
                                        if ($item instanceof \App\Models\CourseLesson) continue;
                                        
                                        $isCompleted = false;
                                        $isCurrent = false;
                                        $isLocked = false;
                                        
                                        if ($item instanceof \App\Models\CourseLesson) {
                                            $lessonProgress = $item->progress->first();
                                            $isCompleted = $lessonProgress && $lessonProgress->is_completed;
                                            $previousItems = $section->activeItems->where('order', '<', $curriculumItem->order);
                                            $allPreviousCompleted = true;
                                            foreach ($previousItems as $prevItem) {
                                                if ($prevItem->item instanceof \App\Models\CourseLesson) {
                                                    $prevProgress = $prevItem->item->progress->first();
                                                    if (!$prevProgress || !$prevProgress->is_completed) {
                                                        $allPreviousCompleted = false;
                                                        break;
                                                    }
                                                } elseif ($prevItem->item instanceof \App\Models\LearningPattern) {
                                                    $prevBestAttempt = $prevItem->item->getUserBestAttempt(auth()->id());
                                                    if (!$prevBestAttempt || $prevBestAttempt->status !== 'completed') {
                                                        $allPreviousCompleted = false;
                                                        break;
                                                    }
                                                }
                                            }
                                            $isCurrent = !$isCompleted && ($curriculumItem->order == 1 || $allPreviousCompleted);
                                            $isLocked = !$isCurrent && !$isCompleted;
                                        } elseif ($item instanceof \App\Models\LearningPattern) {
                                            $bestAttempt = $item->getUserBestAttempt(auth()->id());
                                            $isCompleted = $bestAttempt && $bestAttempt->status === 'completed';
                                            // الأنماط التعليمية متاحة دائماً للمحاولة (لا تُقفل بترتيب المنهج)
                                            $isCurrent = !$isCompleted;
                                            $isLocked = false;
                                        }
                                    @endphp
                                    
                                    <div class="curriculum-item {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'active' : '' }} {{ $isLocked ? 'locked' : '' }}"
                                         @if($item instanceof \App\Models\CourseLesson)
                                             @click="if ({{ $isLocked ? 'true' : 'false' }}) return; selectedLesson = {{ $item->id }}; loadLesson({{ $item->id }})"
                                         @elseif($item instanceof \App\Models\Lecture)
                                             @click="loadLecture({{ $item->id }})"
                                         @elseif($item instanceof \App\Models\Assignment)
                                             @click="loadAssignment({{ $item->id }})"
                                         @elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam)
                                             @click="loadExam({{ $item->id }})"
                                         @elseif($item instanceof \App\Models\LearningPattern)
                                             @click="if ({{ $isLocked ? 'true' : 'false' }}) return; loadPattern({{ $item->id }})"
                                         @endif
                                         x-show="!searchQuery || '{{ strtolower($item->title) }}'.includes(searchQuery.toLowerCase())">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-shrink-0 mt-0.5">
                                                @if($item instanceof \App\Models\CourseLesson)
                                                    @if($isCompleted)
                                                        <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-check text-white text-[10px]"></i>
                                                        </div>
                                                    @elseif($isCurrent)
                                                        <div class="w-6 h-6 bg-sky-500 rounded-md flex items-center justify-center animate-pulse">
                                                            <i class="fas fa-play text-white text-[10px]"></i>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-lock text-white text-[10px]"></i>
                                                        </div>
                                                    @endif
                                                @elseif($item instanceof \App\Models\Lecture)
                                                    <div class="w-6 h-6 {{ $item->status === 'completed' ? 'bg-green-500' : ($item->status === 'in_progress' ? 'bg-yellow-500' : 'bg-blue-500') }} rounded-md flex items-center justify-center">
                                                        <i class="fas fa-chalkboard-teacher text-white text-[10px]"></i>
                                                    </div>
                                                @elseif($item instanceof \App\Models\Assignment)
                                                    <div class="w-6 h-6 bg-purple-500 rounded-md flex items-center justify-center">
                                                        <i class="fas fa-tasks text-white text-[10px]"></i>
                                                    </div>
                                                @elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam)
                                                    @if($isCompleted)
                                                        <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-check text-white text-[10px]"></i>
                                                        </div>
                                                    @elseif($isCurrent)
                                                        <div class="w-6 h-6 bg-indigo-500 rounded-md flex items-center justify-center animate-pulse">
                                                            <i class="fas fa-clipboard-check text-white text-[10px]"></i>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-lock text-white text-[10px]"></i>
                                                        </div>
                                                    @endif
                                                @elseif($item instanceof \App\Models\LearningPattern)
                                                    @php
                                                        $typeInfo = $item->getTypeInfo();
                                                    @endphp
                                                    @if($isCompleted)
                                                        <div class="w-6 h-6 bg-green-500 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-check text-white text-[10px]"></i>
                                                        </div>
                                                    @elseif($isCurrent)
                                                        <div class="w-6 h-6 bg-orange-500 rounded-md flex items-center justify-center animate-pulse">
                                                            <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-white text-[10px]"></i>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-600 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-lock text-white text-[10px]"></i>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="curriculum-item-title">{{ $item->title }}</div>
                                                <div class="curriculum-item-meta">
                                                    @if($item instanceof \App\Models\CourseLesson)
                                                        <span><i class="fas fa-video ml-1"></i> درس</span>
                                                        @if($item->duration_minutes)
                                                            <span><i class="fas fa-clock ml-1"></i> {{ $item->duration_minutes }} دقيقة</span>
                                                        @endif
                                                    @elseif($item instanceof \App\Models\Lecture)
                                                        <span><i class="fas fa-chalkboard-teacher ml-1"></i> محاضرة</span>
                                                        @if($item->scheduled_at)
                                                            <span><i class="fas fa-calendar ml-1"></i> {{ $item->scheduled_at->format('Y/m/d') }}</span>
                                                        @endif
                                                    @elseif($item instanceof \App\Models\Assignment)
                                                        <span><i class="fas fa-tasks ml-1"></i> واجب</span>
                                                        @if($item->due_date)
                                                            <span><i class="fas fa-calendar ml-1"></i> {{ $item->due_date->format('Y/m/d') }}</span>
                                                        @endif
                                                    @elseif($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam)
                                                        <span><i class="fas fa-clipboard-check ml-1"></i> امتحان</span>
                                                        @if($item->start_date)
                                                            <span><i class="fas fa-calendar ml-1"></i> {{ $item->start_date->format('Y/m/d') }}</span>
                                                        @endif
                                                    @elseif($item instanceof \App\Models\LearningPattern)
                                                        @php
                                                            $typeInfo = $item->getTypeInfo();
                                                        @endphp
                                                        <span><i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} ml-1"></i> {{ $typeInfo['name'] ?? 'نمط تعليمي' }}</span>
                                                        @if($item->points > 0)
                                                            <span><i class="fas fa-star ml-1"></i> {{ $item->points }} نقطة</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- لا يوجد منهج (تم إلغاء عرض الدروس) -->
                        <div class="py-6 px-4 text-center">
                            <p class="text-slate-400 text-sm">لا توجد عناصر في المنهج بعد.</p>
                            <p class="text-slate-500 text-xs mt-1">المحاضرات والواجبات والامتحانات تظهر هنا عند إضافتها من المدرب.</p>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    
    <!-- لوحة الإعدادات -->
    <div class="focus-settings-panel" :class="{ 'active': showSettings }">
        <div class="mb-5 pb-4 border-b border-slate-600/50">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-500/20 flex items-center justify-center"><i class="fas fa-cog text-sky-400"></i></span>
                إعدادات العرض
            </h3>
        </div>
        <div class="space-y-5">
            <div>
                <label class="text-slate-300 text-sm font-medium mb-2 block flex items-center gap-2">
                    <i class="fas fa-font text-slate-400"></i>
                    حجم الخط
                </label>
                <div class="flex gap-2">
                    <button @click="fontSize = 'small'" 
                            :class="fontSize === 'small' ? 'bg-sky-500/30 border-sky-400/50 text-sky-200' : 'bg-slate-700/60 border-slate-600 text-slate-300 hover:border-slate-500'"
                            class="px-3 py-2 rounded-xl text-sm font-medium border transition-all">صغير</button>
                    <button @click="fontSize = 'medium'" 
                            :class="fontSize === 'medium' ? 'bg-sky-500/30 border-sky-400/50 text-sky-200' : 'bg-slate-700/60 border-slate-600 text-slate-300 hover:border-slate-500'"
                            class="px-3 py-2 rounded-xl text-sm font-medium border transition-all">متوسط</button>
                    <button @click="fontSize = 'large'" 
                            :class="fontSize === 'large' ? 'bg-sky-500/30 border-sky-400/50 text-sky-200' : 'bg-slate-700/60 border-slate-600 text-slate-300 hover:border-slate-500'"
                            class="px-3 py-2 rounded-xl text-sm font-medium border transition-all">كبير</button>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-600/50">
                <p class="text-slate-400 text-xs font-medium mb-3">اختصارات لوحة المفاتيح</p>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center text-slate-400">
                        <span>البحث</span>
                        <kbd class="px-2 py-1 bg-slate-700/80 rounded-lg text-slate-300 font-mono">Ctrl+F</kbd>
                    </div>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>الطباعة</span>
                        <kbd class="px-2 py-1 bg-slate-700/80 rounded-lg text-slate-300 font-mono">Ctrl+P</kbd>
                    </div>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>الإعدادات</span>
                        <kbd class="px-2 py-1 bg-slate-700/80 rounded-lg text-slate-300 font-mono">Ctrl+,</kbd>
                    </div>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>إغلاق</span>
                        <kbd class="px-2 py-1 bg-slate-700/80 rounded-lg text-slate-300 font-mono">ESC</kbd>
                    </div>
                </div>
            </div>
            <div>
                <label class="text-slate-300 text-sm font-medium mb-2 block">عرض العناصر</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-slate-300 text-sm cursor-pointer">
                        <input type="checkbox" x-model="showLectures" class="rounded border-slate-500 text-sky-500 focus:ring-sky-500/30">
                        <span>إظهار المحاضرات</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="mt-6 pt-4 border-t border-gray-700">
            <button @click="showSettings = false" class="w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-times ml-2"></i>
                إغلاق
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function courseFocusMode() {
    // قراءة البيانات من data attribute
    const element = document.querySelector('[data-lectures]');
    let lecturesData = {};
    
    if (element && element.dataset.lectures) {
        try {
            lecturesData = JSON.parse(element.dataset.lectures);
            console.log('=== Lectures data loaded ===');
            console.log('Total lectures:', Object.keys(lecturesData).length);
            console.log('Lecture IDs:', Object.keys(lecturesData));
            
            // طباعة تفاصيل كل محاضرة
            Object.keys(lecturesData).forEach(lectureId => {
                const lecture = lecturesData[lectureId];
                console.log(`Lecture ${lectureId}:`, {
                    title: lecture.title,
                    recording_url: lecture.recording_url,
                    video_platform: lecture.video_platform,
                    has_recording_url: !!lecture.recording_url
                });
            });
        } catch (e) {
            console.error('Error parsing lectures data:', e);
            lecturesData = {};
        }
    }
    
    return {
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
        selectedPattern: null,
        lessonContent: '',
        lectureContent: '',
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
        videoTimeCurrent: '0:00',
        videoTimeTotal: '0:00',
        async loadLesson(lessonId) {
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
                this.currentLessonThumbnail = this.getYoutubeThumb(lesson.video_url) || '';
                this.currentLessonCompleted = !!(lesson.progress && lesson.progress.is_completed);
                
                // إذا كان هناك فيديو، اعرض جزء المشاهدة
                if (lesson.video_url) {
                    // التحقق من نوع الفيديو
                    const isExternalVideo = this.isExternalVideo(lesson.video_url);
                    
                    // عرض جزء المشاهدة للفيديو
                    this.showVideoPlayer = true;
                    this.currentLessonVideoUrl = lesson.video_url;
                    
                    let platform = null;
                    if (lesson.video_url.includes('youtube.com') || lesson.video_url.includes('youtu.be')) platform = 'youtube';
                    else if (lesson.video_url.includes('vimeo.com')) platform = 'vimeo';
                    else if (lesson.video_url.includes('drive.google.com')) platform = 'google_drive';
                    else if (lesson.video_url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) platform = 'direct';
                    [100, 250, 500].forEach(delay => {
                        setTimeout(() => {
                            const videoContainer = document.querySelector('#video-container');
                            if (videoContainer && videoContainer.__x) {
                                const v = videoContainer.__x.$data;
                                if (v && v.loadVideo && (v.currentLessonVideoUrl !== lesson.video_url || !v.currentSourceType)) {
                                    v.currentLessonVideoUrl = lesson.video_url;
                                    v.loadVideo(lesson.video_url, platform);
                                }
                            }
                        }, delay);
                    });
                    
                    // تحديث تقدم المشاهدة
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
        trackLessonProgress(lessonId) {
            // إيقاف أي interval سابق
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }
            
            // تحديث تقدم المشاهدة كل 30 ثانية
            this.progressInterval = setInterval(async () => {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    await fetch(`{{ route('my-courses.lesson.progress', [$course, ':lessonId']) }}`.replace(':lessonId', lessonId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            watch_time: 30, // 30 ثانية لكل تحديث
                            completed: false,
                            progress_percent: 0
                        })
                    });
                } catch (error) {
                    console.error('Error tracking progress:', error);
                }
            }, 30000);
        },
        loadLecture(lectureId) {
            console.log('=== loadLecture called ===');
            console.log('lectureId:', lectureId, 'Type:', typeof lectureId);
            console.log('lecturesData:', this.lecturesData);
            console.log('lecturesData keys:', Object.keys(this.lecturesData || {}));
            
            this.selectedLecture = lectureId;
            this.selectedLesson = null;
            this.selectedPattern = null;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
            
            const lectures = this.lecturesData || {};
            const lectureIdStr = String(lectureId);
            const lectureIdNum = parseInt(lectureId);
            
            console.log('Looking for lecture with:', {
                'as string': lectureIdStr,
                'as number': lectureIdNum,
                'as original': lectureId
            });
            
            let lecture = lectures[lectureIdStr] || lectures[lectureIdNum] || lectures[lectureId];
            
            // محاولة إضافية - البحث في جميع المفاتيح
            if (!lecture) {
                console.log('Trying to find lecture in all keys...');
                Object.keys(lectures).forEach(key => {
                    const l = lectures[key];
                    if (l && (l.id == lectureId || String(l.id) === String(lectureId))) {
                        lecture = l;
                        console.log('Found lecture with key:', key);
                    }
                });
            }
            
            if (!lecture) {
                console.error('Lecture not found:', lectureId);
                console.error('Available lecture IDs:', Object.keys(lectures));
                console.error('Available lectures:', lectures);
                this.lectureContent = '<div class="text-center text-red-600 p-8"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p class="text-xl font-bold">المحاضرة غير موجودة</p><p class="text-sm mt-2">ID: ' + lectureId + '</p></div>';
                return;
            }
            
            console.log('=== Lecture found ===');
            console.log('Lecture ID:', lecture.id);
            console.log('Lecture title:', lecture.title);
            console.log('Has recording_url:', !!lecture.recording_url);
            console.log('video_platform:', lecture.video_platform);
            
            // إذا كان هناك فيديو، اعرض جزء المشاهدة
            if (lecture.recording_url && lecture.recording_url.trim() !== '') {
                console.log('=== Lecture has video ===');
                console.log('video_platform:', lecture.video_platform);
                console.log('recording_url length:', lecture.recording_url.length);
                
                this.showVideoPlayer = true;
                this.currentLessonVideoUrl = lecture.recording_url;
                
                console.log('Set showVideoPlayer to true');
                
                // تحديد platform تلقائياً إذا لم يكن موجوداً
                let platform = lecture.video_platform || null;
                if (!platform) {
                    if (lecture.recording_url.includes('youtube.com') || lecture.recording_url.includes('youtu.be')) {
                        platform = 'youtube';
                    } else if (lecture.recording_url.includes('vimeo.com')) {
                        platform = 'vimeo';
                    } else if (lecture.recording_url.includes('drive.google.com')) {
                        platform = 'google_drive';
                    } else if (lecture.recording_url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
                        platform = 'direct';
                    }
                }
                
                console.log('Using platform for lecture video:', platform);
                
                // إعطاء وقت للـ DOM للتحديث ثم تحميل الفيديو مباشرة
                setTimeout(() => {
                    const videoContainer = document.querySelector('#video-container');
                    if (videoContainer && videoContainer.__x) {
                        const videoPlayerData = videoContainer.__x.$data;
                        if (videoPlayerData && videoPlayerData.loadVideo) {
                            videoPlayerData.currentLessonVideoUrl = lecture.recording_url;
                            videoPlayerData.loadVideo(lecture.recording_url, platform);
                        }
                    }
                }, 200);
                
                // محاولة أخرى بعد تأخير أطول
                [500, 1000].forEach((delay, i) => {
                    setTimeout(() => {
                        const videoContainer = document.querySelector('#video-container');
                        if (videoContainer && videoContainer.__x) {
                            const videoPlayerData = videoContainer.__x.$data;
                            if (videoPlayerData && videoPlayerData.loadVideo && videoPlayerData.currentLessonVideoUrl !== lecture.recording_url) {
                                videoPlayerData.currentLessonVideoUrl = lecture.recording_url;
                                videoPlayerData.loadVideo(lecture.recording_url, platform);
                            }
                        }
                    }, delay);
                });
                
                // تحديث تقدم المشاهدة
                this.trackLectureProgress(lectureId);
                return;
            } else {
                console.log('=== Lecture has NO video ===');
                console.log('recording_url value:', lecture.recording_url);
                console.log('recording_url is empty:', !lecture.recording_url || lecture.recording_url.trim() === '');
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
            
            // روابط Teams
            if (lecture.teams_meeting_link || lecture.teams_registration_link) {
                html += '<div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 w-full">';
                html += '<h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-video text-sky-500"></i><span>روابط Microsoft Teams</span></h3>';
                html += '<div class="space-y-3">';
                if (lecture.teams_meeting_link) {
                    html += '<a href="' + this.escapeHtml(lecture.teams_meeting_link) + '" target="_blank" class="block bg-white border-2 border-blue-300 rounded-lg p-4 hover:bg-blue-50 transition-all hover:shadow-lg w-full"><div class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="fas fa-video text-sky-500 text-xl"></i><div><div class="font-bold text-gray-900">رابط الاجتماع</div><div class="text-sm text-gray-600">انقر للانضمام</div></div></div><i class="fas fa-external-link-alt text-gray-400"></i></div></a>';
                }
                if (lecture.teams_registration_link) {
                    html += '<a href="' + this.escapeHtml(lecture.teams_registration_link) + '" target="_blank" class="block bg-white border-2 border-blue-300 rounded-lg p-4 hover:bg-blue-50 transition-all hover:shadow-lg w-full"><div class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="fas fa-user-plus text-sky-500 text-xl"></i><div><div class="font-bold text-gray-900">رابط التسجيل</div><div class="text-sm text-gray-600">سجل للانضمام</div></div></div><i class="fas fa-external-link-alt text-gray-400"></i></div></a>';
                }
                html += '</div></div>';
            }
            
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
            this.lectureContent = '<div class="text-center text-gray-600 p-8"><i class="fas fa-tasks text-4xl mb-4"></i><p class="text-xl font-bold">عرض الواجب قريباً</p></div>';
        },
        loadPattern(patternId) {
            this.selectedLesson = null;
            this.selectedLecture = null;
            this.selectedPattern = patternId;
            this.showVideoPlayer = false;
            this.currentLessonVideoUrl = null;
        },
        async loadExam(examId) {
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
        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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
                const res = await fetch('/my-courses/{{ $course->id }}/lessons/' + lessonId + '/progress', {
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
            // شريط التقدم يعرض تقدم الطالب في الكورس (من الخادم)
            const wrapper = document.querySelector('.focus-mode');
            const progressBar = document.querySelector('.focus-progress-bar .progress-fill');
            if (progressBar && wrapper && wrapper.dataset.courseProgress !== undefined) {
                const pct = Math.min(100, parseFloat(wrapper.dataset.courseProgress) || 0);
                progressBar.style.width = pct + '%';
            }
        },
        isExternalVideo(url) {
            if (!url) return false;
            return url.includes('youtube.com') || 
                   url.includes('youtu.be') || 
                   url.includes('vimeo.com') ||
                   url.includes('drive.google.com');
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

// دالة مشغل الفيديو - يتحكم في كل المصادر: يوتيوب، فيمييو، مباشر، درايف
function videoPlayer() {
    return {
        isPlaying: false,
        isMuted: false,
        isFullscreen: false,
        progressPercent: 0,
        currentLessonVideoUrl: null,
        /** نوع المصدر الحالي: 'youtube' | 'vimeo' | 'direct' | 'generic' */
        currentSourceType: null,
        youtubePlayer: null,
        get currentVideoUrl() {
            return this.currentLessonVideoUrl;
        },
        set currentVideoUrl(value) {
            this.currentLessonVideoUrl = value;
            if (value) {
                this.loadVideo(value);
            }
        },
        vimeoPlayer: null,
        videoElement: null,
        watchStartTime: null,
        totalWatchTime: 0,
        lastProgressUpdate: 0,
        isVideoReady: false,
        progressInterval: null,
        watchersSetup: false,
        init() {
            // الحصول على البيانات من Alpine.js parent
            this.setupParentWatcher();
            
            // محاولة أخرى بعد تأخير قصير للتأكد من أن Alpine.js جاهز
            setTimeout(() => {
                this.setupParentWatcher();
            }, 100);
            
            setTimeout(() => {
                this.setupParentWatcher();
            }, 300);
            
            setTimeout(() => {
                this.setupParentWatcher();
            }, 500);
            
            const checkInterval = setInterval(() => {
                const parent = this.$el.closest('[x-data*="courseFocusMode"]');
                if (parent && parent.__x) {
                    const d = parent.__x.$data;
                    if (d.showVideoPlayer && d.currentLessonVideoUrl && d.currentLessonVideoUrl !== this.currentLessonVideoUrl) {
                        this.currentLessonVideoUrl = d.currentLessonVideoUrl;
                        this.loadVideo(d.currentLessonVideoUrl, this.detectPlatform(d.currentLessonVideoUrl));
                    }
                }
            }, 2000);
            
            // تنظيف interval عند إزالة العنصر
            this.$el.addEventListener('alpine:destroy', () => {
                clearInterval(checkInterval);
            });
        },
        setupParentWatcher() {
            const parent = this.$el.closest('[x-data*="courseFocusMode"]');
            if (!parent || !parent.__x) return;
            const parentData = parent.__x.$data;
            if (parentData.showVideoPlayer && parentData.currentLessonVideoUrl) {
                this.currentLessonVideoUrl = parentData.currentLessonVideoUrl;
                this.loadVideo(parentData.currentLessonVideoUrl, this.detectPlatform(parentData.currentLessonVideoUrl));
            }
            
            // مراقبة التغييرات من parent
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
            if (this.$el) {
                const s = this.$el.querySelector('#video-surface');
                if (s) return s;
            }
            return document.querySelector('#video-container #video-surface');
        },
        detectPlatform(url) {
            if (!url) return null;
            if (url.includes('youtube.com') || url.includes('youtu.be')) return 'youtube';
            if (url.includes('vimeo.com')) return 'vimeo';
            if (url.includes('drive.google.com')) return 'google_drive';
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
        loadVideo(videoUrl, platform = null) {
            if (!videoUrl) {
                this.currentLessonVideoUrl = null;
                return;
            }
            // تحديث الرابط فوراً حتى تظهر منطقة الفيديو ولا تبقى رسالة "لا يوجد فيديو"
            this.currentLessonVideoUrl = videoUrl;
            if (this._ytProgressInterval) { clearInterval(this._ytProgressInterval); this._ytProgressInterval = null; }
            if (this._vimeoProgressInterval) { clearInterval(this._vimeoProgressInterval); this._vimeoProgressInterval = null; }
            this.youtubePlayer = null;
            this.vimeoPlayer = null;
            this.videoElement = null;
            this.currentSourceType = null;
            this.isVideoReady = false;
            this.progressPercent = 0;

            const surface = this.getSurface();
            if (!surface) {
                this.$nextTick(() => {
                    const s = this.getSurface();
                    if (s) this.loadVideo(videoUrl, platform);
                    else setTimeout(() => this.loadVideo(videoUrl, platform), 150);
                });
                return;
            }
            if (!platform) platform = this.detectPlatform(videoUrl);
            surface.innerHTML = '';
            const self = this;

            if (platform === 'youtube') {
                const vid = this.getYoutubeVideoId(videoUrl);
                if (!vid) return;
                const box = document.createElement('div');
                box.id = 'yt-player-box';
                box.className = 'absolute inset-0 w-full h-full';
                surface.appendChild(box);
                this.currentSourceType = 'youtube';
                if (!window.YT) {
                    const s = document.createElement('script');
                    s.src = 'https://www.youtube.com/iframe_api';
                    document.head.appendChild(s);
                }
                const onReady = () => {
                    self.youtubePlayer = new YT.Player('yt-player-box', {
                        height: '100%',
                        width: '100%',
                        videoId: vid,
                        playerVars: { autoplay: 0, controls: 0, rel: 0, modestbranding: 1, playsinline: 1, origin: window.location.origin },
                        events: {
                            onReady() { self.isVideoReady = true; self.updateProgress(); },
                            onStateChange(e) {
                                if (e.data === YT.PlayerState.PLAYING) {
                                    self.startWatchTimer(); self.isPlaying = true;
                                    if (self._ytProgressInterval) clearInterval(self._ytProgressInterval);
                                    self._ytProgressInterval = setInterval(() => self.updateProgress(), 400);
                                } else if (e.data === YT.PlayerState.PAUSED) {
                                    self.stopWatchTimer(); self.isPlaying = false;
                                    if (self._ytProgressInterval) { clearInterval(self._ytProgressInterval); self._ytProgressInterval = null; }
                                } else if (e.data === YT.PlayerState.ENDED) {
                                    if (self._ytProgressInterval) { clearInterval(self._ytProgressInterval); self._ytProgressInterval = null; }
                                    self.markLessonComplete();
                                }
                            }
                        }
                    });
                };
                if (window.YT && window.YT.Player) {
                    onReady();
                } else {
                    window.onYouTubeIframeAPIReady = onReady;
                }
            } else if (platform === 'vimeo') {
                const vid = this.getVimeoVideoId(videoUrl);
                if (!vid) return;
                const iframe = document.createElement('iframe');
                iframe.src = 'https://player.vimeo.com/video/' + vid + '?title=0&byline=0&portrait=0&controls=0';
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                surface.appendChild(iframe);
                this.currentSourceType = 'vimeo';
                const initVimeo = () => {
                    if (typeof window.Vimeo === 'undefined' || !window.Vimeo.Player) {
                        setTimeout(initVimeo, 80);
                        return;
                    }
                    try {
                        self.vimeoPlayer = new Vimeo.Player(iframe);
                        self.isVideoReady = true;
                        self.vimeoPlayer.on('play', () => {
                            self.startWatchTimer(); self.isPlaying = true;
                            if (self._vimeoProgressInterval) clearInterval(self._vimeoProgressInterval);
                            self._vimeoProgressInterval = setInterval(() => self.updateProgress(), 400);
                        });
                        self.vimeoPlayer.on('pause', () => {
                            self.stopWatchTimer(); self.isPlaying = false;
                            if (self._vimeoProgressInterval) { clearInterval(self._vimeoProgressInterval); self._vimeoProgressInterval = null; }
                        });
                        self.vimeoPlayer.on('ended', () => self.markLessonComplete());
                        self.vimeoPlayer.getDuration().then(d => d > 0 && self.updateProgress()).catch(() => {});
                    } catch (err) { self.isVideoReady = true; }
                };
                if (window.Vimeo && window.Vimeo.Player) initVimeo();
                else {
                    const s = document.createElement('script');
                    s.src = 'https://player.vimeo.com/api/player.js';
                    s.onload = initVimeo;
                    document.head.appendChild(s);
                }
            } else if (platform === 'direct') {
                const video = document.createElement('video');
                video.className = 'absolute inset-0 w-full h-full object-contain';
                video.controls = false;
                video.setAttribute('playsinline', '');
                const src = this.escapeHtml(videoUrl);
                video.innerHTML = '<source src="' + src + '" type="video/mp4">';
                video.oncontextmenu = () => false;
                surface.appendChild(video);
                this.currentSourceType = 'direct';
                this.videoElement = video;
                video.addEventListener('loadeddata', () => { self.isVideoReady = true; });
                video.addEventListener('play', () => { self.startWatchTimer(); self.isPlaying = true; });
                video.addEventListener('pause', () => { self.stopWatchTimer(); self.isPlaying = false; });
                video.addEventListener('timeupdate', () => self.updateProgress());
                video.addEventListener('ended', () => self.markLessonComplete());
            } else if (platform === 'google_drive') {
                const fileId = this.getDriveFileId(videoUrl);
                if (!fileId) return;
                const iframe = document.createElement('iframe');
                iframe.src = 'https://drive.google.com/file/d/' + fileId + '/preview';
                iframe.className = 'absolute inset-0 w-full h-full border-0';
                surface.appendChild(iframe);
                this.currentSourceType = 'generic';
                this.isVideoReady = true;
            }
        },
        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        togglePlayPause() {
            if (this.currentSourceType === 'youtube' && this.youtubePlayer && typeof this.youtubePlayer.getPlayerState === 'function') {
                try {
                    const state = this.youtubePlayer.getPlayerState();
                    if (state === YT.PlayerState.PLAYING) {
                        this.youtubePlayer.pauseVideo();
                    } else {
                        this.youtubePlayer.playVideo();
                    }
                } catch (e) { console.warn('YT play/pause:', e); }
            } else if (this.currentSourceType === 'vimeo' && this.vimeoPlayer) {
                this.vimeoPlayer.getPaused().then(paused => {
                    if (paused) this.vimeoPlayer.play(); else this.vimeoPlayer.pause();
                }).catch(() => {});
            } else if (this.currentSourceType === 'direct' && this.videoElement) {
                if (this.videoElement.paused) this.videoElement.play(); else this.videoElement.pause();
            }
        },
        toggleMute() {
            if (this.currentSourceType === 'youtube' && this.youtubePlayer && typeof this.youtubePlayer.isMuted === 'function') {
                try {
                    if (this.youtubePlayer.isMuted()) {
                        this.youtubePlayer.unMute();
                        this.isMuted = false;
                    } else {
                        this.youtubePlayer.mute();
                        this.isMuted = true;
                    }
                } catch (e) { console.warn('YT mute:', e); }
            } else if (this.currentSourceType === 'vimeo' && this.vimeoPlayer) {
                this.vimeoPlayer.getVolume().then(vol => {
                    const mute = vol > 0;
                    return this.vimeoPlayer.setVolume(mute ? 0 : 1).then(() => {
                        this.isMuted = mute;
                    });
                }).catch(() => {});
            } else if (this.currentSourceType === 'direct' && this.videoElement) {
                this.videoElement.muted = !this.videoElement.muted;
                this.isMuted = this.videoElement.muted;
            }
        },
        toggleFullscreen() {
            const container = document.getElementById('video-container');
            if (document.fullscreenElement) {
                document.exitFullscreen();
                this.isFullscreen = false;
            } else {
                container.requestFullscreen();
                this.isFullscreen = true;
            }
        },
        seekTo(event) {
            const progressBar = event.currentTarget;
            const rect = progressBar.getBoundingClientRect();
            const pos = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            if (this.currentSourceType === 'youtube' && this.youtubePlayer && typeof this.youtubePlayer.seekTo === 'function') {
                try {
                    const duration = typeof this.youtubePlayer.getDuration === 'function' ? this.youtubePlayer.getDuration() : 0;
                    if (duration > 0) this.youtubePlayer.seekTo(pos * duration, true);
                } catch (e) { console.warn('YT seek:', e); }
            } else if (this.currentSourceType === 'vimeo' && this.vimeoPlayer) {
                this.vimeoPlayer.getDuration().then(duration => {
                    if (duration > 0) return this.vimeoPlayer.setCurrentTime(pos * duration);
                }).catch(() => {});
            } else if (this.currentSourceType === 'direct' && this.videoElement) {
                this.videoElement.currentTime = pos * this.videoElement.duration;
            }
        },
        updateProgress() {
            let currentTime = 0;
            let duration = 0;
            
            if (this.currentSourceType === 'youtube' && this.youtubePlayer && typeof this.youtubePlayer.getCurrentTime === 'function') {
                try {
                    currentTime = this.youtubePlayer.getCurrentTime();
                    duration = typeof this.youtubePlayer.getDuration === 'function' ? this.youtubePlayer.getDuration() : 0;
                } catch (e) {}
            } else if (this.currentSourceType === 'vimeo' && this.vimeoPlayer) {
                Promise.all([this.vimeoPlayer.getCurrentTime(), this.vimeoPlayer.getDuration()]).then(([c, d]) => {
                    if (d > 0) this.progressPercent = (c / d) * 100;
                }).catch(() => {});
                return;
            } else if (this.currentSourceType === 'direct' && this.videoElement) {
                currentTime = this.videoElement.currentTime;
                duration = this.videoElement.duration;
            }
            
            if (duration > 0) {
                this.progressPercent = (currentTime / duration) * 100;
            }
        },
        startWatchTimer() {
            this.watchStartTime = Date.now();
        },
        stopWatchTimer() {
            if (this.watchStartTime) {
                this.totalWatchTime += Math.floor((Date.now() - this.watchStartTime) / 1000);
                this.watchStartTime = null;
            }
        },
        async markLessonComplete() {
            const parent = this.$el.closest('[x-data*="courseFocusMode"]');
            if (!parent || !parent.__x) return;
            
            const parentData = parent.__x.$data;
            const lessonId = parentData.selectedLesson || parentData.currentLessonId;
            if (!lessonId) return;
            
            const finalWatchTime = this.totalWatchTime + (this.watchStartTime ? Math.floor((Date.now() - this.watchStartTime) / 1000) : 0);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/my-courses/{{ $course->id }}/lessons/${lessonId}/progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        completed: true,
                        watch_time: finalWatchTime
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.showCompletionMessage();
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
        showCompletionMessage() {
            // يمكن إضافة رسالة إكمال هنا
            console.log('Lesson completed!');
        }
    };
}
</script>
@endpush
