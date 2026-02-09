<div class="flex flex-col h-full bg-white">
    <!-- Logo & Brand -->
    <div class="logo-section p-4 md:p-5 border-b border-slate-200 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center z-10 transition-colors">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="flex items-center gap-2 md:gap-3 pr-8 lg:pr-0">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl flex items-center justify-center overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                <img src="{{ asset('logo-removebg-preview.png') }}" alt="Mindlytics Logo" class="w-full h-full object-contain" style="transform: none !important; object-position: center !important;">
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-base md:text-lg font-bold text-slate-800 tracking-tight">Mindlytics</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">لوحة المدرب</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @php
        $user = auth()->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
        $myOfflineCoursesCount = \App\Models\OfflineCourse::where('instructor_id', $user->id)->count();
    @endphp
    <div class="p-3 md:p-4 border-b border-slate-200 bg-slate-50/50">
        <div class="grid grid-cols-2 gap-2">
            <div class="rounded-xl p-3 border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-1.5 mb-1">
                    <i class="fas fa-book text-sky-500 text-xs"></i>
                    <span class="text-xs font-semibold text-slate-600">الكورسات</span>
                </div>
                <div class="text-lg font-bold text-slate-800">{{ $myCoursesCount }}</div>
            </div>
            <div class="rounded-xl p-3 border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-1.5 mb-1">
                    <i class="fas fa-user-graduate text-emerald-500 text-xs"></i>
                    <span class="text-xs font-semibold text-slate-600">الطلاب</span>
                </div>
                <div class="text-lg font-bold text-slate-800">{{ $totalStudents }}</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-scroll p-2 md:p-3 space-y-1">
        @php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        @endphp
        @if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.groups', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks'))
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('dashboard') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-sky-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm">لوحة التحكم</div>
                    <div class="text-xs text-slate-500 mt-0.5">نظرة عامة</div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <!-- My Courses -->
            @if($isInstructor || $user->hasPermission('instructor.view.courses'))
            <a href="{{ route('instructor.courses.index') }}" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.courses.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-sky-600 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm">كورساتي</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $myCoursesCount }} كورس</div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            <!-- كورساتي الأوفلاين -->
            @if($isInstructor || $user->hasPermission('instructor.view.courses'))
            <a href="{{ route('instructor.offline-courses.index') }}" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.offline-courses.*') ? 'bg-amber-50 border border-amber-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm">كورساتي الأوفلاين</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $myOfflineCoursesCount ?? 0 }} كورس أوفلاين</div>
                </div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @php $teachingPaths = auth()->user()->teachingLearningPaths()->where('is_active', true)->get(); @endphp
            @if($teachingPaths->count() > 0)
            <a href="{{ route('instructor.learning-path.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.learning-path.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-route text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">المسار التعليمي</div><div class="text-xs text-slate-500 mt-0.5">{{ $teachingPaths->count() }} مسار</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.lectures'))
            <a href="{{ route('instructor.lectures.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.lectures.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-violet-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-chalkboard-teacher text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">المحاضرات</div><div class="text-xs text-slate-500 mt-0.5">إدارة المحاضرات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.assignments'))
            <a href="{{ route('instructor.assignments.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.assignments.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-tasks text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">الواجبات</div><div class="text-xs text-slate-500 mt-0.5">إدارة الواجبات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.exams'))
            <a href="{{ route('instructor.exams.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.exams.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-clipboard-check text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">الامتحانات</div><div class="text-xs text-slate-500 mt-0.5">إدارة الاختبارات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor)
            <a href="{{ route('instructor.question-banks.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-teal-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-database text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">بنوك الأسئلة</div><div class="text-xs text-slate-500 mt-0.5">إدارة الأسئلة</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.groups'))
            <a href="{{ route('instructor.groups.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.groups.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-users text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">المجموعات</div><div class="text-xs text-slate-500 mt-0.5">إدارة المجموعات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.attendance'))
            <a href="{{ route('instructor.attendance.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.attendance.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-cyan-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-clipboard-list text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">الحضور</div><div class="text-xs text-slate-500 mt-0.5">تسجيل الحضور</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.view.tasks'))
            <a href="{{ route('instructor.tasks.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.tasks.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-check-square text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">المهام من الإدارة</div><div class="text-xs text-slate-500 mt-0.5">مهام مسندة من الإدارة</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            @if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index'))
            <a href="{{ route('instructor.management-requests.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.management-requests.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-indigo-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-paper-plane text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">تقديم طلبات للإدارة</div><div class="text-xs text-slate-500 mt-0.5">طلباتي للإدارة</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif

            <a href="{{ route('instructor.agreements.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.agreements.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-teal-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-handshake text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">نظام الاتفاقيات</div><div class="text-xs text-slate-500 mt-0.5">عقدي مع المنصة</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>

            <a href="{{ route('instructor.withdrawals.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.withdrawals.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-money-bill-wave text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">طلبات السحب</div><div class="text-xs text-slate-500 mt-0.5">سحب الماديات</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isInstructor())
            <hr class="my-3 border-slate-200">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('admin.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
                <div class="w-9 h-9 rounded-lg bg-slate-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-cog text-sm"></i></div>
                <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">لوحة الإدارة</div><div class="text-xs text-slate-500 mt-0.5">الإدارة</div></div>
                <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
            </a>
            @endif
        @endif

        <a href="{{ route('instructor.portfolio.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.portfolio.*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
            <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-briefcase text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">البورتفوليو</div><div class="text-xs text-slate-500 mt-0.5">مراجعة المشاريع</div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>

        <a href="{{ route('instructor.profile') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('instructor.profile*') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
            <div class="w-9 h-9 rounded-lg bg-slate-600 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-user text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">الملف الشخصي</div><div class="text-xs text-slate-500 mt-0.5">معلوماتي</div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>

        @if(auth()->check() && auth()->user()->hasPermission('student.view.settings'))
        <a href="{{ route('settings') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors {{ request()->routeIs('settings') ? 'bg-sky-50 border border-sky-200' : 'hover:bg-slate-50 border border-transparent' }}">
            <div class="w-9 h-9 rounded-lg bg-slate-500 text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-cog text-sm"></i></div>
            <div class="flex-1 min-w-0"><div class="font-bold text-slate-800 text-sm">الإعدادات</div><div class="text-xs text-slate-500 mt-0.5">الخيارات</div></div>
            <i class="fas fa-chevron-left text-slate-400 text-xs"></i>
        </a>
        @endif
    </nav>

    <!-- User profile at bottom -->
    <div class="p-3 md:p-4 border-t border-slate-200 bg-slate-50/50">
        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white font-bold text-sm overflow-hidden flex-shrink-0">
                @if(auth()->user()->profile_image)
                    <img src="{{ asset(auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-800 text-sm truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500 truncate">مدرب</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
