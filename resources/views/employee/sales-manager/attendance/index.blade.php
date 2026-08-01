@extends('layouts.employee')

@section('title', 'حضور الفريق')
@section('header', 'حضور وغياب الفريق')

@section('content')
@php
    $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels();
    $modeLabels = [
        'working' => 'يعمل الآن',
        'manager_unlocked_working' => 'يعمل (مفتوح)',
        'manager_unlocked' => 'مفتوح — بانتظار الحضور',
        'awaiting_clock_in' => 'بانتظار تسجيل الحضور',
        'pending_manager_approval' => 'بانتظار موافقة المدير',
        'attendance_rejected' => 'رُفض طلب الحضور',
        'locked_before_shift' => 'قبل موعد العمل',
        'missed_shift' => 'فات الموعد',
        'completed' => 'انتهى اليوم',
        'off_day' => 'يوم راحة',
        'on_leave' => 'إجازة',
        'exempt' => 'غير خاضع',
        'no_schedule' => 'بدون موعد',
    ];
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

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            ['label' => 'سجلات', 'value' => $stats['total']],
            ['label' => 'مكتمل', 'value' => $stats['completed']],
            ['label' => 'متأخر', 'value' => $stats['late']],
            ['label' => 'جاري العمل', 'value' => $stats['active_now']],
            ['label' => 'غياب', 'value' => $stats['absent']],
            ['label' => 'بانتظار موافقة', 'value' => $stats['pending_approval'] ?? 0],
        ] as $s)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold text-slate-900">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    @if(($pendingApprovals ?? collect())->isNotEmpty())
        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-100">
                <h3 class="font-black text-amber-950 flex items-center gap-2">
                    <i class="fas fa-user-check"></i>
                    طلبات حضور أوفلاين بانتظارك ({{ $pendingApprovals->count() }})
                </h3>
                <p class="text-xs text-amber-900/80 mt-1">أكد تواجد الموظف في المكتب ثم اختر: في الميعاد / إعفاء تأخير / تأخير بخصم.</p>
            </div>
            <div class="p-4 space-y-3">
                @foreach($pendingApprovals as $rec)
                    <div class="bg-white rounded-xl border border-amber-100 p-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-bold text-slate-900">{{ $rec->user?->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                طلب {{ $rec->attendance_requested_at?->format('H:i') ?? '—' }}
                                · ميعاد الشيفت {{ $rec->scheduled_start?->format('H:i') ?? '—' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                            @foreach(($latenessLabels ?? \App\Models\EmployeeAttendanceRecord::latenessDecisionLabels()) as $decision => $label)
                                <form method="POST" action="{{ route('employee.sales-manager.attendance.approve', $rec) }}">
                                    @csrf
                                    <input type="hidden" name="lateness_decision" value="{{ $decision }}">
                                    <button type="submit" @class([
                                        'text-xs font-bold px-3 py-1.5 rounded-lg border',
                                        'bg-emerald-600 text-white border-emerald-600' => $decision === 'on_time',
                                        'bg-sky-50 text-sky-800 border-sky-200' => $decision === 'excused_late',
                                        'bg-rose-50 text-rose-800 border-rose-200' => $decision === 'confirmed_late',
                                    ])>{{ $label }}</button>
                                </form>
                            @endforeach
                            <form method="POST" action="{{ route('employee.sales-manager.attendance.reject', $rec) }}" class="flex gap-1"
                                  onsubmit="return confirm('رفض طلب الحضور؟');">
                                @csrf
                                <input type="text" name="reason" required placeholder="سبب الرفض" class="px-2 py-1.5 border border-slate-200 rounded-lg text-xs w-36">
                                <button type="submit" class="text-xs font-bold px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">رفض</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- فتح النظام --}}
    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-emerald-50/40 overflow-hidden">
        <div class="px-5 py-4 border-b border-teal-100 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-sm">
                        <i class="fas fa-unlock-alt"></i>
                    </span>
                    فتح النظام للموظفين
                </h3>
                <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    يمكنك فتح النظام لأي عضو في الفريق خارج موعد العمل أو في يوم راحته.
                    يُسجَّل السبب والمدة في سجل التدقيق، ويحتاج الموظف لتسجيل الحضور بعد الفتح.
                </p>
            </div>
            @if($activeUnlocks->isNotEmpty())
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-teal-600 text-white">
                    {{ $activeUnlocks->count() }} فتح نشط
                </span>
            @endif
        </div>

        @if($activeUnlocks->isNotEmpty())
            <div class="px-5 py-3 bg-white/70 border-b border-teal-100 space-y-2">
                @foreach($activeUnlocks as $u)
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-teal-100 bg-white px-3 py-2.5">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900">{{ $u->user?->name }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                حتى {{ $u->expires_at->format('H:i') }}
                                · {{ $u->duration_label }}
                                · {{ \Illuminate\Support\Str::limit($u->reason, 60) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('employee.sales-manager.attendance.employee', $u->user_id) }}"
                               class="text-[11px] font-semibold text-teal-700 hover:underline">التفاصيل</a>
                            <form method="POST" action="{{ route('employee.sales-manager.attendance.unlock.revoke', [$u->user_id, $u]) }}"
                                  onsubmit="return confirm('إلغاء فتح النظام لهذا الموظف؟');">
                                @csrf
                                <button type="submit" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">
                                    إلغاء
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="p-5">
            <form method="POST" action="#" id="wa-unlock-form" class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end"
                  onsubmit="this.action = this.dataset.base.replace('__ID__', this.employee_id.value);">
                @csrf
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الموظف</label>
                    <select name="employee_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white"
                            onchange="document.getElementById('wa-unlock-form').dataset.base = '{{ url('/employee/sales-manager/attendance/employees') }}/__ID__/unlock'">
                        <option value="">اختر موظفاً...</option>
                        @foreach($members as $m)
                            @php $st = $memberStates[$m->id] ?? []; @endphp
                            <option value="{{ $m->id }}">
                                {{ $m->name }} — {{ $modeLabels[$st['mode'] ?? ''] ?? ($st['mode'] ?? '—') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">مدة الفتح</label>
                    <select name="duration" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                        @foreach($durationOptions as $key => $label)
                            <option value="{{ $key }}" @selected($key === 'end_of_day')>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">سبب الفتح</label>
                    <input type="text" name="reason" required minlength="5" maxlength="500"
                           value="{{ old('reason') }}"
                           placeholder="مثال: تغطية وردية / حملة عاجلة / يوم راحة بطلب الإدارة"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                </div>
                <div class="lg:col-span-2">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm">
                        <i class="fas fa-unlock-alt text-xs"></i>
                        فتح النظام
                    </button>
                </div>
            </form>
            <script>
                (function () {
                    var form = document.getElementById('wa-unlock-form');
                    form.dataset.base = '{{ url('/employee/sales-manager/attendance/employees') }}/__ID__/unlock';
                    form.addEventListener('submit', function (e) {
                        if (!form.employee_id.value) {
                            e.preventDefault();
                            alert('اختر موظفاً أولاً');
                            return;
                        }
                        form.action = form.dataset.base.replace('__ID__', form.employee_id.value);
                    });
                })();
            </script>
        </div>

        <div class="px-5 pb-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                @foreach($members as $m)
                    @php
                        $st = $memberStates[$m->id] ?? [];
                        $mode = $st['mode'] ?? '';
                        $needsUnlock = in_array($mode, ['off_day', 'on_leave', 'locked_before_shift', 'missed_shift', 'completed'], true);
                        $hasUnlock = !empty($st['unlock']);
                    @endphp
                    <div class="rounded-xl border bg-white px-3 py-3 flex items-start justify-between gap-2
                        {{ $hasUnlock ? 'border-teal-300 ring-1 ring-teal-100' : 'border-slate-200' }}">
                        <div class="min-w-0">
                            <a href="{{ route('employee.sales-manager.attendance.employee', $m) }}"
                               class="text-sm font-bold text-slate-900 hover:text-teal-700 truncate block">{{ $m->name }}</a>
                            <p class="text-[11px] mt-0.5 {{ $needsUnlock ? 'text-amber-700' : 'text-slate-500' }}">
                                {{ $modeLabels[$mode] ?? $mode }}
                            </p>
                            @if($hasUnlock)
                                <p class="text-[10px] text-teal-700 mt-1 font-semibold">
                                    مفتوح حتى {{ $st['unlock']['expires_at_human'] ?? '—' }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('employee.sales-manager.attendance.employee', $m) }}"
                           class="shrink-0 text-[10px] font-bold px-2 py-1 rounded-lg border
                           {{ $needsUnlock && !$hasUnlock ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                            {{ $hasUnlock ? 'إدارة' : ($needsUnlock ? 'فتح' : 'عرض') }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="employee_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" @selected(request('employee_id') == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">دخول</th>
                    <th class="px-4 py-3 text-right">خروج</th>
                    <th class="px-4 py-3 text-right">ساعات</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($records as $rec)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('employee.sales-manager.attendance.employee', $rec->user_id) }}" class="hover:text-teal-700">
                                {{ $rec->user->name ?? '—' }}
                            </a>
                            @if(($rec->user->work_mode ?? '') === 'offline')
                                <span class="text-[10px] text-violet-700 font-bold">أوفلاين</span>
                            @elseif(($rec->user->work_mode ?? '') === 'hybrid')
                                <span class="text-[10px] text-amber-700 font-bold">Hybrid</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $rec->work_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            {{ $rec->clock_in_at?->format('H:i') ?? ($rec->isAwaitingManagerApproval() ? 'طلب '.$rec->attendance_requested_at?->format('H:i') : '—') }}
                            @if($rec->is_late)
                                <span class="text-[10px] text-rose-600 font-bold">متأخر</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $rec->clock_out_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $rec->worked_minutes ? round($rec->worked_minutes / 60, 1) : '—' }}</td>
                        <td class="px-4 py-3">
                            {{ $statusLabels[$rec->status] ?? $rec->status }}
                            @if($rec->isAwaitingManagerApproval())
                                <span class="block text-[10px] text-amber-700 font-bold">بانتظار موافقة</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($rec->is_late && ! $rec->late_penalty_waived && $rec->clock_in_at)
                                <form method="POST" action="{{ route('employee.sales-manager.attendance.waive-late', $rec) }}"
                                      onsubmit="return confirm('إعفاء خصم التأخير؟');">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold text-sky-700 hover:underline">إعفاء خصم</button>
                                </form>
                            @elseif($rec->late_penalty_waived)
                                <span class="text-[11px] text-emerald-700">تم الإعفاء</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">لا توجد سجلات.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($records->hasPages())<div class="px-4 py-3 border-t">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
