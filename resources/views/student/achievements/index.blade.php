@extends('layouts.student-dashboard')

@section('title', __('student.achievements_title'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .ach-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: 12px;
    }
    .ach-card {
        display: flex; flex-direction: column; align-items: center; text-align: center;
        gap: 10px; padding: 20px 16px;
        background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r);
        text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    a.ach-card:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .ach-card .ico {
        width: 64px; height: 64px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 210, 63, 0.22); color: var(--ml-yellow-ink);
        font-size: 1.75rem;
    }
    .ach-card h3 {
        margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35;
    }
    .ach-card .desc {
        margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.55;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        max-width: 36ch;
    }
    .ach-pts {
        display: inline-flex; align-items: center; min-height: 28px; padding: 0 10px;
        border-radius: 8px; font-size: 12px; font-weight: 700;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.achievements_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.achievements_title') }}</span>
            </nav>
            <h1>{{ __('student.achievements_title') }}</h1>
            <p class="sub">{{ __('student.achievements_subtitle') }}</p>
        </div>
        @if(isset($stats))
            <div class="oc-signals">
                <span class="oc-signal oc-signal-hot">{{ __('student.total_points') }}: {{ $stats['total_points'] ?? 0 }}</span>
                @if(isset($achievements))
                    <span class="oc-signal oc-signal-live">{{ $achievements->total() }} {{ __('student.achievements_count_label') }}</span>
                @endif
            </div>
        @endif
    </header>

    @if(isset($stats))
        <div class="oc-pulse" aria-label="{{ __('student.achievements_title') }}">
            <div>
                <span class="lbl">{{ __('student.total_points') }}</span>
                <span class="val hot">{{ $stats['total_points'] ?? 0 }}</span>
            </div>
            @if(isset($achievements))
                <div>
                    <span class="lbl">{{ __('student.achievements_count_label') }}</span>
                    <span class="val teal">{{ $achievements->total() }}</span>
                </div>
            @endif
        </div>
    @endif

    @if(isset($achievements) && $achievements->count() > 0)
        <p class="oc-section-title">{{ __('student.achievements_count_label') }}</p>
        <div class="ach-grid">
            @foreach($achievements as $achievement)
                @php
                    $name = $achievement->achievement->name ?? __('student.achievement_default');
                    $desc = $achievement->achievement->description ?? '';
                    $icon = $achievement->achievement->icon ?? null;
                    $hasShow = Route::has('student.achievements.show');
                @endphp
                @if($hasShow)
                    <a href="{{ route('student.achievements.show', $achievement) }}" class="ach-card">
                @else
                    <div class="ach-card">
                @endif
                    <div class="ico" aria-hidden="true">
                        @if($icon)
                            <i class="{{ $icon }}"></i>
                        @else
                            <i class="fas fa-trophy"></i>
                        @endif
                    </div>
                    <h3>{{ $name }}</h3>
                    @if($desc)
                        <p class="desc">{{ $desc }}</p>
                    @endif
                    @if($achievement->points_earned)
                        <span class="ach-pts">+{{ $achievement->points_earned }} {{ __('student.points_earned') }}</span>
                    @endif
                @if($hasShow)
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
        @if($achievements->hasPages())
            <div style="margin-top:20px;display:flex;justify-content:center">
                {{ $achievements->links() }}
            </div>
        @endif
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-trophy"></i></div>
            <h3>{{ __('student.no_achievements') }}</h3>
            <p>{{ __('student.no_achievements_desc') }}</p>
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
