<?php $__env->startSection('title', __('admin.course_community_new_post') . ' — Mindlytics Community'); ?>
<?php $__env->startSection('page_title', __('admin.course_community_new_post')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-4 sm:space-y-6" style="background: #f8fafc;">

    <?php if(session('success')): ?>
        <div class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white px-5 py-4 text-emerald-900 shadow-lg flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"><i class="fas fa-check text-lg"></i></span>
            <span class="font-bold"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50/95 px-5 py-4 shadow-lg">
            <p class="font-black text-rose-900 mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-1 text-sm text-rose-800">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="rounded-2xl p-6 sm:p-8 relative overflow-hidden border-2 border-violet-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 w-full group"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.96) 45%, rgba(237,233,254,0.92) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-100/50 via-indigo-50/30 to-fuchsia-50/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        <div class="absolute top-0 left-0 w-40 h-40 bg-gradient-to-br from-violet-400/15 to-transparent rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            <div class="flex items-start gap-5 min-w-0 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center shadow-xl shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #4f46e5 100%); box-shadow: 0 10px 28px rgba(124, 58, 237, 0.35);">
                    <i class="fas fa-bullhorn text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-violet-700/90 mb-1">Mindlytics Community · <?php echo e(__('admin.course_community_title')); ?></p>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight bg-gradient-to-r from-violet-800 via-indigo-700 to-violet-700 bg-clip-text text-transparent">
                        <?php echo e(__('admin.course_community_new_post')); ?>

                    </h1>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed max-w-4xl">
                        <?php echo e(__('admin.course_community_subtitle')); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="<?php echo e(route('admin.mobile-app.course-community.index')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-<?php echo e(app()->getLocale() === 'ar' ? 'right' : 'left'); ?> text-violet-600"></i>
                    رجوع للمراقبة
                </a>
                <span class="inline-flex items-center gap-2 rounded-xl border-2 border-violet-200/80 bg-white/90 px-4 py-2.5 text-xs sm:text-sm font-bold text-violet-900 shadow-sm">
                    <i class="fas fa-users text-violet-600"></i> يظهر للطلاب في التطبيق
                </span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 w-full">
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-blue-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(239,246,255,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-blue-800/90">اختيار الكورس</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg"><i class="fas fa-graduation-cap text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-blue-700/80 leading-relaxed">المنشور يُدرج في خلاصة مجتمع هذا الكورس فقط كما يراها الطلاب المشتركون.</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-emerald-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(236,253,245,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-emerald-800/90">نص أو صور</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg"><i class="fas fa-image text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-emerald-800/80 leading-relaxed">يمكن نشر نص فقط، أو صور فقط، أو الجمع بينهما — حتى 10 صور.</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-amber-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,251,235,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-amber-900/90">التخزين</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg"><i class="fas fa-cloud text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-amber-900/80 leading-relaxed">الصور تُرفع على نفس قرص تطبيق الطلاب (Cloudflare R2 عند التفعيل في .env).</p>
        </div>
    </div>

    <form method="post" action="<?php echo e(route('admin.mobile-app.course-community.posts.store')); ?>" enctype="multipart/form-data" class="space-y-6 w-full">
        <?php echo csrf_field(); ?>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 text-white shadow-md"><i class="fas fa-book"></i></span>
                الكورس المستهدف
            </h2>
            <div class="w-full">
                <label class="block text-sm font-bold text-slate-700 mb-2">الكورس <span class="text-red-500">*</span></label>
                <select name="course_id" required
                        class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                    <option value="">— اختر الكورس —</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(old('course_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?> <?php if($c->title_en): ?> (<?php echo e($c->title_en); ?>) <?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-md"><i class="fas fa-align-right"></i></span>
                نص المنشور
            </h2>
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-700">المحتوى الظاهر في التطبيق</label>
                <textarea name="body" rows="10"
                          placeholder="اكتب المنشور كما سيظهر للطلاب في التطبيق…"
                          class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm leading-relaxed focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow min-h-[200px]"><?php echo e(old('body')); ?></textarea>
                <p class="text-xs text-slate-500 font-medium">يمكن ترك النص فارغًا إذا كان المنشور صورًا فقط.</p>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md"><i class="fas fa-images"></i></span>
                الصور المرفقة
            </h2>
            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700">رفع صور (اختياري، حتى 10 ملفات)</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="block w-full text-sm text-slate-600 file:me-4 file:py-3 file:px-5 file:rounded-xl file:border-2 file:border-violet-200 file:bg-violet-50 file:text-violet-800 file:font-bold hover:file:bg-violet-100 cursor-pointer"/>
                <p class="text-xs text-slate-500 leading-relaxed">صور JPEG أو PNG أو WebP وغيرها — الحد الأقصى لكل ملف 8 ميجابايت كما في API الطلاب.</p>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md"><i class="fas fa-thumbtack"></i></span>
                خيارات العرض
            </h2>
            <label class="flex items-start gap-4 cursor-pointer rounded-xl border-2 border-amber-100 bg-amber-50/40 p-4 hover:bg-amber-50/70 transition-colors">
                <input type="checkbox" name="is_pinned" value="1" class="mt-1 rounded border-slate-300 text-violet-600 focus:ring-violet-500 w-5 h-5" <?php if(old('is_pinned')): echo 'checked'; endif; ?>/>
                <span>
                    <span class="font-black text-slate-900 block">تثبيت المنشور في أعلى خلاصة الكورس</span>
                    <span class="text-sm text-slate-600">يُعرض قبل المنشورات غير المثبتة كما في تطبيق الطلاب.</span>
                </span>
            </label>
        </div>

        <div class="rounded-2xl border-2 border-indigo-200/60 p-5 sm:p-8 shadow-lg w-full"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(238,242,255,0.9) 100%);">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-indigo-900 mb-1">جاهز للنشر؟</p>
                    <p class="text-xs sm:text-sm text-indigo-700/80 leading-relaxed">سيُسجَّل المنشور باسم حسابك كمسؤول ويظهر فورًا في خلاصة الكورس للطلاب المؤهلين.</p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="<?php echo e(route('admin.mobile-app.course-community.index')); ?>"
                       class="inline-flex items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-6 py-3.5 font-black text-slate-700 hover:bg-slate-50 transition-colors min-w-[140px]">
                        إلغاء
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 via-indigo-600 to-violet-700 px-8 py-3.5 text-white font-black shadow-xl shadow-violet-600/35 hover:shadow-2xl hover:from-violet-500 hover:via-indigo-500 hover:to-violet-600 transition-all duration-300 min-w-[200px]">
                        <i class="fas fa-paper-plane"></i>
                        نشر المنشور
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\mobile-app\course-community\create.blade.php ENDPATH**/ ?>