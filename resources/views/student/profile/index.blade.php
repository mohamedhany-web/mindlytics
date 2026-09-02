@extends('layouts.student-dashboard')

@section('title', __('student.profile_title'))
@section('header', __('student.profile_title'))

@push('styles')
<style>
    .sp-profile-cover {
        height: 140px;
        border-radius: 30px 30px 0 0;
        background: linear-gradient(135deg, rgba(174,217,234,0.55) 0%, rgba(247,247,245,1) 55%, rgba(220,222,242,0.45) 100%);
        position: relative;
        overflow: hidden;
    }
    .sp-profile-cover::after {
        content: '';
        position: absolute;
        inset-inline-end: -20px;
        top: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(174,217,234,0.35);
    }
    .sp-profile-shell {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
        background: #fff;
    }
    .sp-profile-avatar {
        width: 112px;
        height: 112px;
        border-radius: 28px;
        border: 4px solid #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        overflow: hidden;
        background: var(--sp-accent);
        display: grid;
        place-items: center;
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--sp-accent-text);
        flex-shrink: 0;
    }
    .sp-profile-field {
        width: 100%;
        border-radius: 18px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fafaf8;
        padding: 0.8rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--sp-text);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .sp-profile-field:focus {
        outline: none;
        border-color: var(--sp-accent);
        box-shadow: 0 0 0 3px rgba(174,217,234,0.35);
        background: #fff;
    }
    .sp-profile-nav a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--sp-muted);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .sp-profile-nav a:hover { background: #f7f7f5; color: var(--sp-accent-text); }
    .sp-profile-rail { position: sticky; top: 12px; }
    @media (max-width: 1023px) { .sp-profile-rail { position: static; } }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Str;

    $roleLabels = [
        'student' => __('student.student_role'),
        'teacher' => __('student.teacher_role'),
        'admin' => __('student.admin_role_label'),
        'super_admin' => __('student.super_admin_role'),
    ];
    $roleLabel = $roleLabels[$user->role] ?? __('student.user_role');

    $memberSince = $user->created_at instanceof \Carbon\CarbonInterface
        ? $user->created_at->translatedFormat('d F Y')
        : '—';

    $lastLogin = $user->last_login_at instanceof \Carbon\CarbonInterface
        ? $user->last_login_at->diffForHumans()
        : null;
@endphp

<div class="space-y-5 max-w-6xl mx-auto">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif

    {{-- Identity shell — light studio layout (not dark hero) --}}
    <div class="sp-profile-shell">
        <div class="sp-profile-cover" aria-hidden="true"></div>

        <div class="px-5 sm:px-8 pb-6 -mt-14 relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-end gap-5 lg:gap-8">
                <div class="sp-profile-avatar">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="{{ __('student.profile_image_alt') }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($user->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0 pb-1">
                    <p class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0">{{ __('student.profile_studio_eyebrow') }}</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold m-0 mt-1 leading-tight">{{ $user->name }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.profile_studio_tagline') }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="sp-pill sp-pill--progress">{{ $roleLabel }}</span>
                        <span class="sp-pill {{ $user->is_active ? 'sp-pill--done' : '' }}">{{ $user->is_active ? __('student.profile_status_active') : __('student.profile_status_inactive') }}</span>
                        @if($user->phone)
                            <span class="text-xs font-bold text-[var(--sp-muted)]">{{ $user->phone }}</span>
                        @endif
                        @if($user->email)
                            <span class="text-xs font-bold text-[var(--sp-muted)] hidden sm:inline">· {{ $user->email }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('settings') }}" class="inline-flex items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                        {{ __('student.profile_settings_link') }}
                    </a>
                    <a href="{{ route('student.portfolio.journey') }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)] !py-2.5 !px-4">
                        {{ __('student.profile_journey_link') }}
                    </a>
                </div>
            </div>

            <nav class="sp-profile-nav flex flex-wrap gap-2 mt-6 pt-5 border-t border-black/5">
                <a href="#profile-personal"><x-student.figma-icon name="icon-profile.svg" box="size-4" />{{ __('student.profile_nav_personal') }}</a>
                <a href="#profile-security"><x-student.figma-icon name="icon-settings.svg" box="size-4" />{{ __('student.profile_nav_security') }}</a>
                <a href="#profile-activity"><x-student.figma-icon name="icon-notifications.svg" box="size-4" />{{ __('student.profile_nav_activity') }}</a>
            </nav>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[280px_minmax(0,1fr)]">
        {{-- Left rail --}}
        <aside class="space-y-4 sp-profile-rail">
            <section class="sp-card p-5 space-y-3">
                <h3 class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0">{{ __('student.profile_contact_section') }}</h3>
                @foreach([
                    ['label' => __('student.profile_member_id'), 'value' => '#' . str_pad($user->id, 5, '0', STR_PAD_LEFT)],
                    ['label' => __('student.join_date_label'), 'value' => $memberSince],
                    ['label' => __('student.profile_account_type'), 'value' => $roleLabel],
                ] as $row)
                    <div class="flex items-center justify-between gap-2 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <span class="text-xs font-bold text-[var(--sp-muted)]">{{ $row['label'] }}</span>
                        <span class="text-xs font-extrabold truncate">{{ $row['value'] }}</span>
                    </div>
                @endforeach
            </section>

            <section class="sp-card p-5 space-y-3">
                @foreach([
                    ['icon' => 'icon-courses.svg', 'label' => __('student.profile_stat_courses'), 'value' => $stats['courses'] ?? 0, 'bg' => 'var(--sp-sky)'],
                    ['icon' => 'icon-classes.svg', 'label' => __('student.profile_stat_offline'), 'value' => $stats['offline'] ?? 0, 'bg' => 'var(--sp-lilac)'],
                    ['icon' => 'icon-notifications.svg', 'label' => __('student.profile_stat_notifications'), 'value' => $stats['notifications'] ?? 0, 'bg' => 'var(--sp-peach)'],
                ] as $stat)
                    <div class="flex items-center gap-3">
                        <span class="sp-icon-bubble shrink-0 !w-9 !h-9" style="background:{{ $stat['bg'] }}">
                            <x-student.figma-icon :name="$stat['icon']" box="size-4" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0">{{ $stat['label'] }}</p>
                            <p class="text-lg font-black m-0 text-[var(--sp-accent-text)]">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="sp-card p-5">
                <h3 class="font-extrabold text-sm m-0 mb-3">{{ __('student.profile_tips_title') }}</h3>
                <ul class="space-y-3 m-0 p-0 list-none">
                    @foreach([
                        ['title' => __('student.profile_tip_contact_title'), 'desc' => __('student.profile_tip_contact_desc')],
                        ['title' => __('student.profile_tip_password_title'), 'desc' => __('student.profile_tip_password_desc')],
                    ] as $tip)
                        <li class="text-xs">
                            <p class="font-extrabold m-0 text-[var(--sp-text)]">{{ $tip['title'] }}</p>
                            <p class="text-[var(--sp-muted)] m-0 mt-0.5 leading-relaxed font-bold">{{ $tip['desc'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        </aside>

        {{-- Main form --}}
        <div class="space-y-5 min-w-0">
            <section id="profile-personal" class="sp-card overflow-hidden scroll-mt-24">
                <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4 border-b border-black/5 bg-[#fafaf8]">
                    <div>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.profile_edit_section') }}</h3>
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-1 font-bold">{{ __('student.profile_edit_hint') }}</p>
                    </div>
                    <span class="sp-pill sp-pill--progress">{{ __('student.profile_encrypted_badge') }}</span>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.profile_field_name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="sp-profile-field">
                            @error('name')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.profile_field_phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="sp-profile-field">
                            @error('phone')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.profile_field_email') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="sp-profile-field" dir="ltr">
                            @error('email')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="rounded-[20px] border border-dashed border-[var(--sp-accent)] bg-[rgba(174,217,234,0.08)] p-4 sm:p-5">
                        <p class="font-extrabold text-sm m-0 mb-3">{{ __('student.profile_photo_section') }}</p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-24 h-24 rounded-[20px] overflow-hidden border-2 border-white shadow-md shrink-0 bg-[#f7f7f5] flex items-center justify-center">
                                @if($user->profile_image)
                                    <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <x-student.figma-icon name="icon-profile.svg" box="size-8" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-[18px] border border-black/5 bg-white px-4 py-3 text-sm font-extrabold hover:bg-[#f7f7f5] transition">
                                    <x-student.figma-icon name="icon-plus.svg" box="size-4" />
                                    {{ __('student.profile_photo_choose') }}
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                <p class="text-xs text-[var(--sp-muted)] m-0 mt-2 font-bold">{{ __('student.profile_photo_max') }}</p>
                                @error('profile_image')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div id="profile-security" class="scroll-mt-24 rounded-[20px] bg-[#fafaf8] border border-black/5 p-4 sm:p-5 space-y-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h4 class="font-extrabold text-sm m-0">{{ __('student.profile_password_section') }}</h4>
                                <p class="text-xs text-[var(--sp-muted)] m-0 mt-1 font-bold">{{ __('student.profile_password_hint') }}</p>
                            </div>
                            <span class="sp-pill !text-[10px]">{{ __('student.profile_password_tip') }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.profile_current_password') }}</label>
                                <input type="password" name="current_password" class="sp-profile-field">
                                @error('current_password')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.profile_new_password') }}</label>
                                <input type="password" name="password" class="sp-profile-field">
                                @error('password')<p class="text-xs font-bold text-[#7a3b2e] mt-1 m-0">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5">{{ __('student.profile_confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="sp-profile-field">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-black/5">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.profile_save_notice') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-5 py-3 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                                {{ __('student.profile_back_dashboard') }}
                            </a>
                            <button type="submit" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">{{ __('student.profile_save_changes') }}</button>
                        </div>
                    </div>
                </form>
            </section>

            <section id="profile-activity" class="sp-card p-5 sm:p-6 space-y-3 scroll-mt-24">
                <h3 class="font-extrabold text-base m-0 mb-1">{{ __('student.profile_activity_title') }}</h3>
                <div class="sp-process-row !shadow-none border border-[#f0f0ec]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky)">
                        <x-student.figma-icon name="icon-dashboard.svg" box="size-5" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-sm m-0">{{ __('student.profile_activity_login_title') }}</p>
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5 font-bold">{{ __('student.profile_activity_login_desc') }}</p>
                    </div>
                    <span class="text-xs font-bold text-[var(--sp-muted)] shrink-0">{{ $lastLogin ?: __('student.profile_just_now') }}</span>
                </div>
                <div class="sp-process-row !shadow-none border border-[#f0f0ec]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                        <x-student.figma-icon name="icon-settings.svg" box="size-5" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-sm m-0">{{ __('student.profile_activity_security_title') }}</p>
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5 font-bold">{{ __('student.profile_activity_security_desc') }}</p>
                    </div>
                    <a href="{{ route('settings') }}" class="sp-link text-xs font-extrabold shrink-0">{{ __('student.profile_activity_learn_more') }}</a>
                </div>
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 pt-1">{{ __('student.profile_security_tip') }}</p>
            </section>
        </div>
    </div>
</div>
@endsection
