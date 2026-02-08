<?php $__env->startSection('title', 'إدارة أسئلة الاختبار'); ?>
<?php $__env->startSection('header', 'إدارة أسئلة الاختبار'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    [x-cloak] { display: none !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ activeTab: 'current', showAddModal: false, showCreateModal: false }">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2 flex-wrap">
            <a href="<?php echo e(route('instructor.exams.index')); ?>" class="hover:text-sky-600 transition-colors">الامتحانات</a>
            <span>/</span>
            <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>" class="hover:text-sky-600 transition-colors truncate max-w-[180px]"><?php echo e($exam->title); ?></a>
            <span>/</span>
            <span class="text-slate-700 font-medium">الأسئلة</span>
        </nav>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">إدارة أسئلة الاختبار</h1>
                <p class="text-sm text-slate-500 mt-0.5"><?php echo e($exam->title); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="showAddModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-database"></i>
                    <span>إضافة من البنك</span>
                </button>
                <button type="button" @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus-circle"></i>
                    <span>سؤال جديد</span>
                </button>
                <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
            <i class="fas fa-check-circle ml-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
            <i class="fas fa-exclamation-circle ml-2"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 text-sm">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle ml-2"></i>يرجى تصحيح الأخطاء التالية:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- التبويبات والمحتوى -->
    <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/50">
            <nav class="flex gap-1 p-2">
                <button @click="activeTab = 'current'"
                        :class="activeTab === 'current' ? 'bg-white text-sky-600 border-slate-200 shadow-sm' : 'text-slate-600 hover:text-slate-800 border-transparent'"
                        class="px-4 py-2.5 rounded-lg border text-sm font-semibold transition-colors flex items-center gap-2">
                    <i class="fas fa-list"></i>
                    الأسئلة الحالية (<?php echo e($exam->questions->count()); ?>)
                </button>
                <button @click="activeTab = 'bank'"
                        :class="activeTab === 'bank' ? 'bg-white text-sky-600 border-slate-200 shadow-sm' : 'text-slate-600 hover:text-slate-800 border-transparent'"
                        class="px-4 py-2.5 rounded-lg border text-sm font-semibold transition-colors flex items-center gap-2">
                    <i class="fas fa-database"></i>
                    بنك الأسئلة (<?php echo e($availableQuestions->count()); ?>)
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- تبويب الأسئلة الحالية -->
            <div x-show="activeTab === 'current'" x-transition>
                <?php if($exam->questions->count() > 0): ?>
                    <div class="space-y-3" id="questions-list">
                        <?php $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl p-4 bg-white border border-slate-200 hover:border-sky-300 hover:shadow-sm transition-all flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <span class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-sm font-bold shrink-0"><?php echo e($index + 1); ?></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-800 mb-1"><?php echo e($question->question); ?></p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                            <span><i class="fas fa-tag text-sky-500 ml-1"></i> <?php echo e($question->getTypeLabel()); ?></span>
                                            <span><i class="fas fa-star text-amber-500 ml-1"></i> <?php echo e($question->pivot->marks ?? 1); ?> نقطة</span>
                                            <span><?php echo e($question->getDifficultyLabel()); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <form action="<?php echo e(route('instructor.exams.questions.remove', [$exam, $question->id])); ?>" method="POST" onsubmit="return confirm('هل تريد حذف هذا السؤال من الاختبار؟');" class="shrink-0">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="حذف من الاختبار">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 rounded-xl border border-dashed border-slate-200 bg-slate-50/50">
                        <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-question-circle text-2xl text-sky-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد أسئلة</h3>
                        <p class="text-sm text-slate-500 max-w-sm mx-auto mb-4">ابدأ بإضافة أسئلة من البنك أو إنشاء سؤال جديد</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <button type="button" @click="showAddModal = true" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold">
                                <i class="fas fa-database ml-1"></i> إضافة من البنك
                            </button>
                            <button type="button" @click="showCreateModal = true" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold">
                                <i class="fas fa-plus ml-1"></i> سؤال جديد
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب بنك الأسئلة -->
            <div x-show="activeTab === 'bank'" x-transition style="display: none;">
                <?php if($availableQuestions->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $availableQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl p-4 border border-slate-200 hover:border-sky-300 bg-white hover:shadow-sm transition-all">
                                <p class="font-semibold text-slate-800 mb-2 line-clamp-2"><?php echo e(Str::limit($question->question, 100)); ?></p>
                                <div class="flex flex-wrap gap-2 text-xs text-slate-500 mb-3">
                                    <span><?php echo e($question->getTypeLabel()); ?></span>
                                    <span><?php echo e($question->getDifficultyLabel()); ?></span>
                                    <?php if($question->questionBank): ?>
                                        <span><i class="fas fa-database text-sky-500 ml-1"></i> <?php echo e($question->questionBank->title); ?></span>
                                    <?php endif; ?>
                                </div>
                                <form action="<?php echo e(route('instructor.exams.questions.add-from-bank', $exam)); ?>" method="POST" class="flex items-center gap-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="question_id" value="<?php echo e($question->id); ?>">
                                    <input type="number" name="marks" value="<?php echo e($question->points ?? 1); ?>" min="0.5" step="0.5"
                                           class="w-20 px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition-colors">
                                        <i class="fas fa-plus"></i> إضافة
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 rounded-xl border border-dashed border-slate-200 bg-slate-50/50">
                        <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-database text-2xl text-sky-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد أسئلة في البنك</h3>
                        <p class="text-sm text-slate-500 max-w-sm mx-auto">أنشئ بنك أسئلة وأضف أسئلة إليه أولاً</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal إنشاء سؤال جديد (داخل نفس x-data) -->
    <div x-show="showCreateModal" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);"
     @click.self="showCreateModal = false">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
         @click.stop>
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">إنشاء سؤال جديد</h3>
            <button type="button" @click="showCreateModal = false" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php if($questionBanks->isEmpty()): ?>
            <div class="p-6 text-center">
                <p class="text-slate-600 mb-4">يجب إنشاء بنك أسئلة أولاً قبل إضافة أسئلة جديدة.</p>
                <a href="<?php echo e(route('instructor.question-banks.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-database ml-1"></i> الذهاب لبنوك الأسئلة
                </a>
            </div>
        <?php else: ?>
        <form action="<?php echo e(route('instructor.exams.questions.create-new', $exam)); ?>" method="POST" class="p-6 overflow-y-auto flex-1">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">بنك الأسئلة <span class="text-red-500">*</span></label>
                    <select name="question_bank_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                        <option value="">اختر بنك الأسئلة</option>
                        <?php $__currentLoopData = $questionBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bank->id); ?>" <?php echo e(old('question_bank_id') == $bank->id ? 'selected' : ''); ?>><?php echo e($bank->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['question_bank_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع السؤال <span class="text-red-500">*</span></label>
                    <select name="type" id="question_type" required onchange="updateQuestionForm()" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                        <option value="">اختر النوع</option>
                        <option value="multiple_choice">اختيار متعدد</option>
                        <option value="true_false">صح أو خطأ</option>
                        <option value="fill_blank">املأ الفراغ</option>
                        <option value="short_answer">إجابة قصيرة</option>
                        <option value="essay">مقالي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نص السؤال <span class="text-red-500">*</span></label>
                    <textarea name="question" rows="3" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="اكتب السؤال..."></textarea>
                </div>
                <div id="options_field" style="display: none;">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الخيارات (سطر لكل خيار)</label>
                    <textarea name="options_text" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="الخيار 1&#10;الخيار 2&#10;..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الإجابة الصحيحة <span class="text-red-500">*</span></label>
                    <input type="text" name="correct_answer" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800" placeholder="الإجابة الصحيحة">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">شرح الإجابة</label>
                    <textarea name="explanation" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800" placeholder="اختياري..."></textarea>
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
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة في الاختبار <span class="text-red-500">*</span></label>
                        <input type="number" name="marks" value="1" min="0.5" step="0.5" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save ml-1"></i> إنشاء وإضافة
                </button>
                <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    </div>

    <!-- Modal إضافة من البنك (داخل نفس x-data) -->
    <div x-show="showAddModal" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);"
     @click.self="showAddModal = false">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">إضافة أسئلة من البنك</h3>
            <button type="button" @click="showAddModal = false" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <p class="text-sm text-slate-600 mb-4">اختر الأسئلة واضغط إضافة لإدراجها في الاختبار</p>
            <?php if($availableQuestions->isEmpty()): ?>
                <div class="text-center py-8 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                    <i class="fas fa-database text-2xl mb-2"></i>
                    <p class="font-semibold">لا توجد أسئلة في البنك</p>
                    <p class="text-sm mt-1">أنشئ بنك أسئلة وأضف أسئلة إليه، أو استخدم "سؤال جديد" لإنشاء سؤال وإضافته مباشرة.</p>
                    <a href="<?php echo e(route('instructor.question-banks.index')); ?>" class="inline-block mt-3 text-sky-600 hover:text-sky-700 font-medium">بنوك الأسئلة</a>
                </div>
            <?php else: ?>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                <?php $__currentLoopData = $availableQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <form action="<?php echo e(route('instructor.exams.questions.add-from-bank', $exam)); ?>" method="POST" class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-sky-200 hover:bg-sky-50/30 transition-colors">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="question_id" value="<?php echo e($question->id); ?>">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 text-sm truncate"><?php echo e(Str::limit($question->question, 70)); ?></p>
                            <div class="flex gap-2 text-xs text-slate-500 mt-0.5">
                                <span><?php echo e($question->getTypeLabel()); ?></span>
                                <span><?php echo e($question->getDifficultyLabel()); ?></span>
                            </div>
                        </div>
                        <input type="number" name="marks" value="<?php echo e($question->points ?? 1); ?>" min="0.5" step="0.5" class="w-16 px-2 py-1.5 border border-slate-200 rounded-lg text-sm text-center">
                        <button type="submit" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-plus"></i> إضافة
                        </button>
                    </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function updateQuestionForm() {
    var type = document.getElementById('question_type').value;
    var el = document.getElementById('options_field');
    el.style.display = type === 'multiple_choice' ? 'block' : 'none';
}
document.querySelectorAll('form[action*="create-new"]').forEach(function(form) {
    form.addEventListener('submit', function() {
        var optionsText = form.querySelector('textarea[name="options_text"]');
        if (optionsText && optionsText.value) {
            var options = optionsText.value.split('\n').filter(function(opt) { return opt.trim(); });
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'options';
            input.value = JSON.stringify(options);
            form.appendChild(input);
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/exams/manage-questions.blade.php ENDPATH**/ ?>