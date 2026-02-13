

<?php $__env->startSection('title', 'تفاصيل المحفظة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
        <!-- العنوان والهوية -->
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 text-white rounded-3xl shadow-2xl p-8 relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
                <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            </div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-3xl font-black tracking-tight"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h1>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                                <i class="fas fa-tag text-xs"></i>
                                <?php echo e($wallet->type_name); ?>

                            </span>
                            <?php if($wallet->is_active): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-emerald-100">
                                    <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                                    نشطة
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-rose-100">
                                    <span class="w-2 h-2 rounded-full bg-rose-300"></span>
                                    غير نشطة
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-white/70 mt-3">
                            حساب مرتبط بالطالب: <?php echo e($wallet->user?->name ?? 'غير محدد'); ?> — <?php echo e($wallet->user?->phone ?? 'بدون رقم'); ?>

                        </p>
                        <?php if($metrics['last_transaction_at'] ?? null): ?>
                            <p class="text-white/60 text-sm mt-2">
                                آخر حركة <?php echo e($metrics['last_transaction_at']->diffForHumans()); ?> (
                                <?php echo e($metrics['last_transaction_type'] === 'deposit' ? 'إيداع' : 'سحب'); ?> )
                            </p>
                        <?php else: ?>
                            <p class="text-white/60 text-sm mt-2">لا توجد تعاملات مسجلة بعد.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 justify-end">
                    <a href="<?php echo e(route('admin.wallets.transactions', $wallet)); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-receipt"></i>
                        سجل المعاملات
                    </a>
                    <a href="<?php echo e(route('admin.wallets.reports', $wallet)); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                        <i class="fas fa-chart-line"></i>
                        تقارير مالية
                    </a>
                    <a href="<?php echo e(route('admin.wallets.edit', $wallet)); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                        <i class="fas fa-edit"></i>
                        تعديل البيانات
                    </a>
                </div>
            </div>
        </div>

        <!-- البطاقات الإحصائية -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6 relative overflow-hidden">
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-sky-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-sky-500">الرصيد الحالي</p>
                        <p class="text-3xl font-black text-gray-900 mt-2"><?php echo e(number_format($wallet->balance, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                        <i class="fas fa-coins"></i>
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-4">الرصيد المتاح حالياً بعد آخر حركة.</p>
            </div>
            <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6 relative overflow-hidden">
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-emerald-500">إجمالي الإيداعات</p>
                        <p class="text-3xl font-black text-gray-900 mt-2"><?php echo e(number_format($metrics['total_deposits'] ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-arrow-down"></i>
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-4">جميع المبالغ المضافة منذ إنشاء المحفظة.</p>
            </div>
            <div class="rounded-2xl bg-white shadow-lg border border-rose-100 p-6 relative overflow-hidden">
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-rose-500">إجمالي السحوبات</p>
                        <p class="text-3xl font-black text-gray-900 mt-2"><?php echo e(number_format($metrics['total_withdrawals'] ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-rose-100 text-rose-600">
                        <i class="fas fa-arrow-up"></i>
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-4">جميع المبالغ المسحوبة من المحفظة.</p>
            </div>
            <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6 relative overflow-hidden">
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-purple-500">صافي التدفقات</p>
                        <p class="text-3xl font-black text-gray-900 mt-2">
                            <?php echo e(number_format($metrics['net_flow'] ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                        </p>
                    </div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                        <i class="fas fa-balance-scale"></i>
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-4">الفرق بين الإيداعات والسحوبات.</p>
            </div>
        </div>

        <!-- ملخص شهري وتفاصيل المحفظة -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-black text-gray-900">ملخص <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></h2>
                            <p class="text-sm text-gray-500 mt-1">نظرة سريعة على نشاط الشهر الحالي.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                            <i class="fas fa-history"></i>
                            <?php echo e($metrics['transactions_count']); ?> معاملات إجمالية
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl border border-emerald-100 bg-emerald-50/80">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-emerald-600">إيداعات الشهر</p>
                                <i class="fas fa-plus text-emerald-500"></i>
                            </div>
                            <p class="mt-3 text-2xl font-black text-gray-900">
                                <?php echo e(number_format($metrics['current_month_deposits'] ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                            </p>
                        </div>
                        <div class="p-5 rounded-2xl border border-rose-100 bg-rose-50/80">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-rose-600">سحوبات الشهر</p>
                                <i class="fas fa-minus text-rose-500"></i>
                            </div>
                            <p class="mt-3 text-2xl font-black text-gray-900">
                                <?php echo e(number_format($metrics['current_month_withdrawals'] ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h2 class="text-xl font-black text-gray-900 mb-6">تفاصيل المحفظة</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">صاحب الحساب</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e($wallet->account_holder ?? $wallet->user?->name ?? 'غير محدد'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">رقم الحساب / المحفظة</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e($wallet->account_number ?? 'غير متوفر'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">اسم البنك</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e($wallet->bank_name ?? 'غير محدد'); ?></p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">الرصيد المعلق</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e(number_format($wallet->pending_balance ?? 0, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">تاريخ الإنشاء</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e(optional($wallet->created_at)->format('Y-m-d') ?? 'غير متوفر'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">آخر تحديث</p>
                                <p class="mt-2 text-sm font-semibold text-gray-900"><?php echo e(optional($wallet->updated_at)->diffForHumans() ?? 'غير متوفر'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if($wallet->notes): ?>
                        <div class="mt-6 p-4 rounded-2xl border border-sky-100 bg-sky-50/80">
                            <p class="text-xs text-sky-600 font-semibold mb-2">ملاحظات إدارية</p>
                            <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($wallet->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 h-full">
                    <h2 class="text-lg font-black text-gray-900 mb-4">آخر المعاملات</h2>
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-gray-50/60 hover:border-sky-200 hover:shadow-lg transition-all">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl <?php echo e($transaction->type === 'deposit' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'); ?>">
                                    <i class="fas <?php echo e($transaction->type === 'deposit' ? 'fa-arrow-down' : 'fa-arrow-up'); ?>"></i>
                                </span>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-gray-900">
                                            <?php echo e($transaction->type === 'deposit' ? 'إيداع' : 'سحب'); ?>

                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo e($transaction->created_at?->format('Y-m-d H:i')); ?>

                                        </p>
                                    </div>
                                    <p class="mt-2 text-base font-black text-gray-900"><?php echo e(number_format($transaction->amount, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        الرصيد بعد العملية: <?php echo e(number_format($transaction->balance_after, 2)); ?> <?php echo e($wallet->currency ?? 'ج.م'); ?>

                                    </p>
                                    <?php if($transaction->notes): ?>
                                        <p class="text-xs text-gray-600 mt-2 leading-relaxed"><?php echo e($transaction->notes); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-3"></i>
                                <p>لا توجد معاملات مسجلة لهذه المحفظة حتى الآن.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="<?php echo e(route('admin.wallets.transactions', $wallet)); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 hover:text-sky-800 transition-all">
                            عرض سجل المعاملات بالكامل
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/wallets/show.blade.php ENDPATH**/ ?>