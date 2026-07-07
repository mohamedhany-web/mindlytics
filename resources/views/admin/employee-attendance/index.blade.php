@extends('layouts.admin')

@section('title', 'تقارير حضور الموظفين')
@section('header', 'تقارير حضور الموظفين')

@section('content')
@php
    $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels();
    $statusBadgeClasses = [
        'completed' => 'bg-green-100 text-green-800',
        'active' => 'bg-blue-100 text-blue-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'absent' => 'bg-red-100 text-red-800',
        'late' => 'bg-orange-100 text-orange-800',
        'incomplete' => 'bg-rose-100 text-rose-800',
        'on_leave' => 'bg-purple-100 text-purple-800',
        'off_day' => 'bg-gray-100 text-gray-800',
    ];
    $hasFilters = request()->hasAny(['employee_id', 'job_id', 'status', 'from', 'to', 'late_only']);
@endphp

<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تقارير حضور الموظفين</h1>
                <p class="text-gray-600 mt-1">متابعة الحضور والانصراف والتأخير والخصومات التلقائية</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.employee-attendance.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
                <a href="{{ route('admin.employee-attendance.penalty-settings') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-cog"></i>
                    إعدادات الخصومات
                </a>
            </div>
        </div>

        <!-- تطبيق خصومات يدوياً -->
        <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200">
            <form method="post" action="{{ route('admin.employee-attendance.apply-penalties') }}"
                  class="flex flex-col sm:flex-row sm:items-end gap-3"
                  onsubmit="return confirm('تطبيق خصومات الحضور للتاريخ المحدد؟');">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-amber-900 mb-1">تطبيق خصومات يدوياً</label>
                    <p class="text-xs text-amber-800 mb-2">للغياب والتأخير غير المُعالَج — يُنفَّذ تلقائياً يومياً الساعة 02:30</p>
                    <input type="date" name="date" value="{{ old('date', now()->subDay()->format('Y-m-d')) }}"
                           class="w-full sm:w-auto px-3 py-2 border border-amber-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-bolt mr-1"></i>تطبيق الآن
                </button>
            </form>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 items-end">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                    <select name="employee_id" id="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الموظفين</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">الوظيفة</label>
                    <select name="job_id" id="job_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الوظائف</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" @selected(request('job_id') == $job->id)>{{ $job->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        @foreach($statusLabels as $k => $label)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="from" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="to" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="flex items-center gap-2 h-[42px] px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="late_only" value="1" @checked(request('late_only')) class="rounded text-blue-600">
                        <span class="text-sm text-gray-700">متأخرين فقط</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>بحث
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('admin.employee-attendance.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-blue-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,249,255,0.95) 50%, rgba(224,242,254,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي السجلات</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-green-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,250,0.95) 50%, rgba(209,250,229,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">مكتمل</p>
                    <p class="text-3xl font-black text-green-700">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-amber-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,251,235,0.95) 50%, rgba(254,243,199,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">متأخر</p>
                    <p class="text-3xl font-black text-amber-700">{{ $stats['late'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-red-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(254,242,242,0.95) 50%, rgba(254,226,226,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">غائب</p>
                    <p class="text-3xl font-black text-red-700">{{ $stats['absent'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-violet-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.95) 50%, rgba(237,233,254,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">متوسط ساعات</p>
                    <p class="text-3xl font-black text-violet-700">{{ $stats['avg_hours'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 card-hover-effect border-2 border-rose-200/50 shadow-xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,241,242,0.95) 50%, rgba(255,228,230,0.9) 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي خصومات</p>
                    <p class="text-2xl font-black text-rose-700">{{ number_format($stats['total_deductions'] ?? 0, 2) }}</p>
                    <p class="text-xs text-rose-600">ج.م</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول السجلات -->
    @if($records->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-slate-50 to-gray-100/50">
                <h2 class="text-lg font-semibold text-gray-900">سجلات الحضور</h2>
                <p class="text-sm text-gray-600 mt-0.5">{{ $records->total() }} سجل</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">التاريخ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">الموظف</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">موعد العمل</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">حضور</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">انصراف</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">ساعات</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">الحالة</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">خصومات</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($records as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 tabular-nums font-medium text-gray-900">{{ $row->work_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $row->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $row->user->employeeJob->name ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 tabular-nums">{{ $row->workSchedule?->timeRangeLabel() ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums text-gray-900">{{ $row->clock_in_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums text-gray-900">{{ $row->clock_out_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">
                                @if($row->worked_minutes)
                                    <span class="font-medium text-gray-900">{{ number_format($row->worked_minutes / 60, 2) }}</span>
                                    <span class="text-xs text-gray-500">/ {{ number_format($row->required_minutes / 60, 2) }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadgeClasses[$row->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$row->status] ?? $row->status }}
                                    </span>
                                    @if($row->is_late)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">متأخر</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                @if($row->totalDeductionAmount() > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-rose-100 text-rose-800">
                                        {{ number_format($row->totalDeductionAmount(), 2) }} ج.م
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.employee-attendance.employee', $row->user_id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg font-medium transition-colors">
                                    <i class="fas fa-user"></i>
                                    ملف الموظف
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $records->links() }}
            </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-check text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد سجلات حضور</p>
            <p class="text-sm text-gray-600 mb-6">
                @if($hasFilters)
                    لا توجد نتائج مطابقة للفلاتر المحددة — جرّب تغيير معايير البحث
                @else
                    ستظهر سجلات الحضور هنا عندما يسجّل الموظفون حضورهم
                @endif
            </p>
            @if($hasFilters)
                <a href="{{ route('admin.employee-attendance.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-times"></i>
                    <span>مسح الفلاتر</span>
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
