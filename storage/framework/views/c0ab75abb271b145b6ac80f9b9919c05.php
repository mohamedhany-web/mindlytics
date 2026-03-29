

<?php $__env->startSection('title', 'تفاصيل المهمة'); ?>
<?php $__env->startSection('header', 'تفاصيل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('import_report') && $task->isVideoEditing()): ?>
        <?php $rep = session('import_report'); ?>
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-sm text-gray-800 shadow-sm">
            <p class="font-semibold text-gray-900 mb-2">تقرير الاستيراد</p>
            <p>تم استيراد: <strong><?php echo e($rep['imported'] ?? 0); ?></strong></p>
            <?php if(!empty($rep['skipped_duplicates'])): ?>
                <p class="mt-2 text-amber-800">روابط مُتخطاة (مكررة): <?php echo e(count($rep['skipped_duplicates'])); ?></p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-32 overflow-y-auto mt-1">
                    <?php $__currentLoopData = array_slice($rep['skipped_duplicates'], 0, 15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($line); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <?php if(!empty($rep['row_errors'])): ?>
                <p class="mt-2 text-red-800">أخطاء في الصفوف: <?php echo e(count($rep['row_errors'])); ?></p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-32 overflow-y-auto mt-1">
                    <?php $__currentLoopData = array_slice($rep['row_errors'], 0, 15, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowNum => $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>صف <?php echo e($rowNum); ?>: <?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo e($task->title); ?></h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold
                        <?php if($task->isVideoEditing()): ?> bg-violet-100 text-violet-800
                        <?php elseif($task->isSales()): ?> bg-emerald-100 text-emerald-800
                        <?php else: ?> bg-slate-100 text-slate-700
                        <?php endif; ?>">
                        <?php if($task->isVideoEditing()): ?>
                            <i class="fas fa-video"></i> مونتاج فيديو
                        <?php elseif($task->isSales()): ?>
                            <i class="fas fa-handshake"></i> مبيعات
                        <?php else: ?>
                            <i class="fas fa-tasks"></i> مهمة عامة
                        <?php endif; ?>
                    </span>
                </div>
                <p class="text-gray-600">عرض تفاصيل المهمة والتسليمات</p>
            </div>
            <a href="<?php echo e(route('employee.tasks.index')); ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors shrink-0">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">المكلف</p>
                <p class="font-semibold text-gray-900"><?php echo e($task->assigner->name); ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الأولوية</p>
                <span class="inline-block px-2 py-1 rounded-lg text-sm font-semibold
                    <?php if($task->priority === 'urgent'): ?> bg-red-100 text-red-800
                    <?php elseif($task->priority === 'high'): ?> bg-orange-100 text-orange-800
                    <?php elseif($task->priority === 'medium'): ?> bg-yellow-100 text-yellow-800
                    <?php else: ?> bg-gray-100 text-gray-800
                    <?php endif; ?>">
                    <?php if($task->priority === 'urgent'): ?> عاجل
                    <?php elseif($task->priority === 'high'): ?> عالي
                    <?php elseif($task->priority === 'medium'): ?> متوسط
                    <?php else: ?> منخفض
                    <?php endif; ?>
                </span>
            </div>
            <?php if($task->deadline): ?>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الموعد النهائي</p>
                <p class="font-semibold <?php echo e($task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-900'); ?>">
                    <?php echo e($task->deadline->format('Y-m-d')); ?>

                </p>
            </div>
            <?php endif; ?>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">التقدم</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[60px]">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?php echo e($task->progress); ?>%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700"><?php echo e($task->progress); ?>%</span>
                </div>
            </div>
        </div>

        <?php if($task->description): ?>
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed whitespace-pre-wrap"><?php echo e($task->description); ?></p>
        </div>
        <?php endif; ?>

        <?php if($task->isSales() && auth()->user()->isSalesEmployee()): ?>
        <div class="mb-6 rounded-xl border-2 border-emerald-200 bg-emerald-50/60 p-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-emerald-900"><i class="fas fa-chart-line ml-1"></i> مهمة مبيعات: يمكنك ربط عملك بمركز المبيعات والعملاء المحتملين.</p>
            <a href="<?php echo e(route('employee.sales.dashboard')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">مركز المبيعات</a>
        </div>
        <?php endif; ?>

        <?php if($task->isVideoEditing()): ?>
            <?php
                $sumBeforeMin = (int) $task->deliverables->sum('duration_before_minutes');
                $sumAfterMin = (int) $task->deliverables->sum('duration_after_minutes');
            ?>
            <?php if($task->deliverables->count() > 0): ?>
                <div class="mb-6 flex flex-wrap gap-3 text-sm text-gray-700">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-violet-50 border border-violet-100">مجموع دقائق قبل: <strong class="mr-1"><?php echo e($sumBeforeMin); ?></strong></span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100">مجموع دقائق بعد: <strong class="mr-1"><?php echo e($sumAfterMin); ?></strong></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- تحديث الحالة -->
        <div class="mb-6 p-5 bg-slate-50 rounded-xl border border-slate-200">
            <h3 class="text-base font-semibold text-gray-900 mb-3">تحديث حالة المهمة</h3>
            <form action="<?php echo e(route('employee.tasks.update-status', $task)); ?>" method="POST" class="flex flex-wrap items-end gap-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending" <?php echo e($task->status == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="in_progress" <?php echo e($task->status == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="completed" <?php echo e($task->status == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="on_hold" <?php echo e($task->status == 'on_hold' ? 'selected' : ''); ?>>معلقة مؤقتاً</option>
                    </select>
                </div>
                <div class="w-24">
                    <label class="block text-sm font-medium text-gray-700 mb-1">التقدم %</label>
                    <input type="number" name="progress" value="<?php echo e($task->progress); ?>" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث
                </button>
            </form>
        </div>

        <!-- زر التسليمات: عند النقر يفتح ويظهر كامل التسليمات -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <details class="group rounded-2xl border-2 border-slate-200 bg-white overflow-hidden" id="deliverables-section" <?php echo e(request()->has('open') ? 'open' : ''); ?>>
                <summary class="flex items-center justify-between gap-4 w-full cursor-pointer list-none px-6 py-4 bg-gradient-to-l from-slate-50 to-white hover:from-blue-50/50 hover:to-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-2xl">
                    <span class="flex items-center gap-3 font-bold text-gray-900 text-lg">
                        <?php if($task->isVideoEditing()): ?>
                            <span class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-film text-xl"></i>
                            </span>
                        <?php elseif($task->isSales()): ?>
                            <span class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-handshake text-xl"></i>
                            </span>
                        <?php else: ?>
                            <span class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-inbox text-xl"></i>
                            </span>
                        <?php endif; ?>
                        <span>التسليمات</span>
                        <?php if($task->deliverables->count() > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                                <?php echo e($task->deliverables->count()); ?>

                            </span>
                        <?php endif; ?>
                    </span>
                    <span class="flex items-center gap-2 text-gray-500 group-open:rotate-180 transition-transform">
                        <i class="fas fa-chevron-down"></i>
                        <span class="text-sm font-medium">عرض الكل</span>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 bg-slate-50/50 border-t border-slate-100">
            <!-- نموذج التسليم (فوق) -->
            <?php if($task->isVideoEditing()): ?>
                <div class="bg-violet-50/30 border-2 border-violet-200 rounded-xl p-6 mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">
                        <i class="fas fa-plus-circle text-violet-600 mr-2"></i>تسليم مونتاج جديد
                    </h4>
                    <form action="<?php echo e(route('employee.tasks.submit-deliverable', $task)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="task_type_context" value="video_editing">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان التسليم (اختياري)</label>
                                <input type="text" name="title" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: فيديو الحلقة ١">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">رابط الفيديو من Bunny <span class="text-red-500">*</span></label>
                                <input type="url" name="video_link_url" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="https://...bunny.net أو b-cdn.net أو mediadelivery.net">
                                <p class="text-xs text-gray-500 mt-1">رابط من Bunny: bunny.net أو b-cdn.net أو mediadelivery.net — لا يتم رفع ملفات</p>
                                <?php $__errorArgs = ['video_link_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ممن استلمته <span class="text-red-500">*</span></label>
                                <input type="text" name="received_from" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="اسم الشخص أو المصدر">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الفيديو قبل المونتاج (نص)</label>
                                <input type="text" name="duration_before" value="<?php echo e(old('duration_before')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 10:30 أو 45 دقيقة">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الفيديو بعد المونتاج (نص)</label>
                                <input type="text" name="duration_after" value="<?php echo e(old('duration_after')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 8:00 أو 35 دقيقة">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دقائق قبل (رقم، اختياري)</label>
                                <input type="number" name="duration_before_minutes" value="<?php echo e(old('duration_before_minutes')); ?>" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="يُفضَّل لحساب المجاميع">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دقائق بعد (رقم، اختياري)</label>
                                <input type="number" name="duration_after_minutes" value="<?php echo e(old('duration_after_minutes')); ?>" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="يُفضَّل لحساب المجاميع">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                                <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="أي تفاصيل إضافية..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 w-full md:w-auto px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-upload mr-2"></i>تسليم المونتاج
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-violet-200">
                        <h5 class="text-sm font-semibold text-gray-900 mb-3"><i class="fas fa-file-excel text-green-600 mr-2"></i>استيراد عدة تسليمات من Excel</h5>
                        <p class="text-xs text-gray-600 mb-3">عمود إلزامي: رابط الفيديو (Bunny). يُمنع تكرار نفس الرابط في الملف أو في التسليمات السابقة.</p>
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <a href="<?php echo e(route('employee.tasks.deliverables.montage-excel-template', $task)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-violet-300 text-violet-800 text-sm font-medium hover:bg-violet-50">
                                <i class="fas fa-download"></i> تنزيل القالب
                            </a>
                        </div>
                        <form action="<?php echo e(route('employee.tasks.deliverables.import-excel', $task)); ?>" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                            <?php echo csrf_field(); ?>
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-700 mb-1">ملف Excel (.xlsx)</label>
                                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="w-full text-sm text-gray-700 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-100 file:text-violet-800">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-file-import mr-1"></i> استيراد
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="rounded-xl p-6 mb-6 <?php if($task->isSales()): ?> bg-emerald-50/40 border-2 border-emerald-200 <?php else: ?> bg-gray-50 border border-gray-200 <?php endif; ?>">
                    <h4 class="text-base font-semibold text-gray-900 mb-1 flex items-center gap-2">
                        <?php if($task->isSales()): ?><i class="fas fa-handshake text-emerald-600"></i><?php endif; ?>
                        إضافة تسليم جديد
                    </h4>
                    <?php if($task->isSales()): ?>
                        <p class="text-xs text-emerald-800 mb-4">تسليم مهمة مبيعات: ملف، صورة، أو رابط (تقرير، عرض، محادثة، إلخ).</p>
                    <?php endif; ?>
                    <form action="<?php echo e(route('employee.tasks.submit-deliverable', $task)); ?>" method="POST" enctype="multipart/form-data" id="deliverableForm">
                        <?php echo csrf_field(); ?>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان التسليم *</label>
                                <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التسليم *</label>
                                <select name="delivery_type" id="delivery_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="file">ملف</option>
                                    <option value="image">صورة</option>
                                    <option value="link">رابط</option>
                                </select>
                            </div>
                            <div id="file_field">
                                <label class="block text-sm font-medium text-gray-700 mb-2" id="file_label">الملف *</label>
                                <input type="file" name="file" id="file_input" accept="" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1" id="file_hint">حدد ملف للتسليم</p>
                            </div>
                            <div id="link_field" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الرابط *</label>
                                <input type="url" name="link_url" id="link_input" placeholder="https://example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-upload mr-2"></i>تسليم المهمة
                            </button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('delivery_type').addEventListener('change', function() {
                            var type = this.value;
                            var fileField = document.getElementById('file_field');
                            var linkField = document.getElementById('link_field');
                            var fileInput = document.getElementById('file_input');
                            var linkInput = document.getElementById('link_input');
                            var fileLabel = document.getElementById('file_label');
                            var fileHint = document.getElementById('file_hint');
                            if (type === 'link') {
                                fileField.style.display = 'none';
                                linkField.style.display = 'block';
                                fileInput.removeAttribute('required');
                                linkInput.setAttribute('required', 'required');
                            } else {
                                fileField.style.display = 'block';
                                linkField.style.display = 'none';
                                fileInput.setAttribute('required', 'required');
                                linkInput.removeAttribute('required');
                                fileLabel.textContent = type === 'image' ? 'الصورة *' : 'الملف *';
                                fileInput.setAttribute('accept', type === 'image' ? 'image/*' : '');
                                fileHint.textContent = type === 'image' ? 'حدد صورة للتسليم' : 'حدد ملف للتسليم';
                            }
                        });
                    </script>
                </div>
            <?php endif; ?>

            <!-- جميع التسليمات (تحت) -->
            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-list text-slate-500"></i>
                جميع التسليمات
            </h4>
            <?php if($task->deliverables->count() > 0): ?>
                <div class="space-y-4" id="task-deliverables-list">
                    <?php $__currentLoopData = $task->deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $deliverable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition-colors bg-white shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1 space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">
                                            <?php echo e($deliverable->title ?: ($task->isVideoEditing() ? 'فيديو ' . ($index + 1) : 'تسليم ' . ($index + 1))); ?>

                                        </h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            <?php if($deliverable->status === 'approved'): ?> bg-green-100 text-green-800
                                            <?php elseif($deliverable->status === 'rejected'): ?> bg-red-100 text-red-800
                                            <?php elseif($deliverable->status === 'submitted'): ?> bg-blue-100 text-blue-800
                                            <?php else: ?> bg-gray-100 text-gray-800
                                            <?php endif; ?>">
                                            <?php if($deliverable->status === 'approved'): ?> معتمد
                                            <?php elseif($deliverable->status === 'rejected'): ?> مرفوض
                                            <?php elseif($deliverable->status === 'submitted'): ?> مقدم
                                            <?php else: ?> معلق
                                            <?php endif; ?>
                                        </span>
                                        <?php if($task->isVideoEditing() && ($deliverable->received_from || $deliverable->duration_before || $deliverable->duration_after)): ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-violet-50 text-violet-700">
                                                <i class="fas fa-video"></i> مونتاج
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if($task->isVideoEditing() && ($deliverable->received_from || $deliverable->duration_before || $deliverable->duration_after)): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                            <?php if($deliverable->received_from): ?>
                                                <div class="bg-violet-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-violet-600 mb-0.5">ممن استلمته</p>
                                                    <p class="text-gray-900 font-medium"><?php echo e($deliverable->received_from); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($deliverable->duration_before): ?>
                                                <div class="bg-amber-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-amber-700 mb-0.5">مدة قبل المونتاج</p>
                                                    <p class="text-gray-900 font-medium"><?php echo e($deliverable->duration_before); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($deliverable->duration_after): ?>
                                                <div class="bg-emerald-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-emerald-700 mb-0.5">مدة بعد المونتاج</p>
                                                    <p class="text-gray-900 font-medium"><?php echo e($deliverable->duration_after); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($deliverable->description): ?>
                                        <p class="text-sm text-gray-600"><?php echo e($deliverable->description); ?></p>
                                    <?php endif; ?>

                                    <?php if($deliverable->delivery_type === 'link' && $deliverable->link_url): ?>
                                        <div class="flex flex-wrap items-center gap-2 text-sm">
                                            <?php if($task->isVideoEditing()): ?>
                                                <i class="fas fa-video text-violet-500"></i>
                                                <span class="text-gray-600">رابط الفيديو (Bunny):</span>
                                            <?php else: ?>
                                                <i class="fas fa-link text-gray-500"></i>
                                            <?php endif; ?>
                                            <a href="<?php echo e($deliverable->link_url); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 font-medium break-all">
                                                <?php echo e(Str::limit($deliverable->link_url, 60)); ?>

                                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                                            </a>
                                        </div>
                                    <?php elseif($deliverable->file_name): ?>
                                        <div class="flex items-center gap-2 text-sm">
                                            <i class="fas fa-file-video text-violet-500"></i>
                                            <span class="text-gray-700"><?php echo e($deliverable->file_name); ?></span>
                                            <?php if($deliverable->file_path): ?>
                                                <a href="<?php echo e(Storage::url($deliverable->file_path)); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download"></i> تحميل
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($deliverable->feedback): ?>
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                            <p class="text-xs font-semibold text-amber-800 mb-1">ملاحظات المراجع</p>
                                            <p class="text-sm text-gray-900"><?php echo e($deliverable->feedback); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <p class="text-xs text-gray-400"><?php echo e($deliverable->created_at->format('Y-m-d H:i')); ?></p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 shrink-0 justify-end md:flex-col md:items-stretch">
                                    <button type="button" onclick="document.getElementById('edit-form-<?php echo e($deliverable->id); ?>').classList.toggle('hidden')" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-800 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <form action="<?php echo e(route('employee.tasks.deliverables.destroy', [$task, $deliverable])); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسليم؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 transition-colors">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div id="edit-form-<?php echo e($deliverable->id); ?>" class="hidden mt-4 pt-4 border-t border-gray-200">
                                <?php if($task->isVideoEditing()): ?>
                                    <p class="text-sm font-semibold text-violet-800 mb-3"><i class="fas fa-pen mr-1"></i> تعديل تسليم المونتاج</p>
                                    <form action="<?php echo e(route('employee.tasks.deliverables.update', [$task, $deliverable])); ?>" method="POST" class="space-y-3">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="task_type_context" value="video_editing">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">عنوان التسليم</label>
                                                <input type="text" name="title" value="<?php echo e(old('title', $deliverable->title)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">رابط الفيديو من Bunny <span class="text-red-500">*</span></label>
                                                <input type="url" name="video_link_url" required value="<?php echo e(old('video_link_url', $deliverable->link_url)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">ممن استلمته <span class="text-red-500">*</span></label>
                                                <input type="text" name="received_from" required value="<?php echo e(old('received_from', $deliverable->received_from)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">مدة قبل المونتاج (نص)</label>
                                                <input type="text" name="duration_before" value="<?php echo e(old('duration_before', $deliverable->duration_before)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">مدة بعد المونتاج (نص)</label>
                                                <input type="text" name="duration_after" value="<?php echo e(old('duration_after', $deliverable->duration_after)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">دقائق قبل</label>
                                                <input type="number" name="duration_before_minutes" value="<?php echo e(old('duration_before_minutes', $deliverable->duration_before_minutes)); ?>" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">دقائق بعد</label>
                                                <input type="number" name="duration_after_minutes" value="<?php echo e(old('duration_after_minutes', $deliverable->duration_after_minutes)); ?>" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">ملاحظات</label>
                                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?php echo e(old('description', $deliverable->description)); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-sm font-medium">حفظ التعديلات</button>
                                            <button type="button" onclick="document.getElementById('edit-form-<?php echo e($deliverable->id); ?>').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="text-sm font-semibold text-blue-800 mb-3"><i class="fas fa-pen mr-1"></i> تعديل التسليم</p>
                                    <form action="<?php echo e(route('employee.tasks.deliverables.update', [$task, $deliverable])); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">عنوان التسليم *</label>
                                            <input type="text" name="title" required value="<?php echo e(old('title', $deliverable->title)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">الوصف</label>
                                            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?php echo e(old('description', $deliverable->description)); ?></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">نوع التسليم *</label>
                                            <select name="delivery_type" id="edit_delivery_type_<?php echo e($deliverable->id); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                <option value="file" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'file' ? 'selected' : ''); ?>>ملف</option>
                                                <option value="image" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'image' ? 'selected' : ''); ?>>صورة</option>
                                                <option value="link" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'link' ? 'selected' : ''); ?>>رابط</option>
                                            </select>
                                        </div>
                                        <div id="edit_file_wrap_<?php echo e($deliverable->id); ?>">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">استبدال الملف (اختياري)</label>
                                            <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للإبقاء على الملف الحالي</p>
                                        </div>
                                        <div id="edit_link_wrap_<?php echo e($deliverable->id); ?>">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">الرابط *</label>
                                            <input type="url" name="link_url" value="<?php echo e(old('link_url', $deliverable->link_url)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://">
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">حفظ التعديلات</button>
                                            <button type="button" onclick="document.getElementById('edit-form-<?php echo e($deliverable->id); ?>').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                        </div>
                                    </form>
                                    <script>
                                        (function () {
                                            var sel = document.getElementById('edit_delivery_type_<?php echo e($deliverable->id); ?>');
                                            var fw = document.getElementById('edit_file_wrap_<?php echo e($deliverable->id); ?>');
                                            var lw = document.getElementById('edit_link_wrap_<?php echo e($deliverable->id); ?>');
                                            function sync() {
                                                if (!sel || !fw || !lw) return;
                                                if (sel.value === 'link') { fw.classList.add('hidden'); lw.classList.remove('hidden'); }
                                                else { fw.classList.remove('hidden'); lw.classList.add('hidden'); }
                                            }
                                            if (sel) { sel.addEventListener('change', sync); sync(); }
                                        })();
                                    </script>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium">لا توجد تسليمات حتى الآن</p>
                    <p class="text-sm text-gray-500 mt-1">التسليمات التي تقدمها من النموذج أعلاه ستظهر هنا</p>
                </div>
            <?php endif; ?>
                </div>
            </details>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/tasks/show.blade.php ENDPATH**/ ?>