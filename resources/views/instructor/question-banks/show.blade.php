@extends('layouts.app')

@section('title', 'بنك الأسئلة: ' . $questionBank->title . ' - Mindlytics')
@section('header', 'بنك الأسئلة: ' . $questionBank->title)

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6" x-data="{ showCreateModal: false }">
    <!-- هيدر الصفحة (عرض الصفحة كاملاً) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.question-banks.index') }}" class="hover:text-sky-600 transition-colors">بنوك الأسئلة</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $questionBank->title }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-database text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ $questionBank->title }}</h1>
                    <p class="text-sm text-slate-600 mt-0.5">{{ $questionBank->description ?? 'بنك أسئلة' }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>إضافة سؤال</span>
                </button>
                <a href="{{ route('instructor.question-banks.edit', $questionBank) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    <span>تعديل البنك</span>
                </a>
                <a href="{{ route('instructor.question-banks.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium mb-6">
            <i class="fas fa-check-circle ml-2"></i>{{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 text-sm mb-6">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- إحصائيات -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-2xl font-bold text-sky-600">{{ $questionBank->questions->count() }}</div>
            <div class="text-sm font-medium text-slate-600 mt-0.5">إجمالي الأسئلة</div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-2xl font-bold text-green-600">{{ $questionBank->questions->where('difficulty_level', 'easy')->count() }}</div>
            <div class="text-sm font-medium text-slate-600 mt-0.5">سهل</div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-2xl font-bold text-amber-600">{{ $questionBank->questions->where('difficulty_level', 'medium')->count() }}</div>
            <div class="text-sm font-medium text-slate-600 mt-0.5">متوسط</div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="text-2xl font-bold text-red-600">{{ $questionBank->questions->where('difficulty_level', 'hard')->count() }}</div>
            <div class="text-sm font-medium text-slate-600 mt-0.5">صعب</div>
        </div>
    </div>

    <!-- قائمة الأسئلة -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-lg font-bold text-slate-800">الأسئلة ({{ $questionBank->questions->count() }})</h2>
            <button type="button" @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة سؤال
            </button>
        </div>

        @if($questionBank->questions->count() > 0)
            <div class="divide-y divide-slate-200">
                @foreach($questionBank->questions as $index => $question)
                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                @php
                                    $typeIcons = [
                                        'multiple_choice' => ['fas fa-list-ul', 'text-sky-600', 'bg-sky-100'],
                                        'true_false' => ['fas fa-check-circle', 'text-green-600', 'bg-green-100'],
                                        'fill_blank' => ['fas fa-edit', 'text-violet-600', 'bg-violet-100'],
                                        'short_answer' => ['fas fa-comment', 'text-amber-600', 'bg-amber-100'],
                                        'essay' => ['fas fa-file-alt', 'text-indigo-600', 'bg-indigo-100'],
                                    ];
                                    $icon = $typeIcons[$question->type] ?? ['fas fa-question', 'text-slate-600', 'bg-slate-100'];
                                @endphp
                                <div class="flex gap-3 mb-2">
                                    <span class="w-8 h-8 rounded-lg {{ $icon[2] }} {{ $icon[1] }} flex items-center justify-center text-sm font-bold shrink-0">{{ $index + 1 }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-800">{{ Str::limit($question->question, 200) }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-500">
                                            <span class="px-2 py-0.5 rounded {{ $icon[2] }} {{ $icon[1] }}">{{ $question->getTypeLabel() }}</span>
                                            <span>{{ $question->points }} نقطة</span>
                                            <span>{{ $question->getDifficultyLabel() }}</span>
                                            @if($question->category)
                                                <span>{{ $question->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($question->type == 'multiple_choice' && $question->options && is_array($question->options))
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($question->options as $opt)
                                            <span class="px-2 py-1 rounded-lg text-sm bg-slate-100 text-slate-700 {{ (!is_array($question->correct_answer) && $opt == $question->correct_answer) || (is_array($question->correct_answer) && in_array($opt, $question->correct_answer)) ? 'ring-2 ring-green-500 bg-green-50 text-green-800' : '' }}">
                                                {{ $opt }} @if((!is_array($question->correct_answer) && $opt == $question->correct_answer) || (is_array($question->correct_answer) && in_array($opt, $question->correct_answer))) <i class="fas fa-check text-green-600 mr-1"></i> @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif(in_array($question->type, ['true_false', 'short_answer', 'essay']))
                                    <p class="mt-2 text-sm text-slate-600">الإجابة: {{ is_array($question->correct_answer) ? ($question->correct_answer[0] ?? '—') : $question->correct_answer }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('instructor.questions.edit', $question) }}" class="p-2 rounded-lg text-sky-600 hover:bg-sky-50 transition-colors" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('instructor.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا السؤال؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-sky-100 text-sky-500 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question-circle text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد أسئلة بعد</h3>
                <p class="text-sm text-slate-600 mb-4">أضف أول سؤال لهذا البنك</p>
                <button type="button" @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة سؤال
                </button>
            </div>
        @endif
    </div>

    <!-- Modal إضافة سؤال جديد (داخل نفس x-data) -->
    <div x-show="showCreateModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);"
         @click.self="showCreateModal = false">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
                <h3 class="text-lg font-bold text-slate-800">إضافة سؤال جديد</h3>
                <button type="button" @click="showCreateModal = false" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('instructor.question-banks.questions.store', $questionBank) }}" method="POST" class="p-6 overflow-y-auto flex-1" id="add-question-form">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">نوع السؤال <span class="text-red-500">*</span></label>
                        <select name="type" id="question_type" required onchange="updateQuestionForm()" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <option value="">اختر نوع السؤال</option>
                            <option value="multiple_choice">اختيار متعدد</option>
                            <option value="true_false">صح أو خطأ</option>
                            <option value="fill_blank">املأ الفراغ</option>
                            <option value="short_answer">إجابة قصيرة</option>
                            <option value="essay">مقالي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">نص السؤال <span class="text-red-500">*</span></label>
                        <textarea name="question" rows="3" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="اكتب نص السؤال..."></textarea>
                    </div>
                    <div id="options_field" style="display: none;">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الخيارات (سطر لكل خيار)</label>
                        <textarea name="options_text" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="الخيار 1&#10;الخيار 2&#10;..."></textarea>
                    </div>
                    <div id="correct_answer_wrap">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الإجابة الصحيحة <span class="text-red-500">*</span></label>
                        <div id="correct_answer_multiple_choice" style="display: none;">
                            <select name="correct_answer" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                                <option value="">اختر الإجابة (أدخل الخيارات أولاً)</option>
                            </select>
                        </div>
                        <div id="correct_answer_true_false" style="display: none;">
                            <select name="correct_answer" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                                <option value="">اختر</option>
                                <option value="صح">صح</option>
                                <option value="خطأ">خطأ</option>
                            </select>
                        </div>
                        <div id="correct_answer_fill_blank" style="display: none;">
                            <textarea name="correct_answer" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="إجابة في كل سطر"></textarea>
                        </div>
                        <div id="correct_answer_short_answer" style="display: none;">
                            <input type="text" name="correct_answer" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="الإجابة الصحيحة">
                        </div>
                        <div id="correct_answer_essay" style="display: none;">
                            <textarea name="correct_answer" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="نموذج الإجابة..."></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">شرح الإجابة</label>
                        <textarea name="explanation" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">النقاط <span class="text-red-500">*</span></label>
                            <input type="number" name="points" value="1" min="0.5" step="0.5" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">الصعوبة <span class="text-red-500">*</span></label>
                            <select name="difficulty_level" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                                <option value="easy">سهل</option>
                                <option value="medium" selected>متوسط</option>
                                <option value="hard">صعب</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">التصنيف</label>
                            <select name="category_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                                <option value="">بدون تصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-700">سؤال نشط</span>
                    </label>
                </div>
                <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors">
                        <i class="fas fa-save ml-1"></i> إضافة السؤال
                    </button>
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateQuestionForm() {
    var type = document.getElementById('question_type').value;
    var optionsField = document.getElementById('options_field');
    var ids = ['correct_answer_multiple_choice', 'correct_answer_true_false', 'correct_answer_fill_blank', 'correct_answer_short_answer', 'correct_answer_essay'];
    ids.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            var inp = el.querySelector('[name="correct_answer"]');
            if (inp) inp.removeAttribute('required');
        }
    });
    if (optionsField) optionsField.style.display = 'none';
    if (type === 'multiple_choice') {
        if (optionsField) optionsField.style.display = 'block';
        var f = document.getElementById('correct_answer_multiple_choice');
        if (f) { f.style.display = 'block'; var s = f.querySelector('select[name="correct_answer"]'); if (s) s.setAttribute('required', 'required'); }
    } else if (type === 'true_false') {
        var f = document.getElementById('correct_answer_true_false');
        if (f) { f.style.display = 'block'; var s = f.querySelector('select[name="correct_answer"]'); if (s) s.setAttribute('required', 'required'); }
    } else if (type === 'fill_blank') {
        var f = document.getElementById('correct_answer_fill_blank');
        if (f) { f.style.display = 'block'; var t = f.querySelector('textarea[name="correct_answer"]'); if (t) t.setAttribute('required', 'required'); }
    } else if (type === 'short_answer') {
        var f = document.getElementById('correct_answer_short_answer');
        if (f) { f.style.display = 'block'; var i = f.querySelector('input[name="correct_answer"]'); if (i) i.setAttribute('required', 'required'); }
    } else if (type === 'essay') {
        var f = document.getElementById('correct_answer_essay');
        if (f) { f.style.display = 'block'; var t = f.querySelector('textarea[name="correct_answer"]'); if (t) t.setAttribute('required', 'required'); }
    }
}
function updateMultipleChoiceOptions() {
    var optionsText = document.querySelector('#add-question-form textarea[name="options_text"]');
    var select = document.querySelector('#correct_answer_multiple_choice select[name="correct_answer"]');
    if (!optionsText || !select) return;
    var options = optionsText.value.split('\n').filter(function(o) { return o.trim(); });
    var current = select.value;
    while (select.options.length > 1) select.remove(1);
    options.forEach(function(opt) {
        var o = document.createElement('option');
        o.value = o.textContent = opt.trim();
        select.appendChild(o);
    });
    if (current && Array.from(select.options).some(function(opt) { return opt.value === current; })) select.value = current;
}
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('add-question-form');
    if (form) {
        var optionsText = form.querySelector('textarea[name="options_text"]');
        if (optionsText) optionsText.addEventListener('input', updateMultipleChoiceOptions);
        form.addEventListener('submit', function() {
            var type = document.getElementById('question_type').value;
            if (type === 'multiple_choice') {
                var ids = ['correct_answer_true_false', 'correct_answer_fill_blank', 'correct_answer_short_answer', 'correct_answer_essay'];
                ids.forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el) {
                        var inp = el.querySelector('[name="correct_answer"]');
                        if (inp) inp.disabled = true;
                    }
                });
            } else {
                var mc = document.getElementById('correct_answer_multiple_choice');
                if (mc) { var s = mc.querySelector('select[name="correct_answer"]'); if (s) s.disabled = true; }
            }
        });
    }
});
</script>
@endpush
@endsection
