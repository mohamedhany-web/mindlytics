

<?php $__env->startSection('title', 'مساهمو المجتمع'); ?>
<?php $__env->startSection('header', 'مساهمو مجتمع الذكاء الاصطناعي'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside"><?php echo e($errors->first()); ?></ul>
        </div>
    <?php endif; ?>

    <!-- مراجعة ملفات المساهمين المعلقة -->
    <?php if(isset($pendingProfiles) && $pendingProfiles->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl shadow border border-amber-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-amber-200 bg-amber-50">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-amber-600"></i>
                    مراجعة ملفات المساهمين المعلقة (<?php echo e($pendingProfiles->count()); ?>)
                </h2>
                <p class="text-sm text-slate-600 mt-1">ملفات نبذة عنك التي أرسلها المساهمون — وافق أو ارفض لعرضهم في صفحة المساهمين.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $pendingProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $user = $profile->user; ?>
                        <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50/50 hover:shadow-lg transition-shadow">
                            <div class="p-4 border-b border-slate-200 flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-200 flex-shrink-0">
                                    <?php if($profile->photo_url): ?>
                                        <img src="<?php echo e($profile->photo_url); ?>" alt="<?php echo e($user->name ?? ''); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-xl font-black text-slate-400"><?php echo e(mb_substr($user->name ?? 'م', 0, 1)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-900 truncate"><?php echo e($user->name ?? '—'); ?></p>
                                    <p class="text-xs text-slate-500 truncate"><?php echo e($user->email ?? '—'); ?></p>
                                    <?php if($profile->submitted_at): ?>
                                        <p class="text-xs text-slate-400 mt-1"><?php echo e($profile->submitted_at->diffForHumans()); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="p-4 space-y-2 text-sm">
                                <?php if($profile->bio): ?>
                                    <p class="text-slate-600 line-clamp-3"><?php echo e(Str::limit($profile->bio, 120)); ?></p>
                                <?php endif; ?>
                                <?php if($profile->experience): ?>
                                    <p class="text-slate-500 line-clamp-2"><?php echo e(Str::limit($profile->experience, 80)); ?></p>
                                <?php endif; ?>
                                <div class="flex flex-wrap gap-2 pt-2">
                                    <?php if($profile->linkedin_url): ?>
                                        <a href="<?php echo e($profile->linkedin_url); ?>" target="_blank" rel="noopener" class="text-[#0A66C2] text-xs font-semibold">LinkedIn</a>
                                    <?php endif; ?>
                                    <?php if($profile->twitter_url): ?>
                                        <a href="<?php echo e($profile->twitter_url); ?>" target="_blank" rel="noopener" class="text-slate-600 text-xs font-semibold">X</a>
                                    <?php endif; ?>
                                    <?php if($profile->website_url): ?>
                                        <a href="<?php echo e($profile->website_url); ?>" target="_blank" rel="noopener" class="text-cyan-600 text-xs font-semibold">الموقع</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="p-4 border-t border-slate-200 flex gap-2">
                                <form action="<?php echo e(route('admin.community.contributors.profiles.approve', $profile)); ?>" method="POST" class="flex-1">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check ml-1"></i> موافقة
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.community.contributors.profiles.reject', $profile)); ?>" method="POST" class="flex-1" onsubmit="return confirm('رفض ملف هذا المساهم؟');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full py-2 rounded-xl bg-red-100 text-red-700 font-bold text-sm hover:bg-red-200 transition-colors">
                                        <i class="fas fa-times ml-1"></i> رفض
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ترقية مستخدم حالي إلى مساهم -->
    <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">ترقية مستخدم حالي إلى مساهم</h2>
        <p class="text-sm text-slate-600 mb-4">أدخل بريد مستخدم مسجل واختر نوع المساهمة: إما <strong>مجتمع البيانات</strong> (رفع مجموعات بيانات) أو <strong>الذكاء الاصطناعي</strong> (رفع نماذج Model Zoo).</p>
        <form action="<?php echo e(route('admin.community.contributors.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع المساهم <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="contributor_type" value="data" <?php echo e(old('contributor_type', 'data') === 'data' ? 'checked' : ''); ?> class="rounded-full border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="font-medium text-slate-700"><i class="fas fa-database text-cyan-600 ml-1"></i> مساهم في مجتمع البيانات</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="contributor_type" value="ai" <?php echo e(old('contributor_type') === 'ai' ? 'checked' : ''); ?> class="rounded-full border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span class="font-medium text-slate-700"><i class="fas fa-brain text-amber-600 ml-1"></i> مساهم في الذكاء الاصطناعي</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">البريد الإلكتروني للمستخدم</label>
                    <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" placeholder="user@example.com"
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors">
                    <i class="fas fa-user-plus ml-1"></i> ترقية إلى مساهم
                </button>
            </div>
        </form>
    </div>

    <!-- إنشاء حساب مساهم جديد -->
    <div class="bg-white rounded-2xl shadow border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">إنشاء حساب مساهم جديد</h2>
        <p class="text-sm text-slate-600 mb-4">إنشاء مستخدم جديد بصلاحية مساهم — اختر نوع المساهمة: <strong>مجتمع البيانات</strong> أو <strong>الذكاء الاصطناعي</strong>.</p>
        <form action="<?php echo e(route('admin.community.contributors.new.store')); ?>" method="POST" class="space-y-4 max-w-2xl">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع المساهم <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="contributor_type" value="data" <?php echo e(old('contributor_type', 'data') === 'data' ? 'checked' : ''); ?> class="rounded-full border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span><i class="fas fa-database text-cyan-600 ml-1"></i> مجتمع البيانات</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="contributor_type" value="ai" <?php echo e(old('contributor_type') === 'ai' ? 'checked' : ''); ?> class="rounded-full border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span><i class="fas fa-brain text-amber-600 ml-1"></i> الذكاء الاصطناعي</span>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="new_name" class="block text-sm font-semibold text-slate-700 mb-1">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="new_name" value="<?php echo e(old('name')); ?>" required
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label for="new_email" class="block text-sm font-semibold text-slate-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="new_email" value="<?php echo e(old('email')); ?>" required
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-1">كلمة المرور <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="new_password" required minlength="8"
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <p class="mt-1 text-xs text-slate-500">8 أحرف على الأقل</p>
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="new_password_confirmation" required minlength="8"
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
            </div>
            <div class="max-w-xs">
                <label for="new_phone" class="block text-sm font-semibold text-slate-700 mb-1">الهاتف (اختياري)</label>
                <input type="text" name="phone" id="new_phone" value="<?php echo e(old('phone')); ?>"
                       class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 text-white font-bold hover:bg-teal-700 transition-colors">
                <i class="fas fa-user-plus ml-1"></i> إنشاء حساب مساهم
            </button>
        </form>
    </div>

    <!-- مساهمون مجتمع البيانات -->
    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-cyan-50/80">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-database text-cyan-600"></i>
                مساهمون مجتمع البيانات (<?php echo e($contributorsData->count()); ?>)
            </h2>
            <p class="text-sm text-slate-600 mt-1">يمكنهم رفع مجموعات البيانات وتقديمها للمراجعة قبل النشر.</p>
        </div>
        <div class="p-6">
            <?php if($contributorsData->isEmpty()): ?>
                <div class="text-center py-10 text-slate-500">
                    <i class="fas fa-database text-3xl mb-3 text-slate-300"></i>
                    <p>لا يوجد مساهمون في مجتمع البيانات. أضف من خلال "ترقية مستخدم" واختر "مجتمع البيانات".</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-600 text-sm">
                                <th class="py-3 px-4">الاسم</th>
                                <th class="py-3 px-4">البريد</th>
                                <th class="py-3 px-4">الدور</th>
                                <th class="py-3 px-4">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $contributorsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-medium text-slate-900"><?php echo e($user->name); ?></td>
                                    <td class="py-3 px-4 text-slate-600"><?php echo e($user->email); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold
                                            <?php if($user->role === 'student'): ?> bg-emerald-100 text-emerald-700
                                            <?php elseif($user->role === 'instructor'): ?> bg-indigo-100 text-indigo-700
                                            <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                                            <?php if($user->role === 'student'): ?> طالب
                                            <?php elseif($user->role === 'instructor'): ?> مدرب
                                            <?php else: ?> <?php echo e($user->role ?? '—'); ?> <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form action="<?php echo e(route('admin.community.contributors.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('إزالة صلاحية المساهم؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                <i class="fas fa-user-minus ml-1"></i> إزالة
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- مساهمون الذكاء الاصطناعي -->
    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-amber-50/80">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-brain text-amber-600"></i>
                مساهمون الذكاء الاصطناعي (<?php echo e($contributorsAi->count()); ?>)
            </h2>
            <p class="text-sm text-slate-600 mt-1">يمكنهم رفع النماذج (Model Zoo) وتقديمها للمراجعة قبل النشر.</p>
        </div>
        <div class="p-6">
            <?php if($contributorsAi->isEmpty()): ?>
                <div class="text-center py-10 text-slate-500">
                    <i class="fas fa-brain text-3xl mb-3 text-slate-300"></i>
                    <p>لا يوجد مساهمون في الذكاء الاصطناعي. أضف من خلال "ترقية مستخدم" واختر "الذكاء الاصطناعي".</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-600 text-sm">
                                <th class="py-3 px-4">الاسم</th>
                                <th class="py-3 px-4">البريد</th>
                                <th class="py-3 px-4">الدور</th>
                                <th class="py-3 px-4">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $contributorsAi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-medium text-slate-900"><?php echo e($user->name); ?></td>
                                    <td class="py-3 px-4 text-slate-600"><?php echo e($user->email); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold
                                            <?php if($user->role === 'student'): ?> bg-emerald-100 text-emerald-700
                                            <?php elseif($user->role === 'instructor'): ?> bg-indigo-100 text-indigo-700
                                            <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                                            <?php if($user->role === 'student'): ?> طالب
                                            <?php elseif($user->role === 'instructor'): ?> مدرب
                                            <?php else: ?> <?php echo e($user->role ?? '—'); ?> <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form action="<?php echo e(route('admin.community.contributors.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('إزالة صلاحية المساهم؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                <i class="fas fa-user-minus ml-1"></i> إزالة
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\community\contributors.blade.php ENDPATH**/ ?>