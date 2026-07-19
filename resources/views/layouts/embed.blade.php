<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'النمط التعليمي')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        html { margin: 0; padding: 0; width: 100%; overflow-x: auto; }
        body { margin: 0; padding: 0.5rem 0.75rem 0.75rem; min-height: 100%; width: 100%; max-width: 100%; background: #fff; overflow-x: auto; }
        @media (min-width: 768px) { body { padding: 0.75rem 1rem 1rem; } }
        body > * { max-width: 100%; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
