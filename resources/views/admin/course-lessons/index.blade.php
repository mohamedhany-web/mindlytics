@extends('layouts.admin')

@section('title', 'دروس الكورس البرمجي')
@section('header', 'دروس الكورس: ' . $course->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.show', $course) }}" class="hover:text-primary-600">{{ $course->title }}</a>
                <span class="mx-2">/</span>
                <span>الدروس</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة درس جديد
            </a>
            <a href="{{ route('admin.advanced-courses.show', $course) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $course->academicYear->name ?? 'غير محدد' }} - {{ $course->academicSubject->name ?? 'غير محدد' }}
                    <span class="text-sky-600 font-medium">| كورس برمجي</span>
                </p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600">{{ $lessons->count() }}</div>
                <div class="text-sm text-gray-500">درس</div>
            </div>
        </div>
    </div>

    <!-- قائمة الدروس -->
    @if($lessons->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900">قائمة الدروس</h4>
            </div>
            
            <div class="divide-y divide-gray-200" id="lessons-container">
                @foreach($lessons as $lesson)
                    <div class="p-6 hover:bg-gray-50 transition-colors" data-lesson-id="{{ $lesson->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- أيقونة الترتيب -->
                                <div class="cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- أيقونة نوع الدرس -->
                                <div class="flex-shrink-0">
                                    @php
                                        $iconBg = $lesson->type == 'video' ? 'bg-blue-100' : ($lesson->type == 'document' ? 'bg-green-100' : ($lesson->type == 'quiz' ? 'bg-yellow-100' : 'bg-purple-100'));
                                        $iconFa = $lesson->type == 'video' ? 'fa-play text-blue-600' : ($lesson->type == 'document' ? 'fa-file-alt text-green-600' : ($lesson->type == 'quiz' ? 'fa-question-circle text-yellow-600' : 'fa-tasks text-purple-600'));
                                    @endphp
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $iconBg }}">
                                        <i class="fas {{ $iconFa }}"></i>
                                    </div>
                                </div>

                                <!-- معلومات الدرس -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3">
                                        <h5 class="text-lg font-medium text-gray-900">{{ $lesson->title }}</h5>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $lesson->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                        @if($lesson->is_free)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                مجاني
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($lesson->description)
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($lesson->description, 100) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center gap-6 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-clock ml-1"></i>
                                            {{ $lesson->duration_minutes ?? 0 }} دقيقة
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-sort-numeric-up ml-1"></i>
                                            ترتيب: {{ $lesson->order }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-tag ml-1"></i>
                                            @if($lesson->type == 'video') فيديو
                                            @elseif($lesson->type == 'document') مستند
                                            @elseif($lesson->type == 'quiz') كويز
                                            @else واجب
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button onclick="toggleLessonStatus({{ $lesson->id }})" 
                                        class="p-2 {{ $lesson->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} transition-colors"
                                        title="{{ $lesson->is_active ? 'إيقاف' : 'تفعيل' }}">
                                    <i class="fas {{ $lesson->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                </button>
                                <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
                                   class="p-2 text-blue-600 hover:text-blue-800 transition-colors" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
                                   class="p-2 text-indigo-600 hover:text-indigo-800 transition-colors" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" 
                                      class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:text-red-800 transition-colors" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-play-circle text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد دروس</h3>
            <p class="text-gray-500 mb-6">ابدأ بإضافة الدروس لهذا الكورس لتنظيم المحتوى التعليمي</p>
            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة أول درس
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// إعداد السحب والإفلات لإعادة الترتيب
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('lessons-container');
    if (container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'bg-blue-50',
            chosenClass: 'bg-blue-100',
            onEnd: function(evt) {
                const lessons = [];
                container.querySelectorAll('[data-lesson-id]').forEach((element, index) => {
                    lessons.push({
                        id: element.dataset.lessonId,
                        order: index + 1
                    });
                });
                
                // إرسال الترتيب الجديد للخادم
                fetch(`{{ route('admin.courses.lessons.reorder', $course) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ lessons: lessons })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // يمكن إضافة إشعار نجاح هنا
                        console.log('تم حفظ الترتيب الجديد');
                    } else {
                        alert('حدث خطأ في حفظ الترتيب');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ في حفظ الترتيب');
                    location.reload();
                });
            }
        });
    }
});

function toggleLessonStatus(lessonId) {
    if (confirm('هل تريد تغيير حالة هذا الدرس؟')) {
        fetch(`{{ route('admin.courses.lessons.index', $course) }}/${lessonId}/toggle-status`, {
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
                alert('حدث خطأ في تغيير حالة الدرس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الدرس');
        });
    }
}
</script>
@endpush
@endsection
