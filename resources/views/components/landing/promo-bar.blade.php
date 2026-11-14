@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
@endphp

<div class="lp-promo" data-node-id="40:1622">
    <p>
        {{ __('landing.educo.promo.text') }}
        <a href="{{ route('register') }}">{{ __('landing.educo.promo.link') }}</a>
    </p>
    <img src="{{ $a['header_arrow'] }}" alt="" width="16" height="16">
</div>
