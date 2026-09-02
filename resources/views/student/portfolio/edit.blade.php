@extends('layouts.student-dashboard')

@section('title', __('student.pf_edit_resubmit'))
@section('header', __('student.pf_edit_resubmit'))

@push('styles')
<style>
    .sp-pf-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-pf-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -30px;
        top: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.25), transparent 70%);
        pointer-events: none;
    }
    .sp-pf-input {
        width: 100%;
        border-radius: 30px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #f7f7f5;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--sp-text);
    }
    .sp-pf-input:focus { outline: none; box-shadow: 0 0 0 2px var(--sp-accent); }
    .sp-pf-textarea { border-radius: 20px; min-height: 100px; resize: vertical; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-5xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.portfolio.index') }}" class="sp-link">{{ __('student.pf_page_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)] truncate max-w-[50vw]">{{ $project->title }}</span>
    </nav>

    @if($errors->any())
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            <ul class="space-y-1 m-0 p-0 list-none">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if($project->instructor_notes || $project->rejected_reason)
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-amber-soft);color:var(--sp-accent-text)">
            <p class="font-extrabold m-0 mb-1">{{ __('student.pf_instructor_notes') }}</p>
            <p class="m-0 whitespace-pre-line font-bold opacity-90">{{ $project->instructor_notes ?: $project->rejected_reason }}</p>
        </div>
    @endif

    <section class="sp-pf-hero">
        <div class="relative z-[1]">
            <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.pf_index_eyebrow') }}</p>
            <h2 class="text-xl sm:text-2xl font-extrabold m-0">{{ __('student.pf_edit_title', ['title' => $project->title]) }}</h2>
        </div>
    </section>

    <section class="sp-card overflow-hidden">
        <form action="{{ route('student.portfolio.update', $project) }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 md:p-8">
            @csrf
            @method('PUT')
            @include('student.portfolio._form_fields')

            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-5 mt-5 border-t border-black/5">
                <a href="{{ route('student.portfolio.index') }}" class="inline-flex items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-5 py-3.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                    {{ __('student.pf_back') }}
                </a>
                <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center rounded-[20px] border border-[var(--sp-accent)] bg-white hover:bg-[rgba(174,217,234,.15)] px-5 py-3.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                    {{ __('student.pf_save_draft') }}
                </button>
                <button type="submit" name="action" value="submit" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                    {{ __('student.pf_submit_review') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
