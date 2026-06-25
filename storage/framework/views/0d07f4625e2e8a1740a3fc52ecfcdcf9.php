<?php $__env->startSection('title', 'تسجيلات الطلاب - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'تسجيلات الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="enrollmentsPage()">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="<?php echo e(route('admin.offline-courses.index')); ?>" class="hover:text-blue-600">الكورسات الأوفلاين</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.offline-courses.show', $offlineCourse)); ?>" class="hover:text-blue-600"><?php echo e($offlineCourse->title); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">التسجيلات</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">تسجيلات الطلاب: <?php echo e($offlineCourse->title); ?></h1>
                <p class="text-gray-600 mt-1">سعر الكورس: <span class="font-bold text-green-700"><?php echo e(number_format($offlineCourse->price, 2)); ?> ج.م</span></p>
                <p class="text-sm text-gray-500 mt-2 max-w-3xl">
                    المجموعة الواحدة تشترك في <strong>نفس المواعيد والجلسات</strong>؛ ويُفصل فقط <strong>من حضر بالمركز (أوفلاين)</strong> عن <strong>من سجّل على قناة الأونلاين</strong> من حيث السعة والتسجيلات.
                </p>
            </div>
            <a href="<?php echo e(route('admin.offline-courses.show', $offlineCourse)); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>العودة للكورس
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($err); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- تبويب القناة: أوفلاين / أونلاين -->
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200 flex flex-wrap gap-2 items-center">
        <span class="text-sm font-semibold text-gray-600 ml-2">عرض القائمة:</span>
        <a href="<?php echo e(route('admin.offline-courses.enrollments.index', ['offlineCourse' => $offlineCourse, 'channel' => 'offline'])); ?>"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors <?php echo e($channel === 'offline' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
            <i class="fas fa-building ml-1"></i> تسجيلات الحضور (أوفلاين)
            <span class="opacity-90">(<?php echo e($channelCounts['offline']); ?>)</span>
        </a>
        <a href="<?php echo e(route('admin.offline-courses.enrollments.index', ['offlineCourse' => $offlineCourse, 'channel' => 'online'])); ?>"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors <?php echo e($channel === 'online' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
            <i class="fas fa-video ml-1"></i> تسجيلات الأونلاين
            <span class="opacity-90">(<?php echo e($channelCounts['online']); ?>)</span>
        </a>
    </div>

    <!-- تسجيل طالب جديد -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 border-t-4 <?php echo e($channel === 'online' ? 'border-t-indigo-500' : 'border-t-blue-500'); ?>">
        <h2 class="text-lg font-bold text-gray-900 mb-1">
            <i class="fas fa-user-plus text-blue-600 ml-2"></i>تسجيل طالب جديد
            <?php if($channel === 'online'): ?>
                <span class="text-sm font-normal text-indigo-700">— على قناة <strong>الأونلاين</strong> (يُحتسب ضمن سعة الأونلاين للمجموعة)</span>
            <?php else: ?>
                <span class="text-sm font-normal text-blue-800">— على قناة <strong>الحضور بالمركز</strong> (يُحتسب ضمن سعة الحضور)</span>
            <?php endif; ?>
        </h2>
        <form action="<?php echo e(route('admin.offline-courses.enrollments.store', $offlineCourse)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="enrollment_channel" value="<?php echo e($channel); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">البحث بالإيميل</label>
                    <input type="email" x-model="emailSearch" @input="filterStudents()"
                           placeholder="اكتب إيميل الطالب للبحث..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-400 text-xs mt-1">اكتب الإيميل لتصفية قائمة الطلاب</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الطالب <span class="text-red-500">*</span></label>
                    <select name="user_id" id="studentSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" @change="fetchWorkshopPromo($event.target.value)">
                        <option value="">اختر الطالب</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" data-email="<?php echo e($s->email); ?>" data-name="<?php echo e($s->name); ?>"><?php echo e($s->name); ?> (<?php echo e($s->email); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if($students->isEmpty()): ?>
                        <p class="text-amber-600 text-xs mt-1">جميع الطلاب مسجلون بالفعل.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المجموعة <span class="text-red-500">*</span></label>
                    <select name="group_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">اختر المجموعة</option>
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($g->id); ?>">
                                <?php echo e($g->name); ?>

                                <?php if($g->start_date): ?> — يبدأ <?php echo e($g->start_date->format('Y-m-d')); ?> <?php endif; ?>
                                | حضور <?php echo e($g->current_students); ?>/<?php echo e($g->max_students); ?> (<?php echo e($g->offline_enrollments_count ?? 0); ?> سجل)
                                | أونلاين <?php echo e($g->current_students_online); ?>/<?php echo e($g->max_students_online); ?> (<?php echo e($g->online_enrollments_count ?? 0); ?> سجل)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="active" selected>نشط</option>
                        <option value="pending">قيد الانتظار</option>
                    </select>
                </div>
            </div>

            <!-- الدفع -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-money-bill-wave text-green-600 ml-2"></i>تفاصيل الدفع</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الدفع <span class="text-red-500">*</span></label>
                        <select name="payment_type" x-model="paymentType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="full">دفع كامل</option>
                            <option value="partial">دفع جزئي</option>
                            <option value="free">دفع مجاني (بدون مبلغ على الطالب أو الحساب)</option>
                        </select>
                        <p x-show="paymentType === 'full'" class="text-xs text-green-700 mt-1 font-semibold">
                            المبلغ المستحق: <span x-text="formatMoney(netPrice)"></span> ج.م
                        </p>
                    </div>
                    <div x-show="paymentType === 'partial'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المدفوع</label>
                        <input type="number" name="paid_amount" step="0.01" min="0" :max="netPrice"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="0.00">
                        <p class="text-xs text-gray-500 mt-1">الحد الأقصى بعد الخصم: <span x-text="formatMoney(netPrice)"></span> ج.م</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع</label>
                        <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="cash" <?php echo e(old('payment_method', 'cash') === 'cash' ? 'selected' : ''); ?>>نقدي</option>
                            <option value="wallet" <?php echo e(old('payment_method') === 'wallet' ? 'selected' : ''); ?>>تحويل على محفظة</option>
                        </select>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div id="wallet_wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">المحفظة</label>
                        <select name="wallet_id" id="wallet_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">اختر المحفظة</option>
                            <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($wallet->id); ?>" <?php if((string) old('wallet_id') === (string) $wallet->id): echo 'selected'; endif; ?>>
                                    <?php echo e($wallet->name); ?> — <?php echo e(\App\Models\Wallet::typeLabel($wallet->type)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات الدفع</label>
                        <input type="text" name="payment_notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="اختياري">
                    </div>
                </div>

                <!-- الخصم -->
                <div x-show="paymentType !== 'free'" x-cloak class="mt-4 pt-4 border-t border-gray-200">
                    <div x-show="workshopPromo.has_discount" class="mb-4 rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <p class="text-sm font-bold text-violet-900"><i class="fas fa-ticket-alt ml-1"></i> خصم ورشة مفعّل للطالب</p>
                        <p class="text-sm text-violet-800 mt-1">
                            كود <span class="font-mono font-bold" x-text="workshopPromo.promo_code"></span> —
                            خصم <span x-text="formatMoney(workshopPromo.discount_amount)"></span> ج.م
                        </p>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="apply_discount" value="1" x-model="applyDiscount"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-gray-800"><i class="fas fa-tag text-amber-500 ml-1"></i>تطبيق خصم على الاشتراك</span>
                    </label>
                    <div x-show="applyDiscount" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع الخصم</label>
                            <select name="discount_type" x-model="discountType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                                <option value="fixed">مبلغ ثابت (ج.م)</option>
                                <option value="percent">نسبة مئوية (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">قيمة الخصم</label>
                            <input type="number" name="discount_value" x-model="discountValue" step="0.01" min="0"
                                   :max="discountType === 'percent' ? 100 : coursePrice"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                   placeholder="0.00">
                            <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">
                            <p class="text-gray-600">السعر الأصلي: <strong x-text="formatMoney(coursePrice)"></strong> ج.م</p>
                            <p class="text-amber-700">الخصم: <strong x-text="formatMoney(discountAmount)"></strong> ج.م</p>
                            <p class="text-green-800 font-bold mt-1">صافي الاشتراك: <span x-text="formatMoney(netPrice)"></span> ج.م</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors" <?php echo e($students->isEmpty() ? 'disabled' : ''); ?>>
                    <i class="fas fa-plus mr-2"></i>تسجيل الطالب
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2">
            <h2 class="text-lg font-bold text-gray-900">
                قائمة تسجيلات <?php echo e($channel === 'online' ? 'الأونلاين' : 'الحضور (أوفلاين)'); ?>

                (<?php echo e($enrollments->total()); ?>)
            </h2>
            <?php if($channel === 'online'): ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-indigo-100 text-indigo-800">قناة أونلاين</span>
            <?php else: ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-800">قناة حضور بالمركز</span>
            <?php endif; ?>
        </div>
        <?php if($enrollments->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الطالب</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الإيميل</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المجموعة</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">التسجيل</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الحالة</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الإجمالي</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الخصم</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المدفوع</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المتبقي</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">حالة الدفع</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?php echo e($enrollment->student->name ?? '—'); ?></div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs"><?php echo e($enrollment->student->email ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-700"><?php echo e($enrollment->group->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-700"><?php echo e($enrollment->enrolled_at?->format('Y-m-d') ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                    $sLabels = [
                                        'pending' => ['قيد الانتظار', 'bg-amber-100 text-amber-800'],
                                        'active' => ['نشط', 'bg-green-100 text-green-800'],
                                        'completed' => ['منتهي', 'bg-blue-100 text-blue-800'],
                                        'suspended' => ['موقوف', 'bg-red-100 text-red-800'],
                                        'cancelled' => ['ملغي', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $sl = $sLabels[$enrollment->status] ?? ['—', 'bg-gray-100 text-gray-800'];
                                ?>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($sl[1]); ?>"><?php echo e($sl[0]); ?></span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?php echo e(number_format($enrollment->total_amount, 2)); ?></td>
                            <td class="px-4 py-3 text-gray-700">
                                <?php if((float) ($enrollment->discount_amount ?? 0) > 0): ?>
                                    <span class="text-amber-700 font-semibold">-<?php echo e(number_format($enrollment->discount_amount, 2)); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?php echo e(number_format($enrollment->paid_amount, 2)); ?></td>
                            <td class="px-4 py-3 text-gray-700">
                                <?php if((float)$enrollment->remaining_amount > 0): ?>
                                    <span class="text-red-600 font-semibold"><?php echo e(number_format($enrollment->remaining_amount, 2)); ?></span>
                                <?php else: ?>
                                    <span class="text-green-600">0.00</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if((float) $enrollment->total_amount <= 0): ?>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">مجاني</span>
                                <?php else: ?>
                                    <?php
                                        $pLabels = [
                                            'unpaid' => ['لم يدفع', 'bg-red-100 text-red-800'],
                                            'partial' => ['جزئي', 'bg-amber-100 text-amber-800'],
                                            'paid' => ['مكتمل', 'bg-green-100 text-green-800'],
                                        ];
                                        $pl = $pLabels[$enrollment->payment_status] ?? ['—', 'bg-gray-100 text-gray-800'];
                                    ?>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($pl[1]); ?>"><?php echo e($pl[0]); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <!-- تفعيل/إيقاف -->
                                    <form action="<?php echo e(route('admin.offline-courses.enrollments.update-status', [$offlineCourse, $enrollment])); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="status" value="<?php echo e($enrollment->status === 'active' ? 'suspended' : 'active'); ?>">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-blue-50">
                                            <?php echo e($enrollment->status === 'active' ? 'إيقاف' : 'تفعيل'); ?>

                                        </button>
                                    </form>

                                    <?php if((float)$enrollment->remaining_amount > 0): ?>
                                    <!-- دفعة إضافية -->
                                    <button type="button" @click="openPaymentModal(<?php echo e(json_encode($enrollment)); ?>)"
                                            class="text-green-600 hover:text-green-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-green-50">
                                        <i class="fas fa-money-bill-wave"></i> دفعة
                                    </button>
                                    <?php endif; ?>

                                    <!-- حذف -->
                                    <form action="<?php echo e(route('admin.offline-courses.enrollments.destroy', [$offlineCourse, $enrollment])); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-red-50">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($enrollments->hasPages()): ?>
                <div class="px-6 py-3 border-t border-gray-200"><?php echo e($enrollments->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                <p>لا يوجد تسجيلات لهذا الكورس.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- نافذة دفعة إضافية -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showPaymentModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-money-bill-wave text-green-600 ml-2"></i>تسجيل دفعة إضافية</h3>
                <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
                    <p>الطالب: <strong x-text="paymentEnrollment?.student?.name"></strong></p>
                    <p>المبلغ المتبقي: <strong class="text-red-600" x-text="paymentEnrollment?.remaining_amount + ' ج.م'"></strong></p>
                </div>
                <form :action="paymentAction" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" :max="paymentEnrollment?.remaining_amount" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع <span class="text-red-500">*</span></label>
                            <select name="payment_method" x-model="paymentMethodModal" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                <option value="cash">نقدي</option>
                                <option value="wallet">تحويل على محفظة (إيداع في محفظة الأكاديمية)</option>
                            </select>
                        </div>
                        <div x-show="paymentMethodModal === 'wallet'" x-cloak class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">المحفظة <span class="text-red-500">*</span></label>
                            <select name="wallet_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                    :disabled="paymentMethodModal !== 'wallet'"
                                    :required="paymentMethodModal === 'wallet'">
                                <option value="">اختر المحفظة</option>
                                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($wallet->id); ?>"><?php echo e($wallet->name); ?> — <?php echo e(\App\Models\Wallet::typeLabel($wallet->type)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($wallets->isEmpty()): ?>
                                <p class="text-amber-600 text-xs">لا توجد محافظ مفعّلة. أنشئ محفظة من <a href="<?php echo e(route('admin.wallets.index')); ?>" class="font-semibold text-sky-700 underline">إدارة المحافظ</a>.</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showPaymentModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">تسجيل الدفعة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function enrollmentsPage() {
    const allOptions = [];
    const sel = document.getElementById('studentSelect');
    if (sel) {
        for (let i = 1; i < sel.options.length; i++) {
            allOptions.push({
                value: sel.options[i].value,
                text: sel.options[i].text,
                email: sel.options[i].dataset.email || '',
                name: sel.options[i].dataset.name || '',
            });
        }
    }

    return {
        paymentType: 'full',
        coursePrice: <?php echo e((float) $offlineCourse->price); ?>,
        applyDiscount: <?php echo e(old('apply_discount') ? 'true' : 'false'); ?>,
        discountType: <?php echo json_encode(old('discount_type', 'fixed'), 512) ?>,
        discountValue: <?php echo json_encode(old('discount_value', ''), 512) ?>,
        showPaymentModal: false,
        paymentEnrollment: null,
        paymentAction: '',
        paymentMethodModal: 'cash',
        emailSearch: '',
        workshopPromo: { has_discount: false, discount_amount: 0, promo_code: '' },
        offlineCourseId: <?php echo e($offlineCourse->id); ?>,
        promoPreviewUrl: <?php echo json_encode(route('admin.workshop-promo-codes.preview-discount'), 15, 512) ?>,

        get effectiveDiscountAmount() {
            if (this.paymentType === 'free') return 0;
            if (this.applyDiscount) return this.discountAmount;
            if (this.workshopPromo.has_discount) return parseFloat(this.workshopPromo.discount_amount) || 0;
            return 0;
        },

        get discountAmount() {
            if (!this.applyDiscount || this.paymentType === 'free') return 0;
            const value = parseFloat(this.discountValue) || 0;
            if (value <= 0) return 0;
            if (this.discountType === 'percent') {
                const pct = Math.min(100, value);
                return Math.min(this.coursePrice, Math.round(this.coursePrice * pct / 100 * 100) / 100);
            }
            return Math.min(this.coursePrice, Math.round(value * 100) / 100);
        },

        get netPrice() {
            if (this.paymentType === 'free') return 0;
            return Math.max(0, Math.round((this.coursePrice - this.effectiveDiscountAmount) * 100) / 100);
        },

        formatMoney(amount) {
            return (parseFloat(amount) || 0).toFixed(2);
        },

        filterStudents() {
            const q = this.emailSearch.trim().toLowerCase();
            const select = document.getElementById('studentSelect');
            if (!select) return;

            while (select.options.length > 1) select.remove(1);

            const filtered = q === '' ? allOptions : allOptions.filter(o =>
                o.email.toLowerCase().includes(q) || o.name.toLowerCase().includes(q)
            );

            filtered.forEach(o => {
                const opt = new Option(o.text, o.value);
                opt.dataset.email = o.email;
                opt.dataset.name = o.name;
                select.add(opt);
            });

            if (filtered.length === 1) {
                select.value = filtered[0].value;
                this.fetchWorkshopPromo(filtered[0].value);
            }
        },

        async fetchWorkshopPromo(userId) {
            this.workshopPromo = { has_discount: false, discount_amount: 0, promo_code: '' };
            if (!userId) return;
            try {
                const url = new URL(this.promoPreviewUrl, window.location.origin);
                url.searchParams.set('user_id', userId);
                url.searchParams.set('offline_course_id', this.offlineCourseId);
                const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (data.has_discount) {
                    this.workshopPromo = data;
                    this.applyDiscount = false;
                }
            } catch (e) { /* ignore */ }
        },

        openPaymentModal(enrollment) {
            this.paymentEnrollment = enrollment;
            this.paymentMethodModal = 'cash';
            this.paymentAction = "<?php echo e(url('admin/offline-courses/' . $offlineCourse->id . '/enrollments')); ?>/" + enrollment.id + "/payment";
            this.showPaymentModal = true;
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const paymentTypeEl = document.querySelector('select[name="payment_type"]');
    const paymentMethodEl = document.getElementById('payment_method');
    const walletWrapEl = document.getElementById('wallet_wrap');
    const walletSelectEl = document.getElementById('wallet_id');

    function toggleWalletField() {
        const requiresPayment = paymentTypeEl && paymentTypeEl.value !== 'free';
        const isWallet = paymentMethodEl && paymentMethodEl.value === 'wallet';
        if (walletWrapEl) {
            walletWrapEl.style.display = requiresPayment && isWallet ? '' : 'none';
        }
        if (walletSelectEl) {
            walletSelectEl.required = requiresPayment && isWallet;
            if (!requiresPayment || !isWallet) {
                walletSelectEl.value = '';
            }
        }
    }

    if (paymentTypeEl) paymentTypeEl.addEventListener('change', toggleWalletField);
    if (paymentMethodEl) paymentMethodEl.addEventListener('change', toggleWalletField);
    toggleWalletField();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/offline-courses/enrollments/index.blade.php ENDPATH**/ ?>