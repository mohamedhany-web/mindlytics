@extends('sales::crm.layout')

@section('title', 'تحويل لطالب — '.$lead->name)

@section('content')
<form method="post" action="{{ route($crm->route('leads.convert.store'), $lead->id) }}" style="display: flex; flex-direction: column; gap: 14px; max-width: 1180px">
    @csrf
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <h1 style="margin: 0; font-size: 26px; font-weight: 300; color: #4B4B4B">{{ $lead->name }} « تحويل لطالب</h1>
        <span style="flex: 1"></span>
        <button type="submit" class="crm-btn crm-btn-primary" @if($orders->isEmpty()) disabled style="opacity: .55" @endif>حفظ</button>
        <a href="{{ route($crm->route('leads.show'), $lead->id) }}" class="crm-btn crm-btn-muted">إلغاء</a>
    </div>

    <p class="crm-alert crm-alert-ok" style="margin: 0; line-height: 1.8">التحويل بيحصل <strong>تلقائي</strong> لما طلب الطالب يتعمد في المحاسبة. استخدم الشاشة دي لو الطالب دفع برقم أو إيميل مختلف عن العميل: اختار الطلب وهيتربط بالعميل، والصفقة تبقى «فاز» بالمبلغ المدفوع فعلاً.</p>

    @if($lead->order_id)
        <p class="crm-alert crm-alert-err" style="margin: 0">العميل ده مربوط بالفعل بالطلب #{{ $lead->order_id }}.</p>
    @endif

    <section class="crm-card">
        <h2 style="margin: 0; padding: 10px 14px; background: #627587; color: #fff; font-size: 14px; font-weight: 700">1 · الطالب (حسابات بنفس الرقم أو الإيميل)</h2>
        <div style="overflow-x: auto">
            <table class="crm-table crm-table-plain" style="min-width: 560px">
                <thead><tr><th>الطالب</th><th>الهاتف</th><th>الإيميل</th><th>اتسجّل</th></tr></thead>
                <tbody>
                    @forelse($students as $s)
                        <tr><td style="font-weight: 700">{{ $s->name }} (#{{ $s->id }})</td><td dir="ltr" style="text-align: end">{{ $s->phone }}</td><td>{{ $s->email }}</td><td class="crm-muted">{{ $s->created_at?->format('d/m/Y') }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="crm-muted" style="text-align: center; padding: 16px">مفيش حساب طالب بنفس رقم أو إيميل العميل. اطلب من الطالب يسجّل أو يدفع من الموقع بنفس رقمه.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="crm-card">
        <h2 style="margin: 0; padding: 10px 14px; background: #627587; color: #fff; font-size: 14px; font-weight: 700">2 · الطلب المدفوع</h2>
        <div style="padding: 14px; display: flex; flex-direction: column; gap: 8px; font-size: 13px">
            @forelse($orders as $o)
                <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid #DADDE1">
                    <input type="radio" name="order_id" value="{{ $o->id }}" @checked($loop->first) required>
                    <span style="font-weight: 700">#{{ $o->id }}</span>
                    <span>{{ $o->course?->title ?? '—' }}</span>
                    <span>{{ number_format((float) $o->amount, 2) }} ج.م</span>
                    <span class="crm-muted">{{ $o->user?->name }} · معتمد {{ $o->approved_at?->format('d/m/Y') }}</span>
                </label>
            @empty
                <p class="crm-muted" style="margin: 0">مفيش طلبات معتمدة غير مربوطة للحسابات دي. لو الدفع تم خارج المنصة، الأدمن يعتمد الصفقة من «اعتماد صفقات الفوز» بسبب مكتوب.</p>
            @endforelse
            <p class="crm-muted" style="margin: 0">العمولة بتتحسب على المدفوع فعلاً، وبتتسترجع تلقائي لو حصل استرداد.</p>
        </div>
    </section>
</form>
@endsection
