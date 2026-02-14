

<?php $__env->startSection('title', ($profile->user->name ?? 'مدرب') . ' - Mindlytics'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                <?php if($profile->photo_path): ?>
                    <div class="w-32 h-32 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                        <img src="<?php echo e($profile->photo_url); ?>" alt="<?php echo e($profile->user->name); ?>" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                    </div>
                <?php else: ?>
                    <div class="w-32 h-32 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-900"><?php echo e($profile->user->name); ?></h1>
                <p class="text-sky-600 font-medium mt-1"><?php echo e($profile->headline ?? 'مدرب'); ?></p>
                <?php if($profile->bio): ?>
                    <div class="mt-4 text-slate-700 whitespace-pre-line"><?php echo e($profile->bio); ?></div>
                <?php endif; ?>
                <?php if(!empty($profile->social_links['linkedin'])): ?>
                <a href="<?php echo e($profile->social_links['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 mt-4 rounded-xl bg-[#0A66C2] text-white px-4 py-2 text-sm font-semibold hover:bg-[#004182] transition-colors">
                    <i class="fab fa-linkedin text-lg"></i>
                    <span>LinkedIn</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php if($profile->experience): ?>
        <div class="px-6 sm:px-8 pb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-3">الخبرات</h2>
            <?php if(count($profile->experience_list) > 0): ?>
            <ul class="space-y-3 ps-4 pe-4 max-w-full">
                <?php $__currentLoopData = $profile->experience_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex gap-3 text-slate-700 items-start">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold mt-0.5">•</span>
                    <span class="flex-1 min-w-0 break-words text-justify"><?php echo e($item); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <div class="text-slate-700 whitespace-pre-line break-words pe-2"><?php echo e($profile->experience); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($profile->skills_list): ?>
        <div class="px-6 sm:px-8 pb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-3">المهارات</h2>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $profile->skills_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 px-3 py-1.5 text-sm font-medium border border-slate-200"><?php echo e($skill); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if($courses->count() > 0): ?>
        <div class="px-6 sm:px-8 pb-8 border-t border-slate-100 pt-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">كورسات المدرب</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('public.course.show', $c->id)); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <?php if($c->thumbnail): ?>
                        <img src="<?php echo e(asset('storage/' . str_replace('\\', '/', $c->thumbnail))); ?>" alt="" class="w-14 h-14 rounded-lg object-cover">
                    <?php else: ?>
                        <div class="w-14 h-14 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-book"></i></div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 truncate"><?php echo e($c->title); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($c->price > 0 ? number_format($c->price) . ' ج.م' : 'مجاني'); ?></p>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructors/show.blade.php ENDPATH**/ ?>