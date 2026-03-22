<?php $__env->startSection('title', __('instructor.build_curriculum') . ' - ' . $course->title); ?>
<?php $__env->startSection('header', __('instructor.build_curriculum') . ' - ' . $course->title); ?>

<?php $__env->startPush('styles'); ?>
<style>
    #lectureModal { backdrop-filter: blur(4px); }
    .section-block.collapsed .section-body { display: none; }
    .section-block.collapsed .section-chevron { transform: rotate(-90deg); }
    /* سحب وإفلات احترافي */
    .sortable-ghost { opacity: 0.35; background: #e0f2fe !important; border-color: #0ea5e9 !important; border-style: dashed !important; transform: scale(0.98); }
    .sortable-drag { opacity: 1; box-shadow: 0 10px 40px rgba(0,0,0,0.15); cursor: grabbing !important; z-index: 1000; }
    .sortable-chosen { background: #f0f9ff !important; border-color: #0ea5e9 !important; }
    .items-container.sortable-dragging { min-height: 52px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 0.75rem; }
    body.curriculum-dragging .curriculum-drag-hint { opacity: 1 !important; }
    .section-block .section-header { touch-action: none; }
    .item-card .fa-grip-vertical { pointer-events: none; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1"><?php echo e(__('instructor.build_curriculum')); ?></h1>
                <p class="text-sm text-slate-500"><?php echo e($course->title); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('instructor.lectures.index')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span><?php echo e(__('instructor.lectures')); ?></span>
                </a>
                <a href="<?php echo e(route('instructor.courses.index')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span><?php echo e(__('instructor.back')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- الأقسام والمنهج -->
        <div class="lg:col-span-2 space-y-6">
            <!-- الأقسام -->
            <div id="sections-container">
                <?php $__empty_1 = true; $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('instructor.curriculum.partials.section', ['section' => $section, 'depth' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-200">
                        <i class="fas fa-folder-open text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-600 mb-4">لا توجد أقسام بعد</p>
                        <button onclick="showAddSectionModal()" 
                                class="inline-flex items-center gap-2 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة قسم جديد
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($sections->count() > 0): ?>
                <button onclick="showAddSectionModal()" 
                        class="w-full py-3 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    إضافة قسم جديد
                </button>
            <?php endif; ?>
        </div>

        <!-- العناصر المتاحة -->
        <div class="rounded-xl p-5 bg-slate-50 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">العناصر المتاحة</h3>
                <a href="<?php echo e(route('instructor.learning-patterns.index', $course)); ?>" 
                   class="px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-semibold transition-colors">
                    <i class="fas fa-puzzle-piece ml-1"></i>
                    إدارة الأنماط
                </a>
            </div>

            <?php if($availableLectures->count() > 0): ?>
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-500"></i>
                        المحاضرات (<?php echo e($availableLectures->count()); ?>)
                    </h4>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $availableLectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\Lecture', <?php echo e($lecture->id); ?>, '<?php echo e(addslashes($lecture->title)); ?>', 'محاضرة')">
                                <div class="font-semibold text-sm text-slate-800"><?php echo e($lecture->title); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($availableAssignments->count() > 0): ?>
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-tasks text-emerald-500"></i>
                        الواجبات (<?php echo e($availableAssignments->count()); ?>)
                    </h4>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $availableAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\Assignment', <?php echo e($assignment->id); ?>, '<?php echo e(addslashes($assignment->title)); ?>', 'واجب')">
                                <div class="font-semibold text-sm text-slate-800"><?php echo e($assignment->title); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($availableExams) && $availableExams->count() > 0): ?>
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-violet-500"></i>
                        الامتحانات (<?php echo e($availableExams->count()); ?>)
                    </h4>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $availableExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\AdvancedExam', <?php echo e($exam->id); ?>, '<?php echo e(addslashes($exam->title)); ?>', 'امتحان')">
                                <div class="font-semibold text-sm text-slate-800"><?php echo e($exam->title); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($availableLearningPatterns) && $availableLearningPatterns->count() > 0): ?>
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-puzzle-piece text-amber-500"></i>
                        الأنماط التعليمية (<?php echo e($availableLearningPatterns->count()); ?>)
                    </h4>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $availableLearningPatterns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pattern): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $typeInfo = $pattern->getTypeInfo(); ?>
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\LearningPattern', <?php echo e($pattern->id); ?>, '<?php echo e(addslashes($pattern->title)); ?>', '<?php echo e(addslashes($typeInfo['name'] ?? 'نمط تعليمي')); ?>')">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?> text-amber-500"></i>
                                    <span class="font-semibold text-sm text-slate-800"><?php echo e($pattern->title); ?></span>
                                </div>
                                <div class="text-xs text-slate-500"><?php echo e($typeInfo['name'] ?? 'نمط تعليمي'); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($availableLectures->count() == 0 && $availableAssignments->count() == 0 && (!isset($availableExams) || $availableExams->count() == 0) && (!isset($availableLearningPatterns) || $availableLearningPatterns->count() == 0)): ?>
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-check-circle text-2xl mb-2 text-emerald-400"></i>
                    <p class="text-sm">جميع العناصر مضافة للمنهج</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal إضافة/تعديل قسم -->
<div id="sectionModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
        <h3 class="text-xl font-bold text-slate-800 mb-4" id="modalTitle">إضافة قسم جديد</h3>
        <form id="sectionForm" onsubmit="saveSection(event)">
            <input type="hidden" id="sectionId">
            <input type="hidden" id="sectionParentId" name="parent_id" value="">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان القسم</label>
                <input type="text" id="sectionTitle" required 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
            </div>
            <div class="mb-4" id="sectionDescriptionWrap">
                <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف (اختياري) — للأقسام الرئيسية فقط</label>
                <textarea id="sectionDescription" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
            </div>
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                <label class="block text-sm font-semibold text-slate-700 mb-2">فتح هذا القسم للطالب</label>
                <p class="text-xs text-slate-600 mb-2">متى يُسمح للطالب بفتح هذا القسم؟ (القسم التالي لا يفتح إلا بتحقيق الشرط)</p>
                <select id="sectionUnlockRule" onchange="var w=document.getElementById('sectionUnlockPercentWrap');if(w) w.classList.toggle('hidden', this.value !== 'previous_percent');" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800 mb-2">
                    <option value="always">دائماً مفتوح (لا يشترط إكمال قسم سابق)</option>
                    <option value="previous_percent">عند تحقيق نسبة معينة من القسم السابق</option>
                    <option value="previous_all_items">عند إكمال كل عناصر القسم السابق</option>
                </select>
                <div id="sectionUnlockPercentWrap" class="hidden">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">النسبة المئوية من القسم السابق %</label>
                    <input type="number" id="sectionUnlockPercent" min="0" max="100" value="100" placeholder="مثال: 80"
                           class="w-full px-4 py-2 border border-slate-200 rounded-lg text-slate-800">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    حفظ
                </button>
                <button type="button" onclick="closeSectionModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إضافة محاضرة (عرض الصفحة) -->
<div id="lectureModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-6xl my-8 max-h-[90vh] overflow-y-auto shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-800">إضافة محاضرة جديدة</h3>
            <button onclick="closeLectureModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="lectureForm" onsubmit="saveLecture(event)">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="lectureSectionId">
            <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="lectureTitle" required placeholder="مثال: مقدمة في JavaScript"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" id="lectureDescription" rows="3" placeholder="وصف مختصر للمحاضرة..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
                </div>
                <input type="hidden" name="course_lesson_id" value="">
                <input type="hidden" name="duration_minutes" id="lectureDuration" value="60">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">التاريخ والوقت <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="lectureScheduledAt" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نسبة المشاهدة لفتح الفيديو التالي %</label>
                        <input type="number" name="min_watch_percent_to_unlock_next" id="lectureMinWatchPercent" min="0" max="100" placeholder="مثال: 80 — اترك فارغاً لعدم اشتراط نسبة"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">يجب على الطالب مشاهدة هذه النسبة من الفيديو ليتاح له الانتقال للمحاضرة التالية (0–100، اختياري)</p>
                    </div>
                </div>
            </div>
            <div class="space-y-5">
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><i class="fas fa-video text-sky-500 ml-1"></i> رابط تسجيل المحاضرة (اختياري)</label>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">اختر المشغل</label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-3">
                            <button type="button" onclick="selectVideoPlatform('youtube', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="youtube">
                                <i class="fab fa-youtube text-red-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">YouTube</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('vimeo', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="vimeo">
                                <i class="fab fa-vimeo text-blue-500 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">Vimeo</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('google_drive', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="google_drive">
                                <i class="fab fa-google-drive text-green-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">Drive</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('direct', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="direct">
                                <i class="fas fa-file-video text-purple-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">مباشر</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('bunny', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="bunny">
                                <i class="fas fa-cloud text-orange-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">Bunny.net</span>
                            </button>
                        </div>
                        <input type="hidden" name="video_platform" id="lectureVideoPlatform" value="">
                    </div>
                    <div>
                        <input type="url" name="recording_url" id="lectureRecordingUrl" placeholder="ضع رابط الفيديو هنا..." oninput="previewLectureVideo()"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500" id="lectureVideoPlaceholder"></p>
                    </div>
                    <div id="lectureVideoPreview" class="hidden mt-3 bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9; max-height: 200px;">
                        <div id="lectureVideoPreviewContent" class="w-full h-full flex items-center justify-center text-white">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الملاحظات</label>
                    <textarea name="notes" id="lectureNotes" rows="3" placeholder="ملاحظات إضافية..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl cursor-pointer border border-sky-100">
                        <input type="checkbox" name="has_attendance_tracking" value="1" checked class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">تتبع الحضور</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl cursor-pointer border border-emerald-100">
                        <input type="checkbox" name="has_assignment" value="1" class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">يوجد واجب</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl cursor-pointer border border-amber-100">
                        <input type="checkbox" name="has_evaluation" value="1" class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">يوجد تقييم</span>
                    </label>
                </div>
                <!-- مواد المحاضرة -->
                <div class="border-t border-slate-200 pt-5 mt-5">
                    <h4 class="text-sm font-bold text-slate-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-paperclip text-sky-500"></i>
                        مواد المحاضرة (اختياري)
                    </h4>
                    <p class="text-xs text-slate-500 mb-3">يمكنك رفع ملفات (PDF، Word، عروض...) وتحديد ظهورها للطالب.</p>
                    <div id="curriculum-materials-container" class="space-y-3">
                        <div class="curriculum-material-row flex flex-wrap items-end gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex-1 min-w-[160px]">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">الملف</label>
                                <input type="file" name="material_files[]" class="w-full text-sm text-slate-700 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700 file:text-sm file:font-semibold" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg">
                            </div>
                            <div class="w-40">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">عنوان (اختياري)</label>
                                <input type="text" name="material_titles[]" placeholder="مثال: ملخص المحاضرة" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                            </div>
                            <label class="flex items-center gap-2 pb-2">
                                <input type="hidden" name="material_visible[]" value="0">
                                <input type="checkbox" name="material_visible[]" value="1" checked class="w-4 h-4 text-sky-600 rounded">
                                <span class="text-sm font-medium text-slate-700">ظاهر للطالب</span>
                            </label>
                            <button type="button" class="curriculum-remove-material px-3 py-2 bg-rose-100 text-rose-700 rounded-lg text-sm font-medium hover:bg-rose-200" style="display:none;"><i class="fas fa-times ml-1"></i> حذف</button>
                        </div>
                    </div>
                    <button type="button" id="curriculum-add-material" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-sky-100 text-sky-700 rounded-xl font-semibold text-sm hover:bg-sky-200 transition-colors">
                        <i class="fas fa-plus"></i>
                        إضافة مادة أخرى
                    </button>
                </div>
            </div>
            <div class="flex gap-3 mt-6 col-span-full">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    <span id="lectureSubmitText">حفظ وإضافة للمنهج</span>
                </button>
                <button type="button" onclick="closeLectureModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
            </div>
            <input type="hidden" id="lectureEditId" name="lecture_id">
            <input type="hidden" name="status" id="lectureStatus" value="scheduled">
        </form>
    </div>
</div>

<!-- Modal أسئلة الفيديو للمحاضرة -->
<div id="videoQuestionsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-3xl my-8 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 bg-amber-50 flex items-center justify-between shrink-0">
            <h3 class="text-lg font-bold text-slate-800" id="videoQuestionsModalTitle"><i class="fas fa-question-circle text-amber-600 ml-1"></i> أسئلة الفيديو</h3>
            <button type="button" onclick="closeVideoQuestionsModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 overflow-y-auto flex-1">
            <p class="text-sm text-slate-600 mb-4">تظهر الأسئلة للطالب عند الوصول للدقيقة المحددة ويتوقف الفيديو. يمكن استيراد سؤال من البنك أو كتابة سؤال مخصص.</p>
            <div id="videoQuestionsList" class="space-y-2 mb-6"></div>
            <hr class="border-slate-200 my-4">
            <h4 class="text-base font-bold text-slate-800 mb-3">إضافة سؤال جديد</h4>
            <form id="videoQuestionForm" onsubmit="submitVideoQuestion(event)" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="vqLectureId" name="lecture_id" value="">
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="inline-flex items-center gap-2 cursor-pointer flex-1">
                        <input type="checkbox" id="vqShowAtEnd" name="show_at_end" value="1" class="w-4 h-4 text-amber-500 rounded border-slate-300" onchange="toggleVqTimestampFields()">
                        <span class="font-semibold text-slate-800">يظهر السؤال في نهاية الفيديو</span>
                    </label>
                    <span class="text-xs text-slate-500">لا حاجة لتحديد الدقيقة عند التفعيل</span>
                </div>
                <div id="vqTimestampWrap" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الدقيقة في الفيديو <span class="text-red-500">*</span></label>
                        <input type="number" id="vqTimestampMinutes" name="timestamp_minutes" min="0" max="999" value="0" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ثوانٍ إضافية (0–59)</label>
                        <input type="number" id="vqTimestampSeconds" name="timestamp_seconds_extra" min="0" max="59" value="0" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">مصدر السؤال</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer"><input type="radio" name="question_source" value="bank" class="text-sky-500" onchange="toggleVqSource('bank')"> من بنك الأسئلة</label>
                        <label class="inline-flex items-center gap-2 cursor-pointer"><input type="radio" name="question_source" value="custom" checked class="text-sky-500" onchange="toggleVqSource('custom')"> سؤال مخصص</label>
                    </div>
                </div>
                <div id="vqBankWrap" class="hidden space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">البنك</label>
                    <select id="vqBankId" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                        <option value="">-- اختر البنك --</option>
                    </select>
                    <label class="block text-sm font-semibold text-slate-700">السؤال</label>
                    <select name="question_id" id="vqQuestionId" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                        <option value="">-- اختر السؤال --</option>
                    </select>
                </div>
                <div id="vqCustomWrap" class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">نص السؤال <span class="text-red-500">*</span></label>
                    <textarea id="vqCustomText" name="custom_question_text" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800" placeholder="اكتب السؤال..."></textarea>
                    <label class="block text-sm font-semibold text-slate-700">الخيارات (سطر لكل خيار)</label>
                    <textarea id="vqCustomOptions" name="custom_options_text" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800 font-mono text-sm" placeholder="الخيار أ&#10;الخيار ب&#10;الخيار ج"></textarea>
                    <label class="block text-sm font-semibold text-slate-700">الإجابة الصحيحة <span class="text-red-500">*</span></label>
                    <input type="text" id="vqCustomCorrect" name="custom_correct_answer" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800" placeholder="نفس النص كما في أحد الخيارات">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">عند الإجابة الخاطئة</label>
                        <select id="vqOnWrong" name="on_wrong" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800" onchange="toggleVqRewind()">
                            <option value="continue">متابعة الفيديو</option>
                            <option value="rewind">إعادة جزء من الفيديو</option>
                        </select>
                    </div>
                    <div id="vqRewindWrap" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">كم ثانية للرجوع؟</label>
                        <input type="number" name="rewind_seconds" id="vqRewindSeconds" min="0" max="3600" value="0" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">النقاط</label>
                        <input type="number" name="points" id="vqPoints" value="1" min="1" max="100" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">عدد مرات الظهور</label>
                        <select name="show_count" id="vqShowCount" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-slate-800">
                            <option value="0">كل مرة (عند كل مشاهدة)</option>
                            <option value="1" selected>مرة واحدة فقط</option>
                            <option value="2">مرتين</option>
                            <option value="3">3 مرات</option>
                            <option value="5">5 مرات</option>
                            <option value="10">10 مرات</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">متى يظهر السؤال للطالب أثناء المشاهدة</p>
                    </div>
                </div>
                <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-1"></i> إضافة السؤال
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal إضافة امتحان من المنهج (عريض) -->
<div id="examModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl my-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">إضافة امتحان جديد</h3>
                    <p class="text-xs text-slate-500">يُضاف لهذا القسم في الكورس الحالي</p>
                </div>
            </div>
            <button type="button" onclick="closeExamModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="examForm" onsubmit="saveExam(event)">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="section_id" id="examSectionId" value="">
            <input type="hidden" name="course_lesson_id" value="">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الامتحان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="examTitle" required placeholder="مثال: اختبار الوحدة الأولى"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="examDescription" rows="3" placeholder="وصف مختصر..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                        <input type="number" name="total_marks" id="examTotalMarks" value="100" min="1" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">درجة النجاح <span class="text-red-500">*</span></label>
                        <input type="number" name="passing_marks" id="examPassingMarks" value="60" min="0" step="0.5" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="examDuration" value="60" min="5" max="480" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">المحاولات <span class="text-red-500">*</span></label>
                        <input type="number" name="attempts_allowed" id="examAttempts" value="1" min="1" max="10" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3">
                <button type="submit" id="examSubmitBtn" class="flex-1 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج
                </button>
                <button type="button" onclick="closeExamModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إنشاء واجب وإضافة للمنهج (عريض) -->
<div id="assignmentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl my-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-tasks"></i></div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">إنشاء واجب جديد</h3>
                    <p class="text-xs text-slate-500">يُضاف مباشرة لهذا القسم في الكورس الحالي</p>
                </div>
            </div>
            <button type="button" onclick="closeAssignmentModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="assignmentFormCurriculum" onsubmit="saveAssignment(event)">
            <input type="hidden" name="section_id" id="assignmentSectionId" value="">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الواجب <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="assignmentTitle" required placeholder="مثال: واجب الوحدة الأولى"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="assignmentDescription" rows="2" placeholder="وصف مختصر..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">التعليمات</label>
                    <textarea name="instructions" id="assignmentInstructions" rows="2" placeholder="تعليمات للطلاب..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ الاستحقاق</label>
                    <input type="datetime-local" name="due_date" id="assignmentDueDate"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" id="assignmentMaxScore" value="100" min="1" max="1000" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div class="flex items-end gap-4 md:col-span-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="allow_late_submission" id="assignmentAllowLate" value="1"
                               class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                        <span class="text-sm font-medium text-slate-700">السماح بالتسليم المتأخر</span>
                    </label>
                    <div class="w-40">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
                        <select name="status" id="assignmentStatus"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <option value="draft">مسودة</option>
                            <option value="published">منشور</option>
                            <option value="archived">مؤرشف</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3">
                <button type="submit" id="assignmentSubmitBtn" class="flex-1 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج
                </button>
                <button type="button" onclick="closeAssignmentModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إضافة عنصر -->
<div id="itemModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
        <h3 class="text-xl font-bold text-slate-800 mb-2">إضافة عنصر للمنهج</h3>
        <p class="text-sm text-slate-600 mb-4" id="itemName"></p>
        <form id="itemForm" onsubmit="addItem(event)">
            <input type="hidden" id="itemType">
            <input type="hidden" id="itemId">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">اختر القسم</label>
                <select id="targetSection" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    <option value="">اختر القسم</option>
                    <?php $__currentLoopData = $sectionsFlatForSelect; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($entry->section->id); ?>"><?php echo e(str_repeat('— ', $entry->depth)); ?><?php echo e($entry->section->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    إضافة
                </button>
                <button type="button" onclick="closeItemModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
let currentSectionId = null;
let currentItemType = null;
let currentItemId = null;

function toggleSection(sectionId) {
    const block = document.querySelector('.section-block[data-section-id="' + sectionId + '"]');
    if (!block) return;
    block.classList.toggle('collapsed');
    const chevron = document.querySelector('.section-chevron[data-section-id="' + sectionId + '"]');
    if (chevron) chevron.style.transform = block.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
}

function showAddExamModal(sectionId) {
    document.getElementById('examSectionId').value = sectionId;
    document.getElementById('examForm').reset();
    document.getElementById('examSectionId').value = sectionId;
    document.getElementById('examTotalMarks').value = 100;
    document.getElementById('examPassingMarks').value = 60;
    document.getElementById('examDuration').value = 60;
    document.getElementById('examAttempts').value = 1;
    document.getElementById('examModal').classList.remove('hidden');
    document.getElementById('examModal').classList.add('flex');
}

function closeExamModal() {
    document.getElementById('examModal').classList.add('hidden');
    document.getElementById('examModal').classList.remove('flex');
}

function showAddAssignmentModal(sectionId) {
    document.getElementById('assignmentSectionId').value = sectionId;
    document.getElementById('assignmentFormCurriculum').reset();
    document.getElementById('assignmentSectionId').value = sectionId;
    document.getElementById('assignmentMaxScore').value = 100;
    document.getElementById('assignmentModal').classList.remove('hidden');
    document.getElementById('assignmentModal').classList.add('flex');
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
    document.getElementById('assignmentModal').classList.remove('flex');
}

function saveAssignment(e) {
    e.preventDefault();
    var form = document.getElementById('assignmentFormCurriculum');
    var btn = document.getElementById('assignmentSubmitBtn');
    var formData = new FormData(form);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...';
    fetch('<?php echo e(route("instructor.courses.curriculum.assignments.store", $course)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            closeAssignmentModal();
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
        }
    })
    .catch(function() {
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
    });
}

function saveExam(e) {
    e.preventDefault();
    const form = document.getElementById('examForm');
    const btn = document.getElementById('examSubmitBtn');
    const sectionId = document.getElementById('examSectionId').value;
    const totalMarks = parseFloat(document.getElementById('examTotalMarks').value) || 100;
    const passingMarks = parseFloat(document.getElementById('examPassingMarks').value) || 60;
    if (passingMarks > totalMarks) {
        alert('درجة النجاح يجب ألا تتجاوز الدرجة الكلية');
        return;
    }
    const formData = new FormData(form);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...';
    fetch('<?php echo e(route("instructor.courses.curriculum.exams.store", $course)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeExamModal();
            if (data.redirect) {
                if (confirm('تم إنشاء الامتحان بنجاح. هل تريد الانتقال الآن لإضافة الأسئلة؟')) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                location.reload();
            }
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
        }
    })
    .catch(err => {
        console.error(err);
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
    });
}

function showAddSectionModal() {
    document.getElementById('sectionId').value = '';
    document.getElementById('sectionParentId').value = '';
    document.getElementById('sectionTitle').value = '';
    document.getElementById('sectionDescription').value = '';
    var wrap = document.getElementById('sectionDescriptionWrap');
    if (wrap) wrap.style.display = '';
    document.getElementById('modalTitle').textContent = 'إضافة قسم جديد';
    var ruleEl = document.getElementById('sectionUnlockRule');
    var percentWrap = document.getElementById('sectionUnlockPercentWrap');
    if (ruleEl) ruleEl.value = 'previous_all_items';
    if (percentWrap) percentWrap.classList.add('hidden');
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModal').classList.add('flex');
}

function showAddSubSectionModal(parentId) {
    document.getElementById('sectionId').value = '';
    document.getElementById('sectionParentId').value = parentId || '';
    document.getElementById('sectionTitle').value = '';
    document.getElementById('sectionDescription').value = '';
    var wrap = document.getElementById('sectionDescriptionWrap');
    if (wrap) wrap.style.display = 'none';
    document.getElementById('modalTitle').textContent = 'إضافة قسم فرعي';
    var ruleEl = document.getElementById('sectionUnlockRule');
    var percentWrap = document.getElementById('sectionUnlockPercentWrap');
    if (ruleEl) ruleEl.value = 'previous_all_items';
    if (percentWrap) percentWrap.classList.add('hidden');
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModal').classList.add('flex');
}

function editSection(id, title, description, parentId, unlockRule, unlockPercent) {
    document.getElementById('sectionId').value = id;
    document.getElementById('sectionParentId').value = parentId || '';
    document.getElementById('sectionTitle').value = title;
    document.getElementById('sectionDescription').value = description || '';
    var wrap = document.getElementById('sectionDescriptionWrap');
    if (wrap) wrap.style.display = (parentId ? 'none' : '');
    var ruleEl = document.getElementById('sectionUnlockRule');
    var percentWrap = document.getElementById('sectionUnlockPercentWrap');
    var percentEl = document.getElementById('sectionUnlockPercent');
    if (ruleEl) ruleEl.value = unlockRule || 'previous_all_items';
    if (percentEl) percentEl.value = (unlockPercent != null && unlockPercent !== '') ? unlockPercent : 100;
    if (percentWrap) percentWrap.classList.toggle('hidden', ruleEl && ruleEl.value !== 'previous_percent');
    document.getElementById('modalTitle').textContent = 'تعديل القسم';
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModal').classList.add('flex');
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.add('hidden');
    document.getElementById('sectionModal').classList.remove('flex');
}

function saveSection(e) {
    e.preventDefault();
    const id = document.getElementById('sectionId').value;
    const title = document.getElementById('sectionTitle').value;
    const description = document.getElementById('sectionDescription').value;
    const parentIdEl = document.getElementById('sectionParentId');
    const parentId = parentIdEl && parentIdEl.value ? parentIdEl.value : null;
    const unlockRule = document.getElementById('sectionUnlockRule').value || 'previous_all_items';
    const unlockPercentEl = document.getElementById('sectionUnlockPercent');
    const unlockPercent = (unlockRule === 'previous_percent' && unlockPercentEl) ? parseInt(unlockPercentEl.value, 10) : null;
    
    const url = id 
        ? `/instructor/sections/${id}`
        : `/instructor/courses/<?php echo e($course->id); ?>/sections`;
    const method = id ? 'PUT' : 'POST';
    const body = id 
        ? { title, description, unlock_rule: unlockRule, unlock_percent: (unlockRule === 'previous_percent' ? unlockPercent : null) } 
        : { title, description, parent_id: parentId };
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('حدث خطأ أثناء الحفظ');
    });
}

function deleteSection(id) {
    if (!confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم حذف جميع العناصر والأقسام الفرعية بداخله.')) return;
    
    fetch(`/instructor/sections/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function showAddItemModal(type, id, name, typeName) {
    currentItemType = type;
    currentItemId = id;
    document.getElementById('itemType').value = type;
    document.getElementById('itemId').value = id;
    document.getElementById('itemName').textContent = `إضافة ${typeName}: ${name}`;
    document.getElementById('targetSection').value = '';
    document.getElementById('itemModal').classList.remove('hidden');
    document.getElementById('itemModal').classList.add('flex');
}

function closeItemModal() {
    document.getElementById('itemModal').classList.add('hidden');
    document.getElementById('itemModal').classList.remove('flex');
}

function addItem(e) {
    e.preventDefault();
    const sectionId = document.getElementById('targetSection').value;
    
    fetch(`/instructor/sections/${sectionId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            item_type: currentItemType,
            item_id: currentItemId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function removeItem(id) {
    if (!confirm('هل أنت متأكد من حذف هذا العنصر من المنهج؟')) return;
    
    fetch(`/instructor/curriculum-items/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function showAddLectureModal(sectionId) {
    currentSectionId = sectionId;
    document.getElementById('lectureSectionId').value = sectionId;
    document.getElementById('lectureEditId').value = '';
    // مسح الحقول
    document.getElementById('lectureForm').reset();
    document.getElementById('lectureForm').querySelector('input[name="course_id"]').value = <?php echo e($course->id); ?>;
    const statusEl = document.getElementById('lectureStatus');
    if (statusEl) statusEl.value = 'scheduled';
    const hasAttendance = document.getElementById('lectureForm').querySelector('input[name="has_attendance_tracking"]');
    if (hasAttendance) hasAttendance.checked = true;
    // تعيين التاريخ الحالي كقيمة افتراضية
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('lectureScheduledAt').value = now.toISOString().slice(0, 16);
    document.getElementById('lectureDuration').value = 60;
    var minWatchEl = document.getElementById('lectureMinWatchPercent');
    if (minWatchEl) minWatchEl.value = '';
    
    // تحديث العنوان والنص
    document.querySelector('#lectureModal h3').textContent = 'إضافة محاضرة جديدة';
    document.getElementById('lectureSubmitText').textContent = 'حفظ وإضافة للمنهج';
    
    // إعادة تعيين المشغل
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
    
    document.getElementById('lectureModal').classList.remove('hidden');
    document.getElementById('lectureModal').classList.add('flex');
}

// تعديل المحاضرة من باني الدورات
async function editLectureFromCurriculum(lectureId, sectionId) {
    try {
        const response = await fetch(`/instructor/lectures/${lectureId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            // إذا لم يكن JSON، جرب تحميل الصفحة
            window.location.href = `/instructor/lectures/${lectureId}/edit`;
            return;
        }
        
        const lecture = await response.json();
        
        console.log('=== Loaded lecture data ===');
        console.log('Full lecture object:', lecture);
        console.log('recording_url:', lecture.recording_url);
        console.log('video_platform:', lecture.video_platform);
        console.log('recording_url type:', typeof lecture.recording_url);
        console.log('recording_url length:', lecture.recording_url ? lecture.recording_url.length : 0);
        
        // ملء النموذج
        document.getElementById('lectureEditId').value = lectureId;
        document.getElementById('lectureSectionId').value = sectionId;
        document.getElementById('lectureTitle').value = lecture.title || '';
        document.getElementById('lectureDescription').value = lecture.description || '';
        // تحويل التاريخ
        if (lecture.scheduled_at) {
            const scheduledDate = new Date(lecture.scheduled_at);
            scheduledDate.setMinutes(scheduledDate.getMinutes() - scheduledDate.getTimezoneOffset());
            document.getElementById('lectureScheduledAt').value = scheduledDate.toISOString().slice(0, 16);
        }
        
        document.getElementById('lectureDuration').value = lecture.duration_minutes || 60;
        var minWatchEl = document.getElementById('lectureMinWatchPercent');
        if (minWatchEl) minWatchEl.value = (lecture.min_watch_percent_to_unlock_next != null && lecture.min_watch_percent_to_unlock_next !== '') ? lecture.min_watch_percent_to_unlock_next : '';
        
        // تعيين الرابط - مهم جداً - نستخدم setTimeout لضمان أن الحقل موجود
        setTimeout(() => {
            const recordingUrlInput = document.getElementById('lectureRecordingUrl');
            if (recordingUrlInput) {
                const urlValue = lecture.recording_url || '';
                recordingUrlInput.value = urlValue;
                console.log('Set recording_url input to:', urlValue);
                console.log('Input value after setting:', recordingUrlInput.value);
                
                // إطلاق event لتفعيل oninput إذا كان موجوداً
                recordingUrlInput.dispatchEvent(new Event('input', { bubbles: true }));
            } else {
                console.error('lectureRecordingUrl input not found!');
            }
        }, 100);
        
        document.getElementById('lectureNotes').value = lecture.notes || '';
        
        // تعيين حالة المحاضرة (مطلوب عند التحديث)
        const statusInput = document.getElementById('lectureStatus');
        if (statusInput) statusInput.value = lecture.status || 'scheduled';
        
        // تحديد المشغل - تطبيع video_platform لأحرف صغيرة لأن data-platform في HTML كلها lowercase
        setTimeout(() => {
            let platformSet = false;
            const platformNormalized = (lecture.video_platform || '').toString().trim().toLowerCase();
            
            // أولاً: محاولة استخدام video_platform المحفوظ (بعد التطبيع)
            if (platformNormalized) {
                const platformBtn = document.querySelector('[data-platform="' + platformNormalized + '"]');
                if (platformBtn) {
                    const savedUrl = lecture.recording_url || '';
                    selectVideoPlatform(platformNormalized, platformBtn);
                    document.getElementById('lectureVideoPlatform').value = platformNormalized;
                    setTimeout(() => {
                        const recordingUrlInput = document.getElementById('lectureRecordingUrl');
                        if (recordingUrlInput && savedUrl) recordingUrlInput.value = savedUrl;
                    }, 50);
                    platformSet = true;
                }
            }
            
            // ثانياً: إذا لم يتم تعيين المشغل، اكتشفه من الرابط
            if (!platformSet && lecture.recording_url) {
                const url = lecture.recording_url;
                let detectedPlatform = null;
                let platformBtn = null;
                
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    detectedPlatform = 'youtube';
                    platformBtn = document.querySelector('[data-platform="youtube"]');
                } else if (url.includes('vimeo.com')) {
                    detectedPlatform = 'vimeo';
                    platformBtn = document.querySelector('[data-platform="vimeo"]');
                } else if (url.includes('drive.google.com')) {
                    detectedPlatform = 'google_drive';
                    platformBtn = document.querySelector('[data-platform="google_drive"]');
                } else if (url.includes('mediadelivery.net')) {
                    detectedPlatform = 'bunny';
                    platformBtn = document.querySelector('[data-platform="bunny"]');
                } else if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
                    detectedPlatform = 'direct';
                    platformBtn = document.querySelector('[data-platform="direct"]');
                }
                
                if (platformBtn && detectedPlatform) {
                    const savedUrl = lecture.recording_url || '';
                    selectVideoPlatform(detectedPlatform, platformBtn);
                    document.getElementById('lectureVideoPlatform').value = detectedPlatform;
                    setTimeout(() => {
                        const recordingUrlInput = document.getElementById('lectureRecordingUrl');
                        if (recordingUrlInput && savedUrl) recordingUrlInput.value = savedUrl;
                    }, 50);
                    platformSet = true;
                }
            }
            
            // معاينة الفيديو إذا كان موجوداً - بعد تعيين المشغل والرابط
            if (lecture.recording_url) {
                console.log('Setting up video preview for URL:', lecture.recording_url);
                setTimeout(() => {
                    previewLectureVideo();
                }, 400);
            } else {
                console.warn('No recording_url found in lecture data');
            }
        }, 200);
        
        // تحديث الخيارات
        const hasAttendance = document.querySelector('input[name="has_attendance_tracking"]');
        const hasAssignment = document.querySelector('input[name="has_assignment"]');
        const hasEvaluation = document.querySelector('input[name="has_evaluation"]');
        
        if (hasAttendance) hasAttendance.checked = lecture.has_attendance_tracking || false;
        if (hasAssignment) hasAssignment.checked = lecture.has_assignment || false;
        if (hasEvaluation) hasEvaluation.checked = lecture.has_evaluation || false;
        
        // تحديث العنوان والنص
        document.querySelector('#lectureModal h3').textContent = 'تعديل المحاضرة';
        document.getElementById('lectureSubmitText').textContent = 'حفظ التعديلات';
        
        // فتح Modal
        document.getElementById('lectureModal').classList.remove('hidden');
        document.getElementById('lectureModal').classList.add('flex');
        
    } catch (error) {
        console.error('Error loading lecture:', error);
        // في حالة الخطأ، افتح صفحة التعديل
        window.location.href = `/instructor/lectures/${lectureId}/edit`;
    }
}

// حذف المحاضرة من باني الدورات
async function deleteLectureFromCurriculum(lectureId, curriculumItemId) {
    if (!confirm('هل أنت متأكد من حذف هذه المحاضرة؟ سيتم حذفها من المنهج أيضاً.')) {
        return;
    }
    
    try {
        const response = await fetch(`/instructor/lectures/${lectureId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('فشل حذف المحاضرة');
        }
        
        const data = await response.json();
        
        if (data.success || response.ok) {
            // حذف العنصر من المنهج أيضاً
            if (curriculumItemId) {
                await removeItem(curriculumItemId);
            } else {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'فشل حذف المحاضرة');
        }
    } catch (error) {
        console.error('Error deleting lecture:', error);
        alert('حدث خطأ أثناء حذف المحاضرة: ' + error.message);
    }
}

// --- أسئلة الفيديو للمحاضرة ---
let videoQuestionsLectureId = null;
let videoQuestionsBankData = {};

function openVideoQuestionsModal(lectureId, title) {
    videoQuestionsLectureId = lectureId;
    document.getElementById('videoQuestionsModalTitle').textContent = 'أسئلة الفيديو — ' + (title || 'المحاضرة');
    document.getElementById('vqLectureId').value = lectureId;
    var showAtEndEl = document.getElementById('vqShowAtEnd');
    if (showAtEndEl) { showAtEndEl.checked = false; toggleVqTimestampFields(); }
    document.getElementById('videoQuestionsModal').classList.remove('hidden');
    document.getElementById('videoQuestionsModal').classList.add('flex');
    loadVideoQuestionsData(lectureId);
}

function closeVideoQuestionsModal() {
    document.getElementById('videoQuestionsModal').classList.add('hidden');
    document.getElementById('videoQuestionsModal').classList.remove('flex');
    videoQuestionsLectureId = null;
}

function toggleVqSource(source) {
    document.getElementById('vqBankWrap').classList.toggle('hidden', source !== 'bank');
    document.getElementById('vqCustomWrap').classList.toggle('hidden', source !== 'custom');
}
function toggleVqTimestampFields() {
    var showAtEnd = document.getElementById('vqShowAtEnd').checked;
    var wrap = document.getElementById('vqTimestampWrap');
    var minutesInput = document.getElementById('vqTimestampMinutes');
    if (wrap) wrap.style.display = showAtEnd ? 'none' : 'grid';
    if (minutesInput) minutesInput.required = !showAtEnd;
}
function toggleVqRewind() {
    var onWrong = document.getElementById('vqOnWrong').value;
    document.getElementById('vqRewindWrap').classList.toggle('hidden', onWrong !== 'rewind');
}

async function loadVideoQuestionsData(lectureId) {
    var listEl = document.getElementById('videoQuestionsList');
    listEl.innerHTML = '<p class="text-slate-500 text-sm"><i class="fas fa-spinner fa-spin ml-1"></i> جاري التحميل...</p>';
    try {
        var res = await fetch('/instructor/lectures/' + lectureId + '/video-questions', { headers: { 'Accept': 'application/json' } });
        var data = await res.json();
        videoQuestionsBankData = data.bank_questions || {};
        var banks = data.question_banks || [];
        var bankSelect = document.getElementById('vqBankId');
        bankSelect.innerHTML = '<option value="">-- اختر البنك --</option>';
        banks.forEach(function(b) { bankSelect.innerHTML += '<option value="' + b.id + '">' + (b.title || '') + '</option>'; });
        listEl.innerHTML = '';
        (data.video_questions || []).forEach(function(q) {
            var row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200';
            row.innerHTML = '<div class="min-w-0 flex-1"><span class="font-semibold text-slate-800">' + (q.timestamp_label || '0:00') + '</span><span class="text-slate-500 mx-2">—</span><span class="text-slate-700 text-sm truncate">' + (q.question_text || '').substring(0, 60) + (q.question_text && q.question_text.length > 60 ? '...' : '') + '</span><span class="text-xs text-slate-400 mr-2">(' + (q.on_wrong === 'rewind' ? 'إعادة' : 'متابعة') + ')</span><span class="text-xs text-amber-600 mr-2">· ' + (q.show_count_label || 'مرة واحدة') + '</span></div><button type="button" onclick="deleteVideoQuestion(' + lectureId + ',' + q.id + ')" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm"><i class="fas fa-trash"></i></button>';
            listEl.appendChild(row);
        });
        if ((data.video_questions || []).length === 0) listEl.innerHTML = '<p class="text-slate-500 text-sm">لا توجد أسئلة بعد. أضف سؤالاً أدناه.</p>';
        document.getElementById('vqQuestionId').innerHTML = '<option value="">-- اختر السؤال --</option>';
        document.getElementById('vqQuestionId').dispatchEvent(new Event('change'));
    } catch (e) {
        listEl.innerHTML = '<p class="text-red-600 text-sm">فشل تحميل البيانات.</p>';
    }
}

document.getElementById('vqBankId').addEventListener('change', function() {
    var bankId = this.value;
    var qSelect = document.getElementById('vqQuestionId');
    qSelect.innerHTML = '<option value="">-- اختر السؤال --</option>';
    var questions = videoQuestionsBankData[bankId] || [];
    questions.forEach(function(q) {
        qSelect.innerHTML += '<option value="' + q.id + '">' + (q.text || '').substring(0, 50) + (q.text && q.text.length > 50 ? '...' : '') + '</option>';
    });
});

async function submitVideoQuestion(e) {
    e.preventDefault();
    var form = document.getElementById('videoQuestionForm');
    var source = form.querySelector('input[name="question_source"]:checked').value;
    var showAtEnd = document.getElementById('vqShowAtEnd') && document.getElementById('vqShowAtEnd').checked;
    var payload = {
        _token: '<?php echo e(csrf_token()); ?>',
        show_at_end: showAtEnd,
        timestamp_minutes: document.getElementById('vqTimestampMinutes').value,
        timestamp_seconds_extra: document.getElementById('vqTimestampSeconds').value,
        question_source: source,
        on_wrong: document.getElementById('vqOnWrong').value,
        rewind_seconds: document.getElementById('vqRewindSeconds').value || 0,
        points: document.getElementById('vqPoints').value || 1,
        show_count: document.getElementById('vqShowCount').value !== undefined ? document.getElementById('vqShowCount').value : '1'
    };
    if (source === 'bank') {
        payload.question_id = document.getElementById('vqQuestionId').value;
        if (!payload.question_id) { alert('اختر سؤالاً من البنك'); return; }
    } else {
        payload.custom_question_text = document.getElementById('vqCustomText').value.trim();
        payload.custom_correct_answer = document.getElementById('vqCustomCorrect').value.trim();
        var opts = document.getElementById('vqCustomOptions').value.split(/\n/).map(function(s) { return s.trim(); }).filter(Boolean);
        payload.custom_options = opts;
        if (!payload.custom_question_text || !payload.custom_correct_answer) { alert('أدخل نص السؤال والإجابة الصحيحة'); return; }
    }
    try {
        var res = await fetch('/instructor/lectures/' + videoQuestionsLectureId + '/video-questions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        var data = await res.json();
        if (!data.success) { alert(data.message || 'فشل الإضافة'); return; }
        var q = data.question;
        var listEl = document.getElementById('videoQuestionsList');
        if (listEl.querySelector('p')) listEl.innerHTML = '';
        var row = document.createElement('div');
        row.className = 'flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200';
        row.innerHTML = '<div class="min-w-0 flex-1"><span class="font-semibold text-slate-800">' + (q.timestamp_label || '0:00') + '</span><span class="text-slate-500 mx-2">—</span><span class="text-slate-700 text-sm truncate">' + (q.question_text || '').substring(0, 60) + (q.question_text && q.question_text.length > 60 ? '...' : '') + '</span><span class="text-xs text-slate-400 mr-2">(' + (q.on_wrong === 'rewind' ? 'إعادة' : 'متابعة') + ')</span><span class="text-xs text-amber-600 mr-2">· ' + (q.show_count_label || 'مرة واحدة') + '</span></div><button type="button" onclick="deleteVideoQuestion(' + videoQuestionsLectureId + ',' + q.id + ')" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm"><i class="fas fa-trash"></i></button>';
        listEl.appendChild(row);
        form.reset();
        document.getElementById('vqTimestampMinutes').value = 0;
        document.getElementById('vqTimestampSeconds').value = 0;
        document.getElementById('vqPoints').value = 1;
        document.getElementById('vqShowCount').value = '1';
        var showAtEndEl = document.getElementById('vqShowAtEnd');
        if (showAtEndEl) { showAtEndEl.checked = false; toggleVqTimestampFields(); }
    } catch (err) {
        alert('حدث خطأ: ' + (err.message || 'فشل الإضافة'));
    }
}

async function deleteVideoQuestion(lectureId, vqId) {
    if (!confirm('حذف هذا السؤال؟')) return;
    try {
        var res = await fetch('/instructor/lectures/' + lectureId + '/video-questions/' + vqId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
        });
        var data = await res.json();
        if (data.success) loadVideoQuestionsData(lectureId);
        else alert(data.message || 'فشل الحذف');
    } catch (e) {
        alert('فشل الحذف');
    }
}

function closeLectureModal() {
    document.getElementById('lectureModal').classList.add('hidden');
    document.getElementById('lectureModal').classList.remove('flex');
    document.getElementById('lectureForm').reset();
    document.getElementById('lectureEditId').value = '';
    document.getElementById('lectureSectionId').value = '';
    currentSectionId = null;
    // إعادة صفوف المواد إلى صف واحد
    var matContainer = document.getElementById('curriculum-materials-container');
    if (matContainer) {
        var rows = matContainer.querySelectorAll('.curriculum-material-row');
        for (var i = 1; i < rows.length; i++) rows[i].remove();
        var firstRemove = matContainer.querySelector('.curriculum-remove-material');
        if (firstRemove) firstRemove.style.display = 'none';
    }
    // إعادة تعيين المشغل
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
}

(function initCurriculumMaterials() {
    var container = document.getElementById('curriculum-materials-container');
    var addBtn = document.getElementById('curriculum-add-material');
    if (container && addBtn) {
        addBtn.addEventListener('click', function() {
            var first = container.querySelector('.curriculum-material-row');
            if (!first) return;
            var clone = first.cloneNode(true);
            clone.querySelector('input[type="file"]').value = '';
            clone.querySelector('input[type="text"]').value = '';
            clone.querySelector('input[type="checkbox"]').checked = true;
            var removeBtn = clone.querySelector('.curriculum-remove-material');
            if (removeBtn) removeBtn.style.display = 'inline-flex';
            container.appendChild(clone);
        });
        container.addEventListener('click', function(e) {
            if (e.target.closest('.curriculum-remove-material')) {
                var row = e.target.closest('.curriculum-material-row');
                if (container.querySelectorAll('.curriculum-material-row').length > 1) row.remove();
            }
        });
    }
})();

function saveLecture(e) {
    e.preventDefault();
    const form = document.getElementById('lectureForm');
    const formData = new FormData(form);
    const sectionId = document.getElementById('lectureSectionId').value;
    const lectureId = document.getElementById('lectureEditId').value;
    
    // التأكد من أن video_platform و recording_url موجودان في البيانات
    const videoPlatformInput = document.getElementById('lectureVideoPlatform');
    const recordingUrlInput = document.getElementById('lectureRecordingUrl');
    
    const videoPlatform = selectedVideoPlatform || (videoPlatformInput ? videoPlatformInput.value : '');
    const recordingUrl = recordingUrlInput ? recordingUrlInput.value.trim() : '';
    
    console.log('Form data before save:');
    console.log('- video_platform:', videoPlatform);
    console.log('- recording_url:', recordingUrl);
    console.log('- selectedVideoPlatform:', selectedVideoPlatform);
    console.log('- videoPlatformInput value:', videoPlatformInput ? videoPlatformInput.value : 'N/A');
    console.log('- recordingUrlInput value:', recordingUrlInput ? recordingUrlInput.value : 'N/A');
    
    // التأكد من إضافة recording_url إلى formData
    if (recordingUrl) {
        formData.set('recording_url', recordingUrl);
        console.log('Set recording_url in formData:', recordingUrl);
    } else {
        // إذا كان فارغاً، أرسل string فارغ
        formData.set('recording_url', '');
        console.log('Set recording_url to empty string');
    }
    
    // التأكد من إضافة video_platform إلى formData
    if (videoPlatform) {
        formData.set('video_platform', videoPlatform);
        console.log('Set video_platform in formData:', videoPlatform);
    } else if (recordingUrl) {
        // إذا لم يكن platform محدداً لكن يوجد رابط، حاول اكتشافه
        console.log('No platform selected, trying to auto-detect from URL');
        let detectedPlatform = '';
        if (recordingUrl.includes('youtube.com') || recordingUrl.includes('youtu.be')) {
            detectedPlatform = 'youtube';
        } else if (recordingUrl.includes('vimeo.com')) {
            detectedPlatform = 'vimeo';
        } else if (recordingUrl.includes('drive.google.com')) {
            detectedPlatform = 'google_drive';
        } else if (recordingUrl.includes('mediadelivery.net')) {
            detectedPlatform = 'bunny';
        } else if (recordingUrl.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
            detectedPlatform = 'direct';
        }
        
        if (detectedPlatform) {
            formData.set('video_platform', detectedPlatform);
            console.log('Auto-detected and set video_platform:', detectedPlatform);
        }
    } else {
        // إذا لم يكن هناك رابط ولا platform، أرسل string فارغ
        formData.set('video_platform', '');
        console.log('Set video_platform to empty string');
    }
    
    // طباعة جميع البيانات في formData للتحقق
    console.log('All formData entries:');
    for (let pair of formData.entries()) {
        console.log('- ' + pair[0] + ': ' + pair[1]);
    }
    
    // إضافة section_id للبيانات
    formData.append('section_id', sectionId);

    var minWatchVal = document.getElementById('lectureMinWatchPercent') ? document.getElementById('lectureMinWatchPercent').value.trim() : '';
    formData.set('min_watch_percent_to_unlock_next', minWatchVal === '' ? '' : minWatchVal);

    // مواد المحاضرة: إرسال فقط الصفوف التي تحتوي ملفاً حتى تتطابق المؤشرات مع الـ backend
    formData.delete('material_files[]');
    formData.delete('material_titles[]');
    formData.delete('material_visible[]');
    var materialRows = document.querySelectorAll('#curriculum-materials-container .curriculum-material-row');
    materialRows.forEach(function(row) {
        var fileInput = row.querySelector('input[type="file"]');
        var titleInput = row.querySelector('input[type="text"]');
        var visibleCb = row.querySelector('input[type="checkbox"]');
        if (!fileInput || !fileInput.files || !fileInput.files[0]) return;
        formData.append('material_files[]', fileInput.files[0]);
        formData.append('material_titles[]', titleInput ? titleInput.value : '');
        formData.append('material_visible[]', (visibleCb && visibleCb.checked) ? '1' : '0');
    });
    
    // تحديد URL والـ method
    let url = '<?php echo e(route("instructor.lectures.store")); ?>';
    let method = 'POST';
    
    if (lectureId) {
        url = `/instructor/lectures/${lectureId}`;
        formData.append('_method', 'PUT');
        // إرسال كـ POST مع _method=PUT لأن PHP لا يفسر body طلبات PUT multipart/form-data
        method = 'POST';
        console.log('Updating lecture:', lectureId);
    } else {
        console.log('Creating new lecture');
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        const contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return res.json();
        } else {
            const text = await res.text();
            throw new Error('Expected JSON but got HTML');
        }
    })
    .then(data => {
        console.log('Save response:', data);
        if (data.success || (lectureId && !data.error)) {
            if (lectureId) {
                // تم التعديل - إعادة تحميل الصفحة
                console.log('Lecture updated successfully, reloading page...');
                location.reload();
            } else {
                // إذا تم إنشاء المحاضرة بنجاح، أضفها تلقائياً للقسم
                if (data.lecture && data.lecture.id && sectionId) {
                    console.log('Lecture created successfully, adding to section...');
                    addLectureToSection(data.lecture.id, sectionId);
                } else {
                    console.log('Lecture created but no section ID, reloading page...');
                    location.reload();
                }
            }
        } else {
            console.error('Save failed:', data);
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(err => {
        console.error('Error saving lecture:', err);
        console.error('Error details:', err.message, err.stack);
        
        // إذا كان هناك أخطاء في التحقق من Laravel
        if (err.message && err.message.includes('422')) {
            fetch('<?php echo e(route("instructor.lectures.store")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.errors) {
                    let errorMsg = 'أخطاء في النموذج:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => errorMsg += error + '\n');
                    });
                    alert(errorMsg);
                }
            });
        } else {
            alert('حدث خطأ أثناء حفظ المحاضرة');
        }
    });
}

function addLectureToSection(lectureId, sectionId) {
    fetch(`/instructor/sections/${sectionId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            item_type: 'App\\Models\\Lecture',
            item_id: lectureId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('تم إنشاء المحاضرة لكن حدث خطأ في إضافتها للمنهج: ' + (data.message || ''));
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('تم إنشاء المحاضرة لكن حدث خطأ في إضافتها للمنهج');
        location.reload();
    });
}

// اختيار المشغل للفيديو
let selectedVideoPlatform = '';
function selectVideoPlatform(platform, button) {
    selectedVideoPlatform = platform;
    const platformInput = document.getElementById('lectureVideoPlatform');
    if (platformInput) {
        platformInput.value = platform;
        console.log('Platform selected:', platform, 'Input value set to:', platformInput.value);
    } else {
        console.error('lectureVideoPlatform input not found!');
    }
    
    // تحديث الأزرار
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
    if (button) {
        button.classList.remove('border-slate-200');
        button.classList.add('border-sky-500', 'bg-sky-50');
    }
    
    // تحديث placeholder
    const placeholder = document.getElementById('lectureVideoPlaceholder');
    const input = document.getElementById('lectureRecordingUrl');
    
    if (input && placeholder) {
        // حفظ القيمة الحالية قبل التحديث
        const currentValue = input.value;
        
        switch(platform) {
            case 'youtube':
                placeholder.textContent = 'مثال: https://www.youtube.com/watch?v=VIDEO_ID أو https://youtu.be/VIDEO_ID';
                input.placeholder = 'الصق رابط YouTube هنا...';
                break;
            case 'vimeo':
                placeholder.textContent = 'مثال: https://vimeo.com/VIDEO_ID';
                input.placeholder = 'الصق رابط Vimeo هنا...';
                break;
            case 'google_drive':
                placeholder.textContent = 'مثال: https://drive.google.com/file/d/FILE_ID/view';
                input.placeholder = 'الصق رابط Google Drive هنا...';
                break;
            case 'direct':
                placeholder.textContent = 'مثال: https://example.com/video.mp4';
                input.placeholder = 'الصق رابط الفيديو المباشر هنا...';
                break;
            case 'bunny':
                placeholder.textContent = 'مثال: https://iframe.mediadelivery.net/embed/LIBRARY_ID/VIDEO_ID أو player.mediadelivery.net/embed/...';
                input.placeholder = 'الصق رابط Bunny.net (embed) هنا...';
                break;
            default:
                placeholder.textContent = '';
                input.placeholder = 'ضع رابط الفيديو هنا...';
        }
        
        // استعادة القيمة إذا كانت موجودة (للتعديل)
        if (currentValue) {
            input.value = currentValue;
            console.log('Preserved recording_url value:', currentValue);
        } else {
            // مسح المعاينة السابقة فقط إذا لم تكن هناك قيمة
            document.getElementById('lectureVideoPreview').classList.add('hidden');
        }
    }
}

// معاينة الفيديو
function previewLectureVideo() {
    const url = document.getElementById('lectureRecordingUrl').value.trim();
    let platform = selectedVideoPlatform || document.getElementById('lectureVideoPlatform').value;
    
    // إذا لم يكن platform محدداً، حاول اكتشافه تلقائياً
    if (!platform && url) {
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            platform = 'youtube';
            selectedVideoPlatform = 'youtube';
            document.getElementById('lectureVideoPlatform').value = 'youtube';
            // تحديث زر YouTube
            const youtubeBtn = document.querySelector('[data-platform="youtube"]');
            if (youtubeBtn) {
                selectVideoPlatform('youtube', youtubeBtn);
            }
        } else if (url.includes('vimeo.com')) {
            platform = 'vimeo';
            selectedVideoPlatform = 'vimeo';
            document.getElementById('lectureVideoPlatform').value = 'vimeo';
            const vimeoBtn = document.querySelector('[data-platform="vimeo"]');
            if (vimeoBtn) {
                selectVideoPlatform('vimeo', vimeoBtn);
            }
        } else if (url.includes('drive.google.com')) {
            platform = 'google_drive';
            selectedVideoPlatform = 'google_drive';
            document.getElementById('lectureVideoPlatform').value = 'google_drive';
            const driveBtn = document.querySelector('[data-platform="google_drive"]');
            if (driveBtn) {
                selectVideoPlatform('google_drive', driveBtn);
            }
        } else if (url.includes('mediadelivery.net')) {
            platform = 'bunny';
            selectedVideoPlatform = 'bunny';
            document.getElementById('lectureVideoPlatform').value = 'bunny';
            const bunnyBtn = document.querySelector('[data-platform="bunny"]');
            if (bunnyBtn) {
                selectVideoPlatform('bunny', bunnyBtn);
            }
        } else if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
            platform = 'direct';
            selectedVideoPlatform = 'direct';
            document.getElementById('lectureVideoPlatform').value = 'direct';
            const directBtn = document.querySelector('[data-platform="direct"]');
            if (directBtn) {
                selectVideoPlatform('direct', directBtn);
            }
        }
    }
    
    const previewDiv = document.getElementById('lectureVideoPreview');
    const previewContent = document.getElementById('lectureVideoPreviewContent');
    
    if (!url || !platform) {
        previewDiv.classList.add('hidden');
        return;
    }
    
    previewDiv.classList.remove('hidden');
    previewContent.innerHTML = '<i class="fas fa-spinner fa-spin text-2xl"></i>';
    
    let html = '';
    let isValid = false;
    
    try {
        // YouTube
        if (platform === 'youtube') {
            let videoId = null;
            
            // نمط 1: youtube.com/watch?v=VIDEO_ID أو youtube.com/watch?v=VIDEO_ID&si=...
            const watchMatch = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
            if (watchMatch && watchMatch[1]) {
                videoId = watchMatch[1];
            }
            
            // نمط 2: youtu.be/VIDEO_ID أو youtu.be/VIDEO_ID?si=...
            if (!videoId) {
                const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
                if (shortMatch && shortMatch[1]) {
                    videoId = shortMatch[1];
                }
            }
            
            // نمط 3: youtube.com/embed/VIDEO_ID
            if (!videoId) {
                const embedMatch = url.match(/embed\/([a-zA-Z0-9_-]{11})/);
                if (embedMatch && embedMatch[1]) {
                    videoId = embedMatch[1];
                }
            }
            
            // نمط 4: youtube.com/v/VIDEO_ID
            if (!videoId) {
                const vMatch = url.match(/\/v\/([a-zA-Z0-9_-]{11})/);
                if (vMatch && vMatch[1]) {
                    videoId = vMatch[1];
                }
            }
            
            // نمط 5: أي رابط يحتوي على 11 حرف/رقم متتالي (video ID) في رابط YouTube
            if (!videoId) {
                const genericMatch = url.match(/([a-zA-Z0-9_-]{11})/);
                if (genericMatch && genericMatch[1] && (url.includes('youtube') || url.includes('youtu.be'))) {
                    videoId = genericMatch[1];
                }
            }
            
            if (videoId && videoId.length === 11) {
                isValid = true;
                const origin = encodeURIComponent(window.location.origin);
                html = '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '&autoplay=0" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            } else {
                console.error('Could not extract YouTube video ID from:', url);
            }
        }
        // Vimeo
        else if (platform === 'vimeo') {
            const pattern = /vimeo\.com\/(?:.*\/)?(\d+)/;
            const match = url.match(pattern);
            if (match && match[1]) {
                isValid = true;
                html = '<iframe src="https://player.vimeo.com/video/' + match[1] + '?title=0&byline=0&portrait=0&controls=1" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            }
        }
        // Google Drive
        else if (platform === 'google_drive') {
            const pattern = /drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/;
            const match = url.match(pattern);
            if (match && match[1]) {
                isValid = true;
                html = '<iframe src="https://drive.google.com/file/d/' + match[1] + '/preview" width="100%" height="100%" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
            }
        }
        // Direct Video
        else if (platform === 'direct') {
            const pattern = /\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i;
            if (pattern.test(url)) {
                isValid = true;
                const escapedUrl = url.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                html = '<video controls width="100%" height="100%" style="max-height: 100%; border-radius: 0.75rem;" class="w-full h-full"><source src="' + escapedUrl + '" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>';
            }
        }
        // Bunny.net (Bunny Stream) - أي نطاق mediadelivery.net مع مسار embed
        else if (platform === 'bunny') {
            const bunnyMatch = url.match(/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/);
            if (bunnyMatch && bunnyMatch[1] && bunnyMatch[2]) {
                isValid = true;
                const embedUrl = url.split('?')[0];
                const src = embedUrl.startsWith('http') ? embedUrl : ('https://' + embedUrl.replace(/^\/+/, ''));
                html = '<iframe src="' + src.replace(/"/g, '&quot;') + '" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            }
        }
        
        if (isValid && html) {
            previewContent.innerHTML = html;
        } else {
            previewContent.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-triangle text-yellow-400 text-2xl mb-2"></i><p class="text-sm">الرابط غير صحيح أو غير مدعوم</p></div>';
        }
    } catch (error) {
        console.error('Error generating preview:', error);
        previewContent.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-circle text-red-400 text-2xl mb-2"></i><p class="text-sm">حدث خطأ في عرض المعاينة</p></div>';
    }
}

// إعادة تعيين عند إغلاق modal
function closeLectureModal() {
    document.getElementById('lectureModal').classList.add('hidden');
    document.getElementById('lectureForm').reset();
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
}

// ——— سحب وإفلات المنهج (أقسام + عناصر) ———
(function curriculumSortable() {
    if (typeof Sortable === 'undefined') return;
    var courseId = <?php echo e($course->id); ?>;
    var sectionsOrderUrl = '<?php echo e(route("instructor.courses.sections.order", $course)); ?>';
    var moveItemUrlBase = '<?php echo e(url("instructor/curriculum-items")); ?>';
    var token = '<?php echo e(csrf_token()); ?>';

    function showToast(msg, isError) {
        var t = document.createElement('div');
        t.className = 'fixed bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded-xl text-white text-sm font-semibold shadow-lg z-[9999] ' + (isError ? 'bg-red-500' : 'bg-emerald-500');
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function() { t.remove(); }, 2500);
    }

    function buildSectionsPayload() {
        var sections = [];
        var root = document.getElementById('sections-container');
        if (!root) return sections;
        var rootBlocks = root.querySelectorAll(':scope > .section-block');
        rootBlocks.forEach(function(el, i) {
            sections.push({ id: parseInt(el.dataset.sectionId, 10), order: i, parent_id: null });
        });
        document.querySelectorAll('.sections-children').forEach(function(container) {
            var parentId = parseInt(container.dataset.parentId, 10);
            var blocks = container.querySelectorAll(':scope > .section-block');
            blocks.forEach(function(el, i) {
                sections.push({ id: parseInt(el.dataset.sectionId, 10), order: i, parent_id: parentId });
            });
        });
        return sections;
    }

    function saveSectionsOrder() {
        var payload = buildSectionsPayload();
        if (payload.length === 0) return;
        fetch(sectionsOrderUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ sections: payload })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) showToast('تم تحديث ترتيب الأقسام');
            else showToast(data.message || 'حدث خطأ', true);
        })
        .catch(function() { showToast('حدث خطأ في الاتصال', true); });
    }

    function saveItemsOrder(sectionId, itemIds) {
        var items = itemIds.map(function(id, i) { return { id: id, order: i }; });
        var url = '/instructor/sections/' + sectionId + '/items/order';
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ items: items })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) showToast('تم تحديث الترتيب');
            else showToast(data.message || 'حدث خطأ', true);
        })
        .catch(function() { showToast('حدث خطأ في الاتصال', true); });
    }

    function moveCurriculumItem(itemId, sectionId, order) {
        fetch(moveItemUrlBase + '/' + itemId + '/move', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ section_id: sectionId, order: order })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) showToast('تم نقل العنصر');
            else showToast(data.message || 'حدث خطأ', true);
        })
        .catch(function() { showToast('حدث خطأ في الاتصال', true); });
    }

    var sectionOpts = {
        group: 'sections',
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        draggable: '.section-block',
        onStart: function() { document.body.classList.add('curriculum-dragging'); },
        onEnd: function() {
            document.body.classList.remove('curriculum-dragging');
            saveSectionsOrder();
        }
    };

    var itemOpts = {
        group: 'curriculum-items',
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        draggable: '.item-card',
        onStart: function() { document.body.classList.add('curriculum-dragging'); },
        onEnd: function(evt) {
            document.body.classList.remove('curriculum-dragging');
            var from = evt.from;
            var to = evt.to;
            var sectionId = to.dataset.sectionId;
            if (!sectionId) return;
            if (from === to) {
                var ids = [];
                to.querySelectorAll('.item-card').forEach(function(card) {
                    var id = card.dataset.itemId;
                    if (id) ids.push(parseInt(id, 10));
                });
                if (ids.length) saveItemsOrder(sectionId, ids);
            } else {
                var itemId = evt.item.dataset.itemId;
                if (itemId) moveCurriculumItem(parseInt(itemId, 10), parseInt(sectionId, 10), evt.newIndex);
                var fromIds = [];
                from.querySelectorAll('.item-card').forEach(function(card) {
                    var id = card.dataset.itemId;
                    if (id) fromIds.push(parseInt(id, 10));
                });
                if (fromIds.length && from.dataset.sectionId) saveItemsOrder(from.dataset.sectionId, fromIds);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        var root = document.getElementById('sections-container');
        if (root) Sortable.create(root, sectionOpts);
        document.querySelectorAll('.sections-children').forEach(function(el) {
            Sortable.create(el, sectionOpts);
        });
        document.querySelectorAll('.items-container').forEach(function(el) {
            Sortable.create(el, itemOpts);
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/curriculum/index.blade.php ENDPATH**/ ?>