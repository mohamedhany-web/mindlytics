@extends('layouts.admin')

@section('title', 'تعديل الدرس')
@section('header', 'تعديل الدرس: ' . $lesson->title)

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-white">الكورسات</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="hover:text-white">دروس {{ Str::limit($course->title, 25) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">تعديل الدرس</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">تعديل: {{ Str::limit($lesson->title, 40) }}</h1>
                <p class="text-sm text-white/90 mt-1">{{ $course->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-eye"></i>
                    عرض الدرس
                </a>
                <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-medium transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للدروس
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات الكورس (مختصرة) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-graduation-cap text-indigo-600"></i>
        </div>
        <div class="min-w-0">
            <h3 class="font-semibold text-gray-900 truncate">{{ $course->title }}</h3>
            <p class="text-sm text-gray-500">{{ $course->academicYear->name ?? '—' }} · {{ $course->academicSubject->name ?? '—' }}</p>
        </div>
    </div>

    <!-- نموذج تعديل الدرس -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-indigo-600"></i>
                تعديل بيانات الدرس
            </h4>
        </div>

        <form action="{{ route('admin.courses.lessons.update', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- عنوان الدرس -->
                <div class="lg:col-span-8">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        عنوان الدرس <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $lesson->title) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="أدخل عنوان الدرس"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- نوع الدرس + مدة + ترتيب في صف واحد -->
                <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">نوع الدرس <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required onchange="toggleTypeFields()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">اختر النوع</option>
                            <option value="video" {{ old('type', $lesson->type) == 'video' ? 'selected' : '' }}>فيديو</option>
                            <option value="document" {{ old('type', $lesson->type) == 'document' ? 'selected' : '' }}>مستند</option>
                            <option value="quiz" {{ old('type', $lesson->type) == 'quiz' ? 'selected' : '' }}>كويز</option>
                            <option value="assignment" {{ old('type', $lesson->type) == 'assignment' ? 'selected' : '' }}>واجب</option>
                        </select>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">المدة (دقيقة)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1" placeholder="30"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('duration_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">الترتيب</label>
                        <input type="number" name="order" id="order" value="{{ old('order', $lesson->order) }}" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- خيارات: مجاني / نشط -->
            <div class="mt-6 flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" {{ old('is_free', $lesson->is_free) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                    <span class="text-sm font-medium text-gray-700">درس مجاني</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $lesson->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                    <span class="text-sm font-medium text-gray-700">درس نشط</span>
                </label>
            </div>

            <!-- وصف ومحتوى الدرس -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">وصف الدرس</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="وصف مختصر عن محتوى الدرس">{{ old('description', $lesson->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">محتوى الدرس</label>
                    <textarea name="content" id="content" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                              placeholder="محتوى الدرس التفصيلي">{{ old('content', $lesson->content) }}</textarea>
                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- رابط الفيديو (للفيديوهات) -->
            <div id="video_url_field" class="mt-8 p-6 rounded-xl bg-gray-50 border border-gray-200" style="display: none;">
                <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">رابط الفيديو</label>
                <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       placeholder="https://www.youtube.com/watch?v=... أو رابط فيديو آخر">
                
                @if($lesson->video_url)
                    <div class="mt-3 p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">معاينة الفيديو الحالي:</span>
                            @php
                                $videoSource = \App\Helpers\VideoHelper::getVideoSource($lesson->video_url);
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($videoSource == 'youtube') bg-red-100 text-red-800
                                @elseif($videoSource == 'vimeo') bg-blue-100 text-blue-800
                                @elseif($videoSource == 'google_drive') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($videoSource == 'youtube') YouTube
                                @elseif($videoSource == 'vimeo') Vimeo
                                @elseif($videoSource == 'google_drive') Google Drive
                                @elseif($videoSource == 'direct') ملف مباشر
                                @else مصدر آخر
                                @endif
                            </span>
                        </div>
                        <div class="bg-black rounded-xl overflow-hidden" style="aspect-ratio: 16/9; max-height: 200px;">
                            {!! \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%') !!}
                        </div>
                    </div>
                @endif
                
                <div class="mt-2 text-sm text-gray-500">
                    <p class="mb-1"><strong>المصادر المدعومة:</strong></p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>YouTube: https://www.youtube.com/watch?v=VIDEO_ID</li>
                        <li>Vimeo: https://vimeo.com/VIDEO_ID</li>
                        <li>Google Drive: رابط مشاركة الفيديو</li>
                        <li>ملفات فيديو مباشرة: .mp4, .avi, .mov</li>
                    </ul>
                </div>
                @error('video_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>



            <!-- المرفقات الحالية -->
            @if($lesson->attachments)
                @php
                    $attachments = json_decode($lesson->attachments, true);
                @endphp
                @if($attachments && count($attachments) > 0)
                    <div class="mt-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">المرفقات الحالية</label>
                        <div class="space-y-2">
                            @foreach($attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <i class="fas fa-file text-primary-600"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $attachment['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ number_format($attachment['size'] / 1024, 2) }} KB</div>
                                        </div>
                                    </div>
                                    <a href="{{ $attachment['path'] }}" 
                                       target="_blank"
                                       class="text-primary-600 hover:text-primary-700 font-medium">
                                        <i class="fas fa-download ml-1"></i>
                                        تحميل
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- رفع مرفقات جديدة -->
            <div class="mt-8">
                <label for="attachments" class="block text-sm font-semibold text-gray-700 mb-2">إضافة مرفقات جديدة (اختياري)</label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold">
                <p class="mt-1 text-sm text-gray-500">يمكن رفع عدة ملفات. الحد الأقصى لكل ملف: 10 ميجابايت. سيتم إضافتها للمرفقات الحالية.</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الحفظ -->
            <div class="flex flex-wrap items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    إلغاء والعودة
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleTypeFields() {
    const type = document.getElementById('type').value;
    const videoUrlField = document.getElementById('video_url_field');
    
    // إخفاء جميع الحقول أولاً
    videoUrlField.style.display = 'none';
    
    // إظهار حقل رابط الفيديو للفيديوهات فقط
    if (type === 'video') {
        videoUrlField.style.display = 'block';
    }
}

// تشغيل الدالة عند تحميل الصفحة للحفاظ على القيم القديمة
document.addEventListener('DOMContentLoaded', function() {
    toggleTypeFields();
});
</script>
@endpush
@endsection
