@extends('layouts.employee')

@section('title', 'تأكيد حضور المقر')
@section('header', 'تأكيد حضور المقر من الشيفتات')

@section('content')
@php
    $statusLabels = [
        'awaiting' => 'بانتظار التأكيد',
        'pending_request' => 'طلب حضور بانتظار الموافقة',
        'clocked_unconfirmed' => 'سجّل دخولًا — بانتظار تأكيدك',
        'confirmed_on_time' => 'مؤكَّد — في الميعاد',
        'confirmed_late' => 'مؤكَّد — متأخر',
    ];
@endphp
<div class="space-y-6" x-data>
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

    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-slate-900">شيفتات المقر — {{ $roster['day_name'] ?? '' }}</h2>
            <p class="text-sm text-slate-500 mt-1">
                أعضاء الفريق المجدولون <b>من المقر</b> فقط (وضع الشيفت ≠ من البيت).
                اللي شغال أونلاين/من البيت في نفس اليوم لا يظهر هنا — حتى لو باقي الفريق نازل بالليل.
                التأكيد منك مطلوب حتى لو سجّل الموظف حضورًا مسبقًا.
            </p>
        </div>
        <form method="GET" action="{{ route('employee.sales-manager.attendance.offline-day') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">اليوم</label>
                <input type="date" name="date" value="{{ $date->toDateString() }}"
                       class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800">
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-bold hover:bg-slate-800">
                عرض
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'من المقر', 'value' => $stats['office'], 'tone' => 'text-slate-900'],
            ['label' => 'مؤكَّد', 'value' => $stats['confirmed'], 'tone' => 'text-emerald-700'],
            ['label' => 'متأخر', 'value' => $stats['late'], 'tone' => 'text-amber-700'],
            ['label' => 'بانتظار', 'value' => $stats['awaiting'], 'tone' => 'text-rose-700'],
        ] as $s)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold {{ $s['tone'] }}">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    @if(($roster['empty_reason'] ?? null) === 'no_plan')
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-8 text-center">
            <p class="font-bold text-amber-950">لا يوجد جدول شيفتات نشط</p>
            <p class="text-sm text-amber-900/80 mt-1">فعّل خطة شيفتات من الإدارة ثم عد لهذه الصفحة.</p>
        </div>
    @elseif(($roster['empty_reason'] ?? null) === 'no_office_shifts' || empty($roster['members']))
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-8 text-center">
            <p class="font-bold text-slate-900">لا يوجد أحد من الفريق بشيفت من المقر في هذا اليوم</p>
            <p class="text-sm text-slate-500 mt-1">الشيفتات «من البيت» لا تظهر هنا — اختر يومًا آخر أو راجع جدول الشيفتات.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-start px-4 py-3 font-bold">الموظف</th>
                            <th class="text-start px-4 py-3 font-bold">وقت الشيفت</th>
                            <th class="text-start px-4 py-3 font-bold">الحالة الحالية</th>
                            <th class="text-start px-4 py-3 font-bold">تأكيد الحضور</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($roster['members'] as $member)
                            <tr class="align-top" x-data="{ decision: '{{ $member['manager_confirmed'] ? ($member['is_late'] ? 'late' : 'on_time') : '' }}' }">
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-900">{{ $member['name'] }}</p>
                                    @if($member['location_badge'])
                                        <p class="text-xs text-teal-700 mt-0.5">{{ $member['location_badge'] }}</p>
                                    @endif
                                    @if($member['channels_label'])
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $member['channels_label'] }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-700 whitespace-nowrap">
                                    {{ $member['start_label'] }} — {{ $member['end_label'] }}
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-lg px-2.5 py-1 text-xs font-bold',
                                        'bg-emerald-50 text-emerald-800' => $member['status_key'] === 'confirmed_on_time',
                                        'bg-amber-50 text-amber-900' => $member['status_key'] === 'confirmed_late',
                                        'bg-sky-50 text-sky-800' => in_array($member['status_key'], ['pending_request', 'clocked_unconfirmed'], true),
                                        'bg-slate-100 text-slate-700' => $member['status_key'] === 'awaiting',
                                    ])>
                                        {{ $statusLabels[$member['status_key']] ?? $member['status_key'] }}
                                    </span>
                                    @if($member['clock_in_at'])
                                        <p class="text-xs text-slate-400 mt-1">دخول {{ \Carbon\Carbon::parse($member['clock_in_at'])->format('H:i') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <form method="POST" action="{{ route('employee.sales-manager.attendance.offline-day.confirm') }}" class="space-y-3 max-w-md">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $member['user_id'] }}">
                                        <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                                        <input type="hidden" name="decision" :value="decision">

                                        <div class="flex flex-wrap gap-2">
                                            <button type="button"
                                                    @click="decision = 'on_time'"
                                                    :class="decision === 'on_time' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50'"
                                                    class="rounded-xl border px-3 py-2 text-xs font-bold transition">
                                                في الميعاد
                                            </button>
                                            <button type="button"
                                                    @click="decision = 'late'"
                                                    :class="decision === 'late' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-50'"
                                                    class="rounded-xl border px-3 py-2 text-xs font-bold transition">
                                                متأخر
                                            </button>
                                        </div>

                                        <div x-show="decision === 'late'" x-cloak class="flex items-end gap-2">
                                            <div class="flex-1">
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">مبلغ الخصم (ج.م)</label>
                                                <input type="number" name="deduction_amount" step="0.01" min="0"
                                                       value="{{ old('deduction_amount', number_format((float) $defaultDeduction, 2, '.', '')) }}"
                                                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                            </div>
                                        </div>

                                        <button type="submit"
                                                :disabled="!decision"
                                                :class="decision ? 'bg-slate-900 hover:bg-slate-800' : 'bg-slate-300 cursor-not-allowed'"
                                                class="rounded-xl text-white px-4 py-2 text-xs font-bold transition">
                                            {{ $member['manager_confirmed'] ? 'تحديث التأكيد' : 'تأكيد الحضور' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
