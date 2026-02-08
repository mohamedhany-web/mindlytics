@extends('layouts.app')

@section('title', 'إضافة محاضرة جديدة - Mindlytics')
@section('header', 'إضافة محاضرة جديدة')

@push('styles')
<style>
    .video-preview-container {
        min-height: 300px;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .video-preview-container.has-video {
        border: 2px solid #3b82f6;
        background: #000;
    }
    
    .video-preview-container iframe,
    .video-preview-container video {
        width: 100%;
        height: 100%;
        min-height: 300px;
    }
    
    .platform-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .platform-option {
        padding: 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        background: white;
    }
    
    .platform-option:hover {
        border-color: #3b82f6;
        background: #f0f9ff;
        transform: translateY(-2px);
    }
    
    .platform-option.active {
        border-color: #3b82f6;
        background: #dbeafe;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    
    .platform-option i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .video-info-card {
        background: #f0f9ff;
        border: 1px solid #bfdbfe;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f4f6;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-black text-[#1C2C39] mb-2">إضافة محاضرة جديدة</h1>
                <p class="text-[#1F3A56] font-semibold">قم بإنشاء محاضرة جديدة وحدد موعدها ومعلوماتها</p>
            </div>
            <a href="{{ route('instructor.lectures.index') }}" 
               class="bg-white hover:bg-gray-50 text-[#1F3A56] px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg border-2 border-[#2CA9BD]/20">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- النموذج -->
    <form action="{{ route('instructor.lectures.store') }}" method="POST" 
          class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden"
          x-data="videoPreviewData()">
        @csrf
        
        <div class="p-8 space-y-8">
            <!-- معلومات أساسية -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">المعلومات الأساسية</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- الكورس -->
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-graduation-cap text-[#2CA9BD] ml-1"></i>
                            الكورس <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" id="course_id" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold"
                                @if(request('course_id')) value="{{ request('course_id') }}" @endif>
                            <option value="">اختر الكورس</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                        {{ (old('course_id', request('course_id')) == $course->id) ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الدرس (اختياري) -->
                    <div>
                        <label for="course_lesson_id" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-book text-[#2CA9BD] ml-1"></i>
                            الدرس (اختياري)
                        </label>
                        <select name="course_lesson_id" id="course_lesson_id"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                            <option value="">بدون درس محدد</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('course_lesson_id') == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-600 font-medium">يمكنك ربط المحاضرة بدرس محدد من الكورس</p>
                        @error('course_lesson_id')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- العنوان -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-heading text-[#2CA9BD] ml-1"></i>
                            عنوان المحاضرة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               placeholder="مثال: مقدمة في JavaScript"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-align-right text-[#2CA9BD] ml-1"></i>
                            الوصف
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="وصف مختصر للمحاضرة..."
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- رابط تسجيل المحاضرة -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">
                    <i class="fas fa-video text-[#2CA9BD] ml-2"></i>
                    رابط تسجيل المحاضرة
                </h2>
                
                <!-- الخطوة 1: اختيار المشغل -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-4">
                        <i class="fas fa-question-circle text-[#2CA9BD] ml-1"></i>
                        ما هو مصدر الفيديو؟ <span class="text-red-500">*</span>
                    </label>
                    <div class="platform-selector">
                        <div class="platform-option" 
                             :class="{ 'active': selectedPlatform === 'youtube' }"
                             @click="selectPlatform('youtube')">
                            <i class="fab fa-youtube text-red-600"></i>
                            <div class="font-bold text-gray-900 mt-2">YouTube</div>
                        </div>
                        <div class="platform-option" 
                             :class="{ 'active': selectedPlatform === 'vimeo' }"
                             @click="selectPlatform('vimeo')">
                            <i class="fab fa-vimeo text-blue-500"></i>
                            <div class="font-bold text-gray-900 mt-2">Vimeo</div>
                        </div>
                        <div class="platform-option" 
                             :class="{ 'active': selectedPlatform === 'google_drive' }"
                             @click="selectPlatform('google_drive')">
                            <i class="fab fa-google-drive text-green-600"></i>
                            <div class="font-bold text-gray-900 mt-2">Google Drive</div>
                        </div>
                        <div class="platform-option" 
                             :class="{ 'active': selectedPlatform === 'direct' }"
                             @click="selectPlatform('direct')">
                            <i class="fas fa-file-video text-purple-600"></i>
                            <div class="font-bold text-gray-900 mt-2">رابط مباشر</div>
                        </div>
                    </div>
                    <input type="hidden" name="video_platform" x-model="selectedPlatform" required>
                </div>
                
                <!-- الخطوة 2: حقل الرابط (يظهر بعد اختيار المشغل) -->
                <div x-show="selectedPlatform" x-transition class="space-y-4">
                    <div>
                        <label for="recording_url" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-link text-[#2CA9BD] ml-1"></i>
                            رابط الفيديو <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal" x-show="selectedPlatform === 'youtube'">(مثال: https://www.youtube.com/watch?v=...)</span>
                            <span class="text-xs text-gray-500 font-normal" x-show="selectedPlatform === 'vimeo'">(مثال: https://vimeo.com/...)</span>
                            <span class="text-xs text-gray-500 font-normal" x-show="selectedPlatform === 'google_drive'">(مثال: https://drive.google.com/file/d/...)</span>
                            <span class="text-xs text-gray-500 font-normal" x-show="selectedPlatform === 'direct'">(مثال: https://example.com/video.mp4)</span>
                        </label>
                        <div class="flex gap-3">
                            <input type="url" 
                                   id="recording_url" 
                                   name="recording_url"
                                   x-model="videoUrl"
                                   @input.debounce.1000ms="updatePreview()"
                                   @paste.debounce.1000ms="updatePreview()"
                                   @blur="updatePreview()"
                                   value="{{ old('recording_url') }}"
                                   :placeholder="getPlaceholder()"
                                   class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                            <button type="button" 
                                    @click="updatePreview()"
                                    :disabled="!selectedPlatform || !videoUrl || isLoading"
                                    class="px-6 py-3 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white rounded-xl font-bold transition-all duration-300 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                <span x-show="!isLoading">
                                    <i class="fas fa-search ml-2"></i>
                                    قراءة الرابط
                                </span>
                                <span x-show="isLoading" class="flex items-center gap-2">
                                    <span class="loading-spinner"></span>
                                    جاري القراءة...
                                </span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 font-medium" x-show="selectedPlatform">
                            <i class="fas fa-info-circle ml-1"></i>
                            سيتم قراءة معلومات الفيديو وعرض المعاينة تلقائياً عند وضع الرابط
                        </p>
                        @error('recording_url')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- معلومات الفيديو -->
                    <div x-show="videoInfo" class="video-info-card" x-transition>
                        <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                            معلومات الفيديو
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">العنوان:</span>
                                <span class="text-gray-900 mr-2" x-text="videoInfo?.title || 'غير متاح'"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">المدة:</span>
                                <span class="text-gray-900 mr-2" x-text="videoInfo?.duration || 'غير متاح'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- معاينة الفيديو -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-eye text-[#2CA9BD] ml-1"></i>
                            معاينة الفيديو
                        </label>
                        <div class="video-preview-container" 
                             :class="{ 'has-video': hasPreview }">
                            <div x-show="!hasPreview && selectedPlatform" class="text-center text-gray-500 p-8">
                                <i class="fas fa-video text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold text-lg mb-2">معاينة الفيديو</p>
                                <p class="text-sm">ضع رابط الفيديو أعلاه لعرض المعاينة هنا</p>
                            </div>
                            <div x-ref="previewContainer" 
                                 class="w-full h-full flex items-center justify-center p-4" 
                                 style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- رسالة عند عدم اختيار المشغل -->
                <div x-show="!selectedPlatform" class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 text-center">
                    <i class="fas fa-hand-point-up text-4xl text-blue-500 mb-3"></i>
                    <p class="font-bold text-gray-900 text-lg mb-1">اختر مصدر الفيديو أولاً</p>
                    <p class="text-sm text-gray-600">اختر المشغل من الأعلى لعرض حقل الرابط</p>
                </div>
            </div>

            <!-- الموعد والمدة -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">
                    <i class="fas fa-calendar-alt text-[#2CA9BD] ml-2"></i>
                    الموعد والمدة
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- التاريخ والوقت -->
                    <div>
                        <label for="scheduled_at" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-clock text-[#2CA9BD] ml-1"></i>
                            التاريخ والوقت <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                               value="{{ old('scheduled_at') }}" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                        @error('scheduled_at')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- المدة -->
                    <div>
                        <label for="duration_minutes" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-hourglass-half text-[#2CA9BD] ml-1"></i>
                            المدة (بالدقائق) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_minutes" id="duration_minutes" 
                               value="{{ old('duration_minutes', 60) }}" min="15" max="480" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                        @error('duration_minutes')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- روابط Teams -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">
                    <i class="fab fa-microsoft text-[#2CA9BD] ml-2"></i>
                    روابط Microsoft Teams
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="teams_registration_link" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-user-plus text-[#2CA9BD] ml-1"></i>
                            رابط تسجيل Teams
                        </label>
                        <input type="url" name="teams_registration_link" id="teams_registration_link" 
                               value="{{ old('teams_registration_link') }}"
                               placeholder="https://teams.microsoft.com/..."
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                        @error('teams_registration_link')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="teams_meeting_link" class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-video text-[#2CA9BD] ml-1"></i>
                            رابط اجتماع Teams
                        </label>
                        <input type="url" name="teams_meeting_link" id="teams_meeting_link" 
                               value="{{ old('teams_meeting_link') }}"
                               placeholder="https://teams.microsoft.com/..."
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">
                        @error('teams_meeting_link')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">
                    <i class="fas fa-sticky-note text-[#2CA9BD] ml-2"></i>
                    الملاحظات
                </h2>
                
                <div>
                    <label for="notes" class="block text-sm font-bold text-gray-900 mb-2">
                        ملاحظات إضافية
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                              placeholder="ملاحظات إضافية..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2CA9BD] focus:border-[#2CA9BD] bg-white font-semibold">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الخيارات -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-gray-900 border-b-2 border-[#2CA9BD] pb-3">
                    <i class="fas fa-cog text-[#2CA9BD] ml-2"></i>
                    الخيارات
                </h2>
                
                <div class="space-y-4">
                    <label class="flex items-center gap-4 p-5 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl cursor-pointer hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-[#2CA9BD]/30">
                        <input type="checkbox" name="has_attendance_tracking" value="1" 
                               {{ old('has_attendance_tracking', true) ? 'checked' : '' }}
                               class="w-6 h-6 text-[#2CA9BD] border-gray-300 rounded focus:ring-[#2CA9BD]">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg">تتبع الحضور</div>
                            <div class="text-sm text-gray-600 font-medium">تسجيل حضور الطلاب تلقائياً أو يدوياً</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl cursor-pointer hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-[#2CA9BD]/30">
                        <input type="checkbox" name="has_assignment" value="1" 
                               {{ old('has_assignment') ? 'checked' : '' }}
                               class="w-6 h-6 text-[#2CA9BD] border-gray-300 rounded focus:ring-[#2CA9BD]">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg">يوجد واجب</div>
                            <div class="text-sm text-gray-600 font-medium">إضافة واجب مرتبط بهذه المحاضرة</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-5 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl cursor-pointer hover:shadow-lg transition-all duration-300 border-2 border-transparent hover:border-[#2CA9BD]/30">
                        <input type="checkbox" name="has_evaluation" value="1" 
                               {{ old('has_evaluation') ? 'checked' : '' }}
                               class="w-6 h-6 text-[#2CA9BD] border-gray-300 rounded focus:ring-[#2CA9BD]">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-lg">يوجد تقييم</div>
                            <div class="text-sm text-gray-600 font-medium">السماح للطلاب بتقييم المحاضرة</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-blue-50 border-t-2 border-gray-200 flex items-center justify-end gap-4">
            <a href="{{ route('instructor.lectures.index') }}" 
               class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-md">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ المحاضرة
            </button>
        </div>
    </form>
</div>

<script>
function videoPreviewData() {
    return {
        selectedPlatform: '{{ old('video_platform', '') }}',
        videoUrl: '{{ old('recording_url', '') }}',
        videoInfo: null,
        isLoading: false,
        hasPreview: false,
        
        selectPlatform(platform) {
            this.selectedPlatform = platform;
            this.videoUrl = '';
            this.hasPreview = false;
            this.videoInfo = null;
            this.clearPreview();
        },
        
        getPlaceholder() {
            if (this.selectedPlatform === 'youtube') return 'الصق رابط YouTube هنا...';
            if (this.selectedPlatform === 'vimeo') return 'الصق رابط Vimeo هنا...';
            if (this.selectedPlatform === 'google_drive') return 'الصق رابط Google Drive هنا...';
            if (this.selectedPlatform === 'direct') return 'الصق رابط الفيديو المباشر هنا...';
            return 'الصق رابط الفيديو هنا...';
        },
        
        updatePreview() {
            if (!this.videoUrl || !this.selectedPlatform) {
                this.hasPreview = false;
                this.clearPreview();
                return;
            }
            
            const url = String(this.videoUrl).trim();
            if (!url) {
                this.hasPreview = false;
                this.clearPreview();
                return;
            }
            
            this.generatePreview(url);
            this.fetchVideoInfo();
        },
        
        generatePreview(url) {
            try {
                const container = this.$refs.previewContainer;
                if (!container) return;
                
                let html = '';
                let isValid = false;
                
                // YouTube
                if (this.selectedPlatform === 'youtube') {
                    let videoId = null;
                    
                    // محاولة استخراج من youtube.com/watch?v=...
                    const watchMatch = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
                    if (watchMatch && watchMatch[1]) {
                        videoId = watchMatch[1];
                    }
                    
                    // محاولة استخراج من youtu.be/...
                    if (!videoId) {
                        const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
                        if (shortMatch && shortMatch[1]) {
                            videoId = shortMatch[1];
                        }
                    }
                    
                    // محاولة استخراج من youtube.com/embed/...
                    if (!videoId) {
                        const embedMatch = url.match(/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/);
                        if (embedMatch && embedMatch[1]) {
                            videoId = embedMatch[1];
                        }
                    }
                    
                    // محاولة استخراج من youtube.com/v/...
                    if (!videoId) {
                        const vMatch = url.match(/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/);
                        if (vMatch && vMatch[1]) {
                            videoId = vMatch[1];
                        }
                    }
                    
                    if (videoId && videoId.length === 11) {
                        isValid = true;
                        // استخدام youtube.com مع إعدادات محسّنة
                        const origin = encodeURIComponent(window.location.origin);
                        html = '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '" width="100%" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    
                    if (!isValid) {
                        html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700"><i class="fas fa-exclamation-circle ml-2"></i>رابط YouTube غير صحيح. تأكد من نسخ الرابط الكامل مثل: https://www.youtube.com/watch?v=VIDEO_ID</div>';
                    }
                }
                // Vimeo
                else if (this.selectedPlatform === 'vimeo') {
                    const pattern = /vimeo\.com\/(?:.*\/)?(\d+)/;
                    const match = url.match(pattern);
                    if (match && match[1]) {
                        const videoId = match[1];
                        isValid = true;
                        html = '<iframe src="https://player.vimeo.com/video/' + videoId + '?title=0&byline=0&portrait=0&badge=0&autopause=0&player_id=0&app_id=58479" width="100%" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) {
                        html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700"><i class="fas fa-exclamation-circle ml-2"></i>رابط Vimeo غير صحيح</div>';
                    }
                }
                // Google Drive
                else if (this.selectedPlatform === 'google_drive') {
                    const pattern = /drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/;
                    const match = url.match(pattern);
                    if (match && match[1]) {
                        const fileId = match[1];
                        isValid = true;
                        html = '<iframe src="https://drive.google.com/file/d/' + fileId + '/preview" width="100%" height="400" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) {
                        html = '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700"><i class="fas fa-info-circle ml-2"></i>ملاحظة: قد لا تعمل معاينة Google Drive بشكل صحيح. تأكد من أن الملف قابل للمشاركة.</div>';
                    }
                }
                // Direct Video
                else if (this.selectedPlatform === 'direct') {
                    const pattern = /\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i;
                    if (pattern.test(url)) {
                        isValid = true;
                        const escapedUrl = url.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        html = '<video controls width="100%" height="400" style="max-height: 400px; border-radius: 0.75rem;" class="w-full"><source src="' + escapedUrl + '" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>';
                    }
                    if (!isValid) {
                        html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700"><i class="fas fa-exclamation-circle ml-2"></i>الرابط المباشر يجب أن ينتهي بامتداد فيديو (.mp4, .webm, .ogg, .avi, .mov)</div>';
                    }
                }
                
                if (html) {
                    container.innerHTML = html;
                    this.hasPreview = true;
                } else {
                    this.clearPreview();
                }
            } catch (error) {
                console.error('Error generating preview:', error);
                const container = this.$refs.previewContainer;
                if (container) {
                    container.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">حدث خطأ في عرض المعاينة</div>';
                    this.hasPreview = true;
                }
            }
        },
        
        clearPreview() {
            const container = this.$refs.previewContainer;
            if (container) {
                container.innerHTML = '';
            }
            this.hasPreview = false;
        },
        
        async fetchVideoInfo() {
            if (!this.videoUrl || !this.selectedPlatform) return;
            
            this.isLoading = true;
            this.videoInfo = null;
            
            try {
                const response = await fetch('/api/video/info', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        url: this.videoUrl,
                        platform: this.selectedPlatform
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.videoInfo = data.data;
                }
            } catch (error) {
                console.log('Video info fetch failed (optional):', error);
            } finally {
                this.isLoading = false;
            }
        }
    };
}

// تحديث قائمة الدروس عند اختيار الكورس
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course_id');
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
            const courseId = this.value;
            const lessonSelect = document.getElementById('course_lesson_id');
            
            // مسح الخيارات الحالية (ما عدا الخيار الأول)
            while (lessonSelect.children.length > 1) {
                lessonSelect.removeChild(lessonSelect.lastChild);
            }
            
            if (courseId) {
                // جلب دروس الكورس
                fetch('/api/courses/' + courseId + '/lessons')
                    .then(response => response.json())
                    .then(data => {
                        if (data.lessons) {
                            data.lessons.forEach(function(lesson) {
                                const option = document.createElement('option');
                                option.value = lesson.id;
                                option.textContent = lesson.title;
                                lessonSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching lessons:', error);
                        // محاولة جلب الدروس من الصفحة إذا كان API غير متاح
                        fetch('{{ route('instructor.lectures.create') }}?course_id=' + courseId)
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newLessonSelect = doc.getElementById('course_lesson_id');
                                if (newLessonSelect) {
                                    Array.from(newLessonSelect.options).forEach(function(option) {
                                        if (option.value) {
                                            const newOption = document.createElement('option');
                                            newOption.value = option.value;
                                            newOption.textContent = option.textContent;
                                            lessonSelect.appendChild(newOption);
                                        }
                                    });
                                }
                            })
                            .catch(err => console.error('Error:', err));
                    });
            }
        });
    }
});
</script>
@endsection
