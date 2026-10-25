@extends('layouts.admin')

@section('title', 'تعديل السؤال')
@section('header', 'تعديل السؤال: ' . Str::limit($question->question, 50))

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.question-bank.index') }}" class="hover:text-primary-600">بنك الأسئلة</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.question-bank.show', $question) }}" class="hover:text-primary-600">تفاصيل السؤال</a>
                <span class="mx-2">/</span>
                <span>تعديل</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.question-bank.show', $question) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-eye ml-2"></i>
                عرض السؤال
            </a>
            <a href="{{ route('admin.question-bank.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- نموذج تعديل السؤال -->
    <form action="{{ route('admin.question-bank.update', $question) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات السؤال</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- نص السؤال -->
                        <div>
                            <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                نص السؤال <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question" id="question" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="اكتب نص السؤال هنا...">{{ old('question', $question->question) }}</textarea>
                            @error('question')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- نوع السؤال -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    نوع السؤال <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required onchange="toggleQuestionFields()"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر نوع السؤال</option>
                                    @foreach($questionTypes as $key => $type)
                                        <option value="{{ $key }}" {{ old('type', $question->type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    مستوى الصعوبة <span class="text-red-500">*</span>
                                </label>
                                <select name="difficulty_level" id="difficulty_level" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر مستوى الصعوبة</option>
                                    @foreach($difficultyLevels as $key => $level)
                                        <option value="{{ $key }}" {{ old('difficulty_level', $question->difficulty_level) == $key ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- الدرجة والوقت -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                                    درجة السؤال <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" id="points" step="0.5" min="0.5" max="100" 
                                       value="{{ old('points', $question->points) }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="1.0">
                                @error('points')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                    الوقت المحدد (ثانية)
                                </label>
                                <input type="number" name="time_limit" id="time_limit" min="10" max="600" 
                                       value="{{ old('time_limit', $question->time_limit) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="اتركه فارغاً لاستخدام وقت الامتحان العام">
                                @error('time_limit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- خيارات السؤال (تظهر حسب النوع) -->
                <div id="question-options" class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">خيارات السؤال</h3>
                    </div>
                    <div class="p-6">
                        <!-- اختيار متعدد -->
                        <div id="multiple-choice-options" style="display: none;">
                            <div class="space-y-4">
                                @for($i = 0; $i < 5; $i++)
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_option" value="{{ $i }}" id="correct_{{ $i }}"
                                               {{ (is_array($question->correct_answer) && in_array((string)$i, $question->correct_answer)) ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <label for="option_{{ $i + 1 }}" class="text-sm font-medium text-gray-700">الخيار {{ $i + 1 }}:</label>
                                        <input type="text" name="option_{{ $i + 1 }}" id="option_{{ $i + 1 }}" 
                                               value="{{ old('option_' . ($i + 1), $question->options[$i] ?? '') }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="اكتب الخيار {{ $i + 1 }} {{ $i < 2 ? '(مطلوب)' : '(اختياري)' }}" 
                                               {{ $i < 2 ? 'required' : '' }}>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- صح أو خطأ -->
                        <div id="true-false-options" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">الإجابة الصحيحة:</label>
                                <div class="flex gap-4">
                                    @php
                                        $tfAnswer = is_array($question->correct_answer) ? ($question->correct_answer[0] ?? '') : $question->correct_answer;
                                    @endphp
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="صح" 
                                               {{ old('true_false_answer', $tfAnswer) == 'صح' ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <span class="mr-2 text-sm font-medium text-gray-700">صح</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="خطأ" 
                                               {{ old('true_false_answer', $tfAnswer) == 'خطأ' ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <span class="mr-2 text-sm font-medium text-gray-700">خطأ</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- املأ الفراغ -->
                        <div id="fill-blank-options" style="display: none;">
                            <div>
                                <label for="correct_answers" class="block text-sm font-medium text-gray-700 mb-2">
                                    الإجابات الصحيحة (مفصولة بفواصل)
                                </label>
                                @php
                                    $correctAnswers = is_array($question->correct_answer) ? 
                                        implode(', ', $question->correct_answer) : 
                                        $question->correct_answer;
                                @endphp
                                <input type="text" name="correct_answers" id="correct_answers" 
                                       value="{{ old('correct_answers', $correctAnswers) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="الإجابة الأولى, الإجابة الثانية, ...">
                                <p class="mt-1 text-sm text-gray-500">يمكنك إدخال عدة إجابات صحيحة مفصولة بفواصل</p>
                            </div>
                        </div>

                        <!-- إجابة قصيرة/مقالي -->
                        <div id="text-answer-options" style="display: none;">
                            <div>
                                <label for="model_answer" class="block text-sm font-medium text-gray-700 mb-2">
                                    الإجابة النموذجية (اختياري)
                                </label>
                                @php
                                    $modelAnswer = is_array($question->correct_answer) ? 
                                        implode("\n", $question->correct_answer) : 
                                        $question->correct_answer;
                                @endphp
                                <textarea name="model_answer" id="model_answer" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="اكتب الإجابة النموذجية للمساعدة في التصحيح...">{{ old('model_answer', $modelAnswer) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">ستساعد في التصحيح اليدوي</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوسائط -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">الوسائط المرفقة</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <!-- الصورة الحالية -->
                        @if($question->image_url)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الصورة الحالية:
                                </label>
                                <div class="flex items-start gap-4">
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $question->image_url) }}" 
                                             alt="صورة السؤال" 
                                             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                        <button type="button" 
                                                onclick="removeCurrentImage()"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition-colors"
                                                title="حذف الصورة">
                                            ×
                                        </button>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">
                                            اسم الملف: {{ basename($question->image_url) }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            انقر على × لحذف الصورة أو ارفع صورة جديدة لاستبدالها
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" id="remove_image" name="remove_image" value="0">
                            </div>
                        @endif

                        <!-- رفع صورة جديدة -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $question->image_url ? 'تغيير الصورة' : 'رفع صورة' }}
                            </label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <p class="mt-1 text-sm text-gray-500">الحد الأقصى: 2 ميجابايت. الأنواع المدعومة: JPG, PNG, GIF</p>
                        </div>

                        <!-- أو رابط صورة خارجي -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">
                                أو رابط صورة خارجي
                            </label>
                            <input type="url" name="image_url" id="image_url" 
                                   value="{{ old('image_url', $question->image_url && !str_starts_with($question->image_url, 'questions/') ? $question->image_url : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <!-- رابط صوتي -->
                        <div>
                            <label for="audio_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط ملف صوتي
                            </label>
                            <input type="url" name="audio_url" id="audio_url" 
                                   value="{{ old('audio_url', $question->audio_url) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://example.com/audio.mp3">
                        </div>

                        <!-- رابط فيديو -->
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط فيديو
                            </label>
                            <input type="url" name="video_url" id="video_url" 
                                   value="{{ old('video_url', $question->video_url) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://www.youtube.com/watch?v=... أو أي رابط فيديو">
                        </div>
                    </div>
                </div>

                <!-- الشرح -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">شرح الإجابة</h3>
                    </div>
                    <div class="p-6">
                        <textarea name="explanation" id="explanation" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="اكتب شرحاً مفصلاً للإجابة الصحيحة (اختياري)...">{{ old('explanation', $question->explanation) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">سيظهر للطلاب بعد الانتهاء من الامتحان (حسب إعدادات الامتحان)</p>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- التصنيف والتاجز -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">التصنيف والتاجز</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- التصنيف -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                التصنيف <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $question->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->full_path ?? $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- التاجز -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                التاجز (مفصولة بفواصل)
                            </label>
                            @php
                                $tagsString = is_array($question->tags) ? implode(', ', $question->tags) : '';
                            @endphp
                            <input type="text" name="tags" id="tags" 
                                   value="{{ old('tags', $tagsString) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="رياضيات, جبر, معادلات">
                            <p class="mt-1 text-sm text-gray-500">ستساعد في البحث والتصنيف</p>
                        </div>

                        <!-- حالة السؤال -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $question->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                <span class="mr-2 text-sm font-medium text-gray-700">سؤال نشط</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- معاينة -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">معاينة السؤال</h3>
                    </div>
                    <div class="p-6">
                        <div id="question-preview" class="min-h-32 bg-gray-50 rounded-lg p-4">
                            <!-- سيتم تحديثها بـ JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التحديثات
                            </button>
                            
                            <a href="{{ route('admin.question-bank.show', $question) }}" 
                               class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                <i class="fas fa-eye ml-2"></i>
                                عرض السؤال
                            </a>
                            
                            <a href="{{ route('admin.question-bank.index') }}" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleQuestionFields() {
    const type = document.getElementById('type').value;
    
    // إخفاء جميع الخيارات
    document.getElementById('multiple-choice-options').style.display = 'none';
    document.getElementById('true-false-options').style.display = 'none';
    document.getElementById('fill-blank-options').style.display = 'none';
    document.getElementById('text-answer-options').style.display = 'none';
    
    if (type) {
        switch(type) {
            case 'multiple_choice':
                document.getElementById('multiple-choice-options').style.display = 'block';
                break;
            case 'true_false':
                document.getElementById('true-false-options').style.display = 'block';
                break;
            case 'fill_blank':
                document.getElementById('fill-blank-options').style.display = 'block';
                break;
            case 'short_answer':
            case 'essay':
                document.getElementById('text-answer-options').style.display = 'block';
                break;
        }
    }
    
    updatePreview();
}

function updatePreview() {
    const question = document.getElementById('question').value;
    const type = document.getElementById('type').value;
    const preview = document.getElementById('question-preview');
    
    if (!question) {
        preview.innerHTML = '<div class="text-center text-gray-500">اكتب السؤال لرؤية المعاينة</div>';
        return;
    }
    
    let previewHtml = `<div class="text-right"><strong>السؤال:</strong> ${question}</div>`;
    
    if (type === 'multiple_choice') {
        previewHtml += '<div class="mt-3"><strong>الخيارات:</strong>';
        for (let i = 1; i <= 5; i++) {
            const optionElement = document.getElementById(`option_${i}`);
            if (optionElement) {
                const option = optionElement.value;
                if (option) {
                    previewHtml += `<div class="mt-1">○ ${option}</div>`;
                }
            }
        }
        previewHtml += '</div>';
    } else if (type === 'true_false') {
        previewHtml += '<div class="mt-3"><strong>الخيارات:</strong><div class="mt-1">○ صح</div><div class="mt-1">○ خطأ</div></div>';
    }
    
    preview.innerHTML = previewHtml;
}

function removeCurrentImage() {
    if (confirm('هل أنت متأكد من حذف الصورة الحالية؟')) {
        document.getElementById('remove_image').value = '1';
        // إخفاء عرض الصورة
        document.querySelector('.relative').style.display = 'none';
    }
}

// تحديث المعاينة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    toggleQuestionFields();
    
    document.getElementById('question').addEventListener('input', updatePreview);
    document.getElementById('type').addEventListener('change', updatePreview);
    
    // تحديث المعاينة عند تغيير الخيارات
    for (let i = 1; i <= 5; i++) {
        const option = document.getElementById(`option_${i}`);
        if (option) {
            option.addEventListener('input', updatePreview);
        }
    }
    
    // تحديث المعاينة الأولية
    updatePreview();
});
</script>
@endpush
@endsection
