@extends('layouts.app')

@section('title', 'إضافة درس جديد')
@section('header', 'إضافة درس جديد')

@push('styles')
<style>
    .form-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('instructor.courses.index') }}" class="text-[#2CA9BD] hover:underline">الكورسات</a>
        <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
        <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-[#2CA9BD] hover:underline">{{ $course->title }}</a>
        <i class="fas fa-chevron-left text-gray-400 text-xs"></i>
        <span class="text-gray-600">إضافة درس</span>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle mt-0.5 ml-2"></i>
            <div>
                <strong class="font-bold">خطأ!</strong>
                <ul class="mt-2 mr-4 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="form-card p-6">
        <form action="{{ route('instructor.courses.lessons.store', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if(request('section_id'))
            <input type="hidden" name="section_id" value="{{ request('section_id') }}">
            @endif

            <div class="space-y-6">
                <!-- العنوان ونوع الدرس -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- العنوان -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-heading text-[#2CA9BD] ml-2"></i>
                            عنوان الدرس <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD] focus:border-transparent" 
                               value="{{ old('title') }}" 
                               placeholder="مثال: مقدمة في البرمجة" 
                               required>
                    </div>

                    <!-- نوع الدرس -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-list text-[#2CA9BD] ml-2"></i>
                            نوع الدرس <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="lessonType" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD] focus:border-transparent" 
                                required>
                            <option value="">اختر نوع الدرس</option>
                            <option value="video">فيديو</option>
                            <option value="text">نص</option>
                            <option value="document">ملف</option>
                            <option value="quiz">اختبار</option>
                        </select>
                    </div>
                </div>

                <!-- الوصف -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-align-right text-[#2CA9BD] ml-2"></i>
                        وصف الدرس
                    </label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD] focus:border-transparent" 
                              placeholder="وصف مختصر للدرس">{{ old('description') }}</textarea>
                </div>

                <!-- Video Section -->
                <div class="video-section hidden">
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h6 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-video text-red-500"></i>
                            <span>إعدادات الفيديو</span>
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">رابط الفيديو (يوتيوب/فيميو)</label>
                                <input type="url" name="video_url" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]" 
                                       placeholder="https://youtube.com/watch?v=...">
                                <p class="text-xs text-gray-500 mt-1">أو يمكنك رفع ملف فيديو</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">رفع ملف فيديو (حتى 500MB)</label>
                                <input type="file" name="video_file" accept="video/*"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]">
                                <p class="text-xs text-gray-500 mt-1">MP4, WebM, OGG</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Text Content Section -->
                <div class="text-section hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-paragraph text-[#2CA9BD] ml-2"></i>
                        محتوى الدرس
                    </label>
                    <textarea name="content" rows="10" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]" 
                              placeholder="اكتب محتوى الدرس هنا...">{{ old('content') }}</textarea>
                </div>

                <!-- المدة والترتيب -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-clock text-[#2CA9BD] ml-2"></i>
                            مدة الدرس (بالدقائق)
                        </label>
                        <input type="number" name="duration_minutes" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]" 
                               value="{{ old('duration_minutes') }}" 
                               min="0" 
                               placeholder="45">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up text-[#2CA9BD] ml-2"></i>
                            ترتيب الدرس <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="order" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]" 
                               value="{{ old('order', $lastOrder + 1) }}" 
                               min="0" 
                               required>
                    </div>
                </div>

                <!-- المرفقات -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-paperclip text-[#2CA9BD] ml-2"></i>
                        المرفقات (اختياري)
                    </label>
                    <input type="file" name="attachments[]" multiple
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2CA9BD]">
                    <p class="text-xs text-gray-500 mt-1">يمكنك رفع عدة ملفات (حتى 10MB لكل ملف)</p>
                </div>

                <!-- الخيارات -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h6 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cog text-[#2CA9BD]"></i>
                        <span>خيارات الدرس</span>
                    </h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" checked
                                   class="w-5 h-5 text-[#2CA9BD] border-gray-300 rounded focus:ring-[#2CA9BD]">
                            <span class="text-sm font-semibold text-gray-700">
                                <i class="fas fa-eye text-green-500 ml-2"></i>
                                الدرس نشط
                            </span>
                        </label>
                        
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_free"
                                   class="w-5 h-5 text-[#2CA9BD] border-gray-300 rounded focus:ring-[#2CA9BD]">
                            <span class="text-sm font-semibold text-gray-700">
                                <i class="fas fa-gift text-yellow-500 ml-2"></i>
                                درس مجاني (معاينة)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('instructor.courses.curriculum', $course->id) }}" 
                   class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-bold transition-all">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white rounded-lg font-bold transition-all shadow-lg">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الدرس
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lessonType = document.getElementById('lessonType');
    const videoSection = document.querySelector('.video-section');
    const textSection = document.querySelector('.text-section');
    
    function updateSections() {
        const type = lessonType.value;
        
        videoSection.classList.add('hidden');
        textSection.classList.add('hidden');
        
        if (type === 'video') {
            videoSection.classList.remove('hidden');
        } else if (type === 'text') {
            textSection.classList.remove('hidden');
        }
    }
    
    lessonType.addEventListener('change', updateSections);
    updateSections();
});
</script>
@endpush
@endsection
