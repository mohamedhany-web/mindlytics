@extends('layouts.admin')

@section('title', $lead->name)
@section('header', 'عميل محتمل: ' . $lead->name)

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6" style="background:#f8fafc;min-height:100vh;">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(!empty(session('sales_duplicate_warnings')))
        <div class="bg-amber-50 border border-amber-300 text-amber-950 px-4 py-3 rounded-lg text-sm space-y-1">
            @foreach(session('sales_duplicate_warnings') as $w)
                <p><i class="fas fa-exclamation-triangle ml-1"></i>{{ $w }}</p>
            @endforeach
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.sales.leads.index') }}" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.sales.leads.edit', $lead) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold">تعديل / إعادة إسناد</a>
            <form action="{{ route('admin.sales.leads.destroy', $lead) }}" method="post" onsubmit="return confirm('حذف نهائياً؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-semibold">حذف</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                @php $pr = $lead->priority ?? 'normal'; @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    @if($pr === 'urgent') bg-rose-100 text-rose-800
                    @elseif($pr === 'high') bg-orange-100 text-orange-800
                    @elseif($pr === 'low') bg-slate-100 text-slate-700
                    @else bg-gray-100 text-gray-800 @endif">{{ \App\Models\SalesLead::priorityLabel($pr) }}</span>
                <span class="text-sm text-gray-500">{{ \App\Models\SalesLead::sourceLabel($lead->source) }}</span>
            </div>
            <p class="text-sm text-gray-600">مسند إلى: <strong>{{ $lead->assignee->name ?? '—' }}</strong></p>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500">الهاتف</dt><dd class="font-medium">{{ $lead->phone ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">البريد</dt><dd class="font-medium">{{ $lead->email ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">الشركة</dt><dd class="font-medium">{{ $lead->company ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">قيمة متوقعة</dt><dd class="font-medium">{{ $lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">متابعة تالية</dt><dd class="font-medium @if($lead->isFollowUpOverdue()) text-rose-600 @endif">{{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">آخر تواصل مسجّل</dt><dd class="font-medium">{{ $lead->last_contacted_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
            @if($lead->interest)
            <div class="sm:col-span-2"><dt class="text-gray-500">الاهتمام</dt><dd class="text-gray-800 whitespace-pre-wrap">{{ $lead->interest }}</dd></div>
            @endif
            @if($lead->notes)
            <div class="sm:col-span-2"><dt class="text-gray-500">ملاحظات</dt><dd class="text-gray-800 whitespace-pre-wrap">{{ $lead->notes }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-4">سجل النشاط (يُسجَّل في سجل المراقبة)</h2>
        <form method="post" action="{{ route('admin.sales.leads.activities.store', $lead) }}" class="space-y-3 mb-8 pb-8 border-b border-gray-100">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">النوع</label>
                    <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                        @foreach(\App\Models\SalesActivity::TYPES as $k => $label)
                            @if($k !== 'stage_change')
                            <option value="{{ $k }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">عنوان</label>
                    <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <textarea name="body" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="التفاصيل"></textarea>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">إضافة نشاط</button>
        </form>

        <ul class="space-y-4">
            @forelse($lead->activities as $act)
            <li class="border-r-4 border-emerald-300 pr-4">
                <div class="flex flex-wrap justify-between gap-2 text-xs text-gray-500">
                    <span class="font-semibold text-emerald-800">{{ \App\Models\SalesActivity::typeLabel($act->type) }}</span>
                    <span>{{ $act->created_at->format('Y-m-d H:i') }} — {{ $act->user->name }}</span>
                </div>
                @if($act->title)<p class="font-medium text-gray-900 mt-1">{{ $act->title }}</p>@endif
                @if($act->body)<p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $act->body }}</p>@endif
            </li>
            @empty
            <li class="text-gray-500 text-sm">لا أنشطة بعد</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
