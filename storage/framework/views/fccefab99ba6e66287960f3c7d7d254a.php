<div class="flex flex-col h-full">
    <!-- Logo & Brand -->
    <div class="logo-section p-3 md:p-4 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-2.5 left-2.5 w-8 h-8 rounded-lg bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center transition-all z-10">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="flex items-center gap-2 md:gap-2.5 pr-8 lg:pr-0">
            <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center overflow-hidden bg-sky-100 border border-sky-200 flex-shrink-0">
                <img src="<?php echo e($platformLogoUrl ?? asset('logo-removebg-preview.png')); ?>" alt="Mindlytics Logo" class="w-full h-full object-contain" style="transform: none !important; object-position: center;" onerror="this.onerror=null; this.src='<?php echo e(asset('logo-removebg-preview.png')); ?>';">
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm md:text-base font-bold text-gray-900 tracking-tight leading-tight">Mindlytics</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">مركز التعلم</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="p-2.5 md:p-3 border-b border-gray-100 bg-white">
        <div class="grid grid-cols-2 gap-2">
            <div class="stats-card bg-sky-50 rounded-lg p-2 md:p-2.5 border border-sky-100 cursor-pointer transition-all duration-200">
                <div class="flex items-center gap-1 mb-0.5">
                    <i class="fas fa-book text-sky-500 text-xs"></i>
                    <span class="text-xs text-gray-600 font-medium">الكورسات</span>
                </div>
                <div class="text-base md:text-lg font-bold text-sky-600 leading-none"><?php echo e(auth()->user()->activeCourses()->count()); ?></div>
            </div>
            <div class="stats-card bg-amber-50 rounded-lg p-2 md:p-2.5 border border-amber-100 cursor-pointer transition-all duration-200">
                <div class="flex items-center gap-1 mb-0.5">
                    <i class="fas fa-chart-line text-amber-500 text-xs"></i>
                    <span class="text-xs text-gray-600 font-medium">التقدم</span>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">لوحة التحكم</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">نظرة عامة</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">تصفح الكورسات</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">اكتشف جديد</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">كورساتي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(auth()->user()->activeCourses()->count()); ?> كورس نشط</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">مجموعاتي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e($myGroupsCount); ?> مجموعة</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">المسار التعليمي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e(\Illuminate\Support\Str::limit($activeEnrollment->learningPath->name, 20)); ?></div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

            <!-- Offline Courses -->
            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <a href="<?php echo e(route('student.offline-courses.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="nav-card block <?php echo e(request()->routeIs('student.offline-courses.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="nav-icon bg-gradient-to-br from-purple-500 to-purple-700 text-white flex-shrink-0">
                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-gray-900 text-sm leading-tight">كورساتي الأوفلاين</div>
                        <?php
                            try {
                                $offlineCount = auth()->user()->offlineEnrollments()->where('status', 'active')->count();
                            } catch (\Exception $e) {
                                $offlineCount = 0;
                            }
                        ?>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight"><?php echo e($offlineCount); ?> كورس أوفلاين</div>
                    </div>
                    <i class="fas fa-chevron-left text-gray-400 text-xs flex-shrink-0"></i>
                </div>
            </a>
            <?php endif; ?>

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
                        <div class="font-black text-gray-900 text-sm leading-tight">طلباتي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">متابعة الطلبات</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">الامتحانات</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">اختباراتي</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">شهاداتي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">إنجازاتي</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">محفظتي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">المالية</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">التقويم</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">
                            <?php if($upcomingEventsCount > 0): ?>
                                <?php echo e($upcomingEventsCount); ?> حدث قادم
                            <?php else: ?>
                                الأحداث والمواعيد
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
                        <div class="font-black text-gray-900 text-sm leading-tight">الإشعارات</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">3 جديد</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">مشاريعي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">البورتفوليو</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">الملف الشخصي</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">معلوماتي</div>
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
                        <div class="font-black text-gray-900 text-sm leading-tight">الإعدادات</div>
                        <div class="text-xs text-gray-500 mt-0.5 leading-tight">الخيارات</div>
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
                            <div class="font-black text-gray-900 text-sm leading-tight">لوحة الإدارة</div>
                            <div class="text-xs text-gray-500 mt-0.5 leading-tight">مدير</div>
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
                            <i class="fas fa-shield-alt text-sky-500 text-[10px]"></i> مدير
                        <?php elseif(auth()->user()->isInstructor()): ?>
                            <i class="fas fa-chalkboard-teacher text-sky-500 text-[10px]"></i> مدرب
                        <?php else: ?>
                            <i class="fas fa-user-graduate text-sky-500 text-[10px]"></i> طالب
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/layouts/student-sidebar.blade.php ENDPATH**/ ?>