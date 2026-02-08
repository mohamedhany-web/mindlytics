@extends('layouts.admin')

@section('title', 'إحصائيات المستخدمين')
@section('header', 'إحصائيات المستخدمين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">إحصائيات المستخدمين</h2>
                    <p class="text-sm text-slate-600 mt-1">تحليل تفصيلي لنمو المستخدمين</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.statistics.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
                <a href="{{ route('admin.statistics.courses') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-code"></i>
                    إحصائيات الكورسات
                </a>
            </div>
        </div>
    </section>

    <!-- توزيع المستخدمين حسب الدور -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-user-tag text-blue-600"></i>
                توزيع المستخدمين حسب الدور
            </h3>
        </div>
        <div class="p-6">
            @if($usersByRole->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($usersByRole as $role)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-slate-700 mb-1">
                                        @if($role->role === 'student')
                                            الطلاب
                                        @elseif($role->role === 'instructor' || $role->role === 'teacher')
                                            المدربين
                                        @elseif($role->role === 'admin' || $role->role === 'super_admin')
                                            الإدارة
                                        @else
                                            {{ htmlspecialchars($role->role, ENT_QUOTES, 'UTF-8') }}
                                        @endif
                                    </h4>
                                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($role->count) }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-sm
                                    @if($role->role === 'student') bg-emerald-100 text-emerald-600
                                    @elseif($role->role === 'instructor' || $role->role === 'teacher') bg-blue-100 text-blue-600
                                    @elseif($role->role === 'admin' || $role->role === 'super_admin') bg-purple-100 text-purple-600
                                    @else bg-slate-100 text-slate-600
                                    @endif">
                                    <i class="fas 
                                        @if($role->role === 'student') fa-user-graduate
                                        @elseif($role->role === 'instructor' || $role->role === 'teacher') fa-chalkboard-teacher
                                        @elseif($role->role === 'admin' || $role->role === 'super_admin') fa-user-shield
                                        @else fa-user
                                        @endif text-lg"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-900 mb-1">لا توجد بيانات</p>
                    <p class="text-xs text-slate-600">لا توجد بيانات متاحة حالياً.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- نمو المستخدمين حسب الشهر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                نمو المستخدمين حسب الشهر (آخر 12 شهر)
            </h3>
        </div>
        <div class="p-6">
            @if($usersPerMonth->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الشهر</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">السنة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">عدد المستخدمين</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($usersPerMonth as $stat)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::create($stat->year, $stat->month, 1)->locale('ar')->format('F') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $stat->year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold text-slate-900">{{ number_format($stat->count) }}</span>
                                        <div class="flex-1 max-w-xs bg-slate-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-600 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ min(($stat->count / max($usersPerMonth->max('count'), 1)) * 100, 100) }}%"></div>
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
                    <p class="text-sm text-slate-600">لا توجد إحصائيات للمستخدمين متاحة</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
