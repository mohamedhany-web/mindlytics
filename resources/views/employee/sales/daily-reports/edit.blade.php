@extends('layouts.employee')

@section('title', 'تعبئة التقرير اليومي')
@section('header', 'تعبئة التقرير اليومي — '.$date->format('Y-m-d'))

@push('styles')
<style>
    .dr-panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
    .dr-input { border:1px solid #cbd5e1; border-radius:8px; width:100%; }
    .dr-input:focus { outline:none; border-color:#64748b; box-shadow:0 0 0 2px rgba(100,116,139,.12); }
    .dr-auto { background:#f8fafc; }
    .dr-manual { background:#fffbeb; border-color:#fde68a; }
    .kpi-met { color:#047857; }
    .kpi-near { color:#b45309; }
    .kpi-behind { color:#be123c; }
</style>
@endpush

@section('content')
@php
    $r = $report;
    $existingContacts = old('contacts');
    if ($existingContacts === null) {
        if ($r?->contacts?->isNotEmpty()) {
            $existingContacts = $r->contacts->map(fn ($c) => [
                'sales_lead_id' => (string) ($c->sales_lead_id ?? ''),
                'contact_name' => $c->contact_name,
                'contact_phone' => $c->contact_phone,
                'interaction_type' => $c->interaction_type,
                'client_status' => $c->client_status,
                'client_problems' => $c->client_problems,
            ])->values()->all();
        } elseif (! empty($suggestedContacts)) {
            $existingContacts = collect($suggestedContacts)->map(fn ($c) => [
                'sales_lead_id' => (string) ($c['sales_lead_id'] ?? ''),
                'contact_name' => $c['contact_name'] ?? '',
                'contact_phone' => $c['contact_phone'] ?? '',
                'interaction_type' => $c['interaction_type'] ?? 'call',
                'client_status' => $c['client_status'] ?? '',
                'client_problems' => $c['client_problems'] ?? '',
            ])->values()->all();
        } else {
            $existingContacts = [];
        }
    }
    $todayLeadsJson = collect($todayLeads ?? [])->map(fn ($c) => [
        'sales_lead_id' => (string) ($c['sales_lead_id'] ?? ''),
        'contact_name' => $c['contact_name'] ?? '',
        'contact_phone' => $c['contact_phone'] ?? '',
        'interaction_type' => $c['interaction_type'] ?? 'call',
        'client_status' => $c['client_status'] ?? '',
        'client_problems' => $c['client_problems'] ?? '',
        'activity_label' => $c['activity_label'] ?? '',
    ])->values();
    $kpi = $kpiComparison ?? ['status' => 'behind', 'status_label' => '', 'overall_pct' => 0, 'lines' => []];
    $kpiClass = match ($kpi['status'] ?? '') {
        'met' => 'kpi-met',
        'near' => 'kpi-near',
        default => 'kpi-behind',
    };
@endphp

<div class="space-y-4" x-data="dailyReportForm(@js($existingContacts), @js($leads->map(fn($l) => ['id' => (string)$l->id, 'name' => $l->name, 'phone' => $l->phone, 'stage' => $l->stage])->values()), @js($todayLeadsJson))">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">تقرير {{ $date->format('Y-m-d') }}</h2>
            <p class="text-sm text-slate-500">آخر موعد للتسليم: {{ $settings['deadline_time'] ?? '23:59' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales.daily-reports.index', ['date' => $date->toDateString()]) }}" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700">العودة</a>
            <form method="post" action="{{ route('employee.sales.daily-reports.sync-auto') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                <button type="submit" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50"><i class="fas fa-sync ml-1"></i> تحديث من النشاط</button>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
        {{-- KPI sidebar --}}
        <aside class="xl:col-span-3 space-y-4 xl:sticky xl:top-4">
            <div class="dr-panel p-4">
                <h3 class="font-bold text-slate-900 text-sm mb-2"><i class="fas fa-bullseye ml-1"></i> مقارنة KPI اليوم</h3>
                <p class="text-2xl font-black {{ $kpiClass }}">{{ $kpi['overall_pct'] ?? 0 }}%</p>
                <p class="text-xs font-semibold {{ $kpiClass }} mt-1">{{ $kpi['status_label'] ?? '' }}</p>
                <ul class="mt-4 space-y-2 text-xs">
                    @foreach($kpi['lines'] ?? [] as $line)
                        @php $lc = match($line['status']){ 'met'=>'text-emerald-700','near'=>'text-amber-700',default=>'text-rose-700' }; @endphp
                        <li class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                            <span class="text-slate-600">{{ $line['label'] }}</span>
                            <span class="font-bold {{ $lc }}">{{ $line['actual'] }}/{{ $line['target'] }} ({{ $line['pct'] }}%)</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @php $sos = $dailyResults ?? null; @endphp
            @if($sos)
            <div class="dr-panel p-4">
                <h3 class="font-bold text-slate-900 text-sm mb-1"><i class="fas fa-chart-line ml-1"></i> نتائج اليوم مقابل الهدف</h3>
                <p class="text-[11px] text-slate-500 mb-2">قراءة فقط من CRM (مكالمات بنتائج + مراحل)</p>
                <p class="text-xl font-black {{ match($sos['status'] ?? '') { 'met' => 'kpi-met', 'near' => 'kpi-near', default => 'kpi-behind' } }}">{{ $sos['overall_pct'] ?? 0 }}%</p>
                <ul class="mt-3 space-y-2 text-xs">
                    @foreach($sos['lines'] ?? [] as $line)
                        @php $lc = match($line['status']){ 'met'=>'text-emerald-700','near'=>'text-amber-700',default=>'text-rose-700' }; @endphp
                        <li class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                            <span class="text-slate-600">{{ $line['label'] }}</span>
                            <span class="font-bold {{ $lc }}">{{ $line['actual'] }}/{{ (int) $line['target'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="dr-panel p-4 text-xs text-slate-700 space-y-2">
                <p class="font-bold text-slate-900">ما يُحسب تلقائياً</p>
                <ul class="list-disc list-inside space-y-1 text-slate-600">
                    <li>أرقام الإنتاجية من CRM</li>
                    <li>تفاصيل المكالمة/الاجتماع (اسم، رقم، مرحلة)</li>
                    <li>التأهيل والحجوزات من تغيير المرحلة</li>
                </ul>
                <p class="font-bold text-amber-800 pt-2">يجب كتابته يدوياً</p>
                <ul class="list-disc list-inside space-y-1 text-amber-900/90">
                    <li><strong>مشاكل/احتياجات العميل</strong> لكل مكالمة/اجتماع</li>
                    <li><strong>ملاحظات النشاط</strong> لأحداث لا يُسجّلها النظام</li>
                    <li><strong>ملاحظات الإنتاجية</strong> عند وجود ظروف خاصة</li>
                </ul>
            </div>
        </aside>

        <div class="xl:col-span-9">
            <form method="post" action="{{ route('employee.sales.daily-reports.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="report_date" value="{{ $date->toDateString() }}">

                <section class="dr-panel dr-auto p-5">
                    <h2 class="font-bold text-slate-900 mb-1">١ — نشاط اليوم <span class="text-xs font-normal text-slate-500">(تلقائي)</span></h2>
                    <div class="grid sm:grid-cols-3 gap-3 mt-3">
                        @foreach(['messages_replied' => 'ردود رسائل', 'leads_qualified' => 'عملاء مؤهّلون', 'bookings_from_leads' => 'حجوزات'] as $key => $label)
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                                <input type="number" min="0" name="{{ $key }}" value="{{ old($key, $r?->$key ?? 0) }}" required class="dr-input px-3 py-2 text-sm">
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 dr-manual rounded-lg border p-3">
                        <label class="block text-xs font-bold text-amber-900 mb-1">ملاحظات النشاط — اكتب ما لا يُسجّل تلقائياً</label>
                        <textarea name="activity_notes" rows="3" class="dr-input px-3 py-2 text-sm" placeholder="مثال: عميل طلب عرض خاص، متابعة مع الإدارة…">{{ old('activity_notes', $r?->activity_notes) }}</textarea>
                    </div>
                </section>

                <section class="dr-panel dr-auto p-5">
                    <h2 class="font-bold text-slate-900 mb-1">٢ — الإنتاجية <span class="text-xs font-normal text-slate-500">(تلقائي)</span></h2>
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3 mt-3">
                        @foreach(['numbers_worked' => 'أرقام', 'followups_done' => 'متابعات', 'calls_made' => 'مكالمات', 'meetings_held' => 'اجتماعات', 'calls_answered' => 'ردود مكالمات'] as $key => $label)
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                                <input type="number" min="0" name="{{ $key }}" value="{{ old($key, $r?->$key ?? 0) }}" required class="dr-input px-3 py-2 text-sm">
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 dr-manual rounded-lg border p-3">
                        <label class="block text-xs font-bold text-amber-900 mb-1">ملاحظات الإنتاجية — اختياري</label>
                        <textarea name="productivity_notes" rows="2" class="dr-input px-3 py-2 text-sm">{{ old('productivity_notes', $r?->productivity_notes) }}</textarea>
                    </div>
                </section>

                <section class="dr-panel p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div>
                            <h2 class="font-bold text-slate-900">٣ — تفاصيل المكالمات والاجتماعات</h2>
                            <p class="text-xs text-slate-500 mt-1">اختر من <strong>عملاء اليوم</strong> — البيانات تُملأ وحدك — أكمل <strong class="text-amber-800">المشاكل/الاحتياجات</strong> فقط</p>
                        </div>
                    </div>

                    {{-- عملاء اليوم --}}
                    <div class="mb-4 rounded-lg border border-sky-200 bg-sky-50/60 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <p class="text-sm font-bold text-sky-900">
                                <i class="fas fa-users ml-1"></i>
                                عملاء تواصلت معهم اليوم ({{ count($todayLeads ?? []) }})
                            </p>
                            <button type="button" x-show="todayLeads.length" @click="addAllTodayLeads()" class="text-xs px-3 py-1.5 rounded-lg bg-sky-800 text-white font-semibold">
                                إضافة الكل
                            </button>
                        </div>
                        @if(empty($todayLeads))
                            <p class="text-xs text-slate-600">لا يوجد نشاط مسجّل لهذا اليوم — سجّل مكالمة/واتساب/متابعة من صفحة العملاء ثم اضغط «تحديث من النشاط».</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                <template x-for="tl in todayLeads" :key="tl.sales_lead_id">
                                    <button type="button"
                                            @click="addFromTodayLead(tl)"
                                            class="text-xs px-3 py-2 rounded-lg border transition-colors"
                                            :class="hasContact(tl.sales_lead_id) ? 'border-emerald-400 bg-emerald-50 text-emerald-800' : 'border-sky-300 bg-white text-sky-900 hover:bg-sky-100'">
                                        <span x-text="tl.contact_name"></span>
                                        <span class="opacity-70" x-text="' · ' + (tl.activity_label || '')"></span>
                                        <span x-show="hasContact(tl.sales_lead_id)" class="mr-1">✓</span>
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>

                    <template x-for="(row, index) in contacts" :key="'c-'+index+'-'+(row.sales_lead_id||index)">
                        <div class="border rounded-lg p-4 mb-3" :class="(!row.client_problems || row.client_problems.trim()==='') ? 'dr-manual' : 'border-slate-200 bg-slate-50/50'">
                            <input type="hidden" :name="'contacts['+index+'][interaction_type]'" x-model="row.interaction_type">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-700">
                                    <span x-text="row.interaction_type === 'meeting' ? 'اجتماع' : 'مكالمة/تواصل'"></span>
                                    <span x-show="row.contact_name" class="text-slate-500 font-normal" x-text="' — ' + row.contact_name"></span>
                                </span>
                                <button type="button" @click="contacts.splice(index, 1)" class="text-rose-600 text-xs">حذف</button>
                            </div>
                            <input type="hidden" :name="'contacts['+index+'][sales_lead_id]'" :value="row.sales_lead_id || ''">
                            <input type="hidden" :name="'contacts['+index+'][contact_name]'" :value="row.contact_name || ''">
                            <input type="hidden" :name="'contacts['+index+'][contact_phone]'" :value="row.contact_phone || ''">
                            <input type="hidden" :name="'contacts['+index+'][client_status]'" :value="row.client_status || ''">
                            <div class="grid md:grid-cols-2 gap-3 mb-3 text-sm dr-auto rounded-lg p-3 border border-slate-100">
                                <div><span class="text-xs text-slate-500">العميل</span><p class="font-semibold" x-text="row.contact_name || '—'"></p></div>
                                <div><span class="text-xs text-slate-500">الهاتف</span><p class="font-semibold" dir="ltr" x-text="row.contact_phone || '—'"></p></div>
                                <div class="md:col-span-2"><span class="text-xs text-slate-500">الحالة (تلقائي)</span><p class="text-xs mt-0.5" x-text="row.client_status || '—'"></p></div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">مشاكل / احتياجات العميل <span class="text-red-500">*</span> — اكتبها أنت</label>
                                <textarea rows="2" class="dr-input text-sm py-2 px-3 mt-1" :class="(!row.client_problems || row.client_problems.trim()==='') ? 'border-amber-400 bg-amber-50/30' : ''" :name="'contacts['+index+'][client_problems]'" x-model="row.client_problems" required placeholder="احتياجات العميل وملاحظاتك…"></textarea>
                            </div>
                        </div>
                    </template>
                    <p x-show="contacts.length === 0 && todayLeads.length === 0" class="text-sm text-slate-500">لا يوجد عملاء لليوم — سجّل نشاطك أولاً.</p>
                    <p x-show="contacts.length === 0 && todayLeads.length > 0" class="text-sm text-sky-800 font-medium">اضغط على عميل من القائمة أعلاه — أو «إضافة الكل».</p>
                </section>

                @if(($campaigns ?? collect())->isNotEmpty())
                <section class="dr-panel p-5">
                    <div class="mb-3">
                        <h2 class="font-bold text-slate-900">٤ — الحملات الإعلانية <span class="text-xs font-normal text-slate-500">(تُملأ يومياً)</span></h2>
                        <p class="text-xs text-slate-500 mt-1">سجّل نتائج اليوم لكل حملة أُسندت إليك — الرسائل الجديدة، القنوات، والتأهيل.</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($campaigns as $campaign)
                            @php $entry = ($campaignEntries ?? collect())->get($campaign->id); @endphp
                            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm"><i class="fas fa-bullhorn ml-1 text-indigo-500"></i> {{ $campaign->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $campaign->platformLabel() }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($campaignFieldLabels as $key => $label)
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                                            <input type="number" min="0"
                                                   name="campaigns[{{ $campaign->id }}][{{ $key }}]"
                                                   value="{{ old('campaigns.'.$campaign->id.'.'.$key, $entry?->$key ?? 0) }}"
                                                   class="dr-input px-3 py-2 text-sm">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات عن اليوم — لهذه الحملة</label>
                                    <textarea name="campaigns[{{ $campaign->id }}][notes]" rows="2" class="dr-input px-3 py-2 text-sm" placeholder="ملاحظاتك عن أداء الحملة اليوم…">{{ old('campaigns.'.$campaign->id.'.notes', $entry?->notes) }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <div class="flex flex-wrap gap-3 sticky bottom-0 bg-white/95 backdrop-blur border-t border-slate-200 py-3 -mx-1 px-1">
                    <button type="submit" name="action" value="draft" class="px-5 py-2.5 border border-slate-300 rounded-lg font-semibold text-sm">حفظ مسودة</button>
                    <button type="submit" name="action" value="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-sm">تسليم نهائي</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function dailyReportForm(initialContacts, leads, todayLeads) {
    return {
        leads,
        todayLeads: todayLeads || [],
        contacts: initialContacts.length ? initialContacts : [],
        init() {
            this.contacts.forEach(c => {
                if (c.sales_lead_id != null && c.sales_lead_id !== '') {
                    c.sales_lead_id = String(c.sales_lead_id);
                }
            });
            if (this.contacts.length === 0 && this.todayLeads.length > 0) {
                this.todayLeads.forEach(tl => this.addFromTodayLead(tl));
            }
        },
        hasContact(leadId) {
            if (!leadId) return false;
            return this.contacts.some(c => String(c.sales_lead_id) === String(leadId));
        },
        addFromTodayLead(tl) {
            if (this.hasContact(tl.sales_lead_id)) return;
            this.contacts.push({
                sales_lead_id: String(tl.sales_lead_id || ''),
                contact_name: tl.contact_name || '',
                contact_phone: tl.contact_phone || '',
                interaction_type: tl.interaction_type || 'call',
                client_status: tl.client_status || '',
                client_problems: tl.client_problems || '',
            });
        },
        addAllTodayLeads() {
            this.todayLeads.forEach(tl => this.addFromTodayLead(tl));
        },
    };
}
</script>
@endpush
@endsection
