{{-- Mindlytics student sidebar — all guards preserved; offline/online only when enrolled --}}
@php
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
@endphp

<div class="los-sidebar">
    <div class="los-sidebar-brand relative">
        <button type="button"
                @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-2 end-2 los-icon-btn"
                aria-label="{{ __('common.close_menu') }}">
            <i class="fas fa-times text-xs"></i>
        </button>
        <img src="{{ $platformLogoUrl ?? asset('logo-fallback.svg') }}"
             alt="Mindlytics"
             onerror="this.onerror=null;this.src='{{ asset('logo-fallback.svg') }}';">
        <div class="min-w-0 ps-8 lg:ps-0">
            <strong>Mindlytics</strong>
            <span>{{ __('student.learning_center') }}</span>
        </div>
    </div>

    <div class="los-side-progress" aria-label="{{ __('common.overall_progress') }}">
        <div class="row">
            <span>{{ __('student.progress') }}</span>
            <span style="color:var(--ml-teal-deep)">{{ $totalProgress }}{{ app()->getLocale() === 'ar' ? '٪' : '%' }}</span>
        </div>
        <div class="track"><i style="width:{{ min(100, $totalProgress) }}%"></i></div>
        <div class="row" style="margin:8px 0 0;margin-bottom:0">
            <span>{{ $activeCoursesCount }} {{ __('student.active_course') }}</span>
        </div>
    </div>

    <nav class="los-nav sidebar-scroll" aria-label="{{ __('common.learning_nav') }}">
        @if($isStudent || $user->hasAnyPermission(
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
        ))

            <a href="{{ route('dashboard') }}"
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="los-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <i class="fas fa-house nav-ic" aria-hidden="true"></i>
                <span>{{ __('student.dashboard') }}</span>
            </a>

            <p class="los-nav-group-label">{{ __('student.my_courses') }}</p>

            @unless($scholarshipOnlyPortal)
                @hasPermission('student.view.courses')
                <a href="{{ route('academic-years') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ $catalogActive ? 'is-active' : '' }}">
                    <i class="fas fa-compass nav-ic"></i>
                    <span>{{ __('student.browse_courses') }}</span>
                </a>
                @endif
            @endunless

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
                <a href="{{ route('my-courses.index') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('my-courses.*') ? 'is-active' : '' }}">
                    <i class="fas fa-book-open nav-ic"></i>
                    <span>{{ __('student.my_courses') }}</span>
                    @if($activeCoursesCount > 0)<small>{{ $activeCoursesCount }}</small>@endif
                </a>
            @endif

            @unless($scholarshipOnlyPortal)
                @if($isStudent || $user->hasPermission('student.view.my-courses'))
                    <a href="{{ route('student.groups.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.groups.*') ? 'is-active' : '' }}">
                        <i class="fas fa-users nav-ic"></i>
                        <span>{{ __('student.my_groups') }}</span>
                        @if($myGroupsCount > 0)<small>{{ $myGroupsCount }}</small>@endif
                    </a>
                @endif

                @if($activeEnrollment && $activeEnrollment->learningPath)
                    <a href="{{ route('student.learning-path.show', \Illuminate\Support\Str::slug($activeEnrollment->learningPath->name)) }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.learning-path.*') ? 'is-active' : '' }}">
                        <i class="fas fa-route nav-ic"></i>
                        <span>{{ __('student.learning_path') }}</span>
                        <small>{{ \Illuminate\Support\Str::limit($activeEnrollment->learningPath->name, 14) }}</small>
                    </a>
                @endif

                {{-- Offline: enrolled only --}}
                @if(($isStudent || $user->hasPermission('student.view.my-courses')) && $showOfflineNav)
                    <a href="{{ route('student.offline-courses.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.offline-courses.*') ? 'is-active' : '' }}">
                        <i class="fas fa-chalkboard-teacher nav-ic"></i>
                        <span>{{ __('student.offline_courses') }}</span>
                        @if($offlineCountSidebar > 0)<small>{{ $offlineCountSidebar }}</small>@endif
                    </a>
                @endif

                {{-- Online: enrolled (+ portal-enabled) only --}}
                @if(($isStudent || $user->hasPermission('student.view.my-courses')) && $showOnlineNav)
                    <a href="{{ route('student.online-courses.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.online-courses.*') ? 'is-active' : '' }}">
                        <i class="fas fa-laptop-house nav-ic"></i>
                        <span>{{ __('student.my_online_courses') }}</span>
                        @if($onlineCountSidebar > 0)<small>{{ $onlineCountSidebar }}</small>@endif
                    </a>
                @endif
            @endunless

            <p class="los-nav-group-label">{{ __('student.exams') }}</p>

            @if($isStudent || $user->hasPermission('student.view.exams'))
                <a href="{{ route('student.exams.index') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('student.exams.*') ? 'is-active' : '' }}">
                    <i class="fas fa-clipboard-check nav-ic"></i>
                    <span>{{ __('student.exams') }}</span>
                </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
                <a href="{{ route('student.assignments.index') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('student.assignments.*') ? 'is-active' : '' }}">
                    <i class="fas fa-tasks nav-ic"></i>
                    <span>{{ __('student.assignments') }}</span>
                </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.calendar'))
                <a href="{{ route('calendar') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('calendar') ? 'is-active' : '' }}">
                    <i class="fas fa-calendar-days nav-ic"></i>
                    <span>{{ __('student.calendar') }}</span>
                </a>
            @endif

            <p class="los-nav-group-label">{{ __('student.certificates') }}</p>

            @if($isStudent || $user->hasPermission('student.view.certificates'))
                <a href="{{ route('student.certificates.index') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('student.certificates.*') ? 'is-active' : '' }}">
                    <i class="fas fa-certificate nav-ic"></i>
                    <span>{{ __('student.certificates') }}</span>
                </a>
            @endif

            @if(($isStudent || $user->hasPermission('student.view.achievements')) && Route::has('student.achievements.index'))
                <a href="{{ route('student.achievements.index') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('student.achievements.*') ? 'is-active' : '' }}">
                    <i class="fas fa-trophy nav-ic"></i>
                    <span>{{ __('student.my_achievements') }}</span>
                </a>
            @endif

            @unless($scholarshipOnlyPortal)
                @if(($isStudent || $user->hasPermission('student.view.profile')) && Route::has('student.portfolio.index'))
                    <a href="{{ route('student.portfolio.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.portfolio.*') ? 'is-active' : '' }}">
                        <i class="fas fa-briefcase nav-ic"></i>
                        <span>{{ __('student.my_projects') }}</span>
                    </a>
                @endif
            @endunless

            <p class="los-nav-group-label">{{ __('student.profile') }}</p>

            @if($isStudent || $user->hasPermission('student.view.notifications'))
                <a href="{{ route('notifications') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('notifications*') ? 'is-active' : '' }}">
                    <i class="fas fa-bell nav-ic"></i>
                    <span>{{ __('student.notifications') }}</span>
                </a>
            @endif

            @unless($scholarshipOnlyPortal)
                @if($isStudent || $user->hasPermission('student.view.orders'))
                    <a href="{{ route('orders.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('orders.*') ? 'is-active' : '' }}">
                        <i class="fas fa-shopping-cart nav-ic"></i>
                        <span>{{ __('student.orders') }}</span>
                    </a>
                @endif
                @if($isStudent || $user->hasPermission('student.view.wallet'))
                    <a href="{{ route('student.wallet.index') }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="los-nav-link {{ request()->routeIs('student.wallet.*') ? 'is-active' : '' }}">
                        <i class="fas fa-wallet nav-ic"></i>
                        <span>{{ __('student.wallet') }}</span>
                    </a>
                @endif
            @endunless

            @if($isStudent || $user->hasPermission('student.view.profile'))
                <a href="{{ route('profile') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('profile') ? 'is-active' : '' }}">
                    <i class="fas fa-user nav-ic"></i>
                    <span>{{ __('student.profile') }}</span>
                </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.settings'))
                <a href="{{ route('settings') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('settings') ? 'is-active' : '' }}">
                    <i class="fas fa-gear nav-ic"></i>
                    <span>{{ __('student.settings') }}</span>
                </a>
            @endif

        @endif

        @if($user->isAdmin())
            <div class="los-nav-pin">
                <a href="{{ route('admin.dashboard') }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="los-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                    <i class="fas fa-shield-halved nav-ic"></i>
                    <span>{{ __('student.admin_panel') }}</span>
                </a>
            </div>
        @endif
    </nav>

    <div class="los-side-user">
        <div class="av">
            @if($user->profile_image)
                <img src="{{ $user->profile_image_url }}" alt="">
            @else
                {{ mb_substr($user->name, 0, 1) }}
            @endif
        </div>
        <div class="meta min-w-0">
            <strong class="truncate">{{ $user->name }}</strong>
            <span>
                @if($user->isAdmin())
                    {{ __('student.admin_role') }}
                @elseif($user->isInstructor())
                    {{ __('student.instructor_role') }}
                @else
                    {{ __('student.student_role') }}
                @endif
            </span>
        </div>
    </div>
</div>
