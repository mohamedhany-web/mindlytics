@extends('layouts.employee')

@section('title', 'العملاء المحتملون')
@section('header', 'العملاء المحتملون')

@push('styles')
<style>
    .leads-panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
    .leads-panel input, .leads-panel select {
        border: 1px solid #cbd5e1; border-radius: 6px;
    }
    .leads-panel input:focus, .leads-panel select:focus {
        outline: none; border-color: #64748b;
        box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.12);
    }
    .preset-link {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600;
        border: 1px solid #e2e8f0; color: #475569; background: #fff;
    }
    .preset-link.active { background: #1e293b; color: #fff; border-color: #1e293b; }
    .preset-link .cnt { font-size: 0.7rem; opacity: 0.85; font-weight: 700; }
    .act-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 6px; border: 1px solid #e2e8f0;
        color: #475569; background: #fff; font-size: 0.8rem;
    }
    .act-btn:hover { background: #f8fafc; border-color: #94a3b8; }
</style>
@endpush

@section('content')
@php
    $req = request();
    $activePreset = 'all';
    if ($req->boolean('stale')) {
        $activePreset = 'stale';
    } elseif ($req->get('follow_up') === 'today') {
        $activePreset = 'today';
    } elseif ($req->get('follow_up') === 'overdue') {
        $activePreset = 'overdue';
    } elseif ($req->get('stage') === 'new' && ! $req->hasAny(['follow_up', 'priority', 'search', 'category_id'])) {
        $activePreset = 'new';
    }

    $presets = [
        'all' => ['label' => 'الكل', 'url' => route('employee.sales.leads.index'), 'count' => null],
        'today' => ['label' => 'متابعات اليوم', 'url' => route('employee.sales.leads.index', ['follow_up' => 'today', 'sort' => 'follow_up']), 'count' => $quickCounts['today'] ?? 0],
        'overdue' => ['label' => 'متأخرة', 'url' => route('employee.sales.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up']), 'count' => $quickCounts['overdue'] ?? 0],
        'stale' => ['label' => 'بلا تواصل', 'url' => route('employee.sales.leads.index', ['stale' => 1, 'sort' => 'last_contact']), 'count' => $quickCounts['stale'] ?? 0],
        'new_lead' => ['label' => 'New Lead', 'url' => route('employee.sales.leads.index', ['stage' => 'new_lead']), 'count' => $quickCounts['new'] ?? 0],
    ];

    $redirectTo = url()->full();

    $waPhone = function (?string $phone): ?string {
        if (! $phone) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '20'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '20')) {
            $digits = '20'.$digits;
        }

        return 'https://wa.me/'.$digits;
    };
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">العملاء المحتملون</h2>
            <p class="text-sm text-slate-500 mt-0.5">فلاتر فورية — إجراءات من الجدول مباشرة</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">مركز المبيعات</a>
            <a href="{{ route('employee.sales.leads.create') }}"
               class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900">
                + عميل جديد
            </a>
        </div>
    </div>

    {{-- فلاتر يومية بنقرة واحدة --}}
    <div class="flex flex-wrap gap-2">
        @foreach($presets as $key => $preset)
            <a href="{{ $preset['url'] }}"
               class="preset-link {{ $activePreset === $key ? 'active' : '' }}">
                {{ $preset['label'] }}
                @if($preset['count'] !== null)
                    <span class="cnt">({{ $preset['count'] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- تصفية — تطبيق تلقائي عند التغيير --}}
    <div class="leads-panel p-4" x-data="{ showMore: {{ $req->hasAny(['category_id','import_batch','stage','priority','stale','group_id','source','origin','interest_type_id']) && ! in_array($activePreset, ['today','overdue','stale','new']) ? 'true' : 'false' }} }">
        <form method="get" id="leads-filter-form" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5 lg:col-span-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1">بحث</label>
                    <input type="search" name="search" value="{{ $req->search }}"
                           placeholder="اسم، هاتف، بريد..."
                           class="w-full px-3 py-2 text-sm"
                           @keydown.enter.prevent="$el.form.submit()">
                </div>
                <div class="md:col-span-3 lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">المتابعة</label>
                    <select name="follow_up" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="overdue" @selected($req->follow_up === 'overdue')>متأخرة</option>
                        <option value="today" @selected($req->follow_up === 'today')>اليوم</option>
                        <option value="week" @selected($req->follow_up === 'week')>خلال أسبوع</option>
                        <option value="none" @selected($req->follow_up === 'none')>بدون موعد</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">ترتيب</label>
                    <select name="sort" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="" @selected(! $req->sort)>آخر تحديث</option>
                        <option value="follow_up" @selected($req->sort === 'follow_up')>أقرب متابعة</option>
                        <option value="priority" @selected($req->sort === 'priority')>الأولوية</option>
                        <option value="last_contact" @selected($req->sort === 'last_contact')>آخر تواصل</option>
                        <option value="value" @selected($req->sort === 'value')>القيمة</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">بحث</button>
                    @if($req->hasAny(['stage','priority','follow_up','sort','stale','search','category_id','import_batch','group_id','source','origin','interest_type_id']))
                        <a href="{{ route('employee.sales.leads.index') }}" class="px-3 py-2 text-sm text-slate-600">مسح</a>
                    @endif
                    <button type="button" @click="showMore = !showMore" class="px-3 py-2 text-sm text-slate-600 border border-slate-200 rounded-lg">
                        المزيد
                    </button>
                </div>
            </div>

            <div x-show="showMore" x-cloak class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">المرحلة</label>
                    <select name="stage" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                            <option value="{{ $k }}" @selected($req->stage === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">الأولوية</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
                            <option value="{{ $k }}" @selected($req->priority === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">التصنيف</label>
                    <select name="category_id" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($req->category_id == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">الاهتمام</label>
                    <select name="interest_type_id" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach($interestTypes ?? [] as $itype)
                            <option value="{{ $itype->id }}" @selected($req->interest_type_id == $itype->id)>{{ $itype->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">جاي منين</label>
                    <select name="origin" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="workshop" @selected($req->origin === 'workshop')>ورشة</option>
                        <option value="import" @selected($req->origin === 'import')>استيراد</option>
                        <option value="manual" @selected($req->origin === 'manual')>يدوي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">المصدر</label>
                    <select name="source" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach(\App\Models\SalesLead::SOURCES as $k => $label)
                            <option value="{{ $k }}" @selected($req->source === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if(($groups ?? collect())->isNotEmpty())
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">المجموعة</label>
                    <select name="group_id" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" @selected($req->group_id == $grp->id)>{{ $grp->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if($importBatches->isNotEmpty())
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">دفعة استيراد</label>
                    <select name="import_batch" class="w-full px-3 py-2 text-sm bg-white" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        @foreach($importBatches as $batch)
                            <option value="{{ $batch }}" @selected($req->import_batch === $batch)>{{ $batch }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="stale" value="1" class="rounded border-slate-300"
                               @checked($req->boolean('stale')) onchange="this.form.submit()">
                        بلا تواصل {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }}+ يوم
                    </label>
                </div>
            </div>
        </form>
    </div>

    <div class="leads-panel overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-right py-2.5 px-3 font-semibold">العميل</th>
                    <th class="text-right py-2.5 px-3 font-semibold">تواصل</th>
                    <th class="text-right py-2.5 px-3 font-semibold hidden lg:table-cell">المرحلة</th>
                    <th class="text-right py-2.5 px-3 font-semibold">متابعة</th>
                    <th class="text-right py-2.5 px-3 font-semibold hidden md:table-cell">آخر تواصل</th>
                    <th class="text-right py-2.5 px-3 font-semibold w-44">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                @php
                    $rowBg = '';
                    if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                        $rowBg = 'bg-red-50/70';
                    } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                        $rowBg = 'bg-amber-50/50';
                    }
                    $wa = $waPhone($lead->phone);
                @endphp
                <tr class="{{ $rowBg }}">
                    <td class="py-2.5 px-3">
                        <a href="{{ route('employee.sales.leads.show', $lead) }}" class="font-semibold text-slate-900 hover:underline">
                            {{ $lead->name }}
                        </a>
                        @php $origin = $lead->originSummary(); @endphp
                        <span class="inline-flex mt-0.5 items-center rounded px-1.5 py-0.5 text-[10px] font-semibold
                            @if($origin['kind'] === 'workshop') bg-indigo-50 text-indigo-700
                            @elseif($origin['kind'] === 'import') bg-violet-50 text-violet-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ $origin['label'] }}@if($origin['detail']) · {{ $origin['detail'] }}@endif
                        </span>
                        @if($lead->group)
                            <span class="block text-[10px] text-emerald-700">{{ $lead->group->name }}</span>
                        @endif
                        @if($lead->interestType)
                            <span class="inline-flex mt-0.5 items-center rounded px-1.5 py-0.5 text-[10px] font-bold text-white" style="background:{{ $lead->interestType->color }}">{{ $lead->interestType->name_ar }}</span>
                        @endif
                        @if($lead->category)
                            <span class="block text-[11px] text-slate-500 mt-0.5">{{ $lead->category->name }}</span>
                        @endif
                        @if($lead->creator)
                            <span class="block text-[10px] text-slate-400">سجّله: {{ $lead->creator->name }}</span>
                        @endif
                        @php $lastTr = $lead->transfers->first(); @endphp
                        @if($lastTr && $lastTr->fromUser)
                            <span class="block text-[10px] text-sky-600">حُوّل من: {{ $lastTr->fromUser->name }}</span>
                        @endif
                        <span class="lg:hidden block text-[11px] text-slate-500">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                    </td>
                    <td class="py-2.5 px-3 text-slate-700">
                        @if($lead->phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $lead->phone) }}" class="font-medium hover:underline" dir="ltr">{{ $lead->phone }}</a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="py-2.5 px-3 hidden lg:table-cell text-slate-700">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</td>
                    <td class="py-2.5 px-3 text-xs whitespace-nowrap @if($lead->isFollowUpOverdue()) text-red-700 font-semibold @else text-slate-600 @endif">
                        {{ $lead->next_follow_up_at?->format('m-d H:i') ?? '—' }}
                    </td>
                    <td class="py-2.5 px-3 hidden md:table-cell text-xs text-slate-500 whitespace-nowrap">
                        {{ $lead->last_contacted_at?->format('m-d H:i') ?? '—' }}
                    </td>
                    <td class="py-2.5 px-3">
                        <div class="flex flex-wrap items-center gap-1">
                            @if($lead->phone)
                                <a href="tel:{{ preg_replace('/\s+/', '', $lead->phone) }}" class="act-btn" title="اتصال"><i class="fas fa-phone"></i></a>
                            @endif
                            @if($wa)
                                <a href="{{ $wa }}" target="_blank" rel="noopener" class="act-btn" title="واتساب"><i class="fab fa-whatsapp"></i></a>
                            @endif
                            <form method="post" action="{{ route('employee.sales.leads.quick-activity', $lead) }}" class="inline">
                                @csrf
                                <input type="hidden" name="type" value="call">
                                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                                <button type="button" class="act-btn" title="سجّل مكالمة"
                                        onclick="openCallOutcomeModal({{ $lead->id }}, @js($lead->name), @js(route('employee.sales.leads.quick-activity', $lead)))">
                                    <i class="fas fa-phone-volume"></i>
                                </button>
                            </form>
                            <form method="post" action="{{ route('employee.sales.leads.quick-activity', $lead) }}" class="inline">
                                @csrf
                                <input type="hidden" name="type" value="follow_up">
                                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                                <button type="submit" class="act-btn" title="متابعة غداً 10:00"><i class="fas fa-redo"></i></button>
                            </form>
                            @if($lead->isOpen())
                                <button type="button" class="act-btn" title="تحديد Next Follow"
                                        onclick="openNextFollowModal({{ $lead->id }}, @js($lead->name), @js($lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')))">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            @endif
                            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="act-btn" title="عرض"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('employee.sales.leads.edit', $lead) }}" class="act-btn" title="تعديل"><i class="fas fa-pen"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-500">
                        لا توجد سجلات —
                        <a href="{{ route('employee.sales.leads.create') }}" class="text-slate-800 font-semibold underline">أضف عميلاً</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leads->hasPages())
        <div>{{ $leads->links() }}</div>
    @endif
</div>

{{-- مودال تحديد Next Follow --}}
<div id="next-follow-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">تحديد Next Follow</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="nf-lead-name"></p>
            </div>
            <button type="button" onclick="closeNextFollowModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fas fa-times"></i></button>
        </div>
        <form method="post" id="nf-form" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">موعد المتابعة</label>
                <input type="datetime-local" name="next_follow_up_at" id="nf-datetime" required
                       min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">الإجراء التالي (Next Action)</label>
                <select name="follow_up_channel" id="nf-channel" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                    @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظة (اختياري)</label>
                <input type="text" name="note" maxlength="500" placeholder="مثال: متابعة عرض السعر"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div class="flex gap-2 justify-end pt-1">
                <button type="button" onclick="closeNextFollowModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">حفظ الموعد</button>
            </div>
        </form>
    </div>
</div>
{{-- مودال نتيجة المكالمة --}}
<div id="call-outcome-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">تسجيل مكالمة</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="co-lead-name"></p>
            </div>
            <button type="button" onclick="closeCallOutcomeModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fas fa-times"></i></button>
        </div>
        <form method="post" id="co-form" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="type" value="call">
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            <input type="hidden" name="apply_stage" value="1">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">نتيجة المكالمة <span class="text-rose-500">*</span></label>
                <select name="outcome" id="co-outcome" required class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2.5 text-sm font-semibold">
                    <option value="">— اختر —</option>
                    @foreach(\App\Models\SalesActivity::OUTCOMES as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظة (اختياري)</label>
                <input type="text" name="body" maxlength="500" placeholder="ملخص سريع…"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div class="flex gap-2 justify-end pt-1">
                <button type="button" onclick="closeCallOutcomeModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">حفظ المكالمة</button>
            </div>
        </form>
    </div>
</div>
<script>
function openNextFollowModal(leadId, name, datetime) {
    var modal = document.getElementById('next-follow-modal');
    var form = document.getElementById('nf-form');
    form.action = @json(url('/employee/sales/leads')) + '/' + leadId + '/next-follow';
    document.getElementById('nf-lead-name').textContent = name || '';
    document.getElementById('nf-datetime').value = datetime || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeNextFollowModal() {
    var modal = document.getElementById('next-follow-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('next-follow-modal').addEventListener('click', function (e) {
    if (e.target === this) closeNextFollowModal();
});
function openCallOutcomeModal(leadId, name, actionUrl) {
    var modal = document.getElementById('call-outcome-modal');
    var form = document.getElementById('co-form');
    form.action = actionUrl;
    document.getElementById('co-lead-name').textContent = name || '';
    document.getElementById('co-outcome').value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeCallOutcomeModal() {
    var modal = document.getElementById('call-outcome-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('call-outcome-modal').addEventListener('click', function (e) {
    if (e.target === this) closeCallOutcomeModal();
});
</script>
@endsection
