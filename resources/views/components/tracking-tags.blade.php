{{--
  Marketing tracking: dataLayer + GTM (+ Clarity + Meta Pixel).
  Values come from Admin → التسويق → تتبع التسويق (with .env defaults).
  placement=head  → put near top of <head>
  placement=body  → put immediately after <body> (GTM noscript)
--}}
@props(['placement' => 'head'])

@php
    $t = \App\Support\MarketingWebAnalyticsSettings::forTracking();
    $analyticsEnabled = (bool) $t['enabled'];
    $gtmId = $t['gtm_container_id'];
    $clarityId = $t['clarity_project_id'];
    $metaPixelId = $t['meta_pixel_id'];
    $hasGtm = $analyticsEnabled && $gtmId !== '';
    $hasClarity = $analyticsEnabled && $clarityId !== '';
    $hasMeta = $analyticsEnabled && (bool) $t['meta_pixel_enabled'] && $metaPixelId !== '';
    $currency = $t['currency'];
@endphp

@if($placement === 'head' && ($hasGtm || $hasClarity || $hasMeta || $analyticsEnabled))
<script>
window.dataLayer = window.dataLayer || [];
window.mindlyticsPushEcommerce = window.mindlyticsPushEcommerce || function (payload) {
    if (!payload || typeof payload !== 'object') return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push(payload);

    // Mirror key funnel events to Meta Pixel when available
    try {
        if (typeof window.fbq !== 'function') return;
        var eventName = payload.event || '';
        var ecom = payload.ecommerce || {};
        var items = ecom.items || [];
        var contentIds = items.map(function (it) { return String(it.item_id || ''); }).filter(Boolean);
        var contents = items.map(function (it) {
            return {
                id: String(it.item_id || ''),
                quantity: parseInt(it.quantity || 1, 10) || 1,
                item_price: parseFloat(it.price || 0) || 0
            };
        });
        var value = typeof ecom.value === 'number' ? ecom.value : contents.reduce(function (s, c) {
            return s + (c.item_price * c.quantity);
        }, 0);
        var currency = ecom.currency || @json($currency);
        var common = {
            content_ids: contentIds,
            contents: contents,
            content_type: 'product',
            value: value,
            currency: currency
        };
        if (eventName === 'view_item') {
            window.fbq('track', 'ViewContent', common);
        } else if (eventName === 'view_item_list') {
            window.fbq('trackCustom', 'ViewItemList', common);
        } else if (eventName === 'select_item') {
            window.fbq('trackCustom', 'SelectItem', common);
        } else if (eventName === 'begin_checkout') {
            window.fbq('track', 'InitiateCheckout', common);
        } else if (eventName === 'add_payment_info') {
            window.fbq('track', 'AddPaymentInfo', common);
        } else if (eventName === 'purchase') {
            window.fbq('track', 'Purchase', Object.assign({}, common, {
                value: parseFloat(ecom.value || value) || 0,
                currency: currency
            }), { eventID: String(ecom.transaction_id || '') });
        }
    } catch (err) {}
};
</script>

@if($hasGtm)
<!-- Google Tag Manager -->
<script>
if (!window.__mlGtmLoaded) {
window.__mlGtmLoaded = true;
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',@json($gtmId));
}
</script>
<!-- End Google Tag Manager -->
@endif

@if($hasClarity)
<!-- Microsoft Clarity -->
<script type="text/javascript">
if (!window.__mlClarityLoaded) {
window.__mlClarityLoaded = true;
(function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
})(window, document, "clarity", "script", @json($clarityId));
}
</script>
@endif

@if($hasMeta)
<!-- Meta Pixel -->
<script>
if (!window.__mlMetaPixelLoaded) {
window.__mlMetaPixelLoaded = true;
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', @json($metaPixelId));
fbq('track', 'PageView');
}
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ urlencode($metaPixelId) }}&ev=PageView&noscript=1"
alt="" /></noscript>
<!-- End Meta Pixel -->
@endif
@endif

@if($placement === 'body' && $hasGtm)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
