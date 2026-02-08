{{-- SEO Meta Tags Component --}}
@php
    $title = $title ?? 'Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي';
    $description = $description ?? 'تعلم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف مع Mindlytics. كورسات برمجة عربية شاملة، دروس تفاعلية، مشاريع عملية، وشهادات معتمدة.';
    $keywords = $keywords ?? 'برمجة, ذكاء اصطناعي, تعلم البرمجة, كورسات برمجة, دورات برمجة, برمجة عربية';
    $image = $image ?? asset('images/og-image.jpg');
    $url = $url ?? url()->current();
    $type = $type ?? 'website';
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="Mindlytics">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="language" content="Arabic">
<meta name="revisit-after" content="7 days">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $url }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="ar_AR">
<meta property="og:site_name" content="Mindlytics">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

