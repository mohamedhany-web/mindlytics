@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
@endphp

<section class="lp-section lp-hero content-wrapper" data-node-id="5:105" aria-label="{{ __('landing.educo.hero.headline') }}">
    <div class="lp-hero__desktop relative" style="min-height: 780px;">
        <div class="lp-hero__stage" data-node-id="5:482">
            <div class="lp-hero__dots lp-anim lp-anim--float" data-node-id="5:302" aria-hidden="true">
                @for ($i = 0; $i < 9; $i++)
                    <img src="{{ $a['hero_dot'] }}" alt="" width="100" height="9">
                @endfor
            </div>

            <div class="lp-hero__box1 lp-reveal" data-node-id="5:464" style="--lp-delay:80ms">
                <div class="lp-hero__video" data-node-id="5:129">
                    <img class="lp-hero__media-img" src="{{ $a['hero_video'] }}" alt="{{ __('landing.educo.hero.how_title') }}" width="158" height="158" loading="eager">
                    <img class="lp-hero__play" src="{{ $a['hero_play'] }}" alt="" width="36" height="36">
                </div>
                <div>
                    <p class="m-0 font-bold text-[18px]" style="color:var(--lp-primary)">{{ __('landing.educo.hero.how_title') }}</p>
                    <p class="m-0 mt-1 text-[12px] leading-4" style="color:var(--lp-text)">{{ __('landing.educo.hero.how_meta') }}</p>
                </div>
            </div>

            <div class="lp-hero__title lp-reveal" data-node-id="5:461" style="--lp-delay:0ms">
                <div class="lp-hero__title-inner" data-node-id="5:460">
                    <p class="lp-eyebrow">{{ __('landing.educo.hero.eyebrow') }}</p>
                    <h1 class="lp-display" style="width:794px;max-width:100%">{{ __('landing.educo.hero.headline') }}</h1>
                </div>
                <p class="lp-lead lp-hero__lead">{{ __('landing.educo.hero.subheadline') }}</p>
            </div>

            <div class="lp-hero__cta lp-reveal" data-node-id="5:462" style="--lp-delay:140ms">
                <a href="{{ route('register') }}" class="lp-btn-yellow">{{ __('landing.educo.hero.cta_join') }}</a>
                <a href="{{ route('public.courses') }}" class="lp-link-underline">{{ __('landing.educo.hero.cta_more') }}</a>
            </div>

            <div class="lp-hero__testimonial lp-reveal" data-node-id="5:470" style="--lp-delay:200ms">
                <img class="lp-hero__stars" src="{{ $a['hero_stars'] }}" alt="" width="128" height="24">
                <p class="lp-lead" style="width:281px;max-width:100%">{{ __('landing.educo.hero.quote') }}</p>
                <div class="flex items-center gap-3 mt-1">
                    <div class="lp-hero__avatar">
                        <img src="{{ $a['hero_avatar'] }}" alt="" width="48" height="48" loading="lazy">
                    </div>
                    <div>
                        <p class="m-0 font-semibold text-[14px] leading-none" style="color:var(--lp-primary)">{{ __('landing.educo.hero.quote_name') }}</p>
                        <p class="m-0 mt-2 text-[12px] leading-4" style="color:var(--lp-text)">{{ __('landing.educo.hero.quote_role') }}</p>
                    </div>
                </div>
            </div>

            <div class="lp-hero__arrow lp-anim lp-anim--draw" data-node-id="5:154" aria-hidden="true">
                <img src="{{ $a['hero_arrow'] }}" alt="" width="96" height="90" class="block size-full max-w-none">
            </div>

            <div class="lp-hero__box2 lp-reveal" data-node-id="5:466" style="--lp-delay:120ms">
                <div class="lp-hero__instructor-img" data-node-id="5:130">
                    <img class="lp-hero__media-img" src="{{ $a['hero_instructor'] }}" alt="{{ __('landing.educo.hero.instructor_name') }}" width="212" height="244" loading="eager">
                </div>
                <div class="text-end">
                    <p class="m-0 font-bold text-[18px]" style="color:var(--lp-primary)">{{ __('landing.educo.hero.instructor_name') }}</p>
                    <p class="m-0 mt-1 text-[12px] leading-4" style="color:var(--lp-text)">{{ __('landing.educo.hero.instructor_role') }}</p>
                </div>
            </div>
        </div>

        <div class="lp-hero__scroll lp-anim lp-anim--bounce" data-node-id="5:459">
            <img src="{{ $a['hero_scroll'] }}" alt="" width="12" height="18">
            <span>{{ __('landing.educo.hero.scroll') }}</span>
        </div>
    </div>

    <div class="lp-hero__mobile lp-container">
        <div class="text-center max-w-xl mx-auto mb-8 lp-reveal">
            <p class="lp-eyebrow mb-5">{{ __('landing.educo.hero.eyebrow') }}</p>
            <h1 class="lp-display mb-6">{{ __('landing.educo.hero.headline') }}</h1>
            <p class="lp-lead mb-8">{{ __('landing.educo.hero.subheadline') }}</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('register') }}" class="lp-btn-yellow w-full sm:w-auto">{{ __('landing.educo.hero.cta_join') }}</a>
                <a href="{{ route('public.courses') }}" class="lp-link-underline">{{ __('landing.educo.hero.cta_more') }}</a>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 items-start max-w-lg mx-auto">
            <div class="lp-reveal" style="--lp-delay:80ms">
                <div class="lp-hero__video mx-auto sm:mx-0">
                    <img class="lp-hero__media-img" src="{{ $a['hero_video'] }}" alt="{{ __('landing.educo.hero.how_title') }}" width="158" height="158" loading="lazy">
                    <img class="lp-hero__play" src="{{ $a['hero_play'] }}" alt="" width="36" height="36">
                </div>
                <p class="m-0 mt-2 font-bold text-[16px]" style="color:var(--lp-primary)">{{ __('landing.educo.hero.how_title') }}</p>
                <p class="m-0 text-[12px]" style="color:var(--lp-text)">{{ __('landing.educo.hero.how_meta') }}</p>
            </div>
            <div class="text-end lp-reveal" style="--lp-delay:140ms">
                <div class="lp-hero__instructor-img ms-auto" style="width:100%;max-width:160px;height:190px">
                    <img class="lp-hero__media-img" src="{{ $a['hero_instructor'] }}" alt="{{ __('landing.educo.hero.instructor_name') }}" width="160" height="190" loading="lazy">
                </div>
                <p class="m-0 mt-2 font-bold text-[16px]" style="color:var(--lp-primary)">{{ __('landing.educo.hero.instructor_name') }}</p>
                <p class="m-0 text-[12px]" style="color:var(--lp-text)">{{ __('landing.educo.hero.instructor_role') }}</p>
            </div>
        </div>
        <div class="mt-8 max-w-sm mx-auto lp-reveal" style="--lp-delay:200ms">
            <img class="lp-hero__stars mb-3" src="{{ $a['hero_stars'] }}" alt="" width="128" height="24">
            <p class="lp-lead">{{ __('landing.educo.hero.quote') }}</p>
            <div class="flex items-center gap-3 mt-3">
                <div class="lp-hero__avatar">
                    <img src="{{ $a['hero_avatar'] }}" alt="" width="48" height="48" loading="lazy">
                </div>
                <div>
                    <p class="m-0 font-semibold text-[14px]" style="color:var(--lp-primary)">{{ __('landing.educo.hero.quote_name') }}</p>
                    <p class="m-0 text-[12px]" style="color:var(--lp-text)">{{ __('landing.educo.hero.quote_role') }}</p>
                </div>
            </div>
        </div>
        <div class="lp-hero__scroll mt-8">
            <img src="{{ $a['hero_scroll'] }}" alt="" width="12" height="18">
            <span>{{ __('landing.educo.hero.scroll') }}</span>
        </div>
    </div>
</section>
