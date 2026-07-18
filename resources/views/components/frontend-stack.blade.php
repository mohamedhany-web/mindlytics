{{-- Self-hosted Tailwind Play CDN + Alpine (no external CDN dependency) --}}
<script src="{{ asset('js/vendor/tailwindcss.js') }}"></script>
<script defer src="{{ asset('js/vendor/alpine.min.js') }}"
        onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js';"></script>
