<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>{{ $title ?? 'Video' }}</title>
    <style>
        html, body { height: 100%; margin: 0; background: #000; }
        .wrap { position: fixed; inset: 0; }
        iframe, video { width: 100%; height: 100%; border: 0; display: block; }
    </style>
</head>
<body>
<div class="wrap">
    @if(($type ?? '') === 'html5')
        <video playsinline controls controlsList="nodownload noplaybackrate noremoteplayback" disablePictureInPicture disableRemotePlayback>
            <source src="{{ $src }}" type="{{ $mime ?? 'video/mp4' }}">
        </video>
    @else
        <iframe src="{{ $src }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                allowfullscreen
                loading="eager"
                title="{{ $title ?? 'Video' }}"></iframe>
    @endif
</div>
</body>
</html>

