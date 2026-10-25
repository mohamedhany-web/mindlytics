// دالة تحميل نموذج البيانات التفاعلية
function loadPatternDataForm(type) {
    const content = document.getElementById('patternDataContent');
    let html = '';
    
    switch(type) {
        case 'code_challenge':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">وصف التحدي</label>
                        <textarea name="pattern_data[problem_description]" rows="4" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                                  placeholder="اكتب وصف التحدي البرمجي..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">مثال الإدخال</label>
                        <textarea name="pattern_data[input_example]" rows="2" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="مثال: [1, 2, 3, 4, 5]"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">مثال الإخراج المتوقع</label>
                        <textarea name="pattern_data[output_example]" rows="2" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="مثال: 15"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الكود المبدئي (اختياري)</label>
                        <textarea name="pattern_data[starter_code]" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="def solution(...):&#10;    # اكتب الكود هنا&#10;    pass"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">لغة البرمجة</label>
                        <select name="pattern_data[language]" 
                                class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="java">Java</option>
                            <option value="cpp">C++</option>
                        </select>
                    </div>
                </div>
            `;
            break;
            
        case 'interactive_quiz':
            html = `
                <div class="space-y-4">
                    <div id="questionsContainer">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <label class="block text-sm font-bold text-[#1C2C39]">الأسئلة</label>
                            <div class="flex gap-2">
                                <button type="button" onclick="typeof openModalAddFromBank === \'function\' && openModalAddFromBank()" 
                                        class="px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium">
                                    <i class="fas fa-database ml-1"></i> إضافة من البنك
                                </button>
                                <button type="button" onclick="typeof openModalAddQuestion === \'function\' && openModalAddQuestion()" 
                                        class="px-3 py-1.5 bg-[#2CA9BD] hover:bg-[#258fa3] text-white rounded-lg text-sm font-medium">
                                    <i class="fas fa-plus ml-1"></i> إضافة سؤال
                                </button>
                            </div>
                        </div>
                        <div id="questionsList" class="space-y-4">
                            <!-- سيتم إضافة الأسئلة هنا -->
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'code_playground':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الكود المبدئي</label>
                        <textarea name="pattern_data[starter_code]" rows="10" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="# اكتب الكود هنا&#10;print('Hello, World!')"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">لغة البرمجة</label>
                        <select name="pattern_data[language]" 
                                class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="java">Java</option>
                            <option value="cpp">C++</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الموضوع (اختياري)</label>
                        <input type="text" name="pattern_data[topic]" 
                               class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                               placeholder="مثال: Loops, Functions, etc.">
                    </div>
                </div>
            `;
            break;
            
        case 'debugging_exercise':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الكود الذي يحتوي على أخطاء</label>
                        <textarea name="pattern_data[buggy_code]" rows="10" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="# كود يحتوي على أخطاء&#10;def calculate_sum(numbers):&#10;    total = 0&#10;    for num in numbers:&#10;        total += num&#10;    return total"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">وصف المشكلة</label>
                        <textarea name="pattern_data[problem_description]" rows="3" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                                  placeholder="اكتب وصف المشكلة والأخطاء المتوقعة..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">لغة البرمجة</label>
                        <select name="pattern_data[language]" 
                                class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="java">Java</option>
                            <option value="cpp">C++</option>
                        </select>
                    </div>
                </div>
            `;
            break;
            
        case 'project_based':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">وصف المشروع</label>
                        <textarea name="pattern_data[project_description]" rows="5" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                                  placeholder="اكتب وصفاً مفصلاً للمشروع..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">المتطلبات</label>
                        <textarea name="pattern_data[requirements]" rows="4" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                                  placeholder="اكتب متطلبات المشروع (سطر واحد لكل متطلب)..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">اكتب كل متطلب في سطر منفصل</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">ملفات المشروع (اختياري)</label>
                        <textarea name="pattern_data[project_files]" rows="3" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="main.py&#10;utils.py&#10;README.md"></textarea>
                        <p class="text-xs text-gray-500 mt-1">اكتب اسم كل ملف في سطر منفصل</p>
                    </div>
                </div>
            `;
            break;
            
        case 'code_snippet':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">مثال الكود</label>
                        <textarea name="pattern_data[code_example]" rows="10" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD] font-mono text-sm"
                                  placeholder="# مثال الكود&#10;def greet(name):&#10;    return f'Hello, {name}!'"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الشرح</label>
                        <textarea name="pattern_data[explanation]" rows="4" 
                                  class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                                  placeholder="اكتب شرحاً تفصيلياً للكود..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">لغة البرمجة</label>
                        <select name="pattern_data[language]" 
                                class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]">
                            <option value="python">Python</option>
                            <option value="javascript">JavaScript</option>
                            <option value="java">Java</option>
                            <option value="cpp">C++</option>
                        </select>
                    </div>
                </div>
            `;
            break;
            
        case 'live_coding':
            html = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">رابط الفيديو</label>
                        <input type="url" name="pattern_data[video_url]" 
                               class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                               placeholder="https://youtube.com/watch?v=... أو رابط Vimeo">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">منصة الفيديو</label>
                        <select name="pattern_data[video_platform]" 
                                class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]">
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="direct">رابط مباشر</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1C2C39] mb-2">الموضوع</label>
                        <input type="text" name="pattern_data[topic]" 
                               class="w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl focus:border-[#2CA9BD]"
                               placeholder="مثال: Building a REST API">
                    </div>
                </div>
            `;
            break;
            
        case 'flashcards':
            html = `
                <div class="space-y-4">
                    <div id="flashcardsContainer">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-bold text-[#1C2C39]">البطاقات التعليمية</label>
                            <button type="button" onclick="addFlashcard()" 
                                    class="px-3 py-1 bg-[#2CA9BD] text-white rounded-lg text-sm">
                                <i class="fas fa-plus ml-1"></i> إضافة بطاقة
                            </button>
                        </div>
                        <div id="flashcardsList" class="space-y-4">
                            <!-- سيتم إضافة البطاقات هنا -->
                        </div>
                    </div>
                </div>
            `;
            break;
            
        default:
            html = '<p class="text-gray-500">لا توجد بيانات تفاعلية مطلوبة لهذا النوع من الأنماط</p>';
    }
    
    content.innerHTML = html;
    
    // إضافة أول سؤال/بطاقة تلقائياً للأنواع التي تحتاجها
    if (type === 'interactive_quiz') {
        addQuestion();
    } else if (type === 'flashcards') {
        addFlashcard();
    }
}

let questionCounter = 0;
function addQuestion() {
    questionCounter++;
    const container = document.getElementById('questionsList');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'border-2 border-gray-200 rounded-xl p-4';
    questionDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold text-[#1C2C39]">سؤال ${questionCounter}</h4>
            <button type="button" onclick="this.parentElement.parentElement.remove()" 
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-[#1C2C39] mb-1">نص السؤال *</label>
                <textarea name="pattern_data[questions][${questionCounter}][question]" rows="2" 
                          class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]"
                          required placeholder="اكتب نص السؤال هنا..."></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-[#1C2C39] mb-1">نوع السؤال</label>
                <select name="pattern_data[questions][${questionCounter}][type]" 
                        class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]"
                        onchange="toggleQuestionType(this, ${questionCounter})">
                    <option value="multiple_choice">اختيار متعدد</option>
                    <option value="true_false">صحيح/خطأ</option>
                </select>
            </div>
            <div id="optionsContainer${questionCounter}">
                <label class="block text-xs font-bold text-[#1C2C39] mb-1">الخيارات (اختر الإجابة الصحيحة) *</label>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="pattern_data[questions][${questionCounter}][correct_answer]" value="0" required>
                        <input type="text" name="pattern_data[questions][${questionCounter}][options][0]" 
                               class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                               placeholder="الخيار الأول" required>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="radio" name="pattern_data[questions][${questionCounter}][correct_answer]" value="1" required>
                        <input type="text" name="pattern_data[questions][${questionCounter}][options][1]" 
                               class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                               placeholder="الخيار الثاني" required>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="radio" name="pattern_data[questions][${questionCounter}][correct_answer]" value="2" required>
                        <input type="text" name="pattern_data[questions][${questionCounter}][options][2]" 
                               class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                               placeholder="الخيار الثالث" required>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="radio" name="pattern_data[questions][${questionCounter}][correct_answer]" value="3" required>
                        <input type="text" name="pattern_data[questions][${questionCounter}][options][3]" 
                               class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                               placeholder="الخيار الرابع" required>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.appendChild(questionDiv);
}

function addQuestionFromBank(jsonOrObj) {
    var data = typeof jsonOrObj === 'string' ? (function(){ try { return JSON.parse(jsonOrObj); } catch(e) { return null; } })() : jsonOrObj;
    if (!data || !data.question) return;
    questionCounter++;
    var c = questionCounter;
    var type = data.type === 'true_false' ? 'true_false' : 'multiple_choice';
    var opts = data.options || [];
    var correct = data.correct_answer != null ? String(data.correct_answer) : '0';
    var optionsHtml = type === 'true_false' ? ''
        : [0,1,2,3].map(function(i) {
            var val = opts[i] || '';
            var checked = correct === String(i) ? ' checked' : '';
            return '<div class="flex items-center gap-2"><input type="radio" name="pattern_data[questions][' + c + '][correct_answer]" value="' + i + '"' + checked + ' required><input type="text" name="pattern_data[questions][' + c + '][options][' + i + ']" class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" placeholder="الخيار ' + (i+1) + '" value="' + escapeHtmlAttr(val) + '" required></div>';
        }).join('');
    var optionsContainerHtml = type === 'true_false'
        ? '<label class="block text-xs font-bold text-[#1C2C39] mb-1">الإجابة الصحيحة</label><select name="pattern_data[questions][' + c + '][correct_answer]" class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]" required><option value="true"' + (correct === 'true' ? ' selected' : '') + '>صحيح</option><option value="false"' + (correct === 'false' ? ' selected' : '') + '>خطأ</option></select>'
        : '<label class="block text-xs font-bold text-[#1C2C39] mb-1">الخيارات (اختر الإجابة الصحيحة) *</label><div class="space-y-2">' + optionsHtml + '</div>';
    var questionDiv = document.createElement('div');
    questionDiv.className = 'border-2 border-gray-200 rounded-xl p-4';
    questionDiv.innerHTML = '<div class="flex items-center justify-between mb-3"><h4 class="font-bold text-[#1C2C39]">سؤال ' + c + '</h4><button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></div><div class="space-y-3"><div><label class="block text-xs font-bold text-[#1C2C39] mb-1">نص السؤال *</label><textarea name="pattern_data[questions][' + c + '][question]" rows="2" class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]" required placeholder="اكتب نص السؤال هنا...">' + escapeHtmlAttr(data.question) + '</textarea></div><div><label class="block text-xs font-bold text-[#1C2C39] mb-1">نوع السؤال</label><select name="pattern_data[questions][' + c + '][type]" class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]" onchange="toggleQuestionType(this, ' + c + ')"><option value="multiple_choice"' + (type === 'multiple_choice' ? ' selected' : '') + '>اختيار متعدد</option><option value="true_false"' + (type === 'true_false' ? ' selected' : '') + '>صحيح/خطأ</option></select></div><div id="optionsContainer' + c + '">' + optionsContainerHtml + '</div></div>';
    var container = document.getElementById('questionsList');
    if (container) container.appendChild(questionDiv);
}
function escapeHtmlAttr(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function toggleQuestionType(select, questionId) {
    const container = document.getElementById('optionsContainer' + questionId);
    if (select.value === 'true_false') {
        container.innerHTML = `
            <label class="block text-xs font-bold text-[#1C2C39] mb-1">الإجابة الصحيحة</label>
            <select name="pattern_data[questions][${questionId}][correct_answer]" 
                    class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]"
                    required>
                <option value="true">صحيح</option>
                <option value="false">خطأ</option>
            </select>
        `;
    } else {
        container.innerHTML = `
            <label class="block text-xs font-bold text-[#1C2C39] mb-1">الخيارات</label>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <input type="radio" name="pattern_data[questions][${questionId}][correct_answer]" value="0" required>
                    <input type="text" name="pattern_data[questions][${questionId}][options][0]" 
                           class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                           placeholder="الخيار الأول" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="radio" name="pattern_data[questions][${questionId}][correct_answer]" value="1" required>
                    <input type="text" name="pattern_data[questions][${questionId}][options][1]" 
                           class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                           placeholder="الخيار الثاني" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="radio" name="pattern_data[questions][${questionId}][correct_answer]" value="2" required>
                    <input type="text" name="pattern_data[questions][${questionId}][options][2]" 
                           class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                           placeholder="الخيار الثالث" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="radio" name="pattern_data[questions][${questionId}][correct_answer]" value="3" required>
                    <input type="text" name="pattern_data[questions][${questionId}][options][3]" 
                           class="flex-1 px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg" 
                           placeholder="الخيار الرابع" required>
                </div>
            </div>
        `;
    }
}

let flashcardCounter = 0;
function addFlashcard() {
    flashcardCounter++;
    const container = document.getElementById('flashcardsList');
    const cardDiv = document.createElement('div');
    cardDiv.className = 'border-2 border-gray-200 rounded-xl p-4';
    cardDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold text-[#1C2C39]">بطاقة ${flashcardCounter}</h4>
            <button type="button" onclick="this.parentElement.parentElement.remove()" 
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-[#1C2C39] mb-1">الوجه الأمامي (السؤال/المصطلح)</label>
                <textarea name="pattern_data[flashcards][${flashcardCounter}][front]" rows="3" 
                          class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]"
                          required></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-[#1C2C39] mb-1">الوجه الخلفي (الإجابة/التعريف)</label>
                <textarea name="pattern_data[flashcards][${flashcardCounter}][back]" rows="3" 
                          class="w-full px-3 py-2 border-2 border-[#2CA9BD]/20 rounded-lg focus:border-[#2CA9BD]"
                          required></textarea>
            </div>
        </div>
    `;
    container.appendChild(cardDiv);
}
