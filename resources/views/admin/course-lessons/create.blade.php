@extends('layouts.admin')

@section('title', 'إضافة درس برمجي جديد')
@section('header', 'إضافة درس جديد للكورس: ' . $course->title)

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-white">الكورسات</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.courses.lessons.index', $course) }}" class="hover:text-white">دروس {{ Str::limit($course->title, 25) }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">إضافة درس</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">إضافة درس جديد</h1>
                <p class="text-sm text-white/90 mt-1 truncate">{{ $course->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    العودة للدروس
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-graduation-cap text-2xl text-indigo-600"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-900 truncate">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $course->academicYear->name ?? '—' }} · {{ $course->academicSubject->name ?? '—' }}
                </p>
            </div>
        </div>
    </div>

    <!-- نموذج إضافة الدرس -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-900">بيانات الدرس الجديد</h4>
        </div>

        <form action="{{ route('admin.courses.lessons.store', $course) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- عنوان الدرس -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        عنوان الدرس <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="أدخل عنوان الدرس"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- نوع الدرس -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                        نوع الدرس <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            id="type" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            required 
                            onchange="toggleTypeFields()">
                        <option value="">اختر نوع الدرس</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>فيديو</option>
                        <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>مستند</option>
                        <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>كويز</option>
                        <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>واجب</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- مدة الدرس -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                        مدة الدرس (دقيقة)
                    </label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes') }}"
                           min="1"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="مثال: 30">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ترتيب الدرس -->
                <div>
                    <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                        ترتيب الدرس
                    </label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order') }}"
                           min="0"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="يُحدد تلقائياً إن تُرك فارغاً">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الخيارات -->
                <div class="md:col-span-2 flex flex-wrap items-center gap-6 gap-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="is_free" 
                               value="1"
                               {{ old('is_free') ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        <span class="text-sm font-medium text-gray-700">درس مجاني</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        <span class="text-sm font-medium text-gray-700">درس نشط</span>
                    </label>
                </div>
            </div>

            <!-- وصف الدرس -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    وصف الدرس
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                          placeholder="وصف مختصر عن محتوى الدرس">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- محتوى الدرس -->
            <div class="mt-6">
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                    محتوى الدرس
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="6"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                          placeholder="محتوى الدرس التفصيلي">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- رابط الفيديو (للفيديوهات) -->
            <div id="video_url_field" class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200" style="display: none;">
                <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                    رابط الفيديو
                </label>
                <input type="url" 
                       name="video_url" 
                       id="video_url" 
                       value="{{ old('video_url') }}"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       placeholder="YouTube / Vimeo / Google Drive / رابط .mp4"
                       onblur="previewVideo()">
                <div class="mt-3 text-sm text-gray-500">
                    <p class="mb-1 font-medium text-gray-600">المصادر المدعومة:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-gray-500">
                        <li>YouTube · Vimeo · Google Drive · ملفات .mp4, .avi, .mov</li>
                    </ul>
                </div>
                <div id="video_preview" class="mt-3 rounded-xl overflow-hidden border border-gray-200" style="display: none;">
                    <div class="bg-black" style="aspect-ratio: 16/9; max-height: 220px;">
                        <div id="video_embed_container"></div>
                    </div>
                </div>
                @error('video_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- رفع المرفقات -->
            <div class="mt-6">
                <label for="attachments" class="block text-sm font-semibold text-gray-700 mb-2">
                    المرفقات (اختياري)
                </label>
                <input type="file" 
                       name="attachments[]" 
                       id="attachments" 
                       multiple
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold">
                <p class="mt-1 text-sm text-gray-500">يمكن رفع عدة ملفات. الحد الأقصى لكل ملف: 10 ميجابايت</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الحفظ -->
            <div class="flex flex-wrap items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                    إلغاء
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-save"></i>
                    حفظ الدرس
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

function previewVideo() {
    const url = document.getElementById('video_url').value;
    const previewDiv = document.getElementById('video_preview');
    const embedContainer = document.getElementById('video_embed_container');
    
    if (!url) {
        previewDiv.style.display = 'none';
        return;
    }
    
    // تحويل الرابط إلى embed
    let embedHtml = generateVideoEmbed(url);
    
    if (embedHtml.includes('غير مدعوم')) {
        embedContainer.innerHTML = `
            <div class="bg-red-100 text-red-700 p-4 rounded-lg h-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                رابط الفيديو غير صحيح أو غير مدعوم
            </div>
        `;
    } else {
        embedContainer.innerHTML = embedHtml;
    }
    
    previewDiv.style.display = 'block';
}

function generateVideoEmbed(url) {
    // YouTube
    const youtubeMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
    if (youtubeMatch) {
        const videoId = youtubeMatch[1];
        return `<iframe src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&showinfo=0" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>`;
    }
    
    // Vimeo
    const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
    if (vimeoMatch) {
        const videoId = vimeoMatch[1];
        return `<iframe src="https://player.vimeo.com/video/${videoId}?title=0&byline=0&portrait=0" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>`;
    }
    
    // Google Drive
    const driveMatch = url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
    if (driveMatch) {
        const fileId = driveMatch[1];
        return `<iframe src="https://drive.google.com/file/d/${fileId}/preview" width="100%" height="100%" frameborder="0" allow="autoplay" class="w-full h-full"></iframe>`;
    }
    
    // ملف فيديو مباشر
    if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
        return `<video controls width="100%" height="100%" class="w-full h-full"><source src="${url}" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>`;
    }
    
    // مصدر غير مدعوم
    return `<div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg h-full flex items-center justify-center">نوع الفيديو غير مدعوم حالياً</div>`;
}
</script>
@endpush
@endsection
