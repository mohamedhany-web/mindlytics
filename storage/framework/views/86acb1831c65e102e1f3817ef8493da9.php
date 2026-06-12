<?php $__env->startSection('title', __('student.my_courses_active_title')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hero-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .hero-card:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.1);
        border-color: rgb(186 230 253);
    }
    .hero-card-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(14 165 233), rgb(37 99 235));
        border-radius: 0 16px 16px 0;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 9999px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 11px;
        line-height: 1;
        border: 1px solid rgb(226 232 240);
        background: #fff;
        color: rgb(51 65 85);
        white-space: nowrap;
        max-width: 100%;
    }
    .chip i { opacity: .85; }

    .stats-card {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .stats-card:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.08);
        border-color: rgb(186 230 253);
    }

    .course-card {
        transition: all 0.2s ease;
        background: #fff;
        border: 1px solid rgb(226 232 240);
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(14, 165, 233, 0.12);
        border-color: rgb(186 230 253);
    }
    .course-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(900px 260px at 95% 0%, rgba(14, 165, 233, 0.10), transparent 60%),
                    radial-gradient(700px 220px at 5% 100%, rgba(99, 102, 241, 0.06), transparent 55%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 160ms ease;
    }
    .course-card:hover::before { opacity: 1; }

    .course-thumb {
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgb(241 245 249);
        background: linear-gradient(135deg, rgba(14,165,233,0.12), rgba(99,102,241,0.06));
    }
    .empty-state {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalShown = $activeCourses->count();
?>
<div class="w-full max-w-full space-y-6" x-data="window.__myCoursesPage(<?php echo e((int) $totalShown); ?>)">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="مسار التنقل">
        <a href="<?php echo e(route('dashboard')); ?>" class="font-medium hover:text-sky-600">لوحة التحكم</a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-800"><?php echo e(__('student.my_courses_active_title')); ?></span>
    </nav>

    <div class="hero-card">
        <div class="hero-card-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-sky-600">مسارك · كورساتك المفعلة</p>
            <h1 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl"><?php echo e(__('student.my_courses_active_title')); ?></h1>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600 sm:text-base">
                <?php echo e(__('student.my_courses_subtitle')); ?>

            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-800 transition-colors hover:bg-sky-100">
                    <i class="fas fa-search"></i>
                    <?php echo e(__('student.browse_new_courses')); ?>

                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="stats-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('student.active_label')); ?></p>
                    <p class="text-2xl font-bold text-sky-600 leading-none"><?php echo e($stats['total_active']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('student.completed')); ?></p>
                    <p class="text-2xl font-bold text-emerald-600 leading-none"><?php echo e($stats['total_completed']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('student.hours_label')); ?></p>
                    <p class="text-2xl font-bold text-gray-700 leading-none"><?php echo e($stats['total_hours']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 flex-shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('student.avg_progress_label')); ?></p>
                    <p class="text-2xl font-bold text-amber-600 leading-none"><?php echo e($stats['avg_progress']); ?>%</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky top-[64px] z-20 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-gray-50/95 backdrop-blur border-y border-slate-200">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <i class="fas fa-search text-slate-400"></i>
                    <input
                        x-model.trim="q"
                        type="text"
                        placeholder="ابحث باسم الكورس أو اسم المدرّب أو المادة…"
                        class="w-full min-w-0 border-0 bg-transparent p-0 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                    />
                    <button type="button" class="text-xs font-black text-slate-500 hover:text-slate-800" x-show="q.length" @click="q=''">
                        مسح
                    </button>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500">
                    <span class="chip">
                        <i class="fas fa-filter text-[10px] text-slate-400"></i>
                        عرض: <span class="text-slate-700" x-text="visibleCount"></span> / <?php echo e($totalShown); ?>

                    </span>
                    <span class="chip" x-show="q.length">نتائج البحث</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" x-model="onlyInProgress">
                    قيد التعلم
                </label>
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" x-model="onlyCompleted">
                    مكتملة
                </label>
            </div>
        </div>
    </div>

    <!-- الكورسات -->
    <?php if($activeCourses->count() > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" x-ref="grid">
            <?php $__currentLoopData = $activeCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $progress = $course->pivot->progress ?? 0;
                $isCompleted = $progress >= 100;
                $subjectName = $course->academicSubject->name ?? __('student.course_fallback');
                $teacherName = $course->teacher->name ?? '—';
            ?>
            <a href="<?php echo e(route('my-courses.show', $course)); ?>"
               class="course-card block"
               data-title="<?php echo e(mb_strtolower((string) $course->localized('title'))); ?>"
               data-teacher="<?php echo e(mb_strtolower((string) $teacherName)); ?>"
               data-subject="<?php echo e(mb_strtolower((string) $subjectName)); ?>"
               data-progress="<?php echo e((int) $progress); ?>"
               x-show="matches($el)"
               x-transition.opacity.duration.150ms
            >
                <div class="course-thumb h-36 flex items-center justify-center relative">
                    <?php if($course->thumbnail): ?>
                        <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="<?php echo e($course->localized('title')); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="text-sky-600">
                            <i class="fas fa-graduation-cap text-3xl"></i>
                            <p class="text-xs font-medium mt-1 text-sky-700"><?php echo e($subjectName); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($isCompleted): ?>
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-black bg-emerald-500 text-white shadow-sm">
                            <i class="fas fa-check-circle"></i> <?php echo e(__('student.completed_badge')); ?>

                        </span>
                    <?php else: ?>
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-1 rounded-xl text-xs font-black bg-sky-500 text-white shadow-sm">
                            <i class="fas fa-play-circle"></i> <?php echo e(__('student.active_badge')); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <div class="relative p-4">
                    <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-snug"><?php echo e($course->localized('title')); ?></h3>
                    <p class="text-xs text-gray-500 mb-3">
                        <?php echo e($subjectName); ?> · <?php echo e($teacherName); ?> · <?php echo e($course->lessons->count()); ?> <?php echo e(__('student.lesson_singular')); ?>

                    </p>

                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-medium text-gray-600"><?php echo e(__('student.progress')); ?></span>
                        <span class="text-sm font-bold text-sky-600"><?php echo e($progress); ?>%</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-medium text-gray-600">النقاط</span>
                        <span class="text-sm font-bold text-amber-600"><i class="fas fa-star text-amber-500 ml-1"></i><?php echo e(number_format((float)($course->student_points ?? 0), 0)); ?></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: <?php echo e(min($progress, 100)); ?>%;"></div>
                    </div>

                    <span class="mt-3 inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-black transition-colors shadow-sm">
                        <i class="fas fa-play text-xs"></i>
                        <?php echo e(__('student.continue_learning')); ?>

                    </span>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6 flex justify-center">
            <?php echo e($activeCourses->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state rounded-xl p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo e(__('student.no_active_courses_my')); ?></h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto"><?php echo e(__('student.no_active_courses_desc')); ?></p>
            <a href="<?php echo e(route('academic-years')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-search"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
window.__myCoursesPage = function (initialCount) {
    return {
        q: '',
        onlyInProgress: false,
        onlyCompleted: false,
        visibleCount: initialCount || 0,
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        matches(el) {
            if (!el) return true;

            const progress = Number(el.dataset.progress || 0);
            if (this.onlyInProgress && progress >= 100) return false;
            if (this.onlyCompleted && progress < 100) return false;

            const q = this.normalize(this.q);
            if (!q) return true;
            const hay = [
                el.dataset.title || '',
                el.dataset.teacher || '',
                el.dataset.subject || ''
            ].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const grid = this.$refs.grid;
                if (!grid) return;
                const cards = Array.from(grid.querySelectorAll('a.course-card'));
                this.visibleCount = cards.filter(a => this.matches(a)).length;
            } catch (e) {}
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('onlyInProgress', () => this.recount());
            this.$watch('onlyCompleted', () => this.recount());
            this.recount();
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/my-courses/index.blade.php ENDPATH**/ ?>