@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();

    $features = [
        ['title' => __('landing.educo.why.f1_title'), 'desc' => __('landing.educo.why.f1_desc')],
        ['title' => __('landing.educo.why.f2_title'), 'desc' => __('landing.educo.why.f2_desc')],
        ['title' => __('landing.educo.why.f3_title'), 'desc' => __('landing.educo.why.f3_desc')],
    ];
@endphp

<section class="lp-section lp-why content-wrapper" data-node-id="25:1068">
    <div class="lp-container">
        <div class="lp-why__grid">
            <div class="lp-why__visual lp-reveal">
                <div class="lp-why__photo" data-node-id="25:1074">
                    <img src="{{ $a['why_media'] }}" alt="{{ __('landing.educo.why.title') }}" width="463" height="510" loading="lazy">
                </div>
                <div class="lp-why__note lp-anim lp-anim--float-soft" data-node-id="25:1323">
                    <img src="{{ $a['why_star'] }}" alt="" width="60" height="60">
                    <p>{{ __('landing.educo.why.pace_note') }}</p>
                </div>
            </div>

            <div class="lp-reveal" style="--lp-delay:100ms">
                <div class="lp-why__copy" data-node-id="25:1069">
                    <div class="lp-who__heading">
                        <p class="lp-eyebrow">{{ __('landing.educo.why.eyebrow') }}</p>
                        <h2 class="lp-h1" style="max-width:580px">{{ __('landing.educo.why.title') }}</h2>
                    </div>
                    <p class="lp-lead" style="max-width:580px">{{ __('landing.educo.why.desc') }}</p>
                </div>

                <div class="lp-why__features" data-node-id="25:1322">
                    @foreach ($features as $i => $feature)
                        <div class="lp-reveal" style="--lp-delay:{{ ($i + 2) * 80 }}ms">
                            <div class="lp-why__feature-head">
                                <div class="lp-why__feature-icon">
                                    <img src="{{ $a['why_book'] }}" alt="" width="24" height="24">
                                </div>
                                <strong>{{ $feature['title'] }}</strong>
                            </div>
                            <p class="lp-why__feature-body">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
