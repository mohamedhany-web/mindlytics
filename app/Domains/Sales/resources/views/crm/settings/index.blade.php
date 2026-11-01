@extends('sales::crm.layout')

@section('title', 'إعدادات CRM')

@use('App\Domains\Sales\Http\Controllers\Crm\SalesCrmSettingsController')
@use('App\Domains\Sales\Support\CrmPermissions')
@php
    $roleLabel = fn (string $r) => CrmPermissions::ROLES[$r] ?? $r;
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">إعدادات CRM</h1>
        <span class="crm-muted" style="font-size: 12px">الأدمن دايماً عنده كل الصلاحيات. التعديل هنا بيأثر على شاشات CRM لموظفي المبيعات ومديريهم.</span>
    </div>

    <nav aria-label="أقسام الإعدادات" style="display: flex; flex-wrap: wrap; gap: 6px">
        @foreach(SalesCrmSettingsController::TABS as $k => $v)
            <a href="{{ route('admin.crm.settings.index', ['tab' => $k]) }}" class="crm-chip {{ $tab === $k ? 'is-active' : '' }}" @if($tab === $k) aria-current="page" @endif>{{ $v }}</a>
        @endforeach
    </nav>

    @if($tab === 'roles')
        <section class="crm-card">
            <div class="crm-dashlet-head"><i class="fas fa-layer-group"></i><h2>نطاق البيانات (ثابت حسب الدور)</h2></div>
            <div style="overflow-x: auto">
                <table class="crm-table crm-table-plain" style="min-width: 640px">
                    <thead><tr><th></th><th>موظف المبيعات</th><th>مدير المبيعات</th><th>الأدمن</th></tr></thead>
                    <tbody>
                        <tr><td style="font-weight: 700">العملاء والصفقات والمحادثات</td><td>بتوعه</td><td>بتوع فريقه</td><td>الكل</td></tr>
                        <tr><td style="font-weight: 700">الطلاب</td><td>اللي جم من عملائه</td><td>اللي جم من عملاء فريقه</td><td>كل الطلاب</td></tr>
                        <tr><td style="font-weight: 700">الأنشطة والتقارير</td><td>بتاعته</td><td>بتاعة فريقه</td><td>الكل</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <form method="post" action="{{ route('admin.crm.settings.roles.update') }}" class="crm-card">
            @csrf @method('PUT')
            <div class="crm-dashlet-head"><i class="fas fa-user-shield"></i><h2>صلاحيات الوحدات والإجراءات</h2></div>
            <div style="overflow-x: auto">
                <table class="crm-table" style="min-width: 640px">
                    <thead><tr><th>الوحدة / الإجراء</th>@foreach(CrmPermissions::ROLES as $r => $label)<th style="text-align: center">{{ $label }}</th>@endforeach<th style="text-align: center">الأدمن</th></tr></thead>
                    <tbody>
                        @foreach(CrmPermissions::MODULES as $module => $m)
                            <tr style="background: #EEF0F2"><td colspan="{{ count(CrmPermissions::ROLES) + 2 }}" style="font-weight: 700"><i class="fas {{ $m['icon'] }}" style="color: #534D64"></i> {{ $m['label'] }}</td></tr>
                            @foreach($m['actions'] as $action => $a)
                                @php $key = $module.'.'.$action; @endphp
                                <tr>
                                    <td style="padding-inline-start: 26px">{{ $a['label'] }}</td>
                                    @foreach(CrmPermissions::ROLES as $r => $label)
                                        <td style="text-align: center">
                                            @if(CrmPermissions::applies($r, $key))
                                                <input type="checkbox" name="perm[{{ $r }}][{{ SalesCrmSettingsController::field($key) }}]" value="1" @checked($matrix[$r][$key] ?? false) aria-label="{{ $label }}: {{ $m['label'] }} — {{ $a['label'] }}" style="width: 17px; height: 17px; accent-color: #B8492F">
                                            @else
                                                <span class="crm-muted" title="مش متاح للدور ده">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align: center; color: #0E6B44"><i class="fas fa-check" aria-label="مسموح دايماً"></i></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 12px 14px; border-top: 1px solid #E5E7EB">
                <button type="submit" class="crm-btn crm-btn-primary">حفظ الصلاحيات</button>
                <button type="submit" name="reset" value="1" class="crm-btn crm-btn-outline" onclick="return confirm('ترجع كل صلاحيات الأدوار للافتراضي؟')">رجوع للافتراضي</button>
                <span class="crm-muted" style="font-size: 12px">قفل «دخول الوحدة» بيخفي التاب ويمنع فتح صفحاتها. «—» يعني الإجراء مش متاح للدور ده أصلاً.</span>
            </div>
        </form>
    @elseif($tab === 'users')
        <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
            <section class="crm-card" style="flex: 1 1 280px">
                <div class="crm-dashlet-head"><i class="fas fa-users"></i><h2>الموظفين ({{ $staff->count() }})</h2></div>
                <ul style="list-style: none; margin: 0; padding: 0; max-height: 560px; overflow-y: auto">
                    @forelse($staff as $u)
                        @php $n = count($overrides[$u->id] ?? []); @endphp
                        <li style="border-bottom: 1px solid #E5E7EB">
                            <a href="{{ route('admin.crm.settings.index', ['tab' => 'users', 'user' => $u->id]) }}" style="display: flex; align-items: center; gap: 8px; padding: 9px 14px; text-decoration: none; color: inherit; {{ $selected?->id === $u->id ? 'background: #F3F1F7; font-weight: 700' : '' }}">
                                <span style="flex: 1">{{ $u->name }}<span class="crm-muted" style="display: block; font-size: 11px; font-weight: 400">{{ $u->hasSalesManagerPortalAccess() ? 'مدير' : 'موظف' }}</span></span>
                                @if($n)<span class="crm-chip" style="height: 22px; cursor: pointer">{{ $n }} استثناء</span>@endif
                            </a>
                        </li>
                    @empty
                        <li class="crm-muted" style="padding: 14px">مفيش موظفين مبيعات.</li>
                    @endforelse
                </ul>
            </section>

            <section class="crm-card" style="flex: 999 1 520px; min-width: 0">
                @if($selected)
                    <form method="post" action="{{ route('admin.crm.settings.users.update', $selected->id) }}">
                        @csrf @method('PUT')
                        <div class="crm-dashlet-head"><i class="fas fa-user-gear"></i><h2>{{ $selected->name }} — {{ $roleLabel($selectedRole) }}</h2></div>
                        <div style="overflow-x: auto">
                            <table class="crm-table" style="min-width: 560px">
                                <thead><tr><th>الوحدة / الإجراء</th><th>حسب الدور</th><th>للموظف ده</th></tr></thead>
                                <tbody>
                                    @foreach(CrmPermissions::MODULES as $module => $m)
                                        @php $actions = array_filter($m['actions'], fn ($a, $action) => CrmPermissions::applies($selectedRole, $module.'.'.$action), ARRAY_FILTER_USE_BOTH); @endphp
                                        @continue($actions === [])
                                        <tr style="background: #EEF0F2"><td colspan="3" style="font-weight: 700"><i class="fas {{ $m['icon'] }}" style="color: #534D64"></i> {{ $m['label'] }}</td></tr>
                                        @foreach($actions as $action => $a)
                                            @php
                                                $key = $module.'.'.$action;
                                                $roleValue = $matrix[$selectedRole][$key] ?? false;
                                                $own = $overrides[$selected->id][$key] ?? null;
                                            @endphp
                                            <tr>
                                                <td style="padding-inline-start: 26px">{{ $a['label'] }}</td>
                                                <td class="{{ $roleValue ? 'crm-good' : 'crm-bad' }}">{{ $roleValue ? 'مسموح' : 'ممنوع' }}</td>
                                                <td>
                                                    <select class="crm-select" name="perm[{{ SalesCrmSettingsController::field($key) }}]" aria-label="{{ $m['label'] }} — {{ $a['label'] }}" style="{{ $own !== null ? 'border-color: #B8492F; font-weight: 700' : '' }}">
                                                        <option value="" @selected($own === null)>زي الدور</option>
                                                        <option value="allow" @selected($own === true)>مسموح</option>
                                                        <option value="deny" @selected($own === false)>ممنوع</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 12px 14px; border-top: 1px solid #E5E7EB">
                            <button type="submit" class="crm-btn crm-btn-primary">حفظ الاستثناءات</button>
                            <button type="submit" name="reset" value="1" class="crm-btn crm-btn-outline">إلغاء كل الاستثناءات</button>
                            <span class="crm-muted" style="font-size: 12px">الاستثناء بيغلب صلاحية الدور للموظف ده بس. نطاق بياناته ما بيتغيرش.</span>
                        </div>
                    </form>
                @else
                    <p class="crm-muted" style="margin: 0; padding: 28px 14px; text-align: center">اختار موظف من القائمة عشان تدّيله صلاحية زيادة أو تقفل عنه حاجة، من غير ما تغيّر صلاحيات دوره.</p>
                @endif
            </section>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px">
            @foreach($setup as $g)
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas {{ $g['icon'] }}"></i><h2>{{ $g['label'] }}</h2></div>
                    <ul style="list-style: none; margin: 0; padding: 6px 0">
                        @foreach($g['links'] as $l)<li><a class="crm-link" href="{{ $l['url'] }}" style="display: block; padding: 7px 14px; font-size: 13px">{{ $l['label'] }}</a></li>@endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
