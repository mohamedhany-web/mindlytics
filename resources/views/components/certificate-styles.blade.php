<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
    .certificate-container {
        overflow: auto;
        padding: 1rem;
    }

    .certificate-template {
        position: relative;
        background: #fff;
        box-sizing: border-box;
    }

    .template-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .template-option {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.75rem;
        cursor: pointer;
        background: #fff;
        transition: 0.2s ease;
        text-align: center;
    }

    .template-option:hover { border-color: #00334E; }
    .template-option.active {
        border-color: #00334E;
        box-shadow: 0 0 0 3px rgba(0, 51, 78, 0.15);
    }

    .template-preview {
        height: 64px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 2px solid #00334E;
        background: #fff;
        background-image:
            linear-gradient(135deg, #00334E 0 18%, transparent 18%),
            linear-gradient(225deg, #00A9A5 0 14%, transparent 14%);
    }

    .preview-achievement { border-color: #00334E; }
    .preview-achievement-teal {
        border-color: #0f766e;
        background-image:
            linear-gradient(135deg, #0f766e 0 18%, transparent 18%),
            linear-gradient(225deg, #14b8a6 0 14%, transparent 14%);
    }
    .preview-achievement-navy {
        border-color: #0b1f33;
        background-image:
            linear-gradient(135deg, #0b1f33 0 18%, transparent 18%),
            linear-gradient(225deg, #1d4e89 0 14%, transparent 14%);
    }

    /* Achievement certificate family */
    .template-achievement,
    .template-econev {
        position: relative;
        background: #ffffff !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: 0 18px 50px rgba(0, 51, 78, 0.16);
        padding: 0 !important;
        color: #00334E;
        font-family: 'Montserrat', 'Segoe UI', sans-serif;
        overflow: hidden;
        --cert-primary: #00334E;
        --cert-accent: #00A9A5;
        --cert-soft: #D7DEE6;
    }

    .template-achievement.theme-teal {
        --cert-primary: #0f4c5c;
        --cert-accent: #14b8a6;
        --cert-soft: #cceceb;
        color: #0f4c5c;
    }

    .template-achievement.theme-deep {
        --cert-primary: #0b1f33;
        --cert-accent: #1d4e89;
        --cert-soft: #c5ced8;
        color: #0b1f33;
    }

    .econev-art {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    .shape-primary { fill: var(--cert-primary); }
    .shape-accent { fill: var(--cert-accent); }
    .shape-soft { fill: var(--cert-soft); }
    .shape-accent-stroke { stroke: var(--cert-accent); }
    .shape-primary-stroke { stroke: var(--cert-primary); }

    .econev-logo {
        position: absolute;
        top: 6.5%;
        left: 5.5%;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 42%;
    }

    .econev-logo-img {
        max-height: 52px;
        max-width: 120px;
        object-fit: contain;
    }

    .econev-logo-mark {
        position: relative;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
    }

    .econev-logo-mark span {
        position: absolute;
        width: 22px;
        height: 22px;
        transform: rotate(45deg);
        border-radius: 2px;
    }

    .econev-logo-mark .d1 { top: 2px; left: 0; background: #9AA3AD; }
    .econev-logo-mark .d2 { top: 10px; left: 12px; background: var(--cert-primary); }
    .econev-logo-mark .d3 { top: 0; left: 14px; background: #B7BFC8; width: 14px; height: 14px; opacity: 0.9; }

    .econev-logo-text {
        display: flex;
        flex-direction: column;
        line-height: 1.05;
        color: var(--cert-primary);
        min-width: 0;
    }

    .econev-logo-text strong {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 0.4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .econev-logo-text span {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.4px;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .econev-seal {
        position: absolute;
        top: 3.5%;
        right: 4.5%;
        z-index: 2;
        width: 118px;
        height: auto;
    }

    .econev-seal svg { width: 100%; height: auto; display: block; }

    .econev-stamp-img {
        width: 118px;
        height: 118px;
        object-fit: contain;
        display: block;
    }

    .econev-content {
        position: absolute;
        left: 50%;
        top: 18%;
        transform: translateX(-50%);
        z-index: 2;
        width: 68%;
        text-align: center;
    }

    .econev-title {
        margin: 0;
        font-size: 58px;
        font-weight: 800;
        letter-spacing: 2px;
        color: var(--cert-primary);
        line-height: 1;
    }

    .econev-subtitle {
        margin: 8px 0 0;
        font-size: 28px;
        font-weight: 500;
        color: var(--cert-primary);
        line-height: 1.15;
    }

    .econev-presented {
        margin: 22px 0 0;
        font-size: 13px;
        font-weight: 500;
        color: #7A8088;
    }

    .econev-name {
        margin-top: 10px;
        font-family: 'Great Vibes', cursive;
        font-size: 56px;
        line-height: 1.15;
        color: var(--cert-primary);
        font-weight: 400;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .econev-name-line {
        width: 72%;
        max-width: 420px;
        height: 1px;
        background: #CFD4DA;
        margin: 4px auto 0;
    }

    .econev-body {
        margin: 16px auto 0;
        max-width: 520px;
        font-size: 12px;
        line-height: 1.55;
        color: #6B7280;
        font-weight: 500;
    }

    .econev-footer {
        position: absolute;
        left: 14%;
        right: 14%;
        bottom: 10%;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 40px;
    }

    .econev-meta {
        width: 180px;
        text-align: center;
    }

    .econev-meta-value {
        min-height: 28px;
        font-size: 14px;
        font-weight: 600;
        color: var(--cert-primary);
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .econev-sig-script {
        font-family: 'Great Vibes', cursive;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
    }

    .econev-sig-img {
        height: 42px;
        max-width: 170px;
        object-fit: contain;
    }

    .econev-meta-line {
        height: 1px;
        background: #CFD4DA;
        margin: 6px 0 8px;
    }

    .econev-meta-label {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1.6px;
        color: var(--cert-primary);
    }

    .econev-serial {
        position: absolute;
        bottom: 2.8%;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        font-family: ui-monospace, monospace;
        color: #6b7280;
        background: rgba(255,255,255,0.85);
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
    }

    .econev-serial span {
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #9ca3af;
    }

    .econev-serial strong {
        color: var(--cert-primary);
        letter-spacing: 0.4px;
    }

    @media print {
        .template-achievement,
        .template-econev {
            box-shadow: none !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
