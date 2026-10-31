@php
    $badge = $badge ?? null;
    $subtitle = $subtitle ?? null;
    $backUrl = $backUrl ?? null;
    $backLabel = $backLabel ?? 'جميع الوظائف';
    $metaChips = $metaChips ?? [];
@endphp

<section class="hero-section relative overflow-hidden min-h-[48vh] flex items-center pt-24 pb-14 lg:pt-32 lg:pb-20">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 max-w-5xl">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-blue-700 hover:text-blue-900 text-sm font-bold mb-6 transition-colors">
                <i class="fas fa-arrow-right"></i>
                {{ $backLabel }}
            </a>
        @endif

        @if($badge)
            <div class="text-center mb-5 fade-in-up">
                <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold">
                    <i class="fas fa-briefcase text-blue-600"></i>
                    {{ $badge }}
                </span>
            </div>
        @endif

        <div class="text-center fade-in-up" style="animation-delay: 0.05s;">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-blue-900 leading-tight mb-4">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-lg md:text-xl text-blue-700/90 max-w-3xl mx-auto leading-relaxed font-medium">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        @if(!empty($metaChips))
            <div class="mt-8 flex flex-wrap justify-center gap-2 fade-in-up" style="animation-delay: 0.1s;">
                @foreach($metaChips as $chip)
                    <span class="job-meta-chip {{ $chip['tone'] ?? 'blue' }}">
                        @if(!empty($chip['icon']))
                            <i class="{{ $chip['icon'] }}"></i>
                        @endif
                        {{ $chip['label'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</section>
