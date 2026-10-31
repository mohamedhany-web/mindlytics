@extends('sales::crm.layout')
@section('title','التقويم')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'التقويم','subtitle'=>$from->format('Y-m-d').' → '.$to->format('Y-m-d')])
<div style="display:grid;gap:16px;grid-template-columns:1fr 1fr">
<section class="crm-card"><div class="crm-dashlet-head"><h2>متابعات مجدولة</h2></div>
<table class="crm-table"><thead><tr><th>العميل</th><th>الموعد</th><th>القناة</th></tr></thead><tbody>
@forelse($followUps as $lead)<tr>
<td><a class="crm-link" href="{{ route($crm->route('leads.show'),$lead) }}">{{ $lead->name }}</a></td>
<td class="{{ $lead->next_follow_up_at?->isPast() ? 'crm-bad':'' }}">{{ $lead->next_follow_up_at?->format('d/m H:i') }}</td>
<td>{{ \App\Domains\Sales\Models\SalesLead::FOLLOW_UP_CHANNELS[$lead->follow_up_channel] ?? '—' }}</td>
</tr>@empty<tr><td colspan="3" class="crm-muted" style="padding:16px;text-align:center">لا متابعات</td></tr>@endforelse
</tbody></table></section>
<section class="crm-card"><div class="crm-dashlet-head"><h2>أنشطة مجدولة</h2></div>
<table class="crm-table"><thead><tr><th>النوع</th><th>العنوان</th><th>الموعد</th></tr></thead><tbody>
@forelse($activities as $a)<tr>
<td>{{ \App\Domains\Sales\Models\SalesActivity::typeLabel($a->type) }}</td>
<td><a class="crm-link" href="{{ route($crm->route('activities.show'),$a) }}">{{ $a->title ?: '#'.$a->id }}</a></td>
<td>{{ optional($a->scheduled_at ?? $a->due_at)->format('d/m H:i') }}</td>
</tr>@empty<tr><td colspan="3" class="crm-muted" style="padding:16px;text-align:center">لا أنشطة</td></tr>@endforelse
</tbody></table></section>
</div>
@endsection
