<style>
:root {
    /* Align with student portal (labani) */
    --learn-primary: #aed9ea;
    --learn-primary-hover: #9bcfe3;
    --learn-primary-text: #09244b;
    --learn-secondary: #7eb8d0;
    --learn-success: #10B981;
    --learn-warning: #F59E0B;
    --learn-danger: #EF4444;
    --learn-bg: #f7f7f5;
    --learn-card: #FFFFFF;
    --learn-text: #1f1e31;
    --learn-text-muted: #6b6a7a;
    --learn-border: #e8e8e4;
    --learn-radius-lg: 20px;
    --learn-radius-xl: 24px;
    --learn-shadow: 0 4px 24px rgba(31, 30, 49, 0.06), 0 1px 3px rgba(31, 30, 49, 0.04);
    --learn-shadow-lg: 0 20px 50px rgba(31, 30, 49, 0.12);
    --learn-glass: rgba(255, 255, 255, 0.88);
    --learn-sidebar: #1f1e31;
}

[x-cloak] { display: none !important; }

.learn-immersive-body {
    font-family: var(--sp-font, 'Cairo', 'IBM Plex Sans Arabic', 'Tajawal', sans-serif);
    background: var(--learn-bg);
    color: var(--learn-text);
    overflow-x: hidden;
}

.learn-page {
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    background:
        radial-gradient(ellipse 80% 50% at 100% 0%, rgba(174, 217, 234, 0.35), transparent 50%),
        radial-gradient(ellipse 60% 40% at 0% 100%, rgba(174, 217, 234, 0.18), transparent 45%),
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
    color: var(--learn-primary-text);
    background: #d7eef5;
}

.learn-header-course {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--learn-primary-text);
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
    color: var(--learn-primary-text);
    background: #d7eef5;
}

.learn-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    background: var(--learn-primary);
    color: var(--learn-primary-text);
    font-size: 0.8125rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(174, 217, 234, 0.25);
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

/* ─── Achievement strip ─── */
.learn-achievements {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
@media (min-width: 640px) {
    .learn-achievements { grid-template-columns: repeat(4, 1fr); }
}

.learn-achievement-card {
    background: var(--learn-card);
    border: 1px solid var(--learn-border);
    border-radius: 16px;
    padding: 0.875rem 1rem;
    box-shadow: var(--learn-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.learn-achievement-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--learn-shadow-lg);
}
.learn-achievement-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}
.learn-achievement-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--learn-text);
    line-height: 1;
}
.learn-achievement-label {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--learn-text-muted);
    margin-top: 0.25rem;
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
.learn-sidebar-title i { color: var(--learn-primary-text); }

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
    color: var(--learn-primary-text);
    background: #d7eef5;
}
.learn-sidebar-nav-pill:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.learn-sidebar-nav-pill--primary {
    background: var(--learn-primary);
    border-color: var(--learn-primary);
    color: var(--learn-primary-text);
    box-shadow: 0 2px 8px rgba(174, 217, 234, 0.25);
}
.learn-sidebar-nav-pill--primary:hover:not(:disabled) {
    background: var(--learn-primary-hover);
    color: var(--learn-primary-text);
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
    box-shadow: 0 0 0 3px rgba(174, 217, 234, 0.12);
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
.learn-filter-chip:hover { border-color: var(--learn-primary); color: var(--learn-primary-text); }
.learn-filter-chip.active {
    background: #d7eef5;
    border-color: var(--learn-primary);
    color: var(--learn-primary-text);
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
    border-color: var(--learn-primary);
    background: #f7f7f5;
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
    padding: 0;
    margin-bottom: 0.5rem;
    border-radius: 16px;
    border: 1px solid #ecece8;
    background: #fff;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    position: relative;
    overflow: hidden;
}
.curriculum-item:hover:not(.locked) {
    border-color: var(--learn-primary);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(31, 30, 49, 0.08);
}
.curriculum-item-inner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0.875rem;
    min-width: 0;
}
.curriculum-status {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: #f0f0ec;
    color: var(--learn-text-muted);
    font-size: 0.65rem;
}
.curriculum-status--done {
    background: var(--learn-primary);
    color: var(--learn-primary-text);
}
.curriculum-status--current {
    background: var(--learn-primary-text);
    color: #fff;
}
.curriculum-status--locked {
    background: #ecece8;
    color: #9a9aa3;
}
.curriculum-status--ready {
    background: #d7eef5;
    color: var(--learn-primary-text);
}
.curriculum-item.active {
    border-color: var(--learn-primary);
    background: #f3fafc;
    box-shadow: 0 0 0 2px rgba(174, 217, 234, 0.35);
}
.curriculum-item.active::before {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    top: 0.55rem;
    bottom: 0.55rem;
    width: 3px;
    border-radius: 4px;
    background: var(--learn-primary);
}
.curriculum-item.completed { border-color: #cfe8f0; }
.curriculum-item.locked {
    opacity: 0.72;
    cursor: not-allowed;
}
.curriculum-item.locked:hover { transform: none; box-shadow: none; }
.curriculum-item--filtered { display: none !important; }
.curriculum-item-title {
    font-size: 0.875rem;
    font-weight: 800;
    color: var(--learn-text);
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.curriculum-item-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem 0.5rem;
    margin-top: 0.2rem;
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--learn-text-muted);
}
.curriculum-meta-pill {
    display: inline-flex;
    padding: 2px 8px;
    border-radius: 999px;
    background: #f0f0ec;
    color: var(--learn-text-muted);
}
.curriculum-meta-pill--done {
    background: var(--learn-primary);
    color: var(--learn-primary-text);
}
.curriculum-meta-pill--now {
    background: var(--learn-primary-text);
    color: #fff;
}

/* legacy icon/type helpers still referenced elsewhere */
.curriculum-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
}
.learn-type-badge { display: none; }
.learn-type-video { background: #d7eef5; color: #09244b; }
.learn-type-quiz { background: #F3E8FF; color: #7C3AED; }
.learn-type-assignment { background: #FEF3C7; color: #B45309; }
.learn-type-exam { background: #E0E7FF; color: #4338CA; }
.learn-type-pattern { background: #FFEDD5; color: #C2410C; }
.learn-type-live { background: #FEE2E2; color: #DC2626; }
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
    padding: 0.75rem 1rem;
    background: linear-gradient(180deg, rgba(15,23,42,0.95), rgba(15,23,42,0.85));
    border-bottom: 1px solid rgba(255,255,255,0.08);
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
.learn-lesson-meta-item i { color: var(--learn-primary-text); width: 16px; text-align: center; }
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
    background: #d7eef5;
    box-shadow: var(--learn-shadow);
}
.learn-nav-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.learn-nav-btn.next-primary:not(:disabled) {
    background: var(--learn-primary);
    border-color: var(--learn-primary);
    color: var(--learn-primary-text);
}
.learn-nav-btn.next-primary:not(:disabled):hover {
    background: var(--learn-primary-hover);
}
.learn-nav-btn.next-primary:not(:disabled) .learn-nav-label,
.learn-nav-btn.next-primary:not(:disabled) .learn-nav-title { color: var(--learn-primary-text); }
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
.learn-tab-btn:hover { color: var(--learn-primary-text); }
.learn-tab-btn.active {
    color: var(--learn-primary-text);
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
    background: #d7eef5;
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
    box-shadow: 0 0 0 3px rgba(174, 217, 234, 0.1);
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
    color: var(--learn-primary-text);
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
    color: var(--learn-primary-text);
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
    color: var(--learn-primary-text);
    box-shadow: 0 4px 12px rgba(174, 217, 234, 0.3);
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
.learn-mobile-bar button.active { color: var(--learn-primary-text); background: #d7eef5; }
.learn-mobile-bar button i { font-size: 1.125rem; }
.learn-mobile-bar button .sp-figma-ico {
    width: 20px !important;
    height: 20px !important;
    min-width: 20px;
    min-height: 20px;
    color: inherit !important;
    background-color: currentColor !important;
    display: inline-block !important;
}
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

/* =========================================================
   LEARN DASHBOARD LAYOUT — portal twin (dark sidebar + cards)
   ========================================================= */
.learn-page.learn-dash {
    min-height: 100dvh;
    background: var(--sp-bg, #f7f7f5) !important;
    display: block !important;
    direction: inherit !important;
    text-align: start !important;
    width: 100%;
    max-width: 100vw;
    overflow-x: hidden;
}
.learn-page.learn-dash.learn-rtl {
    direction: rtl !important;
}
.learn-page.learn-dash:not(.learn-rtl) {
    direction: ltr !important;
}
.learn-page.learn-dash .learn-top-header,
.learn-page.learn-dash .learn-shell,
.learn-page.learn-dash .learn-grid,
.learn-page.learn-dash .learn-sidebar,
.learn-page.learn-dash .learn-main,
.learn-page.learn-dash .learn-achievements,
.learn-page.learn-dash .learn-filter-chips {
    display: none !important;
}

.learn-dash-shell {
    display: block;
    padding: 0;
    gap: 0;
    min-height: 100dvh;
    box-sizing: border-box;
    background: var(--sp-bg, #f7f7f5);
    width: 100%;
    max-width: 100vw;
    overflow-x: hidden;
}

/* Dark curriculum sidebar — MOBILE FIRST: off-canvas drawer */
.learn-page.learn-dash .learn-dash-sidebar,
.learn-dash-sidebar {
    width: min(300px, 86vw);
    max-width: 320px;
    flex-shrink: 0;
    background: #1f1e31;
    color: #fff;
    border-radius: 0;
    display: flex;
    flex-direction: column;
    height: 100dvh;
    position: fixed !important;
    top: 0;
    bottom: 0;
    z-index: 80;
    overflow: hidden;
    box-shadow: 0 0 40px rgba(0,0,0,0.35);
    transition: transform 0.28s ease;
    pointer-events: none;
    /* LTR default: drawer from start (left) */
    left: 0;
    right: auto;
    transform: translateX(-105%);
}
/* RTL: drawer from end (right) — matches portal */
.learn-page.learn-dash.learn-rtl .learn-dash-sidebar,
html[dir="rtl"] .learn-page.learn-dash .learn-dash-sidebar {
    left: auto;
    right: 0;
    transform: translateX(105%);
}
.learn-page.learn-dash .learn-dash-sidebar.is-open,
.learn-dash-sidebar.is-open {
    transform: translateX(0) !important;
    pointer-events: auto !important;
}

.learn-page.learn-dash .learn-dash-main,
.learn-dash-main {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    padding: 12px 12px 100px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Desktop: sidebar in flow */
@media (min-width: 1024px) {
    .learn-dash-shell {
        display: flex;
        gap: 24px;
        padding: 16px;
        overflow: visible;
    }
    html[dir="ltr"] .learn-dash-shell,
    .learn-page:not(.learn-rtl) .learn-dash-shell {
        flex-direction: row;
    }
    html[dir="rtl"] .learn-dash-shell,
    .learn-page.learn-rtl .learn-dash-shell {
        flex-direction: row;
    }
    .learn-page.learn-dash .learn-dash-sidebar,
    .learn-dash-sidebar,
    .learn-page.learn-dash.learn-rtl .learn-dash-sidebar,
    html[dir="rtl"] .learn-page.learn-dash .learn-dash-sidebar {
        position: sticky !important;
        top: 16px;
        left: auto !important;
        right: auto !important;
        bottom: auto;
        width: 260px;
        max-width: 260px;
        height: calc(100dvh - 32px);
        border-radius: 30px;
        transform: none !important;
        pointer-events: auto !important;
        box-shadow: 0 0 10px rgba(0,0,0,0.08);
        z-index: 30;
    }
    .learn-page.learn-dash .learn-dash-main,
    .learn-dash-main {
        flex: 1;
        min-width: 0;
        padding: 0;
    }
}
.learn-dash-brand {
    padding: 22px 18px 14px;
    text-align: center;
    position: relative;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.learn-dash-brand-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #fff;
    text-decoration: none;
    font-weight: 800;
    font-size: 14px;
}
.learn-dash-brand-link img {
    height: 26px;
    width: auto;
    filter: brightness(0) invert(1);
}
.learn-dash-course-name {
    margin: 10px 0 0;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255,255,255,0.55);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.learn-dash-close {
    position: absolute;
    top: 12px;
    inset-inline-start: 12px;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: 0;
    background: rgba(255,255,255,0.1);
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.learn-dash-close-x {
    font-size: 22px;
    font-weight: 400;
    line-height: 1;
}
.learn-dash-search {
    margin: 14px 16px 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.08);
    border-radius: 30px;
    padding: 10px 14px;
    color: rgba(255,255,255,0.7);
}
.learn-dash-search input {
    flex: 1;
    min-width: 0;
    border: 0 !important;
    background: transparent !important;
    color: #fff !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}
.learn-dash-search input::placeholder { color: rgba(255,255,255,0.4); }

.learn-dash-nav {
    flex: 1;
    overflow-y: auto;
    padding: 8px 12px 16px;
    -webkit-overflow-scrolling: touch;
}
.learn-dash-nav::-webkit-scrollbar { width: 4px; }
.learn-dash-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

.learn-dash .curriculum-section-header,
.learn-dash .learn-dash-section-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    width: 100%;
    padding: 10px 12px;
    margin: 8px 0 6px;
    border: 0;
    border-radius: 14px;
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.85);
    font-family: inherit;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    text-align: start;
}
.learn-dash .curriculum-section-body.is-collapsed,
.learn-dash .learn-dash-section-body.is-collapsed {
    display: none !important;
}
.learn-dash .learn-dash-section-body { display: block; }
.learn-dash .curriculum-section-chevron { color: rgba(255,255,255,0.45); }
.learn-dash-count {
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 20px;
    background: #f8ccb5;
    color: #000;
    font-size: 11px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.learn-dash .curriculum-item {
    background: transparent !important;
    border: 0 !important;
    border-radius: 30px !important;
    margin-bottom: 6px !important;
    box-shadow: none !important;
    color: #fff;
}
.learn-dash .curriculum-item:hover:not(.locked) {
    background: rgba(255,255,255,0.06) !important;
    transform: none !important;
    box-shadow: none !important;
}
.learn-dash .curriculum-item.active {
    background: var(--sp-accent) !important;
    color: var(--sp-accent-text) !important;
    box-shadow: none !important;
}
.learn-dash .curriculum-item.active::before { display: none !important; }
.learn-dash .curriculum-item.completed:not(.active) {
    background: rgba(174, 217, 234, 0.12) !important;
}
.learn-dash .curriculum-item-title {
    color: inherit !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    white-space: normal !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.learn-dash .curriculum-item.active .curriculum-item-title { color: var(--sp-accent-text) !important; }
.learn-dash .curriculum-item-meta {
    color: rgba(255,255,255,0.45) !important;
}
.learn-dash .curriculum-item.active .curriculum-item-meta {
    color: rgba(9, 36, 75, 0.65) !important;
}
.learn-dash .curriculum-meta-pill {
    background: rgba(255,255,255,0.12);
    color: inherit;
}
.learn-dash .curriculum-item.active .curriculum-meta-pill--now,
.learn-dash .curriculum-meta-pill--now {
    background: var(--sp-accent-text);
    color: #fff;
}
.learn-dash .curriculum-item.active .curriculum-meta-pill--done {
    background: rgba(9,36,75,0.15);
    color: var(--sp-accent-text);
}
.learn-dash .curriculum-status {
    width: 28px;
    height: 28px;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.75);
}
.learn-dash .curriculum-item.active .curriculum-status {
    background: var(--sp-accent-text);
    color: #fff;
}
.learn-dash .curriculum-status--done {
    background: var(--sp-accent);
    color: var(--sp-accent-text);
}
.learn-dash .curriculum-item.active .curriculum-status--done {
    background: var(--sp-accent-text);
    color: var(--sp-accent);
}
.learn-dash .curriculum-status--locked {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.35);
}
.learn-dash .curriculum-item.locked { opacity: 0.45; }
.learn-dash .curriculum-type-row {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.learn-dash .curriculum-type-ico,
.learn-dash .curriculum-status .sp-figma-ico {
    width: 14px !important;
    height: 14px !important;
    min-width: 14px;
    min-height: 14px;
    display: inline-block !important;
    color: inherit !important;
    opacity: 1;
    background-color: currentColor !important;
    -webkit-mask-image: var(--sp-ico);
    mask-image: var(--sp-ico);
    -webkit-mask-size: contain;
    mask-size: contain;
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
}
.learn-dash .curriculum-type-ico {
    width: 12px !important;
    height: 12px !important;
    min-width: 12px;
    min-height: 12px;
    display: inline-block !important;
    color: inherit !important;
    background-color: currentColor !important;
    -webkit-mask-image: var(--sp-ico);
    mask-image: var(--sp-ico);
    -webkit-mask-size: contain;
    mask-size: contain;
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
}
.learn-dash .curriculum-item-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}
.learn-dash .curriculum-status i {
    font-size: 11px;
    line-height: 1;
    color: inherit;
}
.learn-dash-search .sp-figma-ico {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px;
    min-height: 16px;
    color: rgba(255,255,255,0.85) !important;
    background-color: currentColor !important;
    display: inline-block !important;
    -webkit-mask-image: var(--sp-ico);
    mask-image: var(--sp-ico);
    -webkit-mask-size: contain;
    mask-size: contain;
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-position: center;
    mask-position: center;
}
.learn-dash-exit .sp-figma-ico,
.learn-dash .learn-simple-nav-btn .sp-figma-ico {
    width: 14px !important;
    height: 14px !important;
    color: currentColor !important;
    background-color: currentColor !important;
    display: inline-block !important;
}
.learn-dash .curriculum-section-chevron {
    color: rgba(255,255,255,0.55) !important;
}

.learn-dash-sidebar-foot {
    padding: 12px 16px 18px;
    border-top: 1px solid rgba(255,255,255,0.08);
}
.learn-dash-exit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border-radius: 20px;
    background: rgba(255,255,255,0.08);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
}
.learn-dash-exit:hover { background: rgba(255,255,255,0.12); color: #fff; }
.learn-dash-empty {
    padding: 24px 12px;
    text-align: center;
    color: rgba(255,255,255,0.45);
    font-size: 12px;
    font-weight: 700;
}

/* Main column chrome (padding/width owned by mobile-first block above) */
.learn-dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 4px 4px 0;
}
.learn-dash-eyebrow {
    margin: 0;
    font-size: 12px;
    font-weight: 800;
    color: var(--sp-muted);
}
.learn-dash-title {
    margin: 2px 0 0;
    font-size: 22px;
    font-weight: 800;
    color: var(--sp-text);
    line-height: 1.25;
}
.learn-dash-menu {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 0;
    background: #fff;
    box-shadow: var(--sp-shadow);
    color: var(--sp-sidebar);
    cursor: pointer;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.learn-dash-menu .sp-figma-ico {
    width: 20px !important;
    height: 20px !important;
    color: inherit !important;
    background-color: currentColor !important;
}

/* grid/primary/rail defined in mobile-first responsive section below */

.learn-dash-ring {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    display: grid;
    place-items: center;
}
.learn-dash-ring-inner {
    width: 108px;
    height: 108px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Video card */
.learn-dash .learn-video-card {
    padding: 0 !important;
}
.learn-video-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px 12px;
}
.learn-dash .learn-video-hero,
.learn-dash .learn-video-hero-main {
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
    background: #0c0b14 !important;
}
.learn-dash .learn-video-aspect {
    min-height: 200px;
    aspect-ratio: 16 / 9;
}
@media (min-width: 768px) {
    .learn-dash .learn-video-aspect { min-height: 360px; }
}
@media (min-width: 1280px) {
    .learn-dash .learn-video-aspect { min-height: 420px; }
}
.learn-video-card-foot {
    padding: 14px 18px 18px;
    background: #fff;
}
.learn-dash-watch-track {
    height: 10px;
    border-radius: 999px;
    background: #ecece8;
    overflow: hidden;
}
.learn-dash-watch-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #aed9ea, #7eb8d0);
    min-width: 2px;
    transition: width 0.3s ease;
}

.learn-dash .learn-simple-nav {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.learn-dash .learn-simple-nav-btn {
    border-radius: 20px;
    padding: 14px 16px;
    font-weight: 800;
    background: #fff;
    border: 1px solid #ecece8;
    box-shadow: var(--sp-shadow);
}
.learn-dash .learn-simple-nav-btn--primary {
    background: var(--sp-accent);
    border-color: var(--sp-accent);
    color: var(--sp-accent-text);
}
.learn-dash .learn-simple-nav-btn:disabled { opacity: 0.4; }

.learn-dash .learn-material-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 16px;
    background: #f7f7f5;
    text-decoration: none;
    color: inherit;
}
.learn-dash .learn-material-row:hover {
    border: 1px solid var(--sp-accent);
}

.learn-dash .learn-drawer-backdrop.open {
    display: block;
    z-index: 65;
}

/* Visibility helpers — mobile first */
.learn-dash-only-mobile { display: inline-flex !important; align-items: center; justify-content: center; }
.learn-dash-only-desktop { display: none !important; }

.learn-page.learn-dash .learn-drawer-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 15, 25, 0.55);
    z-index: 75;
}
.learn-page.learn-dash .learn-drawer-backdrop.open {
    display: block !important;
}

.learn-page.learn-dash .learn-mobile-bar {
    display: flex !important;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 55;
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(12px);
    border-top: 1px solid #ecece8;
    padding: 0.5rem 0.75rem calc(0.5rem + env(safe-area-inset-bottom));
    gap: 0.5rem;
    pointer-events: auto;
}
.learn-page.learn-dash .learn-mobile-bar button {
    pointer-events: auto;
    color: var(--learn-text-muted, #6b6a7a);
}
.learn-page.learn-dash .learn-mobile-bar button.active {
    color: var(--learn-primary-text, #09244b);
}
.learn-page.learn-dash .learn-mobile-bar .sp-figma-ico {
    color: inherit !important;
    background-color: currentColor !important;
}

/* Grid: video first on all narrow screens */
.learn-dash-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
    align-items: start;
}
.learn-dash-primary { order: 1; min-width: 0; width: 100%; }
.learn-dash-rail {
    order: 2;
    position: static;
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
.learn-dash-rail > section { margin: 0; }

@media (min-width: 640px) {
    .learn-dash-rail { grid-template-columns: 1fr 1fr; }
    .learn-dash-rail > .learn-rail-progress { grid-column: 1 / -1; }
    .learn-dash-rail > .learn-rail-snapshot { grid-column: 1 / -1; }
}

@media (min-width: 1024px) {
    .learn-dash-only-mobile { display: none !important; }
    .learn-dash-only-desktop { display: block !important; }
    .learn-page.learn-dash .learn-mobile-bar { display: none !important; }
    .learn-page.learn-dash .learn-drawer-backdrop,
    .learn-page.learn-dash .learn-drawer-backdrop.open { display: none !important; }
}

@media (min-width: 1280px) {
    .learn-dash-grid {
        grid-template-columns: minmax(0, 1.7fr) minmax(260px, 1fr);
        gap: 20px;
    }
    .learn-dash-rail {
        position: sticky;
        top: 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        grid-template-columns: none;
    }
    .learn-dash-rail > .learn-rail-progress,
    .learn-dash-rail > .learn-rail-snapshot,
    .learn-dash-rail > .learn-rail-next { grid-column: auto; }
}

@media (max-width: 639px) {
    .learn-dash-title { font-size: 17px; }
    .learn-dash .learn-video-aspect { min-height: 180px; }
    .learn-video-card-head { padding: 12px 14px 8px; }
    .learn-video-card-foot { padding: 12px 14px 14px; }
    .learn-page.learn-dash .learn-dash-main { padding: 10px 10px 100px; }
}

/* Active content panel — only one visible, portal card rhythm */
.learn-dash .learn-panels-scroll {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.learn-dash .learn-curriculum-panel {
    border: 0 !important;
    border-radius: 20px !important;
    box-shadow: var(--sp-shadow) !important;
    background: #fff !important;
    opacity: 1 !important;
    overflow: hidden;
}
.learn-dash .learn-curriculum-panel:not(.panel-active) {
    display: none !important;
}
.learn-dash .learn-curriculum-panel.panel-active {
    display: block !important;
}
.learn-dash .learn-nav-row {
    display: none !important; /* nav lives under video + rail */
}
.learn-dash .learn-panel-locked-msg {
    margin: 0;
    padding: 48px 24px;
    text-align: center;
    background: linear-gradient(180deg, #fffaf0 0%, #fff 100%);
    border: 0;
    border-radius: 0;
    color: inherit;
}
.learn-dash .learn-lesson-info {
    padding: 20px 20px 16px;
    border-bottom: 1px solid #ecece8;
}
.learn-dash .learn-panel-eyebrow {
    margin: 0 0 6px;
    font-size: 12px;
    font-weight: 800;
    color: var(--sp-accent-text);
    letter-spacing: 0.02em;
}
.learn-dash .learn-panel-title {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
    color: var(--sp-text);
    line-height: 1.3;
}
.learn-dash .learn-lesson-desc {
    margin: 0 0 14px;
    font-size: 14px;
    color: var(--sp-muted);
    line-height: 1.65;
}
.learn-dash .learn-lesson-meta-grid {
    gap: 8px;
}
.learn-dash .learn-tabs-nav {
    display: flex;
    gap: 4px;
    padding: 0 12px;
    margin: 0;
    border-bottom: 1px solid #ecece8;
    overflow-x: auto;
    background: #fafaf8;
}
.learn-dash .learn-tab-btn {
    flex-shrink: 0;
    border: 0;
    background: transparent;
    padding: 14px 14px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 800;
    color: var(--sp-muted);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -1px;
}
.learn-dash .learn-tab-btn:hover { color: var(--sp-accent-text); }
.learn-dash .learn-tab-btn.active {
    color: var(--sp-accent-text);
    border-bottom-color: var(--sp-accent);
}
.learn-dash .learn-tab-panel {
    padding: 20px;
}
.learn-dash .learn-tab-content h4 {
    margin: 18px 0 8px;
    font-size: 14px;
    font-weight: 800;
    color: var(--sp-text);
}
.learn-dash .learn-tab-content h4:first-child { margin-top: 0; }
.learn-dash .learn-tab-content p,
.learn-dash .learn-tab-content li {
    font-size: 14px;
    color: var(--sp-muted);
    line-height: 1.7;
}
.learn-dash .learn-objectives {
    margin: 8px 0 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.learn-dash .learn-objectives li {
    position: relative;
    padding-inline-start: 28px;
    font-weight: 600;
}
.learn-dash .learn-objectives li::before {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    top: 4px;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: var(--sp-accent);
}
.learn-dash .learn-resources-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}
@media (min-width: 640px) {
    .learn-dash .learn-resources-grid { grid-template-columns: 1fr 1fr; }
}
.learn-dash .learn-resource-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 16px;
    background: #f7f7f5;
    text-decoration: none;
    color: inherit;
    border: 1px solid transparent;
}
.learn-dash .learn-resource-card:hover {
    border-color: var(--sp-accent);
}
.learn-dash .learn-notes-editor {
    width: 100%;
    min-height: 160px;
    border-radius: 16px;
    border: 1px solid #ecece8;
    padding: 14px 16px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: var(--sp-text);
    background: #f7f7f5;
    resize: vertical;
}
.learn-dash .learn-notes-editor:focus {
    outline: none;
    border-color: var(--sp-accent);
    box-shadow: 0 0 0 3px rgba(174,217,234,0.35);
    background: #fff;
}
.learn-dash .learn-discussion-placeholder {
    text-align: center;
    padding: 28px 16px;
    color: var(--sp-muted);
    font-size: 14px;
}
.learn-dash .learn-pattern-embed {
    position: relative;
    width: 100%;
    min-height: 360px;
    background: #f7f7f5;
    border-bottom: 1px solid #ecece8;
}
</style>
