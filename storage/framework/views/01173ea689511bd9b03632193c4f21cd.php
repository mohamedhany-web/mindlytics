

<?php $__env->startSection('title', __('admin.course_community_title') . ' — Mindlytics Community'); ?>
<?php $__env->startSection('page_title', __('admin.course_community_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-4 sm:space-y-6" style="background: #f8fafc;">

    <?php if(session('success')): ?>
        <div class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white px-5 py-4 text-emerald-900 shadow-lg flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"><i class="fas fa-check text-lg"></i></span>
            <span class="font-bold"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <div class="rounded-2xl p-6 sm:p-8 relative overflow-hidden border-2 border-violet-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 w-full group"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.96) 45%, rgba(237,233,254,0.92) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-100/50 via-indigo-50/30 to-fuchsia-50/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        <div class="absolute top-0 start-0 w-40 h-40 bg-gradient-to-br from-violet-400/15 to-transparent rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            <div class="flex items-start gap-5 min-w-0 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center shadow-xl shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #4f46e5 100%); box-shadow: 0 10px 28px rgba(124, 58, 237, 0.35);">
                    <i class="fas fa-comments text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-violet-700/90 mb-1">Mindlytics Community · <?php echo e(__('admin.course_community_title')); ?></p>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight bg-gradient-to-r from-violet-800 via-indigo-700 to-violet-700 bg-clip-text text-transparent">
                        مراقبة منشورات التطبيق
                    </h1>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed max-w-4xl">
                        <?php echo e(__('admin.course_community_subtitle')); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 shrink-0 w-full xl:w-auto">
                <a href="<?php echo e(route('admin.mobile-app.edit')); ?>"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50 transition-colors">
                    <i class="fas fa-mobile-alt text-violet-600"></i>
                    محتوى الصفحة الرئيسية
                </a>
                <a href="<?php echo e(route('admin.mobile-app.course-community.posts.create')); ?>"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 via-indigo-600 to-violet-700 px-5 py-2.5 text-white font-black shadow-xl shadow-violet-600/35 hover:from-violet-500 hover:via-indigo-500 hover:to-violet-600 transition-all duration-300">
                    <i class="fas fa-plus"></i>
                    <?php echo e(__('admin.course_community_new_post')); ?>

                </a>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 w-full">
        <div class="rounded-2xl p-5 sm:p-6 border-2 border-blue-200/50 shadow-lg transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(239,246,255,0.95) 100%);">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">إجمالي المنشورات</p>
                    <p class="text-3xl font-black text-blue-700"><?php echo e($posts->total()); ?></p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg shrink-0">
                    <i class="fas fa-layer-group text-white text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-blue-700/75 mt-3 leading-relaxed">حسب الفلاتر الحالية</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 border-2 border-emerald-200/50 shadow-lg transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(236,253,245,0.95) 100%);">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-1">الصفحة الحالية</p>
                    <p class="text-3xl font-black text-emerald-800"><?php echo e($posts->currentPage()); ?> <span class="text-lg font-bold text-emerald-600">/ <?php echo e($posts->lastPage()); ?></span></p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shrink-0">
                    <i class="fas fa-list-ol text-white text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-emerald-800/75 mt-3 leading-relaxed"><?php echo e($posts->perPage()); ?> عنصر في الصفحة</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 border-2 border-amber-200/50 shadow-lg transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,251,235,0.95) 100%);">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-1">كورسات في القائمة</p>
                    <p class="text-3xl font-black text-amber-900"><?php echo e($courses->count()); ?></p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shrink-0">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-amber-900/75 mt-3 leading-relaxed">للفلترة حسب الكورس</p>
        </div>
    </div>

    
    <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
        <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white shadow-md"><i class="fas fa-filter"></i></span>
            تصفية وبحث
        </h2>
        <form method="get" action="<?php echo e(route('admin.mobile-app.course-community.index')); ?>" class="flex flex-col lg:flex-row flex-wrap gap-4 lg:items-end">
            <div class="flex-1 min-w-[200px] space-y-2">
                <label class="block text-sm font-bold text-slate-700"><?php echo e(__('admin.course_community_all_courses')); ?></label>
                <select name="course_id"
                        class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                    <option value="">— الكل —</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(request('course_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-[2] min-w-[240px] space-y-2">
                <label class="block text-sm font-bold text-slate-700"><?php echo e(__('admin.course_community_search_placeholder')); ?></label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(__('admin.course_community_search_placeholder')); ?>"
                       class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow"/>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-black px-6 py-3 shadow-lg transition-colors min-w-[120px]">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
                <?php if(request()->hasAny(['course_id','q'])): ?>
                    <a href="<?php echo e(route('admin.mobile-app.course-community.index')); ?>"
                       class="inline-flex items-center justify-center rounded-xl border-2 border-violet-200 bg-violet-50 px-5 py-3 text-sm font-bold text-violet-800 hover:bg-violet-100 transition-colors">
                        مسح الفلاتر
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    
    <div class="rounded-2xl border-2 border-slate-200/70 bg-white shadow-xl overflow-hidden w-full">
        <div class="px-5 sm:px-8 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gradient-to-r from-slate-50/80 to-white">
            <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-table text-violet-600"></i>
                قائمة المنشورات
            </h2>
            <span class="text-sm font-semibold text-slate-500"><?php echo e($posts->firstItem() ?? 0); ?>–<?php echo e($posts->lastItem() ?? 0); ?> من <?php echo e($posts->total()); ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-100 to-slate-50/95 border-b-2 border-slate-200">
                        <th class="text-start px-4 sm:px-6 py-4 font-black text-slate-800 whitespace-nowrap">#</th>
                        <th class="text-start px-4 sm:px-6 py-4 font-black text-slate-800 whitespace-nowrap">الكورس</th>
                        <th class="text-start px-4 sm:px-6 py-4 font-black text-slate-800 whitespace-nowrap">الكاتب</th>
                        <th class="text-start px-4 sm:px-6 py-4 font-black text-slate-800 min-w-[200px]">المحتوى</th>
                        <th class="text-center px-4 py-4 font-black text-slate-800 whitespace-nowrap">صور</th>
                        <th class="text-center px-4 py-4 font-black text-slate-800 whitespace-nowrap">تعليقات</th>
                        <th class="text-center px-4 py-4 font-black text-slate-800 whitespace-nowrap">تثبيت</th>
                        <th class="text-start px-4 sm:px-6 py-4 font-black text-slate-800 whitespace-nowrap">تاريخ</th>
                        <th class="text-center px-4 sm:px-6 py-4 font-black text-slate-800 whitespace-nowrap">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-violet-50/40 transition-colors">
                            <td class="px-4 sm:px-6 py-4 text-slate-500 font-mono text-xs"><?php echo e($post->id); ?></td>
                            <td class="px-4 sm:px-6 py-4 font-bold text-slate-800 max-w-[200px]">
                                <span class="line-clamp-2"><?php echo e(Str::limit($post->course?->title ?? '—', 48)); ?></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-slate-700 font-semibold"><?php echo e($post->user->name ?? '—'); ?></td>
                            <td class="px-4 sm:px-6 py-4 text-slate-600 max-w-xl">
                                <span class="line-clamp-2"><?php echo e(Str::limit($post->body, 120)); ?></span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex min-w-[2rem] justify-center rounded-lg bg-slate-100 px-2 py-1 font-bold text-slate-700"><?php echo e($post->images->count()); ?></span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex min-w-[2rem] justify-center rounded-lg bg-emerald-50 px-2 py-1 font-bold text-emerald-800"><?php echo e($post->comments_count); ?></span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <?php if($post->is_pinned): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-900 px-2.5 py-1 text-xs font-black border border-amber-200/80"><i class="fas fa-thumbtack"></i> نعم</span>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-slate-500 whitespace-nowrap text-xs font-medium"><?php echo e($post->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 sm:px-6 py-4 text-center whitespace-nowrap">
                                <a href="<?php echo e(route('admin.mobile-app.course-community.posts.show', $post)); ?>"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-violet-50 hover:bg-violet-100 border border-violet-200/80 text-violet-800 font-black px-4 py-2 text-xs transition-colors">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="max-w-md mx-auto rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 p-8">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-2xl text-violet-500"></i>
                                    </div>
                                    <p class="font-black text-slate-800 text-lg mb-2"><?php echo e(__('admin.course_community_no_posts')); ?></p>
                                    <p class="text-sm text-slate-600 mb-6">ابدأ بمراجعة منشورات الطلاب أو انشر منشورًا إداريًا من الزر أعلاه.</p>
                                    <a href="<?php echo e(route('admin.mobile-app.course-community.posts.create')); ?>"
                                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-3 text-white font-black shadow-lg shadow-violet-600/25 hover:from-violet-500 hover:to-indigo-500 transition-all">
                                        <i class="fas fa-plus"></i>
                                        <?php echo e(__('admin.course_community_new_post')); ?>

                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($posts->hasPages()): ?>
            <div class="px-5 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <?php echo e($posts->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/mobile-app/course-community/index.blade.php ENDPATH**/ ?>