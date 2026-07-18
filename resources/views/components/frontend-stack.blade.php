{{-- Local Tailwind + Alpine (no CDN — works when cdn.tailwindcss.com is blocked) --}}
@if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css'])
@else
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=1">
@endif
<script defer src="{{ asset('js/vendor/alpine.min.js') }}"></script>
