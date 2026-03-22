<?php $__env->startSection('title', 'رفع مشروع - البورتفوليو'); ?>
<?php $__env->startSection('header', 'رفع مشروع جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto">
    
    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 border border-[#2CA9BD]/20">
        <h3 class="text-sm font-bold text-[#1F3A56] mb-3 flex items-center gap-2">
            <i class="fas fa-lightbulb text-[#2CA9BD]"></i>
            أفكار لمشاريعك
        </h3>
        <p class="text-xs text-gray-600 mb-2">مثلاً: تطبيق ويب، API، لعبة، مكتبة، سكربت أتمتة، واجهة تصميم، تطبيق موبايل، أداة سطر أوامر...</p>
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = ['تطبيق ويب', 'API', 'مكتبة برمجية', 'لعبة', 'سكربت أتمتة', 'تصميم واجهة', 'تطبيق موبايل', 'أداة CLI', 'مشروع full-stack']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-white/80 text-[#1F3A56] border border-[#2CA9BD]/30"><?php echo e($idea); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 mb-6">
            <ul class="list-disc list-inside text-red-800 text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] px-6 py-4">
            <h2 class="text-lg font-black text-white flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                إضافة مشروع للبورتفوليو
            </h2>
            <p class="text-white/90 text-sm mt-1">أضف مشروعك بعد إتمام كورس أو مسار. سيُراجع من المدرب ثم يُنشر في المعرض. يكفي اختيار المسار التعليمي أو الكورس (أحدهما أو كلاهما).</p>
        </div>

        <form action="<?php echo e(route('student.portfolio.store')); ?>" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            <?php echo csrf_field(); ?>

            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                <div class="lg:col-span-5">
                    <label class="block text-sm font-bold text-gray-900 mb-2">عنوان المشروع <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                           placeholder="مثال: نظام إدارة مهام بلارافيل"
                           class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
                </div>
                <div class="lg:col-span-7">
                    <label class="block text-sm font-bold text-gray-900 mb-3">نوع المشروع</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                        <?php
                            $types = [
                                'web_app' => ['تطبيق ويب', 'fa-globe', 'from-blue-500 to-blue-600'],
                                'mobile_app' => ['موبايل', 'fa-mobile-alt', 'from-green-500 to-green-600'],
                                'api' => ['API', 'fa-plug', 'from-purple-500 to-purple-600'],
                                'library' => ['مكتبة', 'fa-book', 'from-amber-500 to-amber-600'],
                                'script' => ['سكربت', 'fa-file-code', 'from-teal-500 to-teal-600'],
                                'design' => ['تصميم', 'fa-palette', 'from-pink-500 to-pink-600'],
                                'game' => ['لعبة', 'fa-gamepad', 'from-red-500 to-red-600'],
                                'desktop' => ['سطح مكتب', 'fa-desktop', 'from-indigo-500 to-indigo-600'],
                                'cli' => ['CLI', 'fa-terminal', 'from-gray-600 to-gray-700'],
                                'other' => ['أخرى', 'fa-folder', 'from-gray-400 to-gray-500'],
                            ];
                        ?>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="relative block cursor-pointer select-none min-h-[3.5rem]">
                                <input type="radio" name="project_type" value="<?php echo e($value); ?>"
                                       <?php echo e(old('project_type') == $value ? 'checked' : ''); ?>

                                       class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <span class="flex items-center gap-2 p-2.5 rounded-xl border-2 transition-all border-gray-200 hover:border-[#2CA9BD]/50 hover:bg-gray-50 flex h-full pointer-events-none peer-checked:border-[#2CA9BD] peer-checked:bg-[#2CA9BD]/10 peer-checked:ring-2 peer-checked:ring-[#2CA9BD]/30">
                                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br <?php echo e($label[2]); ?> flex items-center justify-center text-white text-xs flex-shrink-0">
                                        <i class="fas <?php echo e($label[1]); ?>"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-gray-800 truncate"><?php echo e($label[0]); ?></span>
                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">الوصف (اختياري)</label>
                <textarea name="description" rows="3" placeholder="اشرح فكرة المشروع، التقنيات المستخدمة، وما تعلمته..."
                          class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20"><?php echo e(old('description')); ?></textarea>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="fab fa-github text-gray-700 ml-1"></i>
                        رابط GitHub
                    </label>
                    <input type="url" name="github_url" value="<?php echo e(old('github_url')); ?>"
                           placeholder="https://github.com/..."
                           class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">رابط المشروع الحي</label>
                    <input type="url" name="project_url" value="<?php echo e(old('project_url')); ?>"
                           placeholder="https://demo.example.com"
                           class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">المسار التعليمي <span class="text-gray-500 font-normal text-xs">(أحدهما أو كلاهما)</span></label>
                    <select name="academic_year_id" class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
                        <option value="">-- اختر المسار --</option>
                        <?php $__empty_1 = true; $__currentLoopData = $learningPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($path->id); ?>" <?php echo e(old('academic_year_id') == $path->id ? 'selected' : ''); ?>><?php echo e($path->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="" disabled>لا يوجد مسارات مسجّل فيها</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">الكورس <span class="text-gray-500 font-normal text-xs">(أحدهما أو كلاهما)</span></label>
                    <select name="advanced_course_id" class="w-full rounded-xl border-2 border-[#2CA9BD]/20 px-4 py-3 focus:border-[#2CA9BD] focus:ring-2 focus:ring-[#2CA9BD]/20">
                        <option value="">-- اختر الكورس --</option>
                        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id') == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <option value="" disabled>لا يوجد كورسات مشتراة</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end pt-4 border-t border-gray-200">
                <div class="lg:col-span-6">
                    <label class="block text-sm font-bold text-gray-900 mb-2">صور من المشروع <span class="text-gray-500 font-normal">(اختياري، حد أقصى 5 صور، كل صورة 2 ميجابايت)</span></label>
                    <div class="border-2 border-dashed border-[#2CA9BD]/30 rounded-xl px-4 py-3 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                        <input type="file" name="images[]" accept="image/*" multiple
                               data-max="5" id="portfolio-images"
                               class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-[#2CA9BD]/10 file:text-[#2CA9BD] hover:file:bg-[#2CA9BD]/20">
                        <p class="text-xs text-gray-500 mt-2" id="images-hint">يمكنك اختيار أكثر من صورة (حد أقصى 5)</p>
                    </div>
                </div>
                <div class="lg:col-span-6 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="<?php echo e(route('student.portfolio.index')); ?>" class="inline-flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-50 transition-all order-2 sm:order-1">
                        <i class="fas fa-arrow-right"></i>
                        إلغاء والعودة
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all order-1 sm:order-2">
                        <i class="fas fa-upload"></i>
                        رفع المشروع
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('portfolio-images');
    var hint = document.getElementById('images-hint');
    if (input && hint) {
        input.addEventListener('change', function() {
            var files = this.files;
            if (files.length > 5) {
                hint.textContent = 'تم تحديد ' + files.length + ' صور. سيتم أخذ أول 5 صور فقط.';
                var dt = new DataTransfer();
                for (var i = 0; i < 5; i++) dt.items.add(files[i]);
                this.files = dt.files;
            } else if (files.length > 0) {
                hint.textContent = 'تم اختيار ' + files.length + ' صورة (حد أقصى 5).';
            } else {
                hint.textContent = 'يمكنك اختيار أكثر من صورة (حد أقصى 5)';
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\portfolio\create.blade.php ENDPATH**/ ?>