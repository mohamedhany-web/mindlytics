@extends('layouts.student-dashboard')

@section('title', __('student.portfolio_create_title'))

@php
    $types = [
        'web_app' => ['label' => __('student.portfolio_type_web_app'), 'icon' => 'fa-globe'],
        'mobile_app' => ['label' => __('student.portfolio_type_mobile_app'), 'icon' => 'fa-mobile-alt'],
        'api' => ['label' => __('student.portfolio_type_api'), 'icon' => 'fa-plug'],
        'library' => ['label' => __('student.portfolio_type_library'), 'icon' => 'fa-book'],
        'script' => ['label' => __('student.portfolio_type_script'), 'icon' => 'fa-file-code'],
        'design' => ['label' => __('student.portfolio_type_design'), 'icon' => 'fa-palette'],
        'game' => ['label' => __('student.portfolio_type_game'), 'icon' => 'fa-gamepad'],
        'desktop' => ['label' => __('student.portfolio_type_desktop'), 'icon' => 'fa-desktop'],
        'cli' => ['label' => __('student.portfolio_type_cli'), 'icon' => 'fa-terminal'],
        'other' => ['label' => __('student.portfolio_type_other'), 'icon' => 'fa-folder'],
    ];
    $ideas = [
        __('student.portfolio_idea_web'),
        __('student.portfolio_idea_api'),
        __('student.portfolio_idea_lib'),
        __('student.portfolio_idea_game'),
        __('student.portfolio_idea_script'),
        __('student.portfolio_idea_ui'),
        __('student.portfolio_idea_mobile'),
        __('student.portfolio_idea_cli'),
        __('student.portfolio_idea_fullstack'),
    ];
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .pf-form label {
        display: block; margin-bottom: 6px;
        font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .pf-form .req { color: #b91c1c; }
    .pf-form .hint { font-weight: 500; color: var(--ml-muted); font-size: 11px; }
    .pf-form input[type="text"],
    .pf-form input[type="url"],
    .pf-form textarea,
    .pf-form select {
        width: 100%; padding: 11px 14px; border-radius: 12px;
        border: 1px solid var(--ml-line); background: var(--ml-surface);
        color: var(--ml-ink); font-family: inherit; font-size: 13px;
    }
    .pf-form textarea { min-height: 100px; resize: vertical; line-height: 1.6; }
    .pf-form input:focus,
    .pf-form textarea:focus,
    .pf-form select:focus {
        outline: none; border-color: rgba(73, 164, 162, 0.55);
        box-shadow: 0 0 0 3px rgba(73, 164, 162, 0.15);
    }
    .pf-form .field { margin-bottom: 14px; }
    .pf-grid-2 {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;
    }
    .pf-grid-4 {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px;
    }
    @media (max-width: 900px) {
        .pf-grid-4 { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 560px) {
        .pf-grid-2, .pf-grid-4 { grid-template-columns: 1fr; }
    }
    .pf-types {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 8px;
    }
    .pf-type {
        position: relative; display: block; cursor: pointer; user-select: none;
    }
    .pf-type input {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2; margin: 0;
    }
    .pf-type .box {
        display: flex; align-items: center; gap: 8px; min-height: 48px;
        padding: 8px 10px; border-radius: 12px; border: 1px solid var(--ml-line);
        background: var(--ml-well); transition: border-color var(--ml-fast) ease, background var(--ml-fast) ease;
    }
    .pf-type .box i {
        width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); font-size: 12px;
    }
    .pf-type .box span { font-size: 12px; font-weight: 700; color: var(--ml-ink); }
    .pf-type input:checked + .box {
        border-color: rgba(73, 164, 162, 0.55);
        background: rgba(73, 164, 162, 0.1);
        box-shadow: 0 0 0 2px rgba(73, 164, 162, 0.15);
    }
    .pf-ideas {
        display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;
    }
    .pf-ideas span {
        display: inline-flex; align-items: center; min-height: 28px; padding: 0 10px;
        border-radius: 8px; font-size: 11px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted); border: 1px solid var(--ml-line);
    }
    .pf-drop {
        border: 1px dashed rgba(73, 164, 162, 0.4); border-radius: 12px;
        padding: 14px; background: rgba(73, 164, 162, 0.04);
    }
    .pf-drop input[type="file"] { width: 100%; font-size: 12px; color: var(--ml-muted); }
    .pf-actions {
        display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end;
        margin-top: 8px; padding-top: 16px; border-top: 1px solid var(--ml-line);
    }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.portfolio_create_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('student.portfolio.index') }}">{{ __('student.my_projects_title') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.portfolio_create_title') }}</span>
            </nav>
            <h1>{{ __('student.portfolio_create_title') }}</h1>
            <p class="sub">{{ __('student.portfolio_create_subtitle') }}</p>
        </div>
    </header>

    @if($errors->any())
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px">
            <ul style="margin:0;padding-inline-start:18px">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px;font-weight:600">
            {{ session('error') }}
        </div>
    @endif

    <section class="oc-panel" style="margin-bottom:16px">
        <p class="oc-label"><i class="fas fa-lightbulb" style="color:var(--ml-teal-deep);margin-inline-end:6px"></i>{{ __('student.portfolio_ideas_title') }}</p>
        <p style="margin:0;font-size:12px;color:var(--ml-muted);line-height:1.55">{{ __('student.portfolio_ideas_hint') }}</p>
        <div class="pf-ideas">
            @foreach($ideas as $idea)
                <span>{{ $idea }}</span>
            @endforeach
        </div>
    </section>

    <section class="oc-panel">
        <p class="oc-label">{{ __('student.portfolio_form_heading') }}</p>
        <form action="{{ route('student.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="pf-form">
            @csrf

            <div class="field">
                <label for="pf-title">{{ __('student.portfolio_title_label') }} <span class="req">*</span></label>
                <input id="pf-title" type="text" name="title" value="{{ old('title') }}" required
                       placeholder="{{ __('student.portfolio_title_placeholder') }}">
            </div>

            <div class="field">
                <label>{{ __('student.portfolio_type_label') }}</label>
                <div class="pf-types">
                    @foreach($types as $value => $meta)
                        <label class="pf-type">
                            <input type="radio" name="project_type" value="{{ $value }}" {{ old('project_type') == $value ? 'checked' : '' }}>
                            <span class="box">
                                <i class="fas {{ $meta['icon'] }}"></i>
                                <span>{{ $meta['label'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="field">
                <label for="pf-desc">{{ __('student.portfolio_desc_label') }}</label>
                <textarea id="pf-desc" name="description" rows="3"
                          placeholder="{{ __('student.portfolio_desc_placeholder') }}">{{ old('description') }}</textarea>
            </div>

            <div class="pf-grid-4" style="margin-bottom:14px">
                <div class="field" style="margin:0">
                    <label for="pf-github"><i class="fab fa-github" style="margin-inline-end:4px"></i>{{ __('student.portfolio_github_label') }}</label>
                    <input id="pf-github" type="url" name="github_url" value="{{ old('github_url') }}" placeholder="https://github.com/...">
                </div>
                <div class="field" style="margin:0">
                    <label for="pf-live">{{ __('student.portfolio_live_url_label') }}</label>
                    <input id="pf-live" type="url" name="project_url" value="{{ old('project_url') }}" placeholder="https://demo.example.com">
                </div>
                <div class="field" style="margin:0">
                    <label for="pf-path">
                        {{ __('student.portfolio_path_label') }}
                        <span class="hint">{{ __('student.portfolio_path_hint') }}</span>
                    </label>
                    <select id="pf-path" name="academic_year_id">
                        <option value="">{{ __('student.portfolio_path_placeholder') }}</option>
                        @forelse($learningPaths as $path)
                            <option value="{{ $path->id }}" {{ old('academic_year_id') == $path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                        @empty
                            <option value="" disabled>{{ __('student.portfolio_path_empty') }}</option>
                        @endforelse
                    </select>
                </div>
                <div class="field" style="margin:0">
                    <label for="pf-course">
                        {{ __('student.portfolio_course_label') }}
                        <span class="hint">{{ __('student.portfolio_path_hint') }}</span>
                    </label>
                    <select id="pf-course" name="advanced_course_id">
                        <option value="">{{ __('student.portfolio_course_placeholder') }}</option>
                        @forelse($courses as $course)
                            <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @empty
                            <option value="" disabled>{{ __('student.portfolio_course_empty') }}</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="portfolio-images">
                    {{ __('student.portfolio_images_label') }}
                    <span class="hint">{{ __('student.portfolio_images_hint') }}</span>
                </label>
                <div class="pf-drop">
                    <input type="file" name="images[]" accept="image/*" multiple id="portfolio-images" data-max="5">
                    <p class="hint" style="margin:8px 0 0" id="images-hint">{{ __('student.portfolio_images_select') }}</p>
                </div>
            </div>

            <div class="pf-actions">
                <a href="{{ route('student.portfolio.index') }}" class="oc-btn oc-btn-quiet">{{ __('student.portfolio_cancel') }}</a>
                <button type="submit" class="oc-btn">
                    <i class="fas fa-upload text-xs"></i> {{ __('student.portfolio_submit') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('portfolio-images');
    var hint = document.getElementById('images-hint');
    if (!input || !hint) return;

    var msgSelect = @json(__('student.portfolio_images_select'));
    var msgSelected = @json(__('student.portfolio_images_selected'));
    var msgTrimmed = @json(__('student.portfolio_images_trimmed'));

    input.addEventListener('change', function () {
        var files = this.files;
        if (files.length > 5) {
            hint.textContent = msgTrimmed.replace(':count', files.length);
            var dt = new DataTransfer();
            for (var i = 0; i < 5; i++) dt.items.add(files[i]);
            this.files = dt.files;
        } else if (files.length > 0) {
            hint.textContent = msgSelected.replace(':count', files.length);
        } else {
            hint.textContent = msgSelect;
        }
    });
});
</script>
@endpush
