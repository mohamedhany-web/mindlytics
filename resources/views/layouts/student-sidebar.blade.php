@php
    use App\Support\StudentFigmaAssets;
    $user = auth()->user();
    $isStudent = $user->role === 'student' || strtolower((string) $user->role) === 'student';
    $scholarshipOnlyPortal = $isStudent && $user->usesScholarshipOnlyPortal();
    $activeCoursesCount = $scholarshipOnlyPortal
        ? $user->courseEnrollments()->scholarshipOnly()->where('status', 'active')->count()
        : $user->activeCourses()->count();
    $spIcons = StudentFigmaAssets::urls();
    $logoUrl = $platformLogoUrl ?? \App\Support\SiteBranding::logoUrl();

    $unreadNotifications = 0;
    try {
        $unreadNotifications = (int) ($user->unreadNotificationsCount ?? 0);
    } catch (\Throwable $e) {}
@endphp

<div class="sp-sidebar flex flex-col h-full">
    <div class="sp-sidebar-brand relative">
        <button type="button" @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 start-3 size-8 rounded-full bg-white/10 text-white flex items-center justify-center text-xl leading-none"
                aria-label="{{ __('common.close') }}">
            &times;
        </button>
        <div class="flex items-center justify-center gap-2">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="" onerror="this.style.display='none'">
            @endif
            <span>Mindlytics</span>
        </div>
    </div>

    <nav class="sp-nav" aria-label="{{ __('student.dashboard') }}">
        @if($isStudent || $user->hasAnyPermission('student.view.courses', 'student.view.my-courses', 'student.view.orders', 'student.view.invoices', 'student.view.wallet', 'student.view.certificates', 'student.view.achievements', 'student.view.exams', 'student.view.calendar', 'student.view.notifications', 'student.view.profile', 'student.view.settings'))

            <a href="{{ route('dashboard') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-dashboard.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.dashboard') }}</span>
            </a>

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
            <a href="{{ route('my-courses.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('my-courses.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-courses.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.my_courses') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
            @php
                $offlineStudentNavOpen = request()->routeIs('student.offline-courses.*');
                $onlineStudentNavOpen = request()->routeIs('student.online-courses.*');
                $offlineEnrollmentsNav = collect();
                $onlineEnrollmentsNav = collect();
                $offlineCountSidebar = 0;
                $onlineCountSidebar = 0;

                try {
                    $offlineEnrollmentsNav = $user->offlineEnrollments()
                        ->where('enrollment_channel', 'offline')
                        ->where('status', 'active')
                        ->with(['course', 'group'])
                        ->latest('enrolled_at')
                        ->get()
                        ->filter(fn ($enrollment) => $enrollment->course !== null)
                        ->values();

                    $onlineEnrollmentsNav = $user->offlineEnrollments()
                        ->where('enrollment_channel', 'online')
                        ->where('status', 'active')
                        ->whereHas('course', fn ($q) => $q->where('student_online_portal_enabled', true))
                        ->with(['course', 'group'])
                        ->latest('enrolled_at')
                        ->get()
                        ->filter(fn ($enrollment) => $enrollment->course !== null)
                        ->values();

                    $offlineCountSidebar = $offlineEnrollmentsNav->count();
                    $onlineCountSidebar = $onlineEnrollmentsNav->count();
                } catch (\Throwable $e) {
                    $offlineEnrollmentsNav = collect();
                    $onlineEnrollmentsNav = collect();
                    \Log::warning('student.sidebar.offline_enrollments_failed', [
                        'user_id' => $user->id,
                        'message' => $e->getMessage(),
                    ]);
                }

                $showOfflineNav = ! $scholarshipOnlyPortal || $offlineCountSidebar > 0;
                $showOnlineNav = ! $scholarshipOnlyPortal || $onlineCountSidebar > 0;
                $offlineNavOpen = $offlineStudentNavOpen;
                $onlineNavOpen = $onlineStudentNavOpen;

                $activeOfflineCourseId = null;
                $activeOnlineCourseId = null;
                if ($offlineStudentNavOpen) {
                    $routeCourse = request()->route('offlineCourse');
                    $activeOfflineCourseId = is_object($routeCourse) ? ($routeCourse->id ?? null) : $routeCourse;
                }
                if ($onlineStudentNavOpen) {
                    $routeCourse = request()->route('offlineCourse');
                    $activeOnlineCourseId = is_object($routeCourse) ? ($routeCourse->id ?? null) : $routeCourse;
                }
            @endphp

            @if($showOfflineNav)
                <x-student.sidebar-channel-courses
                    :enrollments="$offlineEnrollmentsNav"
                    :count="$offlineCountSidebar"
                    icon="icon-classes.svg"
                    :label="__('student.my_offline_courses')"
                    :index-route="route('student.offline-courses.index')"
                    show-route="student.offline-courses.show"
                    :is-section-active="$offlineStudentNavOpen"
                    :active-course-id="$activeOfflineCourseId"
                    :default-open="$offlineNavOpen"
                />
            @endif

            @if($showOnlineNav)
                <x-student.sidebar-channel-courses
                    :enrollments="$onlineEnrollmentsNav"
                    :count="$onlineCountSidebar"
                    icon="icon-community.svg"
                    :label="__('student.my_online_courses')"
                    :index-route="route('student.online-courses.index')"
                    show-route="student.online-courses.show"
                    :is-section-active="$onlineStudentNavOpen"
                    :active-course-id="$activeOnlineCourseId"
                    :default-open="$onlineNavOpen"
                />
            @endif
            @endif

            @unless($scholarshipOnlyPortal)
            @if($isStudent || $user->hasPermission('student.view.courses'))
            @php $catalogActive = request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.*'); @endphp
            <a href="{{ route('academic-years') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ $catalogActive ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-search.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.browse_courses') }}</span>
            </a>
            @endif
            @endunless

            @unless($scholarshipOnlyPortal)
            @if($isStudent || $user->hasPermission('student.view.my-courses'))
            <a href="{{ route('student.groups.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.groups.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-community.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.my_groups') }}</span>
            </a>
            @endif
            @endunless

            @unless($scholarshipOnlyPortal)
            @php $hasLearningPathEnrollment = auth()->user()->learningPathEnrollments()->where('status', 'active')->exists(); @endphp
            @if($hasLearningPathEnrollment)
            <a href="{{ route('student.learning-path.index') }}"
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.learning-path.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-path.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.learning_path') }}</span>
            </a>
            @endif
            @endunless

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
            <a href="{{ route('student.assignments.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.assignments.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-messages.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.assignments') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.notifications'))
            <a href="{{ route('notifications') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('notifications') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-notifications.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.notifications') }}</span>
                @if($unreadNotifications > 0)
                    <span class="sp-nav-badge">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
                @endif
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.calendar'))
            <a href="{{ route('calendar') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('calendar') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-calendar.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.calendar') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.exams'))
            <a href="{{ route('student.exams.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.exams.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-exams.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.exams') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.certificates'))
            <a href="{{ route('student.certificates.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.certificates.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-certificates.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.certificates') }}</span>
            </a>
            @endif

            @unless($scholarshipOnlyPortal)
            @if($isStudent || $user->hasPermission('student.view.orders'))
            <a href="{{ route('orders.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-orders.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.orders') }}</span>
            </a>
            @endif
            @endunless

            @unless($scholarshipOnlyPortal)
            @if($isStudent || $user->hasPermission('student.view.wallet'))
            <a href="{{ route('student.wallet.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.wallet.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-wallet.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.wallet') }}</span>
            </a>
            @endif
            @endunless

            @unless($scholarshipOnlyPortal)
            @if($isStudent || $user->hasPermission('student.view.profile'))
            <a href="{{ route('student.portfolio.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('student.portfolio.*') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-community.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.my_journey') }}</span>
            </a>
            @endif
            @endunless

            @if($isStudent || $user->hasPermission('student.view.profile'))
            <a href="{{ route('profile') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('profile') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-profile.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.profile') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.settings'))
            <a href="{{ route('settings') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('settings') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-settings.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.settings') }}</span>
            </a>
            @endif
        @endif

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sp-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <span class="sp-nav-ico"><x-student.figma-icon name="icon-admin.svg" /></span>
                <span class="flex-1 min-w-0">{{ __('student.admin_panel') }}</span>
            </a>
        @endif
    </nav>

    <a href="{{ route('mobile-app') }}" class="sp-app-card" @click="if (window.innerWidth < 1024) sidebarOpen = false">
        <img src="{{ $spIcons['app_blob'] }}" alt="" class="sp-app-blob" aria-hidden="true">
        <div class="sp-app-arrow">
            <img src="{{ $spIcons['app_arrow'] }}" alt="">
        </div>
        <p>{!! nl2br(e(__('student.download_app_lines'))) !!}</p>
    </a>
</div>
