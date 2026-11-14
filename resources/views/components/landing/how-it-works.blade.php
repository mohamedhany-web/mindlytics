@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
    $steps = [
        ['n' => '01', 'title' => __('landing.educo.how.s1_title'), 'desc' => __('landing.educo.how.s1_desc')],
        ['n' => '02', 'title' => __('landing.educo.how.s2_title'), 'desc' => __('landing.educo.how.s2_desc')],
        ['n' => '03', 'title' => __('landing.educo.how.s3_title'), 'desc' => __('landing.educo.how.s3_desc')],
    ];
@endphp

<section class="lp-section lp-how content-wrapper" data-node-id="25:1473">
    <div class="lp-container">
        <div class="lp-how__copy lp-reveal" data-node-id="25:1474">
            <div class="lp-how__heading">
                <p class="lp-eyebrow">{{ __('landing.educo.how.eyebrow') }}</p>
                <h2 class="lp-h1">{{ __('landing.educo.how.title') }}</h2>
            </div>
            <p class="lp-lead">{{ __('landing.educo.how.desc') }}</p>
        </div>

        <div class="lp-how__steps" data-node-id="25:1503">
            @foreach ($steps as $step)
                <div class="lp-how__step lp-reveal" style="--lp-delay:{{ $loop->index * 100 }}ms">
                    <div class="lp-how__numline">
                        <div class="lp-how__num">{{ $step['n'] }}</div>
                        <div class="lp-how__line" aria-hidden="true">
                            <img src="{{ $a['how_line'] }}" alt="" width="291" height="2">
                        </div>
                    </div>
                    <div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
