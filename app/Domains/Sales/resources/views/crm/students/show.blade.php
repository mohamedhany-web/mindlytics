@extends('sales::crm.layout')

@section('title', $student->name)

@use('App\Domains\Sales\Models\SalesLead')
@use('App\Domains\Sales\Models\SalesActivity')
@use('App\Domains\Sales\Models\SalesPlannedActivity')
@php
    $adminUrl = $crm->isAdmin() && Route::has('admin.users.show') ? route('admin.users.show', $student->id) : null;
    $openLeads = $leads->filter(fn ($l) => $l->isOpen())->count();
    $enrollmentStatus = ['pending' => 'في الانتظار', 'active' => 'نشط', 'completed' => 'مكتمل', 'suspended' => 'موقوف'];
    $orderStatus = ['pending' => 'في الانتظار', 'approved' => 'معتمد', 'rejected' => 'مرفوض'];
    $firstLead = $leads->first(fn ($l) => $crm->canSee($l));
@endphp

@section('content')
<div x-data="{ rel: { courses: true, orders: true, leads: true, planned: true, history: false } }" style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <a href="{{ route($crm->route('students.index')) }}" aria-label="رجوع للطلاب" style="width: 28px; height: 28px; border: 1px solid #534D64; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #534D64"><i class="fas fa-chevron-right"></i></a>
        <h1 style="margin: 0; font-size: 26px; font-weight: 400">{{ $student->name }}</h1>
        <span style="flex: 1"></span>
        @if($student->phone && ($wa = \App\Domains\Sales\Support\PhoneKey::whatsappUrl($student->phone)))
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="crm-btn crm-btn-outline"><i class="fab fa-whatsapp"></i> واتساب</a>
        @endif
        <a href="{{ route($crm->route('activities.create'), array_filter(['kind' => 'call', 'student' => $firstLead ? null : $student->id, 'lead' => $firstLead?->id, 'redirect_to' => url()->current()])) }}" class="crm-btn crm-btn-primary">＋ جدولة مكالمة</a>
        @if($adminUrl)<a href="{{ $adminUrl }}" class="crm-btn crm-btn-slate">الملف في الإدارة</a>@endif
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <div style="flex: 999 1 680px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">الهاتف</span><span class="v" dir="ltr" style="text-align: end">{{ $student->phone ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">الإيميل</span><span class="v">{{ $student->email }}</span></div>
                    <div class="crm-field"><span class="k">اتسجّل</span><span class="v">{{ $student->created_at?->format('d/m/Y') }}</span></div>
                    <div class="crm-field"><span class="k">الحساب</span><span class="v {{ $student->is_active ? 'crm-good' : 'crm-bad' }}">{{ $student->is_active ? 'نشط' : 'موقوف' }}</span></div>
                    <div class="crm-field" style="grid-column: 1 / -1"><span class="k">موظف المبيعات</span><span class="v">{{ $leads->pluck('assignee.name')->filter()->unique()->implode('، ') ?: '—' }}</span></div>
                </div>
            </section>

            <section class="crm-card">
                <div class="crm-rel" style="display: flex; flex-wrap: wrap; align-items: center; gap: 6px; padding: 10px 14px; border-bottom: 1px solid #E5E7EB">
                    <h2 style="margin: 0; font-size: 14px; font-weight: 700; flex: 1 1 100px">العلاقات</h2>
                    <button type="button" :class="rel.courses && 'is-active'" @click="rel.courses = ! rel.courses" :aria-pressed="rel.courses">الكورسات {{ $enrollments->count() }}</button>
                    <button type="button" :class="rel.orders && 'is-active'" @click="rel.orders = ! rel.orders" :aria-pressed="rel.orders">الطلبات {{ $orders->count() }}</button>
                    <button type="button" :class="rel.leads && 'is-active'" @click="rel.leads = ! rel.leads" :aria-pressed="rel.leads">العملاء {{ $leads->count() }}</button>
                    <button type="button" :class="rel.planned && 'is-active'" @click="rel.planned = ! rel.planned" :aria-pressed="rel.planned">الأنشطة {{ $planned->count() }}</button>
                    <button type="button" :class="rel.history && 'is-active'" @click="rel.history = ! rel.history" :aria-pressed="rel.history">السجل {{ $history->count() }}</button>
                </div>

                <div class="crm-subpanel" x-show="rel.courses">
                    <div class="crm-subpanel-head"><h3>الكورسات</h3></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 520px">
                            <thead><tr><th>الكورس</th><th>الحالة</th><th>التقدّم</th><th>السعر</th><th>اتسجّل</th></tr></thead>
                            <tbody>
                                @forelse($enrollments as $e)
                                    <tr><td>{{ $e->course?->title ?? '—' }}</td><td>{{ $enrollmentStatus[$e->status] ?? $e->status }}</td><td>{{ number_format((float) $e->progress, 0) }}٪</td><td>{{ number_format((float) $e->final_price, 0) }}</td><td class="crm-muted">{{ $e->enrolled_at?->format('d/m/Y') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="crm-muted" style="text-align: center">مفيش كورسات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="crm-subpanel" x-show="rel.orders">
                    <div class="crm-subpanel-head"><h3>الطلبات والمدفوعات</h3></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 520px">
                            <thead><tr><th>الطلب</th><th>الكورس</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                @forelse($orders as $o)
                                    <tr><td style="font-weight: 700">#{{ $o->id }}</td><td>{{ $o->course?->title ?? '—' }}</td><td>{{ number_format((float) $o->amount, 2) }}</td>
                                        <td class="{{ $o->status === 'approved' ? 'crm-good' : ($o->status === 'rejected' ? 'crm-bad' : '') }}">{{ $o->payment?->status === 'refunded' ? 'مسترد' : ($orderStatus[$o->status] ?? $o->status) }}</td>
                                        <td class="crm-muted">{{ ($o->approved_at ?? $o->created_at)?->format('d/m/Y') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="crm-muted" style="text-align: center">مفيش طلبات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="crm-subpanel" x-show="rel.leads">
                    <div class="crm-subpanel-head"><h3>العملاء المحتملون المرتبطون</h3></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 520px">
                            <thead><tr><th>العميل</th><th>المرحلة</th><th>الموظف</th><th>الصفقة</th></tr></thead>
                            <tbody>
                                @forelse($leads as $l)
                                    <tr>
                                        <td>@if($crm->canSee($l))<a class="crm-link" href="{{ route($crm->route('leads.show'), $l->id) }}">{{ $l->name }}</a>@else{{ $l->name }}@endif</td>
                                        <td>{{ SalesLead::stageLabel((string) $l->stage) }}</td>
                                        <td>{{ $l->assignee?->name ?? '—' }}</td>
                                        <td>@if($crm->canSee($l) && ($l->order_id || $l->isWonLike()))<a class="crm-link" href="{{ route($crm->route('deals.show'), $l->id) }}">فتح الصفقة</a>@else—@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="crm-muted" style="text-align: center">مفيش عملاء مرتبطين في نطاقك.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="crm-subpanel" x-show="rel.planned">
                    <div class="crm-subpanel-head"><h3>الأنشطة</h3><span style="flex: 1"></span>
                        @foreach(SalesPlannedActivity::KINDS as $k => $v)
                            <a class="crm-btn crm-btn-outline crm-btn-sm" href="{{ route($crm->route('activities.create'), array_filter(['kind' => $k, 'student' => $firstLead ? null : $student->id, 'lead' => $firstLead?->id, 'redirect_to' => url()->current()])) }}">＋ {{ $v }}</a>
                        @endforeach
                    </div>
                    @include('sales::crm.partials.planned-table', ['items' => $planned])
                </div>

                <div class="crm-subpanel" x-show="rel.history">
                    <div class="crm-subpanel-head"><h3>السجل (اللي حصل)</h3></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 520px">
                            <thead><tr><th>النشاط</th><th>العميل</th><th>بواسطة</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                @forelse($history as $h)
                                    <tr><td>{{ $h->title ?: SalesActivity::typeLabel((string) $h->type) }}{{ $h->outcome ? ' · '.SalesActivity::outcomeLabel($h->outcome) : '' }}</td><td>{{ $h->lead?->name }}</td><td>{{ $h->user?->name }}</td><td class="crm-muted">{{ $h->created_at?->format('d/m/Y H:i') }}</td></tr>
                                @empty
                                    <tr><td colspan="4" class="crm-muted" style="text-align: center">لا يوجد نشاط بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <aside style="flex: 1 1 280px; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr))">
                <div class="crm-stat"><b class="crm-good">{{ number_format($paidTotal, 0) }}</b><span>المدفوع (ج.م)</span></div>
                <div class="crm-stat" style="border-inline-start: 1px solid #E5E7EB"><b>{{ $enrollments->count() }}</b><span>كورسات</span></div>
                <div class="crm-stat" style="border-top: 1px solid #E5E7EB"><b>{{ $openLeads }}</b><span>فرص مفتوحة</span></div>
                <div class="crm-stat" style="border-top: 1px solid #E5E7EB; border-inline-start: 1px solid #E5E7EB"><b>{{ $planned->where('status', 'planned')->count() }}</b><span>أنشطة جاية</span></div>
            </section>
            <p class="crm-muted" style="margin: 0; font-size: 12px; line-height: 1.8">بيانات الطالب نفسها (الاسم، الإيميل، الكورسات) بتتعدّل من شاشات الإدارة. هنا للمتابعة والبيع الإضافي (Upsell).</p>
        </aside>
    </div>
</div>
@endsection
