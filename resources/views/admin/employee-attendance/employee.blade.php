@extends('layouts.admin')

@section('title', 'ملف حضور — ' . $employee->name)
@section('header', 'ملف حضور الموظف')

@section('content')
@php $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels(); @endphp

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                    {{ mb_substr($employee->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h1>
                    <p class="text-gray-600 mt-1">
                        {{ $employee->employeeJob->name ?? '—' }}
                        @if($employee->workSchedule)
                            · <i class="fas fa-clock text-gray-400"></i> {{ $employee->workSchedule->name }}
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.employee-attendance.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للتقارير
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-lg border border-green-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">أيام مكتملة</p>
                    <p class="text-2xl font-black text-green-700">{{ $summary['completed_days'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-amber-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">أيام تأخير</p>
                    <p class="text-2xl font-black text-amber-700">{{ $summary['late_days'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-violet-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي ساعات</p>
                    <p class="text-2xl font-black text-violet-700">{{ $summary['total_hours'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-violet-100 text-violet-600">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مهام منجزة</p>
                    <p class="text-2xl font-black text-blue-700">{{ $summary['tasks_completed'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-100 text-blue-600">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-red-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مهام متأخرة</p>
                    <p class="text-2xl font-black text-red-700">{{ $summary['tasks_overdue'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-slate-50 to-gray-100/50">
            <h2 class="text-lg font-semibold text-gray-900">سجل الحضور</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">حضور</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">انصراف</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">ساعات</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 tabular-nums font-medium">{{ $row->work_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $row->clock_in_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $row->clock_out_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $row->worked_minutes ? number_format($row->worked_minutes / 60, 2) : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $statusLabels[$row->status] ?? $row->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">لا توجد سجلات حضور لهذا الموظف.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
