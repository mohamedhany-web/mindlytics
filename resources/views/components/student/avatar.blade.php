@props([
    'user' => null,
])

@php
    $user = $user ?? auth()->user();
    $initial = mb_strtoupper(mb_substr($user->name ?? '?', 0, 1));
@endphp

@if($user && filled($user->profile_image_url))
    <img src="{{ $user->profile_image_url }}" alt="" {{ $attributes->class(['sp-avatar']) }}>
@else
    <span {{ $attributes->class(['sp-avatar', 'sp-avatar-fallback']) }} aria-hidden="true">{{ $initial }}</span>
@endif
