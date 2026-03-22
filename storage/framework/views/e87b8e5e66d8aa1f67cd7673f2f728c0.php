

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

            <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/60">
                <form method="POST" action="<?php echo e(route('admin.workshops.send-whatsapp', $workshop)); ?>" class="flex flex-col gap-3">
                    <?php echo csrf_field(); ?>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700">
                        <span class="font-semibold">إرسال رسالة واتساب:</span>
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="scope" value="all" class="text-green-600 border-slate-300 focus:ring-green-500" checked>
                            <span>لكل الأرقام</span>
                        </label>
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="scope" value="phone" class="text-green-600 border-slate-300 focus:ring-green-500">
                            <span>رقم معين</span>
                        </label>
                        <input type="text" name="phone" placeholder="2010xxxxxxx"
                               class="w-full md:w-52 rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500/70 focus:border-green-500"
                               onfocus="document.querySelectorAll('input[name=&quot;scope&quot;]').forEach(r=>{ if(r.value==='phone') r.checked=true; });">
                        <select name="attendance_mode"
                                class="w-full md:w-40 rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500/70 focus:border-green-500">
                            <option value="all">فلترة: الكل</option>
                            <option value="online">فلترة: أونلاين</option>
                            <option value="offline">فلترة: أوفلاين</option>
                        </select>
                    </div>
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
                        <textarea name="message" rows="2" required
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500/70 focus:border-green-500"
                                  placeholder="اكتب رسالة واتساب جاهزة (مثال: أهلاً بكم في الورشة، رابط جروب الواتساب: https://chat.whatsapp.com/...)"></textarea>
                        <button type="submit"
                                class="inline-flex items-center gap-1 rounded-xl bg-green-600 hover:bg-green-700 px-4 py-2 text-xs font-semibold text-white shadow-md whitespace-nowrap">
                            <i class="fab fa-whatsapp"></i>
                            <span>فتح واتساب</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-500">سيتم فتح تبويب/تبويبات واتساب جاهزة لكل رقم مع الرسالة المكتوبة.</p>
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
                            <tr class="hover:bg-slate-50/80 transition-colors">
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
                                            <span>تم إرسال رابط الواتساب</span>
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
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-sm">
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


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\show.blade.php ENDPATH**/ ?>