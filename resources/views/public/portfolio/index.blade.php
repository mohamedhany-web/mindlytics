@extends('layouts.public')

@section('title', __('public.portfolio_page_title'))

@section('content')
<section class="py-8 md:py-12 bg-slate-50 w-full" style="padding-top: 6rem;">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
        <div class="mb-8 md:mb-10 text-center max-w-4xl mx-auto">
            <p class="text-sm font-semibold tracking-wide text-blue-700 mb-2">Mindlytics Journey</p>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-3" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                {{ __('public.portfolio_heading') }}
            </h1>
            <p class="text-base md:text-lg text-gray-600">
                {{ __('public.portfolio_subtitle') }}
            </p>
        </div>

        {{-- Stats strip for hiring context --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8 max-w-5xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['projects']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('public.journey_stat_projects') }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['recorded']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('public.journey_stat_recorded') }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['diploma']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('public.journey_stat_diploma') }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['talent']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('public.journey_stat_talent') }}</div>
            </div>
        </div>

        {{-- Primary tabs: Projects | Talent --}}
        <div class="flex flex-wrap items-center justify-center gap-2 mb-6">
            <a href="{{ route('public.portfolio.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-blue-600 text-white">{{ __('public.journey_tab_projects') }}</a>
            <a href="{{ route('public.portfolio.talent') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-white text-gray-700 border border-gray-200 hover:border-blue-300">{{ __('public.journey_tab_talent') }}</a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
            <aside class="lg:w-72 xl:w-64 flex-shrink-0 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sticky top-24">
                    <h2 class="text-sm font-bold text-gray-900 mb-3">{{ __('public.journey_program_filter') }}</h2>
                    <ul class="space-y-1 mb-5">
                        <li>
                            <a href="{{ route('public.portfolio.index', array_filter(['path' => $categoryId, 'q' => $q ?: null, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                               class="block px-3 py-2.5 rounded-xl text-sm font-medium {{ !$programType ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ __('public.all') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.portfolio.index', array_filter(['type' => 'recorded', 'path' => $categoryId, 'q' => $q ?: null, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                               class="block px-3 py-2.5 rounded-xl text-sm font-medium {{ $programType === 'recorded' ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ __('public.journey_type_recorded') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.portfolio.index', array_filter(['type' => 'diploma', 'path' => $categoryId, 'q' => $q ?: null, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                               class="block px-3 py-2.5 rounded-xl text-sm font-medium {{ $programType === 'diploma' ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ __('public.journey_type_diploma') }}
                            </a>
                        </li>
                    </ul>

                    <h2 class="text-sm font-bold text-gray-900 mb-3">{{ __('public.learning_paths_sidebar') }}</h2>
                    <ul class="space-y-1 max-h-72 overflow-y-auto">
                        <li>
                            <a href="{{ route('public.portfolio.index', array_filter(['type' => $programType, 'q' => $q ?: null, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                               class="block px-3 py-2.5 rounded-xl text-sm font-medium {{ !$categoryId ? 'bg-slate-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ __('public.all') }}
                            </a>
                        </li>
                        @foreach($learningPaths as $path)
                            <li>
                                <a href="{{ route('public.portfolio.index', array_filter(['type' => $programType, 'path' => $path->id, 'q' => $q ?: null, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                                   class="block px-3 py-2.5 rounded-xl text-sm font-medium {{ (string)$categoryId === (string)$path->id ? 'bg-slate-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $path->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <div class="flex-1 min-w-0 w-full">
                <form method="GET" action="{{ route('public.portfolio.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3">
                    @if($programType)<input type="hidden" name="type" value="{{ $programType }}">@endif
                    @if($categoryId)<input type="hidden" name="path" value="{{ $categoryId }}">@endif
                    <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('public.journey_search_placeholder') }}"
                           class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <select name="sort" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>{{ __('public.journey_sort_latest') }}</option>
                        <option value="featured" {{ $sort === 'featured' ? 'selected' : '' }}>{{ __('public.journey_sort_featured') }}</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-blue-600 text-white px-5 py-2.5 text-sm font-bold hover:bg-blue-700">{{ __('public.journey_search') }}</button>
                </form>

                @if($projects->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5 lg:gap-6">
                        @foreach($projects as $project)
                            @php
                                $journey = $project->user->journeyProfile ?? null;
                                $canOpenJourney = $journey && $journey->isPubliclyVisible();
                            @endphp
                            <article class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-blue-300 transition-colors">
                                <a href="{{ route('public.portfolio.show', $project->id) }}" class="block">
                                    @if($project->preview_image_path)
                                        <div class="aspect-video bg-gray-100 overflow-hidden">
                                            <img src="{{ asset($project->preview_image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300" loading="lazy">
                                        </div>
                                    @else
                                        <div class="aspect-video bg-slate-100 flex items-center justify-center">
                                            <i class="fas fa-code text-3xl text-slate-400"></i>
                                        </div>
                                    @endif
                                </a>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                            <i class="fas fa-check-circle"></i> {{ __('public.journey_verified') }}
                                        </span>
                                        @if($project->is_featured)
                                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">
                                                <i class="fas fa-star"></i> {{ __('public.journey_featured') }}
                                            </span>
                                        @endif
                                        @if($project->program_type)
                                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">{{ $project->programTypeLabel() }}</span>
                                        @endif
                                        @if($project->is_capstone)
                                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Capstone</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('public.portfolio.show', $project->id) }}">
                                        <h3 class="text-base font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-blue-700">{{ $project->title }}</h3>
                                    </a>
                                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ Str::limit(strip_tags($project->description ?? ''), 90) }}</p>
                                    @if(!empty($project->technologies))
                                        <div class="flex flex-wrap gap-1 mb-3">
                                            @foreach(array_slice($project->technologies, 0, 3) as $tech)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-50 text-gray-600 border border-gray-100">{{ $tech }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if($project->user->profile_image)
                                                <img src="{{ $project->user->profile_image_url }}" alt="" class="w-7 h-7 rounded-full object-cover">
                                            @else
                                                <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">{{ mb_substr($project->user->name ?? 'ط', 0, 1) }}</span>
                                            @endif
                                            @if($canOpenJourney)
                                                <a href="{{ route('public.journey.show', $journey->slug) }}" class="text-xs font-semibold text-gray-800 hover:text-blue-700 truncate">{{ $project->user->name }}</a>
                                            @else
                                                <span class="text-xs font-medium text-gray-700 truncate">{{ $project->user->name ?? __('public.student_fallback') }}</span>
                                            @endif
                                        </div>
                                        @if($project->programContextLabel())
                                            <span class="text-[10px] font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md truncate max-w-[40%]">{{ $project->programContextLabel() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $projects->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                            <i class="fas fa-route text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('public.no_projects_yet') }}</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">{{ __('public.no_projects_desc') }}</p>
                        <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700">
                            <i class="fas fa-book"></i>
                            {{ __('public.browse_courses') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
