{{-- Inline GA4 ecommerce dataLayer push (dedupe purchase by transaction_id). --}}
@props(['payload' => null])

@if(is_array($payload) && ($payload['event'] ?? null))
@php
    $json = app(\App\Services\MarketingAnalyticsService::class)->toJson($payload);
    $isPurchase = ($payload['event'] ?? '') === 'purchase';
    $txId = $payload['ecommerce']['transaction_id'] ?? null;
@endphp
<script>
(function () {
    var payload = {!! $json !!};
    @if($isPurchase && $txId)
    try {
        var key = 'ml_purchase_' + @json((string) $txId);
        if (window.sessionStorage && sessionStorage.getItem(key)) {
            return;
        }
        if (window.sessionStorage) {
            sessionStorage.setItem(key, '1');
        }
    } catch (e) {}
    @endif
    if (typeof window.mindlyticsPushEcommerce === 'function') {
        window.mindlyticsPushEcommerce(payload);
    } else {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push(payload);
    }
})();
</script>
@endif
