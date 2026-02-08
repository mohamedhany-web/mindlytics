@extends('layouts.admin')

@section('title', 'إحصائيات الكورسات')
@section('header', 'إحصائيات الكورسات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-code text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">إحصائيات الكورسات</h2>
                    <p class="text-sm text-slate-600 mt-1">تحليل تفصيلي للكورسات والتسجيلات</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.statistics.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
                <a href="{{ route('admin.statistics.users') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-users"></i>
                    إحصائيات المستخدمين
                </a>
            </div>
        </div>
    </section>

    <!-- إحصائيات الكورسات -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-700 mb-1">إجمالي الكورسات</p>
                    <p class="text-3xl font-black text-slate-900">{{ number_format($coursesStats->count()) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-700 mb-1">إجمالي التسجيلات</p>
                    <p class="text-3xl font-black text-slate-900">{{ number_format($coursesStats->sum('enrollments_count')) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-700 mb-1">متوسط التسجيلات</p>
                    <p class="text-3xl font-black text-slate-900">{{ number_format($coursesStats->count() > 0 ? $coursesStats->avg('enrollments_count') : 0, 1) }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- قائمة الكورسات -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                الكورسات
            </h3>
        </div>
        <div class="p-6">
            @if($coursesStats->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الكورس</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">عدد التسجيلات</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($coursesStats->sortByDesc('enrollments_count') as $course)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }}</h4>
                                        <p class="text-xs text-slate-600 mt-1">
                                            {{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                        <i class="fas fa-users"></i>
                                        {{ number_format($course->enrollments_count) }} تسجيل
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $course->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $course->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">لا توجد كورسات</h3>
                    <p class="text-sm text-slate-600">لا توجد كورسات متاحة</p>
                </div>
            @endif
        </div>
    </section>

    <!-- التسجيلات حسب الشهر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                التسجيلات حسب الشهر (آخر 12 شهر)
            </h3>
        </div>
        <div class="p-6">
            @if($enrollmentsPerMonth->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الشهر</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">السنة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">عدد التسجيلات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($enrollmentsPerMonth as $stat)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::create($stat->year, $stat->month, 1)->locale('ar')->format('F') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $stat->year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold text-slate-900">{{ number_format($stat->count) }}</span>
                                        <div class="flex-1 max-w-xs bg-slate-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ min(($stat->count / max($enrollmentsPerMonth->max('count'), 1)) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">لا توجد بيانات</h3>
                    <p class="text-sm text-slate-600">لا توجد إحصائيات للتسجيلات متاحة</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
