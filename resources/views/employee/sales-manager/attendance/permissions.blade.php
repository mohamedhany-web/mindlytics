@extends('layouts.employee')

@section('title', 'أذونات الغياب والانصراف')
@section('header', 'إذن غياب يوم / انصراف مبكر — الأوفلاين')

@section('content')
<div class="space-y-6" x-data="{ type: '{{ old('type', 'day_absence') }}' }">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h2 class="text-lg font-black text-slate-900">إصدار إذن جديد</h2>
        <p class="text-sm text-slate-500 mt-1">
            للموظفين اللي بينزلوا أوفلاين/المقر: إذن غياب يوم كامل بدون خصم، أو إذن انصراف مبكر بدون غرامة ساعات ناقصة.
        </p>

        <form method="POST" action="{{ route('employee.sales-manager.attendance.permissions.store') }}" class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">الموظف *</label>
                <select name="employee_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
                    <option value="">— اختر —</option>
                    @foreach(($offlineMembers->isNotEmpty() ? $offlineMembers : $members) as $member)
                        <option value="{{ $member->id }}" @selected((int) old('employee_id') === (int) $member->id)>
                            {{ $member->name }} — {{ $member->workModeLabel() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">نوع الإذن *</label>
                <select name="type" x-model="type" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
                    @foreach($typeLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">التاريخ *</label>
                <input type="date" name="work_date" value="{{ old('work_date', $date->toDateString()) }}" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
            </div>

            <div x-show="type === 'early_departure'" x-cloak>
                <label class="block text-xs font-semibold text-slate-500 mb-1">وقت الانصراف المبكر *</label>
                <input type="time" name="early_departure_time" value="{{ old('early_departure_time') }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">السبب (اختياري)</label>
                <input type="text" name="reason" value="{{ old('reason') }}" maxlength="500" placeholder="مثال: ظرف عائلي / موعد طبي"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold px-5 py-2.5 text-sm">
                    حفظ الإذن
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-black text-slate-900">سجل الأذونات</h2>
            <form method="GET" action="{{ route('employee.sales-manager.attendance.permissions.index') }}" class="flex items-end gap-2">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">تصفية بالتاريخ</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm font-bold">عرض</button>
                @if(request()->filled('date'))
                    <a href="{{ route('employee.sales-manager.attendance.permissions.index') }}" class="text-sm text-slate-500 underline px-2 py-2">إلغاء</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs">
                    <tr>
                        <th class="text-start px-4 py-3 font-bold">الموظف</th>
                        <th class="text-start px-4 py-3 font-bold">النوع</th>
                        <th class="text-start px-4 py-3 font-bold">التاريخ</th>
                        <th class="text-start px-4 py-3 font-bold">التفاصيل</th>
                        <th class="text-start px-4 py-3 font-bold">الحالة</th>
                        <th class="text-start px-4 py-3 font-bold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permissions as $permission)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900">{{ $permission->employee?->name }}</p>
                                <p class="text-xs text-slate-400">بواسطة {{ $permission->granter?->name }}</p>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $permission->typeLabel() }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $permission->work_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                @if($permission->type === 'early_departure' && $permission->early_departure_time)
                                    <span>انصراف من {{ \Illuminate\Support\Str::of($permission->early_departure_time)->substr(0, 5) }}</span>
                                    @if($permission->reason)<span class="text-slate-400"> · </span>@endif
                                @endif
                                {{ $permission->reason }}
                            </td>
                            <td class="px-4 py-3">
                                @if($permission->isActive())
                                    <span class="inline-flex rounded-lg bg-emerald-50 text-emerald-800 px-2.5 py-1 text-xs font-bold">ساري</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">ملغى</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($permission->isActive())
                                    <form method="POST" action="{{ route('employee.sales-manager.attendance.permissions.revoke', $permission) }}"
                                          onsubmit="return confirm('تأكيد إلغاء الإذن؟')">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">إلغاء</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا أذونات مسجّلة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
