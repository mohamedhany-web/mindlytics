@extends('layouts.admin')

@section('title', 'تقارير الكورسات')
@section('header', 'تقارير الكورسات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-graduation-cap text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">تقارير الكورسات</h2>
                    <p class="text-sm text-slate-600 mt-1">تقارير عن الكورسات، التسجيلات، التقدم، والإنجازات</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
                <a href="{{ route('admin.reports.export.courses', array_merge(request()->all())) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-file-excel"></i>
                    تصدير إلى Excel
                </a>
            </div>
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
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.reports.courses') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- الإحصائيات -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-book text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي الكورسات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الكورسات النشطة</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['active']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-user-graduate text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي التسجيلات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total_enrollments']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-chart-line text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">التسجيلات النشطة</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['active_enrollments']) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- قائمة الكورسات -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                قائمة الكورسات
            </h3>
            <span class="text-xs font-semibold text-slate-600">{{ $courses->total() }} كورس</span>
        </div>
        <div class="p-6">
            @if($courses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">العنوان</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">المسار</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">المجموعة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">التسجيلات</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($courses as $course)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{{ $course->id }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ htmlspecialchars($course->academicYear->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                        <i class="fas fa-users"></i>
                                        {{ number_format($course->enrollments_count) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $course->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $course->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $course->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($courses->hasPages())
                    <div class="mt-5 border-t border-slate-200 pt-5">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-900 mb-1">لا توجد بيانات</p>
                    <p class="text-xs text-slate-600">لا توجد كورسات مطابقة للبحث الحالي.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- الكورسات الأكثر شعبية -->
    @if($popularCourses->count() > 0)
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-trophy text-amber-600"></i>
                الكورسات الأكثر شعبية
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($popularCourses as $course)
                <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-900">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }}</h4>
                            <p class="text-xs text-slate-600 mt-1">
                                {{ htmlspecialchars($course->academicYear->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }} • 
                                {{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                            <i class="fas fa-users"></i>
                            {{ number_format($course->enrollments_count) }} طالب
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
@endsection
