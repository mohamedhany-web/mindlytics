@extends('layouts.student-dashboard')

@section('title', __('student.my_certificates_title'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
        gap: 12px;
    }
    .cert-card {
        display: flex; flex-direction: column; gap: 10px;
        padding: 16px; background: var(--ml-surface);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r);
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .cert-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .cert-card .ico {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 1.15rem;
    }
    .cert-card h3 {
        margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .cert-card .course {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.45;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .cert-meta {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        font-size: 11px; color: var(--ml-muted);
    }
    .cert-meta .num {
        font-family: ui-monospace, monospace; padding: 2px 7px; border-radius: 6px;
        background: var(--ml-well); font-weight: 700; color: var(--ml-ink);
    }
    .cert-go {
        margin-top: auto; padding-top: 4px;
        font-size: 12px; font-weight: 700; color: var(--ml-teal-deep);
        display: inline-flex; align-items: center; gap: 6px;
    }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.my_certificates_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.my_certificates_title') }}</span>
            </nav>
            <h1>{{ __('student.my_certificates_title') }}</h1>
            <p class="sub">{{ __('student.certificates_subtitle') }}</p>
        </div>
        @if(isset($stats))
            <div class="oc-signals">
                <span class="oc-signal oc-signal-live">{{ __('student.total_certificates') }}: {{ $stats['total'] ?? 0 }}</span>
                <span class="oc-signal oc-signal-hot">{{ __('student.issued_label') }}: {{ $stats['issued'] ?? 0 }}</span>
            </div>
        @endif
    </header>

    @if(isset($stats))
        <div class="oc-pulse" aria-label="{{ __('student.my_certificates_title') }}">
            <div>
                <span class="lbl">{{ __('student.total_certificates') }}</span>
                <span class="val teal">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div>
                <span class="lbl">{{ __('student.issued_label') }}</span>
                <span class="val">{{ $stats['issued'] ?? 0 }}</span>
            </div>
        </div>
    @endif

    @if(isset($certificates) && $certificates->count() > 0)
        <p class="oc-section-title">{{ __('student.issued_certificates') }}</p>
        <div class="cert-grid">
            @foreach($certificates as $certificate)
                <a href="{{ route('student.certificates.show', $certificate) }}" class="cert-card">
                    <div class="ico" aria-hidden="true"><i class="fas fa-certificate"></i></div>
                    <h3>{{ $certificate->title ?? $certificate->course_name ?? __('student.completion_certificate') }}</h3>
                    @if($certificate->course)
                        <p class="course">{{ $certificate->course->title }}</p>
                    @endif
                    <div class="cert-meta">
                        <span>
                            <i class="fas fa-calendar" style="color:var(--ml-teal-deep);margin-inline-end:4px"></i>
                            {{ $certificate->issued_at?->format('Y-m-d')
                                ?? $certificate->issue_date?->format('Y-m-d')
                                ?? '—' }}
                        </span>
                        @if($certificate->certificate_number)
                            <span class="num">#{{ substr($certificate->certificate_number, -6) }}</span>
                        @endif
                    </div>
                    <span class="cert-go">
                        {{ __('student.view_certificate') }}
                        <i class="fas fa-arrow-left text-[10px]"></i>
                    </span>
                </a>
            @endforeach
        </div>
        @if($certificates->hasPages())
            <div style="margin-top:20px;display:flex;justify-content:center">
                {{ $certificates->links() }}
            </div>
        @endif
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-certificate"></i></div>
            <h3>{{ __('student.no_certificates') }}</h3>
            <p>{{ __('student.no_certificates_desc') }}</p>
            <div style="margin-top:16px">
                <a href="{{ route('my-courses.index') }}" class="oc-btn">
                    <i class="fas fa-book-open text-xs"></i>
                    {{ __('student.view_my_courses') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
