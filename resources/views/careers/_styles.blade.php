<style>
    .hero-careers {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-careers::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h40v40H0V0zm2 2h36v36H2V2z'/%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.6;
    }
    .section-bar {
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        border-radius: 2px;
    }
    .careers-card {
        transition: all 0.25s ease;
        border: 2px solid #e2e8f0;
    }
    .careers-card:hover {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 12px 32px rgba(59, 130, 246, 0.12);
        transform: translateY(-2px);
    }
    .careers-input {
        width: 100%;
        border-radius: 0.75rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .careers-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }
    .careers-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.375rem;
    }
    .careers-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        font-weight: 800;
        font-size: 0.875rem;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .careers-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.32);
    }
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        background: linear-gradient(to bottom, #f8fafc, #fff);
        padding: 1.25rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .upload-zone:hover {
        border-color: #93c5fd;
        background: #f0f9ff;
    }
    .job-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.15);
        color: #dbeafe;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .step-item {
        display: flex;
        gap: 0.75rem;
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
</style>
