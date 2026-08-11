@extends('layouts.public')

@section('title', __('public.journey_talent_title'))

@section('content')
<section class="py-8 md:py-12 bg-slate-50 w-full" style="padding-top: 6rem;">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <p class="text-sm font-semibold text-blue-700 mb-2">Mindlytics Journey · Hiring</p>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2">{{ __('public.journey_talent_heading') }}</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ __('public.journey_talent_subtitle') }}</p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2 mb-6">
            <a href="{{ route('public.portfolio.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-white text-gray-700 border border-gray-200 hover:border-blue-300">{{ __('public.journey_tab_projects') }}</a>
            <a href="{{ route('public.portfolio.talent') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-blue-600 text-white">{{ __('public.journey_tab_talent') }}</a>
        </div>

        <form method="GET" class="mb-6 bg-white border border-gray-200 rounded-2xl p-4 space-y-4">
            <div class="flex flex-col lg:flex-row gap-3">
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('public.journey_talent_search') }}"
                       class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm">
                <select name="type" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                    <option value="">{{ __('public.journey_program_filter') }} — {{ __('public.all') }}</option>
                    <option value="recorded" {{ ($programType ?? '') === 'recorded' ? 'selected' : '' }}>{{ __('public.journey_type_recorded') }}</option>
                    <option value="diploma" {{ ($programType ?? '') === 'diploma' ? 'selected' : '' }}>{{ __('public.journey_type_diploma') }}</option>
                </select>
                <select name="sort" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                    <option value="recent" {{ ($sort ?? 'recent') === 'recent' ? 'selected' : '' }}>الأحدث</option>
                    <option value="projects" {{ ($sort ?? '') === 'projects' ? 'selected' : '' }}>الأكثر مشاريعًا</option>
                    <option value="completion" {{ ($sort ?? '') === 'completion' ? 'selected' : '' }}>اكتمال الملف</option>
                </select>
                <label class="inline-flex items-center gap-2 bg-slate-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 whitespace-nowrap">
                    <input type="checkbox" name="open_to_work" value="1" {{ !empty($openToWork) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                    {{ __('public.journey_open_to_work') }}
                </label>
                <button class="rounded-xl bg-blue-600 text-white px-5 py-2.5 text-sm font-bold">{{ __('public.journey_search') }}</button>
            </div>

            @if(isset($skillSuggestions) && $skillSuggestions->count())
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500">مهارات شائعة:</span>
                    @foreach($skillSuggestions as $hint)
                        <a href="{{ route('public.portfolio.talent', array_filter(['skill' => $hint, 'q' => $q ?: null, 'type' => $programType ?: null, 'sort' => ($sort ?? 'recent') !== 'recent' ? $sort : null, 'open_to_work' => !empty($openToWork) ? 1 : null])) }}"
                           class="text-xs px-2.5 py-1 rounded-lg border {{ ($skill ?? '') === $hint ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-gray-700 border-gray-200 hover:border-blue-300' }}">
                            {{ $hint }}
                        </a>
                    @endforeach
                    @if(!empty($skill))
                        <a href="{{ route('public.portfolio.talent', array_filter(['q' => $q ?: null, 'type' => $programType ?: null, 'sort' => ($sort ?? 'recent') !== 'recent' ? $sort : null, 'open_to_work' => !empty($openToWork) ? 1 : null])) }}" class="text-xs text-rose-600 font-semibold">مسح المهارة</a>
                    @endif
                </div>
            @endif
            <input type="hidden" name="skill" value="{{ $skill ?? '' }}">
        </form>

        @if($profiles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($profiles as $profile)
                    <a href="{{ route('public.journey.show', $profile->slug) }}" class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 transition-colors">
                        <div class="flex items-start gap-3 mb-4">
                            @if($profile->user->profile_image)
                                <img src="{{ $profile->user->profile_image_url }}" alt="" class="w-14 h-14 rounded-full object-cover border border-gray-100">
                            @else
                                <span class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-black">{{ mb_substr($profile->resolvedDisplayName(), 0, 1) }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h2 class="font-bold text-gray-900 truncate">{{ $profile->resolvedDisplayName() }}</h2>
                                <p class="text-sm text-gray-500 line-clamp-2">{{ $profile->resolvedHeadline() ?: __('public.journey_default_headline') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($profile->is_open_to_work)
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ __('public.journey_open_to_work') }}</span>
                            @endif
                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">{{ $profile->published_projects_count }} {{ __('public.journey_verified_projects') }}</span>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">{{ $profile->profile_completion }}% profile</span>
                        </div>
                        @if(isset($profile->top_technologies) && $profile->top_technologies->count())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($profile->top_technologies as $tech)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-50 text-gray-600 border border-gray-100">{{ $tech }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($profile->career_goal)
                            <p class="text-xs text-gray-500 line-clamp-2">{{ $profile->career_goal }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $profiles->links() }}</div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('public.journey_no_talent') }}</h3>
                <p class="text-gray-600">{{ __('public.journey_no_talent_desc') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
