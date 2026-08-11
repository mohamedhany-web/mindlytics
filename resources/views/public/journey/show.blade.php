@extends('layouts.public')

@section('title', $profile->resolvedDisplayName() . ' — Mindlytics Journey')
@section('meta_description', \Illuminate\Support\Str::limit($profile->resolvedHeadline() ?: ($profile->resolvedBio() ?: 'Mindlytics Journey profile'), 160))
@section('og_title', $profile->resolvedDisplayName() . ' — Mindlytics Journey')
@section('og_description', \Illuminate\Support\Str::limit($profile->resolvedHeadline() ?: 'Verified learning journey at Mindlytics Academy', 160))
@section('og_image', $ogImage)
@section('og_url', route('public.journey.show', $profile->slug))

@section('content')
<section class="py-8 md:py-12 bg-slate-50" style="padding-top: 6rem;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($isOwner && !$profile->isPubliclyVisible())
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('public.journey_owner_preview') }}
                <a href="{{ route('student.portfolio.journey') }}" class="font-bold underline mr-1">{{ __('public.journey_manage_profile') }}</a>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                @if($profile->user->profile_image)
                    <img src="{{ $profile->user->profile_image_url }}" alt="" class="w-24 h-24 rounded-full object-cover border border-gray-100">
                @else
                    <span class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-3xl font-black">{{ mb_substr($profile->resolvedDisplayName(), 0, 1) }}</span>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900">{{ $profile->resolvedDisplayName() }}</h1>
                        @if($profile->is_open_to_work)
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ __('public.journey_open_to_work') }}</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-3">{{ $profile->resolvedHeadline() ?: __('public.journey_default_headline') }}</p>
                    @if($profile->career_goal)
                        <p class="text-sm text-gray-500 mb-4"><span class="font-semibold text-gray-700">{{ __('public.journey_career_goal') }}:</span> {{ $profile->career_goal }}</p>
                    @endif
                    @if($profile->resolvedBio())
                        <p class="text-sm text-gray-600 leading-relaxed mb-4 whitespace-pre-line">{{ $profile->resolvedBio() }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2">
                        @if($profile->github_url)
                            <a href="{{ $profile->github_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:border-blue-300"><i class="fab fa-github"></i> GitHub</a>
                        @endif
                        @if($profile->linkedin_url)
                            <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:border-blue-300"><i class="fab fa-linkedin"></i> LinkedIn</a>
                        @endif
                        @if($profile->website_url)
                            <a href="{{ $profile->website_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:border-blue-300"><i class="fas fa-link"></i> Website</a>
                        @endif
                    </div>
                </div>
                <div class="md:w-56 grid grid-cols-2 md:grid-cols-1 gap-2">
                    <div class="rounded-xl bg-slate-50 border border-gray-100 px-3 py-2 text-center md:text-right">
                        <div class="text-xl font-black text-gray-900">{{ $stats['published'] }}</div>
                        <div class="text-[11px] text-gray-500">{{ __('public.journey_verified_projects') }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-gray-100 px-3 py-2 text-center md:text-right">
                        <div class="text-xl font-black text-gray-900">{{ $stats['featured'] ?? 0 }}</div>
                        <div class="text-[11px] text-gray-500">{{ __('public.journey_featured') }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-gray-100 px-3 py-2 text-center md:text-right">
                        <div class="text-xl font-black text-gray-900">{{ $stats['diploma'] }}</div>
                        <div class="text-[11px] text-gray-500">{{ __('public.journey_stat_diploma') }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-gray-100 px-3 py-2 text-center md:text-right">
                        <div class="text-xl font-black text-gray-900">{{ $stats['capstone'] }}</div>
                        <div class="text-[11px] text-gray-500">Capstone</div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                @include('components.journey-share-bar', [
                    'canonicalUrl' => route('public.journey.show', $profile->slug),
                    'shareTitle' => $profile->resolvedDisplayName() . ' — Mindlytics Journey',
                    'shareableType' => 'profile',
                    'shareableId' => $profile->id,
                    'cardImageUrl' => $ogImage,
                    'cardType' => 'profile',
                ])
            </div>
        </div>

        @if(isset($achievements) && $achievements->count())
            <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('public.journey_achievements') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($achievements as $ua)
                        @continue(!$ua->achievement)
                        <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-slate-50 px-3 py-3">
                            <span class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $ua->achievement->icon ?: 'fa-medal' }} text-sm"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-gray-900">{{ $ua->achievement->name }}</div>
                                <div class="text-xs text-gray-500 line-clamp-2">{{ $ua->achievement->description }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($technologies->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('public.journey_skills_demonstrated') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($technologies as $tech => $count)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-50 text-gray-700 border border-gray-100">
                            {{ $tech }}
                            <span class="text-[10px] text-gray-400">{{ $count }}</span>
                        </span>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ __('public.journey_skills_evidence_note') }}</p>
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">{{ __('public.journey_timeline_projects') }}</h2>
            <a href="{{ route('public.portfolio.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">{{ __('public.back_to_gallery') }}</a>
        </div>

        @if($projects->count() > 0)
            <div class="space-y-4">
                @foreach($projects as $project)
                    <a href="{{ route('public.portfolio.show', $project->id) }}" class="flex flex-col sm:flex-row gap-4 bg-white border border-gray-200 rounded-2xl p-4 hover:border-blue-300 transition-colors">
                        @if($project->preview_image_path)
                            <img src="{{ asset($project->preview_image_path) }}" alt="" class="w-full sm:w-40 h-28 rounded-xl object-cover flex-shrink-0">
                        @else
                            <div class="w-full sm:w-40 h-28 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-code text-slate-400 text-2xl"></i></div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ __('public.journey_verified') }}</span>
                                @if($project->is_featured)
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">{{ __('public.journey_featured') }}</span>
                                @endif
                                @if($project->program_type)
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">{{ $project->programTypeLabel() }}</span>
                                @endif
                                @if($project->is_capstone)
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Capstone</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1">{{ $project->title }}</h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ Str::limit(strip_tags($project->description ?? ''), 140) }}</p>
                            <div class="text-xs text-gray-400">
                                {{ $project->programContextLabel() }}
                                @if($project->published_at) · {{ $project->published_at->translatedFormat('M Y') }} @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $projects->links() }}</div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center text-gray-500">
                {{ __('public.journey_no_projects_on_profile') }}
            </div>
        @endif
    </div>
</section>
@endsection
