<?php $__env->startSection('title', __('public.course_mind_map_page_title', ['course' => $course->localized('title') ?? __('public.course_fallback')]) . ' - ' . __('public.site_suffix')); ?>

<?php
    $cid = (int) $course->id;
    $gradId = 'mindZigGrad'.$cid;
    $pathId = 'mindDecorPath'.$cid;
    $stepCount = count($steps);
?>

<?php $__env->startPush('styles'); ?>
<style>
    .mind-zigzag-wrap {
        position: relative;
        overflow: hidden;
        background: linear-gradient(165deg, #f0f9ff 0%, #ecfeff 22%, #eef2ff 48%, #f8fafc 100%);
    }
    .mind-zigzag-wrap::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 45%),
            radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.09) 0%, transparent 42%),
            radial-gradient(circle at 50% 10%, rgba(139, 92, 246, 0.06) 0%, transparent 35%);
        pointer-events: none;
    }
    .mind-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(56px);
        opacity: 0.45;
        z-index: 0;
        animation: mindOrbFloat 22s ease-in-out infinite;
    }
    .mind-orb--a { width: 220px; height: 220px; background: rgba(59, 130, 246, 0.55); top: 8%; left: -4%; animation-delay: 0s; }
    .mind-orb--b { width: 280px; height: 280px; background: rgba(16, 185, 129, 0.45); bottom: 15%; right: -8%; animation-delay: -7s; }
    .mind-orb--c { width: 160px; height: 160px; background: rgba(139, 92, 246, 0.4); top: 42%; right: 5%; animation-delay: -12s; }
    @keyframes mindOrbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(18px, -22px) scale(1.06); }
        66% { transform: translate(-14px, 12px) scale(0.96); }
    }
    .mind-spine {
        position: absolute;
        left: 50%;
        top: 2rem;
        bottom: 5rem;
        width: 6px;
        transform: translateX(-50%);
        border-radius: 999px;
        background: linear-gradient(180deg,
            #10b981 0%,
            #14b8a6 18%,
            #0ea5e9 42%,
            #6366f1 68%,
            #8b5cf6 100%);
        box-shadow: 0 0 24px rgba(14, 165, 233, 0.35);
        opacity: 0.92;
        z-index: 1;
    }
    .mind-path-svg {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: min(320px, 70vw);
        transform: translateX(-50%);
        z-index: 2;
        pointer-events: none;
        overflow: visible;
    }
    .mind-path-svg path {
        fill: none;
        stroke: url(#<?php echo e($gradId); ?>);
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: 0.55;
        filter: drop-shadow(0 0 6px rgba(14, 165, 233, 0.35));
    }
    .mind-bridge {
        position: absolute;
        top: 50%;
        height: 3px;
        border-radius: 3px;
        transform: translateY(-50%);
        z-index: 4;
        pointer-events: none;
        opacity: 0.85;
    }
    .mind-bridge--to-start {
        right: calc(50% + 28px);
        width: min(42vw, 220px);
        background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.55), rgba(16, 185, 129, 0.9));
        transform: translateY(-50%) rotate(-8deg);
        transform-origin: right center;
    }
    .mind-bridge--to-end {
        left: calc(50% + 28px);
        width: min(42vw, 220px);
        background: linear-gradient(270deg, transparent, rgba(99, 102, 241, 0.55), rgba(99, 102, 241, 0.95));
        transform: translateY(-50%) rotate(8deg);
        transform-origin: left center;
    }
    @media (max-width: 767px) {
        .mind-bridge { display: none; }
        .mind-spine { display: none; }
        .mind-path-svg { opacity: 0.35; width: 100px; }
    }
    [dir="rtl"] .mind-bridge--to-start {
        right: auto;
        left: calc(50% + 28px);
        transform: translateY(-50%) rotate(8deg);
        transform-origin: left center;
        background: linear-gradient(270deg, transparent, rgba(16, 185, 129, 0.55), rgba(16, 185, 129, 0.9));
    }
    [dir="rtl"] .mind-bridge--to-end {
        left: auto;
        right: calc(50% + 28px);
        transform: translateY(-50%) rotate(-8deg);
        transform-origin: right center;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.55), rgba(99, 102, 241, 0.95));
    }
    .mind-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow:
            0 4px 6px -1px rgba(15, 23, 42, 0.06),
            0 12px 24px -8px rgba(59, 130, 246, 0.12);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease, border-color 0.25s ease;
    }
    .mind-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, 0.75) 50%, transparent 65%);
        background-size: 220% 100%;
        opacity: 0;
        pointer-events: none;
    }
    .mind-card:hover::after {
        opacity: 1;
        animation: mindShine 1.35s ease-in-out infinite;
    }
    @keyframes mindShine {
        from { background-position: 130% 0; }
        to { background-position: -130% 0; }
    }
    .mind-card:hover {
        box-shadow:
            0 12px 28px -6px rgba(59, 130, 246, 0.22),
            0 24px 48px -14px rgba(16, 185, 129, 0.14);
        border-color: rgba(125, 211, 252, 0.95);
    }
    .mind-card--tilt-r { transform: rotate(0.65deg); }
    .mind-card--tilt-r:hover { transform: translateY(-6px) scale(1.02) rotate(0.4deg); }
    .mind-card--tilt-l { transform: rotate(-0.65deg); }
    .mind-card--tilt-l:hover { transform: translateY(-6px) scale(1.02) rotate(-0.4deg); }
    .mind-node {
        position: relative;
        z-index: 6;
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.95rem;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease;
    }
    .mind-row:not(.is-inview) .mind-node {
        animation: none !important;
    }
    .mind-row.is-inview .mind-node {
        animation: mindNodePop 0.65s cubic-bezier(0.34, 1.56, 0.64, 1) both,
            mindPulse 2.8s ease-in-out 0.65s infinite;
    }
    @keyframes mindNodePop {
        from { transform: scale(0.6) rotate(-12deg); opacity: 0.5; }
        to { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes mindPulse {
        0%, 100% { box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12), 0 0 0 0 rgba(14, 165, 233, 0.25); }
        50% { box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2), 0 0 0 10px rgba(14, 165, 233, 0); }
    }
    .mind-row {
        position: relative;
        z-index: 5;
        min-height: 7.5rem;
        padding-top: 0.35rem;
        padding-bottom: 0.35rem;
        opacity: 0;
        transform: translateY(48px) scale(0.96);
        filter: blur(8px);
        transition:
            opacity 0.85s cubic-bezier(0.22, 1, 0.36, 1),
            transform 0.85s cubic-bezier(0.22, 1, 0.36, 1),
            filter 0.65s ease;
    }
    .mind-row.is-inview {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }
    @media (max-width: 767px) {
        .mind-row { filter: none; transform: translateY(28px) scale(0.98); }
        .mind-row.is-inview { transform: translateY(0) scale(1); }
    }
    .mind-progress-wrap {
        position: sticky;
        top: 5.5rem;
        z-index: 30;
        margin-bottom: 1.5rem;
    }
    .mind-progress-glow {
        height: 0.5rem;
        border-radius: 999px;
        background: rgba(226, 232, 240, 0.95);
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.06);
    }
    .mind-progress-fill {
        height: 100%;
        width: 0%;
        border-radius: 999px;
        background: linear-gradient(90deg, #10b981, #0ea5e9, #6366f1, #8b5cf6);
        background-size: 200% 100%;
        animation: mindProgressHue 6s linear infinite;
        transition: width 0.55s cubic-bezier(0.22, 1, 0.36, 1);
        box-shadow: 0 0 16px rgba(14, 165, 233, 0.45);
    }
    @keyframes mindProgressHue {
        from { background-position: 0% 50%; }
        to { background-position: 200% 50%; }
    }
    .mind-zigzag-svg {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 280px;
        max-width: 55vw;
        transform: translateX(-50%);
        z-index: 0;
        pointer-events: none;
        opacity: 0.1;
    }
    @media (prefers-reduced-motion: reduce) {
        .mind-orb, .mind-progress-fill { animation: none !important; }
        .mind-node { animation: none !important; }
        .mind-card::after { animation: none !important; opacity: 0 !important; }
        .animate-bounce { animation: none !important; }
        .mind-row {
            opacity: 1 !important;
            transform: none !important;
            filter: none !important;
            transition: none !important;
        }
        .mind-path-svg path { stroke-dashoffset: 0 !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="mind-zigzag-wrap pt-24 pb-20" id="mind-map-scene" data-step-total="<?php echo e($stepCount); ?>" data-progress-label="<?php echo e(e(__('public.course_mind_map_progress'))); ?>">
    <div class="mind-orb mind-orb--a" aria-hidden="true"></div>
    <div class="mind-orb mind-orb--b" aria-hidden="true"></div>
    <div class="mind-orb mind-orb--c" aria-hidden="true"></div>

    <svg class="mind-zigzag-svg" viewBox="0 0 100 400" preserveAspectRatio="none" aria-hidden="true">
        <path fill="none" stroke="url(#<?php echo e($gradId); ?>Bg)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
              d="M50 0 L72 45 L28 90 L72 135 L28 180 L72 225 L28 270 L72 315 L50 360" opacity="0.5"/>
        <defs>
            <linearGradient id="<?php echo e($gradId); ?>Bg" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color:#10b981"/>
                <stop offset="50%" style="stop-color:#3b82f6"/>
                <stop offset="100%" style="stop-color:#8b5cf6"/>
            </linearGradient>
        </defs>
    </svg>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="mb-6 text-slate-600 text-sm flex flex-wrap items-center gap-1">
            <a href="<?php echo e(url('/')); ?>" class="hover:text-blue-600 transition-colors"><?php echo e(__('public.home')); ?></a>
            <span class="text-slate-400">/</span>
            <a href="<?php echo e(route('public.courses')); ?>" class="hover:text-blue-600 transition-colors"><?php echo e(__('public.courses')); ?></a>
            <span class="text-slate-400">/</span>
            <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="hover:text-blue-600 transition-colors"><?php echo e(Str::limit($course->localized('title') ?? __('public.course_fallback'), 40)); ?></a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-900 font-semibold"><?php echo e(__('public.course_mind_map_short')); ?></span>
        </nav>

        <div class="text-center max-w-2xl mx-auto mb-10 md:mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-emerald-200/80 text-emerald-800 text-xs font-bold shadow-sm mb-4">
                <i class="fas fa-wand-magic-sparkles text-violet-600"></i>
                <?php echo e(__('public.course_mind_map_badge')); ?>

            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-3 leading-tight tracking-tight">
                <?php echo e(__('public.course_mind_map_heading')); ?>

            </h1>
            <p class="text-lg sm:text-xl text-slate-700 font-bold"><?php echo e($course->localized('title') ?? __('public.course_title_fallback')); ?></p>
            <p class="text-slate-500 text-sm mt-3 leading-relaxed"><?php echo e(__('public.course_mind_map_intro')); ?></p>
            <p class="text-slate-400 text-xs mt-2 flex items-center justify-center gap-2">
                <i class="fas fa-computer-mouse text-sky-500 animate-bounce"></i>
                <?php echo e(__('public.course_mind_map_scroll_hint')); ?>

            </p>
        </div>

        <?php $timetableBody = trim((string) ($course->mind_map_timetable ?? '')); ?>
        <?php if($timetableBody !== ''): ?>
            <div class="max-w-4xl mx-auto mb-10 md:mb-12">
                <div class="rounded-2xl border border-amber-200/90 bg-gradient-to-br from-amber-50/95 via-white to-orange-50/80 p-5 sm:p-6 shadow-md">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shrink-0">
                            <i class="fas fa-calendar-week text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900"><?php echo e(__('public.course_mind_map_timetable_title')); ?></h2>
                            <p class="text-xs text-amber-900/70 font-medium"><?php echo e(__('public.course_mind_map_timetable_sub')); ?></p>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white/90 border border-amber-100 px-4 py-4 text-slate-800 text-sm sm:text-base leading-relaxed whitespace-pre-line"><?php echo e($timetableBody); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="relative max-w-4xl mx-auto" id="mind-track">
            <div class="mind-spine hidden md:block" aria-hidden="true"></div>

            <svg class="mind-path-svg hidden md:block" viewBox="0 0 100 1000" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="<?php echo e($gradId); ?>" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#10b981"/>
                        <stop offset="35%" style="stop-color:#0ea5e9"/>
                        <stop offset="70%" style="stop-color:#6366f1"/>
                        <stop offset="100%" style="stop-color:#8b5cf6"/>
                    </linearGradient>
                </defs>
                <path id="<?php echo e($pathId); ?>" d="M50 0 C58 80 42 160 50 240 C58 320 42 400 50 480 C58 560 42 640 50 720 C58 800 42 880 50 960 L50 1000"/>
            </svg>

            <div class="mind-progress-wrap hidden sm:block max-w-xl mx-auto px-2">
                <div class="mind-progress-glow" role="progressbar" aria-valuemin="0" aria-valuemax="<?php echo e($stepCount); ?>" aria-valuenow="0" id="mind-progress-bar-wrap">
                    <div class="mind-progress-fill" id="mind-progress-fill"></div>
                </div>
                <p class="text-center text-xs text-slate-500 mt-2 font-medium" id="mind-progress-text"></p>
            </div>

            <ol class="relative z-10 list-none p-0 m-0 space-y-2 md:space-y-0" id="mind-steps-list">
                <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isFirst = $index === 0;
                        $isLast = $index === $stepCount - 1;
                        $onStartSide = ($index % 2) === 0;
                    ?>
                    <li class="mind-row" data-mind-index="<?php echo e($index); ?>">
                        <div class="md:hidden flex gap-4 items-start ps-1 border-s-4 <?php echo e($onStartSide ? 'border-s-emerald-400' : 'border-s-indigo-400'); ?> rounded-s-xl ps-3 -ms-1">
                            <div class="flex-shrink-0 pt-1">
                                <?php if($isFirst): ?>
                                    <div class="mind-node bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-4 ring-emerald-100/90">
                                        <i class="fas fa-flag-checkered text-sm"></i>
                                    </div>
                                <?php elseif($isLast): ?>
                                    <div class="mind-node bg-gradient-to-br from-indigo-600 to-violet-600 text-white ring-4 ring-violet-100/90">
                                        <i class="fas fa-flag text-sm"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="mind-node bg-white text-sky-700 border-2 border-sky-200 ring-4 ring-sky-50"><?php echo e($index); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mind-card flex-1 p-4 sm:p-5 <?php echo e($onStartSide ? 'mind-card--tilt-r' : 'mind-card--tilt-l'); ?>">
                                <?php echo $__env->make('public.partials.course-mind-map-card-inner', [
                                    'isFirst' => $isFirst,
                                    'isLast' => $isLast,
                                    'index' => $index,
                                    'step' => $step,
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </div>

                        <div class="hidden md:grid md:grid-cols-[1fr_auto_1fr] md:gap-x-6 md:gap-y-0 md:items-center md:min-h-[9rem] relative">
                            <?php if($onStartSide): ?>
                                <div class="flex justify-end pe-4 lg:pe-10 relative">
                                    <span class="mind-bridge mind-bridge--to-start hidden lg:block" aria-hidden="true"></span>
                                    <div class="mind-card w-full max-w-md p-5 lg:p-6 mind-card--tilt-r">
                                        <?php echo $__env->make('public.partials.course-mind-map-card-inner', [
                                            'isFirst' => $isFirst,
                                            'isLast' => $isLast,
                                            'index' => $index,
                                            'step' => $step,
                                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>
                                </div>
                                <div class="flex justify-center py-2">
                                    <?php if($isFirst): ?>
                                        <div class="mind-node bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-4 ring-emerald-100/90">
                                            <i class="fas fa-flag-checkered text-lg"></i>
                                        </div>
                                    <?php elseif($isLast): ?>
                                        <div class="mind-node bg-gradient-to-br from-indigo-600 to-violet-600 text-white ring-4 ring-violet-100/90">
                                            <i class="fas fa-flag text-lg"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="mind-node w-14 h-14 text-lg bg-white text-sky-700 border-2 border-sky-200 ring-4 ring-sky-50"><?php echo e($index); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div></div>
                            <?php else: ?>
                                <div></div>
                                <div class="flex justify-center py-2">
                                    <?php if($isFirst): ?>
                                        <div class="mind-node bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-4 ring-emerald-100/90">
                                            <i class="fas fa-flag-checkered text-lg"></i>
                                        </div>
                                    <?php elseif($isLast): ?>
                                        <div class="mind-node bg-gradient-to-br from-indigo-600 to-violet-600 text-white ring-4 ring-violet-100/90">
                                            <i class="fas fa-flag text-lg"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="mind-node w-14 h-14 text-lg bg-white text-sky-700 border-2 border-sky-200 ring-4 ring-sky-50"><?php echo e($index); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex justify-start ps-4 lg:ps-10 relative">
                                    <span class="mind-bridge mind-bridge--to-end hidden lg:block" aria-hidden="true"></span>
                                    <div class="mind-card w-full max-w-md p-5 lg:p-6 mind-card--tilt-l">
                                        <?php echo $__env->make('public.partials.course-mind-map-card-inner', [
                                            'isFirst' => $isFirst,
                                            'isLast' => $isLast,
                                            'index' => $index,
                                            'step' => $step,
                                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </div>

        <div class="mt-14 md:mt-20 text-center">
            <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-sky-500 to-emerald-500 text-white px-8 py-3.5 rounded-full font-bold shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-300 hover:-translate-y-0.5">
                <i class="fas fa-arrow-right"></i>
                <?php echo e(__('public.course_mind_map_back_course')); ?>

            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var scene = document.getElementById('mind-map-scene');
    var track = document.getElementById('mind-track');
    var path = document.getElementById('<?php echo e($pathId); ?>');
    var rows = document.querySelectorAll('.mind-row');
    var fill = document.getElementById('mind-progress-fill');
    var text = document.getElementById('mind-progress-text');
    var barWrap = document.getElementById('mind-progress-bar-wrap');
    if (!scene || !track || !rows.length) return;

    var total = parseInt(scene.getAttribute('data-step-total'), 10) || rows.length;
    var labelTpl = scene.getAttribute('data-progress-label') || '';

    function setProgress(revealed) {
        var n = Math.max(0, Math.min(total, revealed));
        var pct = total ? Math.round((n / total) * 100) : 0;
        if (fill) fill.style.width = pct + '%';
        if (text) text.textContent = labelTpl.replace(':current', String(n)).replace(':total', String(total));
        if (barWrap) barWrap.setAttribute('aria-valuenow', String(n));
    }

    function updatePathScroll() {
        if (!path) return;
        try {
            var len = path.getTotalLength();
            if (!len || !isFinite(len)) return;
            path.style.strokeDasharray = String(len);
            var rect = track.getBoundingClientRect();
            var vh = window.innerHeight || 800;
            var start = rect.top;
            var span = rect.height + vh * 0.5;
            var raw = (vh * 0.42 - start) / span;
            var p = Math.min(1, Math.max(0, raw));
            path.style.strokeDashoffset = String(len * (1 - p * 0.92));
        } catch (e) {}
    }

    var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced) {
        rows.forEach(function (r) { r.classList.add('is-inview'); });
        setProgress(total);
        if (path) {
            try {
                var L = path.getTotalLength();
                path.style.strokeDasharray = String(L);
                path.style.strokeDashoffset = '0';
            } catch (e2) {}
        }
        return;
    }

    var maxSeen = -1;
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var idx = parseInt(el.getAttribute('data-mind-index'), 10);
            if (isNaN(idx)) return;
            el.classList.add('is-inview');
            if (idx > maxSeen) {
                maxSeen = idx;
                setProgress(maxSeen + 1);
            }
        });
    }, { root: null, rootMargin: '0px 0px -6% 0px', threshold: 0.08 });

    rows.forEach(function (r) { io.observe(r); });
    if (typeof io.takeRecords === 'function') {
        io.takeRecords().forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var idx = parseInt(el.getAttribute('data-mind-index'), 10);
            if (isNaN(idx)) return;
            el.classList.add('is-inview');
            if (idx > maxSeen) {
                maxSeen = idx;
                setProgress(maxSeen + 1);
            }
        });
    }

    var ticking = false;
    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
            updatePathScroll();
            ticking = false;
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    onScroll();
    setProgress(0);
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/course-mind-map.blade.php ENDPATH**/ ?>