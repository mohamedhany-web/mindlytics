
<?php
    $paletteItems = [
        ['label' => 'مساحة التعلّم', 'hint' => 'الصفحة الرئيسية', 'url' => route('dashboard'), 'icon' => 'fa-house', 'search' => 'dashboard home مساحة'],
        ['label' => 'تابع التعلم', 'hint' => 'مقرراتي', 'url' => route('my-courses.index'), 'icon' => 'fa-book-open', 'search' => 'courses continue مقررات'],
        ['label' => 'الاختبارات', 'hint' => 'الاستعداد والنتائج', 'url' => route('student.exams.index'), 'icon' => 'fa-file-lines', 'search' => 'exams اختبار'],
        ['label' => 'الواجبات', 'hint' => 'التسليمات', 'url' => route('student.assignments.index'), 'icon' => 'fa-tasks', 'search' => 'assignments واجبات'],
        ['label' => 'التقويم', 'hint' => 'المواعيد', 'url' => route('calendar'), 'icon' => 'fa-calendar', 'search' => 'calendar تقويم'],
        ['label' => 'الشهادات', 'hint' => 'الإنجازات', 'url' => route('student.certificates.index'), 'icon' => 'fa-certificate', 'search' => 'certificates شهادات'],
        ['label' => 'الإشعارات', 'hint' => 'التنبيهات', 'url' => route('notifications'), 'icon' => 'fa-bell', 'search' => 'notifications إشعارات'],
        ['label' => 'المجموعات', 'hint' => 'التعلم الجماعي', 'url' => route('student.groups.index'), 'icon' => 'fa-users', 'search' => 'groups مجموعات'],
        ['label' => 'الملف الشخصي', 'hint' => 'حسابك', 'url' => route('profile'), 'icon' => 'fa-user', 'search' => 'profile ملف'],
        ['label' => 'موجّه الذكاء', 'hint' => 'انتقل لخطوة AI في المساحة', 'url' => route('dashboard').'#los-ai', 'icon' => 'fa-wand-magic-sparkles', 'search' => 'ai coach mentor ذكاء'],
    ];
    if (!auth()->user()?->usesScholarshipOnlyPortal()) {
        array_splice($paletteItems, 2, 0, [[
            'label' => 'استكشف المقررات',
            'hint' => 'الكتالوج',
            'url' => route('academic-years'),
            'icon' => 'fa-compass',
            'search' => 'browse catalog استكشف',
        ]]);
    }
?>

<div data-los-palette class="los-palette-backdrop" style="display:none" role="dialog" aria-modal="true" aria-label="لوحة الأوامر">
    <div class="los-palette" @click.stop>
        <input type="search"
               data-los-palette-input
               placeholder="ابحث عن صفحة، مقرر، أو إجراء…"
               autocomplete="off"
               aria-label="بحث سريع">
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
            <div data-los-palette-empty class="los-palette-empty" style="display:none">لا نتائج مطابقة</div>
        </div>
        <div class="los-palette-foot">
            <span>Enter للفتح</span>
            <span>Esc للإغلاق</span>
            <span>Ctrl/⌘ K</span>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\components\learning-os\command-palette.blade.php ENDPATH**/ ?>