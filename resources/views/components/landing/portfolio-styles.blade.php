{{-- Shared Educo styles for public portfolio / talent / project show --}}
<style>
    .pf {
        --pf-ink: var(--lp-primary);
        --pf-mute: var(--lp-text);
    }

    .pf-stage {
        position: relative;
        overflow: hidden;
        background: var(--lp-stage);
        color: var(--lp-stage-ink);
        padding-block: 3rem 2.35rem;
    }
    .pf-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 55% at 92% 6%, rgba(201, 176, 235, 0.48), transparent 58%),
            radial-gradient(ellipse 55% 50% at 6% 80%, rgba(126, 143, 212, 0.3), transparent 55%),
            linear-gradient(180deg, rgba(238, 242, 249, 0.5) 0%, var(--lp-stage) 100%);
        pointer-events: none;
    }
    .pf-stage .lp-container { position: relative; z-index: 1; }
    .pf-crumb {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        font-size: 0.8rem;
        color: var(--lp-stage-mute);
        margin-bottom: 1rem;
    }
    .pf-crumb a {
        color: var(--lp-stage-ink);
        text-decoration: none;
        font-weight: 600;
    }
    .pf-crumb a:hover { color: var(--lp-blue-hover); }
    .pf-brand {
        font-weight: 800;
        font-size: clamp(1.25rem, 2.8vw, 1.65rem);
        letter-spacing: -0.02em;
        margin: 0 0 0.4rem;
    }
    .pf-brand span { color: var(--lp-blue-hover); }
    .pf-stage .lp-h1 { margin: 0.3rem 0 0.75rem; max-width: 18ch; }
    .pf-stage .lp-lead { margin: 0; max-width: 40rem; color: var(--lp-stage-mute); }

    .pf-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1.75rem;
    }
    @media (min-width: 800px) {
        .pf-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .pf-stat {
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(126, 143, 212, 0.22);
        border-radius: var(--lp-radius);
        padding: 0.95rem 1rem;
        text-align: center;
        box-shadow: 0 8px 20px rgba(63, 51, 86, 0.04);
    }
    .pf-stat strong {
        display: block;
        font-size: clamp(1.35rem, 3vw, 1.75rem);
        font-weight: 800;
        color: var(--lp-stage-ink);
        line-height: 1.1;
    }
    .pf-stat span {
        display: block;
        margin-top: 0.3rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--lp-stage-mute);
    }

    .pf-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        margin-top: 1.5rem;
    }
    .pf-tab {
        display: inline-flex;
        align-items: center;
        padding: 0.7rem 1.2rem;
        border-radius: var(--lp-radius-pill);
        font-size: 0.875rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid rgba(126, 143, 212, 0.28);
        background: rgba(255, 255, 255, 0.72);
        color: var(--lp-stage-ink);
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }
    .pf-tab:hover {
        transform: translateY(-1px);
        border-color: rgba(126, 143, 212, 0.45);
        color: var(--lp-blue-hover);
    }
    .pf-tab.is-active {
        background: var(--lp-yellow);
        border-color: transparent;
        color: var(--lp-yellow-ink);
    }

    .pf-audiences {
        display: grid;
        gap: 0.85rem;
        margin-top: 1.5rem;
    }
    @media (min-width: 720px) {
        .pf-audiences { grid-template-columns: 1fr 1fr; }
    }
    .pf-audience {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(126, 143, 212, 0.2);
        border-radius: var(--lp-radius);
        padding: 1rem 1.15rem;
    }
    .pf-audience strong {
        display: block;
        font-size: 0.95rem;
        color: var(--lp-stage-ink);
        margin-bottom: 0.25rem;
    }
    .pf-audience p {
        margin: 0;
        font-size: 0.85rem;
        line-height: 1.5;
        color: var(--lp-stage-mute);
    }

    .pf-body {
        background: var(--lp-cream);
        padding-block: 2.25rem 4rem;
    }
    .pf-layout {
        display: grid;
        gap: 1.75rem;
        align-items: start;
    }
    @media (min-width: 980px) {
        .pf-layout {
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 2rem;
        }
    }

    .pf-filters {
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        padding: 1.15rem;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
    }
    @media (min-width: 980px) {
        .pf-filters {
            position: sticky;
            top: 6.5rem;
        }
    }
    .pf-filters h2 {
        margin: 0 0 0.65rem;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--pf-ink);
        letter-spacing: 0.02em;
    }
    .pf-filters h2 + ul { margin-bottom: 1.25rem; }
    .pf-filters ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.25rem;
        max-height: 16rem;
        overflow: auto;
    }
    .pf-filters a {
        display: block;
        padding: 0.55rem 0.75rem;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 650;
        color: var(--pf-mute);
        transition: background 0.15s ease, color 0.15s ease;
    }
    .pf-filters a:hover {
        background: rgba(238, 242, 249, 0.9);
        color: var(--pf-ink);
    }
    .pf-filters a.is-active {
        background: var(--lp-secondary);
        color: var(--pf-ink);
        font-weight: 800;
    }

    .pf-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        align-items: center;
    }
    .pf-search {
        flex: 1 1 220px;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.28);
        border-radius: var(--lp-radius-pill);
        padding: 0.65rem 1rem;
        box-shadow: 0 8px 20px rgba(63, 51, 86, 0.04);
    }
    .pf-search input,
    .pf-select,
    .pf-panel input[type="search"],
    .pf-panel select {
        border: 0;
        background: transparent;
        font: inherit;
        color: var(--pf-ink);
        outline: none;
        width: 100%;
        min-width: 0;
    }
    .pf-select,
    .pf-panel select {
        flex: 0 1 160px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.28);
        border-radius: var(--lp-radius-pill);
        padding: 0.7rem 1rem;
        box-shadow: 0 8px 20px rgba(63, 51, 86, 0.04);
    }
    .pf-check {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1rem;
        border-radius: var(--lp-radius-pill);
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.28);
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--pf-ink);
        white-space: nowrap;
    }
    .pf-results {
        margin: 0 0 1rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--pf-mute);
    }
    .pf-results strong { color: var(--pf-ink); }

    .pf-grid {
        display: grid;
        gap: 1.15rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .pf-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1100px) {
        .pf-grid.pf-grid--projects { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (min-width: 720px) {
        .pf-grid.pf-grid--talent { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .pf-grid.pf-grid--talent { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .pf-project-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.14);
        border-radius: var(--lp-radius);
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .pf-project-card:hover {
        transform: translateY(-4px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 16px 36px rgba(63, 51, 86, 0.1);
    }
    .pf-project-card__media {
        position: relative;
        aspect-ratio: 16 / 10;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        overflow: hidden;
    }
    .pf-project-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .pf-project-card__placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 2.25rem;
        color: rgba(63, 51, 86, 0.28);
    }
    .pf-project-card__body {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        flex: 1;
        padding: 1rem 1.05rem 1.15rem;
    }
    .pf-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .pf-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.55rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.01em;
        background: rgba(238, 242, 249, 0.95);
        color: var(--lp-blue-hover);
        border: 1px solid rgba(126, 143, 212, 0.2);
    }
    .pf-badge--ok {
        background: rgba(126, 143, 212, 0.14);
        color: var(--lp-blue-hover);
    }
    .pf-badge--hot {
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink);
        border-color: transparent;
    }
    .pf-project-card__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.35;
        color: var(--pf-ink);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pf-project-card__desc {
        margin: 0;
        font-size: 0.85rem;
        line-height: 1.5;
        color: var(--pf-mute);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }
    .pf-techs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
    }
    .pf-tech {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.22rem 0.45rem;
        border-radius: 8px;
        background: var(--lp-cream);
        color: var(--pf-mute);
        border: 1px solid rgba(126, 143, 212, 0.12);
    }
    .pf-project-card__foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
        margin-top: 0.35rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(126, 143, 212, 0.12);
    }
    .pf-author {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 0;
    }
    .pf-author__avatar {
        width: 1.85rem;
        height: 1.85rem;
        border-radius: 999px;
        overflow: hidden;
        flex-shrink: 0;
        background: var(--lp-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--lp-blue-hover);
    }
    .pf-author__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pf-author strong {
        font-size: 0.8rem;
        color: var(--pf-ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pf-project-card__cta {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--lp-blue-hover);
        white-space: nowrap;
    }

    .pf-talent-card {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        height: 100%;
        text-decoration: none;
        color: inherit;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.14);
        border-radius: var(--lp-radius);
        padding: 1.15rem;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .pf-talent-card:hover {
        transform: translateY(-4px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 16px 36px rgba(63, 51, 86, 0.1);
    }
    .pf-talent-card__top {
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
    }
    .pf-talent-card__photo {
        width: 3.75rem;
        height: 3.75rem;
        border-radius: 16px;
        overflow: hidden;
        flex-shrink: 0;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.35rem;
        color: rgba(63, 51, 86, 0.35);
    }
    .pf-talent-card__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pf-talent-card__name {
        margin: 0 0 0.25rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--pf-ink);
    }
    .pf-talent-card__role {
        margin: 0;
        font-size: 0.85rem;
        color: var(--pf-mute);
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pf-talent-card__goal {
        margin: 0;
        font-size: 0.8rem;
        color: var(--pf-mute);
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .pf-empty {
        text-align: center;
        background: rgba(255, 255, 255, 0.78);
        border: 1px dashed rgba(126, 143, 212, 0.35);
        border-radius: var(--lp-radius);
        padding: 2.75rem 1.5rem;
    }
    .pf-empty .lp-h1 {
        font-size: clamp(1.35rem, 3vw, 1.75rem);
        margin: 0.35rem 0 0.65rem;
    }
    .pf-empty-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
        margin-top: 1.35rem;
    }

    .pf-featured {
        background: var(--lp-secondary);
        padding-block: 2.5rem 2.75rem;
        border-block: 1px solid rgba(63, 51, 86, 0.05);
    }
    .pf-featured__head {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.35rem;
    }

    .pf-how {
        background: var(--lp-secondary);
        padding-block: 2.75rem 3.25rem;
        border-top: 1px solid rgba(63, 51, 86, 0.05);
    }
    .pf-how__grid {
        display: grid;
        gap: 1.75rem;
        margin-top: 1.15rem;
    }
    @media (min-width: 900px) {
        .pf-how__grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; }
    }
    .pf-step__n {
        display: block;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--lp-blue);
        line-height: 1;
        margin-bottom: 0.65rem;
        opacity: 0.9;
    }
    .pf-step h2 {
        margin: 0 0 0.45rem;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--pf-ink);
    }
    .pf-step p {
        margin: 0;
        color: var(--pf-mute);
        line-height: 1.55;
        font-size: 0.95rem;
    }

    .pf-panel {
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        padding: 1.15rem;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        margin-bottom: 1.5rem;
    }
    .pf-panel__row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }
    .pf-skills {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.45rem;
    }
    .pf-skills > span {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--pf-mute);
    }
    .pf-skill-chip {
        display: inline-flex;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid rgba(126, 143, 212, 0.22);
        background: var(--lp-cream);
        color: var(--pf-ink);
    }
    .pf-skill-chip.is-active {
        background: var(--lp-yellow);
        border-color: transparent;
        color: var(--lp-yellow-ink);
    }
    .pf-skill-chip--clear { color: var(--lp-blue-hover); background: transparent; border-style: dashed; }

    .pf-pagination { margin-top: 2rem; }
    .pf-pagination nav { display: flex; justify-content: center; }

    .pf-dock {
        position: fixed;
        inset-inline: 0;
        bottom: 0;
        z-index: 70;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(63, 51, 86, 0.08);
        padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom));
        transform: translateY(110%);
        transition: transform 0.25s ease;
    }
    .pf-dock.is-visible { transform: translateY(0); }
    .pf-dock__inner {
        max-width: var(--lp-max);
        margin-inline: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .pf-dock__meta {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--pf-ink);
        min-width: 0;
    }
    .pf-dock__meta small {
        display: block;
        font-size: 0.75rem;
        color: var(--pf-mute);
        font-weight: 600;
    }
    .pf-dock .lp-btn-yellow {
        padding: 0.85rem 1.2rem;
        font-size: 0.875rem;
        text-decoration: none;
        white-space: nowrap;
    }
    @media (max-width: 899px) {
        body.lp-site-body { padding-bottom: 5.5rem; }
    }
    @media (min-width: 900px) {
        .pf-dock { display: none; }
    }

    /* Project show */
    .pf-show-stage {
        position: relative;
        overflow: hidden;
        background: var(--lp-stage);
        color: var(--lp-stage-ink);
        padding-block: 2.5rem 0;
    }
    .pf-show-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 85% 10%, rgba(201, 176, 235, 0.4), transparent 58%),
            linear-gradient(180deg, rgba(238, 242, 249, 0.45) 0%, var(--lp-stage) 100%);
        pointer-events: none;
    }
    .pf-show-stage .lp-container { position: relative; z-index: 1; }
    .pf-hero-media {
        margin-top: 1.5rem;
        border-radius: 20px 20px 0 0;
        overflow: hidden;
        aspect-ratio: 16 / 9;
        max-height: 420px;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        box-shadow: 0 -8px 40px rgba(63, 51, 86, 0.08);
    }
    .pf-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .pf-show-body {
        background: var(--lp-cream);
        padding-block: 2rem 4.5rem;
    }
    .pf-show-grid {
        display: grid;
        gap: 1.75rem;
    }
    @media (min-width: 960px) {
        .pf-show-grid {
            grid-template-columns: minmax(0, 1.35fr) minmax(260px, 0.75fr);
            gap: 2.25rem;
            align-items: start;
        }
    }
    .pf-article,
    .pf-side {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        padding: 1.35rem 1.35rem 1.5rem;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
    }
    .pf-article h1 {
        margin: 0.75rem 0 1rem;
        font-size: clamp(1.65rem, 4vw, 2.35rem);
        font-weight: 800;
        line-height: 1.15;
        color: var(--pf-ink);
        letter-spacing: -0.02em;
    }
    .pf-prose-block { margin-bottom: 1.5rem; }
    .pf-prose-block h2 {
        margin: 0 0 0.5rem;
        font-size: 1rem;
        font-weight: 800;
        color: var(--pf-ink);
    }
    .pf-prose-block p,
    .pf-prose-block .pf-rich {
        margin: 0;
        color: var(--pf-mute);
        line-height: 1.7;
        font-size: 1rem;
        white-space: pre-line;
    }
    .pf-mentor {
        background: rgba(235, 230, 246, 0.65);
        border: 1px solid rgba(126, 143, 212, 0.22);
        border-radius: 12px;
        padding: 1rem 1.1rem;
        margin-bottom: 1.5rem;
    }
    .pf-mentor h2 { color: var(--pf-ink); }
    .pf-mentor p { color: var(--pf-ink); opacity: 0.85; }
    .pf-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin: 1.25rem 0;
    }
    .pf-link-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.15rem;
        border-radius: var(--lp-radius-pill);
        border: 1px solid rgba(126, 143, 212, 0.28);
        background: #fff;
        color: var(--pf-ink);
        font-weight: 800;
        font-size: 0.875rem;
        text-decoration: none;
    }
    .pf-link-btn:hover { border-color: var(--lp-blue); color: var(--lp-blue-hover); }
    .pf-side { position: relative; }
    @media (min-width: 960px) {
        .pf-side-wrap {
            position: sticky;
            top: 6.5rem;
            display: grid;
            gap: 1rem;
        }
    }
    .pf-student {
        display: flex;
        gap: 0.85rem;
        align-items: center;
    }
    .pf-student__photo {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 14px;
        overflow: hidden;
        flex-shrink: 0;
        background: var(--lp-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--lp-blue-hover);
    }
    .pf-student__photo img { width: 100%; height: 100%; object-fit: cover; }
    .pf-gallery {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.55rem;
        margin-top: 0.75rem;
    }
    .pf-gallery a {
        display: block;
        aspect-ratio: 4/3;
        border-radius: 10px;
        overflow: hidden;
        background: var(--lp-peach);
    }
    .pf-gallery img { width: 100%; height: 100%; object-fit: cover; }

    .pf-related {
        background: var(--lp-secondary);
        padding-block: 3rem 4.5rem;
    }
    .pf-related__head { margin-bottom: 1.35rem; }

    @media (prefers-reduced-motion: reduce) {
        .pf-dock { transition: none; }
        .pf-project-card:hover,
        .pf-talent-card:hover,
        .pf-tab:hover { transform: none; }
    }
</style>
