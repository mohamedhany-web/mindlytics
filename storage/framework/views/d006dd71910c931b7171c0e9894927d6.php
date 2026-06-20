<?php
    $loc = $resolvedPlaceLocation ?? auth()->user()?->offlineLocation;
    $user = auth()->user();
    $profileImage = $user->profile_image_url;
?>
<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white">
    <div class="flex items-center justify-center h-16 px-4 border-b border-slate-700/50">
        <a href="<?php echo e(route('place.office.dashboard')); ?>" class="flex items-center gap-2 min-w-0">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                <i class="fas fa-map-marker-alt text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <span class="text-lg font-bold text-white block truncate"><?php echo e($loc->name ?? 'Mindlytics'); ?></span>
                <span class="text-[10px] text-slate-400 block truncate">إدارة المكان</span>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-6 space-y-2 place-manager-sidebar-nav">
        <a href="<?php echo e(route('place.office.dashboard')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('place.office.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-home text-base"></i>
            <span>لوحة التحكم</span>
        </a>

        <a href="<?php echo e(route('place.office.usage-logs.index')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('place.office.usage-logs.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-clipboard-list text-base"></i>
            <span>التسجيل اليومي</span>
        </a>

        <a href="<?php echo e(route('place.office.settlements.index')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('place.office.settlements.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-calendar-check text-base"></i>
            <span>المخالصة الشهرية</span>
        </a>

        <a href="<?php echo e(route('place.office.invoices.index')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('place.office.invoices.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-file-invoice text-base"></i>
            <span>فواتير الدفع</span>
        </a>

        <div class="border-t border-slate-700/50 my-4"></div>

        <a href="<?php echo e(route('place.office.profile')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('place.office.profile*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-user text-base"></i>
            <span>الملف الشخصي</span>
        </a>
    </nav>

    <div class="border-t border-slate-700/50 p-4">
        <div class="flex items-center gap-3 mb-3">
            <?php if($profileImage): ?>
                <img src="<?php echo e($profileImage); ?>" alt="<?php echo e($user->name); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                    <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                </div>
            <?php endif; ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate"><?php echo e($user->name); ?></p>
                <p class="text-xs text-slate-400 truncate">مدير مكان</p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-700/50 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\layouts\place-manager-sidebar.blade.php ENDPATH**/ ?>