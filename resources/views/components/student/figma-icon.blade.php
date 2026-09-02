@props([
    'name',
    'box' => 'size-6',
    'class' => '',
])

@php
    $src = \App\Support\StudentFigmaAssets::url($name);
@endphp

<img src="{{ $src }}"
     {{ $attributes->class(['sp-figma-img', $box, $class]) }}
     alt=""
     aria-hidden="true"
     loading="lazy"
     decoding="async">
