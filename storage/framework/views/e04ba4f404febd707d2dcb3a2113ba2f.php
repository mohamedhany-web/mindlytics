

<?php $__env->startSection('title', 'تقديم مجموعة بيانات'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full">
    <?php if($errors->any()): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside text-sm"><?php echo e($errors->first()); ?></ul>
        </div>
    <?php endif; ?>

    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900">تقديم مجموعة بيانات جديدة</h1>
        <p class="text-slate-600 mt-1">ستتم مراجعة التقديم من الإدارة قبل النشر. الملفات تُخزَّن على Cloudflare وتُحمَّل بسرعة.</p>
    </div>

    <form action="<?php echo e(route('community.contributor.datasets.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6" id="datasetForm">
        <?php echo csrf_field(); ?>

        <div>
            <label for="title" class="block text-sm font-bold text-slate-700 mb-2">عنوان مجموعة البيانات <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                   placeholder="مثال: مجموعة بيانات المبيعات 2024"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
        </div>

        <div>
            <label for="description" class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
            <textarea name="description" id="description" rows="4" placeholder="وصف المجموعة، المصدر، طريقة الاستخدام..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 resize-y"><?php echo e(old('description')); ?></textarea>
        </div>

        <div>
            <label for="category" class="block text-sm font-bold text-slate-700 mb-2">التصنيف</label>
            <select name="category" id="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
                <option value="">— اختر تصنيفاً —</option>
                <?php $__currentLoopData = \App\Models\CommunityDataset::CATEGORIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php echo e(old('category') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv,.json,.txt,.zip,.pdf,.xml,.tsv" class="hidden" data-single>

        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملفات البيانات</label>
            <div id="dropZone" class="relative border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50/50 hover:bg-cyan-50/30 hover:border-cyan-400 transition-colors cursor-pointer">
                <input type="file" name="files[]" id="filesInput" multiple
                       accept=".xlsx,.xls,.csv,.json,.txt,.zip,.pdf,.xml,.tsv"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div class="pointer-events-none">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-cloud-upload-alt text-2xl"></i>
                    </div>
                    <p class="text-slate-700 font-semibold mb-1">اسحب الملفات هنا أو انقر للاختيار</p>
                    <p class="text-slate-500 text-sm">xlsx, xls, csv, json, txt, zip, pdf, xml, tsv — حتى 100 ميجا لكل ملف، حد أقصى <?php echo e(\App\Http\Controllers\Community\ContributorController::MAX_FILES); ?> ملفات</p>
                </div>
            </div>
            <p class="mt-1.5 text-xs text-slate-500">الملفات تُرفع وتُخزَّن على Cloudflare (R2) لتحميل أسرع.</p>

            
            <ul id="fileList" class="mt-4 space-y-2 hidden"></ul>
        </div>

        <div>
            <label for="file_url" class="block text-sm font-bold text-slate-700 mb-2">رابط التحميل (اختياري)</label>
            <input type="url" name="file_url" id="file_url" value="<?php echo e(old('file_url')); ?>"
                   placeholder="https://example.com/dataset.csv"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane"></i>
                <span>إرسال للمراجعة</span>
            </button>
            <a href="<?php echo e(route('community.contributor.datasets.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">إلغاء</a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const MAX_FILES = <?php echo e(\App\Http\Controllers\Community\ContributorController::MAX_FILES); ?>;
    const MAX_MB = 100;
    const dropZone = document.getElementById('dropZone');
    const filesInput = document.getElementById('filesInput');
    const fileList = document.getElementById('fileList');
    const singleInput = document.getElementById('file');

    function humanSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function updateFileList() {
        const multi = Array.from(filesInput.files || []);
        const single = singleInput.files && singleInput.files[0];
        const all = single ? [single, ...multi] : multi;
        fileList.innerHTML = '';
        if (all.length === 0) {
            fileList.classList.add('hidden');
            return;
        }
        fileList.classList.remove('hidden');
        all.forEach(function(f, i) {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-100 border border-slate-200';
            const size = humanSize(f.size);
            const over = f.size > MAX_MB * 1024 * 1024;
            li.innerHTML = '<span class="flex items-center gap-2 truncate min-w-0"><i class="fas fa-file text-cyan-600 shrink-0"></i><span class="truncate text-sm font-medium text-slate-800">' + (f.name || 'ملف') + '</span><span class="text-xs text-slate-500 shrink-0">' + size + '</span></span>' +
                (over ? '<span class="text-red-600 text-xs shrink-0">يتجاوز ' + MAX_MB + ' ميجا</span>' : '');
            fileList.appendChild(li);
        });
        document.getElementById('submitBtn').disabled = fileList.querySelector('.text-red-600') !== null;
    }

    dropZone.addEventListener('click', function(e) {
        if (e.target === dropZone || e.target.closest('.pointer-events-none')) {
            filesInput.click();
        }
    });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-cyan-500', 'bg-cyan-50/50');
    });
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-cyan-500', 'bg-cyan-50/50');
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-cyan-500', 'bg-cyan-50/50');
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
    singleInput.addEventListener('change', updateFileList);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\contributor\datasets\create.blade.php ENDPATH**/ ?>