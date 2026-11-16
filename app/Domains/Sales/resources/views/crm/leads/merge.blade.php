@extends('sales::crm.layout')

@section('title', 'دمج عميل مكرر — '.$lead->name)

@use('App\Domains\Sales\Models\SalesLead')
@use('App\Domains\Sales\Services\SalesLeadMergeService')
@use('App\Domains\Sales\Support\PhoneKey')

@section('content')
@php
    $mergeUrl = route($crm->route('leads.merge'), $lead->id);
    $fields = [
        'phone' => 'الهاتف', 'email' => 'الإيميل', 'stage' => 'المرحلة', 'assigned_to' => 'الموظف', 'interest' => 'الاهتمام', 'source' => 'المصدر',
        'order_id' => 'الطلب المربوط', 'next_follow_up_at' => 'المتابعة الجاية', 'created_at' => 'اتسجّل', 'updated_at' => 'آخر تحديث',
    ];
    $show = function (SalesLead $l, string $f) {
        return match ($f) {
            'stage' => SalesLead::STAGES[$l->stage] ?? $l->stage,
            'assigned_to' => $l->assignee?->name ?? 'من غير موظف',
            'source' => SalesLead::SOURCES[$l->source] ?? $l->source,
            'order_id' => $l->order_id ? '#'.$l->order_id : null,
            'next_follow_up_at', 'created_at', 'updated_at' => $l->{$f}?->format('Y-m-d H:i'),
            default => $l->{$f},
        };
    };
@endphp
<div style="display: flex; flex-direction: column; gap: 14px; max-width: 1180px">
    <div class="crm-module-header">
        <div>
            <h1 class="crm-title">دمج عميل مكرر</h1>
            <p class="crm-muted" style="margin: 4px 0 0">لو نفس الشخص متسجّل مرتين، ادمجهم: كل الأنشطة والمحادثات والتذاكر والمستندات بتتنقل للعميل اللي هيفضل، والخانات الفاضية فيه بتتكمّل من التاني.</p>
        </div>
        <a href="{{ route($crm->route('leads.show'), $lead->id) }}" class="crm-btn crm-btn-muted">رجوع لـ {{ $lead->name }}</a>
    </div>

    @if(! $other)
        <section class="crm-card" style="padding: 14px; display: flex; flex-direction: column; gap: 12px">
            <h2 style="margin: 0; font-size: 15px">العميل: <a href="{{ route($crm->route('leads.show'), $lead->id) }}" class="crm-link">{{ $lead->name }}</a> <span class="crm-muted" dir="ltr">{{ $lead->phone }}</span></h2>
            <form method="get" action="{{ $mergeUrl }}" style="display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap">
                <label class="crm-label" style="flex: 1 1 320px">دوّر على العميل التاني (اسم، رقم، إيميل، رقم العميل)<input class="crm-input" type="search" name="q" value="{{ $search }}"></label>
                <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
                @if($search !== '')<a href="{{ $mergeUrl }}" class="crm-btn crm-btn-outline">الاقتراحات</a>@endif
            </form>
        </section>

        <section class="crm-card">
            <h2 class="crm-panel-title">{{ $search !== '' ? 'نتائج البحث' : 'ممكن يكونوا نفس الشخص' }}</h2>
            @if($candidates->isEmpty())
                <p style="padding: 18px 14px; margin: 0" class="crm-muted">{{ $search !== '' ? 'مفيش عملاء بالبحث ده ضمن عملائك.' : 'مفيش عملاء بنفس الرقم أو الإيميل أو اسم شبهه. لو عارف إن فيه مكرر، دوّر عليه بالبحث.' }}</p>
            @else
                <div style="overflow-x: auto">
                    <table class="crm-table" style="min-width: 760px">
                        <thead><tr><th>الاسم</th><th>الهاتف</th><th>الإيميل</th><th>المرحلة</th><th>الموظف</th><th>ليه مقترح</th><th></th></tr></thead>
                        <tbody>
                            @foreach($candidates as $c)
                                @php
                                    $why = array_filter([
                                        PhoneKey::same($c->phone, $lead->phone) ? 'نفس الرقم' : null,
                                        filled($lead->email) && mb_strtolower(trim((string) $c->email)) === mb_strtolower(trim((string) $lead->email)) ? 'نفس الإيميل' : null,
                                        $c->name === $lead->name ? 'نفس الاسم' : null,
                                    ]);
                                @endphp
                                <tr>
                                    <td><a href="{{ route($crm->route('leads.show'), $c->id) }}" class="crm-link" target="_blank" rel="noopener">{{ $c->name }}</a> <span class="crm-muted">#{{ $c->id }}</span></td>
                                    <td dir="ltr" style="text-align: start">{{ $c->phone }}</td>
                                    <td>{{ $c->email ?: '—' }}</td>
                                    <td>{{ SalesLead::STAGES[$c->stage] ?? $c->stage }}</td>
                                    <td>{{ $c->assignee?->name ?? '—' }}</td>
                                    <td>{{ $why ? implode('، ', $why) : ($search !== '' ? 'نتيجة بحث' : 'اسم شبهه') }}</td>
                                    <td><a href="{{ $mergeUrl }}?with={{ $c->id }}" class="crm-btn crm-btn-primary crm-btn-sm">قارن وادمج</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @else
        <form method="post" action="{{ route($crm->route('leads.merge.store'), $lead->id) }}" class="crm-card" x-data="{ keep: '{{ ($block['this'] && ! $block['other']) ? 'other' : 'this' }}' }"
              onsubmit="return confirm('تأكيد الدمج؟ العميل التاني هيتشال وكل سجلاته هتتنقل. الخطوة دي ما بترجعش.')">
            @foreach(['this', 'other'] as $key)
                @if($block[$key])
                    <div x-show="keep === '{{ $key }}'" class="crm-alert crm-alert-err" role="alert" style="margin: 12px 14px 0"><i class="fas fa-circle-exclamation"></i> {{ $block[$key] }}</div>
                @endif
            @endforeach
            @csrf
            <input type="hidden" name="other_id" value="{{ $other->id }}">
            <h2 class="crm-panel-title">قارن واختار العميل اللي هيفضل</h2>
            <div style="overflow-x: auto">
                <table class="crm-table crm-table-plain" style="min-width: 720px">
                    <thead>
                        <tr>
                            <th style="width: 180px"></th>
                            @foreach(['this' => $lead, 'other' => $other] as $key => $l)
                                <th>
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer">
                                        <input type="radio" name="keep" value="{{ $key }}" x-model="keep">
                                        <span>{{ $l->name }} <span style="opacity: .75">#{{ $l->id }}</span></span>
                                        <span x-show="keep === '{{ $key }}'" style="background: #F2B630; color: #212529; border-radius: 3px; padding: 0 6px; font-size: 11px">هيفضل</span>
                                    </label>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fields as $f => $label)
                            @php $a = $show($lead, $f); $b = $show($other, $f); @endphp
                            <tr>
                                <td style="font-weight: 700">{{ $label }}</td>
                                @foreach([$a, $b] as $val)
                                    <td style="{{ $a != $b ? 'background: #FFF8E6 !important;' : '' }}{{ $f === 'phone' ? ' text-align: start' : '' }}" @if($f === 'phone') dir="ltr" @endif>{{ filled($val) ? $val : '—' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td style="font-weight: 700">السجلات المربوطة</td>
                            @foreach([$lead, $other] as $l)
                                <td>{{ ($counts[$l->id] ?? []) === [] ? 'مفيش' : collect($counts[$l->id])->map(fn ($n, $t) => SalesLeadMergeService::label($t).' '.$n)->implode('، ') }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding: 14px; display: flex; flex-direction: column; gap: 10px; border-top: 1px solid var(--crm-line)">
                <ul class="crm-muted" style="margin: 0; padding-inline-start: 18px; font-size: 13px; line-height: 1.9">
                    <li>العميل اللي هيفضل بيحتفظ باسمه ورقمه ومرحلته وموظفه.</li>
                    <li>أي خانة فاضية فيه بتتكمّل من التاني، والملاحظات بتتجمع.</li>
                    <li>كل الأنشطة والمحادثات والتذاكر والمستندات والتحويلات بتتنقل له، ويتسجّل في السجل إنه اتدمج.</li>
                    <li>العميل التاني بيتشال من القوايم (متسجّل إنه اتدمج، مش ممسوح نهائي).</li>
                </ul>
                <div style="display: flex; gap: 8px; justify-content: flex-end">
                    <a href="{{ $mergeUrl }}" class="crm-btn crm-btn-outline">اختار عميل تاني</a>
                    <button type="submit" class="crm-btn crm-btn-primary" :disabled="{{ json_encode(['this' => (bool) $block['this'], 'other' => (bool) $block['other']]) }}[keep]" :style="{{ json_encode(['this' => (bool) $block['this'], 'other' => (bool) $block['other']]) }}[keep] ? 'opacity: .5' : ''"><i class="fas fa-code-merge"></i> ادمج</button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
