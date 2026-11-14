@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
    $cards = [
        ['icon' => $a['who_book'], 'bg' => 'var(--lp-blue-hover)', 'title' => __('landing.educo.who.card1_title'), 'desc' => __('landing.educo.who.card1_desc')],
        ['icon' => $a['who_upload'], 'bg' => 'var(--lp-green)', 'title' => __('landing.educo.who.card2_title'), 'desc' => __('landing.educo.who.card2_desc')],
        ['icon' => $a['who_award'], 'bg' => 'var(--lp-orange)', 'title' => __('landing.educo.who.card3_title'), 'desc' => __('landing.educo.who.card3_desc')],
    ];
@endphp

<section class="lp-section lp-who content-wrapper" data-node-id="9:521" id="about">
    <div class="lp-container">
        <div class="lp-who__top">
            <div class="lp-who__copy lp-reveal" data-node-id="9:522">
                <div class="lp-who__heading">
                    <p class="lp-eyebrow">{{ __('landing.educo.who.eyebrow') }}</p>
                    <h2 class="lp-h1" style="max-width:483px">{{ __('landing.educo.who.title') }}</h2>
                </div>
                <p class="lp-lead" style="max-width:483px">{{ __('landing.educo.who.desc') }}</p>
                <p class="lp-lead" style="max-width:483px">{{ __('landing.educo.who.mission') }}</p>
                <ul class="lp-who__bullets">
                    <li>{{ __('landing.educo.who.point1') }}</li>
                    <li>{{ __('landing.educo.who.point2') }}</li>
                    <li>{{ __('landing.educo.who.point3') }}</li>
                </ul>
                <a href="{{ route('public.about') }}" class="lp-link-underline inline-block mt-2">{{ __('landing.educo.who.about_cta') }}</a>
            </div>

            <div class="lp-who__aside lp-reveal" style="--lp-delay:120ms">
                <div class="lp-who__photo">
                    <img src="{{ $a['who_about'] }}" alt="{{ __('landing.educo.who.title') }}" width="560" height="320" loading="lazy">
                </div>
                <div class="lp-who__video" data-node-id="9:582">
                    <div class="lp-who__video-head">
                        <span>{{ __('landing.educo.who.video_title') }}</span>
                        <span>{{ __('landing.educo.who.video_count') }}</span>
                    </div>
                    <div class="lp-who__row lp-who__row--active" data-node-id="9:593">
                        <div class="lp-who__row-left">
                            <img src="{{ $a['who_play'] }}" alt="" width="36" height="36">
                            <span class="text-[16px] leading-[26px]">{{ __('landing.educo.who.lesson_open') }}</span>
                        </div>
                        <span class="text-[16px] leading-[26px]">{{ __('landing.educo.who.lesson_open_time') }}</span>
                    </div>
                    <div class="lp-who__row lp-who__row--locked" data-node-id="9:594">
                        <div class="lp-who__row-left">
                            <span class="lp-who__lock-wrap">
                                <img src="{{ $a['who_lock'] }}" alt="" width="16" height="16">
                            </span>
                            <span class="text-[16px] leading-[26px]">{{ __('landing.educo.who.lesson_locked') }}</span>
                        </div>
                        <span class="text-[16px] leading-[26px]">{{ __('landing.educo.who.lesson_locked_time') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lp-who__cards" data-node-id="9:650">
            @foreach ($cards as $i => $card)
                <div class="lp-who__card lp-reveal" style="--lp-delay:{{ ($i + 1) * 80 }}ms">
                    <div class="lp-who__icon" style="background:{{ $card['bg'] }}">
                        <img src="{{ $card['icon'] }}" alt="" width="24" height="24">
                    </div>
                    <h3>{{ $card['title'] }}</h3>
                    <p class="lp-lead" style="max-width:283px">{{ $card['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
