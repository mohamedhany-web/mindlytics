@extends('layouts.admin')

@php
    $monthNames = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
        7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $excelUrl = route('admin.design-task-cycles.performance-report.excel', ['year' => $year, 'month' => $month]);
@endphp

@section('title', 'تقرير أداء الموظفين — '.$monthNames[$month].' '.$year)
@section('header', 'تحليل الأداء الشهري — '.$monthNames[$month].' '.$year)

@section('content')
<div class="space-y-8">
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <form method="get" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">السنة</label>
                <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="w-28 rounded-xl border-slate-300 text-sm shadow-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">الشهر</label>
                <select name="month" class="rounded-xl border-slate-300 text-sm min-w-[140px] shadow-sm focus:ring-violet-500 focus:border-violet-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int) $month === $m ? 'selected' : '' }}>{{ $monthNames[$m] }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold shadow-md transition-colors">
                <i class="fas fa-sync-alt ml-2"></i> تحديث التحليل
            </button>
        </form>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $excelUrl }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg transition-colors">
                <i class="fas fa-file-excel"></i>
                تنزيل Excel (٤ أوراق)
            </a>
            <a href="{{ route('admin.design-task-cycles.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">
                <i class="fas fa-palette text-violet-600"></i>
                دورات التصميم
            </a>
        </div>
    </div>

    <p class="text-sm text-slate-600 flex flex-wrap items-center gap-x-4 gap-y-1">
        <span><i class="fas fa-calendar-alt text-violet-500 ml-1"></i> الفترة: <strong>{{ $start->format('Y-m-d') }}</strong> — <strong>{{ $end->format('Y-m-d') }}</strong></span>
        <span class="text-slate-400 hidden sm:inline">|</span>
        <span>يضم المهام المكتملة خلال الشهر، دورات التصميم ذات النشاط، والتسليمات المسجّلة.</span>
    </p>

    {{-- مؤشرات تنفيذية --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        <div class="rounded-2xl bg-gradient-to-br from-violet-600 to-violet-800 text-white p-5 shadow-lg border border-violet-500/30">
            <p class="text-xs font-semibold text-violet-200 uppercase tracking-wide">مهام مكتملة</p>
            <p class="text-3xl font-black mt-1 tabular-nums">{{ number_format($summary['tasks_completed']) }}</p>
            <p class="text-xs text-violet-200 mt-2">من {{ number_format($summary['tasks_assigned']) }} مسندة بالشهر</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">التزام الموعد (مهام)</p>
            <p class="text-3xl font-black text-slate-900 mt-1 tabular-nums">
                {{ $summary['tasks_on_time_rate_pct'] !== null ? $summary['tasks_on_time_rate_pct'].'%' : '—' }}
            </p>
            <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, $summary['tasks_on_time_rate_pct'] ?? 0) }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ $summary['tasks_on_time'] }} في الموعد · {{ $summary['tasks_late'] }} متأخر</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">التزام المصمم</p>
            <p class="text-3xl font-black text-fuchsia-700 mt-1 tabular-nums">
                {{ $summary['designer_on_time_rate_pct'] !== null ? $summary['designer_on_time_rate_pct'].'%' : '—' }}
            </p>
            <p class="text-xs text-slate-500 mt-2">{{ $summary['designer_submissions_month'] }} تسليم بالشهر</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">تسليمات ملفات</p>
            <p class="text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($summary['deliverables']) }}</p>
            <p class="text-xs text-slate-500 mt-2">مرفوعة خلال الشهر</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">دورات التصميم</p>
            <p class="text-3xl font-black text-violet-700 mt-1 tabular-nums">{{ number_format($summary['design_cycles_touched_month'] ?? 0) }}</p>
            <p class="text-xs text-slate-500 mt-2">سجلّ نشاط في الفترة</p>
        </div>
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 shadow-sm">
            <p class="text-xs font-bold text-amber-800 uppercase">مفتوحة متأخرة</p>
            <p class="text-3xl font-black text-amber-900 mt-1 tabular-nums">{{ number_format($summary['open_overdue_tasks']) }}</p>
            <p class="text-xs text-amber-800/80 mt-2">نهاية الشهر (غير مكتملة)</p>
        </div>
    </div>

    {{-- جدول تفصيلي --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-md overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-base font-bold text-slate-900">جدول الأداء حسب الموظف</h2>
            <span class="text-xs text-slate-500">{{ count($rows) }} موظفاً نشطاً</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1600px] w-full text-xs">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th rowspan="2" class="text-right px-3 py-2 font-bold sticky right-0 bg-slate-800 z-20 border-l border-slate-600 min-w-[150px] shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)]">الموظف</th>
                        <th rowspan="2" class="text-right px-2 py-2 font-bold border-slate-600 border-l min-w-[100px]">الوظيفة</th>
                        <th colspan="6" class="text-center px-2 py-1.5 font-bold border-slate-600 border-l bg-violet-900/90">المهام العامة</th>
                        <th colspan="4" class="text-center px-2 py-1.5 font-bold border-slate-600 border-l bg-fuchsia-900/90">مكتملة حسب النوع</th>
                        <th colspan="5" class="text-center px-2 py-1.5 font-bold border-slate-600 border-l bg-indigo-900/90">دورات التصميم</th>
                        <th colspan="4" class="text-center px-2 py-1.5 font-bold border-slate-600 border-l bg-slate-700">المشرف</th>
                    </tr>
                    <tr class="bg-slate-700 text-white text-[10px]">
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">مسند</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">مكتمل</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">إنجاز %</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">في الموعد</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">متأخر</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">%موعد</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">تصميم</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">مونتاج</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">مبيعات</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">أخرى</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">كمصمم</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">تسليم</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">بموعد</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">متأخر</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">%موعد</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">أنشأ</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">أكمل</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">ملغاة</th>
                        <th class="text-center px-1 py-2 font-semibold border-slate-600 border-l">متوسط أيام</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rows as $i => $row)
                        <tr class="hover:bg-violet-50/40 {{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="px-3 py-2 font-bold text-slate-900 sticky right-0 z-10 border-l border-slate-100 {{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}">{{ $row['user']->name }}</td>
                            <td class="px-2 py-2 text-slate-600 border-slate-100 border-l max-w-[120px] truncate" title="{{ $row['user']->employeeJob->name ?? '' }}">{{ $row['user']->employeeJob->name ?? '—' }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_assigned_in_month'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums font-semibold text-violet-800 border-slate-100 border-l">{{ $row['tasks_completed_in_month'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_completion_rate_pct'] !== null ? $row['tasks_completion_rate_pct'].'%' : '—' }}</td>
                            <td class="text-center px-1 py-2 tabular-nums text-emerald-700 border-slate-100 border-l">{{ $row['tasks_on_time'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums text-red-600 border-slate-100 border-l">{{ $row['tasks_late'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_on_time_rate_pct'] !== null ? $row['tasks_on_time_rate_pct'].'%' : '—' }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_completed_design'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_completed_video'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_completed_sales'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['tasks_completed_other'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['design_cycles_as_designer'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['designer_submissions_in_month'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['designer_on_time'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['designer_late'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['designer_on_time_rate_pct'] !== null ? $row['designer_on_time_rate_pct'].'%' : '—' }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['design_cycles_created_as_moderator'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['design_cycles_completed_as_moderator'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['design_cycles_cancelled_as_moderator'] }}</td>
                            <td class="text-center px-1 py-2 tabular-nums border-slate-100 border-l">{{ $row['moderator_avg_cycle_completion_days'] !== null ? $row['moderator_avg_cycle_completion_days'] : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- صف ثانوي: تفاصيل إضافية --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-800 text-sm">مؤشرات إضافية (نفس الموظفين)</div>
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-xs">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="text-right px-3 py-2 font-semibold sticky right-0 bg-slate-100">الموظف</th>
                        <th class="text-center px-2 py-2 font-semibold">متوسط ساعات الإكمال</th>
                        <th class="text-center px-2 py-2 font-semibold">مفتوحة متأخرة (آخر الشهر)</th>
                        <th class="text-center px-2 py-2 font-semibold">تسليمات مرفوعة</th>
                        <th class="text-center px-2 py-2 font-semibold">مهام بلا موعد (مكتملة)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rows as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 font-medium sticky right-0 bg-white">{{ $row['user']->name }}</td>
                            <td class="text-center py-2 tabular-nums">{{ $row['avg_completion_hours'] !== null ? $row['avg_completion_hours'] : '—' }}</td>
                            <td class="text-center py-2 tabular-nums text-amber-800 font-medium">{{ $row['open_overdue_tasks_end_of_month'] }}</td>
                            <td class="text-center py-2 tabular-nums">{{ $row['deliverables_submitted'] }}</td>
                            <td class="text-center py-2 tabular-nums text-slate-500">{{ $row['tasks_no_deadline_completed'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <details class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5 text-sm text-slate-700 group">
        <summary class="font-bold text-slate-900 cursor-pointer list-none flex items-center gap-2">
            <i class="fas fa-info-circle text-violet-600"></i>
            منطق الحساب والتصدير
            <i class="fas fa-chevron-down mr-auto text-slate-400 group-open:rotate-180 transition-transform"></i>
        </summary>
        <ul class="mt-4 space-y-2 list-disc list-inside text-xs leading-relaxed max-w-4xl">
            <li><strong>المهام المسندة:</strong> مهام أُنشئت خلال الشهر (حقل <code class="bg-white px-1 rounded border">created_at</code>).</li>
            <li><strong>المهام المكتملة:</strong> حالة مكتملة و<code class="bg-white px-1 rounded border">completed_at</code> ضمن الشهر.</li>
            <li><strong>في الموعد:</strong> إكمال المهمة قبل أو مع نهاية يوم الموعد النهائي.</li>
            <li><strong>دورات المصمم:</strong> دورات أُنشئت للمصمم خلال الشهر؛ «تسليم المصمم» عندما يقع <code class="bg-white px-1 rounded border">designer_submitted_at</code> في الشهر ومقارنته بـ <code class="bg-white px-1 rounded border">deadline_at</code>.</li>
            <li><strong>متوسط أيام إكمال الدورة (مشرف):</strong> لدورات اكتملت في الشهر: الفرق بين <code class="bg-white px-1 rounded border">created_at</code> و<code class="bg-white px-1 rounded border">completed_at</code>.</li>
            <li><strong>ملغاة كمشرف:</strong> دورات بحالة ملغاة و<code class="bg-white px-1 rounded border">updated_at</code> في الشهر (تقريب وقت الإلغاء).</li>
            <li><strong>ملف Excel:</strong> يتضمن «ملخص تنفيذي»، «أداء الموظفين»، «دورات التصميم» (كل دورة لها نشاط في الشهر)، و«المهام المكتملة» سطراً بسطر مع عمود في الموعد.</li>
        </ul>
    </details>
</div>
@endsection
