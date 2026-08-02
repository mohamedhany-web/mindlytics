@extends('layouts.admin')

@section('title', 'Lead Center — Meta Business Suite')
@section('header', 'Lead Center')

@section('content')
@php
    $filters = $filters ?? ['page' => 0, 'tab' => 'all', 'q' => '', 'assigned_to' => null, 'stage' => null];
    $stats = $stats ?? [];
    $rows = $rows ?? collect();
    $detail = $detail ?? null;
    $selectedId = (int) ($selectedId ?? 0);
    $crmReady = (bool) ($crmReady ?? false);
    $stages = $stages ?? [];
    $agents = $agents ?? [];
    $pollUrl = route('admin.meta-social.leads.poll', array_filter([
        'page' => $filters['page'] ?: null,
        'tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null,
        'q' => $filters['q'] ?: null,
        'assigned_to' => $filters['assigned_to'] ?: null,
        'stage' => $filters['stage'] ?: null,
        'lead' => $selectedId ?: null,
    ]));
    $actionUrls = $selectedId ? [
        'createLead' => route('admin.meta-social.leads.create', $selectedId),
        'assign' => route('admin.meta-social.leads.assign', $selectedId),
        'contact' => route('admin.meta-social.leads.contact', $selectedId),
        'stage' => route('admin.meta-social.leads.stage', $selectedId),
    ] : [];
@endphp

<div class="lc-page" x-data="metaLeadCenter()" :class="detail ? 'has-selection' : ''" x-cloak>
    <header class="lc-topbar">
        <div class="lc-topbar__brand">
            <button type="button" @click="$dispatch('open-sidebar')" class="lg:hidden lc-icon-btn"><i class="fas fa-bars"></i></button>
            <div class="lc-mark"><i class="fas fa-user-plus"></i></div>
            <div>
                <h1 class="lc-topbar__title">Lead Center</h1>
                <p class="lc-topbar__sub">Business Suite · مربوط بالـ Inbox و CRM المبيعات</p>
            </div>
        </div>
        <div class="lc-topbar__actions">
            <span class="lc-chip lc-chip--live" id="lc-live"><i class="fas fa-circle"></i> Live</span>
            <a href="{{ route('admin.meta-social.inbox.index') }}" class="lc-btn-ghost"><i class="fas fa-inbox"></i> Inbox</a>
            <a href="{{ route('admin.sales.leads.index') }}" class="lc-btn-ghost hidden md:inline-flex"><i class="fas fa-briefcase"></i> CRM</a>
        </div>
    </header>

    @if(! ($tablesReady ?? false))
        <div class="lc-banner">شغّل: <code>php artisan migrate --force</code></div>
    @endif

    <div class="lc-stats" id="lc-stats">
        @foreach([
            ['all', 'الكل', 'fa-layer-group'],
            ['new', 'جديد', 'fa-star'],
            ['in_crm', 'في CRM', 'fa-database'],
            ['has_phone', 'برقم', 'fa-phone'],
            ['unread', 'غير مقروء', 'fa-envelope'],
            ['unassigned', 'بدون تعيين', 'fa-user-slash'],
        ] as [$key, $label, $icon])
            <a href="{{ route('admin.meta-social.leads.index', array_filter(['tab' => $key === 'all' ? null : $key, 'page' => $filters['page'] ?: null, 'q' => $filters['q'] ?: null])) }}"
               class="lc-stat {{ ($filters['tab'] ?? 'all') === $key || (($filters['tab'] ?? 'all') === 'all' && $key === 'all') ? 'is-active' : '' }}"
               data-stat="{{ $key }}">
                <i class="fas {{ $icon }}"></i>
                <div>
                    <strong data-role="val">{{ number_format($stats[$key] ?? 0) }}</strong>
                    <span>{{ $label }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="lc-shell">
        <aside class="lc-list">
            <div class="lc-list__head">
                <form method="get" action="{{ route('admin.meta-social.leads.index') }}" class="lc-search">
                    <i class="fas fa-search"></i>
                    <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="بحث بالاسم / الهاتف / الرسالة…">
                    @if($filters['page'])<input type="hidden" name="page" value="{{ $filters['page'] }}">@endif
                    @if(($filters['tab'] ?? 'all') !== 'all')<input type="hidden" name="tab" value="{{ $filters['tab'] }}">@endif
                </form>
                <div class="lc-filters">
                    <select onchange="location = this.value">
                        <option value="{{ route('admin.meta-social.leads.index', array_filter(['tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null])) }}">كل الصفحات</option>
                        @foreach($pages as $p)
                            <option value="{{ route('admin.meta-social.leads.index', array_filter(['page' => $p->id, 'tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null])) }}" @selected((int)$filters['page'] === (int)$p->id)>{{ $p->page_name }}</option>
                        @endforeach
                    </select>
                    <select onchange="location = this.value">
                        <option value="{{ route('admin.meta-social.leads.index', array_filter(['page' => $filters['page'] ?: null, 'tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null])) }}">كل الموظفين</option>
                        <option value="{{ route('admin.meta-social.leads.index', array_filter(['page' => $filters['page'] ?: null, 'tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null, 'assigned_to' => 'unassigned'])) }}" @selected(($filters['assigned_to'] ?? '') === 'unassigned')>غير معيّن</option>
                        @foreach($agents as $agent)
                            <option value="{{ route('admin.meta-social.leads.index', array_filter(['page' => $filters['page'] ?: null, 'tab' => $filters['tab'] !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null, 'assigned_to' => $agent->id])) }}" @selected((string)($filters['assigned_to'] ?? '') === (string)$agent->id)>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lc-tabs">
                    @foreach(['all' => 'الكل', 'new' => 'جديد', 'in_crm' => 'CRM', 'messenger' => 'Messenger', 'instagram' => 'Instagram', 'open' => 'مفتوح', 'closed' => 'منتهي'] as $tabKey => $tabLabel)
                        <a href="{{ route('admin.meta-social.leads.index', array_filter(['tab' => $tabKey === 'all' ? null : $tabKey, 'page' => $filters['page'] ?: null, 'q' => $filters['q'] ?: null, 'assigned_to' => $filters['assigned_to'] ?: null])) }}"
                           class="lc-tab {{ ($filters['tab'] ?? 'all') === $tabKey ? 'is-active' : '' }}">{{ $tabLabel }}</a>
                    @endforeach
                </div>
            </div>

            <div class="lc-list__body" id="lc-rows">
                @forelse($rows as $row)
                    <a href="{{ route('admin.meta-social.leads.index', array_filter([
                            'lead' => $row['id'],
                            'page' => $filters['page'] ?: null,
                            'tab' => ($filters['tab'] ?? 'all') !== 'all' ? $filters['tab'] : null,
                            'q' => $filters['q'] ?: null,
                            'assigned_to' => $filters['assigned_to'] ?: null,
                            'stage' => $filters['stage'] ?: null,
                        ])) }}"
                       class="lc-row {{ $selectedId === (int)$row['id'] ? 'is-active' : '' }} {{ ($row['unread'] ?? 0) > 0 ? 'is-unread' : '' }}"
                       data-lead-id="{{ $row['id'] }}">
                        <div class="lc-avatar">
                            @if(!empty($row['profile_pic']))
                                <img src="{{ $row['profile_pic'] }}" alt="">
                            @else
                                <span>{{ mb_substr($row['display_name'] ?? '?', 0, 1) }}</span>
                            @endif
                            <i class="lc-plat {{ ($row['platform'] ?? '') === 'instagram' ? 'fab fa-instagram ig' : 'fab fa-facebook-messenger msgr' }}"></i>
                        </div>
                        <div class="lc-row__main">
                            <div class="lc-row__top">
                                <p class="lc-row__name" data-role="name">{{ $row['display_name'] }}</p>
                                <time data-role="time">{{ $row['last_time'] }}</time>
                            </div>
                            <p class="lc-row__meta" data-role="meta">
                                {{ $row['platform_label'] }} · {{ $row['page_name'] ?: '—' }}
                                @if(!empty($row['assignee_name'])) · {{ $row['assignee_name'] }} @endif
                            </p>
                            <div class="lc-row__bottom">
                                <p class="lc-row__preview" data-role="preview">{{ $row['preview'] ?: '—' }}</p>
                                <div class="lc-badges" data-role="badges">
                                    @if($row['in_crm'])<span class="lc-mini crm">CRM</span>@endif
                                    @if($row['is_real_phone'])<span class="lc-mini phone">Phone</span>@endif
                                    @if(($row['status'] ?? '') === 'closed')<span class="lc-mini done">Done</span>@endif
                                    @if(($row['unread'] ?? 0) > 0)<span class="lc-unread">{{ $row['unread'] }}</span>@endif
                                </div>
                            </div>
                            <p class="lc-row__stage" data-role="stage">{{ $row['stage_label'] }}</p>
                        </div>
                    </a>
                @empty
                    <div class="lc-empty">
                        <i class="fas fa-user-plus"></i>
                        <p>لا توجد leads بهذا الفلتر</p>
                        <a href="{{ route('admin.meta-social.inbox.index') }}">افتح الـ Inbox</a>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="lc-detail" id="lc-detail">
            <template x-if="!detail">
                <div class="lc-empty lc-empty--center">
                    <div class="lc-mark lc-mark--xl"><i class="fas fa-user-plus"></i></div>
                    <p class="font-black text-lg">Lead Center</p>
                    <p class="text-sm text-slate-500 max-w-sm text-center">كل محادثة من Messenger/Instagram تظهر هنا لحظيًا، وترتبط مباشرة بـ CRM المبيعات.</p>
                </div>
            </template>
            <template x-if="detail">
                <div class="lc-detail__wrap">
                    <div class="lc-detail__head">
                        <a href="{{ route('admin.meta-social.leads.index', array_filter(['page' => $filters['page'] ?: null, 'tab' => ($filters['tab'] ?? 'all') !== 'all' ? $filters['tab'] : null, 'q' => $filters['q'] ?: null])) }}"
                           class="lg:hidden lc-icon-btn" title="رجوع"><i class="fas fa-arrow-right"></i></a>
                        <div class="lc-avatar lc-avatar--lg">
                            <template x-if="detail.profile_pic">
                                <img :src="detail.profile_pic" alt="">
                            </template>
                            <template x-if="!detail.profile_pic">
                                <span x-text="(detail.display_name || '?').charAt(0)"></span>
                            </template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="lc-detail__name" x-text="detail.display_name"></h2>
                            <p class="lc-detail__sub">
                                <span x-text="detail.platform_label"></span> ·
                                <span x-text="detail.page_name || '—'"></span>
                                <span x-show="detail.assignee_name"> · <span x-text="detail.assignee_name"></span></span>
                            </p>
                        </div>
                        <a :href="detail.inbox_url" class="lc-btn-primary"><i class="fas fa-comments"></i> فتح المحادثة</a>
                        <a x-show="detail.crm_url" :href="detail.crm_url" class="lc-btn-ghost" target="_blank"><i class="fas fa-external-link-alt"></i> CRM</a>
                    </div>

                    <div class="lc-detail__body">
                        <div class="lc-cards">
                            <div class="lc-card">
                                <h3>الحالة في النظام</h3>
                                <dl>
                                    <div><dt>المرحلة</dt><dd x-text="detail.stage_label"></dd></div>
                                    <div><dt>الهاتف</dt><dd dir="ltr" x-text="detail.phone || '—'"></dd></div>
                                    <div><dt>الإيميل</dt><dd dir="ltr" x-text="detail.email || '—'"></dd></div>
                                    <div><dt>آخر رسالة</dt><dd x-text="detail.last_human || '—'"></dd></div>
                                    <div><dt>غير مقروء</dt><dd x-text="detail.unread || 0"></dd></div>
                                    <div><dt>Inbox</dt><dd x-text="detail.status === 'closed' ? 'Done' : 'Open'"></dd></div>
                                </dl>
                            </div>

                            <div class="lc-card">
                                <h3>آخر رسالة</h3>
                                <p class="lc-preview-box" x-text="detail.preview || '—'"></p>
                            </div>
                        </div>

                        <div class="lc-card">
                            <h3>بيانات التواصل</h3>
                            <form class="lc-form" @submit.prevent="saveContact()">
                                <input type="text" x-model="contactName" placeholder="الاسم">
                                <input type="text" x-model="contactPhone" placeholder="الهاتف" dir="ltr">
                                <input type="email" x-model="contactEmail" placeholder="الإيميل" dir="ltr">
                                <textarea x-model="contactNotes" rows="2" placeholder="ملاحظات"></textarea>
                                <button type="submit" class="lc-btn-dark" :disabled="saving">حفظ في النظام</button>
                            </form>
                        </div>

                        <div class="lc-card">
                            <h3>تعيين موظف مبيعات</h3>
                            <div class="lc-form-row">
                                <select x-model="assigneeId">
                                    <option value="">— اختر —</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="lc-btn-primary" @click="assignAgent()" :disabled="saving || !assigneeId">تعيين</button>
                            </div>
                        </div>

                        <div class="lc-card" x-show="!detail.in_crm">
                            <h3>CRM Lead</h3>
                            <p class="text-xs text-slate-500 mb-2">حوّل المحادثة لعميل محتمل في نظام المبيعات — يظهر في CRM وفي Lead Center معًا.</p>
                            <button type="button" class="lc-btn-success w-full" @click="createLead()" :disabled="saving">
                                <i class="fas fa-plus"></i> إنشاء Lead في CRM
                            </button>
                        </div>

                        <div class="lc-card" x-show="detail.in_crm">
                            <h3>مرحلة الـ Lead</h3>
                            <div class="lc-form-row">
                                <select x-model="stageValue">
                                    @foreach($stages as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="lc-btn-primary" @click="saveStage()" :disabled="saving">تحديث</button>
                            </div>
                            <a :href="detail.crm_url" class="lc-link" target="_blank">فتح بطاقة العميل الكاملة في CRM ←</a>
                        </div>

                        <p class="lc-msg err" x-show="error" x-text="error"></p>
                        <p class="lc-msg ok" x-show="ok" x-text="ok"></p>
                    </div>
                </div>
            </template>
        </section>
    </div>
</div>

@push('styles')
<style>
.lc-page {
    --lc-blue: #0084FF; --lc-ink: #1c2b33; --lc-muted: #65676b; --lc-line: #e4e6eb; --lc-bg: #f0f2f5; --lc-ig: #E1306C;
    display: flex; flex-direction: column; height: calc(100vh - 4rem); min-height: 0; overflow: hidden; background: #fff; color: var(--lc-ink);
}
main:has(.lc-page) { overflow: hidden !important; }
main:has(.lc-page) > div:last-child { padding: 0 !important; max-width: none !important; }
.lc-topbar { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.7rem .9rem; border-bottom:1px solid var(--lc-line); }
.lc-topbar__brand { display:flex; align-items:center; gap:.65rem; }
.lc-topbar__title { font-size:1.05rem; font-weight:800; }
.lc-topbar__sub { font-size:10px; color:var(--lc-muted); }
.lc-topbar__actions { display:flex; gap:.4rem; align-items:center; flex-wrap:wrap; }
.lc-mark { width:2.25rem; height:2.25rem; border-radius:.75rem; background:#e7f3ff; color:var(--lc-blue); display:flex; align-items:center; justify-content:center; }
.lc-mark--xl { width:4.5rem; height:4.5rem; font-size:1.75rem; border-radius:1.25rem; margin-bottom:.75rem; }
.lc-chip { font-size:11px; font-weight:800; padding:.3rem .55rem; border-radius:999px; background:var(--lc-bg); }
.lc-chip--live { background:#ecfdf5; color:#047857; display:inline-flex; align-items:center; gap:.35rem; }
.lc-chip--live i { font-size:7px; color:#10b981; animation:lcPulse 1.4s ease-in-out infinite; }
.lc-chip--live.is-stale { background:#f3f4f6; color:#6b7280; }
.lc-chip--live.is-stale i { animation:none; color:#9ca3af; }
@keyframes lcPulse { 0%,100%{opacity:1} 50%{opacity:.35} }
.lc-banner { margin:.5rem .75rem 0; padding:.55rem .75rem; border-radius:.75rem; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; font-weight:600; }
.lc-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.45rem; padding:.65rem .75rem; border-bottom:1px solid var(--lc-line); background:#fff; }
@media(min-width:900px){ .lc-stats{ grid-template-columns:repeat(6,minmax(0,1fr)); } }
.lc-stat { display:flex; align-items:center; gap:.55rem; padding:.55rem .65rem; border-radius:.85rem; background:var(--lc-bg); text-decoration:none; color:var(--lc-ink); border:1px solid transparent; }
.lc-stat i { color:var(--lc-muted); }
.lc-stat strong { display:block; font-size:1rem; font-weight:900; line-height:1.1; }
.lc-stat span { font-size:10px; color:var(--lc-muted); font-weight:700; }
.lc-stat.is-active { background:#e7f3ff; border-color:#cfe6ff; color:var(--lc-blue); }
.lc-stat.is-active i { color:var(--lc-blue); }
.lc-shell { flex:1; min-height:0; display:grid; grid-template-columns:1fr; }
@media(min-width:1024px){ .lc-shell{ grid-template-columns:minmax(320px,400px) minmax(0,1fr); } }
.lc-list { display:grid; grid-template-rows:auto minmax(0,1fr); border-inline-end:1px solid var(--lc-line); min-height:0; }
.lc-list__head { padding:.65rem .7rem; border-bottom:1px solid var(--lc-line); }
.lc-search { display:flex; align-items:center; gap:.5rem; background:var(--lc-bg); border-radius:999px; padding:.45rem .75rem; margin-bottom:.5rem; }
.lc-search input { border:0; outline:0; background:transparent; width:100%; font-size:.8rem; }
.lc-filters { display:grid; grid-template-columns:1fr 1fr; gap:.35rem; margin-bottom:.45rem; }
.lc-filters select { border:1px solid var(--lc-line); border-radius:.55rem; background:#fff; font-size:11px; padding:.35rem .45rem; }
.lc-tabs { display:flex; flex-wrap:wrap; gap:.3rem; }
.lc-tab { font-size:10px; font-weight:700; padding:.28rem .5rem; border-radius:999px; background:var(--lc-bg); color:var(--lc-muted); text-decoration:none; }
.lc-tab.is-active { background:#e7f3ff; color:var(--lc-blue); }
.lc-list__body { overflow-y:auto; }
.lc-row { display:flex; gap:.65rem; padding:.7rem .75rem; border-bottom:1px solid #f0f2f5; text-decoration:none; color:inherit; }
.lc-row:hover { background:#f7f8fa; }
.lc-row.is-active { background:#e7f3ff; }
.lc-row.is-unread .lc-row__name { font-weight:900; }
.lc-avatar { width:2.6rem; height:2.6rem; border-radius:999px; background:#e7f3ff; color:var(--lc-blue); display:flex; align-items:center; justify-content:center; font-weight:900; position:relative; flex-shrink:0; overflow:hidden; }
.lc-avatar img { width:100%; height:100%; object-fit:cover; }
.lc-avatar--lg { width:3rem; height:3rem; }
.lc-plat { position:absolute; bottom:-2px; inset-inline-end:-2px; width:1rem; height:1rem; border-radius:999px; background:#fff; font-size:10px; display:flex; align-items:center; justify-content:center; }
.lc-plat.msgr { color:var(--lc-blue); } .lc-plat.ig { color:var(--lc-ig); }
.lc-row__main { min-width:0; flex:1; }
.lc-row__top,.lc-row__bottom { display:flex; justify-content:space-between; gap:.5rem; align-items:baseline; }
.lc-row__name { font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.lc-row__top time,.lc-row__meta,.lc-row__stage { font-size:10px; color:var(--lc-muted); }
.lc-row__meta,.lc-row__preview { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.lc-row__preview { font-size:12px; color:#8a8d91; flex:1; }
.lc-row__stage { margin-top:2px; font-weight:700; color:var(--lc-blue); }
.lc-badges { display:flex; gap:.25rem; align-items:center; }
.lc-mini { font-size:9px; font-weight:800; padding:.1rem .35rem; border-radius:999px; }
.lc-mini.crm { background:#ecfdf5; color:#047857; }
.lc-mini.phone { background:#e7f3ff; color:#0084FF; }
.lc-mini.done { background:#f3f4f6; color:#4b5563; }
.lc-unread { min-width:1.1rem; height:1.1rem; padding:0 .3rem; border-radius:999px; background:var(--lc-blue); color:#fff; font-size:10px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; }
.lc-detail { min-height:0; overflow:hidden; background:var(--lc-bg); display:block; }
@media(max-width:1023px){
    .lc-page.has-selection .lc-list { display:none; }
    .lc-page:not(.has-selection) .lc-detail { display:none; }
}
.lc-detail__wrap { height:100%; display:grid; grid-template-rows:auto minmax(0,1fr); }
.lc-detail__head { display:flex; align-items:center; gap:.65rem; padding:.75rem .9rem; background:#fff; border-bottom:1px solid var(--lc-line); flex-wrap:wrap; }
.lc-detail__name { font-size:1rem; font-weight:900; }
.lc-detail__sub { font-size:11px; color:var(--lc-muted); }
.lc-detail__body { overflow-y:auto; padding:.9rem; display:flex; flex-direction:column; gap:.75rem; }
.lc-cards { display:grid; grid-template-columns:1fr; gap:.75rem; }
@media(min-width:900px){ .lc-cards{ grid-template-columns:1fr 1fr; } }
.lc-card { background:#fff; border:1px solid var(--lc-line); border-radius:1rem; padding:.9rem; }
.lc-card h3 { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; color:var(--lc-muted); margin-bottom:.55rem; }
.lc-card dl { display:grid; gap:.4rem; font-size:12px; }
.lc-card dl > div { display:flex; justify-content:space-between; gap:.75rem; }
.lc-card dt { color:var(--lc-muted); } .lc-card dd { font-weight:700; text-align:end; }
.lc-preview-box { font-size:13px; line-height:1.5; background:var(--lc-bg); border-radius:.75rem; padding:.75rem; min-height:4rem; }
.lc-form,.lc-form-row { display:flex; flex-direction:column; gap:.45rem; }
.lc-form-row { flex-direction:row; align-items:center; }
.lc-form input,.lc-form textarea,.lc-form-row select,.lc-form-row input {
    width:100%; border:1px solid var(--lc-line); border-radius:.75rem; padding:.55rem .7rem; font-size:12px; background:#fff;
}
.lc-btn-primary,.lc-btn-ghost,.lc-btn-dark,.lc-btn-success,.lc-icon-btn {
    display:inline-flex; align-items:center; justify-content:center; gap:.35rem; border-radius:.7rem; font-size:11px; font-weight:800; padding:.45rem .7rem; cursor:pointer; border:1px solid var(--lc-line); text-decoration:none;
}
.lc-btn-primary { background:var(--lc-blue); color:#fff; border-color:var(--lc-blue); }
.lc-btn-dark { background:var(--lc-ink); color:#fff; border-color:var(--lc-ink); }
.lc-btn-success { background:#059669; color:#fff; border-color:#059669; }
.lc-btn-ghost,.lc-icon-btn { background:#fff; color:var(--lc-ink); }
.lc-icon-btn { width:2.1rem; height:2.1rem; padding:0; }
.lc-link { display:inline-block; margin-top:.55rem; font-size:12px; font-weight:800; color:var(--lc-blue); }
.lc-msg { font-size:11px; font-weight:700; } .lc-msg.err{color:#dc2626} .lc-msg.ok{color:#059669}
.lc-empty { padding:2.5rem 1rem; text-align:center; color:var(--lc-muted); font-size:.85rem; }
.lc-empty i { font-size:1.75rem; opacity:.35; display:block; margin-bottom:.5rem; }
.lc-empty a { color:var(--lc-blue); font-weight:800; }
.lc-empty--center { height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.w-full { width:100%; }
</style>
@endpush

@push('scripts')
<script>
function metaLeadCenter() {
    return {
        detail: @json($detail),
        pollUrl: @json($pollUrl),
        actionUrls: @json($actionUrls),
        csrf: @json(csrf_token()),
        inboxVersion: '',
        saving: false,
        error: '',
        ok: '',
        contactName: @json($detail['display_name'] ?? ''),
        contactPhone: @json($detail['phone'] ?? ''),
        contactEmail: @json($detail['email'] ?? ''),
        contactNotes: @json($detail['notes'] ?? ''),
        assigneeId: @json(!empty($detail['assigned_to']) ? (string) $detail['assigned_to'] : ''),
        stageValue: @json($detail['stage'] ?? 'new_lead'),
        pollTimer: null,
        polling: false,
        init() {
            this.applyDetail(this.detail);
            this.pollTimer = setInterval(() => this.poll(), document.hidden ? 8000 : 2000);
            document.addEventListener('visibilitychange', () => {
                clearInterval(this.pollTimer);
                this.pollTimer = setInterval(() => this.poll(), document.hidden ? 8000 : 2000);
                if (!document.hidden) this.poll();
            });
        },
        applyDetail(d) {
            if (!d) return;
            this.detail = d;
            this.contactName = d.display_name || '';
            this.contactPhone = d.phone || '';
            this.contactEmail = d.email || '';
            this.contactNotes = d.notes || '';
            this.assigneeId = d.assigned_to ? String(d.assigned_to) : '';
            this.stageValue = d.stage || 'new_lead';
            // حدّث روابط الأكشن حسب الـ lead الحالي
            const id = d.id;
            if (id) {
                const base = @json(url('/admin/meta-social/leads'));
                this.actionUrls = {
                    createLead: base + '/' + id + '/create-lead',
                    assign: base + '/' + id + '/assign',
                    contact: base + '/' + id + '/contact',
                    stage: base + '/' + id + '/stage',
                };
            }
        },
        setLive(ok) {
            const el = document.getElementById('lc-live');
            if (el) el.classList.toggle('is-stale', !ok);
        },
        updateStats(stats) {
            if (!stats) return;
            Object.keys(stats).forEach((k) => {
                const el = document.querySelector('#lc-stats [data-stat="' + k + '"] [data-role="val"]');
                if (el) el.textContent = Number(stats[k] || 0).toLocaleString('en-US');
            });
        },
        updateRows(rows) {
            if (!Array.isArray(rows)) return;
            const box = document.getElementById('lc-rows');
            if (!box) return;
            rows.forEach((r) => {
                const row = box.querySelector('[data-lead-id="' + r.id + '"]');
                if (!row) return;
                const name = row.querySelector('[data-role="name"]');
                const time = row.querySelector('[data-role="time"]');
                const preview = row.querySelector('[data-role="preview"]');
                const stage = row.querySelector('[data-role="stage"]');
                const badges = row.querySelector('[data-role="badges"]');
                if (name) name.textContent = r.display_name || '';
                if (time) time.textContent = r.last_time || '';
                if (preview) preview.textContent = r.preview || '—';
                if (stage) stage.textContent = r.stage_label || '';
                row.classList.toggle('is-unread', Number(r.unread) > 0);
                if (badges) {
                    let html = '';
                    if (r.in_crm) html += '<span class="lc-mini crm">CRM</span>';
                    if (r.is_real_phone) html += '<span class="lc-mini phone">Phone</span>';
                    if (r.status === 'closed') html += '<span class="lc-mini done">Done</span>';
                    if (Number(r.unread) > 0) html += '<span class="lc-unread">' + Number(r.unread) + '</span>';
                    badges.innerHTML = html;
                }
                if (Number(r.unread) > 0 && box.firstElementChild !== row) {
                    box.prepend(row);
                }
            });
        },
        async postJson(url, body) {
            this.saving = true; this.error = ''; this.ok = '';
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                    body: JSON.stringify(body || {}),
                });
                const data = await res.json();
                if (!data.success) { this.error = data.error || 'فشلت العملية'; return null; }
                if (data.detail) this.applyDetail(data.detail);
                if (data.row) this.updateRows([data.row]);
                this.ok = 'تم الحفظ';
                return data;
            } catch (e) {
                this.error = 'خطأ في الاتصال';
                return null;
            } finally {
                this.saving = false;
            }
        },
        async saveContact() {
            if (!this.actionUrls.contact) return;
            await this.postJson(this.actionUrls.contact, {
                name: this.contactName, phone: this.contactPhone, email: this.contactEmail, notes: this.contactNotes,
            });
        },
        async assignAgent() {
            if (!this.actionUrls.assign || !this.assigneeId) return;
            await this.postJson(this.actionUrls.assign, { assigned_to: Number(this.assigneeId) });
        },
        async createLead() {
            if (!this.actionUrls.createLead) return;
            const data = await this.postJson(this.actionUrls.createLead, {
                name: this.contactName, phone: this.contactPhone, email: this.contactEmail,
                assigned_to: this.assigneeId ? Number(this.assigneeId) : null,
            });
            if (data?.lead_id) this.ok = 'تم إنشاء Lead #' + data.lead_id;
        },
        async saveStage() {
            if (!this.actionUrls.stage || !this.stageValue) return;
            await this.postJson(this.actionUrls.stage, { stage: this.stageValue });
        },
        async poll() {
            if (!this.pollUrl || this.polling) return;
            this.polling = true;
            try {
                const sep = this.pollUrl.includes('?') ? '&' : '?';
                let url = this.pollUrl + sep + 'v=' + encodeURIComponent(this.inboxVersion || '');
                if (this.detail?.id) url += '&lead=' + this.detail.id;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                const data = await res.json();
                if (!data.success) { this.setLive(false); return; }
                this.setLive(true);
                if (data.inbox_version) this.inboxVersion = data.inbox_version;
                if (data.stats) this.updateStats(data.stats);
                if (Array.isArray(data.rows)) this.updateRows(data.rows);
                if (data.detail) this.applyDetail(data.detail);
            } catch (e) {
                this.setLive(false);
            } finally {
                this.polling = false;
            }
        },
    };
}
</script>
@endpush
@endsection
