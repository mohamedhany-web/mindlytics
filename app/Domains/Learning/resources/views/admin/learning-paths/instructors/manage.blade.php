@extends('layouts.admin')

@section('title', 'إدارة المدربين - ' . $academicYear->name)
@section('header', 'إدارة المدربين - ' . $academicYear->name)

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-sky-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('admin.learning-paths.instructors.index') }}" class="text-sky-600 hover:text-sky-700">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <h2 class="text-2xl font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                            <i class="fas fa-user-tie text-sky-600 ml-2"></i>
                            {{ $academicYear->name }}
                        </h2>
                    </div>
                    <p class="text-sm text-gray-600">
                        توصيف المدربين المسؤولين عن هذا المسار والكورسات المخصصة لكل مدرب
                    </p>
                </div>
                <button type="button" onclick="showAddInstructorModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 hover:from-purple-700 hover:via-pink-700 hover:to-purple-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-purple-600/30 hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-plus"></i>
                    إضافة مدرب
                </button>
            </div>
        </div>
    </div>

    <!-- المدربين المرتبطين -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
            <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                المدربين المرتبطين بالمسار ({{ $academicYear->instructors->count() }})
            </h3>
        </div>
        <div class="p-6">
            @if($academicYear->instructors->count() > 0)
                <div class="space-y-4">
                    @foreach($academicYear->instructors as $instructor)
                        @php
                            $assignedCourses = json_decode($instructor->pivot->assigned_courses ?? '[]', true);
                        @endphp
                        <div class="p-5 bg-gradient-to-br from-white to-gray-50 rounded-xl border-2 border-gray-200 hover:border-purple-300 transition-all duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            {{ substr($instructor->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $instructor->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $instructor->email }}</p>
                                        </div>
                                    </div>
                                    @if($instructor->pivot->notes)
                                        <p class="text-sm text-gray-600 mb-3 bg-gray-50 p-3 rounded-lg">
                                            <i class="fas fa-sticky-note text-gray-400 ml-2"></i>
                                            {{ $instructor->pivot->notes }}
                                        </p>
                                    @endif
                                    @if(!empty($assignedCourses) && count($assignedCourses) > 0)
                                        <div class="mb-3">
                                            <p class="text-xs font-semibold text-gray-500 mb-2">الكورسات المخصصة:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($assignedCourses as $courseId)
                                                    @php
                                                        $course = $academicYear->linkedCourses->firstWhere('id', $courseId);
                                                    @endphp
                                                    @if($course)
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">
                                                            <i class="fas fa-graduation-cap"></i>
                                                            {{ $course->title }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 mb-3">
                                            <i class="fas fa-info-circle ml-1"></i>
                                            جميع الكورسات في المسار
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="showEditInstructorModal({{ $instructor->id }}, {{ json_encode($assignedCourses) }}, '{{ addslashes($instructor->pivot->notes ?? '') }}')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all duration-300">
                                        <i class="fas fa-edit"></i>
                                        تعديل
                                    </button>
                                    <form method="POST" action="{{ route('admin.learning-paths.instructors.destroy', ['academicYear' => $academicYear->id, 'instructor' => $instructor->id]) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا المدرب من المسار؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-300">
                                            <i class="fas fa-times"></i>
                                            إزالة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-user-tie text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold mb-2">لا يوجد مدربين مرتبطين بهذا المسار</p>
                    <p class="text-sm mb-4">استخدم زر "إضافة مدرب" لإضافة مدربين للمسار</p>
                    <button type="button" onclick="showAddInstructorModal()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold shadow-lg transition-all">
                        <i class="fas fa-plus"></i>
                        إضافة مدرب
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal إضافة مدرب -->
<div id="addInstructorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto relative z-[10000]">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">إضافة مدرب للمسار</h3>
                <button onclick="hideAddInstructorModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.learning-paths.instructors.store', $academicYear) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر المدرب</label>
                <select name="instructor_id" required id="instructorSelect" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                    <option value="">اختر المدرب</option>
                    @foreach($availableInstructors as $instructor)
                        <option value="{{ $instructor->id }}">{{ $instructor->name }} - {{ $instructor->email }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">الكورسات المخصصة (اختياري)</label>
                <p class="text-xs text-gray-500 mb-2">اتركه فارغاً لتعيين جميع الكورسات في المسار</p>
                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3">
                    @foreach($academicYear->linkedCourses as $course)
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input type="checkbox" name="assigned_courses[]" value="{{ $course->id }}" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <span class="text-sm text-gray-700">{{ $course->title }}</span>
                        </label>
                    @endforeach
                    @if($academicYear->linkedCourses->count() == 0)
                        <p class="text-sm text-gray-500 text-center py-4">أضف كورسات للمسار أولاً</p>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20" placeholder="ملاحظات حول هذا المدرب في المسار..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="hideAddInstructorModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold shadow-lg transition-all">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal تعديل مدرب -->
<div id="editInstructorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto relative z-[10000]">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">تعديل المدرب</h3>
                <button onclick="hideEditInstructorModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" id="editInstructorForm" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">الكورسات المخصصة (اختياري)</label>
                <p class="text-xs text-gray-500 mb-2">اتركه فارغاً لتعيين جميع الكورسات في المسار</p>
                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3" id="editCoursesList">
                    @foreach($academicYear->linkedCourses as $course)
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input type="checkbox" name="assigned_courses[]" value="{{ $course->id }}" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 edit-course-checkbox">
                            <span class="text-sm text-gray-700">{{ $course->title }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" id="editNotes" rows="3" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20" placeholder="ملاحظات حول هذا المدرب في المسار..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="hideEditInstructorModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold shadow-lg transition-all">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddInstructorModal() {
    const modal = document.getElementById('addInstructorModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideAddInstructorModal() {
    const modal = document.getElementById('addInstructorModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function showEditInstructorModal(instructorId, assignedCourses, notes) {
    const modal = document.getElementById('editInstructorModal');
    const form = document.getElementById('editInstructorForm');
    const editNotes = document.getElementById('editNotes');
    
    // تحديث رابط الـ form
    form.action = '{{ route("admin.learning-paths.instructors.update-courses", ["academicYear" => $academicYear->id, "instructor" => ":instructorId"]) }}'.replace(':instructorId', instructorId);
    
    // تحديث الملاحظات
    if (editNotes) {
        editNotes.value = notes || '';
    }
    
    // تحديث الكورسات المحددة
    const checkboxes = document.querySelectorAll('.edit-course-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = assignedCourses && assignedCourses.includes(parseInt(checkbox.value));
    });
    
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideEditInstructorModal() {
    const modal = document.getElementById('editInstructorModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addInstructorModal');
    const editModal = document.getElementById('editInstructorModal');
    
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddInstructorModal();
            }
        });
    }
    
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideEditInstructorModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAddInstructorModal();
            hideEditInstructorModal();
        }
    });
});
</script>
@endsection
