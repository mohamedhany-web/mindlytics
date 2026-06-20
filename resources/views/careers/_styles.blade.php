{{-- أنماط صفحات التوظيف — متوافقة مع الصفحة الرئيسية --}}
<style>
    .hero-section {
        background: linear-gradient(165deg, #f0f9ff 0%, #e0f2fe 30%, #eff6ff 60%, #f0fdf4 85%, #ffffff 100%);
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 100vw;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
        pointer-events: none;
    }
    .hero-glow {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.18), transparent);
        border-radius: 50%;
        filter: blur(80px);
        top: -280px;
        right: -200px;
        pointer-events: none;
    }
    .careers-badge {
        background: linear-gradient(to right, #eff6ff, #ecfdf5);
        color: #1e40af;
        border: 1px solid rgba(59, 130, 246, 0.25);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);
    }
    .section-title {
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 72px;
        height: 3px;
        background: linear-gradient(135deg, #2563eb, #0891b2, #10b981);
        border-radius: 4px;
    }
    .section-bar {
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        border-radius: 2px;
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
        border-radius: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.12);
        border-color: rgba(59, 130, 246, 0.2);
    }
    .stat-card:hover::after { opacity: 1; }
    .job-card {
        transition: all 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
        background: linear-gradient(135deg, #ffffff 0%, #fafcff 100%);
        position: relative;
        overflow: hidden;
        border: 1.5px solid rgba(226, 232, 240, 0.8);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border-radius: 1.25rem;
        text-decoration: none;
        color: inherit;
    }
    .job-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981, #3b82f6);
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    .job-card:hover::before { opacity: 1; }
    .job-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 45px rgba(37, 99, 235, 0.12);
        border-color: rgba(59, 130, 246, 0.25);
    }
    .job-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .job-meta-chip.blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .job-meta-chip.green { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .job-meta-chip.violet { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .content-panel {
        background: linear-gradient(135deg, #ffffff 0%, #fafcff 100%);
        border: 1.5px solid rgba(226, 232, 240, 0.8);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .content-panel-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(to right, #f8fafc, #ffffff);
    }
    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9375rem;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px dashed #e2e8f0;
    }
    .form-section-num {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #2563eb, #0891b2);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .careers-input {
        width: 100%;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .careers-input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
    }
    .careers-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.375rem;
    }
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        background: linear-gradient(to bottom, #f8fafc, #fff);
        padding: 1.25rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .upload-zone:hover {
        border-color: #7dd3fc;
        background: #f0f9ff;
    }
    .step-item {
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
    }
    .step-num {
        width: 2rem;
        height: 2rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .req-list li {
        position: relative;
        padding-right: 1.25rem;
        margin-bottom: 0.5rem;
        color: #475569;
        line-height: 1.6;
    }
    .req-list li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0.55rem;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #10b981);
    }
    .careers-btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 35%, #0369a1 60%, #475569 80%, #dc2626 100%);
        color: #fff;
        font-weight: 800;
        font-size: 0.9375rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(14, 165, 233, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .careers-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(14, 165, 233, 0.45);
    }
    .fade-in-up {
        animation: careersFadeUp 0.7s ease-out both;
    }
    @keyframes careersFadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
