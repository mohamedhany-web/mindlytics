
<?php
    $paletteItems = [
        ['label' => __('common.palette_learning_space'), 'hint' => __('common.palette_learning_space_hint'), 'url' => route('dashboard'), 'icon' => 'fa-house', 'search' => 'dashboard home مساحة'],
        ['label' => __('common.palette_continue'), 'hint' => __('common.palette_continue_hint'), 'url' => route('my-courses.index'), 'icon' => 'fa-book-open', 'search' => 'courses continue مقررات'],
        ['label' => __('student.exams'), 'hint' => __('common.palette_exams_hint'), 'url' => route('student.exams.index'), 'icon' => 'fa-file-lines', 'search' => 'exams اختبار'],
        ['label' => __('student.assignments'), 'hint' => __('common.palette_assignments_hint'), 'url' => route('student.assignments.index'), 'icon' => 'fa-tasks', 'search' => 'assignments واجبات'],
        ['label' => __('student.calendar'), 'hint' => __('common.palette_calendar_hint'), 'url' => route('calendar'), 'icon' => 'fa-calendar', 'search' => 'calendar تقويم'],
        ['label' => __('student.certificates'), 'hint' => __('common.palette_certificates_hint'), 'url' => route('student.certificates.index'), 'icon' => 'fa-certificate', 'search' => 'certificates شهادات'],
        ['label' => __('student.notifications'), 'hint' => __('common.palette_notifications_hint'), 'url' => route('notifications'), 'icon' => 'fa-bell', 'search' => 'notifications إشعارات'],
        ['label' => __('student.my_groups'), 'hint' => __('common.palette_groups_hint'), 'url' => route('student.groups.index'), 'icon' => 'fa-users', 'search' => 'groups مجموعات'],
        ['label' => __('student.profile'), 'hint' => __('common.palette_profile_hint'), 'url' => route('profile'), 'icon' => 'fa-user', 'search' => 'profile ملف'],
        ['label' => __('common.ai_guide'), 'hint' => __('common.palette_ai_hint'), 'url' => route('dashboard').'#los-ai', 'icon' => 'fa-wand-magic-sparkles', 'search' => 'ai coach mentor ذكاء'],
    ];
    if (!auth()->user()?->usesScholarshipOnlyPortal()) {
        array_splice($paletteItems, 2, 0, [[
            'label' => __('common.palette_browse'),
            'hint' => __('common.palette_browse_hint'),
            'url' => route('academic-years'),
            'icon' => 'fa-compass',
            'search' => 'browse catalog استكشف',
        ]]);
    }
?>

<div data-los-palette class="los-palette-backdrop" style="display:none" role="dialog" aria-modal="true" aria-label="<?php echo e(__('common.command_palette')); ?>">
    <div class="los-palette" @click.stop>
        <input type="search"
               data-los-palette-input
               placeholder="<?php echo e(__('common.palette_placeholder')); ?>"
               autocomplete="off"
               aria-label="<?php echo e(__('common.quick_search')); ?>">
        <div class="los-palette-list">
            <?php $__currentLoopData = $paletteItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['url']); ?>"
                   class="los-palette-item"
                   data-los-palette-item
                   data-search="<?php echo e($item['search']); ?> <?php echo e($item['label']); ?> <?php echo e($item['hint']); ?>"
                   @click="typeof MindlyticsLOS !== 'undefined' && MindlyticsLOS.closePalette()">
                    <i class="fas <?php echo e($item['icon']); ?>" aria-hidden="true"></i>
                    <span>
                        <?php echo e($item['label']); ?>

                        <small style="display:block;font-size:11px;font-weight:500;color:var(--ml-muted);margin-top:2px"><?php echo e($item['hint']); ?></small>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div data-los-palette-empty class="los-palette-empty" style="display:none"><?php echo e(__('common.palette_no_results')); ?></div>
        </div>
        <div class="los-palette-foot">
            <span><?php echo e(__('common.palette_enter')); ?></span>
            <span><?php echo e(__('common.palette_esc')); ?></span>
            <span>Ctrl/⌘ K</span>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/components/learning-os/command-palette.blade.php ENDPATH**/ ?>