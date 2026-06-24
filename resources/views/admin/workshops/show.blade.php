@extends('layouts.admin')

@section('title', $workshop->title)
@section('header', 'تفاصيل الورشة')

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'converted' => 0, 'pending_leads' => 0, 'checked_in' => 0, 'email_pending' => 0];
    $leadFilter = $leadFilter ?? 'all';
    $registeredCount = $stats['total'];
    $total = $workshop->max_seats ?: null;
    $remaining = $workshop->remaining_seats;
    $statCards = [
        ['label' => 'إجمالي المسجلين', 'value' => number_format($stats['total']), 'icon' => 'fas fa-users', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'في انتظار الترحيل', 'value' => number_format($stats['pending_leads']), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'مُرحَّل للمبيعات', 'value' => number_format($stats['converted']), 'icon' => 'fas fa-right-left', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'تم الحضور', 'value' => number_format($stats['checked_in']), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        ['label' => 'إيميلات معلّقة', 'value' => number_format($stats['email_pending']), 'icon' => 'fas fa-envelope', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-2xl font-black text-slate-900">{{ $workshop->title }}</h2>
                    @if($workshop->is_active)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">نشطة</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">غير نشطة</span>
                    @endif
                </div>
                <p class="text-sm text-slate-600">إدارة التسجيلات، التواصل، وترحيل العملاء الجدد فقط إلى فريق المبيعات</p>
                <a href="{{ route('public.workshops.show', $workshop->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-2">
                    <i class="fas fa-external-link-alt"></i>
                    {{ route('public.workshops.show', $workshop->slug) }}
                </a>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> القائمة
                </a>
                <a href="{{ route('admin.workshops.edit', $workshop) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('admin.workshops.export', $workshop) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
                <button type="button" @click="$dispatch('open-checkin-modal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    <i class="fas fa-qrcode"></i> حضور QR
                </button>
                @if($workshop->is_active)
                    <form action="{{ route('admin.workshops.deactivate', $workshop) }}" method="POST" onsubmit="return confirm('إيقاف الورشة؟');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold">
                            <i class="fas fa-stop-circle"></i> إيقاف
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.workshops.activate', $workshop) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold">
                            <i class="fas fa-play-circle"></i> تفعيل
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            @foreach($statCards as $card)
                <div class="rounded-xl bg-white border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-0 xl:divide-x xl:divide-slate-100">
            {{-- معلومات الورشة --}}
            <aside class="xl:col-span-3 p-5 sm:p-6 space-y-4 border-b xl:border-b-0 border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i> بيانات الورشة
                </h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500">التاريخ</dt>
                        <dd class="font-semibold text-slate-800 mt-0.5">
                            @if($workshop->starts_at)
                                {{ $workshop->starts_at->format('Y-m-d H:i') }}
                                @if($workshop->ends_at)
                                    <span class="block text-xs text-slate-500 font-normal">إلى {{ $workshop->ends_at->format('Y-m-d H:i') }}</span>
                                @endif
                            @else
                                <span class="text-slate-400">غير محدد</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">المقاعد</dt>
                        <dd class="font-semibold text-slate-800 mt-0.5">
                            @if($total)
                                {{ $registeredCount }} / {{ $total }}
                                <span class="block text-xs text-slate-500 font-normal">متبقي: {{ $remaining }}</span>
                            @else
                                غير محدود
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">نوع الحضور</dt>
                        <dd class="mt-1">
                            @if($workshop->mode === 'online')
                                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">أونلاين</span>
                            @elseif($workshop->mode === 'offline')
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">أوفلاين</span>
                            @else
                                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">اختيار الطالب</span>
                            @endif
                        </dd>
                    </div>
                </dl>
                @if($workshop->description)
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-600 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $workshop->description }}</p>
                    </div>
                @endif
            </aside>

            <div class="xl:col-span-9 divide-y divide-slate-100">
                {{-- ترحيل للمبيعات --}}
                <div class="p-5 sm:p-6 bg-gradient-to-br from-blue-50/80 to-white">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <i class="fas fa-right-left text-blue-600"></i>
                                ترحيل للمبيعات (Leads)
                            </h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-xl">
                                يُرحَّل <strong>المسجّلون الجدد فقط</strong>. من سبق ترحيلهم يظهرون بعلامة «مُرحَّل» ولن يُعاد توزيعهم.
                                @if($stats['pending_leads'] > 0)
                                    <span class="text-amber-700 font-semibold">({{ $stats['pending_leads'] }} جاهز للترحيل الآن)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <form id="convert-to-leads-form" action="{{ route('admin.workshops.convert-to-leads', $workshop) }}" method="POST"
                          class="rounded-xl border border-blue-200 bg-white p-4 space-y-3 max-w-2xl"
                          data-pending="{{ $stats['pending_leads'] }}">
                        @csrf
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-2">موظفو المبيعات</p>
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" id="select-all-reps" class="text-[11px] font-bold text-blue-700 hover:underline">تحديد الكل</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" id="clear-all-reps" class="text-[11px] font-bold text-slate-500 hover:underline">إلغاء التحديد</button>
                            </div>
                            <div id="convert-assigned-to-list" class="max-h-32 overflow-y-auto grid sm:grid-cols-2 gap-1 rounded-lg border border-slate-200 p-2">
                                @foreach(($salesReps ?? collect()) as $rep)
                                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer px-1 py-0.5 rounded hover:bg-slate-50">
                                        <input type="checkbox" name="assigned_to[]" value="{{ $rep->id }}" class="convert-rep-checkbox rounded border-slate-300 text-blue-600">
                                        <span>{{ $rep->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">مجموعة العملاء (اختياري)</label>
                            <select name="sales_lead_group_id" id="convert-lead-group" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-blue-500/30">
                                <option value="">بدون مجموعة — أو اختر مجموعة مشتركة</option>
                            </select>
                        </div>
                        <button type="submit" @disabled($stats['pending_leads'] === 0)
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2.5 text-sm font-bold text-white shadow-md">
                            <i class="fas fa-share-nodes"></i>
                            <span>ترحيل الجدد فقط ({{ $stats['pending_leads'] }})</span>
                        </button>
                    </form>
                </div>

                {{-- التواصل --}}
                <div class="p-5 sm:p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-paper-plane text-slate-600"></i> التواصل مع المسجلين
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('admin.workshops.send-acceptance', $workshop) }}" class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/50">
                            @csrf
                            <p class="text-xs font-bold text-slate-800"><i class="fas fa-envelope-open-text text-blue-600 ml-1"></i> إيميل القبول</p>
                            <div class="flex flex-wrap gap-3 text-xs">
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="all" checked class="text-blue-600"> الكل</label>
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="email" class="text-blue-600"> بريد محدد</label>
                            </div>
                            <input type="email" name="email" placeholder="example@mail.com" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-amber-700">متبقي: {{ $emailPendingCount ?? 0 }}</span>
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold">إرسال</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.workshops.send-whatsapp', $workshop) }}" class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/50">
                            @csrf
                            <p class="text-xs font-bold text-slate-800"><i class="fab fa-whatsapp text-green-600 ml-1"></i> واتساب</p>
                            <div class="flex flex-wrap gap-3 text-xs">
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="all" checked class="text-green-600"> كل الأرقام</label>
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="phone" class="text-green-600"> رقم محدد</label>
                            </div>
                            <input type="text" name="phone" placeholder="2010xxxxxxx" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                            <textarea name="message" rows="2" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs" placeholder="نص الرسالة…"></textarea>
                            <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold">فتح واتساب</button>
                        </form>
                    </div>
                </div>

                {{-- جدول المسجلين --}}
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-table text-blue-600"></i>
                            سجل المسجلين
                            <span class="text-xs font-normal text-slate-500">({{ $registrations->total() }} في الصفحة)</span>
                        </h3>
                        <form method="GET" action="{{ route('admin.workshops.show', $workshop) }}" class="flex flex-wrap items-center gap-2">
                            <select name="lead_status" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold">
                                <option value="all" @selected($leadFilter === 'all')>كل الترحيل</option>
                                <option value="pending" @selected($leadFilter === 'pending')>في انتظار الترحيل</option>
                                <option value="converted" @selected($leadFilter === 'converted')>مُرحَّل فقط</option>
                            </select>
                            <select name="attendance_mode" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold">
                                <option value="all" @selected(($filterMode ?? 'all') === 'all')>كل الحضور</option>
                                <option value="online" @selected(($filterMode ?? '') === 'online')>أونلاين</option>
                                <option value="offline" @selected(($filterMode ?? '') === 'offline')>أوفلاين</option>
                            </select>
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold">فلترة</button>
                        </form>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3 text-[11px]">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> مُرحَّل للمبيعات
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> في انتظار الترحيل
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">#</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الترحيل</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الاسم</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التواصل</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الحضور</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">ملاحظات</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التسجيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($registrations as $reg)
                                    @php
                                        $converted = $reg->isConvertedToLead();
                                    @endphp
                                    <tr class="{{ $converted ? 'bg-emerald-50/40' : 'hover:bg-slate-50/80' }}">
                                        <td class="px-3 py-3 text-xs text-slate-500">{{ $reg->id }}</td>
                                        <td class="px-3 py-3 text-xs whitespace-nowrap">
                                            @if($converted)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-[10px]">
                                                    <i class="fas fa-check"></i> مُرحَّل
                                                </span>
                                                @if($reg->converted_to_lead_at)
                                                    <div class="text-[10px] text-emerald-700 mt-1">{{ $reg->converted_to_lead_at->format('m-d H:i') }}</div>
                                                @endif
                                                @if($reg->salesLead)
                                                    <div class="text-[10px] text-slate-600 mt-0.5">
                                                        {{ $reg->salesLead->assignee->name ?? '—' }}
                                                    </div>
                                                    <a href="{{ route('admin.sales.leads.show', $reg->salesLead) }}" class="text-[10px] text-blue-600 hover:underline">عرض Lead</a>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 font-bold text-[10px]">
                                                    <i class="fas fa-clock"></i> جديد
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-slate-900">{{ $reg->name }}</td>
                                        <td class="px-3 py-3 text-xs text-slate-700 space-y-1">
                                            @if($reg->email)
                                                <div><i class="fas fa-envelope text-slate-400 ml-1"></i>{{ $reg->email }}</div>
                                            @endif
                                            @if($reg->phone)
                                                <div><i class="fas fa-phone text-slate-400 ml-1"></i>{{ $reg->phone }}</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-xs">
                                            @php
                                                $mode = $reg->attendance_mode === 'offline' ? 'أوفلاين' : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—');
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold">{{ $mode }}</span>
                                            @if($reg->checked_in_at)
                                                <div class="text-[10px] text-emerald-600 mt-1"><i class="fas fa-check-circle"></i> {{ $reg->checked_in_at->format('m-d H:i') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-xs text-slate-600 max-w-[140px] truncate" title="{{ $reg->notes }}">{{ $reg->notes ?: '—' }}</td>
                                        <td class="px-3 py-3 text-xs text-slate-500 whitespace-nowrap">{{ optional($reg->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد تسجيلات مطابقة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($registrations->hasPages())
                        <div class="mt-4">{{ $registrations->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

{{-- مودال QR --}}
<div x-data="{ open: false, result: '', resultType: 'info' }"
     x-on:open-checkin-modal.window="open = true; result=''; resultType='info';"
     x-cloak x-show="open"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fas fa-qrcode text-indigo-600 ml-1"></i> التأكد من الحضور</h3>
            <button type="button" @click="open=false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 space-y-3">
            <div id="qr-reader" class="border border-slate-200 rounded-xl overflow-hidden"></div>
            <template x-if="result">
                <div class="text-xs px-3 py-2 rounded-xl" :class="resultType==='success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                    <span x-text="result"></span>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const allGroups = @json($salesLeadGroups ?? []);
    const groupSelect = document.getElementById('convert-lead-group');
    const repCheckboxes = document.querySelectorAll('.convert-rep-checkbox');
    const form = document.getElementById('convert-to-leads-form');

    function selectedRepIds() {
        return Array.from(repCheckboxes).filter(cb => cb.checked).map(cb => Number(cb.value));
    }

    function refreshLeadGroups() {
        if (!groupSelect) return;
        const repIds = selectedRepIds();
        groupSelect.innerHTML = '<option value="">بدون مجموعة — أو اختر مجموعة مشتركة</option>';
        if (repIds.length === 0) return;
        allGroups.forEach(function (g) {
            const members = (g.member_ids || []).map(Number);
            if (!repIds.every(id => members.includes(id))) return;
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name + (g.is_admin_managed ? ' (إدارة)' : '');
            groupSelect.appendChild(opt);
        });
    }

    repCheckboxes.forEach(cb => cb.addEventListener('change', refreshLeadGroups));
    document.getElementById('select-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = true; });
        refreshLeadGroups();
    });
    document.getElementById('clear-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = false; });
        refreshLeadGroups();
    });
    refreshLeadGroups();

    form?.addEventListener('submit', function (e) {
        if (selectedRepIds().length === 0) {
            e.preventDefault();
            alert('اختر موظف مبيعات واحد على الأقل.');
            return;
        }
        const pending = Number(form.dataset.pending || 0);
        if (pending === 0) {
            e.preventDefault();
            alert('لا يوجد مسجّلون جدد للترحيل — الكل مُرحَّل مسبقاً.');
            return;
        }
        if (!confirm('ترحيل ' + pending + ' مسجّل جديد فقط؟ المُرحَّلون سابقاً لن يُعاد توزيعهم.')) {
            e.preventDefault();
        }
    });
})();
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    window.addEventListener('open-checkin-modal', () => {
        setTimeout(() => {
            const elementId = 'qr-reader';
            if (!document.getElementById(elementId)) return;
            if (window.__qrScanner) {
                try { window.__qrScanner.stop().then(() => window.__qrScanner.clear()); } catch(e) {}
            }
            const qrScanner = new Html5Qrcode(elementId);
            window.__qrScanner = qrScanner;
            qrScanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 220 },
                async (decodedText) => {
                    try {
                        const res = await fetch("{{ route('admin.workshops.checkin', $workshop) }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ token: decodedText }),
                        });
                        const data = await res.json();
                        const modal = document.querySelector('[x-on\\:open-checkin-modal]');
                        if (modal?.__x) {
                            modal.__x.$data.resultType = data.status || 'error';
                            modal.__x.$data.result = data.message || 'تمت المعالجة.';
                        }
                    } catch (e) { console.error(e); }
                }, () => {}
            ).catch(err => console.error(err));
        }, 150);
    });
});
</script>
@endpush
@endsection
