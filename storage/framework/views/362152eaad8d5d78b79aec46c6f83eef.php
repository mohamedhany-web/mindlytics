<style>
:root {
    --learn-primary: #49A4A2;
    --learn-primary-hover: #2f7f7d;
    --learn-secondary: #5bb8b6;
    --learn-success: #10B981;
    --learn-warning: #F59E0B;
    --learn-danger: #EF4444;
    --learn-bg: #F7F9FC;
    --learn-card: #FFFFFF;
    --learn-text: #1A2238;
    --learn-text-muted: #475569;
    --learn-border: #E2E8F0;
    --learn-radius-lg: 20px;
    --learn-radius-xl: 24px;
    --learn-shadow: 0 4px 24px rgba(26, 34, 56, 0.05), 0 1px 3px rgba(26, 34, 56, 0.04);
    --learn-shadow-lg: 0 20px 50px rgba(26, 34, 56, 0.10);
    --learn-glass: rgba(255, 255, 255, 0.82);
}

[x-cloak] { display: none !important; }

.learn-immersive-body {
    font-family: 'Cairo', 'Inter', sans-serif;
    background: var(--learn-bg);
    color: var(--learn-text);
    overflow-x: hidden;
}

.learn-page {
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    background:
        radial-gradient(ellipse 80% 50% at 100% 0%, rgba(73, 164, 162, 0.07), transparent 50%),
        radial-gradient(ellipse 60% 40% at 0% 100%, rgba(73, 164, 162, 0.04), transparent 45%),
        var(--learn-bg);
    direction: rtl;
    text-align: right;
}

.learn-rtl-root,
.learn-immersive-body.learn-rtl {
    direction: rtl;
}

.learn-page .learn-sidebar-title i,
.learn-page .learn-sidebar-stat i,
.learn-page .curriculum-section-header span i {
    margin-inline-end: 0.35rem;
}

/* ─── Sticky learning header ─── */
.learn-top-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: var(--learn-glass);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--learn-border);
    box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset;
}

.learn-top-header-inner {
    max-width: 1920px;
    margin: 0 auto;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem 1rem;
    flex-wrap: nowrap;
    direction: rtl;
}

@media (min-width: 1024px) {
    .learn-top-header-inner { padding: 0.875rem 1.5rem; gap: 1rem; }
}

@media (max-width: 767px) {
    .learn-top-header-inner {
        flex-wrap: wrap;
    }
    .learn-header-progress {
        order: 10;
        width: 100%;
        max-width: none;
        min-width: 0;
    }
    .learn-header-actions {
        margin-inline-start: auto;
    }
}

.learn-header-titles {
    min-width: 0;
    flex: 1 1 140px;
}

.learn-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    color: var(--learn-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.learn-back-btn:hover {
    border-color: var(--learn-primary);
    color: var(--learn-primary);
    background: #EFF6FF;
}

.learn-header-course {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--learn-primary);
    margin-bottom: 0.125rem;
}
.learn-header-lesson {
    font-size: 1rem;
    font-weight: 700;
    color: var(--learn-text);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
@media (min-width: 768px) {
    .learn-header-lesson { font-size: 1.125rem; }
}

.learn-header-progress {
    display: none;
    flex-direction: column;
    gap: 0.35rem;
    flex: 0 1 220px;
    min-width: 160px;
    max-width: 280px;
}
@media (min-width: 768px) { .learn-header-progress { display: flex; } }

.learn-progress-track {
    height: 8px;
    background: #E2E8F0;
    border-radius: 999px;
    overflow: hidden;
    direction: rtl;
}
.learn-progress-fill {
    height: 100%;
    background: linear-gradient(270deg, var(--learn-primary), var(--learn-secondary));
    border-radius: 999px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.learn-progress-fill::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(270deg, transparent, rgba(255,255,255,0.35), transparent);
    animation: learn-shimmer 2.5s infinite;
}
@keyframes learn-shimmer {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

.learn-progress-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--learn-text-muted);
}

.learn-header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
    margin-inline-start: auto;
}

.learn-icon-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    color: var(--learn-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.learn-icon-btn:hover, .learn-icon-btn.active {
    border-color: var(--learn-primary);
    color: var(--learn-primary);
    background: #EFF6FF;
}

.learn-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    background: var(--learn-primary);
    color: #fff;
    font-size: 0.8125rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}
.learn-btn-primary:hover { background: var(--learn-primary-hover); transform: translateY(-1px); }

/* ─── Main layout grid ─── */
.learn-shell {
    flex: 1;
    display: flex;
    flex-direction: column;
    max-width: 1920px;
    margin: 0 auto;
    width: 100%;
    min-height: 0;
}

.learn-grid {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
    min-height: 0;
    direction: rtl;
}

@media (min-width: 1024px) {
    .learn-grid {
        /* sidebar أولاً في DOM → العمود الأيمن | المحتوى → العمود الأيسر */
        grid-template-columns: minmax(320px, 400px) minmax(0, 1fr);
        gap: 0;
    }
}

.learn-main {
    min-width: 0;
    overflow-y: auto;
    padding: 1rem;
    scroll-behavior: smooth;
    order: 2;
}
@media (min-width: 1024px) {
    .learn-main {
        padding: 1.25rem 1.5rem 2rem;
        order: unset;
        border-inline-start: 1px solid var(--learn-border);
    }
}

/* ─── Curriculum sidebar ─── */
.learn-sidebar {
    background: var(--learn-card);
    border-top: 1px solid var(--learn-border);
    display: flex;
    flex-direction: column;
    min-height: 0;
}

@media (min-width: 1024px) {
    .learn-sidebar {
        border-top: none;
        border-inline-start: none;
        position: sticky;
        top: 73px;
        height: calc(100dvh - 73px);
        max-height: calc(100dvh - 73px);
        box-shadow: -12px 0 40px rgba(15, 23, 42, 0.04);
    }
}

.learn-sidebar-header {
    padding: 1.25rem 1.25rem 1rem;
    border-bottom: 1px solid var(--learn-border);
    flex-shrink: 0;
    background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
}

.learn-sidebar-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--learn-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0;
}
.learn-sidebar-title i { color: var(--learn-primary); }

.learn-sidebar-nav-row {
    display: flex;
    gap: 0.5rem;
    margin: 0.75rem 0;
}
.learn-sidebar-nav-pill {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.5rem 0.75rem;
    border-radius: 12px;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    color: var(--learn-text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.learn-sidebar-nav-pill:hover:not(:disabled) {
    border-color: var(--learn-primary);
    color: var(--learn-primary);
    background: #EFF6FF;
}
.learn-sidebar-nav-pill:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.learn-sidebar-nav-pill--primary {
    background: var(--learn-primary);
    border-color: var(--learn-primary);
    color: #fff;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}
.learn-sidebar-nav-pill--primary:hover:not(:disabled) {
    background: var(--learn-primary-hover);
    color: #fff;
}

.learn-sidebar-hint {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #B45309;
    margin-top: 0.5rem;
    line-height: 1.45;
    padding: 0.5rem 0.65rem;
    border-radius: 10px;
    background: #FFFBEB;
    border: 1px solid #FDE68A;
}

.learn-sidebar-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.learn-sidebar-stat {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--learn-text-muted);
    background: var(--learn-bg);
    border: 1px solid var(--learn-border);
    padding: 0.35rem 0.65rem;
    border-radius: 999px;
}

.learn-search-wrap { position: relative; margin-bottom: 0.75rem; }
.learn-search-wrap input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border-radius: 12px;
    border: 1px solid var(--learn-border);
    background: var(--learn-bg);
    font-size: 0.8125rem;
    color: var(--learn-text);
    transition: border-color 0.2s, box-shadow 0.2s;
    text-align: right;
}
.learn-search-wrap input:focus {
    outline: none;
    border-color: var(--learn-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
.learn-search-wrap i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    inset-inline-end: 0.875rem;
    color: var(--learn-text-muted);
    font-size: 0.8125rem;
    pointer-events: none;
}

.learn-filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}
.learn-filter-chip {
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 600;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    color: var(--learn-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}
.learn-filter-chip:hover { border-color: var(--learn-primary); color: var(--learn-primary); }
.learn-filter-chip.active {
    background: #EFF6FF;
    border-color: var(--learn-primary);
    color: var(--learn-primary);
}

.learn-sidebar-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem 1rem 1.5rem;
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 transparent;
}

/* ─── Curriculum items ─── */
.curriculum-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
    margin-bottom: 0.375rem;
    border-radius: 14px;
    background: var(--learn-bg);
    border: 1px solid var(--learn-border);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    font-weight: 700;
    font-size: 0.8125rem;
    color: var(--learn-text);
}
.curriculum-section-header:first-of-type { margin-top: 0; }
.curriculum-section-header:hover {
    border-color: #BFDBFE;
    background: #F0F9FF;
}
.curriculum-section-chevron {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    color: var(--learn-text-muted);
    font-size: 0.75rem;
}
.curriculum-section-header.collapsed .curriculum-section-chevron {
    transform: rotate(90deg);
}

.curriculum-section-body {
    display: block;
}
.curriculum-section-body.is-collapsed {
    display: none !important;
}

.curriculum-item {
    display: block;
    padding: 0.75rem 0.875rem;
    margin-bottom: 0.375rem;
    border-radius: 14px;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}
.curriculum-item:hover:not(.locked) {
    border-color: #BFDBFE;
    background: #F8FAFC;
    transform: translateX(2px);
    box-shadow: var(--learn-shadow);
}

.curriculum-item.active {
    border-color: var(--learn-primary);
    background: linear-gradient(135deg, #EFF6FF 0%, #F0FDFA 100%);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
}
.curriculum-item.active::before {
    content: '';
    position: absolute;
    inset-inline-end: 0;
    top: 0.5rem;
    bottom: 0.5rem;
    width: 3px;
    border-radius: 4px;
    background: linear-gradient(180deg, var(--learn-primary), var(--learn-secondary));
}
.curriculum-item.completed { border-color: #A7F3D0; }
.curriculum-item.locked {
    opacity: 0.55;
    cursor: not-allowed;
    background: #F8FAFC;
}
.curriculum-item.locked:hover { transform: none; box-shadow: none; }

.curriculum-item--filtered {
    display: none !important;
}

.curriculum-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
}
.curriculum-item-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--learn-text);
    line-height: 1.35;
    margin-bottom: 0.25rem;
}
.curriculum-item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
    font-size: 0.6875rem;
    color: var(--learn-text-muted);
}

.learn-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.45rem;
    border-radius: 6px;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.learn-type-video { background: #DBEAFE; color: #1D4ED8; }
.learn-type-quiz { background: #F3E8FF; color: #7C3AED; }
.learn-type-assignment { background: #FEF3C7; color: #B45309; }
.learn-type-exam { background: #E0E7FF; color: #4338CA; }
.learn-type-live { background: #FEE2E2; color: #DC2626; }
.learn-type-pattern { background: #FFEDD5; color: #C2410C; }
.learn-type-reading { background: #D1FAE5; color: #047857; }

/* ─── Content panels ─── */
.learn-panels-scroll { display: flex; flex-direction: column; gap: 2.5rem; }

.learn-curriculum-panel {
    background: var(--learn-card);
    border: 1px solid var(--learn-border);
    border-radius: var(--learn-radius-xl);
    overflow: hidden;
    box-shadow: var(--learn-shadow);
    transition: box-shadow 0.3s ease, border-color 0.3s ease, opacity 0.3s ease;
    scroll-margin-top: 100px;
}
.learn-curriculum-panel:not(.panel-active) {
    opacity: 0.72;
}
.learn-curriculum-panel.panel-active {
    opacity: 1;
    border-color: #BFDBFE;
    box-shadow: var(--learn-shadow-lg);
}

.learn-video-hero {
    position: relative;
    background: #0F172A;
    border-radius: var(--learn-radius-lg);
    overflow: hidden;
    box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
    margin: 1.25rem 1.25rem 0;
}
.learn-video-hero-main {
    margin: 0 0 1.25rem;
    scroll-margin-top: 96px;
}
.learn-video-hero-main .learn-video-aspect {
    min-height: 280px;
}
@media (min-width: 768px) {
    .learn-video-hero-main .learn-video-aspect { min-height: 360px; }
}
@media (min-width: 1280px) {
    .learn-video-hero-main .learn-video-aspect { min-height: 440px; }
}
.learn-video-progress-bar {
    padding: 0.7rem 1rem 0.85rem;
    background: linear-gradient(180deg, #1a2f2e 0%, #152826 100%);
    border-bottom: 1px solid rgba(73, 164, 162, 0.22);
}
.learn-video-progress-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.55rem;
}
.learn-video-progress-label {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #7ec8c6;
    letter-spacing: 0.01em;
}
.learn-video-progress-value {
    font-size: 0.8125rem;
    font-weight: 800;
    color: #ffffff;
}
.learn-video-progress-track {
    height: 6px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    overflow: hidden;
}
.learn-video-progress-fill {
    height: 100%;
    min-width: 2px;
    border-radius: inherit;
    background: linear-gradient(270deg, #6bbdbb 0%, #49A4A2 55%, #2f7f7d 100%);
    box-shadow: 0 0 10px rgba(73, 164, 162, 0.45);
    transition: width 0.3s ease;
}
.learn-video-aspect {
    aspect-ratio: 16 / 9;
    width: 100%;
    position: relative;
    background: #000;
    min-height: 200px;
}
.lecture-video-mount iframe,
.lecture-video-mount video,
.lecture-video-mount #lecture-yt-player-box,
.lecture-video-mount #lecture-yt-player-box iframe {
    width: 100% !important;
    height: 100% !important;
    border: 0;
    display: block;
}

.learn-lesson-info {
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid var(--learn-border);
}
.learn-lesson-info h2 {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--learn-text);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}
.learn-lesson-desc {
    font-size: 0.9375rem;
    color: var(--learn-text-muted);
    line-height: 1.65;
    margin-bottom: 1rem;
}
.learn-lesson-meta-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.learn-lesson-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--learn-text-muted);
}
.learn-lesson-meta-item i { color: var(--learn-primary); width: 16px; text-align: center; }
.learn-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 700;
}
.learn-status-progress { background: #FEF3C7; color: #B45309; }
.learn-status-done { background: #D1FAE5; color: #047857; }
.learn-status-locked { background: #F1F5F9; color: #64748B; }

/* ─── Prev / Next nav ─── */
.learn-nav-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--learn-border);
}
.learn-nav-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.125rem;
    border-radius: 16px;
    border: 1px solid var(--learn-border);
    background: var(--learn-card);
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: start;
    min-height: 72px;
}
.learn-nav-btn:hover:not(:disabled) {
    border-color: var(--learn-primary);
    background: #EFF6FF;
    box-shadow: var(--learn-shadow);
}
.learn-nav-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.learn-nav-btn.next-primary:not(:disabled) {
    background: var(--learn-primary);
    border-color: var(--learn-primary);
    color: #fff;
}
.learn-nav-btn.next-primary:not(:disabled):hover {
    background: var(--learn-primary-hover);
}
.learn-nav-btn.next-primary:not(:disabled) .learn-nav-label,
.learn-nav-btn.next-primary:not(:disabled) .learn-nav-title { color: #fff; }
.learn-nav-btn.next-primary:not(:disabled) .learn-nav-label { opacity: 0.85; }

.learn-nav-label {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--learn-text-muted);
}
.learn-nav-title {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--learn-text);
    line-height: 1.3;
    margin-top: 0.15rem;
}

/* ─── Tabs ─── */
.learn-tabs-nav {
    display: flex;
    gap: 0.25rem;
    padding: 0 1.25rem;
    border-bottom: 1px solid var(--learn-border);
    overflow-x: auto;
    scrollbar-width: none;
}
.learn-tabs-nav::-webkit-scrollbar { display: none; }
.learn-tab-btn {
    padding: 0.875rem 1rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--learn-text-muted);
    border: none;
    background: none;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    white-space: nowrap;
    transition: all 0.2s ease;
}
.learn-tab-btn:hover { color: var(--learn-primary); }
.learn-tab-btn.active {
    color: var(--learn-primary);
    border-bottom-color: var(--learn-primary);
}

.learn-tab-panel {
    padding: 1.25rem;
    animation: learn-fade-in 0.25s ease;
}
@keyframes learn-fade-in {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

.learn-tab-content h4 {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--learn-text);
    margin-bottom: 0.5rem;
    margin-top: 1rem;
}
.learn-tab-content h4:first-child { margin-top: 0; }
.learn-tab-content p, .learn-tab-content li {
    font-size: 0.875rem;
    color: var(--learn-text-muted);
    line-height: 1.65;
}

.learn-resource-card {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 1rem;
    border-radius: 14px;
    border: 1px solid var(--learn-border);
    background: var(--learn-bg);
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    margin-bottom: 0.5rem;
}
.learn-resource-card:hover {
    border-color: var(--learn-primary);
    background: #EFF6FF;
}

.learn-notes-editor {
    width: 100%;
    min-height: 140px;
    padding: 1rem;
    border-radius: 14px;
    border: 1px solid var(--learn-border);
    font-size: 0.875rem;
    line-height: 1.6;
    resize: vertical;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.learn-notes-editor:focus {
    outline: none;
    border-color: var(--learn-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.learn-discussion-placeholder {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--learn-text-muted);
    font-size: 0.875rem;
}

/* ─── Auto-advance overlay ─── */
#autoplay-next-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(8px);
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
#autoplay-next-overlay.is-visible { display: flex; }

.learn-autoplay-card {
    background: var(--learn-card);
    border-radius: var(--learn-radius-xl);
    padding: 2rem 2.25rem;
    max-width: 440px;
    width: 100%;
    text-align: center;
    box-shadow: var(--learn-shadow-lg);
    animation: learn-pop-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes learn-pop-in {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.learn-celebrate {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    animation: learn-bounce 0.6s ease;
}
@keyframes learn-bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.learn-autoplay-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--learn-text);
    margin-bottom: 0.35rem;
}
.learn-autoplay-next-label {
    font-size: 0.8125rem;
    color: var(--learn-text-muted);
    margin-bottom: 0.25rem;
}
.learn-autoplay-next-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--learn-primary);
    margin-bottom: 1.25rem;
    line-height: 1.4;
}

.learn-countdown-ring {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
#autoplay-countdown-num {
    font-size: 2rem;
    font-weight: 800;
    color: var(--learn-primary);
    min-width: 36px;
}

.learn-autoplay-actions {
    display: flex;
    gap: 0.75rem;
}
.learn-autoplay-actions button {
    flex: 1;
    padding: 0.75rem 1rem;
    border-radius: 14px;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}
#autoplay-btn-now {
    background: var(--learn-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}
#autoplay-btn-now:hover { background: var(--learn-primary-hover); }
#autoplay-btn-cancel {
    background: var(--learn-bg);
    color: var(--learn-text-muted);
    border: 1px solid var(--learn-border) !important;
}

/* ─── Mobile bottom bar ─── */
.learn-mobile-bar {
    display: flex;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 45;
    background: var(--learn-glass);
    backdrop-filter: blur(16px);
    border-top: 1px solid var(--learn-border);
    padding: 0.5rem 0.75rem calc(0.5rem + env(safe-area-inset-bottom));
    gap: 0.5rem;
}
@media (min-width: 1024px) { .learn-mobile-bar { display: none; } }

.learn-mobile-bar button {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    padding: 0.5rem;
    border: none;
    background: none;
    font-size: 0.625rem;
    font-weight: 600;
    color: var(--learn-text-muted);
    cursor: pointer;
    border-radius: 10px;
}
.learn-mobile-bar button.active { color: var(--learn-primary); background: #EFF6FF; }
.learn-mobile-bar button i { font-size: 1.125rem; }
.learn-mobile-bar button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
.learn-mobile-bar button:not(:disabled):active {
    transform: scale(0.97);
}

/* Mobile curriculum drawer */
.learn-drawer-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    z-index: 55;
}
.learn-drawer-backdrop.open { display: block; }

@media (max-width: 1023px) {
    .learn-sidebar {
        position: fixed;
        top: 0;
        inset-inline-end: 0;
        width: min(100%, 400px);
        height: 100dvh;
        z-index: 60;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--learn-shadow-lg);
    }
    .learn-sidebar.drawer-open { transform: translateX(0); }
    .learn-main { padding-bottom: 5rem; order: 1; }
}

/* Skeleton */
.learn-skeleton {
    background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%);
    background-size: 200% 100%;
    animation: learn-skeleton 1.5s infinite;
    border-radius: 8px;
}
@keyframes learn-skeleton {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }

/* ─── Learn discussion / Q&A threads ─── */
.learn-thread { display: flex; flex-direction: column; gap: 1rem; }
.learn-thread-composer {
    background: var(--learn-card);
    border: 1px solid var(--learn-border);
    border-radius: 14px;
    padding: 0.75rem;
}
.learn-thread-input {
    width: 100%;
    border: 1px solid var(--learn-border);
    border-radius: 10px;
    padding: 0.65rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.55;
    color: var(--learn-text);
    background: #fff;
    resize: vertical;
    min-height: 72px;
    font-family: inherit;
}
.learn-thread-input:focus {
    outline: none;
    border-color: rgba(73, 164, 162, 0.55);
    box-shadow: 0 0 0 3px rgba(73, 164, 162, 0.12);
}
.learn-thread-composer-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 0.6rem;
    flex-wrap: wrap;
}
.learn-thread-hint { font-size: 0.7rem; font-weight: 600; color: var(--learn-text-muted); }
.learn-thread-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 0 1rem;
    border-radius: 10px;
    border: 0;
    background: var(--learn-primary);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
}
.learn-thread-submit:disabled { opacity: 0.55; cursor: not-allowed; }
.learn-thread-submit:not(:disabled):hover { background: var(--learn-primary-hover); }
.learn-thread-error { margin: 0.5rem 0 0; font-size: 0.75rem; font-weight: 600; color: var(--learn-danger); }
.learn-thread-list { display: flex; flex-direction: column; gap: 0.75rem; }
.learn-thread-post {
    background: var(--learn-card);
    border: 1px solid var(--learn-border);
    border-radius: 14px;
    padding: 0.85rem 1rem;
}
.learn-thread-post.is-instructor,
.learn-thread-reply.is-instructor {
    border-color: rgba(73, 164, 162, 0.35);
    background: rgba(73, 164, 162, 0.06);
}
.learn-thread-post-h {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.4rem 0.65rem;
    margin-bottom: 0.4rem;
    font-size: 0.75rem;
}
.learn-thread-post-h strong { font-size: 0.85rem; color: var(--learn-text); }
.learn-thread-role {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    background: rgba(73, 164, 162, 0.14);
    color: var(--learn-primary-hover);
}
.learn-thread-post-h time { color: var(--learn-text-muted); margin-inline-start: auto; }
.learn-thread-body {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.65;
    color: var(--learn-text);
    white-space: pre-wrap;
    word-break: break-word;
}
.learn-thread-replies {
    margin-top: 0.75rem;
    padding-inline-start: 0.75rem;
    border-inline-start: 2px solid rgba(73, 164, 162, 0.25);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.learn-thread-reply {
    padding: 0.55rem 0.7rem;
    border-radius: 10px;
    background: rgba(247, 249, 252, 0.9);
    border: 1px solid var(--learn-border);
}
.learn-thread-reply-box { margin-top: 0.55rem; }
.learn-thread-reply-toggle {
    border: 0;
    background: transparent;
    color: var(--learn-primary-hover);
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
}
.learn-thread-reply-form { margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-start; }
</style>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\my-courses\partials\learn-premium-styles.blade.php ENDPATH**/ ?>