@extends('layouts.admin')

@section('title', 'إدارة تسجيلات المسارات التعليمية')
@section('header', 'إدارة تسجيلات المسارات التعليمية')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6">
        <!-- إجمالي التسجيلات -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-blue-800/80 mb-1">إجمالي التسجيلات</p>
                        <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['total'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600">جميع تسجيلات المسارات</p>
            </div>
        </div>

        <!-- في الانتظار -->
        <a href="{{ route('admin.learning-path-enrollments.index', ['status' => 'pending']) }}" class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 block" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-yellow-800/80 mb-1">في الانتظار</p>
                        <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-yellow-700 via-amber-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['pending'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-yellow-600">بحاجة للتفعيل</p>
                @if(($stats['pending'] ?? 0) > 0)
                    <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-yellow-200 rounded-full text-xs font-bold text-yellow-800">
                        <i class="fas fa-exclamation-circle"></i>
                        انقر للمراجعة
                    </div>
                @endif
            </div>
        </a>

        <!-- نشط -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">نشط</p>
                        <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['active'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-emerald-600">مفعل ويتعلم</p>
            </div>
        </div>

        <!-- مكتمل -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-purple-800/80 mb-1">مكتمل</p>
                        <p class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-purple-700 via-pink-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm">{{ number_format($stats['completed'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-pink-500 to-fuchsia-600 rounded-2xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(168, 85, 247, 0.4);">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-600">أنهى المسار</p>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                    <i class="fas fa-filter text-sky-600 ml-2"></i>
                    البحث والفلترة
                </h3>
                <a href="{{ route('admin.learning-path-enrollments.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fas fa-plus"></i>
                    تسجيل طالب جديد
                </a>
            </div>
        </div>
        
        <form method="GET" class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو رقم الهاتف..."
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
                </select>
            </div>

            <div>
                <label for="learning_path_id" class="block text-sm font-semibold text-gray-700 mb-2">المسار التعليمي</label>
                <select name="learning_path_id" id="learning_path_id" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-white/70 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                    <option value="">جميع المسارات</option>
                    @foreach($learningPaths as $path)
                        <option value="{{ $path->id }}" {{ request('learning_path_id') == $path->id ? 'selected' : '' }}>
                            {{ $path->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
                <a href="{{ route('admin.learning-path-enrollments.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300">
                    <i class="fas fa-redo"></i>
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- جدول التسجيلات -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                قائمة التسجيلات
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">المسار التعليمي</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">تاريخ التسجيل</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">التقدم</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($enrollment->student->name ?? 'ط', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $enrollment->student->name ?? 'غير محدد' }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->student->phone ?? 'لا يوجد' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $enrollment->learningPath->name ?? 'غير محدد' }}</p>
                                <p class="text-xs text-gray-500">{{ $enrollment->learningPath->code ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $enrollment->enrolled_at?->format('Y/m/d') ?? 'غير محدد' }}</p>
                                <p class="text-xs text-gray-500">{{ $enrollment->enrolled_at?->diffForHumans() ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-sky-500 to-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ number_format($enrollment->progress ?? 0, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'completed' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'suspended' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'في الانتظار',
                                        'active' => 'نشط',
                                        'completed' => 'مكتمل',
                                        'suspended' => 'موقوف',
                                    ];
                                    $color = $statusColors[$enrollment->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    $label = $statusLabels[$enrollment->status] ?? $enrollment->status;
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $color }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.learning-path-enrollments.toggle-status', $enrollment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-300 {{ $enrollment->status === 'active' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                            <i class="fas fa-power-off"></i>
                                            {{ $enrollment->status === 'active' ? 'إيقاف' : 'تفعيل' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.learning-path-enrollments.destroy', $enrollment) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-300">
                                            <i class="fas fa-trash"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-600 font-semibold">لا توجد تسجيلات</p>
                                    <a href="{{ route('admin.learning-path-enrollments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        إضافة تسجيل جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
