<?php $active = $active ?? ''; ?>
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm">
    <a href="<?php echo e(route('instructor.scholarships.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors <?php echo e($active === 'programs' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'); ?>">
        <i class="fas fa-award"></i>
        <span>منحي</span>
    </a>
    <a href="<?php echo e(route('instructor.scholarships.students.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors <?php echo e($active === 'students' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'); ?>">
        <i class="fas fa-user-graduate"></i>
        <span>طلاب المنح</span>
    </a>
</nav>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/scholarships/_nav.blade.php ENDPATH**/ ?>