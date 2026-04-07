@extends('layouts.admin')

@section('title', 'كورسات الأونلاين')
@section('header', 'إدارة الأونلاين — كورسات الأونلاين')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <!-- الهيدر والفلاتر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">كورسات الأونلاين</h1>
                <p class="text-gray-600 mt-1">الكورسات المعتمدة كأونلاين فقط، أو التي تتضمن مجموعة مفعّل لها الحجز الأونلاين</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.online-management.courses.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    كورس أونلاين فقط
                </a>
                <a href="{{ route('admin.online-management.enroll') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-user-check"></i>
                    تسجيل طالب
                </a>
                <a href="{{ route('admin.online-course-bookings.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors border border-gray-200 inline-flex items-center gap-2">
                    <i class="fas fa-inbox"></i>
                    حجوزات الأونلاين
                </a>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" action="{{ route('admin.online-management.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="عنوان الكورس أو اسم المدرب…"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                    <select name="instructor_id" id="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع المدربين</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        بحث
                    </button>
                    @if(request()->hasAny(['search', 'status', 'instructor_id']))
                        <a href="{{ route('admin.online-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الظاهر في الأونلاين</p>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-laptop-house text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-green-100/60 via-emerald-100/40 to-green-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                        <p class="text-3xl font-black text-green-700">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-100/60 via-amber-100/40 to-yellow-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مسودات</p>
                        <p class="text-3xl font-black text-yellow-700">{{ $stats['draft'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-violet-100/40 to-purple-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">أونلاين فقط</p>
                        <p class="text-3xl font-black text-purple-800">{{ $stats['online_only'] }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-cloud text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الكورسات -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                @php
                    $onlineGroups = $course->groups->filter(fn ($g) => $g->online_booking_enabled && $g->is_active && $g->status === 'active');
                @endphp
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200 flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100/50">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight">{{ $course->title }}</h3>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                @if($course->online_only)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">أونلاين فقط</span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($course->status === 'active') bg-green-100 text-green-800
                                    @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($course->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($course->status === 'active') نشط
                                    @elseif($course->status === 'draft') مسودة
                                    @elseif($course->status === 'completed') مكتمل
                                    @else ملغي
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 flex-1 flex flex-col">
                        @if($course->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-user-tie text-gray-400 w-4 ml-2"></i>
                                <span class="text-gray-600">المدرب:</span>
                                <span class="text-gray-900 mr-2 font-medium">{{ $course->instructor?->name ?? '—' }}</span>
                            </div>
                            @if($course->price !== null)
                                <div class="flex items-center">
                                    <i class="fas fa-coins text-gray-400 w-4 ml-2"></i>
                                    <span class="text-gray-600">السعر:</span>
                                    <span class="text-gray-900 mr-2 font-medium">{{ number_format((float) $course->price, 2) }} ج.م</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-2 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">المجموعات — الأونلاين</p>
                            @if($onlineGroups->isEmpty())
                                <p class="text-sm text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                                    لا مجموعة مفعّل لها الأونلاين. افتح إدارة الكورس وفعّل الحجز الأونلاين للمجموعة.
                                </p>
                            @else
                                <ul class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($onlineGroups as $g)
                                        <li class="flex flex-wrap items-center justify-between gap-2 text-sm bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $g->name }}</span>
                                                <span class="text-gray-500 text-xs mr-2">مقاعد: {{ $g->current_students_online }}/{{ $g->max_students_online }}</span>
                                            </div>
                                            <a href="{{ route('admin.online-management.enroll', ['offline_course_id' => $course->id, 'group_id' => $g->id]) }}"
                                               class="text-blue-600 hover:text-blue-800 font-medium text-xs whitespace-nowrap">
                                                <i class="fas fa-user-plus ml-1"></i>تسجيل
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2 flex-wrap">
                        <a href="{{ route('admin.offline-courses.show', $course) }}"
                           class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                            <i class="fas fa-cog mr-1"></i>إدارة الكورس
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-laptop-house text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد كورسات تطابق البحث</p>
            <p class="text-sm text-gray-600 mb-6">أنشئ كورس أونلاين فقط، أو فعّل مجموعة للأونلاين من الكورسات الأوفلاين</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('admin.online-management.courses.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus"></i>
                    كورس أونلاين فقط
                </a>
                <a href="{{ route('admin.offline-courses.index') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    الكورسات الأوفلاين
                </a>
            </div>
        </div>
    @endif

    @if($courses instanceof \Illuminate\Pagination\LengthAwarePaginator && $courses->hasPages())
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
            {{ $courses->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
