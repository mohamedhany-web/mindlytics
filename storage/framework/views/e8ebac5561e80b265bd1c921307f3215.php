<?php $__env->startSection('title', __('student.my_groups_title')); ?>

<?php
    $groupsCount = $groups->count();
    $firstGroup = $groups->first();
?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
<style>
    .sg {
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
    .sg-reveal { animation: sgRise var(--ml-slow) var(--ml-ease) both; animation-delay: var(--reveal-delay, 0ms); }
    @keyframes sgRise {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: none; }
    }
    .sg-chrome {
        display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
        gap: 12px; padding: 8px 0 14px; border-bottom: 1px solid var(--ml-line); margin-bottom: 20px;
    }
    .sg-crumb { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 12px; color: var(--ml-muted); margin-bottom: 6px; }
    .sg-crumb a { color: var(--ml-teal-deep); font-weight: 600; text-decoration: none; }
    .sg-crumb a:hover { text-decoration: underline; }
    .sg-chrome h1 { margin: 0; font-size: clamp(1.25rem, 2vw, 1.55rem); font-weight: 700; letter-spacing: -0.015em; line-height: 1.3; }
    .sg-chrome .sub { margin: 4px 0 0; font-size: 13px; color: var(--ml-muted); max-width: 52ch; line-height: 1.55; }
    .sg-signals { display: flex; flex-wrap: wrap; gap: 8px; }
    .sg-signal {
        display: inline-flex; align-items: center; gap: 6px; min-height: 28px;
        padding: 0 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .sg-signal-live { background: rgba(73, 164, 162, 0.14); color: var(--ml-teal-deep); }

    .sg-stage {
        position: relative; display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: end;
        padding: 18px 20px; margin-bottom: 20px; background: var(--ml-surface);
        border-radius: calc(var(--ml-r) + 4px); border: 1px solid var(--ml-line);
        box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 10px 30px rgba(26, 34, 56, 0.04);
    }
    .sg-stage::before {
        content: ''; position: absolute; inset-block: 16px; inset-inline-start: 0; width: 3px;
        border-radius: 999px; background: linear-gradient(180deg, var(--ml-teal), rgba(73,164,162,0.2));
    }
    .sg-eyebrow {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px;
        font-size: 11px; font-weight: 700; color: var(--ml-teal-deep);
    }
    .sg-eyebrow em {
        font-style: normal; padding: 2px 8px; border-radius: 6px;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
    .sg-stage h2 {
        margin: 0 0 6px; font-size: clamp(1.1rem, 1.8vw, 1.35rem); font-weight: 700;
        line-height: 1.35; letter-spacing: -0.01em; max-width: 36ch;
    }
    .sg-copy { margin: 0; font-size: 13px; line-height: 1.65; color: var(--ml-muted); max-width: 48ch; }
    .sg-stage-actions { display: flex; flex-direction: column; gap: 10px; min-width: 150px; }
    .sg-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 44px; padding: 0 18px; border-radius: 12px; background: var(--ml-teal);
        color: #fff !important; font-size: 14px; font-weight: 700; text-decoration: none !important;
        border: 0; box-shadow: 0 8px 18px rgba(73, 164, 162, 0.22);
        transition: background var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .sg-btn:hover { background: var(--ml-teal-deep); transform: translateY(-1px); }
    .sg-btn-quiet {
        background: transparent; color: var(--ml-ink) !important; box-shadow: none;
        border: 1px solid var(--ml-line);
    }
    .sg-btn-quiet:hover { background: var(--ml-well); transform: none; }

    .sg-list { display: flex; flex-direction: column; gap: 10px; }
    .sg-row {
        display: grid; grid-template-columns: 52px minmax(0, 1fr) auto; gap: 14px; align-items: center;
        padding: 14px; background: var(--ml-surface); border: 1px solid var(--ml-line);
        border-radius: var(--ml-r); text-decoration: none !important; color: inherit !important;
        transition: border-color var(--ml-fast) ease, box-shadow var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease);
    }
    .sg-row:hover {
        border-color: rgba(73, 164, 162, 0.35);
        box-shadow: 0 10px 28px rgba(26, 34, 56, 0.06);
        transform: translateY(-1px);
    }
    .sg-row:focus-visible { outline: 2px solid var(--ml-teal); outline-offset: 2px; }
    .sg-ico {
        width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 1.1rem;
    }
    .sg-body { min-width: 0; }
    .sg-body h3 {
        margin: 0 0 4px; font-size: 15px; font-weight: 700; line-height: 1.35;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .sg-body .meta { margin: 0 0 6px; font-size: 12px; color: var(--ml-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sg-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .sg-tag {
        display: inline-flex; align-items: center; gap: 4px; min-height: 24px; padding: 0 8px;
        border-radius: 6px; font-size: 11px; font-weight: 700;
        background: var(--ml-well); color: var(--ml-muted);
    }
    .sg-tag-hot { background: rgba(255, 210, 63, 0.35); color: var(--ml-yellow-ink); }
    .sg-side { display: flex; align-items: center; gap: 8px; color: var(--ml-teal-deep); font-size: 12px; font-weight: 700; white-space: nowrap; }
    .sg-side i { font-size: 10px; opacity: 0.8; }

    .sg-empty {
        text-align: center; padding: 48px 24px; background: var(--ml-surface);
        border: 1px dashed rgba(26, 34, 56, 0.14); border-radius: calc(var(--ml-r) + 4px);
    }
    .sg-empty .icon {
        width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep); font-size: 22px;
    }
    .sg-empty h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; }
    .sg-empty p { margin: 0 auto; max-width: 36ch; font-size: 13px; color: var(--ml-muted); line-height: 1.6; }

    @media (max-width: 700px) {
        .sg-stage { grid-template-columns: 1fr; }
        .sg-stage-actions { align-items: stretch; min-width: 0; }
        .sg-row { grid-template-columns: 44px minmax(0, 1fr); }
        .sg-side { grid-column: 1 / -1; justify-content: flex-end; padding-top: 2px; }
        .sg-ico { width: 44px; height: 44px; border-radius: 12px; font-size: 1rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sg-reveal, .sg-row, .sg-btn { animation: none !important; transition: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sg">
    <header class="sg-chrome sg-reveal">
        <div>
            <nav class="sg-crumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.my_groups_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.my_groups_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.my_groups_subtitle')); ?></p>
        </div>
        <div class="sg-signals" aria-label="ملخص">
            <span class="sg-signal sg-signal-live"><?php echo e($groupsCount); ?> <?php echo e(__('student.my_groups')); ?></span>
        </div>
    </header>

    <?php if($groupsCount > 0): ?>
        <section class="sg-stage sg-reveal" style="--reveal-delay:50ms" aria-label="مجموعتك">
            <div>
                <div class="sg-eyebrow">مجموعاتك <em>نشطة</em></div>
                <h2><?php echo e($firstGroup->name); ?></h2>
                <p class="sg-copy">
                    <?php echo e($firstGroup->course->title ?? 'مجموعة تعلّم'); ?>

                    · <?php echo e($firstGroup->members->count()); ?> <?php echo e(__('student.member_singular')); ?>

                    <?php if($firstGroup->leader): ?>
                        · <?php echo e(__('student.leader_label')); ?>: <?php echo e($firstGroup->leader->name); ?>

                    <?php endif; ?>
                </p>
            </div>
            <div class="sg-stage-actions">
                <a class="sg-btn" href="<?php echo e(route('student.groups.show', $firstGroup)); ?>">
                    <i class="fas fa-comments text-xs"></i>
                    فتح المجموعة
                </a>
                <a class="sg-btn sg-btn-quiet" href="<?php echo e(route('dashboard')); ?>">العودة لمساحة التعلّم</a>
            </div>
        </section>

        <div class="sg-list sg-reveal" style="--reveal-delay:120ms" role="list">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('student.groups.show', $group)); ?>" class="sg-row" role="listitem">
                    <div class="sg-ico" aria-hidden="true"><i class="fas fa-user-friends"></i></div>
                    <div class="sg-body">
                        <h3><?php echo e($group->name); ?></h3>
                        <p class="meta"><?php echo e($group->course->title ?? '—'); ?></p>
                        <div class="sg-tags">
                            <span class="sg-tag">
                                <i class="fas fa-users" style="font-size:9px;opacity:.7"></i>
                                <?php echo e($group->members->count()); ?> / <?php echo e($group->max_members); ?> <?php echo e(__('student.member_singular')); ?>

                            </span>
                            <?php if($group->leader): ?>
                                <span class="sg-tag sg-tag-hot">
                                    <?php echo e(__('student.leader_label')); ?>: <?php echo e($group->leader->name); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="sg-side">
                        فتح
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <section class="sg-stage sg-reveal" style="--reveal-delay:50ms">
            <div>
                <div class="sg-eyebrow">مجموعاتك <em>فارغة</em></div>
                <h2><?php echo e(__('student.no_groups')); ?></h2>
                <p class="sg-copy"><?php echo e(__('student.no_groups_desc')); ?></p>
            </div>
            <div class="sg-stage-actions">
                <a class="sg-btn" href="<?php echo e(route('dashboard')); ?>">مساحة التعلّم</a>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\groups\index.blade.php ENDPATH**/ ?>