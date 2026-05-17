

<?php $__env->startSection('title', 'إضافة وظيفة جديدة'); ?>
<?php $__env->startSection('header', 'إضافة وظيفة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إضافة وظيفة جديدة</h1>
                <p class="text-gray-600 mt-1">إنشاء وظيفة جديدة في الأكاديمية</p>
            </div>
            <a href="<?php echo e(route('admin.employee-jobs.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <form action="<?php echo e(route('admin.employee-jobs.store')); ?>" method="POST" class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <?php echo csrf_field(); ?>

        <div class="space-y-6">
            <!-- قوالب جاهزة -->
            <div class="rounded-xl border-2 border-indigo-200 bg-gradient-to-l from-indigo-50/80 to-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-600"></i>
                    اختيار وظيفة من القوالب الجاهزة
                </h2>
                <p class="text-sm text-gray-600 mb-4">اختر قالباً لتعبئة الاسم والرمز والوصف والمسؤوليات تلقائياً، ثم يمكنك التعديل قبل الحفظ. اترك «إدخال يدوي» إن أردت كتابة كل شيء بنفسك.</p>
                <div class="max-w-xl">
                    <label for="job_preset_select" class="block text-sm font-medium text-gray-700 mb-2">القالب</label>
                    <select id="job_preset_select"
                            class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">— إدخال يدوي بالكامل —</option>
                        <?php $__currentLoopData = $jobPresets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presetKey => $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($presetKey); ?>"><?php echo e($preset['name']); ?> (<?php echo e($preset['code']); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if(!empty($presetsSkippedBecauseCodeTaken)): ?>
                        <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                            <p class="font-semibold mb-1"><i class="fas fa-info-circle ml-1"></i> قوالب غير متاحة (الرمز مستخدم مسبقاً)</p>
                            <p class="text-amber-800/90 mb-2">لا يمكن إنشاء وظيفة بنفس الرمز مرتين. هذه القوالب مُخفاة لأن الرمز موجود في قائمة الوظائف:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-900">
                                <?php $__currentLoopData = $presetsSkippedBecauseCodeTaken; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><strong><?php echo e($p['name']); ?></strong> — رمز <code class="bg-amber-100 px-1 rounded"><?php echo e($p['code']); ?></code></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <a href="<?php echo e(route('admin.employee-jobs.index')); ?>" class="inline-block mt-2 text-amber-800 font-medium underline hover:text-amber-950">عرض قائمة الوظائف</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <script id="employee-job-presets-data" type="application/json"><?php echo json_encode($jobPresets, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>

            <!-- القسم الأساسي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الوظيفة *</label>
                        <input type="text" name="name" id="job_name_input" value="<?php echo e(old('name')); ?>" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رمز الوظيفة *</label>
                        <input type="text" name="code" id="job_code_input" value="<?php echo e(old('code')); ?>" required
                               placeholder="مثال: sales أو video_editing"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <p class="text-xs text-gray-500 mt-1">للمبيعات استخدم <code class="bg-gray-100 px-1 rounded">sales</code>؛ لمونتاج الفيديو <code class="bg-gray-100 px-1 rounded">video_editing</code> ليتم ربط النظام تلقائياً.</p>
                        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" id="job_description_input" rows="3" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('description')); ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">المسؤوليات</label>
                        <textarea name="responsibilities" id="job_responsibilities_input" rows="4" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo e(old('responsibilities')); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- القسم المالي -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات المالية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى للراتب</label>
                        <input type="number" name="min_salary" value="<?php echo e(old('min_salary')); ?>" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للراتب</label>
                        <input type="number" name="max_salary" value="<?php echo e(old('max_salary')); ?>" min="0" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <?php $__errorArgs = ['max_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- القسم الإداري -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات</h2>
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="mr-2 text-sm font-medium text-gray-700">وظيفة نشطة</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
            <a href="<?php echo e(route('admin.employee-jobs.index')); ?>" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>حفظ الوظيفة
            </button>
        </div>
    </form>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var el = document.getElementById('employee-job-presets-data');
    if (!el) return;
    var presets = {};
    try { presets = JSON.parse(el.textContent || '{}'); } catch (e) { return; }
    var sel = document.getElementById('job_preset_select');
    if (!sel) return;
    sel.addEventListener('change', function () {
        var key = this.value;
        if (!key || !presets[key]) return;
        var p = presets[key];
        var n = document.getElementById('job_name_input');
        var c = document.getElementById('job_code_input');
        var d = document.getElementById('job_description_input');
        var r = document.getElementById('job_responsibilities_input');
        if (n && p.name != null) n.value = p.name;
        if (c && p.code != null) c.value = p.code;
        if (d && p.description != null) d.value = p.description;
        if (r && p.responsibilities != null) r.value = p.responsibilities;
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/employee-jobs/create.blade.php ENDPATH**/ ?>