

<?php $__env->startSection('title', 'تفاصيل السؤال'); ?>
<?php $__env->startSection('header', 'تفاصيل السؤال'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.question-bank.index')); ?>" class="hover:text-primary-600">بنك الأسئلة</a>
                <span class="mx-2">/</span>
                <span>تفاصيل السؤال</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.question-bank.edit', $question)); ?>" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <form action="<?php echo e(route('admin.question-bank.duplicate', $question)); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-copy ml-2"></i>
                    نسخ
                </button>
            </form>
            <form action="<?php echo e(route('admin.question-bank.destroy', $question)); ?>" method="POST" class="inline"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash ml-2"></i>
                    حذف
                </button>
            </form>
            <a href="<?php echo e(route('admin.question-bank.index')); ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- محتوى السؤال -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- السؤال الرئيسي -->
        <div class="xl:col-span-3 space-y-6">
            <!-- تفاصيل السؤال -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">نص السؤال</h3>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($question->type == 'multiple_choice'): ?> bg-blue-100 text-blue-800
                                <?php elseif($question->type == 'true_false'): ?> bg-green-100 text-green-800
                                <?php elseif($question->type == 'fill_blank'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($question->type == 'essay'): ?> bg-purple-100 text-purple-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php echo e($question->type_text); ?>

                            </span>
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($question->difficulty_level == 'easy'): ?> bg-green-100 text-green-800
                                <?php elseif($question->difficulty_level == 'medium'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-red-100 text-red-800
                                <?php endif; ?>">
                                <?php echo e($question->difficulty_text); ?>

                            </span>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php echo e($question->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800); ?>">']
                                <?php echo e($question->is_active ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-lg text-gray-900 mb-4 leading-relaxed">
                        <?php echo e($question->question); ?>

                    </div>

                    <!-- الوسائط المرفقة -->
                    <?php if($question->hasMedia()): ?>
                        <div class="mt-4 space-y-4">
                            <?php if($question->image_url): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">الصورة المرفقة:</h4>
                                    <div class="relative group">
                                        <img src="<?php echo e($question->getImageUrl()); ?>" 
                                             alt="صورة السؤال" 
                                             loading="lazy"
                                             class="max-w-full h-auto rounded-lg shadow-lg border border-gray-200 cursor-pointer transition-transform duration-300 hover:scale-105"
                                             onclick="openImageModal(this.src)">
                                        
                                        <!-- زر التكبير -->
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <button onclick="openImageModal('<?php echo e($question->getImageUrl()); ?>')"
                                                    class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition-colors">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- معلومات الصورة -->
                                        <div class="mt-2 text-xs text-gray-500">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            انقر على الصورة للتكبير
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($question->audio_url): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">الملف الصوتي:</h4>
                                    <audio controls class="w-full max-w-sm">
                                        <source src="<?php echo e($question->audio_url); ?>" type="audio/mpeg">
                                        المتصفح لا يدعم تشغيل الملفات الصوتية
                                    </audio>
                                </div>
                            <?php endif; ?>

                            <?php if($question->video_url): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">الفيديو:</h4>
                                    <?php if(str_contains($question->video_url, 'youtube.com') || str_contains($question->video_url, 'youtu.be')): ?>
                                        <?php
                                            $videoId = null;
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $question->video_url, $matches)) {
                                                $videoId = $matches[1];
                                            }
                                        ?>
                                        <?php if($videoId): ?>
                                            <div class="relative rounded-lg overflow-hidden shadow-lg">
                                                <iframe width="560" height="315" 
                                                        src="https://www.youtube.com/embed/<?php echo e($videoId); ?>" 
                                                        title="فيديو السؤال"
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen
                                                        loading="lazy"
                                                        class="w-full max-w-lg aspect-video">
                                                </iframe>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <video controls preload="metadata" class="w-full max-w-lg rounded-lg shadow-lg">
                                            <source src="<?php echo e($question->video_url); ?>" type="video/mp4">
                                            <p class="text-red-500">المتصفح لا يدعم تشغيل الفيديو</p>
                                        </video>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- الخيارات والإجابات -->
            <?php if(in_array($question->type, ['multiple_choice', 'true_false'])): ?>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">الخيارات والإجابات</h3>
                    </div>
                    <div class="p-6">
                        <?php if($question->options && is_array($question->options)): ?>
                            <div class="space-y-3">
                                <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center p-3 rounded-lg border
                                        <?php if($question->correct_answer && in_array((string)$index, $question->correct_answer)): ?>
                                            border-green-300 bg-green-50
                                        <?php else: ?>
                                            border-gray-200 bg-gray-50
                                        <?php endif; ?>">
                                        
                                        <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                            <?php if($question->correct_answer && in_array((string)$index, $question->correct_answer)): ?>
                                                bg-green-100 text-green-800
                                            <?php else: ?>
                                                bg-gray-200 text-gray-700
                                            <?php endif; ?>">
                                            <?php echo e(chr(65 + $index)); ?>

                                        </span>
                                        
                                        <span class="mr-3 flex-1 text-gray-900"><?php echo e($option); ?></span>
                                        
                                        <?php if($question->correct_answer && in_array((string)$index, $question->correct_answer)): ?>
                                            <i class="fas fa-check text-green-600"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif($question->type == 'fill_blank'): ?>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">الإجابة الصحيحة</h3>
                    </div>
                    <div class="p-6">
                        <?php if($question->correct_answer): ?>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <?php if(is_array($question->correct_answer)): ?>
                                    <?php $__currentLoopData = $question->correct_answer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm mr-2 mb-2">
                                            <?php echo e($answer); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-green-800 font-medium"><?php echo e($question->correct_answer); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- الشرح -->
            <?php if($question->explanation): ?>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">شرح الإجابة</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-blue-800">
                                <?php echo e($question->explanation); ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات سريعة -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات السؤال</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">النقاط</div>
                        <div class="text-lg font-semibold text-gray-900"><?php echo e($question->points); ?></div>
                    </div>

                    <?php if($question->time_limit): ?>
                        <div>
                            <div class="text-sm text-gray-500">الوقت المحدد</div>
                            <div class="text-lg font-semibold text-gray-900"><?php echo e($question->time_limit); ?> ثانية</div>
                        </div>
                    <?php endif; ?>

                    <?php if($question->category): ?>
                        <div>
                            <div class="text-sm text-gray-500">التصنيف</div>
                            <div class="text-sm font-medium text-gray-900"><?php echo e($question->category->name); ?></div>
                            <?php if($question->category->academicYear): ?>
                                <div class="text-xs text-gray-500"><?php echo e($question->category->academicYear->name); ?></div>
                            <?php endif; ?>
                            <?php if($question->category->academicSubject): ?>
                                <div class="text-xs text-gray-500"><?php echo e($question->category->academicSubject->name); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <div class="text-sm text-gray-500">تاريخ الإنشاء</div>
                        <div class="text-sm font-medium text-gray-900"><?php echo e($question->created_at->format('Y-m-d')); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($question->created_at->diffForHumans()); ?></div>
                    </div>

                    <?php if($question->updated_at != $question->created_at): ?>
                        <div>
                            <div class="text-sm text-gray-500">آخر تحديث</div>
                            <div class="text-sm font-medium text-gray-900"><?php echo e($question->updated_at->format('Y-m-d')); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($question->updated_at->diffForHumans()); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- التاجز -->
            <?php if($question->tags): ?>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">العلامات</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $question->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-tag ml-1"></i>
                                    <?php echo e($tag); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- إحصائيات الاستخدام -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">إحصائيات</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-600"><?php echo e($question->examQuestions()->count()); ?></div>
                        <div class="text-sm text-gray-500">مرات الاستخدام في الامتحانات</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لعرض الصور بحجم كامل -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition-colors z-10">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="صورة مكبرة" class="max-w-full max-h-full rounded-lg shadow-2xl">
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // منع التمرير في الخلفية
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // السماح بالتمرير مرة أخرى
    document.body.style.overflow = 'auto';
}

// إغلاق الـ modal عند النقر خارج الصورة
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// إغلاق الـ modal بالضغط على Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\question-bank\show.blade.php ENDPATH**/ ?>