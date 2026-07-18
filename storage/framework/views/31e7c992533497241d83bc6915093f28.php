<?php $__env->startSection('title', __('student.my_courses_active_title')); ?>

<?php
    $totalShown = $activeCourses->count();
    $continueCourse = $activeCourses->getCollection()->first(function ($c) {
        return (float) ($c->pivot->progress ?? 0) < 100;
    }) ?? $activeCourses->getCollection()->first();
    $continueProgress = $continueCourse ? (float) ($continueCourse->pivot->progress ?? 0) : 0;
?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
<style>
    .mc {
        --ml-teal: #49A4A2;
        --ml-teal-deep: #2f7f7d;
        --ml-yellow: #FFD23F;
        --ml-yellow-ink: #5c4500;
        --ml-bg: #F7F9FC;
        --ml-surface: #FFFFFF;
        --ml-well: #EEF2F7;
        --ml-ink: #1A2238;
        --ml-muted: #475569;
        --ml-line: rgba(26, 34, 56, 0.08);
        --ml-r: 14px;
        --ml-fast: 140ms;
        --ml-slow: 400ms;
        --ml-ease: cubic-bezier(0.22, 1, 0.36, 1);
        font-family: 'IBM Plex Sans Arabic', 'Tajawal', 'Cairo', sans-serif;
        color: var(--ml-ink);
        width: 100%;
        max-width: none;
        padding-block: 4px 32px;
    }
    .mc-reveal { animation: mcRise var(--ml-slow) var(--ml-ease) both; animation-delay: var(--reveal-delay, 0ms); }
    @keyframes mcRise {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: none; }
    }

    .mc-chrome {
        display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
        gap: 12px; padding: 8px 0 14px; border-bottom: 1px solid var(--ml-line); margin-bottom: 20px;
    }
    .mc-crumb { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--ml-muted); margin-bottom: 6px; }
    .mc-crumb a { color: var(--ml-teal-deep); font-weight: 600; text-decoration: none; }
    .mc-crumb a:hover { text-decoration: underline; }
    .mc-chrome h1 { margin: 0; font-size: clamp(1.25rem, 2vw, 1.55rem); font-weight: 700; letter-spacing: -0.015em; line-height: 1.3; }
    .mc-chrome .sub { margin: 4px 0 0; font-size: 13px; color: var(--ml-muted); max-width: 52ch; line-height: 1.55; }
    .mc-signals { display: flex; flex-wrap: wrap; gap: 8px; }
    .mc-signal {
        display: inline-flex; align-items: center; gap: 6px; min-height: 28px;
        padding: 0 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .mc-signal-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
    .mc-signal-hot { background: rgba(255, 210, 63, 0.35); color: var(--ml-yellow-ink); }

    .mc-stage {
        position: relative; display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: end;
        padding: 18px 20px; margin-bottom: 20px; background: var(--ml-surface);
        border-radius: calc(var(--ml-r) + 4px); border: 1px solid var(--ml-line);
        box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 10px 30px rgba(26, 34, 56, 0.04);
    }
    .mc-stage::before {
        content: ''; position: absolute; inset-block: 16px; inset-inline-start: 0; width: 3px;
        border-radius: 999px; background: linear-gradient(180deg, var(--ml-teal), rgba(73,164,162,0.2));
    }
    .mc-eyebrow {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;
        font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
    }
    .mc-eyebrow em {
        font-style: normal; padding: 2px 8px; border-radius: 6px;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
    .mc-stage h2 {
        margin: 0 0 6px; font-size: clamp(1.1rem, 1.8vw, 1.35rem); font-weight: 700;
        line-height: 1.35; letter-spacing: -0.01em; max-width: 36ch;
    }
    .mc-copy { margin: 0; font-size: 13px; line-height: 1.65; color: var(--ml-muted); max-width: 48ch; }
    .mc-meter {
        height: 4px; width: 100%; max-width: 240px; margin-top: 12px; border-radius: 999px;
        background: var(--ml-well); overflow: hidden;
    }
    .mc-meter > i { display: block; height: 100%; background: var(--ml-teal); border-radius: inherit; }
    .mc-stage-actions { display: flex; flex-direction: column; gap: 10px; min-width: 160px; }
    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; padding: 0 18px; border-radius: 12px; background: var(--ml-teal);
        color: #fff !important; font-size: 14px; font-weight: 700; text-decoration: none !important;
        border: 0; cursor: pointer; box-shadow: 0 8px 18px rgba(73, 164, 162, 0.22);
        transition: background var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .mc-btn:hover { background: var(--ml-teal-deep); transform: translateY(-1px); }
    .mc-btn-quiet {
        background: transparent; color: var(--ml-ink) !important; box-shadow: none;
        border: 1px solid var(--ml-line);
    }
    .mc-btn-quiet:hover { background: var(--ml-well); transform: none; }

    .mc-pulse {
        display: grid; grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(0, 1fr));
        gap: 1px; margin-bottom: 20px; background: var(--ml-line);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r); overflow: hidden;
    }
    .mc-pulse > div {
        background: var(--ml-surface); padding: 14px 16px;
        display: flex; flex-direction: column; justify-content: center; gap: 4px;
    }
    .mc-pulse .lbl { font-size: 11px; font-weight: 700; color: var(--ml-muted); }
    .mc-pulse .val { font-size: 1.35rem; font-weight: 700; color: var(--ml-ink); letter-spacing: -0.02em; line-height: 1.1; }
    .mc-pulse .val.teal { color: var(--ml-teal-deep); }
    .mc-pulse .hint { font-size: 11px; color: var(--ml-muted); margin-top: 2px; }
    .mc-pulse-main .track {
        height: 5px; border-radius: 999px; background: var(--ml-well); overflow: hidden; margin-top: 8px;
    }
    .mc-pulse-main .track > i { display: block; height: 100%; background: linear-gradient(90deg, var(--ml-teal), #6bbdbb); border-radius: inherit; }

    .mc-toolbar {
        position: sticky; top: 0; z-index: 15; margin-bottom: 16px;
        padding: 12px 14px; background: rgba(247, 249, 252, 0.92); backdrop-filter: blur(10px);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r);
    }
    .mc-toolbar-inner { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; }
    .mc-search {
        flex: 1 1 240px; display: flex; align-items: center; gap: 10px;
        min-height: 42px; padding: 0 12px; background: var(--ml-surface);
        border: 1px solid var(--ml-line); border-radius: 12px;
    }
    .mc-search i { color: var(--ml-muted); font-size: 12px; }
    .mc-search input {
        flex: 1; min-width: 0; border: 0; background: transparent; outline: none;
        font-size: 13px; font-weight: 600; color: var(--ml-ink); font-family: inherit;
    }
    .mc-search button {
        border: 0; background: transparent; font-size: 11px; font-weight: 700;
        color: var(--ml-muted); cursor: pointer;
    }
    .mc-filters { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .mc-chip {
        display: inline-flex; align-items: center; gap: 6px; min-height: 34px;
        padding: 0 12px; border-radius: 999px; font-size: 12px; font-weight: 700;
        border: 1px solid var(--ml-line); background: var(--ml-surface); color: var(--ml-muted);
        cursor: pointer; user-select: none; transition: background var(--ml-fast) ease, color var(--ml-fast) ease, border-color var(--ml-fast) ease;
    }
    .mc-chip input { display: none; }
    .mc-chip.is-on { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); border-color: rgba(73, 164, 162, 0.35); }
    .mc-meta { font-size: 11px; font-weight: 700; color: var(--ml-muted); }

    .mc-library { display: flex; flex-direction: column; gap: 10px; }
    .mc-row {
        display: grid; grid-template-columns: 88px minmax(0, 1fr) auto; gap: 14px; align-items: center;
        padding: 12px; background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .mc-row:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .mc-row:focus-visible { outline: 2px solid var(--ml-teal); outline-offset: 2px; }
    .mc-thumb {
        width: 88px; height: 72px; border-radius: 10px; overflow: hidden; flex-shrink: 0;
        background: linear-gradient(145deg, rgba(73,164,162,0.16), var(--ml-well));
        display: flex; align-items: center; justify-content: center; color: var(--ml-teal-deep);
        position: relative;
    }
    .mc-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .mc-thumb .ph { text-align: center; padding: 6px; }
    .mc-thumb .ph i { font-size: 18px; display: block; margin-bottom: 4px; }
    .mc-thumb .ph span { font-size: 9px; font-weight: 700; line-height: 1.2; display: block; }
    .mc-body { min-width: 0; }
    .mc-body h3 {
        margin: 0 0 4px; font-size: 15px; font-weight: 700; line-height: 1.35;
        letter-spacing: -0.01em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .mc-body .meta { margin: 0 0 8px; font-size: 12px; color: var(--ml-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mc-prog { display: flex; align-items: center; gap: 10px; }
    .mc-prog .bar { flex: 1; height: 4px; border-radius: 999px; background: var(--ml-well); overflow: hidden; max-width: 200px; }
    .mc-prog .bar > i { display: block; height: 100%; background: var(--ml-teal); border-radius: inherit; }
    .mc-prog .pct { font-size: 12px; font-weight: 700; color: var(--ml-teal-deep); min-width: 2.5rem; }
    .mc-side { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; padding-inline-end: 4px; }
    .mc-badge {
        display: inline-flex; align-items: center; gap: 4px; min-height: 24px; padding: 0 8px;
        border-radius: 6px; font-size: 11px; font-weight: 700;
    }
    .mc-badge-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }
    .mc-badge-done { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .mc-pts { font-size: 11px; font-weight: 700; color: var(--ml-yellow-ink); }
    .mc-cta {
        display: inline-flex; align-items: center; gap: 6px; min-height: 34px; padding: 0 12px;
        border-radius: 10px; background: var(--ml-teal); color: #fff !important;
        font-size: 12px; font-weight: 700; white-space: nowrap;
    }
    .mc-row:hover .mc-cta { background: var(--ml-teal-deep); }

    .mc-empty {
        text-align: center; padding: 48px 24px; background: var(--ml-surface);
        border: 1px dashed rgba(26, 34, 56, 0.14); border-radius: calc(var(--ml-r) + 4px);
    }
    .mc-empty .icon {
        width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 22px;
    }
    .mc-empty h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; }
    .mc-empty p { margin: 0 auto 18px; max-width: 36ch; font-size: 13px; color: var(--ml-muted); line-height: 1.6; }

    .mc-pager { margin-top: 20px; display: flex; justify-content: center; }
    .mc-pager nav { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; }
    .mc-pager a, .mc-pager span {
        min-width: 36px; min-height: 36px; padding: 0 10px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; text-decoration: none !important;
        border: 1px solid var(--ml-line); background: var(--ml-surface); color: var(--ml-ink) !important;
    }
    .mc-pager .active span, .mc-pager [aria-current="page"] span {
        background: var(--ml-teal); color: #fff !important; border-color: var(--ml-teal);
    }

    @media (max-width: 900px) {
        .mc-stage { grid-template-columns: 1fr; }
        .mc-stage-actions { align-items: stretch; min-width: 0; }
        .mc-pulse { grid-template-columns: 1fr 1fr; }
        .mc-pulse-main { grid-column: 1 / -1; }
    }
    @media (max-width: 640px) {
        .mc-row { grid-template-columns: 72px minmax(0, 1fr); }
        .mc-side { grid-column: 1 / -1; flex-direction: row; align-items: center; justify-content: space-between; padding: 0; }
        .mc-thumb { width: 72px; height: 60px; }
        .mc-pulse { grid-template-columns: 1fr 1fr; }
    }
    @media (prefers-reduced-motion: reduce) {
        .mc-reveal, .mc-row, .mc-btn { animation: none !important; transition: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="mc" x-data="window.__myCoursesPage(<?php echo e((int) $totalShown); ?>)">

    <header class="mc-chrome mc-reveal">
        <div>
            <nav class="mc-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.my_courses_active_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.my_courses_active_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.my_courses_subtitle')); ?></p>
        </div>
        <div class="mc-signals" aria-label="ملخص سريع">
            <span class="mc-signal mc-signal-live"><?php echo e($stats['total_active']); ?> نشط</span>
            <span class="mc-signal"><?php echo e($stats['total_completed']); ?> مكتمل</span>
            <span class="mc-signal mc-signal-hot"><?php echo e($stats['avg_progress']); ?>٪ متوسط</span>
        </div>
    </header>

    <?php if($continueCourse): ?>
        <section class="mc-stage mc-reveal" style="--reveal-delay:60ms" aria-label="تابع من حيث توقفت">
            <div>
                <div class="mc-eyebrow">
                    واصل التعلّم
                    <em><?php echo e($continueProgress >= 100 ? __('student.completed_badge') : __('student.active_badge')); ?></em>
                </div>
                <h2><?php echo e($continueCourse->localized('title')); ?></h2>
                <p class="mc-copy">
                    <?php echo e(collect([
                        $continueCourse->teacher->name ?? null,
                        $continueCourse->lessons->count() ? $continueCourse->lessons->count().' '.__('student.lesson_singular') : null,
                    ])->filter()->implode(' · ')); ?>

                </p>
                <div class="mc-meter" role="progressbar" aria-valuenow="<?php echo e((int) $continueProgress); ?>" aria-valuemin="0" aria-valuemax="100">
                    <i style="width:<?php echo e(min(100, $continueProgress)); ?>%"></i>
                </div>
            </div>
            <div class="mc-stage-actions">
                <a class="mc-btn" href="<?php echo e(route('my-courses.show', $continueCourse)); ?>">
                    <i class="fas fa-play text-xs"></i>
                    <?php echo e(__('student.continue_learning')); ?>

                </a>
                <a class="mc-btn mc-btn-quiet" href="<?php echo e(route('academic-years')); ?>">
                    <?php echo e(__('student.browse_new_courses')); ?>

                </a>
            </div>
        </section>
    <?php else: ?>
        <section class="mc-stage mc-reveal" style="--reveal-delay:60ms" aria-label="ابدأ التعلّم">
            <div>
                <div class="mc-eyebrow">مكتبتك <em>فارغة</em></div>
                <h2><?php echo e(__('student.no_active_courses_my')); ?></h2>
                <p class="mc-copy"><?php echo e(__('student.no_active_courses_desc')); ?></p>
            </div>
            <div class="mc-stage-actions">
                <a class="mc-btn" href="<?php echo e(route('academic-years')); ?>">
                    <i class="fas fa-compass text-xs"></i>
                    <?php echo e(__('student.browse_courses_btn')); ?>

                </a>
            </div>
        </section>
    <?php endif; ?>

    <?php if($activeCourses->count() > 0): ?>
        <div class="mc-pulse mc-reveal" style="--reveal-delay:100ms" aria-label="نبض التعلّم">
            <div class="mc-pulse-main">
                <span class="lbl"><?php echo e(__('student.avg_progress_label')); ?></span>
                <span class="val teal"><?php echo e($stats['avg_progress']); ?>٪</span>
                <div class="track" aria-hidden="true"><i style="width:<?php echo e(min(100, (float) $stats['avg_progress'])); ?>%"></i></div>
                <span class="hint">متوسط تقدّمك عبر المقررات النشطة</span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.active_label')); ?></span>
                <span class="val"><?php echo e($stats['total_active']); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.completed')); ?></span>
                <span class="val"><?php echo e($stats['total_completed']); ?></span>
            </div>
            <div>
                <span class="lbl"><?php echo e(__('student.hours_label')); ?></span>
                <span class="val"><?php echo e($stats['total_hours']); ?></span>
            </div>
        </div>

        <div class="mc-toolbar mc-reveal" style="--reveal-delay:140ms">
            <div class="mc-toolbar-inner">
                <div class="mc-search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input
                        x-model.trim="q"
                        type="search"
                        placeholder="ابحث باسم المقرر أو المدرّب…"
                        aria-label="بحث في مقرراتي"
                    >
                    <button type="button" x-show="q.length" @click="q=''" x-cloak>مسح</button>
                </div>
                <div class="mc-filters">
                    <label class="mc-chip" :class="onlyInProgress && 'is-on'">
                        <input type="checkbox" x-model="onlyInProgress">
                        قيد التعلّم
                    </label>
                    <label class="mc-chip" :class="onlyCompleted && 'is-on'">
                        <input type="checkbox" x-model="onlyCompleted">
                        مكتملة
                    </label>
                    <span class="mc-meta">
                        عرض <span x-text="visibleCount"></span> / <?php echo e($totalShown); ?>

                    </span>
                </div>
            </div>
        </div>

        <div class="mc-library mc-reveal" style="--reveal-delay:180ms" x-ref="grid" role="list">
            <?php $__currentLoopData = $activeCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $progress = (float) ($course->pivot->progress ?? 0);
                    $isCompleted = $progress >= 100;
                    $teacherName = $course->teacher->name ?? '—';
                ?>
                <a href="<?php echo e(route('my-courses.show', $course)); ?>"
                   class="mc-row course-card"
                   role="listitem"
                   data-title="<?php echo e(mb_strtolower((string) $course->localized('title'))); ?>"
                   data-teacher="<?php echo e(mb_strtolower((string) $teacherName)); ?>"
                   data-progress="<?php echo e((int) $progress); ?>"
                   x-show="matches($el)"
                   x-transition.opacity.duration.150ms
                >
                    <div class="mc-thumb" aria-hidden="true">
                        <?php if($course->thumbnail): ?>
                            <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="">
                        <?php else: ?>
                            <div class="ph">
                                <i class="fas fa-graduation-cap"></i>
                                <span>مقرر</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mc-body">
                        <h3><?php echo e($course->localized('title')); ?></h3>
                        <p class="meta"><?php echo e($teacherName); ?> · <?php echo e($course->lessons->count()); ?> <?php echo e(__('student.lesson_singular')); ?></p>
                        <div class="mc-prog">
                            <div class="bar" role="presentation"><i style="width:<?php echo e(min(100, $progress)); ?>%"></i></div>
                            <span class="pct"><?php echo e((int) $progress); ?>٪</span>
                        </div>
                    </div>
                    <div class="mc-side">
                        <?php if($isCompleted): ?>
                            <span class="mc-badge mc-badge-done"><i class="fas fa-check"></i> <?php echo e(__('student.completed_badge')); ?></span>
                        <?php else: ?>
                            <span class="mc-badge mc-badge-live"><i class="fas fa-play"></i> <?php echo e(__('student.active_badge')); ?></span>
                        <?php endif; ?>
                        <span class="mc-pts"><i class="fas fa-star" style="opacity:.7"></i> <?php echo e(number_format((float) ($course->student_points ?? 0), 0)); ?></span>
                        <span class="mc-cta">
                            <?php echo e($isCompleted ? __('student.completed_badge') : __('student.continue_learning')); ?>

                            <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mc-pager">
            <?php echo e($activeCourses->links()); ?>

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
                el.dataset.teacher || ''
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

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/my-courses/index.blade.php ENDPATH**/ ?>