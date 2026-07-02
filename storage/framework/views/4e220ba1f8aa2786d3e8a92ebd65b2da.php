<?php $active = $active ?? ''; ?>
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm">
    <a href="<?php echo e(route('admin.scholarships.dashboard')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'dashboard' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-tachometer-alt"></i>
        <span>لوحة المنح</span>
    </a>
    <a href="<?php echo e(route('admin.scholarships.programs.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'programs' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-award"></i>
        <span>المنح الدراسية</span>
    </a>
    <a href="<?php echo e(route('admin.scholarships.courses.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'courses' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-book"></i>
        <span>كورسات المنح</span>
    </a>
    <a href="<?php echo e(route('admin.scholarships.instructors.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'instructors' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>مدربو المنح</span>
    </a>
    <a href="<?php echo e(route('admin.scholarships.students.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'students' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-user-graduate"></i>
        <span>طلاب المنح</span>
    </a>
</nav>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/_nav.blade.php ENDPATH**/ ?>