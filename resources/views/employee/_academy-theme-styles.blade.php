<style>
    /* أنماط موحّدة مع الصفحة الرئيسية للأكاديمية */
    .emp-dash-bg {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background: linear-gradient(165deg, #ffffff 0%, #f8fafc 40%, #f0f7ff 70%, #ffffff 100%);
    }
    .emp-dash-bg::before,
    .emp-dash-bg::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.35;
    }
    .emp-dash-bg::before {
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.22), transparent);
        top: -80px;
        right: -60px;
    }
    .emp-dash-bg::after {
        width: 360px;
        height: 360px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.18), transparent);
        bottom: -80px;
        left: -40px;
    }

    .emp-dash-hero {
        background: linear-gradient(165deg, #f0f9ff 0%, #e0f2fe 30%, #eff6ff 60%, #f0fdf4 85%, #ffffff 100%);
        border-radius: 24px;
        border: 1px solid rgba(59, 130, 246, 0.12);
        box-shadow: 0 4px 24px rgba(37, 99, 235, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        padding: 1.75rem 1.5rem;
    }
    @media (min-width: 640px) {
        .emp-dash-hero { padding: 2rem 2.25rem; }
    }
    .emp-dash-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
        pointer-events: none;
    }
    .emp-dash-hero-accent {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #2563eb, #0891b2, #10b981);
    }

    .academy-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 700;
        background: linear-gradient(to right, #eff6ff, #ecfdf5);
        color: #1e40af;
        border: 1px solid rgba(59, 130, 246, 0.2);
        box-shadow: 0 1px 2px rgba(59, 130, 246, 0.08);
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(59, 130, 246, 0.08);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #2563eb, #0891b2);
        border-radius: 0 0 0 3px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.12), 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: rgba(59, 130, 246, 0.2);
    }
    .stat-card:hover::after { opacity: 1; }

    .academy-panel {
        background: #fff;
        border-radius: 24px;
        border: 1px solid rgba(59, 130, 246, 0.1);
        box-shadow: 0 4px 24px rgba(37, 99, 235, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .academy-panel-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(59, 130, 246, 0.08);
        background: linear-gradient(to left, rgba(240, 249, 255, 0.9), #fff);
    }

    .glass-card {
        background: linear-gradient(to bottom, rgba(240, 249, 255, 0.85), rgba(224, 242, 254, 0.75));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        border-color: rgba(59, 130, 246, 0.28);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }

    .btn-academy-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.35rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.875rem;
        color: #fff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 35%, #0369a1 60%, #475569 85%, #dc2626 100%);
        box-shadow: 0 4px 16px rgba(14, 165, 233, 0.35), 0 2px 8px rgba(220, 38, 38, 0.15);
        transition: all 0.3s ease;
    }
    .btn-academy-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(14, 165, 233, 0.45), 0 4px 12px rgba(220, 38, 38, 0.2);
        color: #fff;
    }

    .btn-academy-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.35rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.875rem;
        color: #0284c7;
        text-decoration: none;
        background: #fff;
        border: 2px solid #0ea5e9;
        transition: all 0.3s ease;
    }
    .btn-academy-secondary:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);
        color: #fff;
        transform: translateY(-2px);
    }

    .academy-task-row {
        border-bottom: 1px solid rgba(241, 245, 249, 0.9);
        transition: background 0.2s ease;
    }
    .academy-task-row:last-child { border-bottom: 0; }
    .academy-task-row:hover {
        background: linear-gradient(to left, rgba(14, 165, 233, 0.05), transparent);
    }

    .academy-icon-box {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        flex-shrink: 0;
    }
</style>
