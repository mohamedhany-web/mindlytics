@props([
    'type' => 'general',
    'box' => 'size-5',
    'class' => '',
])

@php
    $bubble = match ($type) {
        'course' => 'var(--sp-sky)',
        'exam' => 'var(--sp-lilac)',
        'assignment' => 'var(--sp-peach)',
        'grade' => 'var(--sp-amber-soft)',
        'announcement' => 'var(--sp-mint)',
        'reminder' => 'var(--sp-sky)',
        'warning' => '#f9e4d7',
        'system' => '#f0f0ec',
        default => 'var(--sp-sky)',
    };
    $icon = match ($type) {
        'course' => 'icon-courses.svg',
        'exam' => 'icon-exams.svg',
        'assignment' => 'icon-messages.svg',
        'grade' => 'icon-star.svg',
        'announcement' => 'icon-community.svg',
        'reminder' => 'icon-calendar.svg',
        'warning' => 'icon-exams.svg',
        'system' => 'icon-settings.svg',
        default => 'icon-notifications.svg',
    };
@endphp

<span {{ $attributes->class(['sp-icon-bubble', 'shrink-0', $class]) }} style="background:{{ $bubble }}">
    <x-student.figma-icon :name="$icon" :box="$box" />
</span>
