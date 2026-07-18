<?php $__env->startSection('title', __('los.page_title')); ?>

<?php
    $losRtl = app()->getLocale() === 'ar';
    $os = $os ?? [];
    $stage = $os['stage'] ?? ['mode' => 'empty', 'type_label' => '', 'title' => '', 'parent' => '', 'why' => '', 'cta_label' => __('los.continue'), 'cta_url' => route('my-courses.index')];
    $mission = $os['mission'] ?? ['title' => '', 'state' => 'open', 'state_label' => '', 'hint' => ''];
    $ai = $os['ai'] ?? ['insight' => '', 'why' => '', 'action_label' => '', 'action_url' => '#', 'recommendations' => []];
    $journey = $os['journey'] ?? ['past' => '', 'present' => '', 'next' => '', 'recovery' => null];
    $skillTree = $os['skill_tree'] ?? [];
    $planning = $os['planning'] ?? [];
    $calendar = $os['calendar'] ?? [];
    $mastery = $os['mastery'] ?? ['progress' => 0, 'whisper' => '', 'achievement_url' => route('student.certificates.index')];
    $heatmap = $os['heatmap'] ?? [];
    $timeline = $os['timeline'] ?? [];
    $courses = $os['courses'] ?? [];
    $notes = $os['notes'] ?? [];
    $streak = (int) ($os['streak_days'] ?? 0);
    $goals = $os['goals'] ?? null;
?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
<style>
    :root {
        --ml-teal: #49A4A2;
        --ml-teal-deep: #2f7f7d;
        --ml-yellow: #FFD23F;
        --ml-yellow-ink: #5c4500;
        --ml-bg: #F7F9FC;
        --ml-surface: #FFFFFF;
        --ml-well: #EEF2F7;
        --ml-ink: #1A2238;
        --ml-muted: #475569;
        --ml-line: rgba(26, 34, 56, 0.07);
        --ml-r: 14px;
        --ml-fast: 140ms;
        --ml-base: 220ms;
        --ml-slow: 420ms;
        --ml-ease: cubic-bezier(0.22, 1, 0.36, 1);
        --ml-space-1: 8px;
        --ml-space-2: 16px;
        --ml-space-3: 24px;
        --ml-space-4: 32px;
    }

    .los {
        --reveal-delay: 0ms;
        font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif;
        color: var(--ml-ink);
        width: 100%;
        max-width: none;
        margin-inline: 0;
        padding-block: 4px 28px;
    }

    .los-reveal {
        animation: losRise var(--ml-slow) var(--ml-ease) both;
        animation-delay: var(--reveal-delay);
    }
    @keyframes losRise {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: none; }
    }

    .los-chrome {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: var(--ml-space-2);
        padding: 10px 0 14px;
        border-bottom: 1px solid var(--ml-line);
        margin-bottom: var(--ml-space-3);
    }
    .los-chrome h1 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1.35;
    }
    .los-chrome .sub {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--ml-muted);
    }
    .los-signals {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .los-signal {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: var(--ml-well);
        color: var(--ml-muted);
    }
    .los-signal-hot {
        background: rgba(255, 210, 63, 0.35);
        color: var(--ml-yellow-ink);
    }
    .los-signal-live {
        background: rgba(73, 164, 162, 0.14);
        color: var(--ml-teal-deep);
    }

    /* ZONE A — editorial stage (not a twin card) */
    .los-stage {
        position: relative;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: var(--ml-space-3);
        align-items: end;
        padding: 18px 20px 18px 20px;
        margin-bottom: var(--ml-space-3);
        background: var(--ml-surface);
        border-radius: calc(var(--ml-r) + 4px);
        border: 1px solid var(--ml-line);
        box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset, 0 10px 30px rgba(26, 34, 56, 0.04);
    }
    .los-stage::before {
        content: '';
        position: absolute;
        inset-block: 16px;
        inset-inline-start: 0;
        width: 3px;
        border-radius: 999px;
        background: linear-gradient(180deg, var(--ml-teal), rgba(73,164,162,0.2));
    }
    .los-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        color: var(--ml-teal-deep);
    }
    .los-eyebrow em {
        font-style: normal;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(73, 164, 162, 0.12);
        color: var(--ml-teal-deep);
    }
    .los-stage h2 {
        margin: 0 0 6px;
        font-size: clamp(1.2rem, 2vw, 1.55rem);
        font-weight: 700;
        line-height: 1.35;
        letter-spacing: -0.015em;
        max-width: 34ch;
    }
    .los-copy {
        margin: 0;
        font-size: 13px;
        line-height: 1.65;
        color: var(--ml-muted);
        max-width: 48ch;
    }
    .los-urgency {
        margin-top: 8px;
        font-size: 12px;
        font-weight: 700;
        color: var(--ml-yellow-ink);
    }
    .los-stage-actions {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        min-width: 160px;
    }
    .los-meter {
        height: 4px;
        width: 100%;
        max-width: 220px;
        margin-top: 12px;
        border-radius: 999px;
        background: var(--ml-well);
        overflow: hidden;
    }
    .los-meter > i {
        display: block;
        height: 100%;
        background: var(--ml-teal);
        border-radius: inherit;
        transform-origin: inline-start center;
        animation: losGrow var(--ml-slow) var(--ml-ease) both;
    }
    @keyframes losGrow {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }

    .los-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: var(--ml-teal);
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none !important;
        border: 0;
        cursor: pointer;
        transition: background var(--ml-fast) ease, transform var(--ml-fast) var(--ml-ease), box-shadow var(--ml-fast) ease;
        box-shadow: 0 8px 18px rgba(73, 164, 162, 0.22);
    }
    .los-btn:hover { background: var(--ml-teal-deep); transform: translateY(-1px); }
    .los-btn:active { transform: translateY(0) scale(0.985); }
    .los-btn:focus-visible {
        outline: 2px solid var(--ml-yellow);
        outline-offset: 3px;
    }
    .los-btn-quiet {
        background: transparent;
        color: var(--ml-teal-deep) !important;
        box-shadow: none;
        min-height: 36px;
        font-size: 13px;
        border: 1px solid transparent;
    }
    .los-btn-quiet:hover {
        background: rgba(73, 164, 162, 0.08);
        border-color: rgba(73, 164, 162, 0.2);
        transform: none;
    }
    .los-btn-ghost {
        background: var(--ml-ink);
        box-shadow: none;
        min-height: 40px;
        font-size: 13px;
    }
    .los-btn-ghost:hover { background: #0f1628; }

    /* B + D — differentiated surfaces */
    .los-pair {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--ml-space-2);
        margin-bottom: var(--ml-space-3);
    }
    .los-mission {
        padding: 16px 16px 14px;
        border-radius: var(--ml-r);
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
        border-inline-start: 3px solid var(--ml-yellow);
    }
    .los-mission h3 {
        margin: 6px 0 0;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.45;
    }
    .los-ai {
        padding: 16px;
        border-radius: var(--ml-r);
        background: rgba(73, 164, 162, 0.07);
        border: 1px solid rgba(73, 164, 162, 0.16);
    }
    .los-ai h3 {
        margin: 6px 0 0;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
    }
    .los-label {
        margin: 0;
        font-size: 11px;
        font-weight: 700;
        color: var(--ml-muted);
        letter-spacing: 0.02em;
    }
    .los-hint {
        margin: 8px 0 0;
        font-size: 12px;
        line-height: 1.55;
        color: var(--ml-muted);
    }
    .los-recs {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .los-recs span {
        font-size: 11px;
        font-weight: 600;
        color: var(--ml-teal-deep);
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(73, 164, 162, 0.18);
        border-radius: 8px;
        padding: 4px 8px;
    }

    /* C — continuous journey rail */
    .los-journey-block {
        margin-bottom: var(--ml-space-3);
        padding: 16px 18px 14px;
        border-radius: var(--ml-r);
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
    }
    .los-path {
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 14px;
    }
    .los-path::before {
        content: '';
        position: absolute;
        top: 11px;
        inset-inline: 12%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--ml-well) 12%, var(--ml-well) 88%, transparent);
        z-index: 0;
    }
    .los-node {
        position: relative;
        z-index: 1;
        text-align: center;
    }
    .los-dot {
        width: 12px;
        height: 12px;
        margin-inline: auto;
        border-radius: 50%;
        background: var(--ml-well);
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px var(--ml-line);
    }
    .los-node.is-now .los-dot {
        background: var(--ml-teal);
        box-shadow: 0 0 0 4px rgba(73, 164, 162, 0.18);
        animation: losPulse 1.8s var(--ml-ease) infinite;
    }
    @keyframes losPulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(73, 164, 162, 0.18); }
        50% { box-shadow: 0 0 0 7px rgba(73, 164, 162, 0.08); }
    }
    .los-node small {
        display: block;
        margin-top: 8px;
        font-size: 11px;
        color: var(--ml-muted);
        font-weight: 600;
    }
    .los-node strong {
        display: block;
        margin-top: 2px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.35;
    }
    .los-skills {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed var(--ml-line);
    }
    .los-skill {
        padding: 10px;
        border-radius: 10px;
        background: var(--ml-bg);
        transition: background var(--ml-fast) ease, transform var(--ml-fast) ease;
    }
    .los-skill:hover { background: rgba(73, 164, 162, 0.08); transform: translateY(-1px); }
    .los-skill b {
        display: block;
        font-size: 10px;
        color: var(--ml-teal-deep);
        font-weight: 700;
    }
    .los-skill span {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .los-skill i {
        display: block;
        height: 3px;
        margin-top: 8px;
        border-radius: 999px;
        background: var(--ml-well);
        overflow: hidden;
    }
    .los-skill i em {
        display: block;
        height: 100%;
        background: var(--ml-teal);
        font-style: normal;
        transform-origin: inline-start center;
        animation: losGrow var(--ml-slow) var(--ml-ease) both;
    }

    /* F + E */
    .los-split {
        display: grid;
        grid-template-columns: 1.4fr 0.9fr;
        gap: var(--ml-space-2);
        margin-bottom: var(--ml-space-3);
    }
    .los-plane {
        padding: 14px 16px;
        border-radius: var(--ml-r);
        background: var(--ml-surface);
        border: 1px solid var(--ml-line);
    }
    .los-plane-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }
    .los-plane-head h3 {
        margin: 2px 0 0;
        font-size: 14px;
        font-weight: 700;
    }
    .los-week {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 8px;
    }
    .los-day {
        text-align: center;
        padding: 6px 2px;
        border-radius: 8px;
        background: var(--ml-bg);
        font-size: 10px;
        color: var(--ml-muted);
        font-weight: 600;
    }
    .los-day strong {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: var(--ml-ink);
    }
    .los-day.is-today {
        background: rgba(73, 164, 162, 0.14);
        color: var(--ml-teal-deep);
    }
    .los-day.is-today strong { color: var(--ml-teal-deep); }
    .los-day.has-event strong::after {
        content: '';
        display: block;
        width: 4px;
        height: 4px;
        margin: 4px auto 0;
        border-radius: 50%;
        background: var(--ml-yellow);
    }
    .los-rows { list-style: none; margin: 0; padding: 0; }
    .los-rows li {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid var(--ml-line);
        font-size: 13px;
    }
    .los-rows li:last-child { border-bottom: 0; }
    .los-rows a {
        color: var(--ml-ink);
        font-weight: 600;
        text-decoration: none;
    }
    .los-rows a:hover { color: var(--ml-teal-deep); }
    .los-rows a:focus-visible {
        outline: 2px solid var(--ml-teal);
        outline-offset: 2px;
        border-radius: 4px;
    }
    .los-rows time {
        flex-shrink: 0;
        font-size: 11px;
        color: var(--ml-muted);
        font-weight: 600;
    }
    .los-link {
        font-size: 12px;
        font-weight: 700;
        color: var(--ml-teal-deep);
        text-decoration: none;
    }
    .los-link:hover { text-decoration: underline; }
    .los-link:focus-visible {
        outline: 2px solid var(--ml-yellow);
        outline-offset: 2px;
    }

    .los-heat {
        display: grid;
        grid-template-columns: repeat(14, 1fr);
        gap: 3px;
        margin-top: 10px;
    }
    .los-heat span {
        display: block;
        aspect-ratio: 1;
        border-radius: 2px;
        background: var(--ml-well);
    }
    .los-heat span[data-l="1"] { background: rgba(73, 164, 162, 0.28); }
    .los-heat span[data-l="2"] { background: rgba(73, 164, 162, 0.55); }
    .los-heat span[data-l="3"] { background: var(--ml-teal); }

    /* H — dense dock, not another fat card stack */
    .los-dock {
        border-radius: var(--ml-r);
        background: var(--ml-ink);
        color: #fff;
        padding: 14px 16px 12px;
    }
    .los-dock .los-label { color: rgba(255,255,255,0.55); }
    .los-dock-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
        margin-bottom: 12px;
    }
    .los-dock a.chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 10px;
        border-radius: 999px;
        background: rgba(255,255,255,0.08);
        color: #fff !important;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.06);
        transition: background var(--ml-fast) ease, transform var(--ml-fast) ease;
    }
    .los-dock a.chip:hover { background: rgba(73, 164, 162, 0.35); transform: translateY(-1px); }
    .los-dock a.chip:focus-visible {
        outline: 2px solid var(--ml-yellow);
        outline-offset: 2px;
    }
    .los-dock a.chip em {
        font-style: normal;
        opacity: 0.55;
        font-size: 11px;
    }
    .los-dock-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding-top: 4px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .los-dock-grid ul {
        list-style: none;
        margin: 6px 0 0;
        padding: 0;
    }
    .los-dock-grid li {
        padding: 5px 0;
        font-size: 12px;
        color: rgba(255,255,255,0.78);
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .los-dock-grid li:last-child { border-bottom: 0; }
    .los-dock-grid a {
        color: #fff !important;
        text-decoration: none !important;
        font-weight: 600;
    }
    .los-dock-grid a:hover { color: var(--ml-yellow) !important; }
    .los-dock-grid time {
        display: block;
        font-size: 10px;
        opacity: 0.45;
        margin-top: 2px;
    }

    .los-alert {
        margin-bottom: var(--ml-space-2);
        padding: 12px 14px;
        border-radius: var(--ml-r);
        background: rgba(255, 210, 63, 0.18);
        border: 1px solid rgba(255, 210, 63, 0.45);
    }
    .los-alert .los-label { color: var(--ml-yellow-ink); }

    .los-skip {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
    }
    .los-skip:focus {
        position: static;
        width: auto;
        height: auto;
        clip: auto;
        display: inline-flex;
        margin-bottom: 8px;
        padding: 8px 12px;
        background: var(--ml-teal);
        color: #fff;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
    }

    @media (max-width: 900px) {
        .los-stage { grid-template-columns: 1fr; }
        .los-stage-actions { align-items: start; }
        .los-pair, .los-split, .los-dock-grid, .los-skills { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .los-pair, .los-split, .los-dock-grid { grid-template-columns: 1fr; }
        .los-skills { grid-template-columns: 1fr 1fr; }
        .los-path::before { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .los-reveal, .los-meter > i, .los-skill i em, .los-node.is-now .los-dot {
            animation: none !important;
        }
        .los-btn, .los-skill, .los-dock a.chip { transition: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<a class="los-skip" href="#los-stage"><?php echo e(__('los.skip_to_next')); ?></a>

<div class="los" dir="<?php echo e($losRtl ? 'rtl' : 'ltr'); ?>">

    
    <header class="los-chrome los-reveal" style="--reveal-delay:0ms">
        <div>
            <h1><?php echo e($os['greeting'] ?? __('los.greeting_evening')); ?><?php echo e($losRtl ? '،' : ','); ?> <?php echo e($os['student_name'] ?? auth()->user()->name); ?></h1>
            <p class="sub"><?php echo e(__('los.learning_space')); ?> · <?php echo e($os['date_label'] ?? now()->format('Y-m-d')); ?></p>
        </div>
        <div class="los-signals" aria-label="<?php echo e(__('los.today_signals')); ?>">
            <?php if($streak > 0): ?>
                <span class="los-signal los-signal-hot"><?php echo e(__('los.streak_label', ['count' => $streak, 'unit' => $streak === 1 ? __('los.streak_day') : __('los.streak_days')])); ?></span>
            <?php endif; ?>
            <span class="los-signal los-signal-live"><?php echo e(__('los.progress_pct', ['pct' => (int) ($mastery['progress'] ?? 0)])); ?></span>
        </div>
    </header>

    <?php if(!empty($pendingScholarshipRegistrations) && $pendingScholarshipRegistrations->isNotEmpty() && ($stage['mode'] ?? '') !== 'blocking'): ?>
        <aside class="los-alert los-reveal" style="--reveal-delay:40ms" aria-label="<?php echo e(__('los.scholarships_pending')); ?>">
            <p class="los-label"><?php echo e(__('los.scholarships_pending')); ?></p>
            <ul class="los-rows">
                <?php $__currentLoopData = $pendingScholarshipRegistrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scholarshipRegistration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span style="font-weight:700;color:var(--ml-ink)"><?php echo e($scholarshipRegistration->program?->name); ?></span>
                        <time><?php echo e(__('los.pending')); ?></time>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </aside>
    <?php endif; ?>

    
    <section id="los-stage" class="los-stage los-reveal" style="--reveal-delay:60ms" aria-label="<?php echo e(__('los.what_now')); ?>">
        <div>
            <div class="los-eyebrow">
                <?php echo e(__('los.next_step')); ?>

                <em><?php echo e($stage['type_label'] ?? ''); ?></em>
            </div>
            <h2><?php echo e($stage['title'] ?? ''); ?></h2>
            <p class="los-copy"><?php echo e($stage['parent'] ?? ''); ?> — <?php echo e($stage['why'] ?? ''); ?></p>
            <?php if(!empty($stage['urgency'])): ?>
                <p class="los-urgency"><?php echo e(__('los.deadline', ['when' => $stage['urgency']])); ?></p>
            <?php endif; ?>
            <?php if(isset($stage['progress'])): ?>
                <div class="los-meter" role="progressbar" aria-valuenow="<?php echo e((int) $stage['progress']); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo e(__('los.course_progress')); ?>">
                    <i style="width: <?php echo e(min(100, (float) $stage['progress'])); ?>%"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="los-stage-actions">
            <a class="los-btn" href="<?php echo e($stage['cta_url'] ?? '#'); ?>"><?php echo e($stage['cta_label'] ?? __('los.continue')); ?></a>
            <?php if(!empty($stage['secondary_url'])): ?>
                <a class="los-btn los-btn-quiet" href="<?php echo e($stage['secondary_url']); ?>"><?php echo e($stage['secondary_label'] ?? __('los.other_options')); ?></a>
            <?php endif; ?>
        </div>
    </section>

    
    <div class="los-pair los-reveal" style="--reveal-delay:110ms">
        <section class="los-mission" aria-label="<?php echo e(__('los.mission_today')); ?>">
            <div style="display:flex;justify-content:space-between;gap:8px;align-items:center">
                <p class="los-label"><?php echo e(__('los.mission_today')); ?></p>
                <span class="los-signal <?php echo e(($mission['state'] ?? '') === 'open' ? 'los-signal-live' : ''); ?>"><?php echo e($mission['state_label'] ?? ''); ?></span>
            </div>
            <h3><?php echo e($mission['title'] ?? ''); ?></h3>
            <p class="los-hint"><?php echo e($mission['hint'] ?? ''); ?></p>
        </section>

        <section class="los-ai" id="los-ai" aria-label="<?php echo e(__('los.ai_guide')); ?>">
            <p class="los-label"><?php echo e(__('los.ai_guide')); ?></p>
            <h3><?php echo e($ai['insight'] ?? ''); ?></h3>
            <p class="los-hint"><?php echo e($ai['why'] ?? ''); ?></p>
            <div style="margin-top:12px">
                <a class="los-btn los-btn-ghost" href="<?php echo e($ai['action_url'] ?? '#'); ?>"><?php echo e($ai['action_label'] ?? __('los.start')); ?></a>
            </div>
            <?php if(!empty($ai['recommendations'])): ?>
                <div class="los-recs" aria-label="<?php echo e(__('los.recommendations')); ?>">
                    <?php $__currentLoopData = $ai['recommendations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span><?php echo e($rec); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    
    <section class="los-journey-block los-reveal" style="--reveal-delay:160ms" aria-label="<?php echo e(__('los.learning_journey')); ?>">
        <p class="los-label"><?php echo e(__('los.learning_journey')); ?></p>
        <h3 style="margin:4px 0 0;font-size:14px;font-weight:700"><?php echo e(__('los.journey_subtitle')); ?></h3>

        <div class="los-path">
            <div class="los-node">
                <div class="los-dot" aria-hidden="true"></div>
                <small><?php echo e(__('los.past')); ?></small>
                <strong><?php echo e($journey['past'] ?? ''); ?></strong>
            </div>
            <div class="los-node is-now">
                <div class="los-dot" aria-hidden="true"></div>
                <small><?php echo e(__('los.now')); ?></small>
                <strong><?php echo e($journey['present'] ?? ''); ?></strong>
            </div>
            <div class="los-node">
                <div class="los-dot" aria-hidden="true"></div>
                <small><?php echo e(__('los.next')); ?></small>
                <strong><?php echo e($journey['next'] ?? ''); ?></strong>
            </div>
        </div>

        <?php if(!empty($journey['recovery'])): ?>
            <p class="los-hint"><?php echo e($journey['recovery']); ?></p>
        <?php endif; ?>

        <p class="los-label" style="margin-top:8px"><?php echo e(__('los.skill_map')); ?></p>
        <div class="los-skills">
            <?php $__currentLoopData = $skillTree; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="los-skill">
                    <b><?php echo e($node['level']); ?></b>
                    <span title="<?php echo e($node['label']); ?>"><?php echo e($node['label']); ?></span>
                    <i aria-hidden="true"><em style="width: <?php echo e(min(100, (float)($node['progress'] ?? 0))); ?>%"></em></i>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <div class="los-split los-reveal" style="--reveal-delay:210ms">
        <section class="los-plane" aria-label="<?php echo e(__('los.planning')); ?>">
            <div class="los-plane-head">
                <div>
                    <p class="los-label"><?php echo e(__('los.this_week')); ?></p>
                    <h3><?php echo e(__('los.your_schedule')); ?></h3>
                </div>
                <a class="los-link" href="<?php echo e(route('calendar')); ?>"><?php echo e(__('los.open_calendar')); ?></a>
            </div>
            <div class="los-week" aria-label="<?php echo e(__('los.week_days')); ?>">
                <?php $__currentLoopData = $calendar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="los-day <?php echo e(!empty($day['today']) ? 'is-today' : ''); ?> <?php echo e(!empty($day['has_event']) ? 'has-event' : ''); ?>">
                        <?php echo e($day['label']); ?>

                        <strong><?php echo e($day['num']); ?></strong>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <ul class="los-rows">
                <?php $__empty_1 = true; $__currentLoopData = $planning; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <a href="<?php echo e($item['url']); ?>"><?php echo e($item['title']); ?></a>
                        <time><?php echo e($item['when']); ?></time>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li><span class="los-hint" style="margin:0"><?php echo e(__('los.no_critical_dates')); ?></span></li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="los-plane" aria-label="<?php echo e(__('los.insights_achievements')); ?>">
            <p class="los-label"><?php echo e(__('los.what_you_achieved')); ?></p>
            <h3 style="margin:4px 0 0;font-size:14px;font-weight:700;line-height:1.45"><?php echo e($mastery['whisper'] ?? ''); ?></h3>
            <p class="los-hint"><?php echo e(__('los.completed_avg', ['count' => (int) ($mastery['completed_courses'] ?? 0), 'pct' => (int) ($mastery['progress'] ?? 0)])); ?></p>
            <a class="los-link" href="<?php echo e($mastery['achievement_url'] ?? route('student.certificates.index')); ?>" style="display:inline-block;margin-top:8px"><?php echo e(__('los.certs_achievements')); ?></a>

            <p class="los-label" style="margin-top:14px"><?php echo e(__('los.rhythm_14')); ?></p>
            <div class="los-heat" role="img" aria-label="<?php echo e(__('los.activity_heatmap')); ?>">
                <?php $__currentLoopData = $heatmap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span data-l="<?php echo e((int)($cell['level'] ?? 0)); ?>" title="<?php echo e($cell['date'] ?? ''); ?>"></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </div>

    <?php if($goals): ?>
    <div class="los-split los-reveal" style="--reveal-delay:230ms;margin-bottom:24px">
        <section class="los-plane" aria-label="<?php echo e(__('los.your_goals')); ?>">
            <p class="los-label"><?php echo e($goals['weekly']['label'] ?? __('los.goal_week')); ?></p>
            <h3 style="margin:4px 0 0;font-size:14px;font-weight:700"><?php echo e($goals['weekly']['title'] ?? ''); ?></h3>
            <div class="los-meter" style="max-width:100%;margin-top:10px" role="progressbar" aria-valuenow="<?php echo e((int)($goals['weekly']['percent'] ?? 0)); ?>" aria-valuemin="0" aria-valuemax="100">
                <i style="width:<?php echo e(min(100, (int)($goals['weekly']['percent'] ?? 0))); ?>%"></i>
            </div>
            <p class="los-hint"><?php echo e((int)($goals['weekly']['done'] ?? 0)); ?> / <?php echo e((int)($goals['weekly']['target'] ?? 3)); ?> <?php echo e(__('los.sessions')); ?></p>
            <p class="los-label" style="margin-top:14px"><?php echo e($goals['monthly']['label'] ?? __('los.goal_month')); ?></p>
            <h3 style="margin:4px 0 0;font-size:13px;font-weight:700;line-height:1.45"><?php echo e($goals['monthly']['title'] ?? ''); ?></h3>
            <div class="los-meter" style="max-width:100%;margin-top:8px"><i style="width:<?php echo e(min(100, (int)($goals['monthly']['percent'] ?? 0))); ?>%"></i></div>
        </section>
        <section class="los-plane" aria-label="<?php echo e(__('los.career_progress')); ?>">
            <p class="los-label"><?php echo e($goals['career']['label'] ?? __('los.career_path')); ?></p>
            <h3 style="margin:4px 0 0;font-size:14px;font-weight:700;line-height:1.45"><?php echo e($goals['career']['title'] ?? ''); ?></h3>
            <a class="los-link" href="<?php echo e($goals['career']['url'] ?? route('student.certificates.index')); ?>" style="display:inline-block;margin-top:12px"><?php echo e(__('los.certs_record')); ?></a>
            <p class="los-hint" style="margin-top:10px"><?php echo e(__('los.next_achievement_hint')); ?></p>
        </section>
    </div>
    <?php endif; ?>

    
    <section class="los-dock los-reveal" style="--reveal-delay:260ms" aria-label="<?php echo e(__('los.library_activity')); ?>">
        <p class="los-label"><?php echo e(__('los.active_courses')); ?></p>
        <div class="los-dock-row">
            <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a class="chip" href="<?php echo e($course['url']); ?>">
                    <?php echo e(\Illuminate\Support\Str::limit($course['title'], 28)); ?>

                    <em><?php echo e($course['meta']); ?></em>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span style="font-size:12px;opacity:.65"><?php echo e(__('los.no_active_courses')); ?></span>
            <?php endif; ?>
        </div>

        <div class="los-dock-grid">
            <div>
                <p class="los-label"><?php echo e(__('los.quick_review')); ?></p>
                <ul>
                    <?php $__currentLoopData = array_slice($notes, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e($note['url']); ?>"><?php echo e($note['label']); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <div>
                <p class="los-label"><?php echo e(__('los.recent_activity')); ?></p>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = array_slice($timeline, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <?php echo e($event['label']); ?>

                            <time><?php echo e($event['when']); ?></time>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><?php echo e(__('los.activity_placeholder')); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/dashboard/student.blade.php ENDPATH**/ ?>