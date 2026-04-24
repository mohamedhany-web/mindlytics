@php
    $faviconUrl = $platformFaviconUrl ?? \App\Support\SiteBranding::faviconUrl();
    $logoUrl = $platformLogoUrl ?? \App\Support\SiteBranding::logoUrl();
@endphp
<meta name="theme-color" content="#0ea5e9">
<meta name="msapplication-TileColor" content="#0ea5e9">
<link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="48x48" href="{{ $faviconUrl }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $logoUrl }}">
<link rel="manifest" href="{{ url('/site.webmanifest') }}">
