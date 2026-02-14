<?php $__env->startSection('title', 'إضافة كورس برمجي جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 py-8" x-data="courseBuilder({
        tracks: <?php echo json_encode($trackOptions, 15, 512) ?>,
        selectedTrack: '<?php echo e(old('academic_year_id', $selectedTrack)); ?>',
        selectedSubject: '<?php echo e(old('academic_subject_id', $selectedSubject)); ?>',
        selectedSkills: <?php echo json_encode(old('skills', []), 512) ?>,
    })" x-init="init()">
    <div class="w-full max-w-full space-y-8">
        <div class="bg-gradient-to-br from-indigo-500 via-sky-500 to-emerald-500 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-sm font-semibold">
                        <i class="fas fa-graduation-cap"></i>
                        إنشاء كورس جديد
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold">أطلق كورساً برمجياً متكاملاً</h1>
                    <p class="text-sm text-white/80 max-w-2xl">
                        قم بتجميع المحتوى التدريبي ضمن مسار التعلم المناسب، حدّد اللغة والأطر والمهارات المستهدفة، واختر الفريق المسؤول.
                    </p>
                </div>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2 text-sm font-semibold hover:bg-white/10 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورسات
                </a>
            </div>
        </div>

        <form action="<?php echo e(route('admin.advanced-courses.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">المعلومات الأساسية</h2>
                            <p class="text-xs text-gray-500 mt-1">املأ تفاصيل الكورس الأساسية واختر المسار والمجموعة المناسبة.</p>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">عنوان الكورس *</label>
                                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: أساسيات تطوير واجهات الويب">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المسار التعليمي</label>
                                    <select name="academic_year_id" x-model="formTrack" @change="refreshSubjects"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">بدون مسار (كورس مستقل)</option>
                                        <?php $__currentLoopData = $trackOptions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($track['id']); ?>" <?php echo e(old('academic_year_id', $selectedTrack) == $track['id'] ? 'selected' : ''); ?>><?php echo e($track['name']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <p class="text-xs text-gray-500">اختياري — يمكن ترك الكورس بدون ربط بمسار.</p>
                                    <?php $__errorArgs = ['academic_year_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مجموعة المهارات</label>
                                    <select name="academic_subject_id" x-model="formSubject"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">اختر المجموعة (تظهر بعد اختيار المسار)</option>
                                        <template x-for="subject in availableSubjects" :key="subject.id">
                                            <option :value="subject.id" x-text="subject.name"></option>
                                        </template>
                                    </select>
                                    <?php $__errorArgs = ['academic_subject_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المدرّس المسؤول</label>
                                    <select name="instructor_id"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">بدون مدرّس محدد</option>
                                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id') == $instructor->id ? 'selected' : ''); ?>>
                                                <?php echo e($instructor->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مستوى الكورس</label>
                                    <select name="level"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="beginner" <?php echo e(old('level', 'beginner') == 'beginner' ? 'selected' : ''); ?>>مبتدئ</option>
                                        <option value="intermediate" <?php echo e(old('level') == 'intermediate' ? 'selected' : ''); ?>>متوسط</option>
                                        <option value="advanced" <?php echo e(old('level') == 'advanced' ? 'selected' : ''); ?>>متقدم</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">لغة البرمجة</label>
                                    <input list="programming_languages" name="programming_language" value="<?php echo e(old('programming_language')); ?>"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: JavaScript">
                                    <datalist id="programming_languages">
                                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($language); ?>"></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">الإطار / التقنية</label>
                                    <input list="frameworks" name="framework" value="<?php echo e(old('framework')); ?>"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: React">
                                    <datalist id="frameworks">
                                        <?php $__currentLoopData = $frameworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($framework); ?>"></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">التصنيف</label>
                                    <input list="categories" name="category" value="<?php echo e(old('category')); ?>"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: تطوير الويب">
                                    <datalist id="categories">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category); ?>"></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مدة الكورس (ساعات)</label>
                                    <input type="number" name="duration_hours" value="<?php echo e(old('duration_hours', 0)); ?>" min="0"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="عدد الساعات">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مدة إضافية (دقائق)</label>
                                    <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', 0)); ?>" min="0" max="59"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="دقائق إضافية">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">سعر الكورس (جنيه)</label>
                                    <input type="number" name="price" value="<?php echo e(old('price', 0)); ?>" min="0" step="0.01"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="0 للمواد المجانية">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">وصف الكورس</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                          placeholder="اشرح محتوى الكورس وقيمته للطلاب."><?php echo e(old('description')); ?></textarea>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <i class="fas fa-video text-indigo-600 ml-1"></i>
                                    رابط فيديو المقدمة
                                </label>
                                <input type="url" name="video_url" value="<?php echo e(old('video_url')); ?>"
                                       class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                       placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/...">
                                <p class="mt-1 text-xs text-gray-500">
                                    رابط فيديو مقدمة الكورس الذي سيتم عرضه في صفحة الكورس. يدعم روابط YouTube و Vimeo.
                                </p>
                                <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">تاريخ البداية</label>
                                    <input type="date" name="starts_at" value="<?php echo e(old('starts_at')); ?>"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">تاريخ النهاية</label>
                                    <input type="date" name="ends_at" value="<?php echo e(old('ends_at')); ?>"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">المهارات والمخرجات</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">المهارات الرئيسية</label>
                                <p class="text-xs text-gray-500 mb-3">
                                    اختر مهارات موجودة أو أضف مهارات جديدة لدعم الفريق أثناء تصميم مسار الكورس.
                                </p>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 hover:border-indigo-400 transition"
                                                @click="addSkill('<?php echo e($skill); ?>')">
                                            <?php echo e($skill); ?>

                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input id="customSkill" type="text" class="flex-1 rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition" placeholder="اكتب مهارة جديدة">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold transition"
                                            @click="addSkill(document.getElementById('customSkill').value); document.getElementById('customSkill').value='';">
                                        <i class="fas fa-plus"></i>
                                        إضافة
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3" x-show="selectedSkills.length">
                                    <template x-for="(skill, index) in selectedSkills" :key="skill">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                            <span x-text="skill"></span>
                                            <button type="button" class="text-indigo-600 hover:text-indigo-800" @click="removeSkill(index)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="skills[]" :value="skill">
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المتطلبات المسبقة</label>
                                    <textarea name="prerequisites" rows="3"
                                              class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                              placeholder="ما الذي يجب أن يعرفه الطالب قبل بدء الكورس؟"><?php echo e(old('prerequisites')); ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">ما الذي سيتعلمه الطالب؟</label>
                                    <textarea name="what_you_learn" rows="3"
                                              class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                              placeholder="المخرجات التعليمية والمهارات المكتسبة"><?php echo e(old('what_you_learn')); ?></textarea>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">متطلبات إضافية</label>
                                <textarea name="requirements" rows="3"
                                          class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                          placeholder="أدوات أو برامج يحتاجها الطلاب خلال الدراسة."><?php echo e(old('requirements')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">إعدادات العرض</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-4 text-sm text-gray-700">
                            <label class="flex items-center justify-between">
                                <span class="font-medium">تفعيل الكورس فوراً</span>
                                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            </label>
                            <label class="flex items-center justify_between">
                                <span class="font-medium">وضع الكورس ضمن الكورسات المميزة</span>
                                <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured') ? 'checked' : ''); ?> class="w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                            </label>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">لغة المحتوى</label>
                                <select name="language"
                                        class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                    <option value="ar" <?php echo e(old('language', 'ar') == 'ar' ? 'selected' : ''); ?>>العربية</option>
                                    <option value="en" <?php echo e(old('language') == 'en' ? 'selected' : ''); ?>>الإنجليزية</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">رفع صورة للكورس</label>
                                <input type="file" name="thumbnail" accept="image/*"
                                       class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                <p class="text-xs text-gray-500">PNG أو JPG بحد أقصى 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">ملخص سريع</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>المسار التعليمي</span>
                                <span class="font-semibold" x-text="summaryTrack"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>مجموعة المهارات</span>
                                <span class="font-semibold" x-text="summarySubject"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>عدد المهارات المحددة</span>
                                <span class="font-semibold" x-text="selectedSkills.length"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>الحالة</span>
                                <span class="font-semibold"><?php echo e(old('is_active', true) ? 'نشط' : 'مسودة'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="p-6 sm:p-8 space-y-3">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-indigo-500/20 transition">
                                <i class="fas fa-save"></i>
                                حفظ الكورس
                            </button>
                            <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function courseBuilder({tracks, selectedTrack, selectedSubject, selectedSkills}) {
    return {
        tracks,
        formTrack: selectedTrack ? String(selectedTrack) : '',
        formSubject: selectedSubject ? String(selectedSubject) : '',
        availableSubjects: [],
        selectedSkills: selectedSkills || [],
        get summaryTrack() {
            const track = this.tracks.find(t => String(t.id) === String(this.formTrack));
            return track ? track.name : 'غير محدد';
        },
        get summarySubject() {
            const subject = this.availableSubjects.find(s => String(s.id) === String(this.formSubject));
            return subject ? subject.name : 'غير محدد';
        },
        init() {
            this.refreshSubjects();
            if (this.formTrack && !this.formSubject && this.availableSubjects.length) {
                this.formSubject = String(this.availableSubjects[0].id);
            }
        },
        refreshSubjects() {
            const track = this.tracks.find(t => String(t.id) === String(this.formTrack));
            this.availableSubjects = track ? track.subjects || [] : [];
            const hasSelected = this.availableSubjects.find(s => String(s.id) === String(this.formSubject));
            this.formSubject = hasSelected ? String(this.formSubject) : '';
            if (!this.formSubject && this.availableSubjects.length) {
                this.formSubject = String(this.availableSubjects[0].id);
            }
        },
        addSkill(value) {
            const skill = (value || '').trim();
            if (!skill) return;
            if (!this.selectedSkills.includes(skill)) {
                this.selectedSkills.push(skill);
            }
        },
        removeSkill(index) {
            this.selectedSkills.splice(index, 1);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/advanced-courses/create.blade.php ENDPATH**/ ?>