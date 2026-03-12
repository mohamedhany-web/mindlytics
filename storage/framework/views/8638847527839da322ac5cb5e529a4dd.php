<?php $locale = app()->getLocale(); $rtl = $locale === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($rtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('public.community_heading')); ?> - <?php echo e(config('app.name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Tajawal', 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
        body { background: #f8fafc; }
        /* سايدبار المجتمع — متناسق مع البلاتفورم */
        .community-sidebar {
            background: #ffffff;
            border-<?php echo e($rtl ? 'left' : 'right'); ?>: 1px solid rgb(226 232 240);
            width: 280px;
            box-shadow: <?php echo e($rtl ? '1px' : '-1px'); ?> 0 6px rgba(0, 0, 0, 0.04);
        }
        .nav-card {
            background: transparent;
            border: none;
            border-radius: 12px;
            padding: 10px 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .nav-card::before {
            content: '';
            position: absolute;
            <?php echo e($rtl ? 'left' : 'right'); ?>: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgb(6 182 212);
            opacity: 0;
            border-radius: <?php echo e($rtl ? '3px 0 0 3px' : '0 3px 3px 0'); ?>;
            transition: opacity 0.2s;
        }
        .nav-card:hover { background: rgb(241 245 249); }
        .nav-card.active {
            background: rgb(207 250 254);
            box-shadow: 0 1px 3px rgba(6, 182, 212, 0.12);
        }
        .nav-card.active::before { opacity: 1; }
        .nav-card.active .nav-icon {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(6, 182, 212, 0.25);
        }
        .nav-card.active .font-black { color: rgb(17 24 39); }
        .nav-card.active .text-xs { color: rgb(75 85 99); }
        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .nav-card:hover .nav-icon { transform: scale(1.05); }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgb(6 182 212), rgb(14 165 233));
            border-radius: 3px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgb(14 165 233), rgb(6 182 212));
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="min-h-screen text-gray-900" x-data="{ sidebarOpen: false, isLg: false }" x-init="
    isLg = window.matchMedia('(min-width: 1024px)').matches;
    window.matchMedia('(min-width: 1024px)').addEventListener('change', e => { isLg = e.matches });
">
    <div class="flex min-h-screen">
        <div x-show="sidebarOpen && !isLg" x-transition class="fixed inset-0 bg-black/50 z-40 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        <aside class="community-sidebar flex flex-col fixed inset-y-0 z-50 transition-transform duration-300 ease-out <?php echo e($rtl ? 'right-0' : 'left-0'); ?>"
               :class="(sidebarOpen || isLg) ? 'translate-x-0' : '<?php echo e($rtl ? 'translate-x-full' : '-translate-x-full'); ?>'"
               x-show="sidebarOpen || isLg"
               x-cloak>
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-white">
                <a href="<?php echo e(route('community.dashboard')); ?>" class="flex items-center gap-3" @click="if(!isLg) sidebarOpen = false">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-brain text-white text-sm"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <span class="font-black text-slate-900 text-sm block"><?php echo e(__('public.community_heading')); ?></span>
                        <span class="text-xs text-slate-500">مجتمع البيانات والذكاء الاصطناعي</span>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100" aria-label="إغلاق القائمة">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto sidebar-scroll p-3 space-y-0.5">
                <a href="<?php echo e(route('community.dashboard')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.dashboard') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-cyan-500 text-white flex-shrink-0">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm"><?php echo e(__('auth.home')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">نظرة عامة</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.competitions.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.competitions.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-amber-500 text-white flex-shrink-0">
                            <i class="fas fa-trophy text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm"><?php echo e(__('admin.community_competitions')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">المسابقات النشطة</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.data.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.data.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-blue-500 text-white flex-shrink-0">
                            <i class="fas fa-database text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm"><?php echo e(__('admin.community_datasets')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">مجموعات البيانات</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.models.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.models.*') && !request()->routeIs('community.contributor.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-amber-600 text-white flex-shrink-0">
                            <i class="fas fa-brain text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm"><?php echo e(__('admin.community_models')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">مكتبة النماذج</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.discussions.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.discussions.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-emerald-500 text-white flex-shrink-0">
                            <i class="fas fa-comments text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm"><?php echo e(__('admin.community_discussions')); ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">المناقشات</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>

                <?php if(auth()->user()->is_community_contributor ?? false): ?>
                <div class="pt-3 mt-3 border-t border-slate-200">
                    <p class="px-3 py-1.5 text-xs font-bold text-cyan-600 uppercase tracking-wide">لوحة المساهم</p>
                </div>
                <a href="<?php echo e(route('community.contributor.dashboard')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.contributor.dashboard') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-cyan-600 text-white flex-shrink-0">
                            <i class="fas fa-user-edit text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm">لوحة المساهم</div>
                            <div class="text-xs text-gray-500 mt-0.5">إحصائياتك وتقديماتك</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.contributor.profile.edit')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.contributor.profile.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-slate-500 text-white flex-shrink-0">
                            <i class="fas fa-id-card text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm">نبذة عنك</div>
                            <div class="text-xs text-gray-500 mt-0.5">الملف التعريفي</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.contributor.datasets.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.contributor.datasets.*') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-blue-600 text-white flex-shrink-0">
                            <i class="fas fa-folder-open text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm">تقديماتي (بيانات)</div>
                            <div class="text-xs text-gray-500 mt-0.5">مجموعات البيانات</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.contributor.models.index')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.contributor.models.*') && !request()->routeIs('community.contributor.models.create') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-amber-600 text-white flex-shrink-0">
                            <i class="fas fa-brain text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm">نماذجي</div>
                            <div class="text-xs text-gray-500 mt-0.5">Model Zoo</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('community.contributor.models.create')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="nav-card block <?php echo e(request()->routeIs('community.contributor.models.create') ? 'active' : ''); ?>">
                    <div class="flex items-center gap-3">
                        <div class="nav-icon bg-emerald-500 text-white flex-shrink-0">
                            <i class="fas fa-plus text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-gray-900 text-sm">إضافة نموذج</div>
                            <div class="text-xs text-gray-500 mt-0.5">رفع نموذج جديد</div>
                        </div>
                        <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                    </div>
                </a>
                <?php endif; ?>

                <div class="pt-3 mt-3 border-t border-slate-200">
                    <a href="<?php echo e(route('public.courses')); ?>" @click="if(!isLg) sidebarOpen = false"
                       class="nav-card block">
                        <div class="flex items-center gap-3">
                            <div class="nav-icon bg-slate-400 text-white flex-shrink-0">
                                <i class="fas fa-book text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-gray-900 text-sm"><?php echo e(__('landing.nav.courses')); ?></div>
                                <div class="text-xs text-gray-500 mt-0.5">تصفح الكورسات</div>
                            </div>
                            <i class="fas fa-chevron-<?php echo e($rtl ? 'right' : 'left'); ?> text-gray-400 text-xs flex-shrink-0"></i>
                        </div>
                    </a>
                </div>
            </nav>

            <div class="p-3 border-t border-slate-200 bg-slate-50/50">
                <a href="<?php echo e(route('dashboard')); ?>" @click="if(!isLg) sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-white hover:text-slate-900 text-sm font-semibold transition-colors">
                    <i class="fas fa-external-link-alt w-4"></i>
                    <span>لوحة المنصة الرئيسية</span>
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-1" @submit="if(!isLg) sidebarOpen = false">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-red-50 hover:text-red-600 w-full text-sm font-semibold transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span><?php echo e(__('auth.logout')); ?></span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex flex-col flex-1 min-w-0 <?php echo e($rtl ? 'lg:mr-[280px]' : 'lg:ml-[280px]'); ?>">
            <header class="flex-shrink-0 sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <button type="button" @click="sidebarOpen = true" class="lg:hidden flex-shrink-0 p-2.5 rounded-xl text-slate-600 hover:bg-slate-100" aria-label="فتح القائمة">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex-1 max-w-md hidden sm:block">
                        <p class="text-slate-500 text-sm font-medium truncate">مجتمع الذكاء الاصطناعي — <?php echo e(__('auth.dashboard')); ?></p>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative flex items-center gap-2">
                    <a href="<?php echo e(route('public.community.index')); ?>" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold">
                        <i class="fas fa-info-circle"></i>
                        <span>عن المجتمع</span>
                    </a>
                    <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                        <?php if(auth()->user()->profile_image ?? null): ?>
                            <img src="<?php echo e(auth()->user()->profile_image_url ?? '#'); ?>" alt="" class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-200">
                        <?php else: ?>
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm"><?php echo e(mb_substr(auth()->user()->name ?? 'م', 0, 1)); ?></span>
                        <?php endif; ?>
                        <span class="hidden sm:inline font-semibold text-slate-700 max-w-[120px] truncate"><?php echo e(auth()->user()->name); ?></span>
                        <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute top-full mt-2 <?php echo e($rtl ? 'right-0' : 'left-0'); ?> w-52 py-1 bg-white rounded-xl shadow-xl border border-slate-200 z-30">
                        <a href="<?php echo e(route('community.dashboard')); ?>" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium"><?php echo e(__('auth.dashboard')); ?></a>
                        <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium">المنصة الرئيسية</a>
                        <hr class="my-1 border-slate-100">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="block w-full text-<?php echo e($rtl ? 'right' : 'left'); ?> px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-medium"><?php echo e(__('auth.logout')); ?></button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto min-w-0 w-full">
                <div class="w-full max-w-full p-4 sm:p-6 lg:p-8">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/community/layouts/app.blade.php ENDPATH**/ ?>