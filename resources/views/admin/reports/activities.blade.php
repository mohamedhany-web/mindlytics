@extends('layouts.admin')

@section('title', 'تقارير النشاطات')
@section('header', 'تقارير النشاطات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-history text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">تقارير النشاطات</h2>
                    <p class="text-sm text-slate-600 mt-1">سجل شامل لجميع النشاطات والتحركات في المنصة</p>
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
                    <label class="block text-xs font-semibold text-slate-700 mb-2">نوع النشاط</label>
                    <input type="text" name="action" value="{{ $action ?? '' }}" maxlength="100" placeholder="بحث في نوع النشاط..." class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div class="md:col-span-4 flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.reports.activities') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
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
                    <i class="fas fa-list text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي النشاطات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-calendar-day text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">اليوم</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['today'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-calendar-week text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">هذا الأسبوع</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['this_week'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-calendar-alt text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">هذا الشهر</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['this_month'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- قائمة النشاطات -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                سجل النشاطات
            </h3>
            <span class="text-xs font-semibold text-slate-600">{{ $activities->total() }} نشاط</span>
        </div>
        <div class="p-6">
            @if($activities->count() > 0)
                <div class="space-y-3">
                    @foreach($activities as $activity)
                    <div class="rounded-lg border border-slate-200 bg-white p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 flex">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-bold text-slate-900">{{ htmlspecialchars($activity->user->name ?? 'مستخدم مجهول', ENT_QUOTES, 'UTF-8') }}</p>
                                    <span class="text-xs text-slate-500">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-700 mb-1">{{ htmlspecialchars($activity->action ?? 'نشاط', ENT_QUOTES, 'UTF-8') }}</p>
                                @if($activity->description)
                                <p class="text-xs text-slate-600">{{ htmlspecialchars(Str::limit($activity->description, 100), ENT_QUOTES, 'UTF-8') }}</p>
                                @endif
                                <p class="text-xs text-slate-500 mt-2">{{ $activity->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($activities->hasPages())
                    <div class="mt-5 border-t border-slate-200 pt-5">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-history text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-900 mb-1">لا توجد بيانات</p>
                    <p class="text-xs text-slate-600">لا توجد نشاطات مطابقة للبحث الحالي.</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
