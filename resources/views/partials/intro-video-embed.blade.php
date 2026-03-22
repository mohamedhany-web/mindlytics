{{--
  فيديو مقدمة الكورس/المسار: YouTube، Vimeo، Bunny (رابط مباشر أو embed/play).
  @param string $url رابط الفيديو
  @param string $title عنوان للـ iframe / إتاحة
--}}
@props([
    'url' => '',
    'title' => 'مقدمة',
])
@php
    $resolved = \App\Support\IntroVideoResolver::resolve($url);
@endphp
<div {{ $attributes->merge(['class' => 'custom-video-player-wrapper']) }}>
@if(in_array($resolved['type'], ['youtube', 'vimeo', 'bunny_embed'], true) && !empty($resolved['embed']))
    <div class="intro-video-container">
        <iframe src="{{ $resolved['embed'] }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                allowfullscreen
                loading="lazy"
                title="{{ $title }}"></iframe>
    </div>
@elseif($resolved['type'] === 'html5' && !empty($resolved['direct']))
    <div class="intro-video-container" style="padding-bottom: 0; height: auto; min-height: 320px;">
        <video class="w-full rounded-lg" style="max-height: 70vh;" playsinline controls preload="metadata">
            <source src="{{ $resolved['direct'] }}" type="{{ $resolved['mime'] ?: 'video/mp4' }}">
            {{ __('public.browser_no_video') ?? 'المتصفح لا يدعم تشغيل الفيديو.' }}
        </video>
    </div>
@else
    <div class="bg-gray-100 rounded-lg p-6 text-center">
        <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
        <p class="text-gray-700 text-sm font-medium">{{ __('public.intro_video_unsupported') }}</p>
    </div>
@endif
</div>
