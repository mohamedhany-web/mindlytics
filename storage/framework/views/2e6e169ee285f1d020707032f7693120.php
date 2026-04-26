<?php $__env->startSection('title', 'منهج الكورس الأوفلاين — ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'بناء المنهج (أوفلاين)'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $attachUrl = route('instructor.offline-courses.curriculum.attach-item', $offlineCourse);
    $curriculumChannel = $curriculumChannel ?? (request()->query('channel') === 'online' ? 'online' : 'offline');
?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="<?php echo e(route('instructor.offline-courses.index', ['channel' => $curriculumChannel])); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
                    <span class="mx-1">/</span>
                    <a href="<?php echo e(route('instructor.offline-courses.show', $offlineCourse)); ?>?channel=<?php echo e(urlencode($curriculumChannel)); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
                    <span class="mx-1">/</span>
                    <span class="text-slate-700 font-semibold">المنهج</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800">بناء منهج الكورس الأوفلاين</h1>
                <p class="text-sm text-slate-500 mt-1">نظّم المحاضرات والموارد والأنشطة والاختبارات في أقسام، كما في منهج الكورسات الأونلاين.</p>
            </div>
            <a href="<?php echo e(route('instructor.offline-courses.show', $offlineCourse)); ?>?channel=<?php echo e(urlencode($curriculumChannel)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
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

    <div class="space-y-4">
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">
            <h2 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><i class="fas fa-plus-circle text-amber-600"></i> قسم رئيسي جديد</h2>
            <p class="text-xs text-slate-600 mb-3">
                من كل بطاقة قسم يمكنك <strong>إنشاء محاضرة جديدة</strong> مرتبطة بالقسم مباشرة، أو ربط عناصر أُنشئت مسبقاً (مورد، نشاط، اختبار).
                المحاضرة الأوفلاين تدعم وصف الجلسة، <strong>نقاط اليوم</strong> (سطر لكل نقطة)، تسجيل إعادة الاستماع، مرفقات وروابط تحميل — وتظهر ملخصاتها داخل المنهج بعد الحفظ.
            </p>
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
                <?php echo $__env->make('instructor.offline-curriculum.partials.section-block', ['section' => $section, 'depth' => 0, 'offlineCourse' => $offlineCourse, 'curriculumChannel' => $curriculumChannel, 'groupSessions' => $groupSessions], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-14 rounded-2xl border border-dashed border-slate-200 bg-white">
                    <i class="fas fa-sitemap text-4xl text-slate-300 mb-3"></i>
                    <p class="text-slate-600 mb-2">لا توجد أقسام بعد.</p>
                    <p class="text-sm text-slate-500">أنشئ قسمًا رئيسيًا أعلاه.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-curriculum/index.blade.php ENDPATH**/ ?>