<?php
    $depth = $depth ?? 0;
    $marginClass = $depth > 0 ? 'mr-4 border-r-2 border-slate-200' : '';
    $isScholarshipCurriculum = $isScholarshipCurriculum ?? false;
    $sectionScope = $section->visibility_scope ?? 'all';
    $sectionSelectedCount = $section->relationLoaded('visibleStudents') ? $section->visibleStudents->count() : 0;
?>
<div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow mb-4 section-block <?php echo e($marginClass); ?>" data-section-id="<?php echo e($section->id); ?>" style="<?php echo e($depth > 0 ? 'margin-right: ' . ($depth * 1.5) . 'rem;' : ''); ?>">
    <div class="flex items-center justify-between p-4 cursor-pointer section-header" onclick="toggleSection(<?php echo e($section->id); ?>)">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <span class="section-chevron text-slate-400 transition-transform duration-200" data-section-id="<?php echo e($section->id); ?>">
                <i class="fas fa-chevron-down"></i>
            </span>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-lg font-bold text-slate-800"><?php echo e($section->title); ?></h3>
                    <?php if($isScholarshipCurriculum && $sectionScope === 'selected'): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] font-semibold" title="ظاهر لطلبة محددين">
                            <i class="fas fa-user-check"></i>
                            <?php echo e($sectionSelectedCount); ?> طالب
                        </span>
                    <?php elseif($isScholarshipCurriculum && $sectionScope === 'groups'): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-semibold" title="ظاهر لمجموعات محددة">
                            <i class="fas fa-layer-group"></i>
                            مجموعات
                        </span>
                    <?php endif; ?>
                </div>
                <?php if($section->description): ?>
                    <p class="text-sm text-slate-500 truncate"><?php echo e($section->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0" onclick="event.stopPropagation();">
            <?php if($isScholarshipCurriculum): ?>
                <button type="button" onclick="event.stopPropagation(); openAccessVisibilityModal('section', <?php echo e($section->id); ?>, '<?php echo e(addslashes($section->title)); ?>')"
                        class="p-2 rounded-lg <?php echo e($sectionScope === 'selected' ? 'bg-indigo-200 text-indigo-800' : 'bg-indigo-100 text-indigo-700'); ?> hover:bg-indigo-200 text-sm transition-colors"
                        title="إعداد الوصول للقسم">
                    <i class="fas fa-user-lock"></i>
                </button>
            <?php endif; ?>
            <button onclick="event.stopPropagation(); editSection(<?php echo e($section->id); ?>, '<?php echo e(addslashes($section->title)); ?>', '<?php echo e(addslashes($section->description ?? '')); ?>', <?php echo e($section->parent_id ?? 'null'); ?>, '<?php echo e($section->unlock_rule ?? 'previous_all_items'); ?>', <?php echo e($section->unlock_percent !== null ? (int)$section->unlock_percent : 'null'); ?>)"
                    class="p-2 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-600 text-sm transition-colors" title="تعديل القسم">
                <i class="fas fa-edit"></i>
            </button>
            <button onclick="event.stopPropagation(); deleteSection(<?php echo e($section->id); ?>)"
                    class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-sm transition-colors" title="حذف القسم">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <div class="section-body px-4 pb-4 border-t border-slate-100">
        <div class="mb-4 flex flex-wrap items-center gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200 mt-4">
            <span class="text-xs font-semibold text-slate-600 mr-2">إضافة:</span>
            <button type="button" onclick="event.stopPropagation(); showAddSubSectionModal(<?php echo e($section->id); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition-colors"
                    title="قسم فرعي داخل هذا القسم">
                <i class="fas fa-folder-plus"></i>
                <span>قسم فرعي</span>
            </button>
            <button onclick="event.stopPropagation(); showAddLectureModal(<?php echo e($section->id); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>محاضرة</span>
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddExamModal(<?php echo e($section->id); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-clipboard-check"></i>
                <span>امتحان</span>
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddAssignmentModal(<?php echo e($section->id); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-tasks"></i>
                <span>واجب</span>
            </button>
            <a href="<?php echo e(route('instructor.learning-patterns.create', $course)); ?>?section_id=<?php echo e($section->id); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold transition-colors"
               title="إضافة نمط تعليمي تفاعلي">
                <i class="fas fa-puzzle-piece"></i>
                <span>نمط تعليمي</span>
            </a>
        </div>

        <div class="items-container" data-section-id="<?php echo e($section->id); ?>">
            <?php $sectionItems = $section->items->filter(fn($i) => !($i->item instanceof \App\Models\CourseLesson)); ?>
            <?php $__empty_1 = true; $__currentLoopData = $sectionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="item-card rounded-lg p-3 mb-2 bg-white border border-slate-200 hover:border-sky-300 hover:shadow-sm transition-all cursor-move" data-item-id="<?php echo e($item->id); ?>">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <i class="fas fa-grip-vertical text-slate-400 cursor-move shrink-0"></i>
                            <?php if($item->item instanceof \App\Models\Lecture): ?>
                                <i class="fas fa-chalkboard-teacher text-sky-500 shrink-0"></i>
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-semibold text-slate-800 truncate"><?php echo e($item->item->title); ?></span>
                                    <?php if($item->item->videoQuestions()->exists()): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] shrink-0" title="توجد أسئلة مرتبطة بالفيديو">
                                            <i class="fas fa-question-circle"></i>
                                            <span>أسئلة فيديو</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if($isScholarshipCurriculum && in_array(($item->visibility_scope ?? 'all'), ['selected', 'groups'], true)): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] shrink-0" title="وصول مقيّد">
                                            <i class="fas fa-user-lock"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs text-slate-500 shrink-0">(محاضرة)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <?php if($isScholarshipCurriculum): ?>
                                        <button type="button" onclick="openAccessVisibilityModal('item', <?php echo e($item->id); ?>, '<?php echo e(addslashes($item->item->title ?? 'عنصر')); ?>')" class="p-1.5 rounded bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs" title="إعداد الوصول"><i class="fas fa-user-lock"></i></button>
                                    <?php endif; ?>
                                    <button type="button" onclick="openVideoQuestionsModal(<?php echo e($item->item->id); ?>, '<?php echo e(addslashes($item->item->title)); ?>')" class="p-1.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs" title="أسئلة الفيديو"><i class="fas fa-question-circle"></i></button>
                                    <button onclick="editLectureFromCurriculum(<?php echo e($item->item->id); ?>, <?php echo e($section->id); ?>)" class="p-1.5 rounded bg-sky-100 hover:bg-sky-200 text-sky-600 text-xs" title="تعديل المحاضرة"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteLectureFromCurriculum(<?php echo e($item->item->id); ?>, <?php echo e($item->id); ?>)" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="حذف المحاضرة"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php elseif($item->item instanceof \App\Models\Assignment): ?>
                                <i class="fas fa-tasks text-emerald-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate"><?php echo e($item->item->title); ?></span>
                                <?php if($isScholarshipCurriculum && in_array(($item->visibility_scope ?? 'all'), ['selected', 'groups'], true)): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] shrink-0" title="ظاهر لطلبة محددين">
                                        <i class="fas fa-user-check"></i>
                                    </span>
                                <?php endif; ?>
                                <span class="text-xs text-slate-500 shrink-0">(واجب)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <?php if($isScholarshipCurriculum): ?>
                                        <button type="button" onclick="openAccessVisibilityModal('item', <?php echo e($item->id); ?>, '<?php echo e(addslashes($item->item->title ?? 'عنصر')); ?>')" class="p-1.5 rounded bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs" title="إعداد الوصول"><i class="fas fa-user-lock"></i></button>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('instructor.assignments.edit', $item->item)); ?>" class="p-1.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-600 text-xs" title="تعديل الواجب"><i class="fas fa-edit"></i></a>
                                    <button onclick="removeItem(<?php echo e($item->id); ?>)" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            <?php elseif($item->item instanceof \App\Models\AdvancedExam || $item->item instanceof \App\Models\Exam): ?>
                                <i class="fas fa-clipboard-check text-violet-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate"><?php echo e($item->item->title); ?></span>
                                <?php if($isScholarshipCurriculum && in_array(($item->visibility_scope ?? 'all'), ['selected', 'groups'], true)): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] shrink-0" title="ظاهر لطلبة محددين">
                                        <i class="fas fa-user-check"></i>
                                    </span>
                                <?php endif; ?>
                                <span class="text-xs text-slate-500 shrink-0">(امتحان)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <?php if($isScholarshipCurriculum): ?>
                                        <button type="button" onclick="openAccessVisibilityModal('item', <?php echo e($item->id); ?>, '<?php echo e(addslashes($item->item->title ?? 'عنصر')); ?>')" class="p-1.5 rounded bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs" title="إعداد الوصول"><i class="fas fa-user-lock"></i></button>
                                    <?php endif; ?>
                                    <?php if($item->item instanceof \App\Models\AdvancedExam): ?>
                                        <a href="<?php echo e(route('instructor.exams.edit', $item->item)); ?>" class="p-1.5 rounded bg-violet-100 hover:bg-violet-200 text-violet-600 text-xs" title="تعديل الامتحان"><i class="fas fa-edit"></i></a>
                                    <?php endif; ?>
                                    <button onclick="removeItem(<?php echo e($item->id); ?>)" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            <?php elseif($item->item instanceof \App\Models\LearningPattern): ?>
                                <?php $typeInfo = $item->item->getTypeInfo(); ?>
                                <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?> text-amber-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate"><?php echo e($item->item->title); ?></span>
                                <?php if($isScholarshipCurriculum && in_array(($item->visibility_scope ?? 'all'), ['selected', 'groups'], true)): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-[10px] shrink-0" title="ظاهر لطلبة محددين">
                                        <i class="fas fa-user-check"></i>
                                    </span>
                                <?php endif; ?>
                                <span class="text-xs text-slate-500 shrink-0">(<?php echo e($typeInfo['name'] ?? 'نمط تعليمي'); ?>)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <?php if($isScholarshipCurriculum): ?>
                                        <button type="button" onclick="openAccessVisibilityModal('item', <?php echo e($item->id); ?>, '<?php echo e(addslashes($item->item->title ?? 'عنصر')); ?>')" class="p-1.5 rounded bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs" title="إعداد الوصول"><i class="fas fa-user-lock"></i></button>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('instructor.learning-patterns.edit', [$course, $item->item])); ?>" class="p-1.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-600 text-xs" title="تعديل النمط"><i class="fas fa-edit"></i></a>
                                    <button onclick="removeItem(<?php echo e($item->id); ?>)" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-6 text-slate-500 border border-dashed border-slate-200 rounded-lg bg-slate-50/50">
                    <i class="fas fa-inbox text-2xl mb-2 text-slate-400"></i>
                    <p class="text-sm mb-1">لا توجد عناصر في هذا القسم</p>
                    <p class="text-xs text-slate-400">أضف محاضرات أو امتحانات أو واجبات من الأزرار أعلاه</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($section->children && $section->children->count() > 0): ?>
            <div class="sections-children mt-4 pr-4 border-r-2 border-slate-100 space-y-4" data-parent-id="<?php echo e($section->id); ?>" style="margin-right: 1rem;">
                <?php $__currentLoopData = $section->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('instructor.curriculum.partials.section', ['section' => $child, 'depth' => $depth + 1, 'isScholarshipCurriculum' => $isScholarshipCurriculum, 'course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="sections-children empty-drop-zone mt-4 pr-4 border-r-2 border-slate-100 border-dashed min-h-[52px] rounded-lg bg-slate-50/70 flex items-center justify-center transition-all" data-parent-id="<?php echo e($section->id); ?>" style="margin-right: 1rem;" data-empty="1"><span class="text-xs text-slate-400 opacity-0 group-hover:opacity-100 curriculum-drag-hint">أفلت قسم هنا</span></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\curriculum\partials\section.blade.php ENDPATH**/ ?>