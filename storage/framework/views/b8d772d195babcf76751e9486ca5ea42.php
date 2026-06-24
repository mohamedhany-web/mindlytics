<div class="flex flex-col h-full">
    <!-- Logo Section -->
    <div class="sidebar-logo flex items-center gap-3 px-6">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
            <i class="fas fa-code text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-lg font-black text-gray-900">Mindlytics</h2>
            <p class="text-xs text-gray-500">أكاديمية البرمجة</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
        <?php if(auth()->check()): ?>
            <?php
                $hasStudentPermissions = (
                    auth()->user()->hasPermission('student.view.courses') ||
                    auth()->user()->hasPermission('student.view.my-courses') ||
                    auth()->user()->hasPermission('student.view.orders') ||
                    auth()->user()->hasPermission('student.view.invoices') ||
                    auth()->user()->hasPermission('student.view.wallet') ||
                    auth()->user()->hasPermission('student.view.certificates') ||
                    auth()->user()->hasPermission('student.view.achievements') ||
                    auth()->user()->hasPermission('student.view.exams') ||
                    auth()->user()->hasPermission('student.view.notifications') ||
                    auth()->user()->hasPermission('student.view.profile') ||
                    auth()->user()->hasPermission('student.view.settings')
                );
            ?>
            <?php if($hasStudentPermissions): ?>
            <!-- Dashboard -->
            <a href="<?php echo e(route('dashboard')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i>
                <span>لوحة التحكم</span>
            </a>

            <!-- Browse Courses -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.courses')): ?>
            <?php
                $catalogActive = request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.*');
            ?>
            <a href="<?php echo e(route('academic-years')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e($catalogActive ? 'active' : ''); ?>">
                <i class="fas fa-search"></i>
                <span>تصفح الكورسات</span>
            </a>
            <?php endif; ?>

            <!-- My Courses -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.my-courses')): ?>
            <a href="<?php echo e(route('my-courses.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('my-courses.*') ? 'active' : ''); ?>">
                <i class="fas fa-book-open"></i>
                <span>كورساتي</span>
            </a>
            <?php endif; ?>

            <!-- Orders -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.orders')): ?>
            <a href="<?php echo e(route('orders.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>طلباتي</span>
            </a>
            <?php endif; ?>

            <!-- Exams -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.exams')): ?>
            <a href="<?php echo e(route('student.exams.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('student.exams.*') ? 'active' : ''); ?>">
                <i class="fas fa-clipboard-check"></i>
                <span>الامتحانات</span>
            </a>
            <?php endif; ?>

            <!-- Certificates -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.certificates')): ?>
            <a href="<?php echo e(route('student.certificates.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('student.certificates.*') ? 'active' : ''); ?>">
                <i class="fas fa-certificate"></i>
                <span>شهاداتي</span>
            </a>
            <?php endif; ?>

            <!-- Wallet -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.wallet')): ?>
            <a href="<?php echo e(route('student.wallet.index')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('student.wallet.*') ? 'active' : ''); ?>">
                <i class="fas fa-wallet"></i>
                <span>محفظتي</span>
            </a>
            <?php endif; ?>

            <!-- Notifications -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.notifications')): ?>
            <a href="<?php echo e(route('notifications')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('notifications') ? 'active' : ''); ?>">
                <i class="fas fa-bell"></i>
                <span>الإشعارات</span>
            </a>
            <?php endif; ?>

            <!-- Profile -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.profile')): ?>
            <a href="<?php echo e(route('profile')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('profile') ? 'active' : ''); ?>">
                <i class="fas fa-user"></i>
                <span>الملف الشخصي</span>
            </a>
            <?php endif; ?>

            <!-- Settings -->
            <?php if(auth()->check() && auth()->user()->hasPermission('student.view.settings')): ?>
            <a href="<?php echo e(route('settings')); ?>" 
               @click="if (window.innerWidth < 1024) sidebarOpen = false"
               class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('settings') ? 'active' : ''); ?>">
                <i class="fas fa-cog"></i>
                <span>الإعدادات</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(auth()->check()): ?>
            <?php
                $isAdminOrInstructor = auth()->user()->isAdmin() || auth()->user()->isInstructor();
                $isAdmin = auth()->user()->isAdmin();
                $isInstructor = auth()->user()->isInstructor();
            ?>
            <?php if($isAdminOrInstructor): ?>
            <hr class="my-4 mx-4 border-gray-200">
            
            <?php if($isAdmin): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>لوحة التحكم</span>
                </a>
            <?php endif; ?>
            
            <?php if($isInstructor): ?>
                <a href="<?php echo e(route('dashboard')); ?>" 
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="sidebar-nav-item flex items-center gap-3 <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>لوحة التحكم</span>
                </a>
            <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- User Info at Bottom -->
    <?php if(auth()->check()): ?>
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                <?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="" class="w-full h-full rounded-full object-cover">
                <?php else: ?>
                    <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs text-gray-500 truncate">
                    <?php
                        $userRole = auth()->user()->isAdmin() ? 'مدير' : (auth()->user()->isInstructor() ? 'مدرب' : 'طالب');
                    ?>
                    <?php echo e($userRole); ?>

                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\layouts\modern-sidebar.blade.php ENDPATH**/ ?>