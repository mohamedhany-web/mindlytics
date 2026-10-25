@extends('layouts.admin')

@section('title', 'تسجيل طالب جديد - الأوفلاين')
@section('header', 'تسجيل طالب جديد - الأوفلاين')

@section('content')
<div class="space-y-6">
    <!-- معلومات التسجيل -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">تسجيل طالب في كورس أوفلاين</h3>
                <a href="{{ route('admin.offline-enrollments.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.offline-enrollments.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الطالب -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار الطالب <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر الطالب</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" 
                                    {{ old('user_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} - {{ $student->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الكورس -->
                <div>
                    <label for="offline_course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار الكورس <span class="text-red-500">*</span>
                    </label>
                    <select name="offline_course_id" id="offline_course_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('offline_course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }} - {{ $course->instructor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('offline_course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المجموعة -->
                <div>
                    <label for="group_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار المجموعة (اختياري)
                    </label>
                    <select name="group_id" id="group_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">بدون مجموعة</option>
                        <!-- سيتم ملء المجموعات عبر JavaScript بناءً على الكورس المختار -->
                    </select>
                    @error('group_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الحالة -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        حالة التسجيل <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
                <a href="{{ route('admin.offline-enrollments.index') }}" 
                   class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-times mr-2"></i>إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>حفظ التسجيل
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('offline_course_id').addEventListener('change', function() {
    const courseId = this.value;
    const groupSelect = document.getElementById('group_id');
    
    // مسح الخيارات السابقة
    groupSelect.innerHTML = '<option value="">بدون مجموعة</option>';
    
    if (courseId) {
        // جلب المجموعات للكورس المختار
        fetch(`/admin/offline-courses/${courseId}/groups`)
            .then(response => response.json())
            .then(data => {
                if (data.groups && data.groups.length > 0) {
                    data.groups.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.textContent = group.name;
                        groupSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching groups:', error);
            });
    }
});
</script>
@endsection
