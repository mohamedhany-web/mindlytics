@extends('layouts.admin')

@section('title', 'التقارير الأكاديمية')
@section('header', 'التقارير الأكاديمية')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-book text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">التقارير الأكاديمية</h2>
                    <p class="text-sm text-slate-600 mt-1">تقارير عن الامتحانات، الواجبات، المحاضرات، والمجموعات</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </section>

    <!-- الفلاتر -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                الفلاتر
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الفترة</label>
                    <select name="period" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>هذا العام</option>
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div class="md:col-span-3 flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.reports.academic') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- الإحصائيات -->
    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-clipboard-check text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الامتحانات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_exams'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-tasks text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المحاولات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_attempts'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-file-alt text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الواجبات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_assignments'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-video text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المحاضرات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_lectures'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-certificate text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الشهادات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_certificates'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- الامتحانات والمحاولات -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-600"></i>
                    الامتحانات
                </h3>
                <span class="text-xs font-semibold text-slate-600">{{ $exams->total() }} امتحان</span>
            </div>
            <div class="p-6">
                @if($exams->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($exams as $exam)
                        <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-slate-900">{{ htmlspecialchars($exam->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</h4>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                    <i class="fas fa-users"></i>
                                    {{ number_format($exam->attempts_count) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $exam->created_at->format('d/m/Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($exams->hasPages())
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            {{ $exams->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-slate-600">لا توجد امتحانات</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    الواجبات
                </h3>
                <span class="text-xs font-semibold text-slate-600">{{ $assignments->total() }} واجب</span>
            </div>
            <div class="p-6">
                @if($assignments->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($assignments as $assignment)
                        <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-slate-900">{{ htmlspecialchars($assignment->title ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</h4>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle"></i>
                                    {{ number_format($assignment->submissions_count ?? 0) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $assignment->created_at->format('d/m/Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($assignments->hasPages())
                        <div class="mt-5 border-t border-slate-200 pt-5">
                            {{ $assignments->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-slate-600">لا توجد واجبات</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
