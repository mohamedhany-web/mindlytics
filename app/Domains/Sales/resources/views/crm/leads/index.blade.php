@extends('sales::crm.layout')

@section('title', 'العملاء المحتملون')

@use('App\Domains\Sales\Models\SalesLead')
@php
    $indexRoute = $crm->route('leads.index');
    $q = fn (array $extra) => route($indexRoute, array_filter(array_merge(request()->except('page'), $extra), fn ($v) => $v !== null && $v !== ''));
    $sortLink = fn (string $col) => $q(['sort' => $col, 'dir' => ($sort === $col && $dir === 'asc') ? 'desc' : 'asc']);
    $quicks = array_filter([
        'all' => [$crm->isRep() ? 'كل عملائي' : ($crm->isAdmin() ? 'الكل' : 'كل الفريق'), null],
        'today' => ['متابعات النهارده', $quickCounts['today']],
        'overdue' => ['متأخر', $quickCounts['overdue']],
        'new' => ['جدد', $quickCounts['new']],
        'stale' => ['بلا حركة 10 أيام', $quickCounts['stale']],
        'meta' => ['إعلانات Meta', null],
        'unassigned' => $crm->isAdmin() ? ['بلا موظف', $quickCounts['unassigned']] : null,
    ]);
    $columns = ['stage' => 'الحالة', 'interest' => 'الاهتمام', 'phone' => 'الهاتف', 'source' => 'المصدر', 'follow' => 'المتابعة التالية', 'owner' => 'الموظف', 'updated' => 'آخر تحديث'];
@endphp

@section('content')
<div x-data="crmLeads()" x-init="init()" style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">العملاء المحتملون</h1>
        <div style="display: flex; gap: 6px">
            <button type="button" class="crm-btn crm-btn-slate" @click="filter = ! filter" :aria-expanded="filter">فلتر</button>
            <button type="button" class="crm-btn crm-btn-dark" @click="toggleInsights()" :aria-pressed="insights"><i class="fas fa-chart-pie"></i> Insights</button>
        </div>
    </div>

    <form method="get" action="{{ route($indexRoute) }}" class="crm-card" x-cloak x-show="filter" style="padding: 14px">
        <input type="hidden" name="quick" value="{{ $filters['quick'] }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px">
            <label class="crm-label">بحث (اسم، رقم، إيميل، رقم طلب)<input class="crm-input" type="search" name="q" value="{{ $filters['q'] }}"></label>
            <label class="crm-label">المرحلة
                <select class="crm-select" name="stage"><option value="">الكل</option>@foreach(SalesLead::STAGES as $k => $v)<option value="{{ $k }}" @selected($filters['stage'] === $k)>{{ $v }}</option>@endforeach</select>
            </label>
            <label class="crm-label">المصدر
                <select class="crm-select" name="source"><option value="">الكل</option>@foreach(SalesLead::SOURCES as $k => $v)<option value="{{ $k }}" @selected($filters['source'] === $k)>{{ $v }}</option>@endforeach</select>
            </label>
            <label class="crm-label">الاهتمام
                <select class="crm-select" name="interest_type_id"><option value="">الكل</option>@foreach($interestTypes as $it)<option value="{{ $it->id }}" @selected((int) $filters['interest_type_id'] === (int) $it->id)>{{ $it->name_ar }}</option>@endforeach</select>
            </label>
            @if($members->isNotEmpty())
                <label class="crm-label">الموظف
                    <select class="crm-select" name="assigned_to"><option value="">الكل</option>@foreach($members as $m)<option value="{{ $m->id }}" @selected((int) $filters['assigned_to'] === (int) $m->id)>{{ $m->name }}</option>@endforeach</select>
                </label>
            @endif
        </div>
        <div style="display: flex; gap: 6px; justify-content: flex-end; margin-top: 12px">
            <a href="{{ route($indexRoute) }}" class="crm-btn crm-btn-outline">مسح</a>
            <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
        </div>
    </form>

    <nav aria-label="فلاتر سريعة" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 13px">
        <span class="crm-muted">فلاتر سريعة:</span>
        @foreach($quicks as $key => [$label, $count])
            <a href="{{ $q(['quick' => $key, 'page' => null]) }}" class="crm-chip {{ $filters['quick'] === $key ? 'is-active' : '' }}" @if($key === 'overdue' && $filters['quick'] !== $key) style="color: #B42318" @endif>{{ $label }}@if($count !== null)<span class="n">{{ $count }}</span>@endif</a>
        @endforeach
    </nav>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <section class="crm-card" style="flex: 999 1 720px; min-width: 0">
            <form method="post" action="{{ route($crm->route('leads.bulk')) }}" id="crm-bulk-form">
                @csrf
                <div class="crm-toolbar">
                    <label style="display: flex; align-items: center; height: 28px; padding: 0 6px; background: #fff; border-radius: 3px"><span class="crm-sr">تحديد كل الصفحة</span><input type="checkbox" @change="toggleAll($event.target.checked)"></label>
                    <div style="position: relative" @click.outside="bulk = false">
                        <button type="button" class="crm-btn crm-btn-muted crm-btn-sm" @click="bulk = ! bulk" :disabled="selected === 0" :style="selected === 0 ? 'opacity:.6' : ''">إجراء جماعي <span x-text="selected ? '(' + selected + ')' : ''"></span> <i class="fas fa-caret-down"></i></button>
                        <div x-cloak x-show="bulk" class="crm-card" style="position: absolute; top: 32px; right: 0; z-index: 20; min-width: 260px; padding: 10px; display: flex; flex-direction: column; gap: 8px; color: #212529">
                            <button type="submit" name="action" value="export" class="crm-btn crm-btn-outline" style="justify-content: flex-start">تصدير المحددين (CSV)</button>
                            @if($crm->canManage() && $members->isNotEmpty())
                                <label class="crm-label">تحويل المحددين لـ
                                    <select class="crm-select" name="to_user_id"><option value="">— اختر —</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select>
                                </label>
                                <input class="crm-input" type="text" name="reason" maxlength="500" placeholder="سبب التحويل (اختياري)" aria-label="سبب التحويل">
                                <button type="submit" name="action" value="transfer" class="crm-btn crm-btn-primary">تحويل</button>
                            @endif
                        </div>
                    </div>
                    <div style="position: relative" @click.outside="cols = false">
                        <button type="button" class="crm-btn crm-btn-muted crm-btn-sm" aria-label="اختيار الأعمدة" @click="cols = ! cols"><i class="fas fa-table-columns"></i></button>
                        <div x-cloak x-show="cols" class="crm-card" style="position: absolute; top: 32px; right: 0; z-index: 20; min-width: 200px; padding: 10px; display: flex; flex-direction: column; gap: 6px; color: #212529; font-size: 13px">
                            <strong style="font-size: 12px">الأعمدة الظاهرة</strong>
                            @foreach($columns as $key => $label)
                                <label style="display: flex; align-items: center; gap: 6px"><input type="checkbox" :checked="show('{{ $key }}')" @change="setCol('{{ $key }}', $event.target.checked)"> {{ $label }}</label>
                            @endforeach
                        </div>
                    </div>
                    <span style="flex: 1"></span>
                    @include('sales::crm.partials.pager', ['paginator' => $leads])
                </div>
                <div style="overflow-x: auto">
                    <table class="crm-table" style="min-width: 860px">
                        <thead>
                            <tr>
                                <th style="width: 30px"><span class="crm-sr">تحديد</span></th>
                                <th><a href="{{ $sortLink('name') }}">الاسم ⇅</a></th>
                                <th x-show="show('stage')"><a href="{{ $sortLink('stage') }}">الحالة ⇅</a></th>
                                <th x-show="show('interest')">الاهتمام</th>
                                <th x-show="show('phone')">الهاتف</th>
                                <th x-show="show('source')"><a href="{{ $sortLink('source') }}">المصدر ⇅</a></th>
                                <th x-show="show('follow')"><a href="{{ $sortLink('next_follow_up_at') }}">المتابعة التالية ⇅</a></th>
                                <th x-show="show('owner')">الموظف</th>
                                <th x-show="show('updated')"><a href="{{ $sortLink('updated_at') }}">آخر تحديث ⇅</a></th>
                                <th><span class="crm-sr">إجراءات سريعة</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                @php($late = $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() && $lead->isOpen())
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $lead->id }}" @change="count()" aria-label="تحديد {{ $lead->name }}"></td>
                                    <td><a class="crm-link" href="{{ route($crm->route('leads.show'), $lead->id) }}">{{ $lead->name }}</a></td>
                                    <td x-show="show('stage')">{{ SalesLead::stageLabel((string) $lead->stage) }}</td>
                                    <td x-show="show('interest')">{{ $lead->interestType?->name_ar ?? '—' }}</td>
                                    <td x-show="show('phone')" dir="ltr" style="text-align: end"><a class="crm-link" href="tel:{{ preg_replace('/[^\d+]/', '', (string) $lead->phone) }}">{{ $lead->phone }}</a></td>
                                    <td x-show="show('source')">{{ SalesLead::sourceLabel((string) $lead->source) }}</td>
                                    <td x-show="show('follow')" class="{{ $late ? 'crm-bad' : '' }}">{{ $lead->next_follow_up_at ? ($lead->next_follow_up_at->isToday() ? 'النهارده '.$lead->next_follow_up_at->format('H:i') : $lead->next_follow_up_at->format('d/m H:i')) : '—' }}{{ $late ? ' (متأخر)' : '' }}</td>
                                    <td x-show="show('owner')">{{ $lead->assignee?->name ?? '—' }}</td>
                                    <td x-show="show('updated')" class="crm-muted">{{ $lead->updated_at?->diffForHumans() }}</td>
                                    <td style="white-space: nowrap">
                                        <a href="tel:{{ preg_replace('/[^\d+]/', '', (string) $lead->phone) }}" aria-label="اتصال بـ {{ $lead->name }}" style="color: #627587; padding: 0 4px"><i class="fas fa-phone"></i></a>
                                        @if($wa = \App\Domains\Sales\Support\PhoneKey::whatsappUrl($lead->phone))<a href="{{ $wa }}" target="_blank" rel="noopener" aria-label="واتساب {{ $lead->name }}" style="color: #627587; padding: 0 4px"><i class="fab fa-whatsapp"></i></a>@endif
                                        <a href="{{ route($crm->route('leads.show'), $lead->id) }}" aria-label="عرض {{ $lead->name }}" style="color: #627587; padding: 0 4px"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="crm-muted" style="text-align: center; padding: 22px">مفيش عملاء بالفلاتر دي.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </section>

        <aside class="crm-card" x-show="insights" style="flex: 1 1 280px; padding: 14px; display: flex; flex-direction: column; gap: 12px">
            <h2 style="margin: 0; font-size: 14px; font-weight: 700">رسوم سريعة — العملاء حسب الحالة</h2>
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; justify-items: center; text-align: center; font-size: 12px">
                @foreach($charts as $c)
                    <a href="{{ $q(['stage' => null, 'page' => null]) }}" style="display: flex; flex-direction: column; align-items: center; gap: 4px; color: inherit; text-decoration: none">
                        <span style="width: 84px; height: 84px; border-radius: 99px; background: conic-gradient({{ $c['color'] }} 0 {{ $c['pct'] }}%, #E5E7EB {{ $c['pct'] }}% 100%); display: flex; align-items: center; justify-content: center">
                            <span style="width: 68px; height: 68px; border-radius: 99px; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 700">{{ rtrim(rtrim(number_format($c['pct'], 1), '0'), '.') }}٪</span>
                        </span>
                        <span>{{ $c['label'] }}</span><span class="crm-muted">{{ $c['count'] }}</span>
                    </a>
                @endforeach
            </div>
            <p class="crm-muted" style="margin: 0; font-size: 12px">الرسم بيتحدّث مع الفلتر والفلاتر السريعة.</p>
        </aside>
    </div>
</div>

@push('scripts')
<script>
    function crmLeads() {
        const KEY = 'crm.leads.columns';
        const read = (k, d) => { try { const v = JSON.parse(localStorage.getItem(k)); return v ?? d; } catch (e) { return d; } };
        const write = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} };
        return {
            filter: {{ ($filters['q'] !== '' || $filters['stage'] || $filters['source'] || $filters['interest_type_id'] || $filters['assigned_to']) ? 'true' : 'false' }},
            insights: true, bulk: false, cols: false, selected: 0, hidden: [],
            init() { this.hidden = read(KEY, ['updated']); this.insights = read('crm.leads.insights', true); },
            show(k) { return ! this.hidden.includes(k); },
            setCol(k, on) { this.hidden = on ? this.hidden.filter(x => x !== k) : [...this.hidden, k]; write(KEY, this.hidden); },
            toggleInsights() { this.insights = ! this.insights; write('crm.leads.insights', this.insights); },
            count() { this.selected = document.querySelectorAll('#crm-bulk-form input[name="ids[]"]:checked').length; },
            toggleAll(on) { document.querySelectorAll('#crm-bulk-form input[name="ids[]"]').forEach(c => c.checked = on); this.count(); },
        };
    }
</script>
@endpush
@endsection
