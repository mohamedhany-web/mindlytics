{{-- Shared Educo styles for community hub + catalogs + details --}}
<style>
    .cm {
        --cm-ink: var(--lp-primary);
        --cm-mute: var(--lp-text);
    }
    .cm-stage {
        position: relative;
        overflow: hidden;
        background: var(--lp-stage);
        color: var(--lp-stage-ink);
        padding-block: 2.75rem 2.15rem;
    }
    .cm-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 68% 55% at 90% 8%, rgba(201, 176, 235, 0.45), transparent 58%),
            radial-gradient(ellipse 50% 48% at 8% 78%, rgba(126, 143, 212, 0.28), transparent 55%),
            linear-gradient(180deg, rgba(238, 242, 249, 0.5) 0%, var(--lp-stage) 100%);
        pointer-events: none;
    }
    .cm-stage .lp-container { position: relative; z-index: 1; }
    .cm-crumb {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        font-size: 0.8rem;
        color: var(--lp-stage-mute);
        margin-bottom: 0.85rem;
    }
    .cm-crumb a {
        color: var(--lp-stage-ink);
        text-decoration: none;
        font-weight: 600;
    }
    .cm-crumb a:hover { color: var(--lp-blue-hover); }
    .cm-brand {
        font-weight: 800;
        font-size: clamp(1.15rem, 2.5vw, 1.5rem);
        margin: 0 0 0.35rem;
    }
    .cm-brand span { color: var(--lp-blue-hover); }
    .cm-stage .lp-h1 { margin: 0.25rem 0 0.65rem; max-width: 18ch; }
    .cm-stage .lp-lead { margin: 0; max-width: 40rem; color: var(--lp-stage-mute); }
    .cm-stage-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
    }
    .cm-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1.25rem;
    }
    .cm-tab {
        display: inline-flex;
        align-items: center;
        padding: 0.55rem 1rem;
        border-radius: var(--lp-radius-pill);
        font-size: 0.8125rem;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid rgba(126, 143, 212, 0.28);
        background: rgba(255, 255, 255, 0.72);
        color: var(--lp-stage-ink);
    }
    .cm-tab:hover { border-color: rgba(126, 143, 212, 0.45); color: var(--lp-blue-hover); }
    .cm-tab.is-active {
        background: var(--lp-yellow);
        border-color: transparent;
        color: var(--lp-yellow-ink);
    }
    .cm-body {
        background: var(--lp-cream);
        padding-block: 2rem 4rem;
    }
    .cm-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        align-items: center;
    }
    .cm-search {
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
    .cm-search input {
        border: 0;
        background: transparent;
        font: inherit;
        color: var(--cm-ink);
        outline: none;
        width: 100%;
        min-width: 0;
    }
    .cm-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-bottom: 1.35rem;
        align-items: center;
    }
    .cm-chips > span {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--cm-mute);
        margin-inline-end: 0.15rem;
    }
    .cm-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid rgba(126, 143, 212, 0.22);
        background: rgba(255, 255, 255, 0.85);
        color: var(--cm-ink);
    }
    .cm-chip.is-active {
        background: var(--lp-yellow);
        border-color: transparent;
        color: var(--lp-yellow-ink);
    }
    .cm-chip small { opacity: 0.75; font-weight: 700; }
    .cm-results {
        margin: 0 0 1rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--cm-mute);
    }
    .cm-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    @media (min-width: 768px) {
        .cm-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (min-width: 1100px) {
        .cm-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .cm-card {
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
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .cm-card:hover {
        transform: translateY(-3px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 14px 32px rgba(63, 51, 86, 0.09);
    }
    .cm-card__media {
        aspect-ratio: 16 / 7;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.5), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.5rem;
        color: rgba(63, 51, 86, 0.28);
    }
    .cm-card__body {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        flex: 1;
        padding: 0.95rem 1rem 1.05rem;
    }
    .cm-card__title {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--cm-ink);
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cm-card__meta {
        margin: 0;
        font-size: 0.75rem;
        font-weight: 650;
        color: var(--cm-mute);
    }
    .cm-badge {
        display: inline-flex;
        align-self: flex-start;
        padding: 0.22rem 0.5rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        background: rgba(126, 143, 212, 0.14);
        color: var(--lp-blue-hover);
        border: 1px solid rgba(126, 143, 212, 0.2);
    }
    .cm-card__foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        margin-top: auto;
        padding-top: 0.55rem;
        border-top: 1px solid rgba(126, 143, 212, 0.12);
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--cm-mute);
    }
    .cm-card__foot strong { color: var(--lp-blue-hover); }
    .cm-empty {
        text-align: center;
        background: rgba(255, 255, 255, 0.78);
        border: 1px dashed rgba(126, 143, 212, 0.3);
        border-radius: var(--lp-radius);
        padding: 2.5rem 1.5rem;
    }
    .cm-empty .lp-h1 {
        font-size: clamp(1.25rem, 3vw, 1.65rem);
        margin: 0.35rem 0 0.65rem;
    }
    .cm-pagination { margin-top: 2rem; display: flex; justify-content: center; }
    .cm-link {
        color: var(--lp-blue-hover);
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .cm-panel {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.16);
        border-radius: var(--lp-radius);
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        overflow: hidden;
    }
    .cm-panel__head {
        padding: 0.85rem 1.1rem;
        border-bottom: 1px solid rgba(126, 143, 212, 0.14);
        background: rgba(238, 242, 249, 0.55);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.65rem;
    }
    .cm-panel__head h2 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--cm-ink);
    }
    .cm-panel__body { padding: 1.1rem; }
    .cm-detail-grid {
        display: grid;
        gap: 1.25rem;
    }
    @media (min-width: 960px) {
        .cm-detail-grid {
            grid-template-columns: minmax(240px, 0.9fr) minmax(0, 1.6fr);
            align-items: start;
        }
        .cm-detail-side { position: sticky; top: 6.5rem; }
    }
    .cm-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }
    .cm-file-list {
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 60vh;
        overflow: auto;
    }
    .cm-file-list li {
        border-bottom: 1px solid rgba(126, 143, 212, 0.1);
    }
    .cm-file-row {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 1rem;
        transition: background 0.15s ease;
    }
    .cm-file-row:hover,
    .cm-file-row[data-active="yes"] {
        background: rgba(235, 230, 246, 0.55);
    }
    .cm-file-icon {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--lp-secondary);
        color: var(--lp-blue-hover);
        font-size: 0.8rem;
    }
    .cm-prose {
        margin: 0;
        color: var(--cm-mute);
        line-height: 1.7;
        white-space: pre-line;
    }
    .cm-code {
        margin: 0;
        padding: 1rem;
        border-radius: 12px;
        background: #2a2438;
        color: #f3eefc;
        font-size: 0.85rem;
        overflow: auto;
        white-space: pre-wrap;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .cm-metric {
        display: inline-flex;
        gap: 0.35rem;
        padding: 0.55rem 0.85rem;
        border-radius: 10px;
        background: rgba(126, 143, 212, 0.12);
        color: var(--cm-ink);
        font-size: 0.85rem;
        font-weight: 700;
    }
    .cm-people-grid {
        display: grid;
        gap: 1.15rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .cm-people-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .cm-people-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .cm-person-card {
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.14);
        border-radius: var(--lp-radius);
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .cm-person-card:hover {
        transform: translateY(-3px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 14px 32px rgba(63, 51, 86, 0.09);
    }
    .cm-person-card__photo {
        aspect-ratio: 4 / 3;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        position: relative;
        overflow: hidden;
    }
    .cm-person-card__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .cm-person-card__photo .cm-initial {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: rgba(63, 51, 86, 0.28);
    }
    .cm-person-card__body { padding: 1.05rem 1.15rem 1.2rem; }
    .cm-person-card__body h2 {
        margin: 0 0 0.35rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--cm-ink);
    }
    .cm-person-card__body p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--cm-mute);
        line-height: 1.5;
    }
    .cm-socials {
        position: absolute;
        inset-inline-start: 0.75rem;
        bottom: 0.75rem;
        display: flex;
        gap: 0.4rem;
        z-index: 2;
    }
    .cm-social {
        width: 2.15rem;
        height: 2.15rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.92);
        color: var(--cm-ink);
        border: 1px solid rgba(126, 143, 212, 0.2);
        text-decoration: none;
        font-size: 0.85rem;
    }
    .cm-profile-layout {
        display: grid;
        gap: 1.5rem;
    }
    @media (min-width: 720px) {
        .cm-profile-layout {
            grid-template-columns: 160px minmax(0, 1fr);
            align-items: start;
        }
    }
    .cm-profile-photo {
        width: 100%;
        max-width: 160px;
        aspect-ratio: 1;
        border-radius: 18px;
        overflow: hidden;
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        position: relative;
        box-shadow: 0 14px 32px rgba(63, 51, 86, 0.1);
    }
    .cm-profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cm-profile-photo .cm-initial {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: rgba(63, 51, 86, 0.28);
    }
    @media (prefers-reduced-motion: reduce) {
        .cm-card:hover, .cm-person-card:hover { transform: none; }
    }

    /* Skin for legacy preview widgets inside Educo panels */
    .cm-skin #previewContainer,
    .cm-skin .dataset-preview-table-wrap {
        background: transparent;
    }
    .cm-skin .dataset-preview-table thead th {
        background: var(--lp-secondary);
        color: var(--cm-ink);
        border-color: rgba(126, 143, 212, 0.18);
    }
    .cm-skin .dataset-preview-table tbody td {
        color: var(--cm-mute);
        border-color: rgba(126, 143, 212, 0.1);
    }
    .cm-skin .dataset-preview-table tbody tr:hover { background: rgba(235, 230, 246, 0.4); }
</style>
