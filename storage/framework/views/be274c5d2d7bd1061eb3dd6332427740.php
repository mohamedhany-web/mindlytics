

<?php $__env->startSection('title', 'تعديل بنك الأسئلة - Mindlytics'); ?>
<?php $__env->startSection('header', 'تعديل بنك الأسئلة: ' . $questionBank->title); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-section {
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-section:hover {
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 8px 16px rgba(44, 169, 189, 0.1);
    }

    .form-input {
        border: 2px solid rgba(44, 169, 189, 0.2);
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: #2CA9BD;
        box-shadow: 0 0 0 4px rgba(44, 169, 189, 0.1);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-r from-[#2CA9BD]/10 via-[#65DBE4]/10 to-[#2CA9BD]/10 rounded-2xl p-6 border-2 border-[#2CA9BD]/20 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1C2C39] mb-2">تعديل بنك الأسئلة</h1>
                <p class="text-sm sm:text-base text-[#1F3A56] font-medium">تعديل معلومات بنك الأسئلة</p>
            </div>
            <a href="<?php echo e(route('instructor.question-banks.show', $questionBank)); ?>" 
               class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-arrow-right"></i>
                <span>العودة</span>
            </a>
        </div>
    </div>

    <!-- نموذج تعديل بنك الأسئلة -->
    <form action="<?php echo e(route('instructor.question-banks.update', $questionBank)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">معلومات بنك الأسئلة</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- العنوان -->
                        <div>
                            <label for="title" class="block text-sm font-bold text-[#1C2C39] mb-2">
                                عنوان بنك الأسئلة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="<?php echo e(old('title', $questionBank->title)); ?>" required
                                   class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                   placeholder="مثال: بنك أسئلة البرمجة الأساسية">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600 font-medium"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- الوصف -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-[#1C2C39] mb-2">
                                وصف بنك الأسئلة
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-input w-full px-4 py-3 rounded-xl focus:outline-none"
                                      placeholder="وصف مختصر عن بنك الأسئلة ومحتواه..."><?php echo e(old('description', $questionBank->description)); ?></textarea>
                        </div>

                        <!-- مستوى الصعوبة -->
                        <div>
                            <label for="difficulty" class="block text-sm font-bold text-[#1C2C39] mb-2">
                                مستوى الصعوبة العام
                            </label>
                            <select name="difficulty" id="difficulty"
                                    class="form-input w-full px-4 py-3 rounded-xl focus:outline-none">
                                <option value="">اختياري</option>
                                <option value="easy" <?php echo e(old('difficulty', $questionBank->difficulty) == 'easy' ? 'selected' : ''); ?>>سهل</option>
                                <option value="medium" <?php echo e(old('difficulty', $questionBank->difficulty) == 'medium' ? 'selected' : ''); ?>>متوسط</option>
                                <option value="hard" <?php echo e(old('difficulty', $questionBank->difficulty) == 'hard' ? 'selected' : ''); ?>>صعب</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- معلومات سريعة -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">معلومات سريعة</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-blue-600"></i>
                                <span class="text-sm font-bold text-blue-800">نصائح</span>
                            </div>
                            <ul class="mt-2 text-sm text-blue-700 space-y-1.5 font-medium">
                                <li>• يمكنك تعديل جميع المعلومات</li>
                                <li>• الأسئلة الموجودة لن تتأثر</li>
                                <li>• تأكد من حفظ التغييرات</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- إعدادات الحالة -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b-2 border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5">
                        <h3 class="text-lg font-black text-[#1C2C39]">إعدادات الحالة</h3>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" 
                                   <?php echo e(old('is_active', $questionBank->is_active) ? 'checked' : ''); ?>

                                   class="w-5 h-5 text-[#2CA9BD] bg-gray-100 border-gray-300 rounded focus:ring-[#2CA9BD] focus:ring-2">
                            <span class="text-sm text-[#1C2C39] font-medium group-hover:text-[#2CA9BD] transition-colors">بنك نشط</span>
                        </label>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="form-section rounded-2xl overflow-hidden">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] hover:from-[#1F3A56] hover:to-[#2CA9BD] text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التغييرات
                            </button>
                            
                            <a href="<?php echo e(route('instructor.question-banks.show', $questionBank)); ?>" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-xl font-bold transition-all duration-300 block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\question-banks\edit.blade.php ENDPATH**/ ?>