<?php $__env->startSection('title', __('instructor.mind_map_page_title') . ' - ' . $course->title); ?>
<?php $__env->startSection('header', __('instructor.mind_map_page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="hover:text-sky-600"><?php echo e(__('instructor.my_courses')); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="hover:text-sky-600"><?php echo e($course->title); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e(__('instructor.mind_map_page_title')); ?></span>
        </nav>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e(__('instructor.mind_map_page_title')); ?></h1>
        <p class="text-slate-600 mt-2 text-sm leading-relaxed"><?php echo e(__('instructor.mind_map_intro')); ?></p>
        <p class="text-slate-500 text-xs mt-2"><?php echo e(__('instructor.mind_map_roles_hint')); ?></p>
        <?php if($course->mind_map_published && is_array($course->mind_map_steps) && count($course->mind_map_steps) >= 2): ?>
            <a href="<?php echo e(route('public.course.mind-map', $course->id)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-sky-600 hover:text-sky-800">
                <i class="fas fa-external-link-alt"></i>
                <?php echo e(__('instructor.mind_map_open_public')); ?>

            </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->has('steps')): ?>
        <div class="rounded-xl bg-red-50 text-red-800 border border-red-200 px-4 py-3"><?php echo e($errors->first('steps')); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('instructor.courses.mind-map.update', $course)); ?>" class="grid grid-cols-1 xl:grid-cols-12 gap-6 xl:gap-8 items-start">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="xl:col-span-8 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6"
                 x-data="{
                    steps: <?php echo e(\Illuminate\Support\Js::from($steps)); ?>,
                    lblFirst: <?php echo e(\Illuminate\Support\Js::from(__('instructor.mind_map_label_first'))); ?>,
                    lblLast: <?php echo e(\Illuminate\Support\Js::from(__('instructor.mind_map_label_last'))); ?>,
                    lblMiddle: <?php echo e(\Illuminate\Support\Js::from(__('instructor.mind_map_label_middle'))); ?>,
                    addStep() { this.steps.push({ title: '', description: '' }); },
                    removeStep(i) { if (this.steps.length > 1) this.steps.splice(i, 1); },
                    moveStep(i, dir) {
                        const j = i + dir;
                        if (j < 0 || j >= this.steps.length) return;
                        const a = this.steps[i], b = this.steps[j];
                        this.steps[i] = b; this.steps[j] = a;
                    },
                    stepBadge(index) {
                        if (index === 0) return this.lblFirst;
                        if (index === this.steps.length - 1) return this.lblLast;
                        return this.lblMiddle.replace(':n', String(index));
                    }
                 }">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-diagram-project text-sky-500"></i>
                    <?php echo e(__('instructor.mind_map_steps_section')); ?>

                </h2>

                <template x-for="(step, index) in steps" :key="'mind-step-' + index">
                    <div class="mb-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-500" x-text="stepBadge(index)"></span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="moveStep(index, -1)" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100" title="<?php echo e(__('instructor.mind_map_move_up')); ?>"><i class="fas fa-chevron-up text-xs"></i></button>
                                <button type="button" @click="moveStep(index, 1)" class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100" title="<?php echo e(__('instructor.mind_map_move_down')); ?>"><i class="fas fa-chevron-down text-xs"></i></button>
                                <button type="button" @click="removeStep(index)" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="<?php echo e(__('instructor.mind_map_remove_step')); ?>"><i class="fas fa-trash-alt text-xs"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.mind_map_step_title')); ?></label>
                            <input type="text" :name="'steps[' + index + '][title]'" x-model="step.title" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" maxlength="200" placeholder="<?php echo e(__('instructor.mind_map_title_placeholder')); ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.mind_map_step_desc')); ?></label>
                            <textarea :name="'steps[' + index + '][description]'" x-model="step.description" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="<?php echo e(__('instructor.mind_map_desc_placeholder')); ?>"></textarea>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addStep()" class="mb-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-800 text-sm font-semibold hover:bg-slate-200">
                    <i class="fas fa-plus"></i>
                    <?php echo e(__('instructor.mind_map_add_step')); ?>

                </button>
            </div>
        </div>

        <div class="xl:col-span-4 space-y-6 xl:sticky xl:top-24">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 border-t-4 border-t-amber-400">
                <h2 class="text-base font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <?php echo e(__('instructor.mind_map_timetable_label')); ?>

                </h2>
                <p class="text-xs text-slate-600 leading-relaxed mb-3"><?php echo e(__('instructor.mind_map_timetable_help')); ?></p>
                <textarea name="mind_map_timetable" rows="12" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 font-mono leading-relaxed" placeholder="<?php echo e(__('instructor.mind_map_timetable_placeholder')); ?>"><?php echo e(old('mind_map_timetable', $course->mind_map_timetable)); ?></textarea>
                <?php $__errorArgs = ['mind_map_timetable'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-2"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-sky-600"></i>
                    <?php echo e(__('instructor.mind_map_lectures_ref_title')); ?>

                </h3>
                <?php if($lecturesForTimetable->isEmpty()): ?>
                    <p class="text-xs text-slate-500 leading-relaxed"><?php echo e(__('instructor.mind_map_lectures_empty')); ?></p>
                <?php else: ?>
                    <ul class="space-y-2 max-h-72 overflow-y-auto pe-1">
                        <?php $__currentLoopData = $lecturesForTimetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="text-xs rounded-lg bg-white border border-slate-200 px-3 py-2">
                                <div class="font-bold text-slate-800"><?php echo e($lec->title); ?></div>
                                <div class="text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span><i class="far fa-clock ml-1 opacity-70"></i><?php echo e($lec->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?></span>
                                    <?php if($lec->duration_minutes): ?>
                                        <span>· <?php echo e($lec->duration_minutes); ?> <?php echo e(__('instructor.mind_map_minutes_abbr')); ?></span>
                                    <?php endif; ?>
                                    <?php if($lec->status): ?>
                                        <span class="inline-flex px-1.5 py-0.5 rounded bg-slate-100 text-slate-600"><?php echo e($lec->status); ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-sky-50/50 p-4">
                <label class="inline-flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="mind_map_published" value="1" class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500" <?php echo e(old('mind_map_published', $course->mind_map_published) ? 'checked' : ''); ?>>
                    <span>
                        <span class="block font-bold text-slate-800 text-sm"><?php echo e(__('instructor.mind_map_publish_label')); ?></span>
                        <span class="block text-xs text-slate-600 mt-1"><?php echo e(__('instructor.mind_map_publish_help')); ?></span>
                    </span>
                </label>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700"><?php echo e(__('instructor.save_changes')); ?></button>
                <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200"><?php echo e(__('instructor.cancel')); ?></a>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/courses/mind-map.blade.php ENDPATH**/ ?>