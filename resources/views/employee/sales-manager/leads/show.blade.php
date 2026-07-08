@extends('layouts.employee')

@section('title', $lead->name)
@section('header', 'تفاصيل العميل')

@section('content')
<div class="space-y-6 max-w-4xl">
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $lead->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">مسند إلى: <strong>{{ $lead->assignee->name ?? '—' }}</strong></p>
                <p class="text-sm text-slate-500">المرحلة: {{ \App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage }}</p>
                @if($lead->phone)<p class="text-sm text-slate-600 mt-2"><i class="fas fa-phone ml-1"></i> {{ $lead->phone }}</p>@endif
                @if($lead->email)<p class="text-sm text-slate-600"><i class="fas fa-envelope ml-1"></i> {{ $lead->email }}</p>@endif
            </div>
            <a href="{{ route('employee.sales-manager.leads.index') }}" class="text-sm text-slate-600 hover:text-slate-900">← العودة للقائمة</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h2 class="font-bold text-slate-900 mb-4">تحويل Lead لعضو آخر في الفريق</h2>
        <form method="POST" action="{{ route('employee.sales-manager.leads.transfer', $lead) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">تحويل إلى</label>
                <select name="to_user_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    @foreach($members as $m)
                        @if((int)$m->user_id !== (int)$lead->assigned_to)
                            <option value="{{ $m->user_id }}">{{ $m->user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">سبب التحويل (اختياري)</label>
                <textarea name="reason" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">{{ old('reason') }}</textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold" onclick="return confirm('تأكيد تحويل هذا العميل؟')">تحويل الآن</button>
        </form>
    </div>
</div>
@endsection
