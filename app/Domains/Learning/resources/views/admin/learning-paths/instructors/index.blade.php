@extends('layouts.admin')

@section('title', 'إدارة المدربين في المسارات التعليمية')
@section('header', 'إدارة المدربين في المسارات التعليمية')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-sky-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                        <i class="fas fa-user-tie text-sky-600 ml-2"></i>
                        إدارة المدربين في المسارات التعليمية
                    </h2>
                    <p class="text-sm text-gray-600 mt-2">
                        قم بتوصيف المدربين المسؤولين عن كل مسار تعليمي والكورسات المخصصة لكل مدرب
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <form method="GET" class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو الكود..."
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
                <a href="{{ route('admin.learning-paths.instructors.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300">
                    <i class="fas fa-redo"></i>
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- قائمة المسارات -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                قائمة المسارات التعليمية
            </h3>
        </div>
        <div class="p-6">
            @if($learningPaths->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($learningPaths as $path)
                        <div class="p-5 bg-gradient-to-br from-white to-gray-50 rounded-xl border-2 border-gray-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $path->name }}</h4>
                                    <p class="text-xs text-gray-500 mb-2">{{ $path->code }}</p>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $path->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            {{ $path->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                                            <i class="fas fa-user-tie"></i>
                                            {{ $path->instructors_count ?? 0 }} مدرب
                                        </span>
                                    </div>
                                </div>
                                @if($path->icon)
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl" style="background: {{ $path->color ?? '#3b82f6' }}20; color: {{ $path->color ?? '#3b82f6' }};">
                                        <i class="{{ $path->icon }}"></i>
                                    </div>
                                @endif
                            </div>
                            @if($path->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $path->description }}</p>
                            @endif
                            <a href="{{ route('admin.learning-paths.instructors.manage', $path) }}" 
                               class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 hover:from-purple-700 hover:via-pink-700 hover:to-purple-700 text-white font-semibold shadow-lg shadow-purple-600/30 hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-user-tie"></i>
                                إدارة المدربين
                            </a>
                        </div>
                    @endforeach
                </div>
                @if($learningPaths->hasPages())
                    <div class="mt-6">
                        {{ $learningPaths->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-route text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold mb-2">لا توجد مسارات تعليمية</p>
                    <p class="text-sm">قم بإنشاء مسار تعليمي أولاً</p>
                    <a href="{{ route('admin.academic-years.create') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold shadow-lg transition-all">
                        <i class="fas fa-plus"></i>
                        إنشاء مسار جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
