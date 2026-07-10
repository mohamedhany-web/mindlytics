@extends('layouts.employee')

@section('title', 'متابعاتي')
@section('header', 'متابعاتي — Next Follow')

@section('content')
@php
    $filterLabels = [
        'overdue' => 'متأخرة',
        'today' => 'اليوم',
        'week' => 'خلال 7 أيام',
        'none' => 'بدون موعد',
        'stale' => 'بلا تواصل',
        'all' => 'الكل',
    ];
    $redirectTo = request()->fullUrl();
@endphp
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-emerald-50/40 px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-teal-600"></i> متابعاتي
                </h1>
                <p class="text-xs text-slate-600 mt-1">جدول مواعيد Next Follow — المتأخرة واليوم والقادمة، مع من لم يُتواصل معهم.</p>
            </div>
            <a href="{{ route('employee.sales.leads.index') }}" class="text-xs font-bold text-teal-700 hover:underline">كل العملاء ←</a>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach($filterLabels as $key => $label)
            <a href="{{ route('employee.sales.follow-ups.index', array_filter(['filter' => $key, 'search' => request('search')])) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors
               {{ $filter === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
                <span class="tabular-nums opacity-80">{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" class="flex gap-2">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الهاتف..."
               class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">بحث</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold hidden sm:table-cell">الهاتف</th>
                    <th class="px-4 py-3 text-right font-semibold hidden md:table-cell">المرحلة</th>
                    <th class="px-4 py-3 text-right font-semibold">المتابعة</th>
                    <th class="px-4 py-3 text-right font-semibold hidden lg:table-cell">آخر تواصل</th>
                    <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-right font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                    @php
                        $status = '—';
                        $statusClass = 'text-slate-500';
                        if ($lead->isFollowUpOverdue()) {
                            $status = 'متأخر';
                            $statusClass = 'text-rose-700 bg-rose-50';
                        } elseif ($lead->next_follow_up_at && $lead->next_follow_up_at->isToday()) {
                            $status = 'اليوم';
                            $statusClass = 'text-amber-800 bg-amber-50';
                        } elseif ($lead->next_follow_up_at && $lead->next_follow_up_at->isFuture()) {
                            $status = 'قادم';
                            $statusClass = 'text-teal-800 bg-teal-50';
                        } elseif (! $lead->next_follow_up_at) {
                            $status = 'بدون موعد';
                            $statusClass = 'text-slate-600 bg-slate-100';
                        }
                        if ($lead->isStaleContact()) {
                            $status = $status === '—' ? 'بلا تواصل' : $status.' · بلا تواصل';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="font-bold text-slate-900 hover:text-teal-700">{{ $lead->name }}</a>
                            @if($lead->category)
                                <span class="block text-[11px] text-slate-500">{{ $lead->category->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell dir-ltr text-slate-600">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-slate-600">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap @if($lead->isFollowUpOverdue()) text-rose-700 font-bold @else text-slate-700 @endif">
                            {{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500 whitespace-nowrap">
                            {{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1 justify-end">
                                <button type="button"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100"
                                        title="تحديد Next Follow"
                                        onclick="openNextFollowModal({{ $lead->id }}, @js($lead->name), @js($lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')))">
                                    <i class="fas fa-calendar-plus text-xs"></i>
                                </button>
                                <form method="post" action="{{ route('employee.sales.leads.quick-activity', $lead) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="call">
                                    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="سجّل مكالمة">
                                        <i class="fas fa-phone text-xs"></i>
                                    </button>
                                </form>
                                <a href="{{ route('employee.sales.leads.show', $lead) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" title="فتح">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                            لا توجد متابعات في هذا الفلتر.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>

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
<script>
function openNextFollowModal(leadId, name, datetime) {
    var modal = document.getElementById('next-follow-modal');
    document.getElementById('nf-form').action = @json(url('/employee/sales/leads')) + '/' + leadId + '/next-follow';
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
</script>
@endsection
