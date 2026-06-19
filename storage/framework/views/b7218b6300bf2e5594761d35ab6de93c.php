

<?php $__env->startSection('title', 'إضافة سؤال جديد'); ?>
<?php $__env->startSection('header', 'إضافة سؤال جديد لبنك الأسئلة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.question-bank.index')); ?>" class="hover:text-primary-600">بنك الأسئلة</a>
                <span class="mx-2">/</span>
                <span>إضافة سؤال جديد</span>
            </nav>
        </div>
        <a href="<?php echo e(route('admin.question-bank.index')); ?>" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <!-- نموذج إضافة السؤال -->
    <form action="<?php echo e(route('admin.question-bank.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        
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
                                      placeholder="اكتب نص السؤال هنا..."><?php echo e(old('question')); ?></textarea>
                            <?php $__errorArgs = ['question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                    <?php $__currentLoopData = \App\Models\Question::getQuestionTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    مستوى الصعوبة <span class="text-red-500">*</span>
                                </label>
                                <select name="difficulty_level" id="difficulty_level" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">اختر مستوى الصعوبة</option>
                                    <?php $__currentLoopData = \App\Models\Question::getDifficultyLevels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('difficulty_level') == $key ? 'selected' : ''); ?>><?php echo e($level); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['difficulty_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- الدرجة والوقت -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                                    درجة السؤال <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" id="points" step="0.5" min="0.5" max="100" 
                                       value="<?php echo e(old('points', 1)); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="1.0">
                                <?php $__errorArgs = ['points'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="time_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                    الوقت المحدد (ثانية)
                                </label>
                                <input type="number" name="time_limit" id="time_limit" min="10" max="600" 
                                       value="<?php echo e(old('time_limit')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="اتركه فارغاً لاستخدام وقت الامتحان العام">
                                <?php $__errorArgs = ['time_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- خيارات السؤال (تظهر حسب النوع) -->
                <div id="question-options" class="bg-white shadow-sm rounded-lg border border-gray-200" style="display: none;">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">خيارات السؤال</h3>
                    </div>
                    <div class="p-6">
                        <!-- اختيار متعدد -->
                        <div id="multiple-choice-options" style="display: none;">
                            <div class="space-y-4">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_option" value="<?php echo e($i); ?>" id="correct_<?php echo e($i); ?>"
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <label for="option_<?php echo e($i); ?>" class="text-sm font-medium text-gray-700">الخيار <?php echo e($i); ?>:</label>
                                        <input type="text" name="option_<?php echo e($i); ?>" id="option_<?php echo e($i); ?>" 
                                               value="<?php echo e(old('option_' . $i)); ?>"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="اكتب الخيار <?php echo e($i); ?> <?php echo e($i <= 2 ? '(مطلوب)' : '(اختياري)'); ?>" 
                                               <?php echo e($i <= 2 ? 'required' : ''); ?>>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- صح أو خطأ -->
                        <div id="true-false-options" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">الإجابة الصحيحة:</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="صح" 
                                               <?php echo e(old('true_false_answer') == 'صح' ? 'checked' : ''); ?>

                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <span class="mr-2 text-sm font-medium text-gray-700">صح</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="خطأ" 
                                               <?php echo e(old('true_false_answer') == 'خطأ' ? 'checked' : ''); ?>

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
                                <input type="text" name="correct_answers" id="correct_answers" 
                                       value="<?php echo e(old('correct_answers')); ?>"
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
                                <textarea name="model_answer" id="model_answer" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="اكتب الإجابة النموذجية للمساعدة في التصحيح..."><?php echo e(old('model_answer')); ?></textarea>
                                <p class="mt-1 text-sm text-gray-500">ستساعد في التصحيح اليدوي</p>
                            </div>
                        </div>

                        <!-- مطابقة -->
                        <div id="matching-options" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="left_items" class="block text-sm font-medium text-gray-700 mb-2">
                                        العناصر اليسرى (كل عنصر في سطر)
                                    </label>
                                    <textarea name="left_items" id="left_items" rows="5"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="العنصر الأول&#10;العنصر الثاني&#10;العنصر الثالث"><?php echo e(old('left_items')); ?></textarea>
                                </div>
                                <div>
                                    <label for="right_items" class="block text-sm font-medium text-gray-700 mb-2">
                                        العناصر اليمنى (كل عنصر في سطر)
                                    </label>
                                    <textarea name="right_items" id="right_items" rows="5"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="المطابق الأول&#10;المطابق الثاني&#10;المطابق الثالث"><?php echo e(old('right_items')); ?></textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    المطابقات الصحيحة
                                </label>
                                <div id="matching-pairs" class="space-y-2">
                                    <!-- سيتم إنشاؤها ديناميكياً بـ JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- ترتيب -->
                        <div id="ordering-options" style="display: none;">
                            <div>
                                <label for="ordering_items" class="block text-sm font-medium text-gray-700 mb-2">
                                    العناصر للترتيب (كل عنصر في سطر)
                                </label>
                                <textarea name="ordering_items" id="ordering_items" rows="5"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="العنصر الأول&#10;العنصر الثاني&#10;العنصر الثالث&#10;العنصر الرابع"><?php echo e(old('ordering_items')); ?></textarea>
                                <p class="mt-1 text-sm text-gray-500">اكتب العناصر بالترتيب الصحيح</p>
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
                        <!-- رفع صورة -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                رفع صورة
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
                                   value="<?php echo e(old('image_url')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <!-- رابط صوتي -->
                        <div>
                            <label for="audio_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط ملف صوتي
                            </label>
                            <input type="url" name="audio_url" id="audio_url" 
                                   value="<?php echo e(old('audio_url')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="https://example.com/audio.mp3">
                        </div>

                        <!-- رابط فيديو -->
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                                رابط فيديو
                            </label>
                            <input type="url" name="video_url" id="video_url" 
                                   value="<?php echo e(old('video_url')); ?>"
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
                                  placeholder="اكتب شرحاً مفصلاً للإجابة الصحيحة (اختياري)..."><?php echo e(old('explanation')); ?></textarea>
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
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" 
                                            <?php echo e((old('category_id', $selectedCategory) == $category->id) ? 'selected' : ''); ?>>
                                        <?php echo e($category->full_path); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- التاجز -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                التاجز (مفصولة بفواصل)
                            </label>
                            <input type="text" name="tags" id="tags" 
                                   value="<?php echo e(old('tags')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="رياضيات, جبر, معادلات">
                            <p class="mt-1 text-sm text-gray-500">ستساعد في البحث والتصنيف</p>
                        </div>

                        <!-- حالة السؤال -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

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
                        <div id="question-preview" class="min-h-32 bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                            اكتب السؤال لرؤية المعاينة
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
                                حفظ السؤال
                            </button>
                            
                            <button type="submit" name="save_and_new" value="1"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plus ml-2"></i>
                                حفظ وإضافة آخر
                            </button>
                            
                            <a href="<?php echo e(route('admin.question-bank.index')); ?>" 
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

<?php $__env->startPush('scripts'); ?>
<script>
function toggleQuestionFields() {
    const type = document.getElementById('type').value;
    
    // إخفاء جميع الخيارات
    document.getElementById('question-options').style.display = 'none';
    document.getElementById('multiple-choice-options').style.display = 'none';
    document.getElementById('true-false-options').style.display = 'none';
    document.getElementById('fill-blank-options').style.display = 'none';
    document.getElementById('text-answer-options').style.display = 'none';
    document.getElementById('matching-options').style.display = 'none';
    document.getElementById('ordering-options').style.display = 'none';
    
    if (type) {
        document.getElementById('question-options').style.display = 'block';
        
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
            case 'matching':
                document.getElementById('matching-options').style.display = 'block';
                break;
            case 'ordering':
                document.getElementById('ordering-options').style.display = 'block';
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
            const option = document.getElementById(`option_${i}`).value;
            if (option) {
                previewHtml += `<div class="mt-1">○ ${option}</div>`;
            }
        }
        previewHtml += '</div>';
    } else if (type === 'true_false') {
        previewHtml += '<div class="mt-3"><strong>الخيارات:</strong><div class="mt-1">○ صح</div><div class="mt-1">○ خطأ</div></div>';
    }
    
    preview.innerHTML = previewHtml;
}

// تحديث المعاينة عند تغيير المدخلات
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
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\question-bank\create.blade.php ENDPATH**/ ?>