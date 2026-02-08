@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - Mindlytics')
@section('header', 'إدارة المستخدمين')

@push('styles')
<style>
    .user-card {
        transition: all 0.2s ease;
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        border: 1px solid rgba(100, 116, 139, 0.2);
    }

    .user-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table-row {
        transition: background-color 0.15s ease;
    }

    .table-row:hover {
        background: rgba(59, 130, 246, 0.05);
    }

    .avatar-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
</style>
@endpush

@section('content')
@php
    // التأكد من وجود المتغيرات
    $stats = $stats ?? [];
    $trends = $trends ?? [];
    $users = $users ?? collect();
    $recentUsers = $recentUsers ?? collect();
    $recentlyActiveUsers = $recentlyActiveUsers ?? collect();
    $usersByRole = $usersByRole ?? collect();
    $usersByMonth = $usersByMonth ?? collect();
    
    $statsCards = [
        [
            'label' => 'إجمالي المستخدمين',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-users',
            'color' => 'blue',
            'description' => 'كل المستخدمين المسجلين',
            'new_this_month' => $stats['new_this_month'] ?? 0,
            'trend' => $trends['users'] ?? null,
        ],
        [
            'label' => 'المستخدمون النشطون',
            'value' => number_format($stats['active'] ?? 0),
            'icon' => 'fas fa-user-check',
            'color' => 'emerald',
            'description' => 'حسابات نشطة',
        ],
        [
            'label' => 'المدرسون',
            'value' => number_format($stats['teachers'] ?? 0),
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => 'indigo',
            'description' => 'مدربون مسجلون',
            'new_this_month' => $stats['new_teachers_this_month'] ?? 0,
            'trend' => $trends['teachers'] ?? null,
        ],
        [
            'label' => 'الطلاب',
            'value' => number_format($stats['students'] ?? 0),
            'icon' => 'fas fa-user-graduate',
            'color' => 'purple',
            'description' => 'طلاب مسجلون',
            'new_this_month' => $stats['new_students_this_month'] ?? 0,
            'trend' => $trends['students'] ?? null,
        ],
    ];

    $roles = [
        'super_admin' => ['label' => 'مدير عام', 'badge' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'admin' => ['label' => 'إداري', 'badge' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'instructor' => ['label' => 'مدرب', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'teacher' => ['label' => 'مدرس', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'student' => ['label' => 'طالب', 'badge' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'parent' => ['label' => 'ولي أمر', 'badge' => 'bg-indigo-100 text-indigo-700 border border-indigo-200'],
        'employee' => ['label' => 'موظف', 'badge' => 'bg-amber-100 text-amber-700 border border-amber-200']
    ];
    
    $colorConfigs = [
        'blue' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%)',
            'border' => 'border-blue-200/50 hover:border-blue-300/70',
            'text' => 'text-blue-800/80',
            'value' => 'from-blue-700 via-blue-600 to-sky-600',
            'icon' => 'from-blue-500 via-blue-600 to-sky-600',
            'iconShadow' => 'rgba(59, 130, 246, 0.4)',
            'hover' => 'from-blue-100/60 via-sky-100/40 to-blue-50/30',
        ],
        'emerald' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%)',
            'border' => 'border-emerald-200/50 hover:border-emerald-300/70',
            'text' => 'text-emerald-800/80',
            'value' => 'from-emerald-700 via-green-600 to-teal-600',
            'icon' => 'from-emerald-500 via-green-500 to-teal-600',
            'iconShadow' => 'rgba(16, 185, 129, 0.4)',
            'hover' => 'from-emerald-100/60 via-green-100/40 to-teal-50/30',
        ],
        'indigo' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%)',
            'border' => 'border-indigo-200/50 hover:border-indigo-300/70',
            'text' => 'text-indigo-800/80',
            'value' => 'from-indigo-700 via-purple-600 to-violet-600',
            'icon' => 'from-indigo-500 via-purple-500 to-violet-600',
            'iconShadow' => 'rgba(99, 102, 241, 0.4)',
            'hover' => 'from-indigo-100/60 via-purple-100/40 to-violet-50/30',
        ],
        'purple' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%)',
            'border' => 'border-purple-200/50 hover:border-purple-300/70',
            'text' => 'text-purple-800/80',
            'value' => 'from-purple-700 via-purple-600 to-violet-600',
            'icon' => 'from-purple-500 via-purple-500 to-violet-600',
            'iconShadow' => 'rgba(168, 85, 247, 0.4)',
            'hover' => 'from-purple-100/60 via-purple-100/40 to-violet-50/30',
        ],
    ];
@endphp

<div class="space-y-6">
    <!-- الهيدر المحسن -->
    <div class="bg-gradient-to-r from-slate-50 to-white rounded-2xl p-6 border border-slate-200 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-1">إدارة المستخدمين</h1>
                    <p class="text-sm sm:text-base text-slate-600 font-medium">متابعة الحسابات، الصلاحيات، وحالة النشاط عبر المنصة</p>
                </div>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-user-plus"></i>
                <span>إضافة مستخدم جديد</span>
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach ($statsCards as $stat)
            @php $config = $colorConfigs[$stat['color']]; @endphp
            <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 bg-white shadow-md hover:shadow-lg transition-all duration-200 w-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 mb-2">{{ $stat['label'] }}</p>
                        <p class="text-4xl sm:text-3xl font-black text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 mr-3 sm:mr-0">
                        <i class="{{ $stat['icon'] }} text-white text-xl"></i>
                    </div>
                </div>
                @if(isset($stat['new_this_month']))
                    <p class="text-xs font-medium text-slate-600 mb-2">
                        {{ $stat['label'] == 'إجمالي المستخدمين' ? 'مستخدمون' : ($stat['label'] == 'المدرسون' ? 'مدربون' : 'طلاب') }} جدد هذا الشهر: 
                        <span class="font-bold text-blue-600">{{ number_format($stat['new_this_month']) }}</span>
                    </p>
                @else
                    <p class="text-xs font-medium text-slate-600 mb-2">{{ $stat['description'] }}</p>
                @endif
                @if(isset($stat['trend']) && $stat['trend'])
                    @php
                        $diff = (int) round($stat['trend']['difference']);
                        $percent = $stat['trend']['percent'];
                        $positive = $diff >= 0;
                    @endphp
                    <div class="mt-2 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold {{ $positive ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $positive ? '+' : '' }}{{ number_format($diff) }}
                        </span>
                        <span class="text-slate-600">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold {{ $positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- البحث والفلترة -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-filter text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">ابحث وفلتر المستخدمين حسب الدور والحالة</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-search text-blue-600 text-sm"></i>
                        البحث
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-blue-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="الاسم، البريد الإلكتروني، رقم الهاتف" 
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-tag text-blue-600 text-sm"></i>
                        الدور
                    </label>
                    <select name="role" 
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الأدوار</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>مدير عام</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>إداري</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>مدرب</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>مدرس</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>طالب</option>
                        <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>ولي أمر</option>
                        <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>موظف</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600 text-sm"></i>
                        الحالة
                    </label>
                    <select name="status" 
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                    @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors" 
                       title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة المستخدمين -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة المستخدمين</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">
                        <span class="font-bold text-blue-600">{{ $users->total() }}</span> مستخدم
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-blue-600"></i>
                                <span>المستخدم</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-tag text-blue-600"></i>
                                <span>الدور</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-toggle-on text-blue-600"></i>
                                <span>الحالة</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-blue-600"></i>
                                <span>تاريخ التسجيل</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-cog text-blue-600"></i>
                                <span>الإجراءات</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse ($users as $user)
                        <tr class="table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                    </div>
                                    <div class="space-y-1">
                                        <p class="font-bold text-slate-900 text-base">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-600 font-medium flex items-center gap-2">
                                            <i class="fas fa-envelope text-blue-500 text-xs"></i>
                                            {{ $user->email ?: 'لا يوجد بريد إلكتروني' }}
                                        </p>
                                        <p class="text-xs text-slate-600 font-medium flex items-center gap-2">
                                            <i class="fas fa-phone text-blue-500 text-xs"></i>
                                            {{ $user->phone }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    // التحقق من كون المستخدم موظف أولاً
                                    if ($user->is_employee) {
                                        $roleKey = 'employee';
                                    } else {
                                        $roleKey = $user->role;
                                    }
                                    $roleMeta = $roles[$roleKey] ?? $roles['student'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $roleMeta['badge'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $roleMeta['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-semibold text-slate-900">{{ $user->created_at->format('Y-m-d') }}</div>
                                    <div class="text-xs text-slate-600 font-medium">{{ $user->created_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="editUser({{ $user->id }})" 
                                            class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                            title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <button type="button" onclick="deleteUser({{ $user->id }})" 
                                                class="w-9 h-9 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                                title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-users text-3xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg mb-1">لا توجد نتائج مطابقة</p>
                                        <p class="text-sm text-slate-600 font-medium">جرب تغيير معايير البحث</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    <!-- آخر المستخدمين والمستخدمين النشطون -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر المستخدمين -->
        <section class="user-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">آخر المستخدمين المسجلين</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">آخر 10 مستخدمين انضموا للمنصة</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentUsers as $recentUser)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                    <div class="avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ mb_substr($recentUser->name, 0, 1, 'UTF-8') }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 truncate">{{ $recentUser->name }}</p>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            @php
                                $recentRoleKey = $recentUser->is_employee ? 'employee' : ($recentUser->role ?? 'student');
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ ($roles[$recentRoleKey] ?? $roles['student'])['badge'] }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ ($roles[$recentRoleKey] ?? $roles['student'])['label'] }}
                            </span>
                            <span class="text-xs text-slate-600 font-medium">{{ $recentUser->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $recentUser->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ $recentUser->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <p class="text-slate-600 font-medium">لا توجد مستخدمين بعد</p>
                </div>
                @endforelse
            </div>
        </section>

        <!-- المستخدمين النشطون مؤخراً -->
        <section class="user-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">المستخدمين النشطون مؤخراً</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">نشطوا خلال آخر 7 أيام</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentlyActiveUsers as $activeUser)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ mb_substr($activeUser->name, 0, 1, 'UTF-8') }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 truncate">{{ $activeUser->name }}</p>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            @php
                                $activeRoleKey = $activeUser->is_employee ? 'employee' : ($activeUser->role ?? 'student');
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ ($roles[$activeRoleKey] ?? $roles['student'])['badge'] }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ ($roles[$activeRoleKey] ?? $roles['student'])['label'] }}
                            </span>
                            <span class="text-xs text-slate-600 font-medium">آخر نشاط: {{ $activeUser->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-md"></div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-check text-2xl text-emerald-600"></i>
                    </div>
                    <p class="text-slate-600 font-medium">لا يوجد مستخدمين نشطون مؤخراً</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- توزيع المستخدمين وإحصائيات التسجيل -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- توزيع المستخدمين حسب الدور -->
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">توزيع المستخدمين حسب الدور</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">نظرة عامة على توزيع المستخدمين</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @php
                        $totalForPercentage = $stats['total'] > 0 ? $stats['total'] : 1;
                        $roleDistribution = [
                            'super_admin' => ['count' => $usersByRole['super_admin'] ?? 0, 'label' => 'مدير عام', 'color' => 'rose', 'icon' => 'fas fa-user-shield'],
                            'admin' => ['count' => $usersByRole['admin'] ?? 0, 'label' => 'إداري', 'color' => 'rose', 'icon' => 'fas fa-user-shield'],
                            'instructor' => ['count' => $usersByRole['instructor'] ?? 0, 'label' => 'مدرب', 'color' => 'sky', 'icon' => 'fas fa-chalkboard-teacher'],
                            'teacher' => ['count' => $usersByRole['teacher'] ?? 0, 'label' => 'مدرس', 'color' => 'sky', 'icon' => 'fas fa-chalkboard-teacher'],
                            'student' => ['count' => $usersByRole['student'] ?? 0, 'label' => 'طالب', 'color' => 'emerald', 'icon' => 'fas fa-user-graduate'],
                            'parent' => ['count' => $usersByRole['parent'] ?? 0, 'label' => 'ولي أمر', 'color' => 'indigo', 'icon' => 'fas fa-user-friends'],
                            'employee' => ['count' => \App\Models\User::where('is_employee', true)->count(), 'label' => 'موظف', 'color' => 'amber', 'icon' => 'fas fa-briefcase'],
                        ];
                        // دمج super_admin مع admin
                        if (isset($roleDistribution['super_admin']) && isset($roleDistribution['admin'])) {
                            $roleDistribution['admin']['count'] += $roleDistribution['super_admin']['count'];
                            unset($roleDistribution['super_admin']);
                        }
                        // دمج instructor مع teacher
                        if (isset($roleDistribution['instructor']) && isset($roleDistribution['teacher'])) {
                            $roleDistribution['instructor']['count'] += $roleDistribution['teacher']['count'];
                            $roleDistribution['instructor']['label'] = 'مدرسون';
                            unset($roleDistribution['teacher']);
                        }
                    @endphp
                    @foreach($roleDistribution as $roleKey => $roleData)
                        @php
                            $percentage = ($roleData['count'] / $totalForPercentage) * 100;
                            $colorClasses = [
                                'rose' => ['bg' => 'bg-rose-500', 'text' => 'text-rose-600', 'light' => 'bg-rose-100', 'border' => 'border-rose-200'],
                                'sky' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'light' => 'bg-blue-100', 'border' => 'border-blue-200'],
                                'emerald' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-100', 'border' => 'border-emerald-200'],
                                'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-100', 'border' => 'border-indigo-200'],
                                'amber' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600', 'light' => 'bg-amber-100', 'border' => 'border-amber-200'],
                            ];
                            $color = $colorClasses[$roleData['color']] ?? $colorClasses['sky'];
                        @endphp
                        <div class="p-3 rounded-lg border border-slate-200 hover:border-{{ $color['text'] }}/30 hover:shadow-md transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 {{ $color['light'] }} rounded-lg flex items-center justify-center">
                                        <i class="{{ $roleData['icon'] }} {{ $color['text'] }} text-base"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $roleData['label'] }}</p>
                                        <p class="text-xs text-slate-600 font-medium">{{ number_format($roleData['count']) }} مستخدم</p>
                                    </div>
                                </div>
                                <span class="text-base font-bold {{ $color['text'] }}">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="{{ $color['bg'] }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- إحصائيات التسجيل الشهرية -->
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">إحصائيات التسجيل الشهرية</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">آخر 6 أشهر</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($usersByMonth->count() > 0)
                    @php
                        $maxCount = $usersByMonth->max('count') ?: 1;
                        $monthNames = [
                            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($usersByMonth->reverse() as $monthData)
                            @php
                                $barHeight = ($monthData->count / $maxCount) * 100;
                                $monthName = $monthNames[$monthData->month] ?? $monthData->month;
                            @endphp
                            <div class="p-3 rounded-lg border border-slate-200 hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-slate-900">{{ $monthName }} {{ $monthData->year }}</span>
                                    <span class="text-base font-bold text-purple-600">{{ number_format($monthData->count) }}</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: {{ $barHeight }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                        </div>
                        <p class="text-slate-600 font-medium">لا توجد بيانات شهرية متاحة</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <!-- الإجراءات السريعة -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-bolt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">إجراءات سريعة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">تنظيم وإدارة صلاحيات المستخدمين بكفاءة</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg">
                <i class="fas fa-tools"></i>
                Quick Actions
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <a href="{{ route('admin.roles.index') }}" 
               class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-2">إدارة الأدوار</h4>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">تعريف الصلاحيات وتوزيعها حسب الفريق</p>
            </a>
            <a href="{{ route('admin.permissions.index') }}" 
               class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-key text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-2">مصفوفة الصلاحيات</h4>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">إدارة الصلاحيات الدقيقة لكل مستخدم</p>
            </a>
            <a href="{{ route('admin.users.create') }}" 
               class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-2">إضافة حساب جديد</h4>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">إنشاء حسابات للمدرسين أو الطلاب الجدد</p>
            </a>
            <a href="{{ route('admin.activity-log') }}" 
               class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                        <i class="fas fa-history text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-2">سجل النشاطات</h4>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">مراجعة تحركات الفريق على المنصة</p>
            </a>
        </div>
    </section>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-edit text-lg"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900">تعديل بيانات المستخدم</h3>
            </div>
            <button type="button" onclick="closeEditModal()" 
                    class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editUserForm" method="POST" class="space-y-5 px-6 py-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                        الاسم الكامل <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit_name" required 
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-envelope text-blue-600 text-sm"></i>
                        البريد الإلكتروني
                    </label>
                    <input type="email" name="email" id="edit_email" 
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-phone text-blue-600 text-sm"></i>
                        رقم الهاتف <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" id="edit_phone" required 
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-lock text-blue-600 text-sm"></i>
                        كلمة المرور الجديدة (اختياري)
                    </label>
                    <input type="password" name="password" id="edit_password" 
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-tag text-blue-600 text-sm"></i>
                        الدور <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="edit_role" required 
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="super_admin">مدير عام</option>
                        <option value="admin">إداري</option>
                        <option value="instructor">مدرب</option>
                        <option value="teacher">مدرس</option>
                        <option value="student">طالب</option>
                        <option value="parent">ولي أمر</option>
                        <option value="employee">موظف</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600 text-sm"></i>
                        حالة الحساب <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" id="edit_is_active" required 
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeEditModal()" 
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </button>
                <button type="submit" 
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const editModal = document.getElementById('editUserModal');
    const editForm = document.getElementById('editUserForm');
    let scrollPosition = 0;

    function openModal() {
        // حفظ موضع التمرير الحالي
        scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        
        // منع التمرير
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollPosition}px`;
        document.body.style.width = '100%';
        
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        editForm.reset();
        
        // استعادة التمرير
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        
        // استعادة موضع التمرير
        window.scrollTo(0, scrollPosition);
    }

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !editModal.classList.contains('hidden')) {
            closeEditModal();
        }
    });

    // Close modal when clicking outside
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    function editUser(userId) {
        fetch(`/admin/users/${userId}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(user => {
                document.getElementById('edit_name').value = user.name || '';
                document.getElementById('edit_email').value = user.email || '';
                document.getElementById('edit_phone').value = user.phone || '';
                // استخدام role من الـ response أو is_employee
                document.getElementById('edit_role').value = user.is_employee ? 'employee' : (user.role || 'student');
                document.getElementById('edit_is_active').value = user.is_active ? '1' : '0';

                editForm.action = `/admin/users/${userId}`;
                openModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحميل بيانات المستخدم');
            });
    }

    function deleteUser(userId) {
        if (confirm('هل أنت متأكد من حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('خطأ: لم يتم العثور على CSRF token');
                return;
            }

            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                // قراءة الـ response مرة واحدة فقط
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    data = { message: text || 'حدث خطأ غير معروف' };
                }
                
                return {
                    ok: response.ok,
                    status: response.status,
                    data: data
                };
            })
            .then(result => {
                if (result.ok && result.data.success) {
                    alert('تم حذف المستخدم بنجاح');
                    window.location.reload();
                } else {
                    const errorMsg = result.data.message || result.data.error || 'حدث خطأ أثناء حذف المستخدم';
                    alert('خطأ: ' + errorMsg);
                    console.error('Delete error:', result);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء حذف المستخدم: ' + error.message);
            });
        }
    }

    // معالج submit للنموذج
    editForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(editForm);
        const userId = editForm.action.split('/').pop();

        fetch(editForm.action, {
            method: 'PUT',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'حدث خطأ أثناء تحديث المستخدم');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('تم تحديث بيانات المستخدم بنجاح');
                closeEditModal();
                window.location.reload();
            } else {
                alert('حدث خطأ أثناء تحديث المستخدم: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'حدث خطأ أثناء تحديث المستخدم');
        });
    });

</script>
@endpush
@endsection
