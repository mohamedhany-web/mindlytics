<?php
    $ms = ($depth ?? 0) * 14;
    $curriculumChannel = $curriculumChannel ?? 'offline';
    $groupSessions = $groupSessions ?? collect();
?>
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" style="margin-inline-start: <?php echo e($ms); ?>px">
    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/80">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="text-lg font-bold text-slate-800"><?php echo e($section->title); ?></h3>
                <?php if($section->description && !$section->parent_id): ?>
                    <p class="text-sm text-slate-600 mt-1 whitespace-pre-line"><?php echo e($section->description); ?></p>
                <?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.move', [$offlineCourse, $section])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="up">
                    <button type="submit" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" title="أعلى"><i class="fas fa-chevron-up text-xs"></i></button>
                </form>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.move', [$offlineCourse, $section])); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="direction" value="down">
                    <button type="submit" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100" title="أسفل"><i class="fas fa-chevron-down text-xs"></i></button>
                </form>
                <details class="relative">
                    <summary class="list-none cursor-pointer p-2 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-sm font-semibold"><i class="fas fa-edit ml-1"></i> تعديل</summary>
                    <div class="absolute left-0 z-20 mt-1 w-72 rounded-xl border border-slate-200 bg-white shadow-lg p-3">
                        <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.update', [$offlineCourse, $section])); ?>" method="POST" class="space-y-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="text" name="title" value="<?php echo e($section->title); ?>" required class="w-full text-sm rounded-lg border-slate-200">
                            <?php if(!$section->parent_id): ?>
                                <textarea name="description" rows="2" class="w-full text-sm rounded-lg border-slate-200" placeholder="وصف"><?php echo e($section->description); ?></textarea>
                            <?php endif; ?>
                            <button type="submit" class="w-full py-2 rounded-lg bg-sky-500 text-white text-xs font-bold">حفظ</button>
                        </form>
                    </div>
                </details>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.destroy', [$offlineCourse, $section])); ?>" method="POST" class="inline" onsubmit="return confirm('حذف القسم وجميع الأقسام الفرعية والعناصر؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-2 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-200/80">
            <p class="text-xs font-bold text-slate-500 mb-2">قسم فرعي</p>
            <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.store', $offlineCourse)); ?>" method="POST" class="flex flex-wrap gap-2 items-end">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="parent_id" value="<?php echo e($section->id); ?>">
                <input type="text" name="title" required placeholder="عنوان القسم الفرعي" class="flex-1 min-w-[160px] text-sm rounded-lg border-slate-200 px-3 py-2">
                <button type="submit" class="px-3 py-2 rounded-lg bg-slate-700 text-white text-xs font-bold">إضافة</button>
            </form>
        </div>
    </div>

    <div class="p-4 sm:p-5 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">عناصر القسم</p>
            <div class="flex flex-wrap gap-1.5 text-[11px]">
                <button type="button"
                        onclick="openCurriculumLectureModal('<?php echo e($section->id); ?>')"
                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-violet-600 text-white border border-violet-700 font-semibold shadow-sm hover:bg-violet-700">
                    <i class="fas fa-plus ml-1"></i> محاضرة جديدة
                </button>
                <?php if(isset($lectures) && $lectures->isNotEmpty()): ?>
                    <button type="button"
                            class="px-2.5 py-1 rounded-lg bg-violet-50 text-violet-700 border border-violet-200 font-semibold"
                            onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','lecture')">
                        <i class="fas fa-link ml-1"></i> ربط محاضرة موجودة
                    </button>
                <?php endif; ?>
                <?php if(isset($resources) && $resources->isNotEmpty()): ?>
                    <button type="button"
                            class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 font-semibold"
                            onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','resource')">
                        <i class="fas fa-file-alt ml-1"></i> إضافة مورد
                    </button>
                <?php endif; ?>
                <?php if(isset($activities) && $activities->isNotEmpty()): ?>
                    <button type="button"
                            class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold"
                            onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','activity')">
                        <i class="fas fa-tasks ml-1"></i> إضافة نشاط
                    </button>
                <?php endif; ?>
                <?php if(isset($exams) && $exams->isNotEmpty()): ?>
                    <button type="button"
                            class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold"
                            onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','exam')">
                        <i class="fas fa-clipboard-check ml-1"></i> إضافة اختبار
                    </button>
                <?php endif; ?>
            </div>
        </div>

        
        <?php $attachUrl = $attachUrl ?? route('instructor.offline-courses.curriculum.attach-item', $offlineCourse); ?>

        <?php if(isset($lectures) && $lectures->isNotEmpty()): ?>
            <div id="attach-lecture-<?php echo e($section->id); ?>" class="hidden rounded-xl border border-violet-100 bg-violet-50/40 p-3 space-y-2">
                <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="offline_course_section_id" value="<?php echo e($section->id); ?>">
                    <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineLecture::class); ?>">
                    <label class="text-[11px] font-semibold text-violet-700">اختر المحاضرة</label>
                    <select name="item_id" class="w-full text-xs rounded-lg border-violet-200" required>
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lecture->id); ?>"><?php echo e($lecture->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="px-2 py-1 text-[11px] rounded-lg border border-violet-200 text-violet-700 bg-white"
                                onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','lecture')">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-3 py-1 text-[11px] rounded-lg bg-violet-600 text-white font-semibold">
                            ربط بالمناهج
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if(isset($resources) && $resources->isNotEmpty()): ?>
            <div id="attach-resource-<?php echo e($section->id); ?>" class="hidden rounded-xl border border-sky-100 bg-sky-50/40 p-3 space-y-2">
                <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="offline_course_section_id" value="<?php echo e($section->id); ?>">
                    <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineCourseResource::class); ?>">
                    <label class="text-[11px] font-semibold text-sky-700">اختر المورد</label>
                    <select name="item_id" class="w-full text-xs rounded-lg border-sky-200" required>
                        <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($resource->id); ?>"><?php echo e($resource->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="px-2 py-1 text-[11px] rounded-lg border border-sky-200 text-sky-700 bg-white"
                                onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','resource')">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-3 py-1 text-[11px] rounded-lg bg-sky-600 text-white font-semibold">
                            ربط بالمناهج
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if(isset($activities) && $activities->isNotEmpty()): ?>
            <div id="attach-activity-<?php echo e($section->id); ?>" class="hidden rounded-xl border border-amber-100 bg-amber-50/40 p-3 space-y-2">
                <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="offline_course_section_id" value="<?php echo e($section->id); ?>">
                    <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineActivity::class); ?>">
                    <label class="text-[11px] font-semibold text-amber-700">اختر النشاط</label>
                    <select name="item_id" class="w-full text-xs rounded-lg border-amber-200" required>
                        <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($activity->id); ?>"><?php echo e($activity->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="px-2 py-1 text-[11px] rounded-lg border border-amber-200 text-amber-700 bg-white"
                                onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','activity')">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-3 py-1 text-[11px] rounded-lg bg-amber-600 text-white font-semibold">
                            ربط بالمناهج
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if(isset($exams) && $exams->isNotEmpty()): ?>
            <div id="attach-exam-<?php echo e($section->id); ?>" class="hidden rounded-xl border border-emerald-100 bg-emerald-50/40 p-3 space-y-2">
                <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="offline_course_section_id" value="<?php echo e($section->id); ?>">
                    <input type="hidden" name="item_type" value="<?php echo e(\App\Models\AdvancedExam::class); ?>">
                    <label class="text-[11px] font-semibold text-emerald-700">اختر الاختبار</label>
                    <select name="item_id" class="w-full text-xs rounded-lg border-emerald-200" required>
                        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($exam->id); ?>"><?php echo e($exam->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="px-2 py-1 text-[11px] rounded-lg border border-emerald-200 text-emerald-700 bg-white"
                                onclick="toggleCurriculumAttach('<?php echo e($section->id); ?>','exam')">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-3 py-1 text-[11px] rounded-lg bg-emerald-600 text-white font-semibold">
                            ربط بالمناهج
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        
        <div id="lecture-modal-<?php echo e($section->id); ?>" class="fixed inset-0 z-[80] hidden lecture-curriculum-modal" role="dialog" aria-modal="true" aria-labelledby="lecture-modal-title-<?php echo e($section->id); ?>">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCurriculumLectureModal('<?php echo e($section->id); ?>')"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="pointer-events-auto w-full max-w-3xl max-h-[90vh] flex flex-col rounded-2xl shadow-2xl border border-slate-200 bg-white overflow-hidden">
                    <div class="shrink-0 flex items-start justify-between gap-4 px-5 py-4 border-b border-slate-200 bg-slate-50">
                        <div class="min-w-0">
                            <h4 id="lecture-modal-title-<?php echo e($section->id); ?>" class="text-lg font-bold text-slate-800">محاضرة جديدة</h4>
                            <p class="text-sm text-slate-500 mt-1">القسم: <span class="font-semibold text-slate-700"><?php echo e($section->title); ?></span> — اربط الجلسة ثم املأ التفاصيل.</p>
                        </div>
                        <button type="button" onclick="closeCurriculumLectureModal('<?php echo e($section->id); ?>')" class="p-2 rounded-xl text-slate-500 hover:bg-slate-200 hover:text-slate-800 shrink-0 transition-colors" aria-label="إغلاق">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto p-5 sm:p-6 bg-white">
                        <form action="<?php echo e(route('instructor.offline-courses.lectures.store', ['offlineCourse' => $offlineCourse, 'channel' => $curriculumChannel])); ?>"
                              method="post"
                              enctype="multipart/form-data"
                              class="space-y-5 text-sm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="curriculum_section_id" value="<?php echo e($section->id); ?>">
                            <?php echo $__env->make('instructor.offline-courses.lectures.partials.session-select', [
                                'groupSessions' => $groupSessions,
                                'required' => $groupSessions->isNotEmpty(),
                                'value' => null,
                                'variant' => 'modal',
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">المحتوى التعليمي</p>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">عنوان المحاضرة <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200" placeholder="مثال: اليوم الثالث — الوحدة 2">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">برنامج اليوم (سطر لكل نقطة)</label>
                                    <textarea name="session_agenda" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-mono shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200" placeholder="- نقطة 1&#10;- نقطة 2"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">الوصف</label>
                                    <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200" placeholder="أهداف أو ملخص للطلاب"></textarea>
                                </div>
                            </div>
                            <?php echo $__env->make('instructor.offline-courses.lectures.partials.offline-mindmap-field', ['variant' => 'modal', 'value' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">بعد الجلسة</p>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">رابط تسجيل المحاضرة</label>
                                    <input type="url" name="recording_url" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200" placeholder="https://...">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">مرفقات (اختياري)</label>
                                    <input type="file" name="attachments[]" multiple class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-slate-700 file:font-semibold">
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" onclick="closeCurriculumLectureModal('<?php echo e($section->id); ?>')" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">إلغاء</button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold shadow-sm">حفظ وربط بالقسم</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $m = $cItem->item;
                $label = $m && isset($m->title) ? $m->title : '(عنصر غير متوفر)';
                [$badgeLabel, $badgeClass] = match ($cItem->item_type) {
                    \App\Models\OfflineLecture::class => ['محاضرة', 'bg-violet-100 text-violet-800'],
                    \App\Models\OfflineCourseResource::class => ['مورد', 'bg-sky-100 text-sky-800'],
                    \App\Models\OfflineActivity::class => ['نشاط', 'bg-amber-100 text-amber-800'],
                    \App\Models\AdvancedExam::class => ['اختبار', 'bg-emerald-100 text-emerald-800'],
                    \App\Models\OfflineCurriculumNote::class => ['نص', 'bg-slate-200 text-slate-800'],
                    default => ['عنصر', 'bg-gray-100 text-gray-800'],
                };
                $isOfflineLecture = $cItem->item_type === \App\Models\OfflineLecture::class && $m instanceof \App\Models\OfflineLecture;
                $agendaLines = $isOfflineLecture && filled($m->session_agenda)
                    ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $m->session_agenda))))
                    : [];
            ?>
            <div class="flex flex-wrap items-start gap-2 p-3 rounded-xl border <?php echo e($isOfflineLecture ? 'border-violet-100 bg-violet-50/20' : 'border-slate-100 bg-slate-50/50'); ?>">
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md shrink-0 <?php echo e($badgeClass); ?>"><?php echo e($badgeLabel); ?></span>
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-semibold text-slate-800"><?php echo e($label); ?></span>
                        <?php if($isOfflineLecture): ?>
                            <a href="<?php echo e(route('instructor.offline-courses.lectures.edit', ['offlineCourse' => $offlineCourse, 'lecture' => $m, 'channel' => $curriculumChannel])); ?>"
                               class="text-[11px] font-semibold text-violet-600 hover:text-violet-800">تعديل المحاضرة</a>
                        <?php endif; ?>
                    </div>
                    <?php if($isOfflineLecture): ?>
                        <?php if($m->relationLoaded('groupSession') && $m->groupSession): ?>
                            <p class="text-[11px] font-semibold text-violet-800">
                                <i class="far fa-calendar-check ml-1"></i>
                                جلسة: <?php echo e($m->groupSession->session_date->format('Y/m/d')); ?>

                                <?php $gst = $m->groupSession->start_time; ?>
                                <?php echo e(is_string($gst) ? substr($gst, 0, 5) : $gst); ?>

                                <?php if($m->groupSession->group): ?> — <?php echo e($m->groupSession->group->name); ?> <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if($m->scheduled_at): ?>
                            <p class="text-[11px] text-slate-500"><i class="far fa-clock ml-1"></i><?php echo e($m->scheduled_at->translatedFormat('l j F Y — H:i')); ?></p>
                        <?php endif; ?>
                        <?php if(count($agendaLines)): ?>
                            <ul class="text-[11px] text-slate-600 list-disc list-inside space-y-0.5">
                                <?php $__currentLoopData = array_slice($agendaLines, 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e(\Illuminate\Support\Str::limit($line, 140)); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php if(count($agendaLines) > 6): ?>
                                <p class="text-[10px] text-slate-400">+ <?php echo e(count($agendaLines) - 6); ?> نقطة أخرى</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($m->description): ?>
                            <p class="text-[11px] text-slate-600 line-clamp-2 whitespace-pre-line"><?php echo e($m->description); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <?php if(filled($m->recording_url)): ?>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100"><i class="fas fa-circle-play ml-1"></i> تسجيل</span>
                            <?php endif; ?>
                            <?php if(!empty($m->attachments)): ?>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-100"><i class="fas fa-paperclip ml-1"></i> <?php echo e(count($m->attachments)); ?> مرفق</span>
                            <?php endif; ?>
                            <?php if(!empty($m->download_links)): ?>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-100"><i class="fas fa-link ml-1"></i> روابط</span>
                            <?php endif; ?>
                            <?php if(filled($m->notes)): ?>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200"><i class="fas fa-sticky-note ml-1"></i> ملاحظات</span>
                            <?php endif; ?>
                            <?php if(filled($m->offline_attendee_mindmap)): ?>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gradient-to-l from-cyan-50 to-indigo-50 text-indigo-800 border border-cyan-200/80"><i class="fas fa-diagram-project ml-1"></i> خريطة حضور أوفلاين</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center gap-1 shrink-0">
                    <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.move', [$offlineCourse, $cItem])); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="direction" value="up">
                        <button type="submit" class="p-1.5 rounded border border-slate-200 bg-white text-slate-500"><i class="fas fa-chevron-up text-[10px]"></i></button>
                    </form>
                    <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.move', [$offlineCourse, $cItem])); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="direction" value="down">
                        <button type="submit" class="p-1.5 rounded border border-slate-200 bg-white text-slate-500"><i class="fas fa-chevron-down text-[10px]"></i></button>
                    </form>
                    <form action="<?php echo e(route('instructor.offline-courses.curriculum.items.destroy', [$offlineCourse, $cItem])); ?>" method="POST" class="inline" onsubmit="return confirm('إزالة من المنهج؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="p-1.5 rounded border border-red-100 bg-red-50 text-red-600 text-xs font-bold">إزالة</button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 py-2">لا عناصر في هذا القسم بعد.</p>
        <?php endif; ?>

        <div class="pt-2 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 mb-2">ملاحظة توضيحية (نص للطلاب)</p>
            <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.notes.store', [$offlineCourse, $section])); ?>" method="POST" class="space-y-2">
                <?php echo csrf_field(); ?>
                <input type="text" name="title" required placeholder="عنوان الملاحظة" class="w-full text-sm rounded-lg border-slate-200 px-3 py-2">
                <textarea name="body" rows="2" placeholder="المحتوى (اختياري)" class="w-full text-sm rounded-lg border-slate-200 px-3 py-2"></textarea>
                <button type="submit" class="px-3 py-2 rounded-lg bg-slate-600 text-white text-xs font-bold">إضافة للقسم</button>
            </form>
        </div>
    </div>

    <?php if($section->children && $section->children->isNotEmpty()): ?>
        <div class="border-t border-slate-100 p-3 space-y-3 bg-white">
            <?php $__currentLoopData = $section->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('instructor.offline-curriculum.partials.section-block', ['section' => $child, 'depth' => ($depth ?? 0) + 1, 'offlineCourse' => $offlineCourse, 'curriculumChannel' => $curriculumChannel, 'groupSessions' => $groupSessions], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<?php if (! $__env->hasRenderedOnce('2703aabb-f3b8-444a-89c7-8dbf4a252de8')): $__env->markAsRenderedOnce('2703aabb-f3b8-444a-89c7-8dbf4a252de8'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            function toggleCurriculumAttach(sectionId, type) {
                const id = `attach-${type}-${sectionId}`;
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('hidden');
            }
            function openCurriculumLectureModal(sectionId) {
                const el = document.getElementById('lecture-modal-' + sectionId);
                if (!el) return;
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            function closeCurriculumLectureModal(sectionId) {
                const el = document.getElementById('lecture-modal-' + sectionId);
                if (!el) return;
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                document.querySelectorAll('[id^="lecture-modal-"]').forEach(function (el) {
                    if (!el.classList.contains('hidden')) {
                        el.classList.add('hidden');
                    }
                });
                document.body.classList.remove('overflow-hidden');
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/offline-curriculum/partials/section-block.blade.php ENDPATH**/ ?>