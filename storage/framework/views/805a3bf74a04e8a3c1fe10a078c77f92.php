<?php
    $filterAction = $filterAction ?? route('instructor.scholarships.students.index');
    $showProgramFilter = $showProgramFilter ?? true;
    $programs = $programs ?? collect();
?>
<div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
    <form method="GET" action="<?php echo e($filterAction); ?>" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 sm:gap-4 items-end">
        <div class="<?php echo e($showProgramFilter ? 'sm:col-span-2 xl:col-span-4' : 'sm:col-span-2 xl:col-span-5'); ?>">
            <label for="scholarship-search" class="block text-sm font-semibold text-slate-700 mb-1">بحث</label>
            <input type="text" name="search" id="scholarship-search" value="<?php echo e(request('search')); ?>"
                   placeholder="الاسم أو البريد أو الهاتف…"
                   class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
        </div>

        <?php if($showProgramFilter): ?>
            <div class="xl:col-span-3">
                <label for="scholarship-program" class="block text-sm font-semibold text-slate-700 mb-1">المنحة</label>
                <select name="program_id" id="scholarship-program"
                        class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">كل المنح</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($program->id); ?>" <?php if((string) request('program_id') === (string) $program->id): echo 'selected'; endif; ?>><?php echo e($program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="<?php echo e($showProgramFilter ? 'xl:col-span-3' : 'xl:col-span-4'); ?>">
            <label for="scholarship-status" class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
            <select name="status" id="scholarship-status"
                    class="w-full h-10 px-3 text-sm border border-slate-200 rounded-xl text-slate-800 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                <option value="">كل الحالات</option>
                <?php $__currentLoopData = \App\Models\ScholarshipRegistration::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if((string) request('status') === (string) $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="sm:col-span-2 xl:col-span-2 flex items-center gap-2">
            <button type="submit"
                    class="h-10 inline-flex items-center justify-center gap-1.5 px-4 text-sm font-semibold bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                <span>تصفية</span>
            </button>
            <?php
                $hasActiveFilters = $showProgramFilter
                    ? request()->anyFilled(['search', 'status', 'program_id'])
                    : request()->anyFilled(['search', 'status']);
            ?>
            <?php if($hasActiveFilters): ?>
                <a href="<?php echo e($filterAction); ?>"
                   class="h-10 w-10 inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shrink-0"
                   title="مسح الفلاتر">
                    <i class="fas fa-times text-sm"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\scholarships\_filters.blade.php ENDPATH**/ ?>