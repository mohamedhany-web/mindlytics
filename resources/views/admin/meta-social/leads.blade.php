@extends('layouts.admin')

@section('title', 'Lead Center — Meta Business Suite')
@section('header', 'Lead Center')

@section('content')
@php
    $filters = $filters ?? ['page' => 0, 'tab' => 'all', 'q' => '', 'view' => 'list', 'sort' => 'recent'];
    $stats = $stats ?? [];
    $rows = $rows ?? collect();
    $pipeline = $pipeline ?? [];
    $pipelineTotal = (int) ($pipelineTotal ?? 0);
    $detail = $detail ?? null;
    $selectedId = (int) ($selectedId ?? 0);
    $stages = $stages ?? \App\Models\MetaSocialConversation::LEAD_STAGES;
    $priorities = $priorities ?? \App\Models\MetaSocialConversation::PRIORITIES;
    $suggestedLabels = $suggestedLabels ?? \App\Models\MetaSocialConversation::SUGGESTED_LABELS;
    $agents = $agents ?? [];
    $viewMode = $filters['view'] ?? 'list';
    $filterQs = array_filter([
        'page' => $filters['page'] ?: null,
        'tab' => ($filters['tab'] ?? 'all') !== 'all' ? $filters['tab'] : null,
        'q' => $filters['q'] ?: null,
        'assigned_to' => $filters['assigned_to'] ?? null,
        'stage' => $filters['stage'] ?? null,
        'label' => $filters['label'] ?? null,
        'priority' => $filters['priority'] ?? null,
        'from' => $filters['from'] ?? null,
        'to' => $filters['to'] ?? null,
        'sort' => ($filters['sort'] ?? 'recent') !== 'recent' ? $filters['sort'] : null,
        'view' => $viewMode !== 'list' ? $viewMode : null,
    ], fn ($v) => $v !== null && $v !== '');
    $urls = $urls ?? [];
    $pollUrl = $urls['poll'] ?? url('/admin/meta-social/leads/poll');
    $exportUrl = $urls['export'] ?? url('/admin/meta-social/leads/export');
    $bulkUrl = $urls['bulk'] ?? url('/admin/meta-social/leads/bulk');
    $leadsIndexUrl = $urls['index'] ?? url('/admin/meta-social/leads');
    $inboxUrl = $urls['inbox'] ?? url('/admin/meta-social/inbox');
    $detailJson = $detailJson ?? 'null';
    $lcRoute = function (array $extra = []) use ($filterQs, $leadsIndexUrl) {
        $params = array_filter(array_merge($filterQs, $extra), fn ($v) => $v !== null && $v !== '');
        try {
            return route('admin.meta-social.leads.index', $params);
        } catch (\Throwable) {
            return $leadsIndexUrl.(count($params) ? ('?'.http_build_query($params)) : '');
        }
    };
@endphp

<div class="lc-page" x-data="metaLeadCenter()" :class="{ 'has-selection': detail && viewMode === 'list' }" x-cloak>
    <header class="lc-topbar">
        <div class="lc-topbar__brand">
            <button type="button" x-on:click="$dispatch('open-sidebar')" class="lg:hidden lc-icon-btn"><i class="fas fa-bars"></i></button>
            <div class="lc-mark"><i class="fas fa-user-plus"></i></div>
            <div>
                <h1 class="lc-topbar__title">Lead Center</h1>
                <p class="lc-topbar__sub">Meta Business Suite · Inbox · CRM · Labels · Reminders · Pipeline</p>
            </div>
        </div>
        <div class="lc-topbar__actions">
            <span class="lc-chip lc-chip--live" id="lc-live"><i class="fas fa-circle"></i> Live</span>
            <div class="lc-view-switch">
                @foreach(['list' => 'قائمة', 'table' => 'جدول', 'pipeline' => 'Pipeline'] as $v => $lbl)
                    <a href="{{ $lcRoute(['view' => $v === 'list' ? null : $v]) }}"
                       class="{{ $viewMode === $v ? 'is-active' : '' }}">{{ $lbl }}</a>
                @endforeach
            </div>
            <a href="{{ $exportUrl }}" class="lc-btn-ghost"><i class="fas fa-download"></i> Export</a>
            <a href="{{ $inboxUrl }}" class="lc-btn-ghost"><i class="fas fa-inbox"></i> Inbox</a>
        </div>
    </header>

    @if(!empty($pageError))
        <div class="lc-banner">خطأ مؤقت في Lead Center: {{ $pageError }} — نفّذ على السيرفر: <code>php artisan migrate --force && php artisan view:clear</code></div>
    @elseif(! ($tablesReady ?? false))
        <div class="lc-banner">شغّل: <code>php artisan migrate --force</code></div>
    @elseif(! ($columnsReady ?? true) || ! ($crmReady ?? true))
        <div class="lc-banner">لتفعيل Lead Center بالكامل (CRM / Labels / Reminder): <code>php artisan migrate --force</code></div>
    @endif

    <div class="lc-stats" id="lc-stats">
        @foreach([
            ['all', 'الكل', 'fa-layer-group'],
            ['unread', 'Unread', 'fa-envelope'],
            ['unassigned', 'Unassigned', 'fa-user-slash'],
            ['reminder_due', 'Reminder', 'fa-bell'],
            ['high_priority', 'Priority', 'fa-fire'],
            ['closed', 'Done', 'fa-check-circle'],
        ] as [$key, $label, $icon])
            <a href="{{ $lcRoute(['tab' => $key === 'all' ? null : $key, 'view' => $viewMode !== 'list' ? $viewMode : null]) }}"
               class="lc-stat {{ ($filters['tab'] ?? 'all') === $key ? 'is-active' : '' }}" data-stat="{{ $key }}">
                <i class="fas {{ $icon }}"></i>
                <div>
                    <strong data-role="val">{{ number_format($stats[$key] ?? 0) }}</strong>
                    <span>{{ $label }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <form method="get" action="{{ $leadsIndexUrl }}" class="lc-toolbar">
        @if($viewMode !== 'list')<input type="hidden" name="view" value="{{ $viewMode }}">@endif
        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="بحث…">
        <select name="page">
            <option value="">كل الصفحات</option>
            @foreach($pages as $p)
                <option value="{{ $p->id }}" @selected((int)($filters['page'] ?? 0) === (int)$p->id)>{{ $p->page_name }}</option>
            @endforeach
        </select>
        <select name="assigned_to">
            <option value="">كل الموظفين</option>
            <option value="unassigned" @selected(($filters['assigned_to'] ?? '') === 'unassigned')>Unassigned</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}" @selected((string)($filters['assigned_to'] ?? '') === (string)$agent->id)>{{ $agent->name }}</option>
            @endforeach
        </select>
        <select name="stage">
            <option value="">كل المراحل</option>
            @foreach($stages as $sk => $sl)
                <option value="{{ $sk }}" @selected(($filters['stage'] ?? '') === $sk)>{{ $sl }}</option>
            @endforeach
        </select>
        <select name="priority">
            <option value="">كل الأولويات</option>
            @foreach($priorities as $pk => $pl)
                <option value="{{ $pk }}" @selected(($filters['priority'] ?? '') === $pk)>{{ $pl }}</option>
            @endforeach
        </select>
        <select name="sort">
            <option value="recent" @selected(($filters['sort'] ?? '') === 'recent')>الأحدث</option>
            <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>الأقدم</option>
            <option value="priority" @selected(($filters['sort'] ?? '') === 'priority')>الأولوية</option>
            <option value="reminder" @selected(($filters['sort'] ?? '') === 'reminder')>التذكير</option>
            <option value="name" @selected(($filters['sort'] ?? '') === 'name')>الاسم</option>
        </select>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
        <button type="submit" class="lc-btn-primary">تطبيق</button>
    </form>

    {{-- PIPELINE --}}
    @if($viewMode === 'pipeline')
        <div class="lc-pipe-summary">
            <strong id="lc-pipe-total">{{ number_format($pipelineTotal) }}</strong>
            <span>lead في الـ Pipeline (كل البيانات)</span>
        </div>
        <div class="lc-pipeline" id="lc-pipeline">
            @foreach($stages as $stageKey => $stageLabel)
                <div class="lc-pipe-col" data-stage="{{ $stageKey }}">
                    <header>
                        <strong>{{ $stageLabel }}</strong>
                        <span>{{ count($pipeline[$stageKey] ?? []) }}</span>
                    </header>
                    <div class="lc-pipe-body">
                        @forelse(($pipeline[$stageKey] ?? []) as $row)
                            <a href="{{ $lcRoute(['view' => 'list', 'lead' => $row['id']]) }}" class="lc-pipe-card {{ !empty($row['is_done']) ? 'is-done' : '' }} {{ !empty($row['priority']) && $row['priority'] !== 'normal' ? 'prio-'.$row['priority'] : '' }}">
                                <div class="lc-pipe-card__top">
                                    <p class="name">{{ $row['display_name'] }}</p>
                                    <span class="time">{{ $row['last_time'] ?: ($row['last_human'] ?? '') }}</span>
                                </div>
                                <p class="meta">{{ $row['platform_label'] }} · {{ $row['page_name'] ?: '—' }}</p>
                                <p class="preview">{{ \Illuminate\Support\Str::limit($row['preview'] ?: '—', 80) }}</p>
                                <div class="lc-pipe-card__fields">
                                    <div><span>الأولوية</span><b>{{ $row['priority_label'] ?: '—' }}</b></div>
                                    <div>
                                        <span>الحالة</span>
                                        <b>
                                            {{ !empty($row['is_done']) ? 'Done' : 'Open' }}
                                            @if(($row['unread'] ?? 0) > 0)
                                                · {{ $row['unread'] }} unread
                                            @endif
                                        </b>
                                    </div>
                                    <div><span>المسؤول</span><b>{{ $row['assignee_name'] ?: 'Unassigned' }}</b></div>
                                    <div><span>الهاتف</span><b dir="ltr">{{ $row['phone'] ?: '—' }}</b></div>
                                    <div><span>الإيميل</span><b dir="ltr">{{ $row['email'] ?: '—' }}</b></div>
                                    <div><span>تذكير</span><b class="{{ !empty($row['reminder_due']) ? 'due' : '' }}">{{ $row['reminder_human'] ?: '—' }}</b></div>
                                    <div><span>CRM</span><b>{{ !empty($row['in_crm']) ? ($row['crm_stage_label'] ?: 'Linked') : '—' }}</b></div>
                                    <div><span>آخر نشاط</span><b>{{ $row['last_at'] ?: ($row['created_at'] ?: '—') }}</b></div>
                                </div>
                                @if(!empty($row['labels']))
                                    <div class="tags">
                                        @foreach($row['labels'] as $lb)<span>{{ $lb }}</span>@endforeach
                                    </div>
                                @endif
                            </a>
                        @empty
                            <p class="lc-pipe-empty">لا يوجد</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    {{-- TABLE --}}
    @elseif($viewMode === 'table')
        <div class="lc-table-wrap">
            <div class="lc-bulk" x-show="selectedIds.length" x-cloak>
                <span x-text="selectedIds.length + ' محدد'"></span>
                <button type="button" x-on:click="bulk('done')">Mark Done</button>
                <button type="button" x-on:click="bulk('reopen')">Reopen</button>
                <button type="button" x-on:click="bulk('create_crm')">إنشاء CRM</button>
                <select x-model="bulkAssignee">
                    <option value="">تعيين إلى…</option>
                    @foreach($agents as $agent)<option value="{{ $agent->id }}">{{ $agent->name }}</option>@endforeach
                </select>
                <button type="button" x-on:click="bulk('assign')" :disabled="!bulkAssignee">Assign</button>
            </div>
            <table class="lc-table" id="lc-table">
                <thead>
                <tr>
                    <th><input type="checkbox" x-on:change="toggleAll($event)"></th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Channel</th>
                    <th>Stage</th>
                    <th>Priority</th>
                    <th>Labels</th>
                    <th>Assigned</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Reminder</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    <tr data-lead-id="{{ $row['id'] }}">
                        <td><input type="checkbox" value="{{ $row['id'] }}" x-on:change="toggleId({{ $row['id'] }}, $event.target.checked)"></td>
                        <td>{{ $row['last_at'] ?: $row['created_at'] }}</td>
                        <td>
                            <a href="{{ $lcRoute(['view' => null, 'lead' => $row['id']]) }}" class="font-bold text-[#0084FF]">{{ $row['display_name'] }}</a>
                            <div class="text-[10px] text-slate-500">{{ \Illuminate\Support\Str::limit($row['preview'] ?: '', 40) }}</div>
                        </td>
                        <td>{{ $row['platform_label'] }}</td>
                        <td data-role="stage">{{ $row['stage_label'] }}</td>
                        <td data-role="priority">{{ $row['priority_label'] }}</td>
                        <td data-role="labels">{{ implode(', ', $row['labels'] ?? []) }}</td>
                        <td data-role="assignee">{{ $row['assignee_name'] ?: '—' }}</td>
                        <td dir="ltr">{{ $row['phone'] ?: '—' }}</td>
                        <td>
                            {{ $row['is_done'] ? 'Done' : 'Open' }}
                            @if(($row['unread'] ?? 0) > 0)
                                · {{ $row['unread'] }} unread
                            @endif
                        </td>
                        <td>{{ $row['reminder_human'] ?: '—' }}</td>
                        <td><a href="{{ $row['inbox_url'] }}" class="lc-link">Message</a></td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center py-10 text-slate-500">لا توجد leads</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

    {{-- LIST + DETAIL --}}
    @else
        <div class="lc-shell">
            <aside class="lc-list">
                <div class="lc-list__body" id="lc-rows">
                    @forelse($rows as $row)
                        <a href="{{ $lcRoute(['lead' => $row['id']]) }}"
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
                                <p class="lc-row__meta">{{ $row['stage_label'] }} · {{ $row['platform_label'] }} @if($row['assignee_name']) · {{ $row['assignee_name'] }} @endif</p>
                                <div class="lc-row__bottom">
                                    <p class="lc-row__preview" data-role="preview">{{ $row['preview'] ?: '—' }}</p>
                                    <div class="lc-badges" data-role="badges">
                                        @if($row['in_crm'])<span class="lc-mini crm">CRM</span>@endif
                                        @if($row['is_real_phone'])<span class="lc-mini phone">Phone</span>@endif
                                        @if($row['is_done'])<span class="lc-mini done">Done</span>@endif
                                        @if(($row['unread'] ?? 0) > 0)<span class="lc-unread">{{ $row['unread'] }}</span>@endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="lc-empty"><i class="fas fa-user-plus"></i><p>لا توجد leads</p></div>
                    @endforelse
                </div>
            </aside>

            <section class="lc-detail">
                <template x-if="!detail">
                    <div class="lc-empty lc-empty--center">
                        <div class="lc-mark lc-mark--xl"><i class="fas fa-user-plus"></i></div>
                        <p class="font-black text-lg">Lead Center</p>
                        <p class="text-sm text-slate-500">اختر lead لإدارة المرحلة والتعيين والتسميات والرد.</p>
                    </div>
                </template>
                <template x-if="detail">
                    <div class="lc-detail__wrap">
                        <div class="lc-detail__head">
                            <a href="{{ $lcRoute() }}" class="lg:hidden lc-icon-btn"><i class="fas fa-arrow-right"></i></a>
                            <div class="lc-avatar lc-avatar--lg">
                                <template x-if="detail.profile_pic"><img :src="detail.profile_pic" alt=""></template>
                                <template x-if="!detail.profile_pic"><span x-text="(detail.display_name || '?').charAt(0)"></span></template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="lc-detail__name" x-text="detail.display_name"></h2>
                                <p class="lc-detail__sub">
                                    <span x-text="detail.platform_label"></span> ·
                                    <span x-text="detail.page_name || '—'"></span> ·
                                    <span x-text="detail.source"></span>
                                </p>
                            </div>
                            <button type="button" class="lc-btn-ghost" x-on:click="toggleDone()" :disabled="saving">
                                <i class="fas" :class="detail.is_done ? 'fa-envelope-open-text' : 'fa-check-circle'"></i>
                                <span x-text="detail.is_done ? 'Reopen' : 'Mark Done'"></span>
                            </button>
                            <a :href="detail.inbox_url" class="lc-btn-primary"><i class="fas fa-comments"></i> Message</a>
                        </div>

                        <div class="lc-detail__body">
                            <div class="lc-actions-grid">
                                <label>Stage
                                    <select x-model="stageValue" x-on:change="saveStage()">
                                        @foreach($stages as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                    </select>
                                </label>
                                <label>Assigned to
                                    <select x-model="assigneeId" x-on:change="assignAgent()">
                                        <option value="">Unassigned</option>
                                        @foreach($agents as $agent)<option value="{{ $agent->id }}">{{ $agent->name }}</option>@endforeach
                                    </select>
                                </label>
                                <label>Priority
                                    <select x-model="priorityValue" x-on:change="savePriority()">
                                        @foreach($priorities as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                    </select>
                                </label>
                                <label>Reminder
                                    <div class="lc-form-row">
                                        <input type="datetime-local" x-model="reminderValue">
                                        <button type="button" class="lc-btn-ghost" x-on:click="saveReminder()">حفظ</button>
                                    </div>
                                </label>
                            </div>

                            <div class="lc-card">
                                <h3>Contact information</h3>
                                <form class="lc-form" x-on:submit.prevent="saveContact()">
                                    <input type="text" x-model="contactName" placeholder="Name">
                                    <input type="text" x-model="contactPhone" placeholder="Phone" dir="ltr">
                                    <input type="email" x-model="contactEmail" placeholder="Email" dir="ltr">
                                    <textarea x-model="contactNotes" rows="2" placeholder="Internal note"></textarea>
                                    <div class="lc-form-row">
                                        <button type="submit" class="lc-btn-dark" :disabled="saving">Save</button>
                                        <button type="button" class="lc-btn-ghost" x-show="detail.can_request_phone" x-on:click="requestPhone()" :disabled="saving">Request phone</button>
                                        <button type="button" class="lc-btn-success" x-show="!detail.in_crm" x-on:click="createLead()" :disabled="saving">Create in CRM</button>
                                        <a x-show="detail.crm_url" :href="detail.crm_url" class="lc-btn-ghost" target="_blank">Open CRM</a>
                                    </div>
                                </form>
                            </div>

                            <div class="lc-card">
                                <h3>Labels</h3>
                                <div class="lc-labels">
                                    <template x-for="lb in (detail.labels || [])" :key="lb">
                                        <button type="button" class="lc-label" x-on:click="removeLabel(lb)" x-text="lb + ' ×'"></button>
                                    </template>
                                </div>
                                <div class="lc-form-row mt-2">
                                    <input type="text" x-model="newLabel" placeholder="Add label" x-on:keydown.enter.prevent="addLabel()">
                                    <button type="button" class="lc-btn-primary" x-on:click="addLabel()">Add</button>
                                </div>
                                <div class="lc-suggest">
                                    @foreach($suggestedLabels as $sug)
                                        <button type="button" x-on:click="addSuggestedLabel(@js($sug))">{{ $sug }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="lc-card">
                                <h3>About</h3>
                                <dl class="lc-dl">
                                    <div><dt>Username</dt><dd x-text="detail.username || '—'"></dd></div>
                                    <div><dt>Meta ID</dt><dd class="mono" x-text="detail.participant_id || '—'"></dd></div>
                                    <div><dt>Page</dt><dd x-text="detail.page_name || '—'"></dd></div>
                                    <div><dt>Messages</dt><dd x-text="detail.message_count || 0"></dd></div>
                                    <div><dt>Created</dt><dd x-text="detail.created_at || '—'"></dd></div>
                                    <div><dt>Last message</dt><dd x-text="detail.last_human || '—'"></dd></div>
                                    <div><dt>CRM stage</dt><dd x-text="detail.crm_stage_label || '—'"></dd></div>
                                </dl>
                            </div>

                            <div class="lc-card">
                                <h3>Message lead</h3>
                                <div class="lc-thread">
                                    <template x-for="m in (detail.recent_messages || [])" :key="m.id">
                                        <div class="lc-thread-msg" :class="m.direction === 'outbound' ? 'out' : 'in'">
                                            <p x-text="m.body"></p>
                                            <small><span x-text="m.author || ''"></span> <span x-text="m.sent_at_human"></span></small>
                                        </div>
                                    </template>
                                </div>
                                <form class="lc-form-row mt-2" x-on:submit.prevent="sendReply()">
                                    <input type="text" x-model="replyBody" placeholder="اكتب رسالة…" class="flex-1">
                                    <button type="submit" class="lc-btn-primary" :disabled="saving || !replyBody.trim()">Send</button>
                                </form>
                            </div>

                            <p class="lc-msg err" x-show="error" x-text="error"></p>
                            <p class="lc-msg ok" x-show="ok" x-text="ok"></p>
                        </div>
                    </div>
                </template>
            </section>
        </div>
    @endif
</div>

@push('styles')
<style>
.lc-page{--lc-blue:#0084FF;--lc-ink:#1c2b33;--lc-muted:#65676b;--lc-line:#e4e6eb;--lc-bg:#f0f2f5;--lc-ig:#E1306C;display:flex;flex-direction:column;height:calc(100vh - 4rem);min-height:0;overflow:hidden;background:#fff;color:var(--lc-ink)}
main:has(.lc-page){overflow:hidden!important} main:has(.lc-page)>div:last-child{padding:0!important;max-width:none!important}
.lc-topbar{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.7rem .9rem;border-bottom:1px solid var(--lc-line);flex-wrap:wrap}
.lc-topbar__brand{display:flex;align-items:center;gap:.65rem}.lc-topbar__title{font-size:1.05rem;font-weight:800}.lc-topbar__sub{font-size:10px;color:var(--lc-muted)}
.lc-topbar__actions{display:flex;gap:.4rem;align-items:center;flex-wrap:wrap}
.lc-mark{width:2.25rem;height:2.25rem;border-radius:.75rem;background:#e7f3ff;color:var(--lc-blue);display:flex;align-items:center;justify-content:center}
.lc-mark--xl{width:4.5rem;height:4.5rem;font-size:1.75rem;border-radius:1.25rem;margin-bottom:.75rem}
.lc-chip{font-size:11px;font-weight:800;padding:.3rem .55rem;border-radius:999px;background:var(--lc-bg)}
.lc-chip--live{background:#ecfdf5;color:#047857;display:inline-flex;align-items:center;gap:.35rem}
.lc-chip--live i{font-size:7px;color:#10b981;animation:lcPulse 1.4s ease-in-out infinite}
.lc-chip--live.is-stale{background:#f3f4f6;color:#6b7280}.lc-chip--live.is-stale i{animation:none}
@keyframes lcPulse{0%,100%{opacity:1}50%{opacity:.35}}
.lc-view-switch{display:inline-flex;border:1px solid var(--lc-line);border-radius:.65rem;overflow:hidden}
.lc-view-switch a{padding:.35rem .55rem;font-size:11px;font-weight:800;color:var(--lc-muted);text-decoration:none}
.lc-view-switch a.is-active{background:#e7f3ff;color:var(--lc-blue)}
.lc-banner{margin:.5rem .75rem 0;padding:.55rem .75rem;border-radius:.75rem;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:12px;font-weight:600}
.lc-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.45rem;padding:.65rem .75rem;border-bottom:1px solid var(--lc-line)}
@media(min-width:900px){.lc-stats{grid-template-columns:repeat(6,minmax(0,1fr))}}
.lc-stat{display:flex;align-items:center;gap:.55rem;padding:.55rem .65rem;border-radius:.85rem;background:var(--lc-bg);text-decoration:none;color:var(--lc-ink);border:1px solid transparent}
.lc-stat strong{display:block;font-size:1rem;font-weight:900}.lc-stat span{font-size:10px;color:var(--lc-muted);font-weight:700}
.lc-stat.is-active{background:#e7f3ff;border-color:#cfe6ff;color:var(--lc-blue)}
.lc-toolbar{display:flex;flex-wrap:wrap;gap:.4rem;padding:.55rem .75rem;border-bottom:1px solid var(--lc-line);background:#fff}
.lc-toolbar input,.lc-toolbar select{border:1px solid var(--lc-line);border-radius:.55rem;padding:.4rem .55rem;font-size:11px;background:#fff}
.lc-toolbar input[type=search]{min-width:10rem;flex:1}
.lc-shell{flex:1;min-height:0;display:grid;grid-template-columns:1fr}
@media(min-width:1024px){.lc-shell{grid-template-columns:minmax(300px,380px) minmax(0,1fr)}}
.lc-list{display:grid;grid-template-rows:minmax(0,1fr);border-inline-end:1px solid var(--lc-line);min-height:0}
.lc-list__body{overflow-y:auto}
.lc-row{display:flex;gap:.65rem;padding:.7rem .75rem;border-bottom:1px solid #f0f2f5;text-decoration:none;color:inherit}
.lc-row:hover{background:#f7f8fa}.lc-row.is-active{background:#e7f3ff}.lc-row.is-unread .lc-row__name{font-weight:900}
.lc-avatar{width:2.6rem;height:2.6rem;border-radius:999px;background:#e7f3ff;color:var(--lc-blue);display:flex;align-items:center;justify-content:center;font-weight:900;position:relative;flex-shrink:0;overflow:hidden}
.lc-avatar img{width:100%;height:100%;object-fit:cover}.lc-avatar--lg{width:3rem;height:3rem}
.lc-plat{position:absolute;bottom:-2px;inset-inline-end:-2px;width:1rem;height:1rem;border-radius:999px;background:#fff;font-size:10px;display:flex;align-items:center;justify-content:center}
.lc-plat.msgr{color:var(--lc-blue)}.lc-plat.ig{color:var(--lc-ig)}
.lc-row__main{min-width:0;flex:1}.lc-row__top,.lc-row__bottom{display:flex;justify-content:space-between;gap:.5rem;align-items:baseline}
.lc-row__name{font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lc-row__top time,.lc-row__meta{font-size:10px;color:var(--lc-muted)}.lc-row__meta,.lc-row__preview{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lc-row__preview{font-size:12px;color:#8a8d91;flex:1}.lc-badges{display:flex;gap:.25rem;align-items:center}
.lc-mini{font-size:9px;font-weight:800;padding:.1rem .35rem;border-radius:999px}
.lc-mini.crm{background:#ecfdf5;color:#047857}.lc-mini.phone{background:#e7f3ff;color:#0084FF}.lc-mini.done{background:#f3f4f6;color:#4b5563}
.lc-unread{min-width:1.1rem;height:1.1rem;padding:0 .3rem;border-radius:999px;background:var(--lc-blue);color:#fff;font-size:10px;font-weight:800;display:inline-flex;align-items:center;justify-content:center}
.lc-detail{min-height:0;overflow:hidden;background:var(--lc-bg);display:block}
@media(max-width:1023px){.lc-page.has-selection .lc-list{display:none}.lc-page:not(.has-selection) .lc-detail{display:none}}
.lc-detail__wrap{height:100%;display:grid;grid-template-rows:auto minmax(0,1fr)}
.lc-detail__head{display:flex;align-items:center;gap:.65rem;padding:.75rem .9rem;background:#fff;border-bottom:1px solid var(--lc-line);flex-wrap:wrap}
.lc-detail__name{font-size:1rem;font-weight:900}.lc-detail__sub{font-size:11px;color:var(--lc-muted)}
.lc-detail__body{overflow-y:auto;padding:.9rem;display:flex;flex-direction:column;gap:.75rem}
.lc-actions-grid{display:grid;grid-template-columns:1fr 1fr;gap:.55rem}
@media(min-width:900px){.lc-actions-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
.lc-actions-grid label{display:flex;flex-direction:column;gap:.3rem;font-size:10px;font-weight:800;color:var(--lc-muted);background:#fff;border:1px solid var(--lc-line);border-radius:.85rem;padding:.55rem}
.lc-actions-grid select,.lc-actions-grid input{border:1px solid var(--lc-line);border-radius:.55rem;padding:.4rem;font-size:12px;color:var(--lc-ink);font-weight:600}
.lc-card{background:#fff;border:1px solid var(--lc-line);border-radius:1rem;padding:.9rem}
.lc-card h3{font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.04em;color:var(--lc-muted);margin-bottom:.55rem}
.lc-form,.lc-form-row{display:flex;flex-direction:column;gap:.45rem}.lc-form-row{flex-direction:row;align-items:center}
.lc-form input,.lc-form textarea,.lc-form-row input,.lc-form-row select{width:100%;border:1px solid var(--lc-line);border-radius:.75rem;padding:.55rem .7rem;font-size:12px;background:#fff}
.lc-btn-primary,.lc-btn-ghost,.lc-btn-dark,.lc-btn-success,.lc-icon-btn{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;border-radius:.7rem;font-size:11px;font-weight:800;padding:.45rem .7rem;cursor:pointer;border:1px solid var(--lc-line);text-decoration:none;background:#fff;color:var(--lc-ink)}
.lc-btn-primary{background:var(--lc-blue);color:#fff;border-color:var(--lc-blue)}.lc-btn-dark{background:var(--lc-ink);color:#fff}.lc-btn-success{background:#059669;color:#fff;border-color:#059669}
.lc-icon-btn{width:2.1rem;height:2.1rem;padding:0}.lc-link{color:var(--lc-blue);font-weight:800;font-size:12px;text-decoration:none}
.lc-labels{display:flex;flex-wrap:wrap;gap:.35rem}.lc-label{border:0;background:#e7f3ff;color:var(--lc-blue);border-radius:999px;padding:.25rem .55rem;font-size:11px;font-weight:800;cursor:pointer}
.lc-suggest{display:flex;flex-wrap:wrap;gap:.3rem;margin-top:.45rem}
.lc-suggest button{border:1px dashed var(--lc-line);background:#fff;border-radius:999px;padding:.2rem .5rem;font-size:10px;font-weight:700;color:var(--lc-muted);cursor:pointer}
.lc-dl{display:grid;gap:.4rem;font-size:12px}.lc-dl>div{display:flex;justify-content:space-between;gap:.75rem}.lc-dl dt{color:var(--lc-muted)}.lc-dl dd{font-weight:700;text-align:end}.lc-dl .mono{font-family:ui-monospace,monospace;font-size:10px}
.lc-thread{max-height:220px;overflow:auto;display:flex;flex-direction:column;gap:.4rem;background:var(--lc-bg);border-radius:.75rem;padding:.65rem}
.lc-thread-msg{max-width:85%;padding:.45rem .65rem;border-radius:.85rem;font-size:12px;background:#fff}
.lc-thread-msg.out{align-self:flex-end;background:var(--lc-blue);color:#fff}.lc-thread-msg small{display:block;opacity:.75;font-size:10px;margin-top:.15rem}
.lc-msg{font-size:11px;font-weight:700}.lc-msg.err{color:#dc2626}.lc-msg.ok{color:#059669}
.lc-empty{padding:2.5rem 1rem;text-align:center;color:var(--lc-muted)}.lc-empty--center{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center}
.mt-2{margin-top:.5rem}.flex-1{flex:1}
.lc-table-wrap{flex:1;min-height:0;overflow:auto;padding:.5rem .75rem}
.lc-table{width:100%;border-collapse:collapse;font-size:12px}
.lc-table th,.lc-table td{border-bottom:1px solid var(--lc-line);padding:.55rem .4rem;text-align:start;white-space:nowrap}
.lc-table th{position:sticky;top:0;background:#fff;font-size:10px;text-transform:uppercase;color:var(--lc-muted)}
.lc-bulk{display:flex;gap:.4rem;align-items:center;flex-wrap:wrap;padding:.45rem .6rem;margin-bottom:.4rem;background:#e7f3ff;border-radius:.75rem;font-size:12px;font-weight:700}
.lc-bulk button,.lc-bulk select{border:1px solid var(--lc-line);border-radius:.55rem;padding:.3rem .5rem;background:#fff;font-size:11px;font-weight:700}
.lc-pipe-summary{display:flex;align-items:baseline;gap:.4rem;padding:.45rem .85rem 0;font-size:12px;color:var(--lc-muted)}
.lc-pipe-summary strong{font-size:1.05rem;color:var(--lc-ink);font-variant-numeric:tabular-nums}
.lc-pipeline{flex:1;min-height:0;display:flex;gap:.55rem;overflow:auto;padding:.65rem .75rem}
.lc-pipe-col{min-width:280px;max-width:300px;background:var(--lc-bg);border-radius:.9rem;display:flex;flex-direction:column;max-height:100%}
.lc-pipe-col header{display:flex;justify-content:space-between;padding:.55rem .65rem;font-size:12px;font-weight:900}
.lc-pipe-body{overflow:auto;padding:0 .45rem .55rem;display:flex;flex-direction:column;gap:.4rem}
.lc-pipe-card{background:#fff;border:1px solid var(--lc-line);border-radius:.75rem;padding:.55rem;text-decoration:none;color:inherit;display:block}
.lc-pipe-card.is-done{opacity:.72}
.lc-pipe-card.prio-high{border-color:#f59e0b}
.lc-pipe-card.prio-urgent{border-color:#ef4444}
.lc-pipe-card__top{display:flex;justify-content:space-between;gap:.35rem;align-items:flex-start}
.lc-pipe-card .name{font-size:12px;font-weight:800;margin:0}
.lc-pipe-card .time{font-size:9px;color:var(--lc-muted);white-space:nowrap}
.lc-pipe-card .meta,.lc-pipe-card .preview{font-size:10px;color:var(--lc-muted);margin-top:.15rem}
.lc-pipe-card__fields{display:grid;grid-template-columns:1fr 1fr;gap:.25rem .35rem;margin-top:.4rem}
.lc-pipe-card__fields > div{min-width:0}
.lc-pipe-card__fields span{display:block;font-size:8px;font-weight:800;color:var(--lc-muted);text-transform:uppercase;letter-spacing:.02em}
.lc-pipe-card__fields b{display:block;font-size:10px;font-weight:700;color:var(--lc-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.lc-pipe-card__fields b.due{color:#b45309}
.lc-pipe-card .tags{display:flex;flex-wrap:wrap;gap:.2rem;margin-top:.35rem}
.lc-pipe-card .tags span{font-size:9px;font-weight:800;background:#e7f3ff;color:var(--lc-blue);border-radius:999px;padding:.1rem .35rem}
.lc-pipe-empty{font-size:11px;color:var(--lc-muted);text-align:center;padding:.75rem .25rem}
</style>
@endpush

@push('scripts')
<script>
function metaLeadCenter() {
    const base = @json(url('/admin/meta-social/leads'));
    return {
        viewMode: @json($viewMode),
        detail: {!! $detailJson !!},
        pollUrl: @json($pollUrl),
        bulkUrl: @json($bulkUrl),
        csrf: @json(csrf_token()),
        inboxVersion: '',
        saving: false,
        error: '',
        ok: '',
        contactName: '',
        contactPhone: '',
        contactEmail: '',
        contactNotes: '',
        assigneeId: '',
        stageValue: 'intake',
        priorityValue: 'normal',
        reminderValue: '',
        newLabel: '',
        replyBody: '',
        selectedIds: [],
        bulkAssignee: '',
        pollTimer: null,
        polling: false,
        init() {
            this.applyDetail(this.detail);
            this.pollTimer = setInterval(() => this.poll(), document.hidden ? 8000 : 2500);
            document.addEventListener('visibilitychange', () => {
                clearInterval(this.pollTimer);
                this.pollTimer = setInterval(() => this.poll(), document.hidden ? 8000 : 2500);
                if (!document.hidden) this.poll();
            });
        },
        urlsFor(id) {
            return {
                createLead: base + '/' + id + '/create-lead',
                assign: base + '/' + id + '/assign',
                contact: base + '/' + id + '/contact',
                stage: base + '/' + id + '/stage',
                priority: base + '/' + id + '/priority',
                reminder: base + '/' + id + '/reminder',
                labels: base + '/' + id + '/labels',
                done: base + '/' + id + '/done',
                requestPhone: base + '/' + id + '/request-phone',
                reply: base + '/' + id + '/reply',
            };
        },
        applyDetail(d) {
            if (!d) return;
            this.detail = d;
            this.contactName = d.display_name || '';
            this.contactPhone = d.phone || '';
            this.contactEmail = d.email || '';
            this.contactNotes = d.notes || '';
            this.assigneeId = d.assigned_to ? String(d.assigned_to) : '';
            this.stageValue = d.stage || 'intake';
            this.priorityValue = d.priority || 'normal';
            this.reminderValue = d.reminder_at || '';
            this.actionUrls = this.urlsFor(d.id);
        },
        actionUrls: {},
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
                const badges = row.querySelector('[data-role="badges"]');
                if (name) name.textContent = r.display_name || '';
                if (time) time.textContent = r.last_time || '';
                if (preview) preview.textContent = r.preview || '—';
                row.classList.toggle('is-unread', Number(r.unread) > 0);
                if (badges) {
                    let html = '';
                    if (r.in_crm) html += '<span class="lc-mini crm">CRM</span>';
                    if (r.is_real_phone) html += '<span class="lc-mini phone">Phone</span>';
                    if (r.is_done) html += '<span class="lc-mini done">Done</span>';
                    if (Number(r.unread) > 0) html += '<span class="lc-unread">' + Number(r.unread) + '</span>';
                    badges.innerHTML = html;
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
            if (!this.actionUrls.assign) return;
            await this.postJson(this.actionUrls.assign, {
                assigned_to: this.assigneeId ? Number(this.assigneeId) : null,
            });
        },
        async saveStage() {
            if (!this.actionUrls.stage) return;
            await this.postJson(this.actionUrls.stage, { stage: this.stageValue });
        },
        async savePriority() {
            if (!this.actionUrls.priority) return;
            await this.postJson(this.actionUrls.priority, { priority: this.priorityValue });
        },
        async saveReminder() {
            if (!this.actionUrls.reminder) return;
            await this.postJson(this.actionUrls.reminder, { reminder_at: this.reminderValue || null });
        },
        async saveLabels(labels) {
            if (!this.actionUrls.labels) return;
            await this.postJson(this.actionUrls.labels, { labels });
        },
        async addLabel() {
            const lb = (this.newLabel || '').trim();
            if (!lb) return;
            const labels = [...(this.detail.labels || [])];
            if (!labels.includes(lb)) labels.push(lb);
            this.newLabel = '';
            await this.saveLabels(labels);
        },
        async addSuggestedLabel(lb) {
            const labels = [...(this.detail.labels || [])];
            if (!labels.includes(lb)) labels.push(lb);
            await this.saveLabels(labels);
        },
        async removeLabel(lb) {
            await this.saveLabels((this.detail.labels || []).filter((x) => x !== lb));
        },
        async createLead() {
            if (!this.actionUrls.createLead) return;
            const data = await this.postJson(this.actionUrls.createLead, {
                name: this.contactName, phone: this.contactPhone, email: this.contactEmail,
                assigned_to: this.assigneeId ? Number(this.assigneeId) : null,
            });
            if (data?.lead_id) this.ok = 'CRM Lead #' + data.lead_id;
        },
        async toggleDone() {
            if (!this.actionUrls.done) return;
            await this.postJson(this.actionUrls.done, { done: !this.detail.is_done });
        },
        async requestPhone() {
            if (!this.actionUrls.requestPhone) return;
            const data = await this.postJson(this.actionUrls.requestPhone, {});
            if (data) this.ok = 'تم إرسال طلب الرقم';
        },
        async sendReply() {
            if (!this.actionUrls.reply || !this.replyBody.trim()) return;
            const data = await this.postJson(this.actionUrls.reply, { body: this.replyBody.trim() });
            if (data) this.replyBody = '';
        },
        toggleId(id, on) {
            if (on) { if (!this.selectedIds.includes(id)) this.selectedIds.push(id); }
            else this.selectedIds = this.selectedIds.filter((x) => x !== id);
        },
        toggleAll(e) {
            const on = e.target.checked;
            this.selectedIds = on ? Array.from(document.querySelectorAll('#lc-table tbody input[type=checkbox]')).map((el) => Number(el.value)) : [];
            document.querySelectorAll('#lc-table tbody input[type=checkbox]').forEach((el) => { el.checked = on; });
        },
        async bulk(action) {
            if (!this.selectedIds.length) return;
            const body = { ids: this.selectedIds, action };
            if (action === 'assign') body.assigned_to = Number(this.bulkAssignee);
            const res = await fetch(this.bulkUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            if (data.success) location.reload();
            else alert(data.error || 'فشلت العملية');
        },
        async poll() {
            if (!this.pollUrl || this.polling || this.viewMode === 'pipeline') return;
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
