@php
    $statusLabels = [
        'draft' => 'مسودة',
        'pending' => 'معلقة',
        'partial' => 'مدفوعة جزئياً',
        'paid' => 'مدفوعة',
        'overdue' => 'متأخرة',
        'cancelled' => 'ملغاة',
        'refunded' => 'مستردة',
    ];
    $selected = $selected ?? old('status', 'pending');
@endphp
@foreach($statusLabels as $value => $label)
    <option value="{{ $value }}" @selected($selected === $value)>{{ $label }}</option>
@endforeach
