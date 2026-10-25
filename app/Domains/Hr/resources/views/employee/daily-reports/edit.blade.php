@extends('layouts.employee')

@section('title', 'تقرير يومي — ' . $date->format('Y-m-d'))
@section('header', 'التقرير اليومي')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('employee.daily-reports.index') }}" class="text-sm text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>

    <form method="post" action="{{ route('employee.daily-reports.store') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf
        <input type="hidden" name="report_date" value="{{ $date->toDateString() }}">

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">تاريخ التقرير</label>
            <p class="text-lg font-bold text-gray-900">{{ $date->format('Y-m-d') }} — {{ $date->locale('ar')->translatedFormat('l') }}</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ملخص اليوم *</label>
            <textarea name="summary" rows="3" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">{{ old('summary', $report->summary) }}</textarea>
            @error('summary')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">المهام المنجزة *</label>
            <textarea name="tasks_done" rows="5" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" placeholder="اذكر كل ما أنجزته اليوم...">{{ old('tasks_done', $report->tasks_done) }}</textarea>
            @error('tasks_done')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">خطة الغد</label>
            <textarea name="tomorrow_plan" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">{{ old('tomorrow_plan', $report->tomorrow_plan) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">معوقات / ملاحظات</label>
            <textarea name="blockers" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">{{ old('blockers', $report->blockers) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ساعات العمل</label>
            <input type="number" name="hours_worked" step="0.5" min="0" max="24" value="{{ old('hours_worked', $report->hours_worked) }}" class="w-32 rounded-xl border border-gray-300 px-3 py-2 text-sm">
        </div>

        <div class="flex flex-wrap gap-3 pt-2 border-t">
            <button type="submit" name="submit" value="0" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm">حفظ مسودة</button>
            <button type="submit" name="submit" value="1" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                <i class="fas fa-paper-plane ml-1"></i> إرسال التقرير
            </button>
        </div>
    </form>
</div>
@endsection
