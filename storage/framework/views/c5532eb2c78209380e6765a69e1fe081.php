<?php $__env->startSection('title', 'دروس الكورس البرمجي'); ?>
<?php $__env->startSection('header', 'دروس الكورس: ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.advanced-courses.show', $course)); ?>" class="hover:text-primary-600"><?php echo e($course->title); ?></a>
                <span class="mx-2">/</span>
                <span>الدروس</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.courses.lessons.create', $course)); ?>" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة درس جديد
            </a>
            <a href="<?php echo e(route('admin.advanced-courses.show', $course)); ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($course->title); ?></h3>
                <p class="text-sm text-gray-500 mt-1">
                    <?php echo e($course->academicYear->name ?? 'غير محدد'); ?> - <?php echo e($course->academicSubject->name ?? 'غير محدد'); ?>

                    <span class="text-sky-600 font-medium">| كورس برمجي</span>
                </p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600"><?php echo e($lessons->count()); ?></div>
                <div class="text-sm text-gray-500">درس</div>
            </div>
        </div>
    </div>

    <!-- قائمة الدروس -->
    <?php if($lessons->count() > 0): ?>
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900">قائمة الدروس</h4>
            </div>
            
            <div class="divide-y divide-gray-200" id="lessons-container">
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors" data-lesson-id="<?php echo e($lesson->id); ?>">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- أيقونة الترتيب -->
                                <div class="cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- أيقونة نوع الدرس -->
                                <div class="flex-shrink-0">
                                    <?php
                                        $iconBg = $lesson->type == 'video' ? 'bg-blue-100' : ($lesson->type == 'document' ? 'bg-green-100' : ($lesson->type == 'quiz' ? 'bg-yellow-100' : 'bg-purple-100'));
                                        $iconFa = $lesson->type == 'video' ? 'fa-play text-blue-600' : ($lesson->type == 'document' ? 'fa-file-alt text-green-600' : ($lesson->type == 'quiz' ? 'fa-question-circle text-yellow-600' : 'fa-tasks text-purple-600'));
                                    ?>
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center <?php echo e($iconBg); ?>">
                                        <i class="fas <?php echo e($iconFa); ?>"></i>
                                    </div>
                                </div>

                                <!-- معلومات الدرس -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3">
                                        <h5 class="text-lg font-medium text-gray-900"><?php echo e($lesson->title); ?></h5>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo e($lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo e($lesson->is_active ? 'نشط' : 'غير نشط'); ?>

                                        </span>
                                        <?php if($lesson->is_free): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                مجاني
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if($lesson->description): ?>
                                        <p class="text-sm text-gray-500 mt-1"><?php echo e(Str::limit($lesson->description, 100)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center gap-6 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-clock ml-1"></i>
                                            <?php echo e($lesson->duration_minutes ?? 0); ?> دقيقة
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-sort-numeric-up ml-1"></i>
                                            ترتيب: <?php echo e($lesson->order); ?>

                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-tag ml-1"></i>
                                            <?php if($lesson->type == 'video'): ?> فيديو
                                            <?php elseif($lesson->type == 'document'): ?> مستند
                                            <?php elseif($lesson->type == 'quiz'): ?> كويز
                                            <?php else: ?> واجب
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button onclick="toggleLessonStatus(<?php echo e($lesson->id); ?>)" 
                                        class="p-2 <?php echo e($lesson->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'); ?> transition-colors"
                                        title="<?php echo e($lesson->is_active ? 'إيقاف' : 'تفعيل'); ?>">
                                    <i class="fas <?php echo e($lesson->is_active ? 'fa-pause' : 'fa-play'); ?>"></i>
                                </button>
                                <a href="<?php echo e(route('admin.courses.lessons.show', [$course, $lesson])); ?>" 
                                   class="p-2 text-blue-600 hover:text-blue-800 transition-colors" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.courses.lessons.edit', [$course, $lesson])); ?>" 
                                   class="p-2 text-indigo-600 hover:text-indigo-800 transition-colors" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('admin.courses.lessons.destroy', [$course, $lesson])); ?>" 
                                      class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 text-red-600 hover:text-red-800 transition-colors" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-play-circle text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد دروس</h3>
            <p class="text-gray-500 mb-6">ابدأ بإضافة الدروس لهذا الكورس لتنظيم المحتوى التعليمي</p>
            <a href="<?php echo e(route('admin.courses.lessons.create', $course)); ?>" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة أول درس
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// إعداد السحب والإفلات لإعادة الترتيب
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('lessons-container');
    if (container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'bg-blue-50',
            chosenClass: 'bg-blue-100',
            onEnd: function(evt) {
                const lessons = [];
                container.querySelectorAll('[data-lesson-id]').forEach((element, index) => {
                    lessons.push({
                        id: element.dataset.lessonId,
                        order: index + 1
                    });
                });
                
                // إرسال الترتيب الجديد للخادم
                fetch(`<?php echo e(route('admin.courses.lessons.reorder', $course)); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ lessons: lessons })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // يمكن إضافة إشعار نجاح هنا
                        console.log('تم حفظ الترتيب الجديد');
                    } else {
                        alert('حدث خطأ في حفظ الترتيب');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ في حفظ الترتيب');
                    location.reload();
                });
            }
        });
    }
});

function toggleLessonStatus(lessonId) {
    if (confirm('هل تريد تغيير حالة هذا الدرس؟')) {
        fetch(`<?php echo e(route('admin.courses.lessons.index', $course)); ?>/${lessonId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الدرس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الدرس');
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/course-lessons/index.blade.php ENDPATH**/ ?>