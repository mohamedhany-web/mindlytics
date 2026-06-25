

<?php $__env->startSection('title', 'إضافة مهمة جديدة - Mindlytics'); ?>
<?php $__env->startSection('header', 'إضافة مهمة جديدة'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s;
    }

    .form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.1);
        border-color: rgba(44, 169, 189, 0.2);
    }

    .form-input {
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: #2CA9BD;
        box-shadow: 0 0 0 4px rgba(44, 169, 189, 0.1);
    }

    .priority-badge {
        font-weight: bold;
    }

    .priority-low { background: linear-gradient(135deg, #10b981, #059669); }
    .priority-medium { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .priority-high { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .priority-urgent { background: linear-gradient(135deg, #ef4444, #dc2626); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر المحسن -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <nav class="text-sm text-[#1F3A56] font-medium mb-3">
                    <a href="<?php echo e(route('instructor.tasks.index')); ?>" class="hover:text-[#2CA9BD] transition-colors">المهام</a>
                    <span class="mx-2">/</span>
                    <span class="text-[#1C2C39] font-bold">إضافة مهمة جديدة</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">إضافة مهمة جديدة</h1>
                <p class="text-sm sm:text-base text-[#1F3A56] font-medium">إنشاء مهمة جديدة لإدارة أعمالك</p>
            </div>
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" 
               class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-5 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </div>
    </div>

    <!-- نموذج الإضافة -->
    <div class="form-card rounded-2xl p-5 sm:p-6">
        <form action="<?php echo e(route('instructor.tasks.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <!-- العنوان -->
            <div>
                <label for="title" class="block text-sm font-bold text-[#1C2C39] mb-2">
                    <i class="fas fa-heading text-[#2CA9BD] ml-1"></i>
                    عنوان المهمة <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="<?php echo e(old('title')); ?>" 
                       required
                       placeholder="أدخل عنوان المهمة..."
                       class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-bold text-[#1C2C39] mb-2">
                    <i class="fas fa-align-right text-[#2CA9BD] ml-1"></i>
                    الوصف
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          placeholder="أدخل وصف المهمة..."
                          class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all"><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الأولوية وتاريخ الاستحقاق -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="priority" class="block text-sm font-bold text-[#1C2C39] mb-2">
                        <i class="fas fa-exclamation-triangle text-[#2CA9BD] ml-1"></i>
                        الأولوية <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority" 
                            required
                            class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                        <option value="low" <?php echo e(old('priority', 'medium') == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                        <option value="medium" <?php echo e(old('priority', 'medium') == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                        <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>عالية</option>
                        <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                    </select>
                    <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-bold text-[#1C2C39] mb-2">
                        <i class="fas fa-calendar-alt text-[#2CA9BD] ml-1"></i>
                        تاريخ الاستحقاق
                    </label>
                    <input type="datetime-local" 
                           name="due_date" 
                           id="due_date" 
                           value="<?php echo e(old('due_date')); ?>"
                           class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                    <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- الربط بالكورس والمحاضرة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="related_course_id" class="block text-sm font-bold text-[#1C2C39] mb-2">
                        <i class="fas fa-book text-[#2CA9BD] ml-1"></i>
                        الكورس (اختياري)
                    </label>
                    <select name="related_course_id" 
                            id="related_course_id"
                            class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                        <option value="">اختر الكورس...</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('related_course_id') == $course->id ? 'selected' : ''); ?>>
                                <?php echo e($course->title); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['related_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="related_lecture_id" class="block text-sm font-bold text-[#1C2C39] mb-2">
                        <i class="fas fa-chalkboard-teacher text-[#2CA9BD] ml-1"></i>
                        المحاضرة (اختياري)
                    </label>
                    <select name="related_lecture_id" 
                            id="related_lecture_id"
                            class="form-input w-full px-4 py-3 border-2 border-[#2CA9BD]/20 rounded-xl bg-white text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20 transition-all">
                        <option value="">اختر المحاضرة...</option>
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lecture->id); ?>" <?php echo e(old('related_lecture_id') == $lecture->id ? 'selected' : ''); ?>>
                                <?php echo e($lecture->title); ?> - <?php echo e($lecture->scheduled_at->format('Y/m/d')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['related_lecture_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- معاينة الأولوية -->
            <div class="p-4 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl border border-[#2CA9BD]/10">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-[#2CA9BD]"></i>
                    <div>
                        <div class="text-sm font-bold text-[#1C2C39] mb-1">معاينة الأولوية:</div>
                        <div id="priority-preview" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-md">
                            <i class="fas fa-minus"></i>
                            متوسطة
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t-2 border-[#2CA9BD]/10">
                <a href="<?php echo e(route('instructor.tasks.index')); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-times"></i>
                    <span>إلغاء</span>
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-save"></i>
                    <span>حفظ المهمة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('related_course_id');
    const lectureSelect = document.getElementById('related_lecture_id');
    const prioritySelect = document.getElementById('priority');
    const priorityPreview = document.getElementById('priority-preview');

    // تحديث المحاضرات عند اختيار الكورس
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        lectureSelect.innerHTML = '<option value="">اختر المحاضرة...</option>';
        
        if (courseId) {
            fetch(`<?php echo e(route('instructor.tasks.lectures')); ?>?course_id=${courseId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                data.forEach(lecture => {
                    const option = document.createElement('option');
                    option.value = lecture.id;
                    option.textContent = `${lecture.title} - ${new Date(lecture.scheduled_at).toLocaleDateString('ar-EG')}`;
                    lectureSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });

    // تحديث معاينة الأولوية
    function updatePriorityPreview() {
        const priority = prioritySelect.value;
        const priorityText = {
            'low': { text: 'منخفضة', icon: 'fa-arrow-down', class: 'priority-low' },
            'medium': { text: 'متوسطة', icon: 'fa-minus', class: 'priority-medium' },
            'high': { text: 'عالية', icon: 'fa-arrow-up', class: 'priority-high' },
            'urgent': { text: 'عاجلة', icon: 'fa-exclamation', class: 'priority-urgent' }
        };
        
        const selected = priorityText[priority];
        priorityPreview.className = `inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-md ${selected.class}`;
        priorityPreview.innerHTML = `<i class="fas ${selected.icon}"></i> ${selected.text}`;
    }

    prioritySelect.addEventListener('change', updatePriorityPreview);
    updatePriorityPreview();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/tasks/create.blade.php ENDPATH**/ ?>