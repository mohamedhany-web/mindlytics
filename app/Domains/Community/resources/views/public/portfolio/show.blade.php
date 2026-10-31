@extends('layouts.public')

@section('title', $project->title . ' - Mindlytics Journey')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($project->description ?? ($project->title . ' — Mindlytics Verified project')), 160))
@section('og_title', $project->title . ' ✓ Mindlytics Verified')
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($project->description ?? 'Mentor-verified student project at Mindlytics Academy'), 160))
@section('og_image', $ogImage)
@section('og_url', route('public.portfolio.show', $project->id))

@section('content')
<section class="py-8 md:py-12 bg-slate-50" style="padding-top: 6rem;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.portfolio.index') }}" class="inline-flex items-center gap-2 text-blue-700 hover:text-gray-900 font-medium mb-8 transition-colors">
            <i class="fas fa-arrow-right"></i>
            {{ __('public.back_to_gallery') }}
        </a>

        <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            @if($project->preview_image_path)
                <div class="aspect-video bg-gray-100">
                    <img src="{{ asset($project->preview_image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover" loading="lazy">
                </div>
            @else
                <div class="aspect-video bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-code text-6xl text-slate-300"></i>
                </div>
            @endif
            <div class="p-8 md:p-10">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800">
                        <i class="fas fa-check-circle"></i> {{ __('public.journey_verified') }}
                    </span>
                    @if($project->is_featured)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800">
                            <i class="fas fa-star"></i> {{ __('public.journey_featured') }}
                        </span>
                    @endif
                    @if($project->program_type)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $project->programTypeLabel() }}
                        </span>
                    @endif
                    @if($project->is_capstone)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800">Capstone</span>
                    @endif
                    @if($project->academicYear)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-900">
                            <i class="fas fa-route"></i> {{ $project->academicYear->name }}
                        </span>
                    @endif
                    @if($project->advancedCourse)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-50 text-gray-700">
                            <i class="fas fa-play-circle"></i> {{ $project->advancedCourse->title }}
                        </span>
                    @endif
                    @if($project->offlineCourse)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-50 text-gray-700">
                            <i class="fas fa-chalkboard-teacher"></i>
                            {{ $project->offlineCourse->title }}
                            ({{ $project->offlineCourse->online_only ? __('public.journey_online') : __('public.journey_offline') }})
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">{{ $project->title }}</h1>

                @if(!empty($project->technologies))
                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach($project->technologies as $tech)
                            <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-50 border border-gray-100 text-gray-700 font-semibold">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif

                @if($project->description)
                    <div class="prose prose-lg text-gray-600 mb-6 max-w-none">
                        <h2 class="text-base font-bold text-gray-900 mb-2">{{ __('public.journey_overview') }}</h2>
                        {!! nl2br(e($project->description)) !!}
                    </div>
                @endif

                @if($project->what_i_learned)
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-gray-900 mb-2">{{ __('public.journey_what_i_learned') }}</h2>
                        <p class="text-gray-600 whitespace-pre-line">{{ $project->what_i_learned }}</p>
                    </div>
                @endif

                @if($project->challenges)
                    <div class="mb-6">
                        <h2 class="text-base font-bold text-gray-900 mb-2">{{ __('public.journey_challenges') }}</h2>
                        <p class="text-gray-600 whitespace-pre-line">{{ $project->challenges }}</p>
                    </div>
                @endif

                @if($project->instructor_notes)
                    <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                        <h2 class="text-sm font-bold text-emerald-900 mb-2">{{ __('public.journey_mentor_feedback') }}</h2>
                        <p class="text-sm text-emerald-900/90 whitespace-pre-line">{{ $project->instructor_notes }}</p>
                        @if($project->rubric_average)
                            <p class="text-xs text-emerald-800 mt-2 font-semibold">{{ __('public.journey_rubric_avg') }}: {{ $project->rubric_average }}/10</p>
                        @endif
                    </div>
                @endif

                <div class="flex flex-wrap gap-3 mb-8">
                    @if($project->project_url)
                        <a href="{{ $project->project_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700">
                            <i class="fas fa-external-link-alt"></i> {{ __('public.view_project') }}
                        </a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 border border-gray-200 text-gray-800 px-5 py-2.5 rounded-xl font-bold hover:border-blue-300">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                    @endif
                </div>

                <div class="mb-8">
                    @include('components.journey-share-bar', [
                        'canonicalUrl' => route('public.portfolio.show', $project->id),
                        'shareTitle' => $project->title . ' — Mindlytics Verified',
                        'shareableType' => 'project',
                        'shareableId' => $project->id,
                        'cardImageUrl' => $ogImage,
                        'cardType' => $project->is_featured ? 'featured' : 'project_verified',
                    ])
                </div>

                @php $journey = $project->user->journeyProfile; @endphp
                <div class="pt-8 border-t border-gray-200 flex items-center gap-4">
                    @if($project->user->profile_image)
                        <img src="{{ $project->user->profile_image_url }}" alt="" class="w-14 h-14 rounded-full object-cover border border-gray-100">
                    @else
                        <span class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-black">{{ mb_substr($project->user->name ?? 'ط', 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="font-bold text-gray-900">{{ $project->user->name ?? __('public.student_fallback') }}</p>
                        <p class="text-sm text-gray-500">{{ __('public.project_from_portfolio') }}</p>
                        @if($journey && $journey->isPubliclyVisible())
                            <a href="{{ route('public.journey.show', $journey->slug) }}" class="text-sm font-semibold text-blue-700 hover:underline">{{ __('public.journey_view_profile') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </article>

        @if($related->count() > 0)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('public.other_projects_same_path') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($related as $r)
                        <a href="{{ route('public.portfolio.show', $r->id) }}" class="flex gap-4 bg-white rounded-xl border border-gray-200 p-4 hover:border-blue-300 transition-colors">
                            @if($r->preview_image_path)
                                <img src="{{ asset($r->preview_image_path) }}" alt="{{ $r->title }}" class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-code text-2xl text-slate-400"></i>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-gray-900 truncate">{{ $r->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $r->user->name ?? __('public.student_fallback') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
