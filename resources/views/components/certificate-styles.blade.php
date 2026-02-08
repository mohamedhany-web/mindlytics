{{-- Certificate Styles Component --}}
{{-- Include this in the head section: @include('components.certificate-styles') --}}

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body * {
            visibility: hidden;
        }
        .certificate-container, .certificate-container * {
            visibility: visible;
        }
        .certificate-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .certificate-print {
            width: 297mm !important;
            height: 210mm !important;
            margin: 0 !important;
            padding: 20mm !important;
        }
        .no-print {
            display: none !important;
        }
    }

    /* Certificate Templates */
    .certificate-template {
        position: relative;
        overflow: hidden;
        page-break-inside: avoid;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Template 1: Classic Elegant */
    .template-classic {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f0f9ff 100%);
        border: 8px solid #1e40af;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), inset 0 0 100px rgba(30, 64, 175, 0.05);
    }

    .template-classic::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(30, 64, 175, 0.03) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .template-classic::after {
        content: '';
        position: absolute;
        inset: 20px;
        border: 2px solid rgba(30, 64, 175, 0.2);
        border-radius: 12px;
        pointer-events: none;
    }

    /* Template 2: Modern Gradient */
    .template-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        border: none;
        box-shadow: 0 25px 70px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .template-modern .certificate-watermark {
        color: rgba(255, 255, 255, 0.05);
    }

    /* Template 3: Premium Gold */
    .template-premium {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 50%, #f5f7fa 100%);
        border: 12px solid;
        border-image: linear-gradient(135deg, #d4af37, #ffd700, #ffed4e, #ffd700, #d4af37) 1;
        box-shadow: 0 30px 80px rgba(212, 175, 55, 0.3), inset 0 0 150px rgba(255, 215, 0, 0.1);
    }

    .template-premium::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(212, 175, 55, 0.03) 10px, rgba(212, 175, 55, 0.03) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255, 215, 0, 0.03) 10px, rgba(255, 215, 0, 0.03) 20px);
        pointer-events: none;
    }

    /* Template 4: Tech Blue */
    .template-tech {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border: 6px solid #3b82f6;
        box-shadow: 
            0 0 0 4px rgba(59, 130, 246, 0.2),
            0 0 0 8px rgba(59, 130, 246, 0.1),
            0 30px 80px rgba(0, 0, 0, 0.5),
            inset 0 0 100px rgba(59, 130, 246, 0.1);
        color: white;
    }

    .template-tech::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        opacity: 0.3;
        pointer-events: none;
    }

    .template-tech .certificate-watermark {
        color: rgba(255, 255, 255, 0.05);
    }

    /* Template 5: Minimalist Clean */
    .template-minimal {
        background: #ffffff;
        border: 3px solid #1f2937;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    /* Decorative Elements */
    .certificate-seal {
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        z-index: 10;
    }

    .certificate-seal::before {
        content: '';
        position: absolute;
        inset: 8px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
    }

    .certificate-seal::after {
        content: '';
        position: absolute;
        inset: 20px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
    }

    .certificate-corner {
        position: absolute;
        width: 80px;
        height: 80px;
        border: 4px solid;
        opacity: 0.3;
    }

    .corner-top-left {
        top: 20px;
        left: 20px;
        border-right: none;
        border-bottom: none;
        border-top-left-radius: 15px;
    }

    .corner-top-right {
        top: 20px;
        right: 20px;
        border-left: none;
        border-bottom: none;
        border-top-right-radius: 15px;
    }

    .corner-bottom-left {
        bottom: 20px;
        left: 20px;
        border-right: none;
        border-top: none;
        border-bottom-left-radius: 15px;
    }

    .corner-bottom-right {
        bottom: 20px;
        right: 20px;
        border-left: none;
        border-top: none;
        border-bottom-right-radius: 15px;
    }

    /* Watermark */
    .certificate-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 120px;
        font-weight: 900;
        color: rgba(0, 0, 0, 0.03);
        z-index: 1;
        white-space: nowrap;
        letter-spacing: 20px;
        user-select: none;
        pointer-events: none;
    }

    /* Signature Lines */
    .signature-line {
        border-top: 2px solid #1f2937;
        width: 150px;
        margin: 8px auto;
    }
    
    .template-modern .signature-line,
    .template-tech .signature-line,
    .template-royal .signature-line,
    .template-ocean .signature-line,
    .template-elegant .signature-line {
        border-color: rgba(255, 255, 255, 0.6);
    }

    .template-modern .signature-line,
    .template-tech .signature-line {
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* Animations */
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    .shimmer-effect {
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        background-size: 1000px 100%;
        animation: shimmer 3s infinite;
    }

    /* Template Selector */
    .template-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .template-option {
        cursor: pointer;
        padding: 1rem;
        border: 3px solid transparent;
        border-radius: 12px;
        transition: all 0.3s;
        text-align: center;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .template-option:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .template-option.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .template-preview {
        width: 100%;
        height: 80px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    /* Template 6: Royal Purple */
    .template-royal {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        border: 10px solid #8b5cf6;
        box-shadow: 
            0 0 0 5px rgba(139, 92, 246, 0.3),
            0 30px 80px rgba(139, 92, 246, 0.4),
            inset 0 0 100px rgba(139, 92, 246, 0.1);
        color: white;
    }

    .template-royal::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .template-royal .certificate-watermark {
        color: rgba(255, 255, 255, 0.05);
    }

    /* Template 7: Ocean Blue */
    .template-ocean {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 25%, #6366f1 50%, #8b5cf6 75%, #a855f7 100%);
        background-size: 400% 400%;
        animation: gradientShift 20s ease infinite;
        border: 8px solid #0ea5e9;
        box-shadow: 0 25px 70px rgba(14, 165, 233, 0.4);
        color: white;
    }

    .template-ocean .certificate-watermark {
        color: rgba(255, 255, 255, 0.05);
    }

    /* Template 8: Elegant Dark */
    .template-elegant {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e293b 100%);
        border: 6px solid #fbbf24;
        box-shadow: 
            0 0 0 3px rgba(251, 191, 36, 0.2),
            0 30px 80px rgba(0, 0, 0, 0.6),
            inset 0 0 100px rgba(251, 191, 36, 0.05);
        color: white;
    }

    .template-elegant::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(251, 191, 36, 0.03) 2px, rgba(251, 191, 36, 0.03) 4px),
            repeating-linear-gradient(90deg, transparent, transparent 2px, rgba(251, 191, 36, 0.03) 2px, rgba(251, 191, 36, 0.03) 4px);
        pointer-events: none;
    }

    .template-elegant .certificate-watermark {
        color: rgba(255, 255, 255, 0.05);
    }

    /* Template 9: Nature Green */
    .template-nature {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 25%, #a7f3d0 50%, #6ee7b7 75%, #34d399 100%);
        border: 8px solid #10b981;
        box-shadow: 0 25px 70px rgba(16, 185, 129, 0.3);
    }

    .template-nature::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 30% 40%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Template 10: Sunset Orange */
    .template-sunset {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 25%, #fcd34d 50%, #f59e0b 75%, #d97706 100%);
        border: 10px solid #f59e0b;
        box-shadow: 0 30px 80px rgba(245, 158, 11, 0.4);
    }

    .template-sunset::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(245, 158, 11, 0.05) 20px, rgba(245, 158, 11, 0.05) 40px);
        pointer-events: none;
    }

    /* Serial Number Badge */
    .serial-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 20;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        font-size: 11px;
    }

    .serial-badge-label {
        font-weight: 700;
        margin-bottom: 2px;
        font-size: 9px;
        opacity: 0.9;
    }

    .serial-badge-number {
        font-weight: 900;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        letter-spacing: 1px;
    }

    /* QR Code Styling */
    #qr-code-container {
        background: white;
        padding: 8px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }

    .preview-classic { background: linear-gradient(135deg, #f0f9ff, #ffffff); border: 3px solid #1e40af; }
    .preview-modern { background: linear-gradient(135deg, #667eea, #764ba2, #f093fb); }
    .preview-premium { background: linear-gradient(135deg, #f5f7fa, #c3cfe2); border: 3px solid #d4af37; }
    .preview-tech { background: linear-gradient(135deg, #0f172a, #1e293b); border: 3px solid #3b82f6; }
    .preview-minimal { background: #ffffff; border: 3px solid #1f2937; }
    .preview-royal { background: linear-gradient(135deg, #667eea, #764ba2, #f093fb); border: 3px solid #8b5cf6; }
    .preview-ocean { background: linear-gradient(135deg, #0ea5e9, #3b82f6, #6366f1); border: 3px solid #0ea5e9; }
    .preview-elegant { background: linear-gradient(135deg, #1e293b, #0f172a); border: 3px solid #fbbf24; }
    .preview-nature { background: linear-gradient(135deg, #ecfdf5, #a7f3d0); border: 3px solid #10b981; }
    .preview-sunset { background: linear-gradient(135deg, #fef3c7, #fcd34d); border: 3px solid #f59e0b; }
</style>
