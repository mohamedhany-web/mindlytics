@extends('layouts.admin')

@section('title', 'إدارة الكورسات البرمجية')
@section('header', 'إدارة الكورسات')

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    @if(isset($selectedCluster) && $selectedCluster)
                        <a href="{{ route('admin.academic-subjects.index') }}" class="hover:text-white">مجموعات المهارات</a>
                        <span class="mx-2">/</span>
                        <span class="text-white truncate">{{ Str::limit($selectedCluster->name, 30) }}</span>
                        <span class="mx-2">/</span>
                    @endif
                    <span class="text-white">الكورسات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إدارة الكورسات البرمجية</h1>
                <p class="text-sm text-white/90 mt-1">
                    @if(isset($selectedCluster) && $selectedCluster)
                        كورسات مجموعة «{{ $selectedCluster->name }}»
                    @else
                        إدارة وتنظيم كورسات البرمجة في الأكاديمية
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @if(isset($selectedCluster) && $selectedCluster)
                    <a href="{{ route('admin.academic-subjects.index', $selectedCluster->academic_year_id ? ['track' => $selectedCluster->academic_year_id] : []) }}" 
                       class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                        <i class="fas fa-arrow-right"></i>
                        العودة للمجموعات
                    </a>
                @endif
                <a href="{{ route('admin.advanced-courses.create', isset($selectedCluster) && $selectedCluster ? ['cluster' => $selectedCluster->id] : []) }}" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة كورس جديد
                </a>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @if(request('cluster'))
                <input type="hidden" name="cluster" value="{{ request('cluster') }}">
            @endif
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث في عناوين الكورسات..."
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">السنة الدراسية</label>
                <select name="academic_year_id" id="academic_year_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع السنوات</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                </select>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-200 flex flex-col">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-lg font-bold text-gray-900 truncate flex-1 min-w-0">{{ $course->title }}</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0 {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $course->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="px-6 py-4 flex-1">
                    @if($course->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($course->description, 120) }}</p>
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar text-gray-400 w-5 ml-2 flex-shrink-0"></i>
                            <span class="mr-1">السنة:</span>
                            <span class="text-gray-900 font-medium">{{ $course->academicYear->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-book text-gray-400 w-5 ml-2 flex-shrink-0"></i>
                            <span class="mr-1">المادة:</span>
                            <span class="text-gray-900 font-medium">{{ $course->academicSubject->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            @if($course->price)
                                <i class="fas fa-money-bill text-gray-400 w-5 ml-2 flex-shrink-0"></i>
                                <span class="mr-1">السعر:</span>
                                <span class="text-gray-900 font-medium">{{ number_format($course->price) }} ج.م</span>
                            @else
                                <i class="fas fa-gift text-green-500 w-5 ml-2 flex-shrink-0"></i>
                                <span class="text-green-600 font-semibold">مجاني</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock text-gray-400 w-5 ml-2 flex-shrink-0"></i>
                            <span class="mr-1">{{ $course->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات سريعة -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 bg-white rounded-xl border border-gray-100">
                            <div class="text-lg font-bold text-gray-900">{{ $course->lessons_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">درس</div>
                        </div>
                        <div class="p-2 bg-white rounded-xl border border-gray-100">
                            <div class="text-lg font-bold text-gray-900">{{ $course->enrollments_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">طالب</div>
                        </div>
                        <div class="p-2 bg-white rounded-xl border border-gray-100">
                            <div class="text-lg font-bold text-gray-900">{{ $course->orders_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">طلب</div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 border-t border-gray-200 space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.advanced-courses.show', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('admin.courses.lessons.index', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-play-circle"></i> الدروس
                        </a>
                        <a href="{{ route('admin.courses.lessons.create', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-100 hover:bg-teal-200 text-teal-700 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-plus"></i> درس
                        </a>
                        <a href="{{ route('admin.advanced-courses.orders', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-shopping-cart"></i> الطلبات
                        </a>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                        <button type="button" onclick="toggleCourseStatus({{ $course->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 {{ $course->is_active ? 'bg-red-50 hover:bg-red-100 text-red-700 border border-red-200' : 'bg-green-50 hover:bg-green-100 text-green-700 border border-green-200' }} text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas {{ $course->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                            {{ $course->is_active ? 'إيقاف' : 'تفعيل' }}
                        </button>
                        <button type="button" onclick="toggleCourseFeatured({{ $course->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 {{ $course->is_featured ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200' : 'bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200' }} text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-star"></i>
                            {{ $course->is_featured ? 'إلغاء الترشيح' : 'ترشيح' }}
                        </button>
                        <a href="{{ route('admin.advanced-courses.edit', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <form method="POST" action="{{ route('admin.advanced-courses.destroy', $course) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- التصفح -->
        <div class="flex justify-center mt-6">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد كورسات</h3>
            <p class="text-gray-500 mb-6">لم يتم العثور على أي كورسات تطابق معايير البحث</p>
            <a href="{{ route('admin.advanced-courses.create', isset($selectedCluster) && $selectedCluster ? ['cluster' => $selectedCluster->id] : []) }}" 
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة أول كورس
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleCourseStatus(courseId) {
    if (confirm('هل تريد تغيير حالة هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الكورس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الكورس');
        });
    }
}

function toggleCourseFeatured(courseId) {
    if (confirm('هل تريد تغيير حالة ترشيح هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الترشيح');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الترشيح');
        });
    }
}
</script>
@endpush
@endsection