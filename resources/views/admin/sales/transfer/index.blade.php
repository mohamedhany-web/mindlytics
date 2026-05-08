@extends('layouts.admin')

@section('title', 'تحويل بيانات موظف — المبيعات')
@section('header', 'المبيعات — تحويل بيانات موظف')

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rose-100 border border-rose-300 text-rose-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 md:p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="space-y-1">
                <h2 class="text-lg font-black text-gray-900">تحويل جميع بيانات موظف مبيعات إلى موظف آخر</h2>
                <p class="text-sm text-gray-600">يشمل: العملاء المحتملين (بكل المراحل)، الأنشطة، سجل المراقبة، و(إن وُجد) أهداف KPI.</p>
            </div>
            <a href="{{ route('admin.sales.leads.index') }}" class="text-sm text-emerald-700 font-bold hover:underline">العودة للعملاء المحتملين</a>
        </div>

        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">اختر موظف (من) لعرض ملخص بياناته</label>
                <select name="from_user_id" class="w-full border rounded-xl px-3 py-2.5 text-sm">
                    <option value="">— اختر —</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" @selected((string)$fromId === (string)$rep->id)>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow">
                <i class="fas fa-search"></i> عرض الملخص
            </button>
        </form>

        @if($fromRep && $stats)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-2xl p-4 border border-gray-200 bg-slate-50">
                    <div class="text-xs text-gray-500">إجمالي العملاء المحتملين</div>
                    <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['leads_total'] ?? 0) }}</div>
                </div>
                <div class="rounded-2xl p-4 border border-gray-200 bg-slate-50">
                    <div class="text-xs text-gray-500">إجمالي الأنشطة</div>
                    <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['activities_total'] ?? 0) }}</div>
                </div>
                <div class="rounded-2xl p-4 border border-gray-200 bg-slate-50">
                    <div class="text-xs text-gray-500">سجل المراقبة (Audit)</div>
                    <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['audit_total'] ?? 0) }}</div>
                </div>
                <div class="rounded-2xl p-4 border border-gray-200 bg-slate-50">
                    <div class="text-xs text-gray-500">أهداف KPI (إن وُجدت)</div>
                    <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['kpi_targets_total'] ?? 0) }}</div>
                </div>
            </div>

            <div class="rounded-2xl p-4 border border-gray-200 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-black text-gray-900">تفصيل المراحل — {{ $fromRep->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">Won confirmed: {{ number_format($stats['won_confirmed_total'] ?? 0) }} — Created by: {{ number_format($stats['created_by_total'] ?? 0) }}</div>
                    </div>
                    @if(session('transfer_summary'))
                        @php $s = session('transfer_summary'); @endphp
                        <div class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-3 py-2">
                            <div class="font-bold mb-1">ملخص آخر تحويل</div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                <div>Leads Assigned: <span class="font-black">{{ (int)($s['leads_assigned'] ?? 0) }}</span></div>
                                <div>Activities: <span class="font-black">{{ (int)($s['activities'] ?? 0) }}</span></div>
                                <div>Audit Logs: <span class="font-black">{{ (int)($s['audit_logs'] ?? 0) }}</span></div>
                                <div>KPI moved: <span class="font-black">{{ (int)($s['kpi_targets_moved'] ?? 0) }}</span></div>
                                <div>KPI conflicts: <span class="font-black">{{ (int)($s['kpi_targets_conflicts'] ?? 0) }}</span></div>
                                <div>Won confirmed: <span class="font-black">{{ (int)($s['leads_won_confirmed_by'] ?? 0) }}</span></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                        @php $c = (int) (($stats['leads_by_stage'][$k] ?? 0)); @endphp
                        <div class="rounded-xl border border-gray-200 bg-slate-50 p-3">
                            <div class="text-xs text-gray-500">{{ $label }}</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($c) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 md:p-6">
        <form method="post" action="{{ route('admin.sales.transfer.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">من (موظف مبيعات)</label>
                    <select name="from_user_id" required class="w-full border rounded-xl px-3 py-2.5 text-sm">
                        <option value="">— اختر —</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" @selected(old('from_user_id', $fromId) == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">سيتم نقل كل بيانات المبيعات المرتبطة بهذا الموظف.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">إلى (موظف مبيعات)</label>
                    <select name="to_user_id" required class="w-full border rounded-xl px-3 py-2.5 text-sm">
                        <option value="">— اختر —</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" @selected(old('to_user_id') == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">سيصبح هو المسؤول عن العملاء والأنشطة بعد التحويل.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="font-black text-amber-900 mb-1">تنبيه</div>
                <div class="text-sm text-amber-900/90 leading-relaxed">
                    هذا الإجراء يقوم بتعديل بيانات قاعدة البيانات بشكل جماعي ولا يمكن التراجع عنه تلقائياً.
                    تأكد من اختيار الموظفين بشكل صحيح.
                </div>
                <label class="mt-3 flex items-center gap-2 text-sm font-bold text-amber-900">
                    <input type="checkbox" name="confirm" value="1" class="rounded border-amber-300" @checked(old('confirm'))>
                    أؤكد أنني أريد تحويل جميع بيانات الموظف المحدد
                </label>
            </div>

            <button class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-black shadow-lg">
                <i class="fas fa-random"></i> تحويل البيانات الآن
            </button>
        </form>
    </div>
</div>
@endsection

