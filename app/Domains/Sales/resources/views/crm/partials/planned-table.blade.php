{{-- جدول الأنشطة المخططة داخل ملف العميل أو الطالب. --}}
@use('App\Domains\Sales\Models\SalesPlannedActivity')
<div style="overflow-x: auto">
    <table class="crm-table crm-table-plain" style="min-width: 560px">
        <thead><tr><th>النوع</th><th>الموضوع</th><th>الموعد</th><th>الحالة</th><th>المكلَّف</th><th></th></tr></thead>
        <tbody>
            @forelse($items as $a)
                <tr>
                    <td><i class="fas {{ SalesPlannedActivity::KIND_ICONS[$a->kind] ?? 'fa-circle' }}" style="color: #534D64"></i> {{ SalesPlannedActivity::kindLabel($a->kind) }}</td>
                    <td><a class="crm-link" href="{{ route($crm->route('activities.show'), $a->id) }}">{{ $a->title }}</a></td>
                    <td class="{{ $a->isOverdue() ? 'crm-bad' : '' }}">{{ $a->starts_at->format('d/m/Y H:i') }}</td>
                    <td class="{{ $a->status === 'done' ? 'crm-good' : (in_array($a->status, ['missed', 'cancelled'], true) ? 'crm-muted' : '') }}">{{ SalesPlannedActivity::statusLabel($a->status) }}{{ $a->isOverdue() ? ' (متأخر)' : '' }}</td>
                    <td>{{ $a->assignee?->name ?? '—' }}</td>
                    <td>@if($crm->canComplete($a))<a class="crm-btn crm-btn-outline crm-btn-sm" href="{{ route($crm->route('activities.show'), $a->id) }}#complete">تنفيذ</a>@endif</td>
                </tr>
            @empty
                <tr><td colspan="6" class="crm-muted" style="text-align: center">مفيش أنشطة مجدولة.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
