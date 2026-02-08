@extends('layouts.app')

@section('title', 'تعديل السؤال - Mindlytics')
@section('header', 'تعديل السؤال')

@push('styles')
<style>
    .form-section {
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-section:hover {
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 8px 16px rgba(44, 169, 189, 0.1);
    }

    .form-input {
        border: 2px solid rgba(44, 169, 189, 0.2);
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: #2CA9BD;
        box-shadow: 0 0 0 4px rgba(44, 169, 189, 0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">تعديل السؤال</h1>
                <p class="text-sm sm:text-base text-[#1F3A56] font-medium">تعديل معلومات السؤال</p>
            </div>
            <a href="{{ route('instructor.question-banks.show', $question->questionBank) }}" 
               class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </div>
    </div>

    <!-- نموذج تعديل السؤال -->
    <form action="{{ route('instructor.questions.update', $question) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات السؤال -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">معلومات السؤال</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- نوع السؤال -->
                        <div>
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                نوع السؤال <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="question_type" required onchange="updateQuestionForm()"
                                    class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>اختيار متعدد</option>
                                <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>صح أو خطأ</option>
                                <option value="fill_blank" {{ $question->type == 'fill_blank' ? 'selected' : '' }}>املأ الفراغ</option>
                                <option value="short_answer" {{ $question->type == 'short_answer' ? 'selected' : '' }}>إجابة قصيرة</option>
                                <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>مقالي</option>
                            </select>
                        </div>

                        <!-- نص السؤال -->
                        <div>
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                نص السؤال <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question" rows="4" required
                                      class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                      placeholder="اكتب نص السؤال هنا...">{{ old('question', $question->question) }}</textarea>
                            @error('question')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الخيارات (لاختيار متعدد) -->
                        <div id="options_field" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                الخيارات (كل خيار في سطر)
                            </label>
                            <textarea name="options_text" rows="4"
                                      class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                      placeholder="الخيار الأول&#10;الخيار الثاني&#10;الخيار الثالث&#10;الخيار الرابع">@if($question->options && is_array($question->options)){{ implode("\n", $question->options) }}@endif</textarea>
                        </div>

                        <!-- الإجابة الصحيحة - ديناميكي حسب النوع -->
                        <div id="correct_answer_field">
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                الإجابة الصحيحة <span class="text-red-500">*</span>
                            </label>
                            
                            @php
                                $correctAnswer = is_array($question->correct_answer) ? $question->correct_answer : [$question->correct_answer];
                                $correctAnswerValue = is_array($question->correct_answer) ? implode("\n", $question->correct_answer) : $question->correct_answer;
                            @endphp
                            
                            <!-- لاختيار متعدد -->
                            <div id="correct_answer_multiple_choice" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
                                <select name="correct_answer" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                    <option value="">اختر الإجابة الصحيحة</option>
                                    @if($question->options && is_array($question->options))
                                        @foreach($question->options as $option)
                                            <option value="{{ $option }}" {{ in_array($option, $correctAnswer) ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="mt-1 text-xs text-gray-500">سيتم تحديث الخيارات تلقائياً عند تعديل الخيارات أعلاه</p>
                            </div>
                            
                            <!-- لصح أو خطأ -->
                            <div id="correct_answer_true_false" style="display: {{ $question->type == 'true_false' ? 'block' : 'none' }};">
                                <select name="correct_answer" class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                    <option value="">اختر الإجابة</option>
                                    <option value="صح" {{ in_array('صح', $correctAnswer) ? 'selected' : '' }}>صح</option>
                                    <option value="خطأ" {{ in_array('خطأ', $correctAnswer) ? 'selected' : '' }}>خطأ</option>
                                </select>
                            </div>
                            
                            <!-- لاملأ الفراغ -->
                            <div id="correct_answer_fill_blank" style="display: {{ $question->type == 'fill_blank' ? 'block' : 'none' }};">
                                <textarea name="correct_answer" rows="3"
                                          class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                          placeholder="أدخل الإجابات الصحيحة (كل إجابة في سطر)">{{ $correctAnswerValue }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">يمكن إدخال عدة إجابات صحيحة، كل إجابة في سطر منفصل</p>
                            </div>
                            
                            <!-- لإجابة قصيرة -->
                            <div id="correct_answer_short_answer" style="display: {{ $question->type == 'short_answer' ? 'block' : 'none' }};">
                                <input type="text" name="correct_answer" value="{{ $correctAnswer[0] ?? '' }}"
                                       class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                       placeholder="الإجابة الصحيحة (كلمة أو جملة قصيرة)">
                            </div>
                            
                            <!-- لمقالي -->
                            <div id="correct_answer_essay" style="display: {{ $question->type == 'essay' ? 'block' : 'none' }};">
                                <textarea name="correct_answer" rows="5"
                                          class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                          placeholder="نموذج الإجابة المثالية أو النقاط الرئيسية للإجابة...">{{ $correctAnswerValue }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">هذا السؤال يحتاج تقييم يدوي من المدرب</p>
                            </div>
                            
                            @error('correct_answer')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الشرح -->
                        <div>
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                شرح الإجابة
                            </label>
                            <textarea name="explanation" rows="3"
                                      class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                      placeholder="شرح الإجابة الصحيحة...">{{ old('explanation', $question->explanation) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- إعدادات السؤال -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">إعدادات السؤال</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- النقاط -->
                            <div>
                                <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                    النقاط <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.5" step="0.5" required
                                       class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                            </div>

                            <!-- مستوى الصعوبة -->
                            <div>
                                <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                    مستوى الصعوبة <span class="text-red-500">*</span>
                                </label>
                                <select name="difficulty_level" required
                                        class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                    <option value="easy" {{ $question->difficulty_level == 'easy' ? 'selected' : '' }}>سهل</option>
                                    <option value="medium" {{ $question->difficulty_level == 'medium' ? 'selected' : '' }}>متوسط</option>
                                    <option value="hard" {{ $question->difficulty_level == 'hard' ? 'selected' : '' }}>صعب</option>
                                </select>
                            </div>

                            <!-- التصنيف -->
                            <div>
                                <label class="block text-sm font-bold text-[#1C2C39] mb-2">
                                    التصنيف
                                </label>
                                <select name="category_id"
                                        class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                    <option value="">بدون تصنيف</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $question->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- معلومات سريعة -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">معلومات سريعة</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-blue-600"></i>
                                <span class="text-sm font-bold text-blue-800">نصائح</span>
                            </div>
                            <ul class="mt-2 text-sm text-blue-700 space-y-1.5 font-medium">
                                <li>• يمكنك تعديل جميع المعلومات</li>
                                <li>• تأكد من صحة الإجابة الصحيحة</li>
                                <li>• سيتم تحديث السؤال في جميع الاختبارات</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الحالة -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">إعدادات الحالة</h3>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-[#2CA9BD] bg-gray-100 border-gray-300 rounded focus:ring-[#2CA9BD] focus:ring-2">
                            <span class="text-sm text-[#1C2C39] font-medium group-hover:text-[#2CA9BD] transition-colors">سؤال نشط</span>
                        </label>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التغييرات
                            </button>
                            
                            <a href="{{ route('instructor.question-banks.show', $question->questionBank) }}" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-xl font-bold transition-all duration-300 block text-center">
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
function updateQuestionForm() {
    const type = document.getElementById('question_type').value;
    const optionsField = document.getElementById('options_field');
    
    // إخفاء جميع حقول الإجابة الصحيحة
    const answerFields = ['correct_answer_multiple_choice', 'correct_answer_true_false', 
                         'correct_answer_fill_blank', 'correct_answer_short_answer', 'correct_answer_essay'];
    answerFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.style.display = 'none';
        const select = field?.querySelector('select[name="correct_answer"]');
        const input = field?.querySelector('input[name="correct_answer"]');
        const textarea = field?.querySelector('textarea[name="correct_answer"]');
        if (select) select.removeAttribute('required');
        if (input) input.removeAttribute('required');
        if (textarea) textarea.removeAttribute('required');
    });
    
    // عرض الحقل المناسب حسب النوع
    if (type === 'multiple_choice') {
        if (optionsField) optionsField.style.display = 'block';
        const field = document.getElementById('correct_answer_multiple_choice');
        if (field) {
            field.style.display = 'block';
            const select = field.querySelector('select[name="correct_answer"]');
            if (select) select.setAttribute('required', 'required');
        }
        // تحديث الخيارات عند تغييرها
        const optionsText = document.querySelector('textarea[name="options_text"]');
        if (optionsText) {
            optionsText.addEventListener('input', updateMultipleChoiceOptions);
        }
    } else if (type === 'true_false') {
        if (optionsField) optionsField.style.display = 'none';
        const field = document.getElementById('correct_answer_true_false');
        if (field) {
            field.style.display = 'block';
            const select = field.querySelector('select[name="correct_answer"]');
            if (select) select.setAttribute('required', 'required');
        }
    } else if (type === 'fill_blank') {
        if (optionsField) optionsField.style.display = 'none';
        const field = document.getElementById('correct_answer_fill_blank');
        if (field) {
            field.style.display = 'block';
            const textarea = field.querySelector('textarea[name="correct_answer"]');
            if (textarea) textarea.setAttribute('required', 'required');
        }
    } else if (type === 'short_answer') {
        if (optionsField) optionsField.style.display = 'none';
        const field = document.getElementById('correct_answer_short_answer');
        if (field) {
            field.style.display = 'block';
            const input = field.querySelector('input[name="correct_answer"]');
            if (input) input.setAttribute('required', 'required');
        }
    } else if (type === 'essay') {
        if (optionsField) optionsField.style.display = 'none';
        const field = document.getElementById('correct_answer_essay');
        if (field) {
            field.style.display = 'block';
            const textarea = field.querySelector('textarea[name="correct_answer"]');
            if (textarea) textarea.setAttribute('required', 'required');
        }
    } else {
        if (optionsField) optionsField.style.display = 'none';
    }
}

function updateMultipleChoiceOptions() {
    const optionsText = document.querySelector('textarea[name="options_text"]');
    const select = document.querySelector('#correct_answer_multiple_choice select[name="correct_answer"]');
    
    if (!optionsText || !select) return;
    
    const options = optionsText.value.split('\n').filter(opt => opt.trim());
    
    // حفظ القيمة المحددة حالياً
    const currentValue = select.value;
    
    // مسح الخيارات الحالية (ما عدا الخيار الأول)
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    // إضافة الخيارات الجديدة
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.trim();
        optionElement.textContent = option.trim();
        select.appendChild(optionElement);
    });
    
    // استعادة القيمة المحددة إذا كانت موجودة
    if (currentValue && Array.from(select.options).some(opt => opt.value === currentValue)) {
        select.value = currentValue;
    }
}

// تحديث النموذج عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    updateQuestionForm();
    
    // تحديث خيارات الاختيار المتعدد عند تغيير نص الخيارات
    const optionsText = document.querySelector('textarea[name="options_text"]');
    if (optionsText) {
        optionsText.addEventListener('input', updateMultipleChoiceOptions);
    }
});

// معالجة الخيارات عند الإرسال
document.querySelector('form[action*="questions.update"]')?.addEventListener('submit', function(e) {
    const optionsText = this.querySelector('textarea[name="options_text"]');
    if (optionsText && optionsText.value && document.getElementById('question_type').value === 'multiple_choice') {
        const options = optionsText.value.split('\n').filter(opt => opt.trim());
        const optionsInput = document.createElement('input');
        optionsInput.type = 'hidden';
        optionsInput.name = 'options';
        optionsInput.value = JSON.stringify(options);
        this.appendChild(optionsInput);
    }
});
</script>
@endpush

@if(session('success'))
    <script>
        alert('{{ session('success') }}');
        window.location.href = '{{ route('instructor.question-banks.show', $question->questionBank) }}';
    </script>
@endif
@endsection
