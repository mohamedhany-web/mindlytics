@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();

    $formatStat = function ($n) {
        $n = (int) $n;
        if ($n >= 1000) {
            $k = round($n / 1000);
            return $k.'K +';
        }
        return $n.' +';
    };

    $items = [
        ['value' => $formatStat($statsCertificates ?? 0), 'label' => __('landing.educo.stats.stories')],
        ['value' => $formatStat($statsLearningPaths ?? 0), 'label' => __('landing.educo.stats.instructors')],
        ['value' => $formatStat($statsCourses ?? 0), 'label' => __('landing.educo.stats.courses')],
        ['value' => $formatStat($statsLearners ?? 0), 'label' => __('landing.educo.stats.members')],
    ];
@endphp

<section class="lp-section lp-stats content-wrapper lp-reveal" data-node-id="9:500" aria-label="{{ __('landing.educo.stats.stories') }}">
    <div class="lp-container py-12 md:py-16">
        <div class="lp-stats__row" data-node-id="9:517">
            @foreach ($items as $i => $item)
                @if ($i > 0)
                    <div class="lp-stats__div" aria-hidden="true">
                        <img src="{{ $a['stats_divider'] }}" alt="" width="90" height="8">
                    </div>
                @endif
                <div class="lp-stats__item">
                    <p class="lp-stats__num">{{ $item['value'] }}</p>
                    <p class="lp-stats__label">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
