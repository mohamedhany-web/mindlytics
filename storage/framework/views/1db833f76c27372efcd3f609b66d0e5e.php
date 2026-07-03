

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e(__('قوالب الرسائل')); ?></h1>
                <p class="text-gray-600"><?php echo e(__('إدارة قوالب الرسائل المختلفة')); ?></p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="showCreateTemplateModal()" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    <?php echo e(__('قالب جديد')); ?>

                </button>
                <a href="<?php echo e(route('admin.messages.index')); ?>" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    <?php echo e(__('العودة')); ?>

                </a>
            </div>
        </div>
    </div>

    <!-- قوالب محددة مسبقاً -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php
            $predefinedTemplates = [
                [
                    'title' => 'تقرير شهري للطالب',
                    'type' => 'student_report',
                    'icon' => 'fa-chart-line',
                    'color' => 'blue',
                    'content' => "مرحباً {student_name}!\n\nإليك تقريرك الشهري لشهر {month_name}:\n• متوسط درجاتك: {avg_score}%\n• عدد الكورسات: {courses_count}\n\nاستمر في التقدم! 🎓"
                ],
                [
                    'title' => 'نتيجة امتحان',
                    'type' => 'exam_result',
                    'icon' => 'fa-clipboard-check',
                    'color' => 'green',
                    'content' => "عزيزي {student_name}،\n\nنتيجة امتحان {exam_title}:\n• الدرجة: {score}/{total_marks}\n• النسبة: {percentage}%\n• الحالة: {status}\n\nمبروك! 🎉"
                ],
                [
                    'title' => 'تقرير لولي الأمر',
                    'type' => 'parent_report',
                    'icon' => 'fa-user-friends',
                    'color' => 'purple',
                    'content' => "عزيزي {parent_name}،\n\nتقرير شهري عن {student_name} لشهر {month_name}:\n• التقييم العام: {overall_grade}\n• تقدم الكورسات: {courses_progress}\n\nشكراً لثقتكم بنا."
                ]
            ];
        ?>

        <?php $__currentLoopData = $predefinedTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-<?php echo e($template['color']); ?>-100 $template['color'] }}-900 rounded-full">
                        <i class="fas <?php echo e($template['icon']); ?> text-<?php echo e($template['color']); ?>-600 $template['color'] }}-300"></i>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <?php echo e($template['title']); ?>

                        </h3>
                        <p class="text-sm text-gray-500">
                            <?php echo e($template['type']); ?>

                        </p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700 mb-4">
                    <?php echo e(Str::limit($template['content'], 100)); ?>

                </div>
                
                <button onclick="useTemplate('<?php echo e(addslashes($template['content'])); ?>', '<?php echo e($template['type']); ?>', '<?php echo e($template['title']); ?>')"
                        class="w-full bg-<?php echo e($template['color']); ?>-600 hover:bg-<?php echo e($template['color']); ?>-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <?php echo e(__('استخدام هذا القالب')); ?>

                </button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- القوالب المخصصة -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <?php echo e(__('القوالب المخصصة')); ?>

            </h3>
        </div>

        <?php if($templates->count() > 0): ?>
            <div class="divide-y divide-gray-200">
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">
                                        <?php echo e($template->title); ?>

                                    </h4>
                                    <span class="mr-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php echo e($template->is_active ? 'bg-green-100 text-green-800 ': ''bg-gray-100 text-gray-800); ?>">']
                                        <?php echo e($template->is_active ? __('نشط') : __('معطل')); ?>

                                    </span>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-3">
                                    <?php echo e(Str::limit($template->content, 150)); ?>

                                </p>
                                
                                <div class="flex items-center text-xs text-gray-500">
                                    <span><?php echo e(__('النوع')); ?>: <?php echo e($template->type); ?></span>
                                    <span class="mx-2">•</span>
                                    <span><?php echo e(__('بواسطة')); ?>: <?php echo e($template->creator->name); ?></span>
                                    <span class="mx-2">•</span>
                                    <span><?php echo e($template->created_at->format('d/m/Y')); ?></span>
                                </div>
                            </div>
                            
                            <div class="flex space-x-1 space-x-reverse">
                                <button onclick="editTemplate(<?php echo e($template->id); ?>)" 
                                        class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="useTemplate('<?php echo e(addslashes($template->content)); ?>', '<?php echo e($template->type); ?>', '<?php echo e($template->title); ?>')"
                                        class="text-green-600 hover:text-green-800 p-2">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <form action="<?php echo e(route('admin.messages.templates.destroy', $template)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2"
                                            onclick="return confirm('<?php echo e(__('هل تريد حذف هذا القالب؟')); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center">
                <div class="text-gray-400 text-4xl mb-4">
                    <i class="fas fa-file-alt"></i>
                </div>
                <p class="text-gray-600">
                    <?php echo e(__('لا توجد قوالب مخصصة')); ?>

                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- مودال إنشاء قالب -->
<div id="createTemplateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <?php echo e(__('إنشاء قالب جديد')); ?>

                    </h3>
                    <button onclick="hideCreateTemplateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="<?php echo e(route('admin.messages.templates.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('اسم القالب')); ?>

                        </label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900"
                               placeholder="<?php echo e(__('مثال: تقرير_شهري_مخصص')); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('عنوان القالب')); ?>

                        </label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900"
                               placeholder="<?php echo e(__('مثال: تقرير شهري مخصص')); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('نوع القالب')); ?>

                        </label>
                        <select name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                            <option value=""><?php echo e(__('اختر النوع...')); ?></option>
                            <option value="student_report"><?php echo e(__('تقرير طالب')); ?></option>
                            <option value="exam_result"><?php echo e(__('نتيجة امتحان')); ?></option>
                            <option value="general_announcement"><?php echo e(__('إعلان عام')); ?></option>
                            <option value="parent_report"><?php echo e(__('تقرير لولي الأمر')); ?></option>
                            <option value="course_reminder"><?php echo e(__('تذكير بالكورس')); ?></option>
                            <option value="welcome_message"><?php echo e(__('رسالة ترحيب')); ?></option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('محتوى القالب')); ?>

                        </label>
                        <textarea name="content" rows="8" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="<?php echo e(__('اكتب محتوى القالب... يمكنك استخدام المتغيرات مثل {student_name}')); ?>"></textarea>
                        <div class="mt-2 text-xs text-gray-500">
                            <?php echo e(__('المتغيرات المتاحة: {student_name}, {month_name}, {avg_score}, {courses_count}, {date}')); ?>

                        </div>
                    </div>

                    <div class="flex space-x-2 space-x-reverse">
                        <button type="submit" 
                                class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <?php echo e(__('إنشاء القالب')); ?>

                        </button>
                        <button type="button" onclick="hideCreateTemplateModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <?php echo e(__('إلغاء')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function showCreateTemplateModal() {
    document.getElementById('createTemplateModal').classList.remove('hidden');
}

function hideCreateTemplateModal() {
    document.getElementById('createTemplateModal').classList.add('hidden');
}

function useTemplate(content, type, title) {
    // إعادة توجيه لصفحة إنشاء رسالة مع القالب
    const params = new URLSearchParams({
        template_content: content,
        template_type: type,
        template_title: title
    });
    
    window.location.href = '<?php echo e(route("admin.messages.create")); ?>?' + params.toString();
}

function editTemplate(templateId) {
    // يمكن إضافة مودال تحرير القالب
    console.log('Edit template:', templateId);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\messages\templates.blade.php ENDPATH**/ ?>