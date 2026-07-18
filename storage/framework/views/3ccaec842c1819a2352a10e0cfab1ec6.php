<?php $__env->startSection('title', __('student.oc_lectures_page_title', ['title' => $offlineCourse->title])); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .lectures-hero {
        background: #fff;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid rgb(226 232 240);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .lectures-hero:hover {
        box-shadow: 0 4px 12px rgba(73, 164, 162, 0.1);
        border-color: rgba(73,164,162,0.35);
    }
    .lectures-hero .lectures-hero-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, rgb(139 92 246), rgb(99 102 241));
        border-radius: 0 16px 16px 0;
    }
    .lectures-stat {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .lectures-stat:hover {
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.12);
        border-color: rgb(196 181 253);
    }
    .lectures-panel {
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .lectures-panel-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid rgb(241 245 249);
        background: linear-gradient(to left, rgb(248 250 252), rgb(255 255 255));
    }

    .lecture-card {
        position: relative;
        overflow: hidden;
    }
    .lecture-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(900px 260px at 95% 0%, rgba(124, 58, 237, 0.08), transparent 60%),
                    radial-gradient(700px 220px at 5% 100%, rgba(73, 164, 162, 0.06), transparent 55%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 180ms ease;
    }
    .lecture-card:hover::before { opacity: 1; }

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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $chLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
    $lectureCount = $lectures->count();
    $dateLocale = app()->getLocale();
?>
<div class="w-full max-w-full space-y-6" x-data="window.__offlineLecturesPage()">
    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500" aria-label="<?php echo e(__('student.oc_breadcrumb')); ?>">
        <a href="<?php echo e(route('dashboard')); ?>" class="font-medium hover:text-teal-700"><?php echo e(__('los.page_title')); ?></a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="<?php echo e(route($sg . '.index')); ?>" class="font-medium hover:text-teal-700"><?php echo e($listTitle); ?></a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <a href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>" class="max-w-[10rem] truncate font-medium hover:text-teal-700 sm:max-w-xs"><?php echo e(\Illuminate\Support\Str::limit($offlineCourse->title, 40)); ?></a>
        <span class="text-slate-300" aria-hidden="true">/</span>
        <span class="font-semibold text-slate-800"><?php echo e(__('student.oc_lectures')); ?></span>
    </nav>

    <div class="lectures-hero">
        <div class="lectures-hero-accent" aria-hidden="true"></div>
        <div class="relative pr-2 sm:pr-3">
            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-violet-600"><?php echo e(__('student.oc_lectures_eyebrow', ['channel' => $chLabel])); ?></p>
            <h1 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl"><?php echo e(__('student.oc_lectures_list')); ?></h1>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600 sm:text-base">
                <?php echo e(__('student.oc_lectures_intro', ['title' => $offlineCourse->title])); ?>

            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="<?php echo e(route($sg . '.show', $offlineCourse)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-800 transition-colors hover:bg-slate-200">
                    <i class="fas fa-arrow-right text-slate-500"></i>
                    <?php echo e(__('student.oc_course_page')); ?>

                </a>
                <a href="<?php echo e(route($sg . '.curriculum', $offlineCourse)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-teal-200 bg-teal-50 px-4 py-2.5 text-sm font-bold text-sky-800 transition-colors hover:bg-teal-100">
                    <i class="fas fa-sitemap"></i>
                    <?php echo e(__('student.oc_curriculum')); ?>

                </a>
                <a href="<?php echo e(route($sg . '.schedule', $offlineCourse)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-800 transition-colors hover:bg-indigo-100">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo e(__('student.oc_schedule')); ?>

                </a>
                <a href="<?php echo e(route($sg . '.resources', $offlineCourse)); ?>" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-violet-700">
                    <i class="fas fa-file-alt"></i>
                    <?php echo e(__('student.oc_resources')); ?>

                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
        <div class="lectures-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e(__('student.oc_lecture_count')); ?></p>
            <p class="mt-1 text-2xl font-black text-violet-600"><?php echo e($lectureCount); ?></p>
        </div>
        <div class="lectures-stat text-center sm:text-start">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e(__('student.oc_learning_type')); ?></p>
            <p class="mt-1 text-lg font-black text-slate-800"><?php echo e($chLabel); ?></p>
        </div>
        <div class="lectures-stat hidden text-center sm:text-start lg:block">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo e(__('student.oc_group')); ?></p>
            <p class="mt-1 truncate text-lg font-bold text-slate-800" title="<?php echo e($enrollment->group->name ?? '—'); ?>"><?php echo e($enrollment->group->name ?? '—'); ?></p>
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
                        placeholder="<?php echo e(__('student.oc_search_lectures_placeholder')); ?>"
                        class="w-full min-w-0 border-0 bg-transparent p-0 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                    />
                    <button type="button" class="text-xs font-black text-slate-500 hover:text-slate-800" x-show="q.length" @click="q=''">
                        <?php echo e(__('student.oc_clear')); ?>

                    </button>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500">
                    <span class="chip">
                        <i class="fas fa-filter text-[10px] text-slate-400"></i>
                        <?php echo e(__('student.oc_showing')); ?>: <span class="text-slate-700" x-text="visibleCount"></span> / <?php echo e($lectureCount); ?>

                    </span>
                    <span class="chip" x-show="q.length">
                        <?php echo e(__('student.oc_search_results')); ?>

                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500" x-model="onlyWithMaterials">
                    <?php echo e(__('student.oc_materials_only')); ?>

                </label>
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500" x-model="onlyUpcoming">
                    <?php echo e(__('student.oc_upcoming_filter')); ?>

                </label>
                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500" x-model="onlyPast">
                    <?php echo e(__('student.oc_past_filter')); ?>

                </label>
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-black text-white shadow-sm hover:bg-slate-800"
                        @click="toggleAll()">
                    <i class="fas" :class="allExpanded ? 'fa-compress-alt' : 'fa-expand-alt'"></i>
                    <span x-text="allExpanded ? <?php echo json_encode(__('student.oc_collapse_all'), 15, 512) ?> : <?php echo json_encode(__('student.oc_expand_all'), 15, 512) ?>"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="lectures-panel">
        <div class="lectures-panel-head">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 ring-1 ring-violet-200/60">
                <i class="fas fa-chalkboard-teacher text-sm"></i>
            </span>
            <div class="min-w-0 text-start">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500"><?php echo e(__('student.oc_content')); ?></p>
                <p class="text-sm font-black text-slate-900"><?php echo e(__('student.oc_all_lectures_in_course')); ?></p>
            </div>
        </div>

        <?php if($lectures->isEmpty()): ?>
            <div class="px-6 py-16 text-center">
                <i class="fas fa-chalkboard-teacher mb-3 block text-5xl text-slate-300" aria-hidden="true"></i>
                <p class="font-bold text-slate-700"><?php echo e(__('student.oc_no_lectures')); ?></p>
                <p class="mt-2 text-sm text-slate-500"><?php echo e(__('student.oc_no_lectures_desc')); ?></p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100" role="list" x-ref="list">
                <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $agendaLines = filled($lecture->session_agenda)
                            ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $lecture->session_agenda))))
                            : [];
                        $linksCount = is_array($lecture->download_links) ? count($lecture->download_links) : 0;
                        $filesCount = is_array($lecture->attachments) ? count($lecture->attachments) : 0;
                        $hasMaterials = ($lecture->recording_url ? 1 : 0) + (($channel ?? 'offline') === 'online' && $lecture->meeting_url ? 1 : 0) + $linksCount + $filesCount;
                        $whenText = null;
                        $whenISO = null;
                        if ($lecture->relationLoaded('groupSession') && $lecture->groupSession) {
                            $whenText = $lecture->groupSession->session_date->locale($dateLocale)->translatedFormat('l j F Y');
                            $whenISO = optional($lecture->groupSession->session_date)->toDateString();
                        } elseif ($lecture->scheduled_at) {
                            $whenText = $lecture->scheduled_at->locale($dateLocale)->translatedFormat('l j F Y — H:i');
                            $whenISO = optional($lecture->scheduled_at)->toIso8601String();
                        }
                    ?>
                    <?php
                        $statusLabel = null;
                        $statusClasses = 'bg-slate-100 text-slate-700 border-slate-200';
                        if ($lecture->relationLoaded('groupSession') && $lecture->groupSession && $lecture->groupSession->session_date) {
                            $d = $lecture->groupSession->session_date;
                            if ($d->isToday()) { $statusLabel = __('student.oc_today'); $statusClasses = 'bg-amber-50 text-amber-800 border-amber-200'; }
                            elseif ($d->isFuture()) { $statusLabel = __('student.oc_status_upcoming'); $statusClasses = 'bg-emerald-50 text-emerald-800 border-emerald-200'; }
                            else { $statusLabel = __('student.oc_status_past'); $statusClasses = 'bg-slate-100 text-slate-700 border-slate-200'; }
                        } elseif ($lecture->scheduled_at) {
                            $d = $lecture->scheduled_at;
                            if ($d->isToday()) { $statusLabel = __('student.oc_today'); $statusClasses = 'bg-amber-50 text-amber-800 border-amber-200'; }
                            elseif ($d->isFuture()) { $statusLabel = __('student.oc_status_upcoming'); $statusClasses = 'bg-emerald-50 text-emerald-800 border-emerald-200'; }
                            else { $statusLabel = __('student.oc_status_past'); $statusClasses = 'bg-slate-100 text-slate-700 border-slate-200'; }
                        }
                    ?>
                    <li id="offline-lecture-<?php echo e($lecture->id); ?>"
                        class="scroll-mt-28 px-4 py-5 sm:px-6 sm:py-6"
                        data-title="<?php echo e(mb_strtolower((string) $lecture->title)); ?>"
                        data-description="<?php echo e(mb_strtolower((string) ($lecture->description ?? ''))); ?>"
                        data-agenda="<?php echo e(mb_strtolower((string) ($lecture->session_agenda ?? ''))); ?>"
                        data-has-materials="<?php echo e($hasMaterials > 0 ? '1' : '0'); ?>"
                        data-when="<?php echo e($whenISO ?? ''); ?>"
                        x-show="matches($el)"
                        x-transition.opacity.duration.150ms
                    >
                        <details class="lecture-card group rounded-2xl border border-slate-200 bg-white shadow-sm hover:border-teal-200 hover:shadow-md transition-all"
                                 :open="allExpanded"
                                 @toggle="onToggle($event)">
                            <summary class="cursor-pointer list-none p-4 sm:p-5 select-none">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-100 text-violet-700 ring-1 ring-violet-200/60 flex-shrink-0">
                                                <i class="fas fa-chalkboard-teacher text-sm"></i>
                                            </span>
                                            <h2 class="min-w-0 flex-1 text-base sm:text-lg font-black leading-snug text-slate-900 break-words">
                                                <?php echo e($lecture->title); ?>

                                            </h2>
                                            <?php if($statusLabel): ?>
                                                <span class="chip border <?php echo e($statusClasses); ?>">
                                                    <i class="fas fa-circle text-[8px]"></i>
                                                    <?php echo e($statusLabel); ?>

                                                </span>
                                            <?php endif; ?>
                                            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-black text-slate-700">
                                                <i class="fas fa-paperclip text-slate-400 text-[10px]"></i>
                                                <?php echo e(__('student.oc_materials_count', ['count' => $hasMaterials])); ?>

                                            </span>
                                        </div>
                                        <?php if($whenText): ?>
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1">
                                                    <i class="far fa-calendar-check text-slate-500"></i>
                                                    <span><?php echo e($whenText); ?></span>
                                                </span>
                                                <?php if($lecture->relationLoaded('groupSession') && $lecture->groupSession): ?>
                                                    <?php $lgt = $lecture->groupSession->start_time; ?>
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1">
                                                        <i class="far fa-clock text-slate-500"></i>
                                                        <span><?php echo e(is_string($lgt) ? substr($lgt, 0, 5) : $lgt); ?></span>
                                                    </span>
                                                    <?php if($lecture->groupSession->group): ?>
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1">
                                                            <i class="fas fa-users text-slate-500 text-[10px]"></i>
                                                            <span class="truncate max-w-[14rem]"><?php echo e($lecture->groupSession->group->name); ?></span>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if($lecture->description): ?>
                                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                                <?php echo e(Str::limit($lecture->description, 220)); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-400 flex-shrink-0">
                                        <span class="inline-flex items-center gap-1 text-xs font-black">
                                            <span class="hidden sm:inline"><?php echo e(__('student.oc_details')); ?></span>
                                        </span>
                                        <i class="fas fa-chevron-down text-sm transition-transform duration-200 group-open:rotate-180"></i>
                                    </div>
                                </div>
                            </summary>

                            <div class="px-4 sm:px-5 pb-5">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div class="lg:col-span-2 space-y-4">
                                        <?php if(count($agendaLines)): ?>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="text-xs font-black text-slate-700"><?php echo e(__('student.oc_session_agenda')); ?></p>
                                                    <span class="text-[11px] font-black text-slate-500"><?php echo e(__('student.oc_agenda_points', ['count' => count($agendaLines)])); ?></span>
                                                </div>
                                                <ul class="mt-3 space-y-2 text-sm text-slate-700 max-h-56 overflow-auto pr-1">
                                                    <?php $__currentLoopData = $agendaLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="flex gap-2 leading-relaxed">
                                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                                            <span><?php echo e($line); ?></span>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>

                                        <?php echo $__env->make('partials.offline-mindmap-visual', ['text' => $lecture->offline_attendee_mindmap], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-xs font-black text-slate-800"><?php echo e(__('student.oc_materials_and_attachments')); ?></p>
                                                <span class="text-[11px] font-black text-slate-500"><?php echo e($hasMaterials); ?></span>
                                            </div>

                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                                                <?php if(($channel ?? 'offline') === 'online' && $lecture->meeting_url): ?>
                                                    <a href="<?php echo e($lecture->meeting_url); ?>" target="_blank" rel="noopener"
                                                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-3.5 py-2.5 text-xs font-black text-white hover:bg-indigo-700 justify-center">
                                                        <i class="fas fa-video"></i>
                                                        <?php echo e(__('student.oc_live_stream')); ?>

                                                    </a>
                                                <?php endif; ?>

                                                <?php if($lecture->recording_url): ?>
                                                    <a href="<?php echo e(route($studentRouteGroup . '.lectures.watch', [$offlineCourse, $lecture])); ?>"
                                                       class="inline-flex items-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-3.5 py-2.5 text-xs font-black text-violet-800 hover:bg-violet-100 justify-center">
                                                        <i class="fas fa-play"></i>
                                                        <?php echo e(__('student.oc_recording')); ?>

                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <?php if($linksCount > 0): ?>
                                                <div class="mt-3">
                                                    <p class="text-[11px] font-black text-slate-500 mb-2"><?php echo e(__('student.oc_download_links')); ?></p>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                                                        <?php $__currentLoopData = $lecture->download_links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <a href="<?php echo e($link['url'] ?? '#'); ?>" target="_blank" rel="noopener"
                                                               class="group inline-flex items-center gap-2 rounded-xl border border-teal-200 bg-teal-50 px-3 py-2.5 text-xs font-black text-sky-800 hover:bg-teal-100">
                                                                <i class="fas fa-download flex-shrink-0"></i>
                                                                <span class="truncate min-w-0"><?php echo e($link['label'] ?? __('student.oc_download')); ?></span>
                                                            </a>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if($filesCount > 0): ?>
                                                <div class="mt-3">
                                                    <p class="text-[11px] font-black text-slate-500 mb-2"><?php echo e(__('student.oc_attachments')); ?></p>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                                                        <?php $__currentLoopData = $lecture->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $name = (string) ($att['name'] ?? __('student.oc_file'));
                                                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                                                $icon = 'fa-file';
                                                                if (in_array($ext, ['pdf'])) $icon = 'fa-file-pdf';
                                                                elseif (in_array($ext, ['ppt', 'pptx'])) $icon = 'fa-file-powerpoint';
                                                                elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = 'fa-file-excel';
                                                                elseif (in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word';
                                                                elseif (in_array($ext, ['png','jpg','jpeg','webp','gif'])) $icon = 'fa-file-image';
                                                                elseif (in_array($ext, ['zip','rar','7z'])) $icon = 'fa-file-archive';
                                                            ?>
                                                            <a href="<?php echo e(asset('storage/' . ($att['path'] ?? ''))); ?>" target="_blank" rel="noopener"
                                                               class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-black text-slate-700 hover:bg-slate-50">
                                                                <i class="fas <?php echo e($icon); ?> text-slate-500 flex-shrink-0"></i>
                                                                <span class="truncate min-w-0"><?php echo e($name); ?></span>
                                                            </a>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
window.__offlineLecturesPage = function () {
    return {
        q: '',
        onlyWithMaterials: false,
        onlyUpcoming: false,
        onlyPast: false,
        allExpanded: false,
        visibleCount: <?php echo e((int) $lectureCount); ?>,
        normalize(s) {
            return (s || '').toString().toLowerCase().trim();
        },
        parseWhen(el) {
            const v = (el && el.dataset && el.dataset.when) ? el.dataset.when : '';
            if (!v) return null;
            const d = new Date(v);
            return isNaN(d.getTime()) ? null : d;
        },
        isUpcoming(el) {
            const d = this.parseWhen(el);
            if (!d) return false;
            const now = new Date();
            return d.getTime() >= now.getTime() - (1000 * 60 * 60 * 12);
        },
        isPast(el) {
            const d = this.parseWhen(el);
            if (!d) return false;
            const now = new Date();
            return d.getTime() < now.getTime() - (1000 * 60 * 60 * 12);
        },
        matches(el) {
            if (!el) return true;

            if (this.onlyWithMaterials && (el.dataset.hasMaterials !== '1')) {
                return false;
            }

            if (this.onlyUpcoming && !this.isUpcoming(el)) {
                return false;
            }

            if (this.onlyPast && !this.isPast(el)) {
                return false;
            }

            const q = this.normalize(this.q);
            if (!q) return true;

            const hay = [
                el.dataset.title || '',
                el.dataset.description || '',
                el.dataset.agenda || ''
            ].join(' ');
            return hay.includes(q);
        },
        recount() {
            try {
                const list = this.$refs.list;
                if (!list) return;
                const items = Array.from(list.querySelectorAll('li'));
                const visible = items.filter(li => this.matches(li)).length;
                this.visibleCount = visible;
            } catch (e) {
                // ignore
            }
        },
        toggleAll() {
            this.allExpanded = !this.allExpanded;
        },
        onToggle() {
            // keep for future (analytics)
        },
        init() {
            this.$watch('q', () => this.recount());
            this.$watch('onlyWithMaterials', () => this.recount());
            this.$watch('onlyUpcoming', () => this.recount());
            this.$watch('onlyPast', () => this.recount());
            this.recount();
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/lectures.blade.php ENDPATH**/ ?>