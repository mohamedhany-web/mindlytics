
<?php
    $user = auth()->user();
    $isStudent = $user->role === 'student' || strtolower((string) $user->role) === 'student';
    $scholarshipOnlyPortal = $isStudent && $user->usesScholarshipOnlyPortal();

    $activeCoursesCount = $scholarshipOnlyPortal
        ? $user->courseEnrollments()->scholarshipOnly()->where('status', 'active')->count()
        : $user->activeCourses()->count();

    $progressEnrollments = $scholarshipOnlyPortal
        ? $user->courseEnrollments()->scholarshipOnly()->whereIn('status', ['active', 'completed'])->get()
        : $user->courseEnrollments()->whereIn('status', ['active', 'completed'])->get();
    $totalProgress = $progressEnrollments->isEmpty() ? 0 : (int) round($progressEnrollments->avg('progress') ?? 0, 0);

    $myGroupsCount = 0;
    if (! $scholarshipOnlyPortal && ($isStudent || $user->hasPermission('student.view.my-courses'))) {
        $myGroupsCount = $user->groups()->where('groups.status', 'active')->count();
    }

    $catalogActive = request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.*');

    $activeEnrollment = null;
    if (! $scholarshipOnlyPortal) {
        $activeEnrollment = $user->learningPathEnrollments()->where('status', 'active')->with('learningPath')->first();
    }

    $offlineCountSidebar = 0;
    $onlineCountSidebar = 0;
    if (! $scholarshipOnlyPortal) {
        try {
            $offlineCountSidebar = (int) $user->offlineEnrollments()
                ->where('enrollment_channel', 'offline')
                ->where('status', 'active')
                ->count();
            $onlineCountSidebar = (int) $user->offlineEnrollments()
                ->where('enrollment_channel', 'online')
                ->where('status', 'active')
                ->whereHas('course', fn ($q) => $q->where('student_online_portal_enabled', true))
                ->count();
        } catch (\Throwable $e) {
            $offlineCountSidebar = 0;
            $onlineCountSidebar = 0;
        }
    }

    // Show portals only when enrolled (or already inside that portal so nav does not vanish)
    $showOfflineNav = $offlineCountSidebar > 0 || request()->routeIs('student.offline-courses.*');
    $showOnlineNav = $onlineCountSidebar > 0 || request()->routeIs('student.online-courses.*');
?>

<div class="los-sidebar">
    <div class="los-sidebar-brand relative">
        <button type="button"
                @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-2 left-2 los-icon-btn"
                aria-label="إغلاق القائمة">
            <i class="fas fa-times text-xs"></i>
        </button>
        <img src="<?php echo e($platformLogoUrl ?? asset('logo-fallback.svg')); ?>"
             alt="Mindlytics"
             onerror="this.onerror=null;this.src='<?php echo e(asset('logo-fallback.svg')); ?>';">
        <div class="min-w-0 pl-8 lg:pl-0">
            <strong>Mindlytics</strong>
            <span><?php echo e(__('student.learning_center')); ?></span>
        </div>
    </div>

    <div class="los-side-progress" aria-label="تقدّمك العام">
        <div class="row">
            <span><?php echo e(__('student.progress')); ?></span>
            <span style="color:var(--ml-teal-deep)"><?php echo e($totalProgress); ?>٪</span>
        </div>
        <div class="track"><i style="width:<?php echo e(min(100, $totalProgress)); ?>%"></i></div>
        <div class="row" style="margin:8px 0 0;margin-bottom:0">
            <span><?php echo e($activeCoursesCount); ?> <?php echo e(__('student.active_course')); ?></span>
        </div>
    </div>

    <nav class="los-nav sidebar-scroll" aria-label="تنقل التعلّم">
        <?php if($isStudent || $user->hasAnyPermission(
            'student.view.courses',
            'student.view.my-courses',
            'student.view.orders',
            'student.view.invoices',
            'student.view.wallet',
            'student.view.certificates',
            'student.view.achievements',
            'student.view.exams',
            'student.view.calendar',
            'student.view.notifications',
            'student.view.profile',
            'student.view.settings'
        )): ?>

            <a href="<?php echo e(route('dashboard')); ?>"
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="los-nav-link <?php echo e(request()->routeIs('dashboard') ? 'is-active' : ''); ?>">
                <i class="fas fa-house nav-ic" aria-hidden="true"></i>
                <span><?php echo e(__('student.dashboard')); ?></span>
            </a>

            <p class="los-nav-group-label"><?php echo e(__('student.my_courses')); ?></p>

            <?php if (! ($scholarshipOnlyPortal)): ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
                <a href="<?php echo e(route('academic-years')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e($catalogActive ? 'is-active' : ''); ?>">
                    <i class="fas fa-compass nav-ic"></i>
                    <span><?php echo e(__('student.browse_courses')); ?></span>
                </a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
                <a href="<?php echo e(route('my-courses.index')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('my-courses.*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-book-open nav-ic"></i>
                    <span><?php echo e(__('student.my_courses')); ?></span>
                    <?php if($activeCoursesCount > 0): ?><small><?php echo e($activeCoursesCount); ?></small><?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if (! ($scholarshipOnlyPortal)): ?>
                <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
                    <a href="<?php echo e(route('student.groups.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.groups.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-users nav-ic"></i>
                        <span><?php echo e(__('student.my_groups')); ?></span>
                        <?php if($myGroupsCount > 0): ?><small><?php echo e($myGroupsCount); ?></small><?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if($activeEnrollment && $activeEnrollment->learningPath): ?>
                    <a href="<?php echo e(route('student.learning-path.show', \Illuminate\Support\Str::slug($activeEnrollment->learningPath->name))); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.learning-path.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-route nav-ic"></i>
                        <span><?php echo e(__('student.learning_path')); ?></span>
                        <small><?php echo e(\Illuminate\Support\Str::limit($activeEnrollment->learningPath->name, 14)); ?></small>
                    </a>
                <?php endif; ?>

                
                <?php if(($isStudent || $user->hasPermission('student.view.my-courses')) && $showOfflineNav): ?>
                    <a href="<?php echo e(route('student.offline-courses.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.offline-courses.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-chalkboard-teacher nav-ic"></i>
                        <span><?php echo e(__('student.offline_courses')); ?></span>
                        <?php if($offlineCountSidebar > 0): ?><small><?php echo e($offlineCountSidebar); ?></small><?php endif; ?>
                    </a>
                <?php endif; ?>

                
                <?php if(($isStudent || $user->hasPermission('student.view.my-courses')) && $showOnlineNav): ?>
                    <a href="<?php echo e(route('student.online-courses.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.online-courses.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-laptop-house nav-ic"></i>
                        <span><?php echo e(__('student.my_online_courses')); ?></span>
                        <?php if($onlineCountSidebar > 0): ?><small><?php echo e($onlineCountSidebar); ?></small><?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <p class="los-nav-group-label"><?php echo e(__('student.exams')); ?></p>

            <?php if($isStudent || $user->hasPermission('student.view.exams')): ?>
                <a href="<?php echo e(route('student.exams.index')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('student.exams.*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-clipboard-check nav-ic"></i>
                    <span><?php echo e(__('student.exams')); ?></span>
                </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
                <a href="<?php echo e(route('student.assignments.index')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('student.assignments.*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-tasks nav-ic"></i>
                    <span><?php echo e(__('student.assignments')); ?></span>
                </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.calendar')): ?>
                <a href="<?php echo e(route('calendar')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('calendar') ? 'is-active' : ''); ?>">
                    <i class="fas fa-calendar-days nav-ic"></i>
                    <span><?php echo e(__('student.calendar')); ?></span>
                </a>
            <?php endif; ?>

            <p class="los-nav-group-label"><?php echo e(__('student.certificates')); ?></p>

            <?php if($isStudent || $user->hasPermission('student.view.certificates')): ?>
                <a href="<?php echo e(route('student.certificates.index')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('student.certificates.*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-certificate nav-ic"></i>
                    <span><?php echo e(__('student.certificates')); ?></span>
                </a>
            <?php endif; ?>

            <?php if(($isStudent || $user->hasPermission('student.view.achievements')) && Route::has('student.achievements.index')): ?>
                <a href="<?php echo e(route('student.achievements.index')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('student.achievements.*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-trophy nav-ic"></i>
                    <span><?php echo e(__('student.my_achievements')); ?></span>
                </a>
            <?php endif; ?>

            <?php if (! ($scholarshipOnlyPortal)): ?>
                <?php if(($isStudent || $user->hasPermission('student.view.profile')) && Route::has('student.portfolio.index')): ?>
                    <a href="<?php echo e(route('student.portfolio.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.portfolio.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-briefcase nav-ic"></i>
                        <span><?php echo e(__('student.my_projects')); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <p class="los-nav-group-label"><?php echo e(__('student.profile')); ?></p>

            <?php if($isStudent || $user->hasPermission('student.view.notifications')): ?>
                <a href="<?php echo e(route('notifications')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('notifications*') ? 'is-active' : ''); ?>">
                    <i class="fas fa-bell nav-ic"></i>
                    <span><?php echo e(__('student.notifications')); ?></span>
                </a>
            <?php endif; ?>

            <?php if (! ($scholarshipOnlyPortal)): ?>
                <?php if($isStudent || $user->hasPermission('student.view.orders')): ?>
                    <a href="<?php echo e(route('orders.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('orders.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-shopping-cart nav-ic"></i>
                        <span><?php echo e(__('student.orders')); ?></span>
                    </a>
                <?php endif; ?>
                <?php if($isStudent || $user->hasPermission('student.view.wallet')): ?>
                    <a href="<?php echo e(route('student.wallet.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link <?php echo e(request()->routeIs('student.wallet.*') ? 'is-active' : ''); ?>">
                        <i class="fas fa-wallet nav-ic"></i>
                        <span><?php echo e(__('student.wallet')); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.profile')): ?>
                <a href="<?php echo e(route('profile')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('profile') ? 'is-active' : ''); ?>">
                    <i class="fas fa-user nav-ic"></i>
                    <span><?php echo e(__('student.profile')); ?></span>
                </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.settings')): ?>
                <a href="<?php echo e(route('settings')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('settings') ? 'is-active' : ''); ?>">
                    <i class="fas fa-gear nav-ic"></i>
                    <span><?php echo e(__('student.settings')); ?></span>
                </a>
            <?php endif; ?>

        <?php endif; ?>

        <?php if($user->isAdmin()): ?>
            <div class="los-nav-pin">
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'is-active' : ''); ?>">
                    <i class="fas fa-shield-halved nav-ic"></i>
                    <span><?php echo e(__('student.admin_panel')); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </nav>

    <div class="los-side-user">
        <div class="av">
            <?php if($user->profile_image): ?>
                <img src="<?php echo e($user->profile_image_url); ?>" alt="">
            <?php else: ?>
                <?php echo e(mb_substr($user->name, 0, 1)); ?>

            <?php endif; ?>
        </div>
        <div class="meta min-w-0">
            <strong class="truncate"><?php echo e($user->name); ?></strong>
            <span>
                <?php if($user->isAdmin()): ?>
                    <?php echo e(__('student.admin_role')); ?>

                <?php elseif($user->isInstructor()): ?>
                    <?php echo e(__('student.instructor_role')); ?>

                <?php else: ?>
                    <?php echo e(__('student.student_role')); ?>

                <?php endif; ?>
            </span>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\layouts\student-sidebar.blade.php ENDPATH**/ ?>