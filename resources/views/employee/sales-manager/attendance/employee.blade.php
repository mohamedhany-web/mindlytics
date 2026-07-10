@extends('layouts.employee')

@section('title', 'حضور '.$employee->name)
@section('header', 'حضور '.$employee->name)

@section('content')
@php
    $modeLabels = [
        'working' => 'يعمل الآن',
        'manager_unlocked_working' => 'يعمل بتصريح فتح',
        'manager_unlocked' => 'مفتوح — بانتظار تسجيل الحضور',
        'awaiting_clock_in' => 'بانتظار تسجيل الحضور',
        'locked_before_shift' => 'قبل موعد العمل',
        'missed_shift' => 'فات موعد العمل',
        'completed' => 'انتهى يوم العمل',
        'off_day' => 'يوم راحة',
        'on_leave' => 'إجازة معتمدة',
        'exempt' => 'غير خاضع للمواعيد',
        'no_schedule' => 'بدون موعد عمل',
    ];
    $mode = $state['mode'] ?? '';
@endphp
<div class="space-y-6">
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

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.sales-manager.attendance.index') }}" class="text-sm text-emerald-700 font-semibold">← العودة لحضور الفريق</a>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border
            {{ !empty($state['unlock']) ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
            {{ $modeLabels[$mode] ?? $mode }}
        </span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'أيام مكتملة', 'value' => $summary['completed_days']],
            ['label' => 'تأخير', 'value' => $summary['late_days']],
            ['label' => 'إجمالي ساعات', 'value' => $summary['total_hours']],
            ['label' => 'أيام نشطة', 'value' => $summary['active_days']],
        ] as $s)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- فتح النظام لهذا الموظف --}}
    <div class="rounded-2xl border border-teal-200 bg-white overflow-hidden shadow-sm">
        <div class="px-5 py-4 bg-gradient-to-l from-teal-50 to-white border-b border-teal-100">
            <h3 class="font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-unlock-alt text-teal-600"></i>
                فتح النظام لـ {{ $employee->name }}
            </h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                يتيح للموظف الدخول والعمل خارج موعده أو في يوم راحته. يُطلب سبب ومدة، ويُحفظ في سجل التدقيق.
            </p>
        </div>

        <div class="p-5 space-y-4">
            @if($activeUnlock)
                <div class="rounded-xl border border-teal-200 bg-teal-50/60 px-4 py-3">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-teal-900">فتح نشط حالياً</p>
                            <p class="text-xs text-teal-800 mt-1">
                                حتى <strong>{{ $activeUnlock->expires_at->format('Y-m-d H:i') }}</strong>
                                ({{ $activeUnlock->duration_label }})
                            </p>
                            <p class="text-xs text-slate-600 mt-1">السبب: {{ $activeUnlock->reason }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">بواسطة: {{ $activeUnlock->unlockedBy?->name }}</p>
                        </div>
                        <form method="POST"
                              action="{{ route('employee.sales-manager.attendance.unlock.revoke', [$employee, $activeUnlock]) }}"
                              onsubmit="return confirm('إلغاء فتح النظام؟ سيعود القفل حسب الموعد.');">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg bg-white border border-rose-200 text-rose-700 text-xs font-bold hover:bg-rose-50">
                                إلغاء الفتح
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.sales-manager.attendance.unlock', $employee) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                @csrf
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">مدة الفتح</label>
                    <select name="duration" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
                        @foreach($durationOptions as $key => $label)
                            <option value="{{ $key }}" @selected(old('duration', 'end_of_day') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-6">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">سبب الفتح</label>
                    <input type="text" name="reason" required minlength="5" maxlength="500"
                           value="{{ old('reason') }}"
                           placeholder="مثال: تغطية وردية / حملة عاجلة / عمل في يوم الراحة"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div class="md:col-span-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">
                        <i class="fas fa-unlock-alt text-xs"></i>
                        {{ $activeUnlock ? 'تجديد الفتح' : 'فتح النظام' }}
                    </button>
                </div>
            </form>

            @if($employee->workSchedule)
                <p class="text-[11px] text-slate-500">
                    الموعد المخصص:
                    {{ substr((string) $employee->workSchedule->start_time, 0, 5) }}
                    —
                    {{ substr((string) $employee->workSchedule->end_time, 0, 5) }}
                    · {{ $employee->workSchedule->name }}
                </p>
            @endif
        </div>
    </div>

    @if($unlockHistory->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800">سجل فتح النظام</h4>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-right">من</th>
                        <th class="px-4 py-2 text-right">إلى</th>
                        <th class="px-4 py-2 text-right">المدة</th>
                        <th class="px-4 py-2 text-right">السبب</th>
                        <th class="px-4 py-2 text-right">بواسطة</th>
                        <th class="px-4 py-2 text-right">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($unlockHistory as $u)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $u->starts_at->format('m-d H:i') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $u->expires_at->format('m-d H:i') }}</td>
                            <td class="px-4 py-2">{{ $u->duration_label }}</td>
                            <td class="px-4 py-2 max-w-[14rem] truncate" title="{{ $u->reason }}">{{ $u->reason }}</td>
                            <td class="px-4 py-2">{{ $u->unlockedBy?->name }}</td>
                            <td class="px-4 py-2">
                                @if($u->isActive())
                                    <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full">نشط</span>
                                @elseif($u->revoked_at)
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">ملغى</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">منتهي</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">دخول</th>
                <th class="px-4 py-3 text-right">خروج</th>
                <th class="px-4 py-3 text-right">الحالة</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($records as $rec)
                    <tr>
                        <td class="px-4 py-3">{{ $rec->work_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_in_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->clock_out_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ \App\Models\EmployeeAttendanceRecord::statusLabels()[$rec->status] ?? $rec->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($records->hasPages())<div class="px-4 py-3">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
