<?php $__env->startSection('title', 'تفاصيل الورشة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 lg:p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-people-arrows text-blue-600"></i>
                <span><?php echo e($workshop->title); ?></span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                إدارة الورشة وحجوزات الطلاب، مع إمكانية تحميل بيانات المسجلين كملف Excel (CSV).
            </p>
            <div class="mt-2 text-xs text-slate-500">
                رابط صفحة الحجز:
                <a href="<?php echo e(route('public.workshops.show', $workshop->slug)); ?>" target="_blank" class="text-blue-600 hover:underline">
                    <?php echo e(route('public.workshops.show', $workshop->slug)); ?>

                </a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <?php if($workshop->is_active): ?>
                <form action="<?php echo e(route('admin.workshops.deactivate', $workshop)); ?>" method="POST" onsubmit="return confirm('إيقاف الورشة؟ الرابط العام سيعرض أن الورشة انتهت.');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 hover:bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                        <i class="fas fa-stop-circle"></i>
                        <span>إيقاف الورشة</span>
                    </button>
                </form>
            <?php else: ?>
                <form action="<?php echo e(route('admin.workshops.activate', $workshop)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                        <i class="fas fa-play-circle"></i>
                        <span>تفعيل الورشة</span>
                    </button>
                </form>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.workshops.edit', $workshop)); ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                <i class="fas fa-edit"></i>
                <span>تعديل بيانات الورشة</span>
            </a>
            <a href="<?php echo e(route('admin.workshops.export', $workshop)); ?>"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                <i class="fas fa-file-excel"></i>
                <span>تحميل بيانات المسجلين (CSV)</span>
            </a>
            <form action="<?php echo e(route('admin.workshops.convert-to-leads', $workshop)); ?>" method="POST" class="flex items-center gap-2 flex-wrap" onsubmit="return confirm('تحويل كل تسجيلات الورشة إلى Leads في قسم المبيعات؟ سيتم تخطي المكرر تلقائياً.');">
                <?php echo csrf_field(); ?>
                <select name="assigned_to" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 min-w-[180px]">
                    <option value="">اختر موظف مبيعات</option>
                    <?php $__currentLoopData = ($salesReps ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rep->id); ?>"><?php echo e($rep->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                    <i class="fas fa-right-left"></i>
                    <span>تحويل المسجلين إلى Leads</span>
                </button>
            </form>
            <button type="button"
                    @click="$dispatch('open-checkin-modal')"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md">
                <i class="fas fa-qrcode"></i>
                <span>التأكد من الحضور (QR)</span>
            </button>
            <a href="<?php echo e(route('admin.workshops.index')); ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-2 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-2 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- معلومات الورشة -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                <span>معلومات الورشة</span>
            </h2>
            <dl class="space-y-3 text-sm text-slate-700">
                <div class="flex items-start justify-between gap-3">
                    <dt class="font-semibold text-slate-600">العنوان:</dt>
                    <dd class="text-right flex-1"><?php echo e($workshop->title); ?></dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="font-semibold text-slate-600">الحالة:</dt>
                    <dd class="text-right">
                        <?php if($workshop->is_active): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                نشطة
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                غير نشطة
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="font-semibold text-slate-600">التاريخ:</dt>
                    <dd class="text-right text-sm">
                        <?php if($workshop->starts_at): ?>
                            <div>من <?php echo e($workshop->starts_at->format('Y-m-d H:i')); ?></div>
                            <?php if($workshop->ends_at): ?>
                                <div class="text-xs text-slate-500">إلى <?php echo e($workshop->ends_at->format('Y-m-d H:i')); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-xs text-slate-400">غير محدد</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="font-semibold text-slate-600">المقاعد:</dt>
                    <?php
                        $total = $workshop->max_seats ?: null;
                        $registeredCount = $workshop->registrations()->count();
                        $remaining = $workshop->remaining_seats;
                    ?>
                    <dd class="text-right text-sm">
                        <?php if($total): ?>
                            <div class="font-semibold"><?php echo e($registeredCount); ?> / <?php echo e($total); ?></div>
                            <div class="text-xs text-slate-500">متبقي: <?php echo e($remaining); ?></div>
                        <?php else: ?>
                            <span class="text-xs text-slate-400">غير محدود</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="font-semibold text-slate-600">نوع الحضور:</dt>
                    <dd class="text-right text-sm">
                        <?php if($workshop->mode === 'online'): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                <i class="fas fa-globe"></i>
                                أونلاين (عن بُعد)
                            </span>
                        <?php elseif($workshop->mode === 'offline'): ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                <i class="fas fa-building"></i>
                                في المكان (أوفلاين)
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                <i class="fas fa-exchange-alt"></i>
                                يمكن للطالب اختيار أونلاين أو أوفلاين
                            </span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>

            <?php if($workshop->description): ?>
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-800 mb-1">وصف الورشة</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                        <?php echo e($workshop->description); ?>

                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- جدول المسجلين -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data>
            <!-- قسم إرسال إيميلات القبول -->
            <div class="px-6 pt-4 pb-3 border-b border-slate-100">
                <form method="POST" action="<?php echo e(route('admin.workshops.send-acceptance', $workshop)); ?>" class="flex flex-col md:flex-row items-start md:items-center gap-3">
                    <?php echo csrf_field(); ?>
                    <div class="flex items-center gap-3 text-sm text-slate-700">
                        <span class="font-semibold">إرسال نموذج القبول عبر الإيميل:</span>
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="scope" value="all" class="text-blue-600 border-slate-300 focus:ring-blue-500" checked>
                            <span>لكل المسجلين الذين لديهم إيميل</span>
                        </label>
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="scope" value="email" class="text-blue-600 border-slate-300 focus:ring-blue-500">
                            <span>لبريد معين فقط</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <input type="email" name="email" placeholder="example@mail.com"
                               class="w-full md:w-60 rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                               onfocus="document.querySelectorAll('input[name=&quot;scope&quot;]').forEach(r=>{ if(r.value==='email') r.checked=true; });">
                        <button type="submit"
                                class="inline-flex items-center gap-1 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-md">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>إرسال الإيميل</span>
                        </button>
                    </div>
                    <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2 py-1">
                        المتبقي بدون إرسال: <?php echo e($emailPendingCount ?? 0); ?>

                    </div>
                </form>
            </div>

            <div class="px-6 py-3 border-b border-slate-100 bg-gradient-to-r from-emerald-50/80 to-green-50/50">
                <?php if(empty($whatsappBatchesReady)): ?>
                    <div class="mb-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900 flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                        <p>إرسال الواتساب بالدفعات يحتاج تحديث السيرفر: <code class="bg-white px-1 rounded">php artisan migrate</code></p>
                    </div>
                <?php endif; ?>
                <form id="workshop-whatsapp-form" method="POST" action="<?php echo e(route('admin.workshops.send-whatsapp', $workshop)); ?>" class="space-y-3"
                      onsubmit="return confirmWhatsappSend(this);">
                    <?php echo csrf_field(); ?>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <i class="fab fa-whatsapp text-emerald-600 text-lg"></i>
                            <span>إرسال واتساب للمسجلين</span>
                            <span class="text-xs font-normal text-slate-500">(<?php echo e($whatsappEligibleCount ?? 0); ?> لديهم رقم)</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-400">
                                <input type="checkbox" id="wa-select-all-page" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>تحديد الصفحة</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-emerald-200 cursor-pointer hover:bg-emerald-50">
                                <input type="checkbox" name="select_all" value="1" id="wa-select-all-workshop" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-semibold text-emerald-800">تحديد كل المسجلين (<?php echo e($whatsappEligibleCount ?? 0); ?>)</span>
                            </label>
                            <select name="attendance_mode" id="wa-attendance-filter"
                                    class="rounded-lg border border-slate-200 px-2 py-1.5 text-slate-800 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="all">فلترة: الكل</option>
                                <option value="online">أونلاين فقط</option>
                                <option value="offline">أوفلاين فقط</option>
                            </select>
                        </div>
                    </div>
                    <textarea name="message" rows="3" required maxlength="4096"
                              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/70 focus:border-emerald-500"
                              placeholder="مرحباً {name}،&#10;شكراً لتسجيلك في ورشة «{workshop_title}».&#10;موعد الورشة: {workshop_date}"><?php echo e(old('message')); ?></textarea>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-[11px] text-slate-500">
                            متغيرات: <code class="bg-white px-1 rounded">{name}</code>
                            <code class="bg-white px-1 rounded">{workshop_title}</code>
                            · إرسال آمن عبر Queue — تأخير عشوائي + حد <?php echo e(config('whatsapp.pacing.max_per_day', 320)); ?>/يوم
                        </p>
                        <?php if(!empty($latestWhatsappBatch) && \Illuminate\Support\Facades\Route::has('admin.whatsapp.batches.show')): ?>
                            <a href="<?php echo e(route('admin.whatsapp.batches.show', $latestWhatsappBatch)); ?>"
                               class="text-xs text-emerald-700 hover:underline font-semibold inline-flex items-center gap-1">
                                <i class="fas fa-tasks"></i>
                                آخر دفعة: <?php echo e($latestWhatsappBatch->statusLabel()); ?>

                                (<?php echo e($latestWhatsappBatch->sent_count); ?>/<?php echo e($latestWhatsappBatch->total_count); ?>)
                            </a>
                        <?php endif; ?>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-green-500 hover:from-emerald-700 hover:to-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-500/20">
                            <i class="fab fa-whatsapp"></i>
                            <span>إرسال للمحددين</span>
                            <span id="wa-selected-count" class="bg-white/20 px-2 py-0.5 rounded-lg text-xs">0</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    <span>الطلاب المسجلون</span>
                </h2>
                <div class="flex items-center gap-4 text-xs text-slate-500">
                    <div>
                        إجمالي المسجلين: <span class="font-semibold"><?php echo e($registrations->total()); ?></span>
                    </div>
                    <form method="GET" action="<?php echo e(route('admin.workshops.show', $workshop)); ?>" class="flex items-center gap-1">
                        <span class="text-[11px] text-slate-500">فلترة حسب نوع الحضور:</span>
                        <select name="attendance_mode" onchange="this.form.submit()"
                                class="rounded-lg border border-slate-200 px-2 py-1 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500/70 focus:border-blue-500">
                            <option value="all" <?php echo e(($filterMode ?? 'all') === 'all' ? 'selected' : ''); ?>>الكل</option>
                            <option value="online" <?php echo e(($filterMode ?? 'all') === 'online' ? 'selected' : ''); ?>>أونلاين فقط</option>
                            <option value="offline" <?php echo e(($filterMode ?? 'all') === 'offline' ? 'selected' : ''); ?>>أوفلاين فقط</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-3 text-center w-10">
                                <span class="sr-only">تحديد</span>
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التواصل</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">نوع الحضور</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الملاحظات</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/80 transition-colors <?php echo e($reg->phone ? '' : 'opacity-60'); ?>">
                                <td class="px-3 py-3 text-center">
                                    <?php if($reg->phone): ?>
                                        <input type="checkbox" name="registration_ids[]" value="<?php echo e($reg->id); ?>"
                                               form="workshop-whatsapp-form"
                                               class="wa-reg-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <?php else: ?>
                                        <span class="text-slate-300" title="لا يوجد رقم">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($reg->id); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-800 font-semibold">
                                    <?php echo e($reg->name); ?>

                                </td>
                                <td class="px-4 py-3 text-xs text-slate-700 space-y-1">
                                    <?php if($reg->email): ?>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-envelope text-slate-400"></i>
                                            <a href="mailto:<?php echo e($reg->email); ?>" class="text-blue-600 hover:underline"><?php echo e($reg->email); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($reg->phone): ?>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-phone text-slate-400"></i>
                                            <span><?php echo e($reg->phone); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($reg->whatsapp_link_sent_at): ?>
                                        <div class="inline-flex items-center gap-1 rounded-full bg-green-50 border border-green-200 px-2 py-0.5 text-[10px] font-semibold text-green-700">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>تم إرسال واتساب</span>
                                            <span class="text-green-600/80">(<?php echo e($reg->whatsapp_link_sent_at->format('Y-m-d H:i')); ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-700 space-y-0.5">
                                    <?php
                                        $mode = $reg->attendance_mode === 'offline'
                                            ? 'أوفلاين'
                                            : ($reg->attendance_mode === 'online' ? 'أونلاين' : '—');
                                    ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                                        <i class="fas fa-location-dot text-slate-500"></i>
                                        <?php echo e($mode); ?>

                                    </span>
                                    <?php if($reg->checked_in_at): ?>
                                        <div class="text-[10px] text-emerald-600 flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i>
                                            تم الحضور <?php echo e($reg->checked_in_at->format('Y-m-d H:i')); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-700 max-w-xs">
                                    <?php echo e(Str::limit($reg->notes, 80) ?: '—'); ?>

                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    <?php echo e(optional($reg->created_at)->format('Y-m-d H:i')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500 text-sm">
                                    لا توجد تسجيلات حتى الآن.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100">
                <?php echo e($registrations->links()); ?>

            </div>
        </div>
    </div>
</div>


<div x-data="{ open: false, result: '', resultType: 'info' }"
     x-on:open-checkin-modal.window="open = true; result=''; resultType='info';"
     x-cloak
     x-show="open"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-qrcode text-indigo-600"></i>
                <span>التأكد من الحضور عبر QR</span>
            </h3>
            <button type="button" @click="open=false" class="p-2 rounded-xl text-slate-500 hover:bg-slate-200 hover:text-slate-800">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 min-h-0 p-4 space-y-3">
            <p class="text-xs text-slate-600">
                افتح الكاميرا ووجّهها نحو QR الموجود في إيميل الطالب للتأكد من حضوره. عند قراءة الكود سيتم تسجيل الحضور تلقائياً.
            </p>
            <div id="qr-reader" class="border border-slate-200 rounded-xl overflow-hidden"></div>
            <template x-if="result">
                <div class="text-xs px-3 py-2 rounded-xl"
                     :class="resultType==='success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                              : (resultType==='already' ? 'bg-amber-50 text-amber-700 border border-amber-200'
                              : 'bg-rose-50 text-rose-700 border border-rose-200')">
                    <span x-text="result"></span>
                </div>
            </template>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const selectPage = document.getElementById('wa-select-all-page');
            const selectAllWorkshop = document.getElementById('wa-select-all-workshop');
            const countEl = document.getElementById('wa-selected-count');
            const checkboxes = () => document.querySelectorAll('.wa-reg-checkbox');

            function updateCount() {
                if (selectAllWorkshop?.checked) {
                    countEl.textContent = 'الكل (<?php echo e($whatsappEligibleCount ?? 0); ?>)';
                    return;
                }
                const n = document.querySelectorAll('.wa-reg-checkbox:checked').length;
                countEl.textContent = String(n);
            }

            selectPage?.addEventListener('change', function () {
                checkboxes().forEach(cb => { cb.checked = selectPage.checked; });
                if (selectPage.checked && selectAllWorkshop) {
                    selectAllWorkshop.checked = false;
                }
                updateCount();
            });

            selectAllWorkshop?.addEventListener('change', function () {
                if (selectAllWorkshop.checked) {
                    checkboxes().forEach(cb => { cb.checked = false; });
                    if (selectPage) selectPage.checked = false;
                }
                updateCount();
            });

            checkboxes().forEach(cb => cb.addEventListener('change', function () {
                if (this.checked && selectAllWorkshop) {
                    selectAllWorkshop.checked = false;
                }
                updateCount();
            }));

            window.confirmWhatsappSend = function (form) {
                const all = selectAllWorkshop?.checked;
                const selected = document.querySelectorAll('.wa-reg-checkbox:checked').length;
                if (!all && selected === 0) {
                    alert('يرجى تحديد مشترك واحد على الأقل، أو تفعيل «تحديد كل المسجلين».');
                    return false;
                }
                const n = all ? <?php echo e((int) ($whatsappEligibleCount ?? 0)); ?> : selected;
                return confirm('بدء إرسال ' + n + ' رسالة في الخلفية؟\n\nسيتم توجيهك لصفحة متابعة — من تم ومن فشل.');
            };

            updateCount();
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
                    const config = { fps: 10, qrbox: 220 };
                    qrScanner.start(
                        { facingMode: 'environment' },
                        config,
                        async (decodedText) => {
                            try {
                                const res = await fetch("<?php echo e(route('admin.workshops.checkin', $workshop)); ?>", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ token: decodedText }),
                                });
                                const data = await res.json();
                                const containers = document.querySelectorAll('[x-data]'); // crude; Alpine will handle binding
                                const modal = document.querySelector('[x-on\\\\:open-checkin-modal]');
                                if (modal && modal.__x) {
                                    const state = modal.__x.$data;
                                    state.resultType = data.status || 'error';
                                    state.result = data.message || 'تمت معالجة الطلب.';
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        },
                        (errorMessage) => {
                            // ignore scan errors
                        }
                    ).catch(err => console.error('QR start error', err));
                }, 150);
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/workshops/show.blade.php ENDPATH**/ ?>