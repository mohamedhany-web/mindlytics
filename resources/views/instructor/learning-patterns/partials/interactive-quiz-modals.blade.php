{{-- حاوية مودالات الاختبار التفاعلي: إضافة سؤال + إضافة من البنك --}}
@php
    $questionBanks = $questionBanks ?? collect();
    $banksJson = $questionBanks->map(function ($bank) {
        return [
            'id' => $bank->id,
            'title' => $bank->title,
            'questions' => $bank->questions->values()->all(),
        ];
    })->values()->toJson();
@endphp

<!-- مودال إضافة سؤال جديد - عرض أكبر لاستيعاب المحتوى -->
<div id="modalAddQuestion" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6 bg-black/50 backdrop-blur-sm overflow-y-auto" style="display: none;">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl my-auto flex flex-col max-h-[90vh] min-h-[420px] min-w-0">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0 rounded-t-2xl">
            <h3 class="text-xl font-bold text-slate-800">إضافة سؤال جديد</h3>
            <button type="button" onclick="closeModalAddQuestion()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" aria-label="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="formAddQuestion" class="p-6 overflow-y-auto flex-1 min-h-0 space-y-5" onsubmit="return submitNewQuestionForm(event);">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">نص السؤال *</label>
                <textarea name="new_question_text" rows="4" required class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-200 focus:border-sky-500" placeholder="اكتب نص السؤال..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">نوع السؤال</label>
                <select name="new_question_type" class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-200 focus:border-sky-500" onchange="toggleNewQuestionOptions(this.value)">
                    <option value="multiple_choice">اختيار متعدد</option>
                    <option value="true_false">صحيح / خطأ</option>
                </select>
            </div>
            <div id="newQuestionOptionsWrap">
                <label class="block text-sm font-bold text-slate-700 mb-2">الخيارات (حدد الإجابة الصحيحة)</label>
                <div class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-3">
                            <input type="radio" name="new_correct_answer" value="{{ $i }}" {{ $i === 0 ? 'required' : '' }} class="shrink-0 w-5 h-5">
                            <input type="text" name="new_options[]" class="flex-1 min-w-0 px-4 py-3 text-base border border-slate-300 rounded-xl" placeholder="الخيار {{ $i + 1 }}">
                        </div>
                    @endfor
                </div>
            </div>
            <div id="newQuestionTrueFalseWrap" class="hidden">
                <label class="block text-sm font-bold text-slate-700 mb-2">الإجابة الصحيحة</label>
                <select name="new_correct_answer_tf" class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-200 focus:border-sky-500">
                    <option value="true">صحيح</option>
                    <option value="false">خطأ</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="flex-1 min-w-[160px] px-5 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors text-base">
                    <i class="fas fa-plus ml-1"></i> إضافة السؤال
                </button>
                <button type="button" onclick="closeModalAddQuestion()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors text-base">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- مودال إضافة من البنك -->
<div id="modalAddFromBank" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto" style="display: none;">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl max-h-[90vh] my-auto flex flex-col min-w-0 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <h3 class="text-lg font-bold text-slate-800">إضافة أسئلة من البنك</h3>
            <button type="button" onclick="closeModalAddFromBank()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5 overflow-hidden flex flex-col flex-1 min-h-0">
            <p class="text-sm text-slate-600 mb-3">اختر بنك الأسئلة ثم اضغط «إضافة» بجانب كل سؤال لإدراجه في الاختبار.</p>
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">بنك الأسئلة</label>
                <select id="bankSelect" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-200 focus:border-sky-500" onchange="filterBankQuestions()">
                    <option value="">-- اختر البنك --</option>
                    @foreach($questionBanks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->title }} ({{ $bank->questions->count() }})</option>
                    @endforeach
                </select>
            </div>
            <div id="bankQuestionsList" class="space-y-2 overflow-y-auto flex-1 min-h-0 border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                <p class="text-sm text-slate-500 text-center py-6">اختر بنكاً من القائمة أعلاه</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    window.patternQuestionBanks = @json($banksJson);
})();
function toggleNewQuestionOptions(type) {
    var isTf = type === 'true_false';
    document.getElementById('newQuestionOptionsWrap').classList.toggle('hidden', isTf);
    document.getElementById('newQuestionTrueFalseWrap').classList.toggle('hidden', !isTf);
    document.querySelectorAll('[name=new_correct_answer]').forEach(function(r) { r.required = !isTf; });
    var sel = document.querySelector('[name=new_correct_answer_tf]');
    if (sel) sel.required = isTf;
}
function openModalAddQuestion() {
    var m = document.getElementById('modalAddQuestion');
    if (m) { m.style.display = 'flex'; m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
}
function closeModalAddQuestion() {
    var m = document.getElementById('modalAddQuestion');
    if (m) { m.style.display = 'none'; document.getElementById('formAddQuestion')?.reset(); document.body.style.overflow = ''; }
}
function openModalAddFromBank() {
    var m = document.getElementById('modalAddFromBank');
    if (m) { m.style.display = 'flex'; filterBankQuestions(); document.body.style.overflow = 'hidden'; }
}
function closeModalAddFromBank() {
    var m = document.getElementById('modalAddFromBank');
    if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
}
function filterBankQuestions() {
    var select = document.getElementById('bankSelect');
    var list = document.getElementById('bankQuestionsList');
    if (!select || !list) return;
    var bankId = select.value;
    var banks = window.patternQuestionBanks || [];
    var bank = banks.find(function(b) { return String(b.id) === String(bankId); });
    if (!bank || !bank.questions || !bank.questions.length) {
        list.innerHTML = '<p class="text-sm text-slate-500 text-center py-6">لا توجد أسئلة في هذا البنك أو اختر بنكاً آخر.</p>';
        return;
    }
    var html = '';
    bank.questions.forEach(function(q) {
        var typeLabel = q.type === 'true_false' ? 'صحيح/خطأ' : 'اختيار متعدد';
        var dataJson = JSON.stringify(q).replace(/'/g, "\\'");
        html += '<div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 hover:border-sky-200" data-question=\'' + dataJson + '\'>';
        html += '<div class="flex-1 min-w-0"><p class="font-semibold text-slate-800 text-sm">' + escapeHtml((q.question || '').substring(0, 120)) + (q.question && q.question.length > 120 ? '...' : '') + '</p><span class="text-xs text-slate-500">' + typeLabel + '</span></div>';
        html += '<button type="button" onclick="var el=this.closest(\'[data-question]\'); if(el){ addQuestionFromBank(el.getAttribute(\'data-question\')); }" class="shrink-0 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold"><i class="fas fa-plus ml-1"></i> إضافة</button>';
        html += '</div>';
    });
    list.innerHTML = html;
}
function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
function submitNewQuestionForm(e) {
    e.preventDefault();
    var form = document.getElementById('formAddQuestion');
    if (!form) return false;
    var type = form.querySelector('[name=new_question_type]').value;
    var question = form.querySelector('[name=new_question_text]').value.trim();
    if (!question) return false;
    var correct = type === 'true_false'
        ? form.querySelector('[name=new_correct_answer_tf]').value
        : form.querySelector('[name=new_correct_answer]:checked');
    if (type !== 'true_false' && !correct) return false;
    var correctVal = type === 'true_false' ? correct : (correct ? correct.value : '0');
    var options = [];
    if (type === 'multiple_choice') {
        form.querySelectorAll('[name="new_options[]"]').forEach(function(inp) { options.push(inp.value.trim()); });
    }
    var data = { question: question, type: type, correct_answer: correctVal, options: options };
    if (typeof addQuestionFromBank === 'function') addQuestionFromBank(data);
    closeModalAddQuestion();
    form.reset();
    return false;
}
</script>
