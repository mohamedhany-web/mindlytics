<?php $__env->startSection('title', 'إدارة الطلبات'); ?>
<?php $__env->startSection('header', 'إدارة الطلبات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        [
            'label' => 'إجمالي الطلبات',
            'value' => number_format($stats['total']),
            'icon' => 'fas fa-shopping-cart',
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'description' => 'كل الطلبات المسجلة في المنصة',
        ],
        [
            'label' => 'طلبات قيد المراجعة',
            'value' => number_format($stats['pending']),
            'icon' => 'fas fa-hourglass-half',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
            'description' => 'بإنتظار الموافقة أو الرفض',
        ],
        [
            'label' => 'طلبات مكتملة',
            'value' => number_format($stats['approved']),
            'icon' => 'fas fa-check-circle',
            'bg' => 'bg-emerald-100',
            'text' => 'text-emerald-600',
            'description' => 'تمت الموافقة عليها بنجاح',
        ],
        [
            'label' => 'طلبات مرفوضة',
            'value' => number_format($stats['rejected']),
            'icon' => 'fas fa-times-circle',
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-600',
            'description' => 'تم رفضها بعد المراجعة',
        ],
    ];

    $statusBadges = [
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'approved' => ['label' => 'مقبولة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'rejected' => ['label' => 'مرفوضة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
    ];

    $paymentMethodLabels = [
        'bank_transfer' => 'تحويل بنكي',
        'wallet' => 'محفظة إلكترونية',
        'online' => 'دفع إلكتروني',
        'cash' => 'نقدي',
        'other' => 'أخرى',
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة إدارة الطلبات</h2>
                    <p class="text-sm text-slate-600 mt-1">متابعة حركة التسجيلات والطلبات المالية عبر المسارات التعليمية.</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> shadow-sm">
                            <i class="<?php echo e($card['icon']); ?> text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600 mt-1">فلترة الطلبات حسب الحالة، طريقة الدفع، أو بيانات الطالب.</p>
        </div>
        <div class="p-6">
            <form method="GET" id="filterForm" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-search text-blue-600 ml-1"></i>
                        البحث
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-3 flex items-center text-blue-500 pointer-events-none"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="<?php echo e(old('search', request('search'))); ?>" maxlength="255" placeholder="اسم الطالب، البريد، الهاتف، أو اسم الكورس" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pl-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-toggle-on text-blue-600 ml-1"></i>
                        الحالة
                    </label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>مقبولة</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>مرفوضة</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-wallet text-blue-600 ml-1"></i>
                        طريقة الدفع
                    </label>
                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الطرق</option>
                        <option value="bank_transfer" <?php echo e(request('payment_method') == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="online" <?php echo e(request('payment_method') == 'online' ? 'selected' : ''); ?>>دفع إلكتروني</option>
                        <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="other" <?php echo e(request('payment_method') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                    <?php if(request()->anyFilled(['search', 'status', 'payment_method'])): ?>
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-black text-slate-900">الطلبات</h3>
                <p class="text-xs text-slate-600 mt-1">آخر الطلبات مرتبة من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200"><?php echo e($orders->total()); ?> طلب</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-6 space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 hover:border-blue-200 hover:bg-white transition-all duration-200 overflow-hidden">
                        <div class="p-5">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                
                                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center rounded-xl
                                    <?php if($order->status === 'pending'): ?> bg-amber-100 text-amber-600
                                    <?php elseif($order->status === 'approved'): ?> bg-emerald-100 text-emerald-600
                                    <?php else: ?> bg-rose-100 text-rose-600
                                    <?php endif; ?>">
                                    <i class="<?php echo e($order->status === 'approved' ? 'fas fa-check' : ($order->status === 'pending' ? 'fas fa-clock' : 'fas fa-times')); ?> text-xl"></i>
                                </div>
                                
                                <div class="flex-1 min-w-0 space-y-2">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-base font-bold text-slate-900"><?php echo e(htmlspecialchars($order->user->name ?? '—')); ?></p>
                                            <p class="text-sm text-slate-600"><?php echo e(htmlspecialchars($order->user->email ?? $order->user->phone ?? '—')); ?></p>
                                        </div>
                                        <?php $badge = $statusBadges[$order->status] ?? null; ?>
                                        <?php if($badge): ?>
                                            <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold <?php echo e($badge['classes']); ?>">
                                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                                <?php echo e($badge['label']); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-600">
                                        <?php if($order->academic_year_id && $order->learningPath): ?>
                                            <span class="font-semibold text-slate-800"><?php echo e(htmlspecialchars($order->learningPath->name ?? 'مسار تعليمي')); ?></span>
                                            <span class="text-blue-600"><i class="fas fa-route ml-1"></i> مسار تعليمي</span>
                                        <?php elseif($order->course): ?>
                                            <span class="font-semibold text-slate-800"><?php echo e(htmlspecialchars($order->course->title ?? 'كورس')); ?></span>
                                            <?php if($order->course->academicYear || $order->course->academicSubject): ?>
                                                <span><?php echo e(optional($order->course->academicYear)->name); ?><?php echo e($order->course->academicSubject ? ' • ' . $order->course->academicSubject->name : ''); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-slate-500">—</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-money-bill-wave text-blue-500"></i>
                                            <strong><?php echo e(number_format($order->amount, 2)); ?></strong> ج.م
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-blue-500"></i>
                                            <?php echo e($order->created_at->format('d/m/Y H:i')); ?>

                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-wallet text-blue-500"></i>
                                            <?php echo e($paymentMethodLabels[$order->payment_method] ?? $order->payment_method ?? '—'); ?>

                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition-colors" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($order->status === 'pending'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.orders.approve', $order)); ?>" class="approve-form inline-block" onsubmit="return confirmApprove(event);">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-emerald-300 bg-white text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-colors" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('admin.orders.reject', $order)); ?>" class="reject-form inline-block" onsubmit="return confirmReject(event);">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-rose-300 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-400 transition-colors" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-900 mb-1">لا توجد طلبات</p>
                        <p class="text-sm text-slate-600">لا توجد طلبات مطابقة لخيارات البحث والفلترة الحالية.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($orders->hasPages()): ?>
                <div class="border-t border-slate-200 px-6 py-4">
                    <?php echo e($orders->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">مؤشرات سريعة</h3>
            <p class="text-xs text-slate-600 mt-1">معدل القبول وطلبات الشهر ومتوسط قيمة الطلب.</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <i class="fas fa-percentage text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-700 mb-1">معدل القبول</p>
                        <p class="text-xl font-black text-emerald-700"><?php echo e($stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0); ?>%</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-700 mb-1">طلبات هذا الشهر</p>
                        <p class="text-xl font-black text-blue-700"><?php echo e(\App\Models\Order::whereMonth('created_at', now()->month)->count()); ?></p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-purple-200 bg-purple-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-purple-700 mb-1">متوسط قيمة الطلب</p>
                        <p class="text-xl font-black text-purple-700"><?php echo e($stats['total'] > 0 ? number_format(\App\Models\Order::avg('amount'), 2) : 0); ?> ج.م</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let formSubmitting = false;

    function confirmApprove(event) {
        if (formSubmitting) { event.preventDefault(); return false; }
        const confirmed = confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للطالب تلقائياً.');
        if (confirmed) {
            formSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
        }
        return confirmed;
    }

    function confirmReject(event) {
        if (formSubmitting) { event.preventDefault(); return false; }
        const confirmed = confirm('هل أنت متأكد من رفض هذا الطلب؟');
        if (confirmed) {
            formSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
        }
        return confirmed;
    }

    var filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            var searchInput = this.querySelector('input[name="search"]');
            if (searchInput) searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
        });
    }

    document.querySelectorAll('.approve-form, .reject-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (formSubmitting) { e.preventDefault(); return false; }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>