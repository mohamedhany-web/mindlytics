@extends('layouts.admin')

@section('title', 'إدارة الكورسات - ' . $academicYear->name)
@section('header', 'إدارة الكورسات - ' . $academicYear->name)

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-sky-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('admin.learning-paths.courses.index') }}" class="text-sky-600 hover:text-sky-700">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <h2 class="text-2xl font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                            <i class="fas fa-graduation-cap text-sky-600 ml-2"></i>
                            {{ $academicYear->name }}
                        </h2>
                    </div>
                    <p class="text-sm text-gray-600">
                        إدارة الكورسات المرتبطة بهذا المسار التعليمي
                    </p>
                </div>
                <button type="button" onclick="showAddCourseModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-plus"></i>
                    إضافة كورس
                </button>
            </div>
        </div>
    </div>

    <!-- الكورسات المرتبطة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                الكورسات المرتبطة بالمسار ({{ $academicYear->linkedCourses->count() }})
            </h3>
        </div>
        <div class="p-6">
            @if($academicYear->linkedCourses->count() > 0)
                <div class="space-y-3" id="coursesList">
                    @foreach($academicYear->linkedCourses as $course)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-sky-300 transition-colors" data-course-id="{{ $course->id }}">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $course->title }}</h4>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                    @if($course->instructor)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                            <i class="fas fa-user-tie"></i>
                                            {{ $course->instructor->name }}
                                        </span>
                                    @endif
                                    @if($course->programming_language)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                                            <i class="fas fa-code"></i>
                                            {{ $course->programming_language }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                        <i class="fas fa-clock"></i>
                                        {{ $course->duration_hours ?? 0 }} ساعة
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $course->price > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        <i class="fas fa-money-bill"></i>
                                        {{ $course->price > 0 ? number_format($course->price) . ' ج.م' : 'مجاني' }}
                                    </span>
                                    @if($course->pivot->is_required)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700">
                                            <i class="fas fa-exclamation-circle"></i>
                                            إجباري
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">ترتيب: {{ $course->pivot->order ?? 0 }}</span>
                                <form method="POST" action="{{ route('admin.learning-paths.courses.destroy', ['academicYear' => $academicYear->id, 'course' => $course->id]) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الكورس من المسار؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-300">
                                        <i class="fas fa-times"></i>
                                        إزالة
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-graduation-cap text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold mb-2">لا توجد كورسات مرتبطة بهذا المسار</p>
                    <p class="text-sm mb-4">استخدم زر "إضافة كورس" لإضافة كورسات للمسار</p>
                    <button type="button" onclick="showAddCourseModal()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold shadow-lg transition-all">
                        <i class="fas fa-plus"></i>
                        إضافة كورس
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal إضافة كورس -->
<div id="addCourseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto relative z-[10000]">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">إضافة كورس للمسار</h3>
                <button onclick="hideAddCourseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.learning-paths.courses.store', $academicYear) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر الكورس</label>
                <select name="course_id" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                    <option value="">اختر الكورس</option>
                    @foreach($availableCourses as $course)
                        @if(!$academicYear->linkedCourses->contains($course->id))
                            <option value="{{ $course->id }}">
                                {{ $course->title }} 
                                @if($course->instructor)
                                    - {{ $course->instructor->name }}
                                @endif
                                @if($course->price > 0)
                                    ({{ number_format($course->price) }} ج.م)
                                @else
                                    (مجاني)
                                @endif
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ترتيب الكورس في المسار</label>
                <input type="number" name="order" value="0" min="0" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_required" value="1" checked id="is_required" class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                <label for="is_required" class="text-sm text-gray-700">الكورس إجباري</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="hideAddCourseModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold shadow-lg transition-all">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddCourseModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideAddCourseModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddCourseModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAddCourseModal();
        }
    });
});
</script>
@endsection
