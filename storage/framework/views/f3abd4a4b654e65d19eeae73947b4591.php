

<?php $__env->startSection('title', __('admin.community_discussions')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
        المناقشات
    </h1>
    <p class="text-slate-600 mb-8">
        ناقش مع الخبراء والمتعلمين، اسأل وأجب، وشارك في حوارات المجتمع.
    </p>

    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm text-center">
        <div class="w-20 h-20 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-comments text-4xl"></i>
        </div>
        <h2 class="text-xl font-black text-slate-900 mb-3">المناقشات قادمة قريباً</h2>
        <p class="text-slate-600 max-w-xl mx-auto mb-6">
            منصة المناقشات ستتيح فتح مواضيع مرتبطة بالمسابقات ومجموعات البيانات والكورسات، مع إمكانية الإجابة والتعليق والتصويت.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">أسئلة وأجوبة</span>
            <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">تعليقات وتصويت</span>
            <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">ربط بالمسابقات والبيانات</span>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\discussions\index.blade.php ENDPATH**/ ?>