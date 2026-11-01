@extends('sales::crm.layout')

@section('title', 'الطلاب')

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">الطلاب</h1>
        <form method="get" action="{{ route($crm->route('students.index')) }}" style="display: flex; gap: 6px">
            <label class="crm-sr" for="student-q">بحث في الطلاب</label>
            <input id="student-q" class="crm-input" type="search" name="q" value="{{ $q }}" placeholder="الاسم، الرقم، الإيميل" style="width: 240px">
            <button type="submit" class="crm-btn crm-btn-slate">بحث</button>
        </form>
    </div>

    <p class="crm-muted" style="margin: 0; font-size: 13px">
        @if($crm->isAdmin()) كل الطلاب المسجّلين ({{ number_format($total) }}).
        @elseif($crm->isManager()) الطلاب اللي جم من عملاء فريقك ({{ number_format($total) }}) — العميل اتحوّل للطالب أو اتربط بطلبه.
        @else الطلاب اللي جم من عملائك ({{ number_format($total) }}) — العميل اتحوّل للطالب أو اتربط بطلبه.
        @endif
    </p>

    <section class="crm-card">
        <div class="crm-toolbar"><span style="flex: 1"></span>@include('sales::crm.partials.pager', ['paginator' => $students])</div>
        <div style="overflow-x: auto">
            <table class="crm-table" style="min-width: 820px">
                <thead><tr><th>الطالب</th><th>الهاتف</th><th>الإيميل</th><th>الكورسات</th><th>المدفوع</th><th>موظف المبيعات</th><th>اتسجّل</th></tr></thead>
                <tbody>
                    @forelse($students as $s)
                        <tr>
                            <td><a class="crm-link" href="{{ route($crm->route('students.show'), $s->id) }}">{{ $s->name }}</a>@unless($s->is_active)<span class="crm-muted" style="font-size: 11px"> (موقوف)</span>@endunless</td>
                            <td dir="ltr" style="text-align: end">{{ $s->phone ?: '—' }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->courses_count }}</td>
                            <td class="{{ ($paid[$s->id] ?? 0) > 0 ? 'crm-good' : 'crm-muted' }}">{{ number_format((float) ($paid[$s->id] ?? 0), 0) }} ج.م</td>
                            <td>{{ $owners[$s->id] ?? '—' }}</td>
                            <td class="crm-muted">{{ $s->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="crm-muted" style="text-align: center; padding: 22px">{{ $q !== '' ? 'مفيش نتائج للبحث ده.' : 'لسه مفيش طلاب مرتبطين. الطالب بيظهر هنا لما العميل يدفع ويتحوّل.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
