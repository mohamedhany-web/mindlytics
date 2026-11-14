{{-- Shared Educo styles for editorial / catalog / FAQ public pages --}}
<style>
    .lp-stage-hero {
        position: relative;
        overflow: hidden;
        background: var(--lp-stage);
        color: var(--lp-stage-ink);
        padding-block: 3rem 2.5rem;
    }
    .lp-stage-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 65% 55% at 88% 8%, rgba(201, 176, 235, 0.45), transparent 58%),
            radial-gradient(ellipse 50% 45% at 8% 70%, rgba(126, 143, 212, 0.28), transparent 55%),
            linear-gradient(180deg, rgba(238, 242, 249, 0.5) 0%, var(--lp-stage) 100%);
        pointer-events: none;
    }
    .lp-stage-hero .lp-container { position: relative; z-index: 1; }
    .lp-stage-crumb {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        font-size: 0.8rem;
        color: var(--lp-stage-mute);
        margin-bottom: 1.15rem;
    }
    .lp-stage-crumb a {
        color: var(--lp-stage-ink);
        text-decoration: none;
        font-weight: 600;
    }
    .lp-stage-crumb a:hover { color: var(--lp-blue-hover); }
    .lp-stage-brand {
        font-weight: 800;
        font-size: clamp(1.25rem, 2.8vw, 1.65rem);
        letter-spacing: -0.02em;
        margin: 0 0 0.45rem;
    }
    .lp-stage-brand span { color: var(--lp-blue-hover); }
    .lp-stage-hero .lp-h1 { margin: 0.25rem 0 0.75rem; max-width: 18ch; }
    .lp-stage-hero .lp-lead { margin: 0; max-width: 38rem; color: var(--lp-stage-mute); }

    .lp-pub-body {
        background: var(--lp-cream);
        padding-block: 2.5rem 4.5rem;
    }
    .lp-pub-body--alt { background: var(--lp-secondary); }

    .lp-panel {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        padding: 1.35rem 1.35rem 1.5rem;
    }
    @media (min-width: 640px) {
        .lp-panel { padding: 1.65rem 1.75rem 1.85rem; }
    }

    .lp-legal-item {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(126, 143, 212, 0.14);
        border-radius: var(--lp-radius);
        padding: 1.25rem 1.35rem;
        border-inline-start: 3px solid var(--lp-blue);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .lp-legal-item:hover {
        border-color: rgba(126, 143, 212, 0.28);
        box-shadow: 0 10px 24px rgba(63, 51, 86, 0.06);
    }
    .lp-legal-item h2 {
        margin: 0 0 0.55rem;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .lp-legal-item p,
    .lp-legal-item li {
        margin: 0;
        color: var(--lp-text);
        line-height: 1.75;
        font-size: 0.95rem;
    }
    .lp-legal-item + .lp-legal-item { margin-top: 0.85rem; }

    .lp-faq-item {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .lp-faq-item:hover,
    .lp-faq-item[aria-expanded="true"] {
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 10px 24px rgba(63, 51, 86, 0.06);
    }
    .lp-faq-item button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: start;
        font-weight: 800;
        color: var(--lp-primary);
        font-size: 0.98rem;
        font-family: inherit;
    }
    .lp-faq-item .lp-faq-chevron {
        flex-shrink: 0;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 999px;
        background: var(--lp-secondary);
        color: var(--lp-blue-hover);
        display: grid;
        place-items: center;
        transition: transform 0.25s ease;
    }
    .lp-faq-item[aria-expanded="true"] .lp-faq-chevron { transform: rotate(180deg); }
    .lp-faq-item .lp-faq-body {
        padding: 0 1.15rem 1.15rem;
        color: var(--lp-text);
        line-height: 1.75;
        font-size: 0.95rem;
        border-top: 1px solid rgba(126, 143, 212, 0.12);
        padding-top: 0.9rem;
    }
    .lp-faq-item + .lp-faq-item { margin-top: 0.65rem; }

    .lp-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .lp-filter-chip {
        appearance: none;
        border: 1px solid rgba(126, 143, 212, 0.28);
        background: rgba(255, 255, 255, 0.85);
        color: var(--lp-primary);
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.55rem 1rem;
        border-radius: var(--lp-radius-pill);
        cursor: pointer;
        font-family: inherit;
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }
    .lp-filter-chip.is-active,
    .lp-filter-chip:hover {
        background: var(--lp-yellow);
        border-color: transparent;
        color: var(--lp-yellow-ink);
    }

    .lp-stat-strip {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: 1fr;
        margin-block: 1.25rem 0;
    }
    @media (min-width: 640px) {
        .lp-stat-strip { grid-template-columns: repeat(3, 1fr); }
    }
    .lp-stat-pill {
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(126, 143, 212, 0.22);
        border-radius: var(--lp-radius);
        padding: 1rem 1.1rem;
        text-align: center;
    }
    .lp-stat-pill .n {
        font-size: 1.65rem;
        font-weight: 900;
        color: var(--lp-primary);
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .lp-stat-pill .l {
        margin-top: 0.25rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--lp-stage-mute);
    }

    .lp-job-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        padding: 1.25rem 1.35rem;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .lp-job-card:hover {
        transform: translateY(-3px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 14px 32px rgba(63, 51, 86, 0.08);
    }
    .lp-job-card h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--lp-primary);
        line-height: 1.35;
    }
    .lp-job-card:hover h3 { color: var(--lp-blue-hover); }
    .lp-job-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin: 0.75rem 0;
    }
    .lp-job-meta span {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 0.3rem 0.65rem;
        border-radius: var(--lp-radius-pill);
        background: var(--lp-secondary);
        color: var(--lp-primary);
    }
    .lp-job-card p {
        margin: 0;
        flex: 1;
        font-size: 0.9rem;
        color: var(--lp-text);
        line-height: 1.65;
    }
    .lp-job-foot {
        margin-top: 1rem;
        padding-top: 0.85rem;
        border-top: 1px solid rgba(126, 143, 212, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--lp-blue-hover);
    }

    .lp-form-row {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-bottom: 1.5rem;
    }
    @media (min-width: 640px) {
        .lp-form-row { flex-direction: row; align-items: stretch; }
    }
    .lp-input {
        flex: 1;
        width: 100%;
        border: 1px solid rgba(126, 143, 212, 0.28);
        background: #fff;
        border-radius: var(--lp-radius);
        padding: 0.75rem 1rem;
        font: inherit;
        color: var(--lp-primary);
        min-width: 0;
    }
    .lp-input:focus {
        outline: 2px solid rgba(126, 143, 212, 0.35);
        outline-offset: 1px;
    }

    .lp-cta-band {
        margin-top: 2.5rem;
        padding: 1.75rem 1.5rem;
        border-radius: var(--lp-radius);
        background: var(--lp-stage);
        text-align: center;
    }
    .lp-cta-band h3 {
        margin: 0 0 0.4rem;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .lp-cta-band p {
        margin: 0 0 1.1rem;
        color: var(--lp-text);
    }

    .lp-grid-2 {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 720px) {
        .lp-grid-2 { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
    }
    .lp-grid-3 {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 720px) {
        .lp-grid-3 { grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    }
    .lp-grid-4 {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .lp-grid-4 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 960px) {
        .lp-grid-4 { grid-template-columns: repeat(4, 1fr); }
    }

    .lp-stage-hero--detail .lp-h1 { max-width: min(36rem, 100%); }

    .lp-chip-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.4rem 0.8rem;
        border-radius: var(--lp-radius-pill);
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(126, 143, 212, 0.28);
        color: var(--lp-primary);
    }
    .lp-chip-soft + .lp-chip-soft,
    .lp-chip-row .lp-chip-soft { margin: 0; }

    .lp-detail-grid {
        display: grid;
        gap: 1.5rem;
        align-items: start;
    }
    @media (min-width: 960px) {
        .lp-detail-grid {
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.85fr);
            gap: 2rem;
        }
    }
    .lp-aside-sticky {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 960px) {
        .lp-aside-sticky {
            position: sticky;
            top: 6.5rem;
        }
    }

    .lp-alert {
        border-radius: var(--lp-radius);
        padding: 0.9rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1.15rem;
        color: var(--lp-primary);
    }
    .lp-alert--ok {
        background: rgba(126, 143, 212, 0.12);
        border: 1px solid rgba(126, 143, 212, 0.28);
    }
    .lp-alert--err {
        background: rgba(201, 176, 235, 0.22);
        border: 1px solid rgba(63, 51, 86, 0.12);
    }
    .lp-alert--err ul {
        margin: 0.35rem 0 0;
        padding-inline-start: 1.1rem;
    }
    .lp-alert strong { display: block; margin-bottom: 0.2rem; }

    .lp-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--lp-primary);
        margin-bottom: 0.4rem;
    }
    .lp-label span { color: var(--lp-blue-hover); }
    .lp-label small {
        font-weight: 600;
        color: var(--lp-text);
        margin-inline-start: 0.25rem;
    }
    .lp-fields { display: grid; gap: 1rem; }
    .lp-fields + .lp-fields { margin-top: 1.25rem; }
    .lp-form-block__title {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--lp-primary);
        margin: 0 0 0.85rem;
    }
    .lp-form-block__num {
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-size: 0.75rem;
        font-weight: 900;
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink);
        flex-shrink: 0;
    }
    textarea.lp-input {
        width: 100%;
        min-height: 110px;
        resize: vertical;
        line-height: 1.55;
    }
    select.lp-input { cursor: pointer; }
    .lp-input::placeholder { color: #b0a8bd; }

    .lp-upload {
        display: block;
        border: 1px dashed rgba(126, 143, 212, 0.45);
        background: rgba(238, 242, 249, 0.55);
        border-radius: var(--lp-radius);
        padding: 0.95rem 1rem;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease;
    }
    .lp-upload:hover {
        border-color: rgba(126, 143, 212, 0.7);
        background: #fff;
    }
    .lp-upload__row {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .lp-upload__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        background: #fff;
        border: 1px solid rgba(126, 143, 212, 0.22);
        color: var(--lp-blue-hover);
        font-weight: 900;
        font-size: 0.85rem;
    }
    .lp-upload__title {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .lp-upload__hint {
        margin: 0.2rem 0 0;
        font-size: 0.75rem;
        color: var(--lp-text);
    }

    .lp-prose {
        color: var(--lp-text);
        font-size: 0.95rem;
        line-height: 1.75;
        white-space: pre-line;
    }
    .lp-panel__head {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.85rem;
    }
    .lp-panel__head h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .lp-req-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.55rem;
    }
    .lp-req-list li {
        position: relative;
        padding-inline-start: 1.15rem;
        color: var(--lp-text);
        font-size: 0.92rem;
        line-height: 1.55;
    }
    .lp-req-list li::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0.55rem;
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 999px;
        background: var(--lp-blue);
    }

    .lp-step-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.85rem;
    }
    .lp-step-list li {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .lp-step-list__n {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 999px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-size: 0.75rem;
        font-weight: 900;
        background: var(--lp-secondary);
        color: var(--lp-blue-hover);
    }
    .lp-step-list strong {
        display: block;
        font-size: 0.9rem;
        color: var(--lp-primary);
        margin-bottom: 0.15rem;
    }
    .lp-step-list p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--lp-text);
        line-height: 1.45;
    }

    .lp-stat-pill--row {
        text-align: start;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    .lp-stat-pill--row .l { margin-top: 0; }
    .lp-stat-pill--row .n {
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 0;
    }

    .lp-empty {
        text-align: center;
        padding: 2.5rem 1.5rem;
    }
    .lp-empty h3 {
        margin: 0 0 0.4rem;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .lp-empty p {
        margin: 0 0 1.1rem;
        color: var(--lp-text);
        max-width: 28rem;
        margin-inline: auto;
    }

    .lp-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 1rem;
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--lp-blue-hover);
        text-decoration: none;
    }
    .lp-back-link:hover { text-decoration: underline; }

    .lp-section-head {
        margin-bottom: 1.25rem;
    }
    .lp-section-head .lp-h1 {
        font-size: clamp(1.35rem, 3vw, 1.85rem);
        margin: 0.25rem 0 0.4rem;
    }
    .lp-section-head .lp-lead {
        margin: 0;
        font-size: 0.95rem;
    }

    [x-cloak] { display: none !important; }
</style>
