@extends('layouts.app')

@section('title', __('instructor.profile') . ' - Mindlytics')
@section('header', __('instructor.profile'))

@section('content')
@php
    $user = auth()->user();
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $myCoursesCount = \App\Domains\Learning\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Domains\Learning\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="font-semibold text-emerald-800">{{ session('success') }}</span>
        </div>
    @endif

    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">{{ __('instructor.profile') }}</h1>
        <p class="text-sm text-slate-500">{{ __('instructor.manage_profile_data') }}</p>
    </div>

    <!-- بطاقة الملف + إحصائيات -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-sky-100 border border-slate-200 overflow-hidden shrink-0 mx-auto sm:mx-0">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ __('instructor.profile_image') }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                        <span class="text-4xl font-bold text-sky-600 hidden">{{ mb_substr($user->name, 0, 1) }}</span>
                    @else
                        <span class="text-4xl font-bold text-sky-600">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-right">
                    <span class="inline-flex items-center gap-2 rounded-lg bg-sky-100 text-sky-700 px-3 py-1.5 text-xs font-semibold mb-2">
                        <i class="fas fa-chalkboard-teacher"></i>
                        {{ __('instructor.instructor_role') }}
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-1">{{ $user->name }}</h2>
                    @if($user->phone)
                        <p class="text-sm text-slate-600 flex items-center justify-center sm:justify-end gap-2 mt-1">
                            <i class="fas fa-phone text-slate-400"></i>
                            {{ $user->phone }}
                        </p>
                    @endif
                    @if($user->email)
                        <p class="text-sm text-slate-600 flex items-center justify-center sm:justify-end gap-2 mt-0.5">
                            <i class="fas fa-envelope text-slate-400"></i>
                            {{ $user->email }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 mx-auto mb-2">
                        <i class="fas fa-calendar-week text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">{{ __('instructor.join_date') }}</p>
                    <p class="text-sm font-bold text-slate-800">{{ $memberSince }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 mx-auto mb-2">
                        <i class="fas fa-book-open text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">{{ __('instructor.my_courses') }}</p>
                    <p class="text-sm font-bold text-slate-800">{{ $myCoursesCount }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mx-auto mb-2">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">{{ __('instructor.students') }}</p>
                    <p class="text-sm font-bold text-slate-800">{{ $totalStudents }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mx-auto mb-2">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">{{ __('instructor.last_login') }}</p>
                    <p class="text-sm font-bold text-slate-800">{{ $lastLogin }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
        <!-- البطاقات الجانبية -->
        <div class="space-y-6">
            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">
                        <i class="fas fa-info-circle text-sm"></i>
                    </span>
                    {{ __('instructor.account_info') }}
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium">{{ __('instructor.membership_number') }}</span>
                        <span class="font-bold text-slate-800">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium">{{ __('instructor.account_type') }}</span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">{{ __('instructor.instructor_role') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium">{{ __('common.status') }}</span>
                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $user->is_active ? __('instructor.active_status') : __('instructor.not_active') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </span>
                    {{ __('instructor.tips_for_instructor') }}
                </h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-3 p-3 bg-sky-50/50 rounded-xl border border-slate-100">
                        <span class="text-sky-500 mt-0.5"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <p class="font-semibold text-slate-800">{{ __('instructor.update_bio') }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ __('instructor.add_bio_for_students') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-emerald-500 mt-0.5"><i class="fas fa-lock"></i></span>
                        <div>
                            <p class="font-semibold text-slate-800">{{ __('instructor.strong_password') }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ __('instructor.change_password_regularly') }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- نموذج التحديث -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-1">{{ __('instructor.update_data') }}</h3>
                <p class="text-sm text-slate-500 mb-6">{{ __('instructor.update_data_subtitle') }}</p>

                <form method="POST" action="{{ route('instructor.profile.update') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.full_name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            @error('name')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            @error('phone')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.email_optional') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            @error('email')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.bio_optional') }}</label>
                            <textarea name="bio" rows="4" placeholder="{{ __('instructor.bio_placeholder_short') }}"
                                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.profile_image') }}</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ __('instructor.profile_image') }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                    <i class="fas fa-user text-slate-400 text-2xl hidden"></i>
                                @else
                                    <i class="fas fa-user text-slate-400 text-2xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors">
                                    <i class="fas fa-upload text-sky-500"></i>
                                    <span>{{ __('instructor.choose_image_label') }}</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                @error('profile_image')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 space-y-4">
                        <h4 class="text-base font-bold text-slate-800">{{ __('instructor.change_password') }}</h4>
                        <p class="text-xs text-slate-500">{{ __('instructor.leave_empty_if_no_change') }}</p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('instructor.current_password') }}</label>
                                <input type="password" name="current_password"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                @error('current_password')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('instructor.new_password') }}</label>
                                <input type="password" name="password"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                @error('password')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('instructor.confirm_password') }}</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-slate-200">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                            {{ __('instructor.back_to_dashboard') }}
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white px-6 py-2.5 text-sm font-semibold transition-colors">
                            <i class="fas fa-save"></i>
                            {{ __('instructor.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
