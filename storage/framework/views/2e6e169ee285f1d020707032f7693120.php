

<?php $__env->startSection('title', 'منهج الكورس الأوفلاين — ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'بناء المنهج (أوفلاين)'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $attachUrl = route('instructor.offline-courses.curriculum.attach-item', $offlineCourse);
?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
                    <span class="mx-1">/</span>
                    <a href="<?php echo e(route('instructor.offline-courses.show', $offlineCourse)); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
                    <span class="mx-1">/</span>
                    <span class="text-slate-700 font-semibold">المنهج</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800">بناء منهج الكورس الأوفلاين</h1>
                <p class="text-sm text-slate-500 mt-1">نظّم المحاضرات والموارد والأنشطة والاختبارات في أقسام، كما في منهج الكورسات الأونلاين.</p>
            </div>
            <a href="<?php echo e(route('instructor.offline-courses.show', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
                <i class="fas fa-arrow-right"></i> صفحة الكورس
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">
                <h2 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><i class="fas fa-plus-circle text-amber-600"></i> قسم رئيسي جديد</h2>
                <form action="<?php echo e(route('instructor.offline-courses.curriculum.sections.store', $offlineCourse)); ?>" method="POST" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">العنوان</label>
                        <input type="text" name="title" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="مثال: الوحدة الأولى — مقدمة">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">وصف القسم (اختياري)</label>
                        <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نبذة للطلاب عن محتوى القسم"></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold">إنشاء القسم</button>
                </form>
            </div>

            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('instructor.offline-curriculum.partials.section-block', ['section' => $section, 'depth' => 0, 'offlineCourse' => $offlineCourse], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-14 rounded-2xl border border-dashed border-slate-200 bg-white">
                        <i class="fas fa-sitemap text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-600 mb-2">لا توجد أقسام بعد.</p>
                        <p class="text-sm text-slate-500">أنشئ قسمًا رئيسيًا أعلاه، ثم أضف عناصر من العمود الأيمن.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 h-fit xl:sticky xl:top-4">
            <h2 class="text-lg font-bold text-slate-800 mb-1">إضافة للمنهج</h2>
            <p class="text-xs text-slate-500 mb-4">اختر القسم من القائمة ثم أرسل الإضافة.</p>

            <?php if(count($sectionsFlat) === 0): ?>
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3">أنشئ قسمًا أولًا.</p>
            <?php else: ?>
                <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                    <?php if($lectures->isNotEmpty()): ?>
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-violet-500"></i> محاضرات</h3>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineLecture::class); ?>">
                                        <input type="hidden" name="item_id" value="<?php echo e($lecture->id); ?>">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2"><?php echo e($lecture->title); ?></p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            <?php $__currentLoopData = $sectionsFlat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($opt['id']); ?>"><?php echo e($opt['label']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-violet-500 hover:bg-violet-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($resources->isNotEmpty()): ?>
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-file-alt text-sky-500"></i> موارد</h3>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineCourseResource::class); ?>">
                                        <input type="hidden" name="item_id" value="<?php echo e($resource->id); ?>">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2"><?php echo e($resource->title); ?></p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            <?php $__currentLoopData = $sectionsFlat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($opt['id']); ?>"><?php echo e($opt['label']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($activities->isNotEmpty()): ?>
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-tasks text-amber-500"></i> أنشطة</h3>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="item_type" value="<?php echo e(\App\Models\OfflineActivity::class); ?>">
                                        <input type="hidden" name="item_id" value="<?php echo e($activity->id); ?>">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2"><?php echo e($activity->title); ?></p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            <?php $__currentLoopData = $sectionsFlat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($opt['id']); ?>"><?php echo e($opt['label']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($exams->isNotEmpty()): ?>
                        <div>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2 flex items-center gap-2"><i class="fas fa-clipboard-check text-emerald-500"></i> اختبارات</h3>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <form action="<?php echo e($attachUrl); ?>" method="POST" class="space-y-1.5 p-2 rounded-xl bg-white border border-slate-200">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="item_type" value="<?php echo e(\App\Models\AdvancedExam::class); ?>">
                                        <input type="hidden" name="item_id" value="<?php echo e($exam->id); ?>">
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-2"><?php echo e($exam->title); ?></p>
                                        <select name="offline_course_section_id" class="w-full text-xs rounded-lg border-slate-200" required>
                                            <?php $__currentLoopData = $sectionsFlat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($opt['id']); ?>"><?php echo e($opt['label']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold">إضافة للقسم</button>
                                    </form>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($lectures->isEmpty() && $resources->isEmpty() && $activities->isEmpty() && $exams->isEmpty()): ?>
                        <p class="text-sm text-slate-500">لا يوجد محتوى بعد. أضف محاضرات أو موارد أو أنشطة من صفحة الكورس.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-curriculum/index.blade.php ENDPATH**/ ?>