<?php $__env->startSection('title', __('instructor.course_details') . ' - ' . $course->title); ?>
<?php $__env->startSection('header', __('instructor.course_details')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    [x-cloak] { display: none !important; }
    .tab-button { transition: all 0.2s; position: relative; }
    .tab-button.active { color: rgb(14 165 233); font-weight: 700; }
    .tab-button.active::after {
        content: ''; position: absolute; bottom: -1px; right: 0; left: 0; height: 2px;
        background: rgb(14 165 233); border-radius: 2px 2px 0 0;
    }
    .content-card {
        background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .content-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: rgb(148 163 184); }
    .stats-mini-card {
        background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .stats-mini-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: rgb(148 163 184); }
    .item-row { transition: background 0.2s; }
    .item-row:hover { background: rgb(241 245 249); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ activeTab: 'overview' }">
    <div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="<?php echo e(route('instructor.courses.index')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('instructor.my_courses')); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 font-semibold truncate block sm:inline"><?php echo e($course->title); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mb-3"><?php echo e($course->title); ?></h1>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($course->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'); ?>">
                        <i class="fas <?php echo e($course->is_active ? 'fa-check-circle' : 'fa-ban'); ?>"></i>
                        <?php echo e($course->is_active ? __('instructor.active_status') : __('instructor.inactive_status')); ?>

                    </span>
                    <?php if($course->is_featured): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800">
                            <i class="fas fa-star"></i> <?php echo e(__('instructor.featured')); ?>

                        </span>
                    <?php endif; ?>
                    <?php if($course->academicYear): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                            <i class="fas fa-graduation-cap"></i> <?php echo e($course->academicYear->name); ?>

                        </span>
                    <?php endif; ?>
                    <?php if($course->academicSubject): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-100 text-violet-700">
                            <i class="fas fa-book"></i> <?php echo e($course->academicSubject->name); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="<?php echo e(route('instructor.courses.mind-map.edit', $course)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-50 to-sky-50 border border-emerald-200 text-emerald-800 hover:border-emerald-300 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-route"></i>
                    <span><?php echo e(__('instructor.mind_map_short')); ?></span>
                </a>
                <a href="<?php echo e(route('instructor.courses.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span><?php echo e(__('instructor.back')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-chalkboard-teacher text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_lectures']); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1"><?php echo e(__('instructor.lecture_single')); ?></div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-clipboard-check text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_exams']); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1"><?php echo e(__('instructor.exam_single')); ?></div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-tasks text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_assignments']); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1"><?php echo e(__('instructor.assignment_single')); ?></div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-user-graduate text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_students']); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1"><?php echo e(__('instructor.student_single')); ?></div>
        </div>
        <div class="stats-mini-card rounded-xl p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-users text-sm"></i>
            </div>
            <div class="text-xl font-bold text-slate-800"><?php echo e($stats['total_groups']); ?></div>
            <div class="text-xs text-slate-600 font-medium mt-1"><?php echo e(__('instructor.group_single')); ?></div>
        </div>
    </div>

    <!-- التبويبات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/50">
            <div class="flex flex-wrap items-center gap-1 sm:gap-2 px-3 sm:px-4 py-2 overflow-x-auto">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-chart-line ml-2"></i> <?php echo e(__('instructor.overview')); ?>

                </button>
                <button @click="activeTab = 'lectures'" 
                        :class="activeTab === 'lectures' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white relative">
                    <i class="fas fa-chalkboard-teacher ml-2"></i> <?php echo e(__('instructor.lectures')); ?>

                    <?php if($stats['upcoming_lectures'] > 0): ?>
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center font-bold"><?php echo e($stats['upcoming_lectures']); ?></span>
                    <?php endif; ?>
                </button>
                <button @click="activeTab = 'exams'" 
                        :class="activeTab === 'exams' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-clipboard-check ml-2"></i> <?php echo e(__('instructor.exams')); ?>

                </button>
                <button @click="activeTab = 'assignments'" 
                        :class="activeTab === 'assignments' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white relative">
                    <i class="fas fa-tasks ml-2"></i> <?php echo e(__('instructor.assignments')); ?>

                    <?php if($stats['pending_submissions'] > 0): ?>
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center font-bold"><?php echo e($stats['pending_submissions']); ?></span>
                    <?php endif; ?>
                </button>
                <button @click="activeTab = 'students'" 
                        :class="activeTab === 'students' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-user-graduate ml-2"></i> <?php echo e(__('instructor.students')); ?>

                </button>
                <button @click="activeTab = 'groups'" 
                        :class="activeTab === 'groups' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-users ml-2"></i> <?php echo e(__('instructor.groups')); ?>

                </button>
                <button @click="activeTab = 'attendance'" 
                        :class="activeTab === 'attendance' ? 'tab-button active' : 'tab-button'"
                        class="px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-sky-600 transition-colors rounded-lg hover:bg-white">
                    <i class="fas fa-clipboard-list ml-2"></i> <?php echo e(__('instructor.attendance')); ?>

                </button>
            </div>
        </div>

        <!-- محتوى التبويبات -->
        <div class="p-4 sm:p-6">
            <!-- تبويب نظرة عامة -->
            <div x-show="activeTab === 'overview'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- معلومات الكورس -->
                    <div class="lg:col-span-2 space-y-6">
                        <?php if($course->thumbnail): ?>
                            <div class="content-card rounded-xl overflow-hidden">
                                <img src="<?php echo e(asset('storage/' . $course->thumbnail)); ?>" alt="<?php echo e($course->title); ?>" 
                                     class="w-full h-64 object-cover">
                            </div>
                            <?php endif; ?>

                        <div class="content-card rounded-xl p-5 sm:p-6">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle text-sky-500"></i>
                                <?php echo e(__('instructor.course_info')); ?>

                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.title')); ?></label>
                                        <div class="font-black text-slate-800 text-base"><?php echo e($course->title); ?></div>
                            </div>
                            <?php if($course->instructor): ?>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.instructor_label')); ?></label>
                                        <div class="text-slate-800 font-bold"><?php echo e($course->instructor->name); ?></div>
                            </div>
                            <?php endif; ?>
                                    <?php if($course->level): ?>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.level')); ?></label>
                                        <div class="text-slate-800 font-bold">
                                    <?php if($course->level == 'beginner'): ?> <?php echo e(__('instructor.beginner')); ?>

                                    <?php elseif($course->level == 'intermediate'): ?> <?php echo e(__('instructor.intermediate')); ?>

                                    <?php elseif($course->level == 'advanced'): ?> <?php echo e(__('instructor.advanced')); ?>

                                    <?php else: ?> <?php echo e(__('instructor.level_unspecified')); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.price')); ?></label>
                                        <div class="text-slate-800 font-black text-lg">
                                            <?php if($course->price && $course->price > 0): ?>
                                                <?php echo e(number_format($course->price, 2)); ?> ج.م
                                            <?php else: ?>
                                                <span class="text-green-600"><?php echo e(__('instructor.free')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.course_duration')); ?></label>
                                        <div class="text-slate-800 font-bold">
                                    <?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('instructor.hour')); ?>

                                    <?php if($course->duration_minutes && $course->duration_minutes > 0): ?>
                                        <?php echo e(__('instructor.and')); ?> <?php echo e($course->duration_minutes); ?> <?php echo e(__('instructor.minutes')); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($course->programming_language): ?>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wide"><?php echo e(__('instructor.programming_language')); ?></label>
                                        <div class="text-slate-800 font-bold"><?php echo e($course->programming_language); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($course->description): ?>
                                <div class="mt-4 pt-4 border-t-2 border-slate-200">
                                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide"><?php echo e(__('instructor.description')); ?></label>
                                    <div class="text-slate-800 font-medium bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <?php echo e($course->description); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($course->objectives): ?>
                                <div class="mt-4 pt-4 border-t-2 border-slate-200">
                                    <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide"><?php echo e(__('instructor.objectives')); ?></label>
                                    <div class="text-slate-800 font-medium bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <?php echo e($course->objectives); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                        </div>
                    </div>

                    <!-- الإجراءات السريعة -->
                    <div class="space-y-4">
                        <div class="content-card rounded-xl p-5">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-bolt text-sky-600"></i>
                                <?php echo e(__('instructor.quick_actions')); ?>

                            </h3>
                            <div class="space-y-2">
                                <a href="<?php echo e(route('instructor.lectures.create', ['course_id' => $course->id])); ?>" 
                                   class="flex items-center gap-3 p-3 bg-sky-50 hover:bg-sky-100 rounded-xl border border-sky-200 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-video text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.add_lecture')); ?></span>
                                </a>
                                <a href="<?php echo e(route('instructor.exams.create', ['course_id' => $course->id])); ?>" 
                                   class="flex items-center gap-3 p-3 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 hover:from-indigo-500/20 hover:to-purple-500/20 rounded-xl border border-indigo-500/20 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-clipboard-check text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.create_exam')); ?></span>
                                </a>
                                <a href="<?php echo e(route('instructor.assignments.create', ['course_id' => $course->id])); ?>" 
                                   class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-tasks text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.create_assignment')); ?></span>
                                </a>
                                <a href="<?php echo e(route('instructor.groups.create', ['course_id' => $course->id])); ?>" 
                                   class="flex items-center gap-3 p-3 bg-gradient-to-r from-green-500/10 to-emerald-500/10 hover:from-green-500/20 hover:to-emerald-500/20 rounded-xl border border-green-500/20 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                                        <i class="fas fa-users text-sm"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo e(__('instructor.create_group')); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- إحصائيات إضافية -->
                        <div class="content-card rounded-xl p-5">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-sky-600"></i>
                                <?php echo e(__('instructor.statistics')); ?>

                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold"><?php echo e(__('instructor.upcoming_lectures')); ?></span>
                                    <span class="font-black text-slate-800"><?php echo e($stats['upcoming_lectures']); ?></span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold"><?php echo e(__('instructor.active_exams')); ?></span>
                                    <span class="font-black text-slate-800"><?php echo e($stats['active_exams']); ?></span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-amber-50 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold"><?php echo e(__('instructor.pending_submissions')); ?></span>
                                    <span class="font-black text-slate-800"><?php echo e($stats['pending_submissions']); ?></span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-gradient-to-r from-blue-500/5 to-cyan-500/5 rounded-lg">
                                    <span class="text-xs text-slate-600 font-semibold"><?php echo e(__('instructor.attendance_records')); ?></span>
                                    <span class="font-black text-slate-800"><?php echo e($stats['total_attendance_records']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تبويب المحاضرات -->
            <div x-show="activeTab === 'lectures'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-600"></i>
                        <?php echo e(__('instructor.lectures')); ?> (<?php echo e($lectures->total()); ?>)
                    </h3>
                    <a href="<?php echo e(route('instructor.lectures.create', ['course_id' => $course->id])); ?>" 
                       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span><?php echo e(__('instructor.add_lecture')); ?></span>
                    </a>
                </div>
                <?php if($lectures->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item-row flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-sky-500 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1"><?php echo e($lecture->title); ?></div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                                        <?php echo e($lecture->scheduled_at->format('Y/m/d H:i')); ?>

                                        <?php if($lecture->lesson): ?>
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-book text-purple-600 ml-1"></i>
                                            <?php echo e($lecture->lesson->title); ?>

                                    <?php endif; ?>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    <?php if($lecture->status == 'scheduled'): ?> bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                    <?php elseif($lecture->status == 'in_progress'): ?> bg-amber-500 text-white
                                    <?php elseif($lecture->status == 'completed'): ?> bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    <?php else: ?> bg-gradient-to-r from-red-500 to-rose-600 text-white
                                    <?php endif; ?>">
                                    <?php if($lecture->status == 'scheduled'): ?> <?php echo e(__('instructor.scheduled_lecture')); ?>

                                    <?php elseif($lecture->status == 'in_progress'): ?> <?php echo e(__('instructor.in_progress')); ?>

                                    <?php elseif($lecture->status == 'completed'): ?> <?php echo e(__('instructor.completed')); ?>

                                    <?php else: ?> <?php echo e(__('instructor.cancelled_lecture')); ?>

                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('instructor.lectures.show', $lecture)); ?>" 
                                   class="px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="<?php echo e(route('instructor.lectures.edit', $lecture)); ?>" 
                                   class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-4">
                        <?php echo e($lectures->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br bg-sky-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-4xl text-sky-600"></i>
                </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_lectures')); ?></p>
                        <a href="<?php echo e(route('instructor.lectures.create', ['course_id' => $course->id])); ?>" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            <?php echo e(__('instructor.add_new_lecture')); ?>

                        </a>
            </div>
            <?php endif; ?>
        </div>

            <!-- تبويب الاختبارات -->
            <div x-show="activeTab === 'exams'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-indigo-600"></i>
                        <?php echo e(__('instructor.exams')); ?> (<?php echo e($exams->total()); ?>)
                    </h3>
                    <a href="<?php echo e(route('instructor.exams.create')); ?>" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span><?php echo e(__('instructor.create_exam')); ?></span>
                    </a>
                </div>
                <?php if($exams->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item-row flex items-center justify-between p-4 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-xl border border-indigo-500/10">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1"><?php echo e($exam->title); ?></div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-clock text-indigo-600 ml-1"></i>
                                        <?php echo e($exam->duration_minutes); ?> <?php echo e(__('instructor.minutes')); ?>

                                        <span class="mr-2">-</span>
                                        <i class="fas fa-question-circle text-purple-600 ml-1"></i>
                                        <?php echo e($exam->questions_count); ?> <?php echo e(__('instructor.question_single')); ?>

                                        <?php if($exam->lesson): ?>
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-book text-blue-600 ml-1"></i>
                                            <?php echo e($exam->lesson->title); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md <?php echo e($exam->is_active ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-amber-500 text-white'); ?>">
                                    <i class="fas <?php echo e($exam->is_active ? 'fa-check-circle' : 'fa-ban'); ?>"></i>
                                    <?php echo e($exam->is_active ? __('instructor.active_status') : __('instructor.inactive_status')); ?>

                                </span>
                            </div>
                            <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>" 
                               class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-4">
                        <?php echo e($exams->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-4xl text-indigo-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_exams')); ?></p>
                        <a href="<?php echo e(route('instructor.exams.create')); ?>" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            <?php echo e(__('instructor.create_new_exam')); ?>

                        </a>
                    </div>
                    <?php endif; ?>
            </div>

            <!-- تبويب الواجبات -->
            <div x-show="activeTab === 'assignments'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tasks text-amber-500"></i>
                        <?php echo e(__('instructor.assignments')); ?> (<?php echo e($assignments->total()); ?>)
                    </h3>
                    <a href="<?php echo e(route('instructor.assignments.create')); ?>" 
                       class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span><?php echo e(__('instructor.create_assignment')); ?></span>
                    </a>
                </div>
                <?php if($assignments->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item-row flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1"><?php echo e($assignment->title); ?></div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-file-upload text-amber-500 ml-1"></i>
                                        <?php echo e($assignment->submissions_count); ?> <?php echo e(__('instructor.submission_single')); ?>

                                        <?php if($assignment->due_date): ?>
                                            <span class="mr-2">-</span>
                                            <i class="fas fa-calendar-alt text-red-600 ml-1"></i>
                                            <?php echo e($assignment->due_date->format('Y/m/d')); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md
                                    <?php if($assignment->status == 'published'): ?> bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                    <?php elseif($assignment->status == 'draft'): ?> bg-amber-500 text-white
                                    <?php else: ?> bg-gradient-to-r from-gray-500 to-gray-600 text-white
                                    <?php endif; ?>">
                                    <?php if($assignment->status == 'published'): ?> <?php echo e(__('instructor.published')); ?>

                                    <?php elseif($assignment->status == 'draft'): ?> <?php echo e(__('instructor.draft')); ?>

                                    <?php else: ?> <?php echo e(__('instructor.archived')); ?>

                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('instructor.assignments.show', $assignment)); ?>" 
                                   class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="<?php echo e(route('instructor.assignments.submissions', $assignment)); ?>" 
                                   class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-list text-xs"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-4">
                        <?php echo e($assignments->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tasks text-4xl text-amber-500"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_assignments')); ?></p>
                        <a href="<?php echo e(route('instructor.assignments.create')); ?>" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            <?php echo e(__('instructor.create_assignment_modal_title')); ?>

                        </a>
                    </div>
                    <?php endif; ?>
    </div>

            <!-- تبويب الطلاب -->
            <div x-show="activeTab === 'students'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-green-600"></i>
                        <?php echo e(__('instructor.enrolled_students')); ?> (<?php echo e($enrollments->total()); ?>)
                    </h3>
        </div>
            <?php if($enrollments->count() > 0): ?>
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-sky-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('instructor.name')); ?></th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('instructor.email')); ?></th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('instructor.phone')); ?></th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('instructor.registration_date')); ?></th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('common.status')); ?></th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-800 uppercase tracking-wider"><?php echo e(__('instructor.actions')); ?></th>
                            </tr>
                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-sky-500 flex items-center justify-center text-white font-black shadow-md">
                                                <?php echo e(mb_substr($enrollment->user->name ?? __('instructor.student_single'), 0, 1)); ?>

                                            </div>
                                            <div class="text-sm font-black text-slate-800">
                                        <?php echo e($enrollment->user->name ?? __('instructor.not_specified')); ?>

                                            </div>
                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        <?php echo e($enrollment->user->email ?? __('instructor.not_specified')); ?>

                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        <?php echo e($enrollment->user->phone ?? __('instructor.not_specified')); ?>

                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 font-medium">
                                        <?php echo e($enrollment->created_at->format('Y/m/d')); ?>

                                    </div>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-md">
                                            <i class="fas fa-check-circle"></i>
                                        <?php echo e($enrollment->status ?? __('instructor.active_status')); ?>

                                    </span>
                                </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="<?php echo e(route('profile')); ?>" 
                                           class="px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-bold text-xs transition-all duration-300 transform hover:scale-105">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                    <div class="mt-4">
                    <?php echo e($enrollments->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-graduate text-4xl text-green-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_enrolled_students')); ?></p>
                        <p class="text-sm text-slate-600 font-medium"><?php echo e(__('instructor.no_enrolled_description')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب المجموعات -->
            <div x-show="activeTab === 'groups'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-users text-green-600"></i>
                        <?php echo e(__('instructor.groups')); ?> (<?php echo e($groups->count()); ?>)
                    </h3>
                    <a href="<?php echo e(route('instructor.groups.create')); ?>" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        <span><?php echo e(__('instructor.create_group')); ?></span>
                    </a>
                </div>
                <?php if($groups->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="content-card rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-black text-slate-800"><?php echo e($group->name); ?></h4>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-md <?php echo e($group->status == 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-amber-500 text-white'); ?>">
                                    <?php echo e($group->status == 'active' ? __('instructor.active') : __('instructor.inactive')); ?>

                                </span>
                            </div>
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-green-600"></i>
                                    <span class="text-slate-600 font-medium"><?php echo e(__('instructor.members_label')); ?>:</span>
                                    <span class="text-slate-800 font-bold"><?php echo e($group->members_count ?? 0); ?> / <?php echo e($group->max_members ?? __('instructor.unlimited')); ?></span>
                                </div>
                            </div>
                            <a href="<?php echo e(route('instructor.groups.show', $group)); ?>" 
                               class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye"></i>
                                <span><?php echo e(__('instructor.view_details')); ?></span>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-4xl text-green-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_groups')); ?></p>
                        <a href="<?php echo e(route('instructor.groups.create')); ?>" 
                           class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus"></i>
                            <?php echo e(__('instructor.create_new_group')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب الحضور -->
            <div x-show="activeTab === 'attendance'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        <?php echo e(__('instructor.attendance_absence')); ?>

                    </h3>
                    <a href="<?php echo e(route('instructor.attendance.index', ['course_id' => $course->id])); ?>" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-cyan-600 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-eye"></i>
                        <span><?php echo e(__('instructor.view_all_records')); ?></span>
                    </a>
                </div>
                <?php
                    $courseLectures = \App\Models\Lecture::where('course_id', $course->id)
                        ->where('status', 'completed')
                        ->withCount('attendanceRecords')
                        ->orderBy('scheduled_at', 'desc')
                        ->take(10)
                        ->get();
                ?>
                <?php if($courseLectures->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $courseLectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item-row flex items-center justify-between p-4 bg-gradient-to-r from-blue-500/5 to-cyan-500/5 rounded-xl border border-blue-500/10">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-black text-slate-800 mb-1"><?php echo e($lecture->title); ?></div>
                                    <div class="text-sm text-slate-600 font-medium">
                                        <i class="fas fa-calendar-alt text-blue-600 ml-1"></i>
                                        <?php echo e($lecture->scheduled_at->format('Y/m/d H:i')); ?>

                                        <span class="mr-2">-</span>
                                        <i class="fas fa-users text-green-600 ml-1"></i>
                                        <?php echo e($lecture->attendance_records_count); ?> <?php echo e(__('instructor.attendance_record_single')); ?>

                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo e(route('instructor.attendance.lecture', $lecture)); ?>" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-eye text-xs"></i>
                                <span class="mr-2"><?php echo e(__('common.view')); ?></span>
                            </a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
                        </div>
                        <p class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('instructor.no_attendance_records')); ?></p>
                        <p class="text-sm text-slate-600 font-medium"><?php echo e(__('instructor.no_attendance_description')); ?></p>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\courses\show.blade.php ENDPATH**/ ?>