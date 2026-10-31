@extends('sales::crm.layout')
@section('title', 'صلاحيات — '.$target->name)
@section('content')
@include('sales::crm.partials.module-header', [
    'title' => 'صلاحيات CRM: '.$target->name,
    'subtitle' => 'دور CRM الحالي: '.match($targetScope->role){ 'admin' => 'أدمن', 'manager' => 'مدير مبيعات', default => 'مندوب' }.' — علّم اللي يظهر، وشلّ اللي مش محتاجينه',
])

<form method="post" action="{{ route($crm->route('access.update'), $target) }}" class="crm-card" style="padding:0;overflow:hidden">
    @csrf
    @method('PUT')
    <div class="crm-dashlet-head">
        <i class="fas fa-sliders"></i>
        <h2>الميزات الظاهرة لهذا المستخدم</h2>
        <a href="{{ route($crm->route('access.index')) }}" class="crm-btn crm-btn-sm crm-btn-slate">رجوع للقائمة</a>
    </div>

    <div style="padding:16px;display:flex;flex-direction:column;gap:18px">
        <p class="crm-muted" style="margin:0;font-size:13px">
            الافتراضي حسب الدور معلّم بـ «أساس الدور». أي تغيير يتحفظ كاستثناء لهذا المستخدم فقط.
        </p>

        @foreach($groups as $groupKey => $groupLabel)
            <section>
                <h3 class="crm-panel-title" style="border-radius:4px">{{ $groupLabel }}</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:8px;padding:12px 0">
                    @foreach($features as $feature)
                        @continue($feature['group'] !== $groupKey)
                        @php
                            $checked = !empty($effective[$feature['key']]);
                            $baseline = !empty($baselines[$feature['key']]);
                        @endphp
                        <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1px solid var(--crm-line);border-radius:6px;background:#fff;cursor:pointer">
                            <input type="hidden" name="features[{{ $feature['key'] }}]" value="0">
                            <input type="checkbox" name="features[{{ $feature['key'] }}]" value="1" @checked($checked) style="margin-top:3px;width:16px;height:16px">
                            <span style="flex:1;min-width:0">
                                <strong style="display:block;font-size:13px">{{ $feature['label'] }}</strong>
                                <span class="crm-muted" style="font-size:11px">
                                    {{ $baseline ? 'أساس الدور: ظاهر' : 'أساس الدور: مخفي' }}
                                    @if($checked !== $baseline)
                                        · <span class="crm-warn">معدَّل</span>
                                    @endif
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="crm-dashlet-foot" style="justify-content:space-between">
        <span>التغييرات تخص {{ $target->name }} فقط</span>
        <button type="submit" class="crm-btn crm-btn-primary">حفظ الصلاحيات</button>
    </div>
</form>
@endsection
