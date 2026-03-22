

<?php $__env->startSection('title', 'تعديل الباقة'); ?>
<?php $__env->startSection('header', 'تعديل الباقة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">تعديل الباقة: <?php echo e($package->name); ?></h1>
            <a href="<?php echo e(route('admin.packages.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <form action="<?php echo e(route('admin.packages.update', $package)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <!-- المعلومات الأساسية -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الباقة *</label>
                    <input type="text" name="name" required value="<?php echo e(old('name', $package->name)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط (Slug)</label>
                    <input type="text" name="slug" value="<?php echo e(old('slug', $package->slug)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                    <input type="number" name="price" step="0.01" min="0" required value="<?php echo e(old('price', $package->price)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأصلي (قبل الخصم)</label>
                    <input type="number" name="original_price" step="0.01" min="0" value="<?php echo e(old('original_price', $package->original_price)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="order" min="0" value="<?php echo e(old('order', $package->order)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مدة الصلاحية (بالأيام)</label>
                    <input type="number" name="duration_days" min="0" value="<?php echo e(old('duration_days', $package->duration_days)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', $package->starts_at ? $package->starts_at->format('Y-m-d\TH:i') : '')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', $package->ends_at ? $package->ends_at->format('Y-m-d\TH:i') : '')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description', $package->description)); ?></textarea>
            </div>

            <!-- الصورة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صورة الباقة</label>
                <?php if($package->thumbnail): ?>
                <div class="mb-2">
                    <img src="<?php echo e(asset('storage/' . $package->thumbnail)); ?>" alt="<?php echo e($package->name); ?>" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" accept="image/*" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <p class="mt-1 text-xs text-gray-500">اتركه فارغاً للاحتفاظ بالصورة الحالية</p>
            </div>

            <!-- المميزات -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المميزات</label>
                <div id="features-container" class="space-y-2">
                    <?php if($package->features && count($package->features) > 0): ?>
                        <?php $__currentLoopData = $package->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex gap-2">
                            <input type="text" name="features[]" value="<?php echo e($feature); ?>" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 <?php echo e(count($package->features) == 1 ? 'hidden' : ''); ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="flex gap-2">
                        <input type="text" name="features[]" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                               placeholder="مثال: وصول لجميع الكورسات">
                        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 hidden">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="addFeature()" class="mt-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة ميزة
                </button>
            </div>

            <!-- اختيار الكورسات -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكورسات *</label>
                <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <?php if($courses->count() > 0): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="courses[]" value="<?php echo e($course->id); ?>" 
                                   <?php echo e(in_array($course->id, old('courses', $package->courses->pluck('id')->toArray())) ? 'checked' : ''); ?>

                                   class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <span class="mr-3 text-sm text-gray-700">
                                <?php echo e($course->title); ?>

                                <?php if($course->price > 0): ?>
                                <span class="text-xs text-gray-500">(<?php echo e(number_format($course->price, 2)); ?> ج.م)</span>
                                <?php else: ?>
                                <span class="text-xs text-green-600">(مجاني)</span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <p class="text-sm text-gray-500 text-center">لا توجد كورسات متاحة</p>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['courses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- الخيارات -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $package->is_active) ? 'checked' : ''); ?> 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">نشط</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $package->is_featured) ? 'checked' : ''); ?> 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">مميز</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_popular" value="1" <?php echo e(old('is_popular', $package->is_popular) ? 'checked' : ''); ?> 
                           class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">الأكثر شعبية</span>
                </label>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-4 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
                <a href="<?php echo e(route('admin.packages.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'flex gap-2';
    newFeature.innerHTML = `
        <input type="text" name="features[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
               placeholder="مثال: وصول لجميع الكورسات">
        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newFeature);
    
    container.querySelectorAll('button').forEach(btn => {
        if (btn.textContent.includes('times')) {
            btn.classList.remove('hidden');
        }
    });
}

function removeFeature(button) {
    button.parentElement.remove();
    
    const container = document.getElementById('features-container');
    const inputs = container.querySelectorAll('input[type="text"]');
    if (inputs.length === 1) {
        container.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes('times')) {
                btn.classList.add('hidden');
            }
        });
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\packages\edit.blade.php ENDPATH**/ ?>