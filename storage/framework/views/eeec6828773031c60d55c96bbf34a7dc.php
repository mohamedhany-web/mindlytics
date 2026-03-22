

<?php $__env->startSection('title', 'تفاصيل المهمة'); ?>
<?php $__env->startSection('header', 'تفاصيل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

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
                <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                    <?php echo e($totalDeliverables); ?> تسليم
                </span>
            </div>

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
                                </tr>
                                <?php if($deliverable->feedback): ?>
                                    <tr class="border-b border-gray-100 bg-amber-50/30">
                                        <td colspan="7" class="py-2 px-3 text-sm text-gray-700">
                                            <span class="font-semibold text-amber-800">ملاحظات المراجع:</span> <?php echo e(Str::limit($deliverable->feedback, 120)); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-tasks\show.blade.php ENDPATH**/ ?>