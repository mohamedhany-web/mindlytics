<div class="flex flex-col h-full bg-white">
    <!-- Logo & Brand -->
    <div class="logo-section p-4 md:p-5 border-b border-slate-200 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center z-10 transition-colors">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="flex items-center gap-2 md:gap-3 pr-8 lg:pr-0">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl flex items-center justify-center overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                <img src="<?php echo e($platformLogoUrl ?? asset('logo-fallback.svg')); ?>" alt="Mindlytics Logo" class="w-full h-full object-contain" style="transform: none !important; object-position: center !important;" onerror="this.onerror=null; this.src='<?php echo e(asset('logo-fallback.svg')); ?>';">
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-base md:text-lg font-bold text-slate-800 tracking-tight">Mindlytics</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('instructor.instructor_panel')); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php
        $user = auth()->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
        $myOfflineCoursesCount = \App\Models\OfflineCourse::where('instructor_id', $user->id)->count();
        $myOnlineStudentsCount = \App\Models\OfflineCourseEnrollment::whereHas('course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->where('enrollment_channel', 'online')->where('status', 'active')->count();
    ?>
    <div class="p-3 md:p-4 border-b border-slate-200 bg-slate-50/50">
        <div class="grid grid-cols-2 gap-2">
            <div class="rounded-xl p-3 border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-1.5 mb-1">
                    <i class="fas fa-book text-sky-500 text-xs"></i>
                    <span class="text-xs font-semibold text-slate-600"><?php echo e(__('instructor.courses')); ?></span>
                </div>
                <div class="text-lg font-bold text-slate-800"><?php echo e($myCoursesCount); ?></div>
            </div>
            <div class="rounded-xl p-3 border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-1.5 mb-1">
                    <i class="fas fa-user-graduate text-emerald-500 text-xs"></i>
                    <span class="text-xs font-semibold text-slate-600"><?php echo e(__('instructor.students')); ?></span>
                </div>
                <div class="text-lg font-bold text-slate-800"><?php echo e($totalStudents); ?></div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-scroll p-2 md:p-3 space-y-1">
        <?php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        ?>
        <?php if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.groups', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks')): ?>
            <!-- Dashboard -->
            <a href="<?php echo e(route('dashboard')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('dashboard') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-sky-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.dashboard')); ?></div>
                    <div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.overview')); ?></div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <!-- My Courses -->
            <?php if($isInstructor || $user->hasPermission('instructor.view.courses')): ?>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.courses.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-sky-600 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.my_courses')); ?></div>
                    <div class="text-xs text-slate-500 mt-0.5"><?php echo e($myCoursesCount); ?> <?php echo e(__('instructor.course')); ?></div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.view.courses')): ?>
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.offline-courses.*') && request('channel', 'offline') === 'offline' ? 'bg-amber-50 border border-amber-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.my_offline_courses')); ?></div>
                    <div class="text-xs text-slate-500 mt-0.5"><?php echo e($myOfflineCoursesCount ?? 0); ?> <?php echo e(__('instructor.offline_course')); ?></div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <a href="<?php echo e(route('instructor.online-group-courses.index')); ?>"
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.online-group-courses.*') ? 'bg-indigo-50 border border-indigo-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-laptop-house text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm">كورسات الأونلاين</div>
                    <div class="text-xs text-slate-500 mt-0.5"><?php echo e($myOnlineStudentsCount); ?> طالب نشط</div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php $teachingPaths = auth()->user()->teachingLearningPaths()->where('is_active', true)->get(); ?>
            <?php if($teachingPaths->count() > 0): ?>
            <a href="<?php echo e(route('instructor.learning-path.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.learning-path.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-route text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.learning_path')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e($teachingPaths->count()); ?> <?php echo e(__('instructor.path')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.lectures')): ?>
            <a href="<?php echo e(route('instructor.lectures.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.lectures.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-violet-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-chalkboard-teacher text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.lectures')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_lectures')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.assignments')): ?>
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.assignments.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-tasks text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.assignments')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_assignments')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.exams')): ?>
            <a href="<?php echo e(route('instructor.exams.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.exams.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-clipboard-check text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.exams')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_exams')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor): ?>
            <a href="<?php echo e(route('instructor.question-banks.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-teal-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-database text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.question_banks')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_questions')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.groups')): ?>
            <a href="<?php echo e(route('instructor.groups.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.groups.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-users text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.groups')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.manage_groups')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.attendance')): ?>
            <a href="<?php echo e(route('instructor.attendance.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.attendance.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-cyan-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-clipboard-list text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.attendance')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.register_attendance')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor): ?>
            <a href="<?php echo e(route('instructor.calendar')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.calendar*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar-alt text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">التقويم</div><div class="text-xs text-slate-500 mt-0.5">جدول الجلسات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.view.tasks')): ?>
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.tasks.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-check-square text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.tasks_from_management')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.tasks_assigned_by_management')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <?php if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index')): ?>
            <a href="<?php echo e(route('instructor.management-requests.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.management-requests.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-paper-plane text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.submit_requests_to_management')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.my_requests_to_management')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('instructor.agreements.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.agreements.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-teal-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-handshake text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.agreements_system')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.my_contract_with_platform')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <a href="<?php echo e(route('instructor.transfer-account.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.transfer-account.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-university text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.transfer_account')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.transfer_account_data')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <a href="<?php echo e(route('instructor.withdrawals.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.withdrawals.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-money-bill-wave text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.withdrawal_requests')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.withdraw_finances')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <a href="<?php echo e(route('instructor.personal-branding.edit')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.personal-branding.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-user-tie text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.personal_branding')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.profile_for_publishing')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <?php if($user->is_community_contributor ?? false): ?>
            <a href="<?php echo e(route('community.dashboard')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('community.*') ? 'bg-cyan-50 border border-cyan-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-cyan-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-brain text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">مجتمع الذكاء الاصطناعي</div><div class="text-xs text-slate-500 mt-0.5">الدخول للمجتمع</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(auth()->user()->isAdmin() || auth()->user()->isInstructor()): ?>
            <hr class="my-3 border-slate-200">
            <?php if(auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('admin.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
                <div class="w-9 h-9 rounded-lg bg-slate-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-cog text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.admin_panel')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.administration')); ?></div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <a href="<?php echo e(route('instructor.portfolio.index')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.portfolio.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
            <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-briefcase text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.portfolio')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.review_projects')); ?></div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>

        <a href="<?php echo e(route('instructor.profile')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('instructor.profile*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
            <div class="w-9 h-9 rounded-lg bg-slate-600 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-user text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.profile')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.my_info')); ?></div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>

        <?php if(auth()->check() && auth()->user()->hasPermission('student.view.settings')): ?>
        <a href="<?php echo e(route('settings')); ?>" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors <?php echo e(request()->routeIs('settings') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent'); ?>">
            <div class="w-9 h-9 rounded-lg bg-slate-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-cog text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.settings')); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo e(__('instructor.options')); ?></div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>
        <?php endif; ?>
    </nav>

    <!-- User profile at bottom -->
    <div class="p-3 md:p-4 border-t border-slate-200 bg-slate-50/50">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white font-bold text-sm overflow-hidden flex-shrink-0 relative">
                <?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="Profile" class="w-full h-full object-cover absolute inset-0" onerror="this.classList.add('!hidden'); this.nextElementSibling?.classList.remove('hidden');">
                    <span class="hidden absolute inset-0 flex items-center justify-center bg-sky-500 text-white font-bold text-sm"><?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?></span>
                <?php else: ?>
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-800 text-sm truncate"><?php echo e(auth()->user()->name); ?></div>
                <div class="text-xs text-slate-500 truncate"><?php echo e(__('instructor.instructor_role')); ?></div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors" title="<?php echo e(__('instructor.logout')); ?>">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/layouts/instructor-sidebar.blade.php ENDPATH**/ ?>