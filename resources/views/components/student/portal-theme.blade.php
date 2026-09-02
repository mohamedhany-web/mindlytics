{{-- Clean Dashboard tokens from Figma yqPTrEqPms3Em8UvSzkswl / node 1:40 --}}
<style>
    :root {
        --sp-bg: #f7f7f5;
        --sp-sidebar: #1f1e31;
        --sp-sidebar-text: #ffffff;
        --sp-accent: #aed9ea;
        --sp-accent-text: #09244b;
        --sp-card: #ffffff;
        --sp-text: #0f0f14;
        --sp-muted: #6b6b76;
        --sp-peach: #f9e4d7;
        --sp-lilac: #dcdef2;
        --sp-mint: #d7eef5;
        --sp-sky: #d7e8f9;
        --sp-amber-soft: #f9f0d7;
        --sp-badge-progress: #dcdef2;
        --sp-badge-done: #aed9ea;
        --sp-badge-upcoming: #f9e4d7;
        --sp-radius-card: 20px;
        --sp-radius-pill: 30px;
        --sp-radius-shell: 50px;
        --sp-sidebar-w: 240px;
        --sp-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        --sp-font: 'Cairo', 'IBM Plex Sans Arabic', 'Tajawal', sans-serif;
    }

    /* Inherit Cairo on the portal shell — do NOT force * { font-family !important }
       or Font Awesome (.fas / .fa) glyphs become invisible. */
    .student-portal,
    .student-portal-body {
        font-family: var(--sp-font);
    }
    .student-portal .fa,
    .student-portal .fas,
    .student-portal .far,
    .student-portal .fab,
    .student-portal .fa-solid,
    .student-portal .fa-regular,
    .student-portal .fa-brands,
    .student-portal-body .fa,
    .student-portal-body .fas,
    .student-portal-body .far,
    .student-portal-body .fab,
    .student-portal-body .fa-solid,
    .student-portal-body .fa-regular,
    .student-portal-body .fa-brands {
        font-family: "Font Awesome 6 Free" !important;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
    }
    .student-portal .fab,
    .student-portal .fa-brands,
    .student-portal-body .fab,
    .student-portal-body .fa-brands {
        font-family: "Font Awesome 6 Brands" !important;
    }
    .student-portal .fas,
    .student-portal .fa-solid,
    .student-portal-body .fas,
    .student-portal-body .fa-solid {
        font-weight: 900;
    }
    .student-portal .far,
    .student-portal .fa-regular,
    .student-portal-body .far,
    .student-portal-body .fa-regular {
        font-weight: 400;
    }

    body.student-portal-body {
        background: var(--sp-bg);
        color: var(--sp-text);
        overflow-x: hidden;
    }

    .sp-shell {
        background: var(--sp-bg);
        min-height: 100vh;
    }

    .sp-sidebar {
        width: var(--sp-sidebar-w);
        max-width: 88vw;
        background: var(--sp-sidebar);
        color: var(--sp-sidebar-text);
        border-radius: 30px;
        margin: 16px;
        margin-inline-end: 0;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 32px);
        box-shadow: var(--sp-shadow);
        overflow: hidden;
    }

    @media (max-width: 1023px) {
        .sp-sidebar {
            margin: 0;
            height: 100vh;
            border-radius: 0;
            max-width: min(300px, 90vw);
        }
    }

    @media (min-width: 1024px) {
        .sp-shell { padding: 16px; gap: 24px; }
        .sp-sidebar { margin: 0; height: calc(100vh - 32px); }
    }

    .sp-sidebar-brand {
        padding: 28px 20px 18px;
        text-align: center;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 0.01em;
        color: #fff;
        line-height: 2;
    }
    .sp-sidebar-brand img {
        height: 28px;
        width: auto;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    .sp-nav {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        -webkit-overflow-scrolling: touch;
    }
    .sp-nav::-webkit-scrollbar { width: 4px; }
    .sp-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

    .sp-nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: var(--sp-radius-pill);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        line-height: 1.25;
        transition: background 0.15s, color 0.15s;
        min-height: 44px;
    }
    .sp-nav-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sp-nav-link.is-active {
        background: var(--sp-accent);
        color: var(--sp-accent-text);
    }

    /* Sidebar icons (img) — white on dark; dark on active pill */
    .sp-figma-img {
        display: inline-block;
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        object-fit: contain;
    }
    .sp-sidebar .sp-figma-img {
        filter: brightness(0) invert(1);
        opacity: 0.92;
    }
    .sp-sidebar .sp-nav-link.is-active .sp-figma-img {
        filter: brightness(0);
        opacity: 1;
    }
    .sp-header .sp-figma-img {
        filter: none;
        opacity: 0.85;
    }
    .sp-search .sp-figma-img {
        filter: none;
        opacity: 0.55;
        width: 20px;
        height: 20px;
    }
    .sp-icon-bubble .sp-figma-img {
        filter: none;
        opacity: 1;
        width: 24px;
        height: 24px;
    }

    /* Legacy mask icons (fallback if used elsewhere) */
    .sp-figma-ico {
        display: inline-block;
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        color: inherit;
        background-color: currentColor !important;
        background-image: none !important;
        -webkit-mask-image: var(--sp-ico);
        mask-image: var(--sp-ico);
        -webkit-mask-size: contain;
        mask-size: contain;
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-position: center;
        mask-position: center;
    }

    .sp-nav-ico {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: inherit;
    }
    .sp-nav-ico .sp-figma-ico {
        width: 24px;
        height: 24px;
        color: inherit;
        background-color: currentColor !important;
    }
    .sp-nav-ico i {
        font-size: 15px;
        color: inherit;
        line-height: 1;
    }

    .sp-nav-meta { display: none; }

    .sp-nav-badge {
        margin-inline-start: auto;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 20px;
        background: #f8ccb5;
        color: #000;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .sp-nav-group { border-radius: 18px; overflow: hidden; }
    .sp-nav-details {
        border-radius: 18px;
    }
    .sp-nav-details > summary { list-style: none; cursor: pointer; }
    .sp-nav-details > summary::-webkit-details-marker { display: none; }
    .sp-nav-details-summary .sp-nav-chevron {
        transition: transform 0.18s ease;
    }
    .sp-nav-details[open] > .sp-nav-details-summary .sp-nav-chevron {
        transform: rotate(180deg);
    }
    .sp-nav-details[open] > .sp-nav-details-summary:not(.is-active) {
        background: rgba(255,255,255,0.05);
    }
    .sp-nav-tree {
        margin: 0 10px 6px 20px;
        padding: 2px 0 4px 12px;
        border-inline-start: 1px solid rgba(174, 217, 234, 0.35);
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .sp-nav-tree-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 5px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.25;
        color: rgba(255,255,255,0.82);
        text-decoration: none;
        min-height: 30px;
        transition: background 0.15s, color 0.15s;
    }
    .sp-nav-tree-link:hover {
        background: rgba(255,255,255,0.06);
        color: #fff;
    }
    .sp-nav-tree-link.is-active {
        background: rgba(174, 217, 234, 0.2);
        color: var(--sp-accent);
    }
    .sp-nav-mini {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.75);
        flex-shrink: 0;
        font-variant-numeric: tabular-nums;
    }
    .sp-nav-tree-link.is-active .sp-nav-mini {
        background: rgba(9, 36, 75, 0.14);
        color: var(--sp-accent-text);
    }
    .sp-nav-tree-more {
        display: block;
        padding: 4px 8px 6px;
        font-size: 11px;
        font-weight: 700;
        color: rgba(174, 217, 234, 0.85);
        text-decoration: none;
    }
    .sp-nav-tree-more:hover { color: var(--sp-accent); }

    .sp-nav-group-btn {
        width: 100%;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: start;
        color: inherit;
        font: inherit;
    }
    .sp-nav-sub {
        padding: 0 10px 10px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        border-inline-start: 2px solid rgba(174, 217, 234, 0.45);
        margin-inline-start: 18px;
    }
    .sp-nav-sub a {
        color: rgba(255,255,255,0.85);
        font-size: 12px;
        font-weight: 600;
        padding: 8px 10px;
        border-radius: 12px;
        text-decoration: none;
    }
    .sp-nav-sub a:hover,
    .sp-nav-sub a.is-active {
        background: rgba(174, 217, 234, 0.22);
        color: var(--sp-accent);
    }
    .sp-nav-sub-label {
        color: rgba(255,255,255,0.55);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 6px 10px 2px;
    }
    .sp-nav-sub-course {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.35;
    }
    .sp-nav-sub-course span:last-child {
        font-size: 10px;
        font-weight: 700;
        color: rgba(255,255,255,0.62);
    }

    .sp-app-card {
        margin: 8px 16px 20px;
        background: var(--sp-accent);
        border-radius: 20px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        min-height: 150px;
        color: #000;
        display: flex;
        align-items: flex-end;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sp-app-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        color: #000;
    }
    .sp-app-card:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 2px;
    }
    .sp-app-card p {
        margin: 0;
        font-weight: 800;
        font-size: 14px;
        line-height: 1.35;
        max-width: 7.5rem;
        position: relative;
        z-index: 1;
        color: #000;
        white-space: pre-line;
    }
    .sp-app-card .sp-app-blob {
        position: absolute;
        left: -24px;
        bottom: -8px;
        width: 118px;
        height: 105px;
        pointer-events: none;
        z-index: 0;
        object-fit: contain;
    }
    .sp-app-card .sp-app-arrow {
        position: absolute;
        top: 11px;
        right: 16px;
        width: 40px;
        height: 40px;
        z-index: 1;
    }
    .sp-app-card .sp-app-arrow img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        display: block;
    }
    [dir="rtl"] .sp-app-card .sp-app-blob {
        left: auto;
        right: -24px;
    }
    [dir="rtl"] .sp-app-card .sp-app-arrow {
        right: auto;
        left: 16px;
    }

    .sp-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: var(--sp-bg);
    }

    .sp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 24px 28px 12px;
        background: transparent;
        position: sticky;
        top: 0;
        z-index: 30;
    }

    .sp-welcome-title {
        font-size: clamp(1.35rem, 2.5vw, 2rem);
        font-weight: 800;
        color: var(--sp-text);
        margin: 0;
        line-height: 1.25;
    }

    .sp-search {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border-radius: var(--sp-radius-pill);
        padding: 10px 14px;
        min-width: 160px;
        box-shadow: var(--sp-shadow);
        color: var(--sp-text);
    }
    .sp-search input {
        border: 0;
        outline: none;
        background: transparent;
        font-size: 13px;
        width: 100%;
        min-width: 0;
        color: var(--sp-text);
    }
    .sp-search .sp-figma-ico {
        color: #6b6b76;
        width: 20px;
        height: 20px;
    }

    .sp-lang {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        background: #fff;
        border-radius: var(--sp-radius-pill);
        padding: 4px;
        box-shadow: var(--sp-shadow);
        flex-shrink: 0;
    }
    .sp-lang-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        color: var(--sp-muted);
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .sp-lang-btn:hover {
        color: var(--sp-accent-text);
        background: #f5f5f2;
    }
    .sp-lang-btn.is-active {
        background: var(--sp-accent);
        color: var(--sp-accent-text);
    }

    .sp-menu {
        position: relative;
        display: inline-flex;
    }
    .sp-menu-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        cursor: pointer;
        background: #f5f5f5;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 800;
        color: var(--sp-text);
        font-family: inherit;
    }
    .sp-menu-btn--ghost {
        background: #fff;
        box-shadow: var(--sp-shadow);
    }
    .sp-menu-panel {
        position: absolute;
        inset-inline-end: 0;
        top: calc(100% + 8px);
        min-width: 180px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        border: 1px solid rgba(0,0,0,0.05);
        padding: 6px;
        z-index: 40;
    }
    .sp-menu-panel a {
        display: block;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        color: var(--sp-text);
        text-decoration: none;
    }
    .sp-menu-panel a:hover,
    .sp-menu-panel a.is-active {
        background: #f5f5f2;
        color: var(--sp-accent-text);
    }

    .sp-cal-day {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--sp-text);
        text-decoration: none;
        border: 0;
        background: transparent;
        cursor: pointer;
        font-family: inherit;
        padding: 0;
    }
    .sp-cal-day:hover:not(.is-muted) {
        background: rgba(174, 217, 234, 0.45);
    }
    .sp-cal-day.is-today {
        background: var(--sp-accent);
        color: var(--sp-accent-text);
        font-weight: 800;
    }
    .sp-cal-day.is-muted { color: #b0b0b8; cursor: default; }

    .sp-avatar {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        object-fit: cover;
        background: #e8e8e4;
        flex-shrink: 0;
        display: block;
    }
    .sp-avatar-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 15px;
        line-height: 1;
        color: var(--sp-accent-text);
        background: var(--sp-accent);
    }

    .sp-content {
        flex: 1;
        overflow-y: auto;
        padding: 8px 28px 32px;
        -webkit-overflow-scrolling: touch;
    }

    .sp-card {
        background: var(--sp-card);
        border-radius: var(--sp-radius-card);
        box-shadow: var(--sp-shadow);
    }

    .sp-section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--sp-text);
        margin: 0;
    }
    .sp-link {
        font-size: 14px;
        font-weight: 700;
        color: var(--sp-muted);
        text-decoration: none;
    }
    .sp-link:hover { color: var(--sp-accent-text); }

    .sp-course-mini {
        background: #fff;
        border-radius: var(--sp-radius-card);
        padding: 20px;
        box-shadow: var(--sp-shadow);
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sp-course-mini:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .sp-icon-bubble {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--sp-peach);
        color: #1f1e31;
    }
    .sp-icon-bubble .sp-figma-ico {
        width: 24px;
        height: 24px;
        color: inherit;
    }

    .sp-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }
    .sp-pill--progress { background: var(--sp-badge-progress); }
    .sp-pill--done { background: var(--sp-badge-done); color: var(--sp-accent-text); }
    .sp-pill--upcoming { background: var(--sp-badge-upcoming); }

    .sp-promo {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 32px 28px;
        position: relative;
        overflow: hidden;
        min-height: 240px;
        display: flex;
        align-items: stretch;
        gap: 8px;
    }
    .sp-promo-copy {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }
    .sp-promo-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--sp-accent);
        color: #000;
        font-weight: 800;
        font-size: 15px;
        padding: 14px 22px;
        border-radius: 20px;
        text-decoration: none;
        margin-top: 16px;
        transition: transform 0.15s ease, filter 0.15s ease;
    }
    .sp-promo-btn:hover {
        transform: translateY(-1px);
        filter: brightness(0.97);
        color: #000;
    }
    .sp-promo-art {
        width: min(168px, 46%);
        align-self: flex-end;
        flex-shrink: 0;
        line-height: 0;
        position: relative;
        z-index: 1;
        margin-inline-end: -8px;
        margin-bottom: -12px;
    }
    .sp-promo-art img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
        pointer-events: none;
    }

    .sp-bar {
        width: 10px;
        border-radius: 999px;
        background: #000;
        align-self: flex-end;
    }
    .sp-bar.is-hot { background: var(--sp-accent); }

    .sp-process-row,
    .sp-assign-row,
    .sp-schedule-row {
        background: #fff;
        border-radius: var(--sp-radius-card);
        padding: 10px 16px 10px 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: var(--sp-shadow);
        text-decoration: none;
        color: inherit;
        min-width: 0;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sp-process-row:hover,
    .sp-assign-row:hover,
    .sp-schedule-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.1);
    }

    .sp-scholarship {
        background: #fff8eb;
        border: 1px solid #f5d9a6;
        border-radius: var(--sp-radius-card);
        padding: 16px 18px;
    }

    @media (max-width: 1279px) {
        .sp-dashboard-grid { grid-template-columns: 1fr !important; }
        .sp-right-rail { order: -1; }
    }

    .sp-course-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sp-course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    @media (max-width: 767px) {
        .sp-header { padding: 14px 14px 6px; flex-wrap: wrap; }
        .sp-content { padding: 6px 14px 24px; }
        .sp-welcome-title { font-size: 1.25rem; }
        .sp-search { width: 100%; }
        .sp-promo {
            min-height: 200px;
            padding: 22px 18px 18px;
            align-items: flex-end;
            gap: 4px;
        }
        .sp-promo-copy h3 {
            font-size: 1.125rem;
            margin-top: 0.75rem !important;
        }
        .sp-promo-copy p.max-w-\[12rem\] {
            max-width: 9.5rem;
            font-size: 0.8125rem;
        }
        .sp-promo-btn {
            margin-top: 12px;
            padding: 12px 18px;
            font-size: 14px;
        }
        .sp-promo-art {
            display: block;
            width: min(118px, 40%);
            margin-inline-end: -4px;
            margin-bottom: -10px;
        }
    }

    /* Immersive student pages (exam take, etc.) — still on student shell/tokens */
    .sp-shell--immersive {
        padding: 0 !important;
        gap: 0 !important;
    }
    .sp-main--immersive {
        width: 100%;
    }
    .sp-content--immersive {
        padding: 0 !important;
        overflow: auto;
    }
    body.sp-immersive {
        overflow: hidden;
    }
</style>
