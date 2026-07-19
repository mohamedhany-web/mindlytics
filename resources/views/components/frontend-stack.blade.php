{{--
  Production-safe stack (no cdn.tailwindcss.com):
  - Compiled Tailwind CSS + Learning OS CSS
  - Paths use request base path (survives wrong APP_URL / cPanel layouts)
  - Alpine local with jsDelivr fallback
--}}
@php
    $assetBase = rtrim(request()->getBasePath(), '/');
@endphp
<link rel="stylesheet" href="{{ $assetBase }}/css/tailwind.css?v=3">
<link rel="stylesheet" href="{{ $assetBase }}/css/mindlytics-los.css?v=6">
<script defer src="{{ $assetBase }}/js/vendor/alpine.min.js"
        onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js';"></script>
