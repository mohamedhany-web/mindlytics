@extends('layouts.student-dashboard')

@section('title', $achievement->achievement->name ?? __('student.achievement_details'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .ach-hero {
        display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
    }
    .ach-hero .ico {
        width: 72px; height: 72px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 210, 63, 0.22); color: var(--ml-yellow-ink);
        font-size: 2rem; flex-shrink: 0;
    }
    .ach-hero h2 { margin: 0 0 4px; font-size: 1.25rem; font-weight: 700; line-height: 1.3; }
    .ach-hero .meta { margin: 0; font-size: 13px; color: var(--ml-muted); }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.achievements_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('student.achievements.index') }}">{{ __('student.achievements_title') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.achievement_details') }}</span>
            </nav>
            <h1>{{ __('student.achievement_details') }}</h1>
            <p class="sub">{{ $achievement->achievement->name ?? __('student.achievement_default') }}</p>
        </div>
    </header>

    <section class="oc-panel">
        <div class="ach-hero">
            <div class="ico" aria-hidden="true">
                @if($achievement->achievement && $achievement->achievement->icon)
                    <i class="{{ $achievement->achievement->icon }}"></i>
                @else
                    <i class="fas fa-trophy"></i>
                @endif
            </div>
            <div class="min-w-0">
                <h2>{{ $achievement->achievement->name ?? __('student.achievement_default') }}</h2>
                <p class="meta">{{ $achievement->achievement->category ?? $achievement->achievement->type ?? '—' }}</p>
            </div>
        </div>

        @if($achievement->achievement && $achievement->achievement->description)
            <p class="oc-label" style="margin-top:18px">{{ __('student.assignment_description') }}</p>
            <p style="margin:0;font-size:14px;line-height:1.7;color:var(--ml-ink)">{{ $achievement->achievement->description }}</p>
        @endif

        <ul class="oc-facts" style="margin-top:16px">
            <li>
                <span class="k">{{ __('student.earned_at_label') }}</span>
                <span class="v">{{ $achievement->earned_at ? $achievement->earned_at->format('Y-m-d') : '—' }}</span>
            </li>
            @if($achievement->points_earned)
                <li>
                    <span class="k">{{ __('student.points_earned_label') }}</span>
                    <span class="v">{{ $achievement->points_earned }} {{ __('student.points_earned') }}</span>
                </li>
            @endif
        </ul>

        <div class="oc-nav" style="margin-top:16px">
            <a href="{{ route('student.achievements.index') }}" class="oc-btn oc-btn-quiet">
                {{ __('student.achievement_back') }}
            </a>
        </div>
    </section>
</div>
@endsection
