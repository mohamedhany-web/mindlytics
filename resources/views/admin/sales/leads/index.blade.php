@extends('layouts.admin')

@section('title', 'العملاء المحتملون — المبيعات')
@section('header', 'المبيعات — العملاء المحتملون')

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.sales.audit-log.index') }}" class="text-sm text-emerald-700 font-medium hover:underline">سجل أنشطة المبيعات</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.sales.leads.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold shadow-lg hover:from-emerald-700 hover:to-teal-700 border border-emerald-400/40">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
            <a href="{{ route('admin.sales.leads.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg">
                <i class="fas fa-plus"></i> عميل جديد
            </a>
        </div>
    </div>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">موظف المبيعات</label>
            <select name="assigned_to" class="border rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="">الكل</option>
                @foreach($salesReps as $rep)
                    <option value="{{ $rep->id }}" @selected(request('assigned_to') == $rep->id)>{{ $rep->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">المرحلة</label>
            <select name="stage" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                    <option value="{{ $k }}" @selected(request('stage') === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">الأولوية</label>
            <select name="priority" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
                    <option value="{{ $k }}" @selected(request('priority') === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">المتابعة</label>
            <select name="follow_up" class="border rounded-lg px-3 py-2 text-sm min-w-[130px]">
                <option value="">الكل</option>
                <option value="overdue" @selected(request('follow_up') === 'overdue')>متأخرة</option>
                <option value="today" @selected(request('follow_up') === 'today')>اليوم</option>
                <option value="week" @selected(request('follow_up') === 'week')>خلال أسبوع</option>
                <option value="none" @selected(request('follow_up') === 'none')>بدون موعد</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">ترتيب</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm min-w-[150px]">
                <option value="" @selected(!request('sort'))>آخر تحديث</option>
                <option value="priority" @selected(request('sort') === 'priority')>الأولوية</option>
                <option value="follow_up" @selected(request('sort') === 'follow_up')>متابعة</option>
                <option value="last_contact" @selected(request('sort') === 'last_contact')>آخر تواصل</option>
                <option value="value" @selected(request('sort') === 'value')>قيمة متوقعة</option>
            </select>
        </div>
        <div class="flex items-center gap-2 pb-2">
            <input type="checkbox" name="stale" value="1" id="stale_ad" class="rounded border-gray-300" @checked(request()->boolean('stale'))>
            <label for="stale_ad" class="text-sm text-gray-700">بلا تواصل {{ \App\Models\SalesLead::STALE_CONTACT_DAYS }}+ يوم</label>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="{{ request('search') }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="اسم، هاتف، بريد...">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium">تصفية</button>
        @if(request()->hasAny(['assigned_to','stage','priority','follow_up','sort','stale','search']))
            <a href="{{ route('admin.sales.leads.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">إعادة ضبط</a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">مسند إلى</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">المرحلة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">أولوية</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">متابعة</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">آخر تواصل</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700">أنشئ</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leads as $lead)
                @php
                    $row = 'hover:bg-gray-50/80';
                    if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
                        $row .= ' bg-rose-50/50';
                    } elseif ($lead->isOpen() && $lead->isStaleContact()) {
                        $row .= ' bg-amber-50/40';
                    }
                    $pr = $lead->priority ?? 'normal';
                @endphp
                <tr class="{{ $row }}">
                    <td class="py-3 px-4 font-medium text-gray-900">{{ $lead->name }}</td>
                    <td class="py-3 px-4 text-gray-700">{{ $lead->assignee->name ?? '—' }}</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                            @if($pr === 'urgent') bg-rose-100 text-rose-800
                            @elseif($pr === 'high') bg-orange-100 text-orange-800
                            @elseif($pr === 'low') bg-slate-100 text-slate-700
                            @else bg-gray-100 text-gray-800 @endif">{{ \App\Models\SalesLead::priorityLabel($pr) }}</span>
                    </td>
                    <td class="py-3 px-4 text-xs @if($lead->isFollowUpOverdue()) text-rose-600 font-semibold @else text-gray-600 @endif">{{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="py-3 px-4 text-gray-600 text-xs">{{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="py-3 px-4 text-gray-500 text-xs">{{ $lead->created_at->format('Y-m-d') }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ route('admin.sales.leads.show', $lead) }}" class="text-emerald-600 font-semibold hover:underline">عرض</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-12 text-center text-gray-500">لا توجد سجلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-2">{{ $leads->links() }}</div>
</div>
@endsection
