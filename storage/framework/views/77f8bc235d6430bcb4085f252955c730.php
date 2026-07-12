

<?php $__env->startSection('title', 'إضافة نموذج — مكتبة النماذج'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full">
    <?php if($errors->any()): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside text-sm"><?php echo e($errors->first()); ?></ul>
        </div>
    <?php endif; ?>

    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900">إضافة نموذج إلى مكتبة النماذج</h1>
        <p class="text-slate-600 mt-1">ارفع نموذجك المدرب مع <strong>شرح كامل للمنهجية والخطوات</strong>. التخزين بالكامل على Cloudflare (R2). ستتم مراجعة التقديم من الإدارة قبل النشر.</p>
    </div>

    <form action="<?php echo e(route('community.contributor.models.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6" id="modelForm">
        <?php echo csrf_field(); ?>

        <div>
            <label for="title" class="block text-sm font-bold text-slate-700 mb-2">عنوان النموذج <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                   placeholder="مثال: نموذج تصنيف النص بالعربية"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
        </div>

        <div>
            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
            <textarea name="description" id="description" rows="3" placeholder="وصف مختصر للنموذج، الاستخدامات، الإطار المستخدم..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 resize-y"><?php echo e(old('description')); ?></textarea>
        </div>

        <div>
            <label for="methodology_steps" class="block text-sm font-bold text-slate-700 mb-2">
                شرح الخطوات والمنهجية (مطلوب) <span class="text-red-500">*</span>
            </label>
            <textarea name="methodology_steps" id="methodology_steps" rows="10" required
                      placeholder="اشرح كل خطوة مررت بها: تجهيز البيانات، التنظيف، التقسيم، المعمارية، التدريب، التقييم، أي ضبط للمعاملات..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 resize-y"><?php echo e(old('methodology_steps')); ?></textarea>
            <p class="mt-1.5 text-xs text-slate-500">المجتمع يقدّر الشفافية: كلما أوضحت الخطوات، زادت الفائدة وإمكانية إعادة التدريب.</p>
        </div>

        <div>
            <label for="community_dataset_id" class="block text-sm font-bold text-slate-700 mb-2">ربط بمجموعة بيانات (اختياري)</label>
            <select name="community_dataset_id" id="community_dataset_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                <option value="">— لا ربط —</option>
                <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ds->id); ?>" <?php echo e((string)old('community_dataset_id') === (string)$ds->id ? 'selected' : ''); ?>><?php echo e($ds->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label for="performance_metrics" class="block text-sm font-bold text-slate-700 mb-2">مقاييس الأداء (JSON اختياري)</label>
            <textarea name="performance_metrics" id="performance_metrics" rows="4" placeholder='{"accuracy": 0.92, "f1": 0.89, "loss": 0.15}'
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-mono text-sm resize-y"><?php echo e(old('performance_metrics')); ?></textarea>
            <p class="mt-1.5 text-xs text-slate-500">مصفوفة JSON: مفتاح وقيمة (رقم أو نص) لكل مقياس.</p>
        </div>

        <div>
            <label for="license" class="block text-sm font-bold text-slate-700 mb-2">الترخيص</label>
            <select name="license" id="license" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                <option value="">— اختر ترخيصاً —</option>
                <?php $__currentLoopData = \App\Models\CommunityModel::LICENSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(old('license') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label for="usage_instructions" class="block text-sm font-bold text-slate-700 mb-2">طريقة الاستخدام أو الاستدعاء</label>
            <textarea name="usage_instructions" id="usage_instructions" rows="6" placeholder="كود أو أوامر لتحميل واستدعاء النموذج (مثلاً: load_model، predict، أو سكربت Python)..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-mono text-sm resize-y"><?php echo e(old('usage_instructions')); ?></textarea>
        </div>

        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملفات النموذج <span class="text-red-500">*</span></label>
            <p class="text-slate-600 text-sm mb-3">يمكنك رفع <strong>ملفات بايثون</strong> (.py, .ipynb)، أوزان النماذج (pkl, pt, h5, onnx…)، أو مزيج منهما. ارفع سكربت التدريب أو الاستدعاء مع أوزان النموذج إن وجدت.</p>
            <div id="dropZone" class="relative border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50/50 hover:bg-amber-50/30 hover:border-amber-400 transition-colors cursor-pointer">
                <input type="file" name="files[]" id="filesInput" multiple
                       accept=".pkl,.pt,.pth,.h5,.hdf5,.onnx,.joblib,.zip,.safetensors,.bin,.json,.py,.pyw,.ipynb"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div class="pointer-events-none">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-brain text-2xl"></i>
                    </div>
                    <p class="text-slate-700 font-semibold mb-1">اسحب الملفات هنا أو انقر للاختيار</p>
                    <p class="text-slate-500 text-sm mb-1">ملفات بايثون: <span class="font-semibold text-amber-700">.py .pyw .ipynb</span></p>
                    <p class="text-slate-500 text-sm">أوزان/أخرى: pkl, pt, pth, h5, onnx, joblib, zip, safetensors, bin, json — حتى <?php echo e((int)(\App\Http\Controllers\Community\ContributorController::MAX_MODEL_FILE_KB / 1024)); ?> ميجا لكل ملف، حد أقصى 10 ملفات</p>
                </div>
            </div>
            <p class="mt-1.5 text-xs text-slate-500">الملفات تُخزَّن بالكامل على Cloudflare (R2) لتحميل أسرع.</p>
            <ul id="fileList" class="mt-4 space-y-2 hidden"></ul>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shadow-md disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane"></i>
                <span>إرسال للمراجعة</span>
            </button>
            <a href="<?php echo e(route('community.contributor.models.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">إلغاء</a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const MAX_FILES = 10;
    const MAX_MB = <?php echo e((int)(\App\Http\Controllers\Community\ContributorController::MAX_MODEL_FILE_KB / 1024)); ?>;
    const dropZone = document.getElementById('dropZone');
    const filesInput = document.getElementById('filesInput');
    const fileList = document.getElementById('fileList');

    function humanSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function updateFileList() {
        const all = Array.from(filesInput.files || []);
        fileList.innerHTML = '';
        if (all.length === 0) {
            fileList.classList.add('hidden');
            document.getElementById('submitBtn').disabled = true;
            return;
        }
        fileList.classList.remove('hidden');
        var hasOver = false;
        all.forEach(function(f) {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-100 border border-slate-200';
            const size = humanSize(f.size);
            const over = f.size > MAX_MB * 1024 * 1024;
            if (over) hasOver = true;
            li.innerHTML = '<span class="flex items-center gap-2 truncate min-w-0"><i class="fas fa-file text-amber-600 shrink-0"></i><span class="truncate text-sm font-medium text-slate-800">' + (f.name || 'ملف') + '</span><span class="text-xs text-slate-500 shrink-0">' + size + '</span></span>' +
                (over ? '<span class="text-red-600 text-xs shrink-0">يتجاوز ' + MAX_MB + ' ميجا</span>' : '');
            fileList.appendChild(li);
        });
        document.getElementById('submitBtn').disabled = hasOver || all.length === 0;
    }

    dropZone.addEventListener('click', function(e) {
        if (e.target === dropZone || e.target.closest('.pointer-events-none')) {
            filesInput.click();
        }
    });
    dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.classList.add('border-amber-500', 'bg-amber-50/50'); });
    dropZone.addEventListener('dragleave', function(e) { e.preventDefault(); dropZone.classList.remove('border-amber-500', 'bg-amber-50/50'); });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-amber-500', 'bg-amber-50/50');
        if (e.dataTransfer.files.length) {
            const dt = new DataTransfer();
            const existing = Array.from(filesInput.files || []);
            const add = Array.from(e.dataTransfer.files);
            const combined = existing.length + add.length > MAX_FILES ? existing.slice(0, MAX_FILES - add.length).concat(add) : existing.concat(add);
            combined.slice(0, MAX_FILES).forEach(function(f) { dt.items.add(f); });
            filesInput.files = dt.files;
            updateFileList();
        }
    });
    filesInput.addEventListener('change', updateFileList);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\contributor\models\create.blade.php ENDPATH**/ ?>