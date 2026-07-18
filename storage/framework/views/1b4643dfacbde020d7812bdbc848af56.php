
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
<style>
.oc {
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
    --ml-ease: cubic-bezier(0.22, 1, 0.36, 1);
    font-family: 'IBM Plex Sans Arabic', 'Tajawal', 'Cairo', sans-serif;
    color: var(--ml-ink);
    width: 100%;
    max-width: none;
    padding-block: 4px 32px;
}
.oc-chrome {
    display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
    gap: 12px; padding: 8px 0 14px; border-bottom: 1px solid var(--ml-line); margin-bottom: 20px;
}
.oc-crumb { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--ml-muted); margin-bottom: 6px; }
.oc-crumb a { color: var(--ml-teal-deep); font-weight: 600; text-decoration: none; }
.oc-crumb a:hover { text-decoration: underline; }
.oc-chrome h1 { margin: 0; font-size: clamp(1.2rem, 2vw, 1.5rem); font-weight: 700; letter-spacing: -0.015em; line-height: 1.3; }
.oc-chrome .sub { margin: 4px 0 0; font-size: 13px; color: var(--ml-muted); max-width: 56ch; line-height: 1.55; }
.oc-signals { display: flex; flex-wrap: wrap; gap: 8px; }
.oc-signal {
    display: inline-flex; align-items: center; gap: 6px; min-height: 28px;
    padding: 0 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
    background: var(--ml-well); color: var(--ml-muted);
}
.oc-signal-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
.oc-signal-hot { background: rgba(255, 210, 63, 0.35); color: var(--ml-yellow-ink); }
.oc-signal-warn { background: rgba(245, 158, 11, 0.16); color: #92400e; }

.oc-stage {
    position: relative; padding: 18px 20px; margin-bottom: 20px; background: var(--ml-surface);
    border-radius: calc(var(--ml-r) + 4px); border: 1px solid var(--ml-line);
    box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 10px 30px rgba(26, 34, 56, 0.04);
}
.oc-stage::before {
    content: ''; position: absolute; inset-block: 16px; inset-inline-start: 0; width: 3px;
    border-radius: 999px; background: linear-gradient(180deg, var(--ml-teal), rgba(73,164,162,0.2));
}
.oc-eyebrow {
    display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;
    font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
}
.oc-eyebrow em {
    font-style: normal; padding: 2px 8px; border-radius: 6px;
    background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
}
.oc-stage h2 { margin: 0 0 6px; font-size: clamp(1.1rem, 1.8vw, 1.35rem); font-weight: 700; line-height: 1.35; }
.oc-copy { margin: 0; font-size: 13px; line-height: 1.65; color: var(--ml-muted); max-width: 52ch; }
.oc-meter { height: 4px; width: 100%; max-width: 240px; margin-top: 12px; border-radius: 999px; background: var(--ml-well); overflow: hidden; }
.oc-meter > i { display: block; height: 100%; background: var(--ml-teal); border-radius: inherit; }

.oc-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    min-height: 40px; padding: 0 16px; border-radius: 12px; background: var(--ml-teal);
    color: #fff !important; font-size: 13px; font-weight: 700; text-decoration: none !important;
    border: 0; box-shadow: 0 8px 18px rgba(73, 164, 162, 0.2);
    transition: background var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
}
.oc-btn:hover { background: var(--ml-teal-deep); transform: translateY(-1px); }
.oc-btn-quiet {
    background: transparent; color: var(--ml-ink) !important; box-shadow: none;
    border: 1px solid var(--ml-line);
}
.oc-btn-quiet:hover { background: var(--ml-well); transform: none; }

.oc-pulse {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1px; margin-bottom: 20px; background: var(--ml-line);
    border: 1px solid var(--ml-line); border-radius: var(--ml-r); overflow: hidden;
}
.oc-pulse > div { background: var(--ml-surface); padding: 14px 16px; }
.oc-pulse .lbl { display: block; font-size: 11px; font-weight: 700; color: var(--ml-muted); margin-bottom: 4px; }
.oc-pulse .val { font-size: 1.25rem; font-weight: 700; color: var(--ml-ink); letter-spacing: -0.02em; }
.oc-pulse .val.teal { color: var(--ml-teal-deep); }
.oc-pulse .val.hot { color: var(--ml-yellow-ink); }

.oc-panel {
    background: var(--ml-surface); border: 1px solid var(--ml-line);
    border-radius: var(--ml-r); padding: 16px 18px; margin-bottom: 16px;
}
.oc-label { margin: 0 0 10px; font-size: 11px; font-weight: 700; color: var(--ml-muted); letter-spacing: 0.02em; }
.oc-facts { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px; }
.oc-facts li {
    display: flex; flex-direction: column; gap: 4px; padding: 10px 12px;
    border-radius: 10px; background: var(--ml-well); font-size: 13px;
}
.oc-facts .k { color: var(--ml-muted); font-weight: 600; font-size: 11px; }
.oc-facts .v { font-weight: 700; color: var(--ml-ink); }

.oc-nav { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.oc-chip {
    display: inline-flex; align-items: center; gap: 6px; min-height: 34px; padding: 0 12px;
    border-radius: 999px; font-size: 12px; font-weight: 700; text-decoration: none !important;
    border: 1px solid var(--ml-line); background: var(--ml-surface); color: var(--ml-ink) !important;
    transition: background var(--ml-fast) ease, border-color var(--ml-fast) ease;
}
.oc-chip:hover { background: rgba(73, 164, 162, 0.1); border-color: rgba(73, 164, 162, 0.35); color: var(--ml-teal-deep) !important; }
.oc-chip i { color: var(--ml-teal-deep); font-size: 11px; }

.oc-hub {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px; margin-bottom: 20px;
}
.oc-hub a {
    display: block; padding: 14px; background: var(--ml-surface); border: 1px solid var(--ml-line);
    border-radius: var(--ml-r); text-decoration: none !important; color: inherit !important;
    transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
}
.oc-hub a:hover {
    border-color: rgba(73, 164, 162, 0.35);
    box-shadow: 0 10px 24px rgba(26, 34, 56, 0.06);
    transform: translateY(-1px);
}
.oc-hub .ico {
    width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); margin-bottom: 10px;
}
.oc-hub strong { display: block; font-size: 14px; font-weight: 700; margin-bottom: 4px; }
.oc-hub span { display: block; font-size: 12px; color: var(--ml-muted); line-height: 1.45; }
.oc-hub .count {
    float: inline-end; min-height: 22px; padding: 0 8px; border-radius: 999px;
    font-size: 11px; font-weight: 700; background: var(--ml-well); color: var(--ml-muted);
}

.oc-list { display: flex; flex-direction: column; gap: 10px; }
.oc-row {
    display: grid; grid-template-columns: 52px minmax(0, 1fr) auto; gap: 14px; align-items: center;
    padding: 14px; background: var(--ml-surface); border: 1px solid var(--ml-line);
    border-radius: var(--ml-r); text-decoration: none !important; color: inherit !important;
    transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
}
.oc-row:hover {
    border-color: rgba(73, 164, 162, 0.35);
    box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
    transform: translateY(-1px);
}
.oc-row.is-static { cursor: default; }
.oc-row.is-static:hover { transform: none; box-shadow: none; border-color: var(--ml-line); }
.oc-ico {
    width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
    background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 1.1rem; flex-shrink: 0;
}
.oc-ico.warn { background: rgba(245, 158, 11, 0.14); color: #b45309; }
.oc-body { min-width: 0; }
.oc-body h3 { margin: 0 0 4px; font-size: 15px; font-weight: 700; line-height: 1.35; }
.oc-body .meta { margin: 0 0 6px; font-size: 12px; color: var(--ml-muted); }
.oc-prog { display: flex; align-items: center; gap: 10px; }
.oc-prog .bar { flex: 1; height: 4px; max-width: 180px; border-radius: 999px; background: var(--ml-well); overflow: hidden; }
.oc-prog .bar > i { display: block; height: 100%; background: var(--ml-teal); border-radius: inherit; }
.oc-prog .pct { font-size: 12px; font-weight: 700; color: var(--ml-teal-deep); }
.oc-side { font-size: 12px; font-weight: 700; color: var(--ml-teal-deep); white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; }
.oc-badge {
    display: inline-flex; align-items: center; min-height: 24px; padding: 0 8px; border-radius: 6px;
    font-size: 11px; font-weight: 700;
}
.oc-badge-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
.oc-badge-ok { background: rgba(16, 185, 129, 0.12); color: #047857; }
.oc-badge-warn { background: rgba(245, 158, 11, 0.16); color: #92400e; }
.oc-badge-bad { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }

.oc-empty {
    text-align: center; padding: 40px 20px; background: var(--ml-surface);
    border: 1px dashed rgba(26, 34, 56, 0.14); border-radius: calc(var(--ml-r) + 4px);
}
.oc-empty .icon {
    width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 22px;
}
.oc-empty h3 { margin: 0 0 6px; font-size: 1.05rem; font-weight: 700; }
.oc-empty p { margin: 0 auto; max-width: 40ch; font-size: 13px; color: var(--ml-muted); line-height: 1.6; }

.oc-section-title {
    margin: 8px 0 12px; font-size: 13px; font-weight: 700; color: var(--ml-ink);
}

@media (max-width: 640px) {
    .oc-row { grid-template-columns: 44px minmax(0, 1fr); }
    .oc-side { grid-column: 1 / -1; justify-content: flex-end; }
    .oc-ico { width: 44px; height: 44px; border-radius: 12px; }
}
</style>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/partials/los-styles.blade.php ENDPATH**/ ?>