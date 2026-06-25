

<?php $__env->startSection('title', 'منهج الكورس — ' . $offlineCourse->title); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .curriculum-hero {
        background: #fff;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .curriculum-hero:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .curriculum-hero .curriculum-hero-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(14 165 233), rgb(2 132 199));
        border-radius: 0 16px 16px 0;
    }
    .curriculum-stat {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .curriculum-stat:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .curriculum-panel {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .curriculum-panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid rgb(241 245 249);
        background: rgb(248 250 252);
    }
    .curriculum-panel-body {
        padding: 20px 22px;
    }
    .curriculum-panel-body.prose-tight {
        font-size: 0.9375rem;
        line-height: 1.75;
        color: rgb(51 65 85);
    }
    .curriculum-aside-card {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    @media (min-width: 1280px) {
        .curriculum-aside-sticky {
            position: sticky;
            top: 1rem;
        }
    }
    .curriculum-section-summary::-webkit-details-marker {
        display: none;
    }
    .curriculum-section-summary::marker {
        display: none;
        content: '';
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $stats = $curriculumStats ?? ['sections' => 0, 'items' => 0];
?>
<div class="w-full max-w-full space-y-6">
    
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="مسار التنقل">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 font-medium">لوحة التحكم</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="<?php echo e(route($sg . '.index')); ?>" class="hover:text-sky-600 font-medium"><?php echo e(($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين'); ?></a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>" class="hover:text-sky-600 font-medium truncate max-w-[10rem] sm:max-w-xs"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->title, 40)); ?></a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="text-slate-800 font-semibold">المنهج</span>
    </nav>

    
    <div class="curriculum-hero">
        <div class="curriculum-hero-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                <div class="min-w-0 flex-1 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-sky-600">منهج الكورس والتوصيف</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight"><?php echo e($offlineCourse->title); ?></h1>
                    <p class="text-sm sm:text-base text-gray-600 max-w-3xl leading-relaxed">
                        اقرأ وصف الكورس والمدرب، ثم استعرض هيكل المحتوى والروابط لكل عنصر — الصفحة تستخدم عرض المنطقة الكامل مثل بقية لوحة الطالب.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <a href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-bold border border-slate-200 transition-colors">
                            <i class="fas fa-arrow-right text-slate-500"></i>
                            صفحة الكورس
                        </a>
                        <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-sm font-bold shadow-sm transition-colors">
                            <i class="fas fa-calendar-alt"></i>
                            التقويم والمواعيد
                        </a>
                        <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-800 text-sm font-bold hover:bg-sky-100 transition-colors">
                            <i class="fas fa-chalkboard-teacher"></i>
                            المحاضرات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="curriculum-stat">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">أقسام المنهج</p>
            <p class="text-2xl font-black text-sky-600 mt-1"><?php echo e($stats['sections']); ?></p>
        </div>
        <div class="curriculum-stat">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">عناصر مرتبطة</p>
            <p class="text-2xl font-black text-violet-600 mt-1"><?php echo e($stats['items']); ?></p>
        </div>
        <div class="curriculum-stat">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">القناة</p>
            <p class="text-lg font-bold text-gray-900 mt-1"><?php echo e(($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين'); ?></p>
        </div>
        <div class="curriculum-stat">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">المجموعة</p>
            <p class="text-lg font-bold text-gray-900 mt-1 truncate" title="<?php echo e($enrollment->group->name ?? '—'); ?>"><?php echo e($enrollment->group->name ?? '—'); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <div class="xl:col-span-8 space-y-6 min-w-0">
            <?php if(filled($offlineCourse->description)): ?>
                <section class="curriculum-panel" aria-labelledby="course-desc-heading">
                    <div class="curriculum-panel-header">
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <h2 id="course-desc-heading" class="text-base sm:text-lg font-black text-gray-900">وصف الكورس (التوصيف)</h2>
                    </div>
                    <div class="curriculum-panel-body prose-tight whitespace-pre-wrap break-words">
                        <?php echo e($offlineCourse->description); ?>

                    </div>
                </section>
            <?php endif; ?>

            <?php if(filled($offlineCourse->notes)): ?>
                <section class="rounded-2xl border border-amber-200 bg-amber-50/50 overflow-hidden shadow-sm" aria-labelledby="course-notes-heading">
                    <div class="px-5 py-4 border-b border-amber-100 bg-amber-50/80 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <h2 id="course-notes-heading" class="text-base font-black text-amber-950">ملاحظات إضافية</h2>
                    </div>
                    <div class="p-5 sm:p-6 text-sm sm:text-base text-amber-950/95 leading-relaxed whitespace-pre-wrap break-words">
                        <?php echo e($offlineCourse->notes); ?>

                    </div>
                </section>
            <?php endif; ?>

            <section class="space-y-4" aria-labelledby="curriculum-structure-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="curriculum-structure-heading" class="text-lg sm:text-xl font-black text-gray-900 flex items-center gap-2">
                        <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                            <i class="fas fa-sitemap"></i>
                        </span>
                        هيكل المنهج
                    </h2>
                </div>

                <?php if($curriculumRoots->isNotEmpty()): ?>
                    <div class="curriculum-panel overflow-visible">
                        <div class="curriculum-panel-body p-4 sm:p-5 bg-slate-50/60">
                            <?php echo $__env->make('student.offline-courses.partials.curriculum-sections', [
                                'sections' => $curriculumRoots,
                                'offlineCourse' => $offlineCourse,
                                'channel' => $channel,
                                'studentRouteGroup' => $studentRouteGroup,
                                'depth' => 0,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="curriculum-panel">
                        <div class="curriculum-panel-body py-14 text-center text-gray-500 text-sm">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3 block" aria-hidden="true"></i>
                            لا يوجد منهج منشور لهذا الكورس بعد.
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        
        <aside class="xl:col-span-4 space-y-6 min-w-0">
            <div class="curriculum-aside-card curriculum-aside-sticky space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                    </span>
                    <h2 class="text-base font-black text-gray-900">المدرب</h2>
                </div>
                <div class="flex flex-col items-start text-start gap-4">
                    <?php if($offlineCourse->instructor->profile_image_url): ?>
                        <img src="<?php echo e($offlineCourse->instructor->profile_image_url); ?>" alt="" class="w-28 h-28 rounded-2xl object-cover border border-slate-200 shadow-md" width="112" height="112">
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 text-white flex items-center justify-center text-3xl font-black border border-slate-200 shadow-md" aria-hidden="true">
                            <?php echo e(mb_substr($offlineCourse->instructor->name, 0, 1)); ?>

                        </div>
                    <?php endif; ?>
                    <div class="w-full space-y-2">
                        <p class="text-lg font-black text-gray-900"><?php echo e($offlineCourse->instructor->name); ?></p>
                        <?php if(filled($offlineCourse->instructor->bio)): ?>
                            <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap break-words"><?php echo e($offlineCourse->instructor->bio); ?></div>
                        <?php else: ?>
                            <p class="text-sm text-slate-500">لم يضف المدرب نبذة تعريفية بعد.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="curriculum-aside-card space-y-3">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">انتقال سريع</p>
                <div class="flex flex-col gap-2">
                    <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>" class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-sky-200 text-sm font-bold text-gray-800 transition-colors">
                        <span class="flex items-center gap-2"><i class="fas fa-file-alt text-sky-500"></i> الموارد</span>
                        <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                    </a>
                    <a href="<?php echo e(route($sg . '.lectures', $offlineCourse)); ?>" class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-violet-200 text-sm font-bold text-gray-800 transition-colors">
                        <span class="flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-violet-500"></i> المحاضرات</span>
                        <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                    </a>
                    <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>" class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-indigo-200 text-sm font-bold text-gray-800 transition-colors">
                        <span class="flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> التقويم</span>
                        <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                    </a>
                    <a href="<?php echo e(route('student.exams.index')); ?>" class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-emerald-200 text-sm font-bold text-gray-800 transition-colors">
                        <span class="flex items-center gap-2"><i class="fas fa-clipboard-check text-emerald-600"></i> الاختبارات</span>
                        <i class="fas fa-chevron-left text-xs text-gray-400"></i>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/offline-courses/curriculum.blade.php ENDPATH**/ ?>