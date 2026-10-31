@php
    $typeLabels = [
        'course' => 'كورس أونلاين',
        'subscription' => 'اشتراك',
        'membership' => 'عضوية',
        'learning_path' => 'مسار تعليمي',
        'offline_course' => 'كورس أوفلاين',
        'other' => 'أخرى',
    ];
    $selected = $selected ?? old('type', 'course');
@endphp
@foreach($typeLabels as $value => $label)
    <option value="{{ $value }}" @selected($selected === $value)>{{ $label }}</option>
@endforeach
