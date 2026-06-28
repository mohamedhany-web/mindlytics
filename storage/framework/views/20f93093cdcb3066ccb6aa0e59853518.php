<?php
    $active = $active ?? '';
?>
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border-2 border-slate-200/50 shadow-sm">
    <a href="<?php echo e(route('admin.hr.jobs.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'jobs' ? 'bg-pink-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-briefcase"></i>
        الوظائف
    </a>
    <a href="<?php echo e(route('admin.hr.applications.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'applications' ? 'bg-pink-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-inbox"></i>
        المتقدمون والسكور
    </a>
    <a href="<?php echo e(route('careers.index')); ?>" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-sky-700 border border-sky-200 bg-sky-50 hover:bg-sky-100 transition-all mr-auto">
        <i class="fas fa-external-link-alt"></i>
        صفحة التوظيف العامة
    </a>
</nav>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/hr/_nav.blade.php ENDPATH**/ ?>