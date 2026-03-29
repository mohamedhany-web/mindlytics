

<?php $__env->startSection('title', 'تفاصيل المهمة'); ?>
<?php $__env->startSection('header', 'تفاصيل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('import_report') && $employeeTask->task_type === 'video_editing'): ?>
        <?php $rep = session('import_report'); ?>
        <div class="bg-white border border-slate-200 text-gray-800 px-4 py-3 rounded-lg mb-4 text-sm shadow-sm">
            <p class="font-semibold text-gray-900 mb-2">تقرير الاستيراد</p>
            <p>تم استيراد: <strong><?php echo e($rep['imported'] ?? 0); ?></strong></p>
            <?php if(!empty($rep['skipped_duplicates'])): ?>
                <p class="mt-2 text-amber-800">روابط مُتخطاة (مكررة): <?php echo e(count($rep['skipped_duplicates'])); ?></p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-28 overflow-y-auto mt-1">
                    <?php $__currentLoopData = array_slice($rep['skipped_duplicates'], 0, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($line); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <?php if(!empty($rep['row_errors'])): ?>
                <p class="mt-2 text-red-800">أخطاء في الصفوف: <?php echo e(count($rep['row_errors'])); ?></p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-28 overflow-y-auto mt-1">
                    <?php $__currentLoopData = array_slice($rep['row_errors'], 0, 12, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowNum => $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>صف <?php echo e($rowNum); ?>: <?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-tasks"></i>
                        تفاصيل المهمة
                    </span>
                    <?php if($employeeTask->task_type === 'video_editing'): ?>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-violet-100 text-violet-700 text-sm font-semibold mr-2">
                            <i class="fas fa-video"></i> مونتاج فيديو
                        </span>
                    <?php elseif($employeeTask->task_type === 'sales'): ?>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold mr-2">
                            <i class="fas fa-handshake"></i> مبيعات
                        </span>
                    <?php endif; ?>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight"><?php echo e($employeeTask->title); ?></h1>
                    <p class="text-gray-600 text-lg">
                        عرض تفاصيل المهمة المخصصة للموظف
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?php echo e(route('admin.employee-tasks.edit', $employeeTask)); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                    <form action="<?php echo e(route('admin.employee-tasks.destroy', $employeeTask)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المهمة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="p-6 sm:p-8 space-y-6">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">معلومات المهمة</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الموظف -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الموظف</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg"><?php echo e($employeeTask->employee->name); ?></p>
                            <?php if($employeeTask->employee->employeeJob): ?>
                                <p class="text-sm text-gray-600"><?php echo e($employeeTask->employee->employeeJob->name); ?></p>
                            <?php endif; ?>
                            <?php if($employeeTask->employee->employee_code): ?>
                                <p class="text-xs text-gray-500">كود: <?php echo e($employeeTask->employee->employee_code); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- المكلف -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">المكلف</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg"><?php echo e($employeeTask->assigner->name); ?></p>
                        </div>
                    </div>
                </div>

                <!-- الأولوية -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الأولوية</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                        <?php if($employeeTask->priority === 'urgent'): ?> bg-red-100 text-red-800 border-2 border-red-300
                        <?php elseif($employeeTask->priority === 'high'): ?> bg-orange-100 text-orange-800 border-2 border-orange-300
                        <?php elseif($employeeTask->priority === 'medium'): ?> bg-yellow-100 text-yellow-800 border-2 border-yellow-300
                        <?php else: ?> bg-gray-100 text-gray-800 border-2 border-gray-300
                        <?php endif; ?>">
                        <?php if($employeeTask->priority === 'urgent'): ?>
                            <i class="fas fa-exclamation-circle"></i>عاجل
                        <?php elseif($employeeTask->priority === 'high'): ?>
                            <i class="fas fa-arrow-up"></i>عالي
                        <?php elseif($employeeTask->priority === 'medium'): ?>
                            <i class="fas fa-minus"></i>متوسط
                        <?php else: ?>
                            <i class="fas fa-arrow-down"></i>منخفض
                        <?php endif; ?>
                    </span>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">نوع المهمة</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border-2
                        <?php if($employeeTask->task_type === 'video_editing'): ?> bg-violet-50 text-violet-800 border-violet-200
                        <?php elseif($employeeTask->task_type === 'sales'): ?> bg-emerald-50 text-emerald-800 border-emerald-200
                        <?php else: ?> bg-slate-50 text-slate-800 border-slate-200
                        <?php endif; ?>">
                        <?php echo e(\App\Models\EmployeeTask::taskTypeLabel($employeeTask->task_type)); ?>

                    </span>
                </div>

                <!-- الحالة -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الحالة</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold
                        <?php if($employeeTask->status === 'completed'): ?> bg-green-100 text-green-800 border-2 border-green-300
                        <?php elseif($employeeTask->status === 'in_progress'): ?> bg-blue-100 text-blue-800 border-2 border-blue-300
                        <?php elseif($employeeTask->status === 'pending'): ?> bg-yellow-100 text-yellow-800 border-2 border-yellow-300
                        <?php elseif($employeeTask->status === 'cancelled'): ?> bg-red-100 text-red-800 border-2 border-red-300
                        <?php else: ?> bg-gray-100 text-gray-800 border-2 border-gray-300
                        <?php endif; ?>">
                        <?php if($employeeTask->status === 'completed'): ?>
                            <i class="fas fa-check-circle"></i>مكتملة
                        <?php elseif($employeeTask->status === 'in_progress'): ?>
                            <i class="fas fa-spinner fa-spin"></i>قيد التنفيذ
                        <?php elseif($employeeTask->status === 'pending'): ?>
                            <i class="fas fa-clock"></i>معلقة
                        <?php elseif($employeeTask->status === 'cancelled'): ?>
                            <i class="fas fa-times-circle"></i>ملغاة
                        <?php else: ?>
                            <i class="fas fa-pause"></i>معلقة مؤقتاً
                        <?php endif; ?>
                    </span>
                </div>

                <!-- الموعد النهائي -->
                <?php if($employeeTask->deadline): ?>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">الموعد النهائي</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br <?php echo e($employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled']) ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600'); ?> rounded-xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg <?php echo e($employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-900'); ?>">
                                <?php echo e($employeeTask->deadline->format('Y-m-d')); ?>

                            </p>
                            <?php if($employeeTask->deadline < now() && !in_array($employeeTask->status, ['completed', 'cancelled'])): ?>
                                <p class="text-xs text-red-600 font-semibold">متأخرة</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- التقدم -->
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">التقدم</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700"><?php echo e($employeeTask->progress); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: <?php echo e($employeeTask->progress); ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- تاريخ البدء -->
                <?php if($employeeTask->started_at): ?>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ البدء</p>
                    <p class="font-semibold text-gray-900"><?php echo e($employeeTask->started_at->format('Y-m-d H:i')); ?></p>
                </div>
                <?php endif; ?>

                <!-- تاريخ الإكمال -->
                <?php if($employeeTask->completed_at): ?>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-2">تاريخ الإكمال</p>
                    <p class="font-semibold text-gray-900"><?php echo e($employeeTask->completed_at->format('Y-m-d H:i')); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- الوصف -->
            <?php if($employeeTask->description): ?>
            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-3">الوصف</p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-wrap"><?php echo e($employeeTask->description); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- الملاحظات -->
            <?php if($employeeTask->notes): ?>
            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-3">ملاحظات إضافية</p>
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-wrap"><?php echo e($employeeTask->notes); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- التسليمات -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <div class="p-6 sm:p-8 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-4">
                <h2 class="text-xl font-bold text-gray-900">التسليمات</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                        <?php echo e($totalDeliverables); ?> تسليم
                    </span>
                    <?php if($employeeTask->task_type === 'video_editing'): ?>
                        <?php
                            $totBeforeMin = (int) $employeeTask->deliverables()->sum('duration_before_minutes');
                            $totAfterMin = (int) $employeeTask->deliverables()->sum('duration_after_minutes');
                        ?>
                        <span class="text-xs sm:text-sm text-gray-600 bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-full">
                            مجموع الدقائق: قبل <?php echo e($totBeforeMin); ?> · بعد <?php echo e($totAfterMin); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($employeeTask->task_type === 'video_editing'): ?>
                <div class="rounded-xl border border-violet-200 bg-violet-50/40 p-4 space-y-3">
                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-file-excel text-green-600 ml-1"></i> استيراد تسليمات المونتاج من Excel</p>
                    <p class="text-xs text-gray-600">عمود إلزامي: رابط الفيديو (Bunny). لا يُسمح بتكرار نفس الرابط في الملف أو مع تسليمات سابقة.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="<?php echo e(route('admin.employee-tasks.deliverables.montage-excel-template', $employeeTask)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-violet-300 text-violet-800 text-sm font-medium hover:bg-violet-50">
                            <i class="fas fa-download"></i> تنزيل القالب
                        </a>
                    </div>
                    <form action="<?php echo e(route('admin.employee-tasks.deliverables.import-excel', $employeeTask)); ?>" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                        <?php echo csrf_field(); ?>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">ملف Excel</label>
                            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="w-full text-sm file:ml-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-100 file:text-violet-800">
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-file-import ml-1"></i> استيراد
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if($employeeTask->task_type === 'sales'): ?>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-900">
                    <i class="fas fa-info-circle ml-1"></i> مهمة مبيعات: التسليمات كملف / رابط / صورة (نفس المهمة العامة). لمتابعة العملاء المحتملين استخدم
                    <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="font-bold underline hover:text-emerald-950">قسم المبيعات</a>.
                </div>
            <?php endif; ?>

            <!-- بحث في التسليمات -->
            <form method="GET" action="<?php echo e(route('admin.employee-tasks.show', $employeeTask)); ?>" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="deliverables-search" class="sr-only">بحث في التسليمات</label>
                    <div class="relative">
                        <input type="search" name="search" id="deliverables-search" value="<?php echo e(request('search')); ?>"
                               placeholder="بحث في العنوان، الوصف، ممن استلم، الرابط، الملف..."
                               class="w-full rounded-xl border border-gray-200 pl-4 pr-10 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('admin.employee-tasks.show', $employeeTask)); ?>" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-medium transition-colors">
                        إلغاء البحث
                    </a>
                <?php endif; ?>
            </form>

            <?php if($deliverables->count() > 0): ?>
                <p class="text-sm text-gray-500">
                    <?php if(request('search')): ?>
                        عرض <?php echo e($deliverables->firstItem()); ?>–<?php echo e($deliverables->lastItem()); ?> من <?php echo e($deliverables->total()); ?> نتيجة للبحث
                    <?php else: ?>
                        عرض <?php echo e($deliverables->firstItem()); ?>–<?php echo e($deliverables->lastItem()); ?> من <?php echo e($deliverables->total()); ?>

                    <?php endif; ?>
                </p>

                <div class="overflow-x-auto -mx-2">
                    <table class="w-full min-w-[640px] border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200 bg-gray-50/80">
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">#</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">العنوان / النوع</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">ممن استلم</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">المدة قبل/بعد</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">الرابط / الملف</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">الحالة</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide">التاريخ</th>
                                <th class="text-right py-3 px-3 text-xs font-bold text-gray-600 uppercase tracking-wide whitespace-nowrap">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $deliverable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-3 text-sm text-gray-500 font-mono"><?php echo e($deliverables->firstItem() + $index); ?></td>
                                    <td class="py-3 px-3">
                                        <div class="font-semibold text-gray-900"><?php echo e($deliverable->title ?: '—'); ?></div>
                                        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                            <?php if($deliverable->delivery_type === 'link'): ?> bg-purple-100 text-purple-700
                                            <?php elseif($deliverable->delivery_type === 'image'): ?> bg-pink-100 text-pink-700
                                            <?php else: ?> bg-blue-100 text-blue-700
                                            <?php endif; ?>">
                                            <?php if($deliverable->delivery_type === 'link'): ?> <i class="fas fa-link"></i> رابط
                                            <?php elseif($deliverable->delivery_type === 'image'): ?> <i class="fas fa-image"></i> صورة
                                            <?php else: ?> <i class="fas fa-file"></i> ملف
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-sm text-gray-700"><?php echo e($deliverable->received_from ?: '—'); ?></td>
                                    <td class="py-3 px-3 text-sm">
                                        <?php if($deliverable->duration_before || $deliverable->duration_after): ?>
                                            <span class="text-amber-700"><?php echo e($deliverable->duration_before ?: '—'); ?></span>
                                            <span class="text-gray-400 mx-1">/</span>
                                            <span class="text-emerald-700"><?php echo e($deliverable->duration_after ?: '—'); ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-sm max-w-[220px]">
                                        <?php if($deliverable->link_url): ?>
                                            <a href="<?php echo e($deliverable->link_url); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 break-all line-clamp-2">
                                                <?php echo e(Str::limit($deliverable->link_url, 45)); ?> <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        <?php elseif($deliverable->file_path): ?>
                                            <a href="<?php echo e(Storage::url($deliverable->file_path)); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                <?php echo e(Str::limit($deliverable->file_name, 30)); ?> <i class="fas fa-download text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold
                                            <?php if($deliverable->status === 'approved'): ?> bg-green-100 text-green-800
                                            <?php elseif($deliverable->status === 'rejected'): ?> bg-red-100 text-red-800
                                            <?php elseif($deliverable->status === 'submitted'): ?> bg-blue-100 text-blue-800
                                            <?php else: ?> bg-gray-100 text-gray-700
                                            <?php endif; ?>">
                                            <?php if($deliverable->status === 'approved'): ?> معتمد
                                            <?php elseif($deliverable->status === 'rejected'): ?> مرفوض
                                            <?php elseif($deliverable->status === 'submitted'): ?> مقدم
                                            <?php else: ?> معلق
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-xs text-gray-500"><?php echo e($deliverable->created_at->format('Y-m-d H:i')); ?></td>
                                    <td class="py-3 px-3 text-sm">
                                        <div class="flex flex-col gap-1.5 items-stretch min-w-[100px]">
                                            <button type="button" onclick="document.getElementById('admin-edit-<?php echo e($deliverable->id); ?>').classList.toggle('hidden')" class="inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-800 hover:bg-gray-50 text-xs font-medium">
                                                <i class="fas fa-edit"></i> تعديل
                                            </button>
                                            <form action="<?php echo e(route('admin.employee-tasks.deliverables.destroy', [$employeeTask, $deliverable])); ?>" method="POST" onsubmit="return confirm('حذف هذا التسليم نهائياً؟');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-medium">
                                                    <i class="fas fa-trash-alt"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php if($deliverable->feedback): ?>
                                    <tr class="border-b border-gray-100 bg-amber-50/30">
                                        <td colspan="8" class="py-2 px-3 text-sm text-gray-700">
                                            <span class="font-semibold text-amber-800">ملاحظات المراجع:</span> <?php echo e(Str::limit($deliverable->feedback, 120)); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr id="admin-edit-<?php echo e($deliverable->id); ?>" class="hidden border-b border-gray-200 bg-slate-50/80">
                                    <td colspan="8" class="py-4 px-4 text-sm">
                                        <?php if($employeeTask->task_type === 'video_editing'): ?>
                                            <p class="font-semibold text-violet-800 mb-3"><i class="fas fa-pen ml-1"></i> تعديل تسليم المونتاج (إدارة)</p>
                                            <form action="<?php echo e(route('admin.employee-tasks.deliverables.update', [$employeeTask, $deliverable])); ?>" method="POST" class="space-y-3 max-w-4xl">
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
                                                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-sm font-medium">حفظ</button>
                                                    <button type="button" onclick="document.getElementById('admin-edit-<?php echo e($deliverable->id); ?>').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <p class="font-semibold text-blue-800 mb-3"><i class="fas fa-pen ml-1"></i> تعديل التسليم (إدارة)</p>
                                            <form action="<?php echo e(route('admin.employee-tasks.deliverables.update', [$employeeTask, $deliverable])); ?>" method="POST" enctype="multipart/form-data" class="space-y-3 max-w-4xl">
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
                                                    <select name="delivery_type" id="admin_edit_delivery_type_<?php echo e($deliverable->id); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                        <option value="file" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'file' ? 'selected' : ''); ?>>ملف</option>
                                                        <option value="image" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'image' ? 'selected' : ''); ?>>صورة</option>
                                                        <option value="link" <?php echo e(old('delivery_type', $deliverable->delivery_type) === 'link' ? 'selected' : ''); ?>>رابط</option>
                                                    </select>
                                                </div>
                                                <div id="admin_edit_file_wrap_<?php echo e($deliverable->id); ?>">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">استبدال الملف (اختياري)</label>
                                                    <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                    <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للإبقاء على الملف الحالي</p>
                                                </div>
                                                <div id="admin_edit_link_wrap_<?php echo e($deliverable->id); ?>">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">الرابط *</label>
                                                    <input type="url" name="link_url" value="<?php echo e(old('link_url', $deliverable->link_url)); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">حفظ</button>
                                                    <button type="button" onclick="document.getElementById('admin-edit-<?php echo e($deliverable->id); ?>').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                                </div>
                                            </form>
                                            <script>
                                                (function () {
                                                    var sel = document.getElementById('admin_edit_delivery_type_<?php echo e($deliverable->id); ?>');
                                                    var fw = document.getElementById('admin_edit_file_wrap_<?php echo e($deliverable->id); ?>');
                                                    var lw = document.getElementById('admin_edit_link_wrap_<?php echo e($deliverable->id); ?>');
                                                    function sync() {
                                                        if (!sel || !fw || !lw) return;
                                                        if (sel.value === 'link') { fw.classList.add('hidden'); lw.classList.remove('hidden'); }
                                                        else { fw.classList.remove('hidden'); lw.classList.add('hidden'); }
                                                    }
                                                    if (sel) { sel.addEventListener('change', sync); sync(); }
                                                })();
                                            </script>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="pt-4">
                    <?php echo e($deliverables->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-semibold">
                        <?php if(request('search')): ?>
                            لا توجد نتائج للبحث "<?php echo e(request('search')); ?>"
                        <?php else: ?>
                            لا توجد تسليمات حتى الآن
                        <?php endif; ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        <?php if(request('search')): ?>
                            <a href="<?php echo e(route('admin.employee-tasks.show', $employeeTask)); ?>" class="text-blue-600 hover:underline">عرض كل التسليمات</a>
                        <?php else: ?>
                            لم يقم الموظف بتسليم أي ملفات لهذه المهمة
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/employee-tasks/show.blade.php ENDPATH**/ ?>