<div class="flex flex-col h-full">
    <!-- Logo & Brand -->
    <div class="logo-section p-3 md:p-4 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-2.5 left-2.5 w-8 h-8 rounded-lg bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center transition-all z-10">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="flex items-center gap-2 md:gap-2.5 pr-8 lg:pr-0">
            <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center overflow-hidden bg-sky-100 border border-sky-200 flex-shrink-0">
                <img src="<?php echo e($platformLogoUrl ?? asset('logo-fallback.svg')); ?>" alt="Mindlytics Logo" class="w-full h-full object-contain" style="transform: none !important; object-position: center;" onerror="this.onerror=null; this.src='<?php echo e(asset('logo-fallback.svg')); ?>';">
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm md:text-base font-bold text-gray-900 tracking-tight leading-tight">Mindlytics</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5"><?php echo e(__('student.learning_center')); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="p-2.5 md:p-3 border-b border-gray-100 bg-white">
        <div class="grid grid-cols-2 gap-2">
            <div class="stats-card bg-sky-50 rounded-lg p-2 md:p-2.5 border border-sky-100 cursor-pointer transition-all duration-200">
                <div class="flex items-center gap-1 mb-0.5">
                    <i class="fas fa-book text-sky-500 text-xs"></i>
                    <span class="text-xs text-gray-600 font-medium"><?php echo e(__('student.courses')); ?></span>
                </div>
                <div class="text-base md:text-lg font-bold text-sky-600 leading-none"><?php echo e(auth()->user()->activeCourses()->count()); ?></div>
            </div>
            <div class="stats-card bg-amber-50 rounded-lg p-2 md:p-2.5 border border-amber-100 cursor-pointer transition-all duration-200">
                <div class="flex items-center gap-1 mb-0.5">
                    <i class="fas fa-chart-line text-amber-500 text-xs"></i>
                    <span class="text-xs text-gray-600 font-medium"><?php echo e(__('student.progress')); ?></span>
                </div>
                <div class="text-base md:text-lg font-bold text-amber-600 leading-none">
                    <?php
                        $enrollments = auth()->user()->courseEnrollments()->whereIn('status', ['active', 'completed'])->get();
                        $totalProgress = $enrollments->isEmpty() ? 0 : round($enrollments->avg('progress') ?? 0, 0);
                    ?>
                    <?php echo e($totalProgress); ?>%
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-scroll p-2 md:p-2.5 space-y-0.5">
        <?php
            $user = auth()->user();
            $isStudent = $user->role === 'student' || strtolower($user->role) === 'student';
        ?>
        <?php if($isStudent || $user->hasAnyPermission('student.view.courses', 'student.view.my-courses', 'student.view.orders', 'student.view.invoices', 'student.view.wallet', 'student.view.certificates', 'student.view.achievements', 'student.view.exams', 'student.view.calendar', 'student.view.notifications', 'student.view.profile', 'student.view.settings')): ?>
            <!-- Dashboard -->
            <a href="<?php echo e(route('dashboard')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-500 text-white flex-shrink-0">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.dashboard')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.overview')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>

            <!-- Browse Courses -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
            <?php
                $catalogActive = request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.*');
            ?>
            <a href="<?php echo e(route('academic-years')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e($catalogActive ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-400 text-white flex-shrink-0">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.browse_courses')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.discover_new')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- My Courses -->
            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <a href="<?php echo e(route('my-courses.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('my-courses.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-600 text-white flex-shrink-0">
                        <i class="fas fa-book-open text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.my_courses')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(auth()->user()->activeCourses()->count()); ?> <?php echo e(__('student.active_course')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- مجموعاتي -->
            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <?php
                $myGroupsCount = auth()->user()->groups()->where('groups.status', 'active')->count();
            ?>
            <a href="<?php echo e(route('student.groups.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.groups.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-emerald-500 text-white flex-shrink-0">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.my_groups')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e($myGroupsCount); ?> <?php echo e(__('student.group_count')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Learning Path -->
            <?php
                $activeEnrollment = auth()->user()->learningPathEnrollments()->where('status', 'active')->with('learningPath')->first();
            ?>
            <?php if($activeEnrollment && $activeEnrollment->learningPath): ?>
            <a href="<?php echo e(route('student.learning-path.show', \Illuminate\Support\Str::slug($activeEnrollment->learningPath->name))); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.learning-path.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-gradient-to-br from-green-500 to-emerald-600 text-white flex-shrink-0">
                        <i class="fas fa-route text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.learning_path')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(\Illuminate\Support\Str::limit($activeEnrollment->learningPath->name, 20)); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- كورساتي الأوفلاين (منفصلة عن الأونلاين) -->
            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <?php
                $offlineStudentNavOpen = request()->routeIs('student.offline-courses.*');
                $onlineStudentNavOpen = request()->routeIs('student.online-courses.*');
                try {
                    $offlineCountSidebar = auth()->user()->offlineEnrollments()->where('enrollment_channel', 'offline')->where('status', 'active')->count();
                    $onlineCountSidebar = auth()->user()->offlineEnrollments()
                        ->where('enrollment_channel', 'online')
                        ->where('status', 'active')
                        ->whereHas('course', fn ($q) => $q->where('student_online_portal_enabled', true))
                        ->count();
                } catch (\Exception $e) {
                    $offlineCountSidebar = 0;
                    $onlineCountSidebar = 0;
                }
            ?>
            <div class="rounded-xl border border-purple-100/80 bg-gradient-to-br from-purple-50/40 to-white overflow-hidden" x-data="{ open: <?php echo e($offlineStudentNavOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-right transition-colors hover:bg-purple-50/90 <?php echo e($offlineStudentNavOpen ? 'bg-purple-50/70' : ''); ?>">
                    <div class="nav-icon bg-gradient-to-br from-purple-500 to-purple-700 text-white flex-shrink-0">
                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight">الكورسات الأوفلاين</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e($offlineCountSidebar); ?> <?php echo e(__('student.offline_course')); ?> نشط</div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-500 text-xs flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition class="mr-3 mt-0.5 mb-2 space-y-0.5 border-r-2 border-purple-200/80 pr-2">
                    <a href="<?php echo e(route('student.offline-courses.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors <?php echo e(request()->routeIs('student.offline-courses.*') && !request()->routeIs('student.offline-courses.booking.*') ? 'bg-purple-600 text-white font-semibold' : 'text-gray-700 hover:bg-purple-100/80'); ?>">
                        <i class="fas fa-book-reader w-4 text-center opacity-90"></i>
                        <span><?php echo e(__('student.my_offline_courses')); ?></span>
                    </a>
                </div>
            </div>

            <!-- كورساتي الأونلاين (مسار /online-courses؛ تظهر بعد تفعيل البوابة من الإدارة) -->
            <div class="rounded-xl border border-indigo-100/80 bg-gradient-to-br from-indigo-50/40 to-white overflow-hidden" x-data="{ openOnline: <?php echo e($onlineStudentNavOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="openOnline = !openOnline"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-right transition-colors hover:bg-indigo-50/90 <?php echo e($onlineStudentNavOpen ? 'bg-indigo-50/70' : ''); ?>">
                    <div class="nav-icon bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex-shrink-0">
                        <i class="fas fa-laptop-house text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight">كورساتي الأونلاين</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">المجموعات المفعّلة في بوابة الطالب</div>
                    </div>
                    <?php if($onlineCountSidebar > 0): ?>
                        <span class="text-[11px] bg-indigo-500 text-white px-1.5 py-0.5 rounded-full font-semibold"><?php echo e($onlineCountSidebar); ?></span>
                    <?php endif; ?>
                    <i class="fas fa-chevron-down text-gray-500 text-xs flex-shrink-0 transition-transform duration-200" :class="openOnline ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openOnline" x-transition class="mr-3 mt-0.5 mb-2 space-y-0.5 border-r-2 border-indigo-200/80 pr-2">
                    <a href="<?php echo e(route('student.online-courses.index')); ?>"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors <?php echo e(request()->routeIs('student.online-courses.*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-700 hover:bg-indigo-100/80'); ?>">
                        <i class="fas fa-book-open w-4 text-center opacity-90"></i>
                        <span>قائمة كورسات الأونلاين</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- مجتمع الذكاء الاصطناعي -->
            <a href="<?php echo e(route('community.dashboard')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('community.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex-shrink-0">
                        <i class="fas fa-brain text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight">مجتمع الذكاء الاصطناعي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">مسابقات ومجموعات بيانات</div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>

            <!-- Orders -->
            <?php if($isStudent || $user->hasPermission('student.view.orders')): ?>
            <a href="<?php echo e(route('orders.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-amber-500 text-white flex-shrink-0">
                        <i class="fas fa-shopping-cart text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.orders')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.orders_tracking')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Assignments -->
            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <a href="<?php echo e(route('student.assignments.index')); ?>"
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.assignments.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-violet-500 text-white flex-shrink-0">
                        <i class="fas fa-tasks text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight">الواجبات</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">عرض وتسليم واجباتك</div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Exams -->
            <?php if($isStudent || $user->hasPermission('student.view.exams')): ?>
            <a href="<?php echo e(route('student.exams.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.exams.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-500 text-white flex-shrink-0">
                        <i class="fas fa-clipboard-check text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.exams')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.my_exams')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Certificates -->
            <?php if($isStudent || $user->hasPermission('student.view.certificates')): ?>
            <a href="<?php echo e(route('student.certificates.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.certificates.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-amber-500 text-white flex-shrink-0">
                        <i class="fas fa-certificate text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.certificates')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.my_achievements')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Wallet -->
            <?php if($isStudent || $user->hasPermission('student.view.wallet')): ?>
            <a href="<?php echo e(route('student.wallet.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.wallet.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-500 text-white flex-shrink-0">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.wallet')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.wallet_financial')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Calendar -->
            <?php if($isStudent || $user->hasPermission('student.view.calendar')): ?>
            <?php
                // حساب عدد الأحداث القادمة للطالب
                $upcomingEventsCount = 0;
                try {
                    $user = auth()->user();
                    // محاضرات قادمة
                    $upcomingEventsCount += \App\Models\Lecture::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) {
                            $q2->where('user_id', $user->id)->where('status', 'active');
                        });
                    })->where('status', 'scheduled')->where('scheduled_at', '>=', now())->count();
                    
                    // امتحانات قادمة
                    $upcomingEventsCount += \App\Models\Exam::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) {
                            $q2->where('user_id', $user->id)->where('status', 'active');
                        });
                    })->where('is_active', true)->where('is_published', true)
                    ->where(function($q) {
                        $q->where('start_time', '>=', now())
                          ->orWhere('start_date', '>=', now());
                    })->count();
                    
                    // واجبات قادمة
                    $upcomingEventsCount += \App\Models\Assignment::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) {
                            $q2->where('user_id', $user->id)->where('status', 'active');
                        });
                    })->where('status', 'published')->where('due_date', '>=', now())->count();
                    
                    // واجبات محاضرات قادمة
                    $upcomingEventsCount += \App\Models\LectureAssignment::whereHas('lecture.course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) {
                            $q2->where('user_id', $user->id)->where('status', 'active');
                        });
                    })->where('status', 'published')->where('due_date', '>=', now())->count();
                } catch (\Exception $e) {
                    // في حالة حدوث خطأ، نترك العدد 0
                }
            ?>
            <a href="<?php echo e(route('calendar')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('calendar') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-500 text-white relative flex-shrink-0">
                        <i class="fas fa-calendar-alt text-sm"></i>
                        <?php if($upcomingEventsCount > 0): ?>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-sky-500 rounded-full border-2 border-white flex items-center justify-center text-[8px] font-bold text-white"><?php echo e($upcomingEventsCount > 9 ? '9+' : $upcomingEventsCount); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.calendar')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">
                            <?php if($upcomingEventsCount > 0): ?>
                                <?php echo e($upcomingEventsCount); ?> <?php echo e(__('student.upcoming_event')); ?>

                            <?php else: ?>
                                <?php echo e(__('student.events_dates')); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Notifications -->
            <?php if($isStudent || $user->hasPermission('student.view.notifications')): ?>
            <a href="<?php echo e(route('notifications')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('notifications') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-amber-400 text-white relative flex-shrink-0">
                        <i class="fas fa-bell text-sm"></i>
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-amber-500 rounded-full border-2 border-white"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.notifications')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">3 <?php echo e(__('student.new_notifications')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- البورتفوليو - مشاريعي -->
            <?php if($isStudent || $user->hasPermission('student.view.profile')): ?>
            <a href="<?php echo e(route('student.portfolio.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.portfolio.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-emerald-500 text-white flex-shrink-0">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.my_projects')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.portfolio')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Profile -->
            <?php if($isStudent || $user->hasPermission('student.view.profile')): ?>
            <a href="<?php echo e(route('profile')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('profile') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-sky-600 text-white flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.profile')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.my_info')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Settings -->
            <?php if($isStudent || $user->hasPermission('student.view.settings')): ?>
            <a href="<?php echo e(route('settings')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('settings') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-gray-500 text-white flex-shrink-0">
                        <i class="fas fa-cog text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.settings')); ?></div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.options')); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(auth()->user()->isAdmin() || auth()->user()->isInstructor()): ?>
            <div class="my-3 border-t-2 border-gray-200"></div>
            
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-gradient-to-br from-red-500 to-red-600 text-white flex-shrink-0">
                            <i class="fas fa-shield-alt text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm leading-tight"><?php echo e(__('student.admin_panel')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(__('student.admin_role')); ?></div>
                        </div>
                        <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- User at Bottom -->
    <div class="user-profile-card p-2 md:p-2.5">
        <div class="user-profile-inner bg-white rounded-xl p-2.5 border border-gray-200 transition-all duration-200">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-sky-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    <?php if(auth()->user()->profile_image): ?>
                        <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="" class="w-full h-full rounded-lg object-cover">
                    <?php else: ?>
                        <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-[10px] text-gray-500 truncate leading-tight mt-0.5 flex items-center gap-1">
                        <?php if(auth()->user()->isAdmin()): ?>
                            <i class="fas fa-shield-alt text-sky-500 text-[10px]"></i> <?php echo e(__('student.admin_role')); ?>

                        <?php elseif(auth()->user()->isInstructor()): ?>
                            <i class="fas fa-chalkboard-teacher text-sky-500 text-[10px]"></i> <?php echo e(__('student.instructor_role')); ?>

                        <?php else: ?>
                            <i class="fas fa-user-graduate text-sky-500 text-[10px]"></i> <?php echo e(__('student.student_role')); ?>

                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/layouts/student-sidebar.blade.php ENDPATH**/ ?>