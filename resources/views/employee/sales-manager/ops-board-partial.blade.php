@php
    $stageLabels = \App\Models\SalesLead::STAGES;
    $attendanceFilterLabels = [
        'not_clocked_in' => 'لم يحضر',
        'pending_approval' => 'بانتظار موافقة',
        'working' => 'حاضر',
        'late' => 'متأخر',
        'completed' => 'انصرف',
        'off_day' => 'راحة',
        'on_leave' => 'إجازة',
    ];
@endphp

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

<div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-black text-slate-900">متابعة الفريق — {{ $date->format('Y-m-d') }}</h2>
            <p class="text-xs text-slate-500 mt-1">يتحدّث تلقائياً كل 30 ثانية. راجع الحضور والنشاط أثناء الشيفت.</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200">
            تحديث حي
        </span>
    </div>

    <form method="GET" action="{{ route('employee.sales-manager.ops-board') }}" class="grid grid-cols-2 lg:grid-cols-6 gap-2">
        <div>
            <label class="block text-[10px] font-bold text-slate-500 mb-1">التاريخ</label>
            <input type="date" name="date" value="{{ $date->toDateString() }}" class="w-full px-2 py-2 border border-slate-200 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 mb-1">الموظف</label>
            <select name="employee_id" class="w-full px-2 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">الكل</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" @selected((string) ($filters['employee_id'] ?? '') === (string) $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 mb-1">نوع العمل</label>
            <select name="work_mode" class="w-full px-2 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">الكل</option>
                <option value="online" @selected(($filters['work_mode'] ?? '') === 'online')>أونلاين</option>
                <option value="offline" @selected(($filters['work_mode'] ?? '') === 'offline')>أوفلاين</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 mb-1">الحضور</label>
            <select name="attendance" class="w-full px-2 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">الكل</option>
                @foreach($attendanceFilterLabels as $k => $label)
                    <option value="{{ $k }}" @selected(($filters['attendance'] ?? '') === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 mb-1">التواجد</label>
            <select name="presence" class="w-full px-2 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">الكل</option>
                <option value="online" @selected(($filters['presence'] ?? '') === 'online')>متصل</option>
                <option value="away" @selected(($filters['presence'] ?? '') === 'away')>بعيد</option>
                <option value="offline" @selected(($filters['presence'] ?? '') === 'offline')>غير متصل</option>
                <option value="not_clocked_in" @selected(($filters['presence'] ?? '') === 'not_clocked_in')>لم يسجّل حضور</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">تصفية</button>
        </div>
    </form>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
    @foreach([
        ['أعضاء', $stats['total']],
        ['متصلون', $stats['online_presence']],
        ['يعملون', $stats['working']],
        ['بانتظار موافقة', $stats['pending_approval']],
        ['لم يحضروا', $stats['not_clocked_in']],
        ['أوفلاين', $stats['offline_workers']],
    ] as [$label, $value])
        <div class="bg-white rounded-xl border border-slate-200 p-3">
            <p class="text-[11px] text-slate-500">{{ $label }}</p>
            <p class="text-xl font-black text-slate-900">{{ $value }}</p>
        </div>
    @endforeach
</div>

@if($pendingApprovals->isNotEmpty())
    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4 space-y-3">
        <h3 class="font-black text-amber-950 flex items-center gap-2">
            <i class="fas fa-user-check"></i>
            طلبات حضور بانتظارك ({{ $pendingApprovals->count() }})
        </h3>
        @foreach($pendingApprovals as $rec)
            <div class="bg-white rounded-xl border border-amber-100 p-3 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-bold text-slate-900">{{ $rec->user?->name }}</p>
                    <p class="text-xs text-slate-500">طلب الساعة {{ $rec->attendance_requested_at?->format('H:i') ?? '—' }} · مجدول {{ $rec->scheduled_start?->format('H:i') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($latenessLabels as $decision => $label)
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
                        <input type="text" name="reason" required placeholder="سبب الرفض" class="px-2 py-1.5 border border-slate-200 rounded-lg text-xs w-32">
                        <button type="submit" class="text-xs font-bold px-2 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">رفض</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-right">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-3 py-2.5">الموظف</th>
                    <th class="px-3 py-2.5">نوع</th>
                    <th class="px-3 py-2.5">حضور</th>
                    <th class="px-3 py-2.5">تواجد</th>
                    <th class="px-3 py-2.5">نشاط اليوم</th>
                    <th class="px-3 py-2.5">متابعات متأخرة</th>
                    <th class="px-3 py-2.5">آخر عميل</th>
                    <th class="px-3 py-2.5">تقرير</th>
                    <th class="px-3 py-2.5">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-3 py-3">
                            <a href="{{ route('employee.sales-manager.team.show', $row['user_id']) }}" class="font-bold text-slate-900 hover:text-teal-700">{{ $row['name'] }}</a>
                            <p class="text-[10px] text-slate-400">آخر ظهور: {{ $row['last_seen_human'] ?? '—' }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <span @class([
                                'text-[11px] font-bold px-2 py-0.5 rounded-full',
                                'bg-sky-50 text-sky-800' => ! $row['is_offline'],
                                'bg-violet-50 text-violet-800' => $row['is_offline'],
                            ])>{{ $row['is_offline'] ? 'أوفلاين' : 'أونلاين' }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <p class="font-semibold text-slate-800">{{ $attendanceFilterLabels[$row['attendance_filter']] ?? $row['attendance_mode'] }}</p>
                            <p class="text-[11px] text-slate-500 tabular-nums">
                                {{ $row['clock_in_at'] ?? ($row['requested_at'] ? 'طلب '.$row['requested_at'] : '—') }}
                                @if($row['clock_out_at']) → {{ $row['clock_out_at'] }}@endif
                                @if($row['is_late'] && $row['late_waived']) · إعفاء تأخير@endif
                                @if($row['is_late'] && ! $row['late_waived']) · متأخر@endif
                            </p>
                        </td>
                        <td class="px-3 py-3">
                            <span @class([
                                'text-[11px] font-bold',
                                'text-emerald-700' => ($row['presence_status'] ?? '') === 'online',
                                'text-amber-700' => ($row['presence_status'] ?? '') === 'away',
                                'text-slate-500' => ! in_array($row['presence_status'] ?? '', ['online', 'away'], true),
                            ])>{{ $row['presence_label'] }}</span>
                        </td>
                        <td class="px-3 py-3 tabular-nums">
                            <span class="font-bold">{{ $row['activities_today'] }}</span>
                            <span class="text-slate-400 text-xs">({{ $row['calls_today'] }} مكالمة)</span>
                        </td>
                        <td class="px-3 py-3">
                            @if($row['overdue_follow_ups'] > 0)
                                <span class="text-rose-700 font-bold">{{ $row['overdue_follow_ups'] }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            @if($row['last_lead_name'])
                                <p class="font-medium text-slate-800 truncate max-w-[140px]">{{ $row['last_lead_name'] }}</p>
                                <p class="text-[10px] text-slate-400">{{ $stageLabels[$row['last_lead_stage']] ?? $row['last_lead_stage'] }}</p>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-xs">
                            @if(($row['daily_report_status'] ?? '') === 'submitted' || ($row['daily_report_status'] ?? '') === \App\Models\SalesDailyReport::STATUS_SUBMITTED)
                                <span class="text-emerald-700 font-bold">مُسلَّم</span>
                            @else
                                <span class="text-amber-700">ناقص</span>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            @if($row['pending_approval'] && $row['record_id'])
                                <div class="flex flex-col gap-1">
                                    @foreach($latenessLabels as $decision => $label)
                                        <form method="POST" action="{{ route('employee.sales-manager.attendance.approve', $row['record_id']) }}">
                                            @csrf
                                            <input type="hidden" name="lateness_decision" value="{{ $decision }}">
                                            <button type="submit" class="w-full text-[10px] font-bold px-2 py-1 rounded-md border border-slate-200 hover:bg-slate-50">{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            @elseif($row['is_late'] && ! $row['late_waived'] && $row['record_id'])
                                <form method="POST" action="{{ route('employee.sales-manager.attendance.waive-late', $row['record_id']) }}"
                                      onsubmit="return confirm('إعفاء خصم التأخير؟');">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold px-2 py-1 rounded-md bg-sky-50 text-sky-800 border border-sky-200">إعفاء خصم</button>
                                </form>
                            @else
                                <a href="{{ route('employee.sales-manager.attendance.employee', $row['user_id']) }}" class="text-[11px] font-semibold text-teal-700 hover:underline">تفاصيل</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-500">لا يوجد أعضاء مطابقون للفلتر.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
