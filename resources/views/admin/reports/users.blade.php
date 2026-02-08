@extends('layouts.admin')

@section('title', 'تقارير المستخدمين')
@section('header', 'تقارير المستخدمين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">تقارير المستخدمين</h2>
                    <p class="text-sm text-slate-600 mt-1">تقارير شاملة عن المستخدمين، الطلاب، المدربين، والإدارة</p>
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
            <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-calendar text-blue-600 text-sm"></i>
                        الفترة
                    </label>
                    <select name="period" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>هذا العام</option>
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>الكل</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                        من تاريخ
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                        إلى تاريخ
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-tag text-blue-600 text-sm"></i>
                        الدور
                    </label>
                    <select name="role" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الأدوار</option>
                        <option value="student" {{ $role == 'student' ? 'selected' : '' }}>الطلاب</option>
                        <option value="instructor" {{ $role == 'instructor' ? 'selected' : '' }}>المدربين</option>
                        <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>الإدارة</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.reports.users') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    <a href="{{ route('admin.reports.export.users', array_merge(request()->all())) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-file-excel"></i>
                        تصدير إلى Excel
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
                    <i class="fas fa-users text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي المستخدمين</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-graduate text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الطلاب</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['students']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-chalkboard-teacher text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">المدربين</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['instructors']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-user-shield text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">الإدارة</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['admins']) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- قائمة المستخدمين -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                قائمة المستخدمين
            </h3>
            <span class="text-xs font-semibold text-slate-600">{{ $users->total() }} مستخدم</span>
        </div>
        <div class="p-6">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الاسم</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">البريد</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الدور</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">{{ htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ htmlspecialchars($user->phone ?? '-', ENT_QUOTES, 'UTF-8') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border
                                        @if($user->role == 'student') bg-emerald-100 text-emerald-700 border-emerald-200
                                        @elseif(in_array($user->role, ['instructor', 'teacher'])) bg-amber-100 text-amber-700 border-amber-200
                                        @else bg-purple-100 text-purple-700 border-purple-200
                                        @endif">
                                        @if($user->role == 'student') الطلاب
                                        @elseif(in_array($user->role, ['instructor', 'teacher'])) المدربين
                                        @else الإدارة
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="mt-5 border-t border-slate-200 pt-5">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-900 mb-1">لا توجد بيانات</p>
                    <p class="text-xs text-slate-600">لا توجد مستخدمين مطابقين للبحث الحالي.</p>
                </div>
            @endif
        </div>
    </section>
</div>

@push('scripts')
<script>
    // حماية من XSS في بيانات البحث
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Sanitization يتم في الخادم
        });
    }
</script>
@endpush
@endsection
