@props([
    'name',
    'box' => 'size-6',
    'class' => '',
])

@php
    $src = \App\Support\StudentFigmaAssets::url($name);
@endphp

<span {{ $attributes->class(['sp-figma-ico', $box, $class]) }}
      style="--sp-ico: url('{{ $src }}')"
      aria-hidden="true"></span>
