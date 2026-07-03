<?php $__env->startSection('title', $workshop->title); ?>
<?php $__env->startSection('header', 'تفاصيل الورشة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $stats = $stats ?? ['total' => 0, 'converted' => 0, 'pending_leads' => 0, 'checked_in' => 0, 'email_pending' => 0];
    $leadFilter = $leadFilter ?? 'all';
    $registeredCount = $stats['total'];
    $total = $workshop->max_seats ?: null;
    $remaining = $workshop->remaining_seats;
    $statCards = [
        ['label' => 'إجمالي المسجلين', 'value' => number_format($stats['total']), 'icon' => 'fas fa-users', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        ['label' => 'في انتظار الترحيل', 'value' => number_format($stats['pending_leads']), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ['label' => 'مُرحَّل للمبيعات', 'value' => number_format($stats['converted']), 'icon' => 'fas fa-right-left', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        ['label' => 'تم الحضور', 'value' => number_format($stats['checked_in']), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        ['label' => 'إيميلات معلّقة', 'value' => number_format($stats['email_pending']), 'icon' => 'fas fa-envelope', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i><span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php $transferSummary = session('workshop_transfer_summary'); ?>
    <?php if(is_array($transferSummary) && (!empty($transferSummary['new']) || !empty($transferSummary['existing']) || !empty($transferSummary['already']))): ?>
        <div class="rounded-2xl border border-blue-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    تفاصيل آخر ترحيل
                </h3>
                <?php if(!empty($transferSummary['batch_id'])): ?>
                    <p class="text-xs text-slate-500 mt-1 font-mono">دفعة: <?php echo e($transferSummary['batch_id']); ?></p>
                <?php endif; ?>
            </div>
            <div class="p-5 grid md:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                    <p class="text-xs font-bold text-emerald-800 mb-2">عملاء جدد (<?php echo e(count($transferSummary['new'] ?? [])); ?>)</p>
                    <?php $__empty_1 = true; $__currentLoopData = $transferSummary['new'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-2 py-1 border-b border-emerald-100 last:border-0 text-xs">
                            <span class="font-semibold text-slate-800"><?php echo e($row['name']); ?></span>
                            <span class="text-slate-500"><?php echo e($row['assignee'] ?? '—'); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-slate-500">لا يوجد</p>
                    <?php endif; ?>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-4">
                    <p class="text-xs font-bold text-amber-800 mb-2">موجودون مسبقاً — تم الربط (<?php echo e(count($transferSummary['existing'] ?? [])); ?>)</p>
                    <?php $__empty_1 = true; $__currentLoopData = $transferSummary['existing'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-2 py-1 border-b border-amber-100 last:border-0 text-xs">
                            <span class="font-semibold text-slate-800"><?php echo e($row['name']); ?></span>
                            <a href="<?php echo e(route('admin.sales.leads.show', $row['lead_id'])); ?>" class="text-blue-600 hover:underline"><?php echo e($row['assignee'] ?? 'Lead'); ?></a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-slate-500">لا يوجد</p>
                    <?php endif; ?>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-700 mb-2">مُرحَّلون سابقاً — تم التخطي (<?php echo e(count($transferSummary['already'] ?? [])); ?>)</p>
                    <?php $__empty_1 = true; $__currentLoopData = $transferSummary['already'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="py-1 border-b border-slate-100 last:border-0 text-xs text-slate-600"><?php echo e($name); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-slate-500">لا يوجد</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="px-5 pb-4 text-xs text-slate-500">
                <i class="fas fa-bell text-blue-500 ml-1"></i>
                تم إرسال إشعار لكل موظف مبيعات يوضح من الجدد ومن الموجودين مسبقاً.
            </div>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-2xl font-black text-slate-900"><?php echo e($workshop->title); ?></h2>
                    <?php if($workshop->is_active): ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">نشطة</span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">غير نشطة</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-slate-600">إدارة التسجيلات، التواصل، وترحيل العملاء الجدد فقط إلى فريق المبيعات</p>
                <a href="<?php echo e(route('public.workshops.show', $workshop->slug)); ?>" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-2">
                    <i class="fas fa-external-link-alt"></i>
                    <?php echo e(route('public.workshops.show', $workshop->slug)); ?>

                </a>
                <a href="<?php echo e(route('public.workshops.confirm.show', $workshop->slug)); ?>" target="_blank" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline mt-1">
                    <i class="fas fa-certificate"></i>
                    رابط التأكيد للمشاركين
                </a>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.workshops.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i> القائمة
                </a>
                <a href="<?php echo e(route('admin.workshops.edit', $workshop)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="<?php echo e(route('admin.workshops.export', $workshop)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
                <a href="<?php echo e(route('admin.workshops.confirmations', $workshop)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold">
                    <i class="fas fa-certificate"></i> تأكيد الحضور
                </a>
                <button type="button" @click="$dispatch('open-checkin-modal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    <i class="fas fa-qrcode"></i> حضور QR
                </button>
                <?php if($workshop->is_active): ?>
                    <form action="<?php echo e(route('admin.workshops.deactivate', $workshop)); ?>" method="POST" onsubmit="return confirm('إيقاف الورشة؟');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold">
                            <i class="fas fa-stop-circle"></i> إيقاف
                        </button>
                    </form>
                <?php else: ?>
                    <form action="<?php echo e(route('admin.workshops.activate', $workshop)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold">
                            <i class="fas fa-play-circle"></i> تفعيل
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center">
                            <i class="<?php echo e($card['icon']); ?>"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900"><?php echo e($card['value']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-0 xl:divide-x xl:divide-slate-100">
            
            <aside class="xl:col-span-3 p-5 sm:p-6 space-y-4 border-b xl:border-b-0 border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i> بيانات الورشة
                </h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500">التاريخ</dt>
                        <dd class="font-semibold text-slate-800 mt-0.5">
                            <?php if($workshop->starts_at): ?>
                                <?php echo e($workshop->starts_at->format('Y-m-d H:i')); ?>

                                <?php if($workshop->ends_at): ?>
                                    <span class="block text-xs text-slate-500 font-normal">إلى <?php echo e($workshop->ends_at->format('Y-m-d H:i')); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-slate-400">غير محدد</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">المقاعد</dt>
                        <dd class="font-semibold text-slate-800 mt-0.5">
                            <?php if($total): ?>
                                <?php echo e($registeredCount); ?> / <?php echo e($total); ?>

                                <span class="block text-xs text-slate-500 font-normal">متبقي: <?php echo e($remaining); ?></span>
                            <?php else: ?>
                                غير محدود
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">نوع الحضور</dt>
                        <dd class="mt-1">
                            <?php if($workshop->mode === 'online'): ?>
                                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">أونلاين</span>
                            <?php elseif($workshop->mode === 'offline'): ?>
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">أوفلاين</span>
                            <?php else: ?>
                                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">اختيار الطالب</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
                <?php if($workshop->description): ?>
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-600 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed"><?php echo e($workshop->description); ?></p>
                    </div>
                <?php endif; ?>
            </aside>

            <div class="xl:col-span-9 divide-y divide-slate-100">
                
                <div class="p-5 sm:p-6 bg-gradient-to-br from-blue-50/80 to-white">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <i class="fas fa-right-left text-blue-600"></i>
                                ترحيل للمبيعات (Leads)
                            </h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-xl">
                                يُرحَّل <strong>المسجّلون الجدد فقط</strong>. من سبق ترحيلهم يظهرون بعلامة «مُرحَّل» ولن يُعاد توزيعهم.
                                إن وُجد العميل مسبقاً في Leads يُربَط بالورشة دون إنشاء تكرار.
                                <?php if($stats['pending_leads'] > 0): ?>
                                    <span class="text-amber-700 font-semibold">(<?php echo e($stats['pending_leads']); ?> جاهز للترحيل الآن)</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <form id="convert-to-leads-form" action="<?php echo e(route('admin.workshops.convert-to-leads', $workshop)); ?>" method="POST"
                          class="rounded-xl border border-blue-200 bg-white p-4 space-y-3 max-w-2xl"
                          data-pending="<?php echo e($stats['pending_leads']); ?>">
                        <?php echo csrf_field(); ?>
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-2">موظفو المبيعات</p>
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" id="select-all-reps" class="text-[11px] font-bold text-blue-700 hover:underline">تحديد الكل</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" id="clear-all-reps" class="text-[11px] font-bold text-slate-500 hover:underline">إلغاء التحديد</button>
                            </div>
                            <div id="convert-assigned-to-list" class="max-h-32 overflow-y-auto grid sm:grid-cols-2 gap-1 rounded-lg border border-slate-200 p-2">
                                <?php $__currentLoopData = ($salesReps ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer px-1 py-0.5 rounded hover:bg-slate-50">
                                        <input type="checkbox" name="assigned_to[]" value="<?php echo e($rep->id); ?>" class="convert-rep-checkbox rounded border-slate-300 text-blue-600">
                                        <span><?php echo e($rep->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">مجموعة العملاء (اختياري)</label>
                            <select name="sales_lead_group_id" id="convert-lead-group" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-blue-500/30">
                                <option value="">بدون مجموعة — أو اختر مجموعة مشتركة</option>
                            </select>
                        </div>
                        <button type="submit" <?php if($stats['pending_leads'] === 0): echo 'disabled'; endif; ?>
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2.5 text-sm font-bold text-white shadow-md">
                            <i class="fas fa-share-nodes"></i>
                            <span>ترحيل الجدد فقط (<?php echo e($stats['pending_leads']); ?>)</span>
                        </button>
                    </form>
                </div>

                
                <div class="p-5 sm:p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-paper-plane text-slate-600"></i> التواصل مع المسجلين
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 items-start">
                        <form method="POST" action="<?php echo e(route('admin.workshops.send-acceptance', $workshop)); ?>" class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/50 h-full">
                            <?php echo csrf_field(); ?>
                            <p class="text-xs font-bold text-slate-800"><i class="fas fa-envelope-open-text text-blue-600 ml-1"></i> إيميل القبول</p>
                            <div class="flex flex-wrap gap-3 text-xs">
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="all" checked class="text-blue-600"> الكل</label>
                                <label class="inline-flex items-center gap-1"><input type="radio" name="scope" value="email" class="text-blue-600"> بريد محدد</label>
                            </div>
                            <input type="email" name="email" placeholder="example@mail.com" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-amber-700">متبقي: <?php echo e($emailPendingCount ?? 0); ?></span>
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold">إرسال</button>
                            </div>
                        </form>
                        <?php echo $__env->make('admin.workshops._whatsapp_bulk', ['workshop' => $workshop], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                    <?php echo $__env->make('admin.workshops._whatsapp_manual', ['workshop' => $workshop], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-table text-blue-600"></i>
                            سجل المسجلين
                            <span class="text-xs font-normal text-slate-500">(<?php echo e($registrations->total()); ?> في الصفحة)</span>
                        </h3>
                        <form method="GET" action="<?php echo e(route('admin.workshops.show', $workshop)); ?>" class="flex flex-wrap items-center gap-2">
                            <select name="lead_status" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold">
                                <option value="all" <?php if($leadFilter === 'all'): echo 'selected'; endif; ?>>كل الترحيل</option>
                                <option value="pending" <?php if($leadFilter === 'pending'): echo 'selected'; endif; ?>>في انتظار الترحيل</option>
                                <option value="converted" <?php if($leadFilter === 'converted'): echo 'selected'; endif; ?>>مُرحَّل فقط</option>
                            </select>
                            <select name="attendance_mode" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold">
                                <option value="all" <?php if(($filterMode ?? 'all') === 'all'): echo 'selected'; endif; ?>>كل الحضور</option>
                                <option value="online" <?php if(($filterMode ?? '') === 'online'): echo 'selected'; endif; ?>>أونلاين</option>
                                <option value="offline" <?php if(($filterMode ?? '') === 'offline'): echo 'selected'; endif; ?>>أوفلاين</option>
                            </select>
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold">فلترة</button>
                        </form>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3 text-[11px]">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> مُرحَّل للمبيعات
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> في انتظار الترحيل
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">#</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الترحيل</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الاسم</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التواصل</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">الحضور</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">ملاحظات</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-slate-500">التسجيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $converted = $reg->isConvertedToLead();
                                    ?>
                                    <tr class="<?php echo e($converted ? 'bg-emerald-50/40' : 'hover:bg-slate-50/80'); ?>">
                                        <td class="px-3 py-3 text-xs text-slate-500"><?php echo e($reg->id); ?></td>
                                        <td class="px-3 py-3 text-xs whitespace-nowrap">
                                            <?php if($converted): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-[10px]">
                                                    <i class="fas fa-check"></i> مُرحَّل
                                                </span>
                                                <?php if($reg->converted_to_lead_at): ?>
                                                    <div class="text-[10px] text-emerald-700 mt-1"><?php echo e($reg->converted_to_lead_at->format('m-d H:i')); ?></div>
                                                <?php endif; ?>
                                                <?php if($reg->salesLead): ?>
                                                    <div class="text-[10px] text-slate-600 mt-0.5">
                                                        <?php echo e($reg->salesLead->assignee->name ?? '—'); ?>

                                                    </div>
                                                    <a href="<?php echo e(route('admin.sales.leads.show', $reg->salesLead)); ?>" class="text-[10px] text-blue-600 hover:underline">عرض Lead</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 font-bold text-[10px]">
                                                    <i class="fas fa-clock"></i> جديد
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 font-semibold text-slate-900"><?php echo e($reg->name); ?></td>
                                        <td class="px-3 py-3 text-xs text-slate-700 space-y-1">
                                            <?php if($reg->email): ?>
                                                <div><i class="fas fa-envelope text-slate-400 ml-1"></i><?php echo e($reg->email); ?></div>
                                            <?php endif; ?>
                                            <?php if($reg->phone): ?>
                                                <div><i class="fas fa-phone text-slate-400 ml-1"></i><?php echo e($reg->phone); ?></div>
                                            <?php endif; ?>
                                            <?php if($reg->whatsapp_link_sent_at): ?>
                                                <div class="text-[10px] text-green-700 font-semibold">
                                                    <i class="fab fa-whatsapp"></i> تم التواصل <?php echo e($reg->whatsapp_link_sent_at->format('m-d H:i')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 text-xs">
                                            <?php
                                                $mode = $reg->attendance_mode === 'offline' ? 'أوفلاين' : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—');
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold"><?php echo e($mode); ?></span>
                                            <?php if($reg->checked_in_at): ?>
                                                <div class="text-[10px] text-emerald-600 mt-1"><i class="fas fa-check-circle"></i> <?php echo e($reg->checked_in_at->format('m-d H:i')); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-slate-600 max-w-[140px] truncate" title="<?php echo e($reg->notes); ?>"><?php echo e($reg->notes ?: '—'); ?></td>
                                        <td class="px-3 py-3 text-xs text-slate-500 whitespace-nowrap"><?php echo e(optional($reg->created_at)->format('Y-m-d H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد تسجيلات مطابقة.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($registrations->hasPages()): ?>
                        <div class="mt-4"><?php echo e($registrations->links()); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>


<div x-data="{ open: false, result: '', resultType: 'info' }"
     x-on:open-checkin-modal.window="open = true; result=''; resultType='info';"
     x-cloak x-show="open"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900"><i class="fas fa-qrcode text-indigo-600 ml-1"></i> التأكد من الحضور</h3>
            <button type="button" @click="open=false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-200"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 space-y-3">
            <div id="qr-reader" class="border border-slate-200 rounded-xl overflow-hidden"></div>
            <template x-if="result">
                <div class="text-xs px-3 py-2 rounded-xl" :class="resultType==='success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                    <span x-text="result"></span>
                </div>
            </template>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const allGroups = <?php echo json_encode($salesLeadGroups ?? [], 15, 512) ?>;
    const groupSelect = document.getElementById('convert-lead-group');
    const repCheckboxes = document.querySelectorAll('.convert-rep-checkbox');
    const form = document.getElementById('convert-to-leads-form');

    function selectedRepIds() {
        return Array.from(repCheckboxes).filter(cb => cb.checked).map(cb => Number(cb.value));
    }

    function refreshLeadGroups() {
        if (!groupSelect) return;
        const repIds = selectedRepIds();
        groupSelect.innerHTML = '<option value="">بدون مجموعة — أو اختر مجموعة مشتركة</option>';
        if (repIds.length === 0) return;
        allGroups.forEach(function (g) {
            const members = (g.member_ids || []).map(Number);
            if (!repIds.every(id => members.includes(id))) return;
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name + (g.is_admin_managed ? ' (إدارة)' : '');
            groupSelect.appendChild(opt);
        });
    }

    repCheckboxes.forEach(cb => cb.addEventListener('change', refreshLeadGroups));
    document.getElementById('select-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = true; });
        refreshLeadGroups();
    });
    document.getElementById('clear-all-reps')?.addEventListener('click', () => {
        repCheckboxes.forEach(cb => { cb.checked = false; });
        refreshLeadGroups();
    });
    refreshLeadGroups();

    form?.addEventListener('submit', function (e) {
        if (selectedRepIds().length === 0) {
            e.preventDefault();
            alert('اختر موظف مبيعات واحد على الأقل.');
            return;
        }
        const pending = Number(form.dataset.pending || 0);
        if (pending === 0) {
            e.preventDefault();
            alert('لا يوجد مسجّلون جدد للترحيل — الكل مُرحَّل مسبقاً.');
            return;
        }
        if (!confirm('ترحيل ' + pending + ' مسجّل جديد فقط؟ المُرحَّلون سابقاً لن يُعاد توزيعهم.')) {
            e.preventDefault();
        }
    });
})();
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    window.addEventListener('open-checkin-modal', () => {
        setTimeout(() => {
            const elementId = 'qr-reader';
            if (!document.getElementById(elementId)) return;
            if (window.__qrScanner) {
                try { window.__qrScanner.stop().then(() => window.__qrScanner.clear()); } catch(e) {}
            }
            const qrScanner = new Html5Qrcode(elementId);
            window.__qrScanner = qrScanner;
            qrScanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 220 },
                async (decodedText) => {
                    try {
                        const res = await fetch("<?php echo e(route('admin.workshops.checkin', $workshop)); ?>", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                            body: JSON.stringify({ token: decodedText }),
                        });
                        const data = await res.json();
                        const modal = document.querySelector('[x-on\\:open-checkin-modal]');
                        if (modal?.__x) {
                            modal.__x.$data.resultType = data.status || 'error';
                            modal.__x.$data.result = data.message || 'تمت المعالجة.';
                        }
                    } catch (e) { console.error(e); }
                }, () => {}
            ).catch(err => console.error(err));
        }, 150);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\show.blade.php ENDPATH**/ ?>