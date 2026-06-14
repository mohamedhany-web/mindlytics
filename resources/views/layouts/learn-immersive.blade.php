@php $learnLocale = app()->getLocale(); @endphp
<!DOCTYPE html>
<html lang="{{ $learnLocale }}" dir="rtl" class="learn-rtl-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('student.learn'))</title>
    @include('components.favicon-meta')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('meta')
    @stack('styles')
</head>
<body class="learn-immersive-body learn-rtl antialiased" dir="rtl">
    @yield('content')
    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</body>
</html>
