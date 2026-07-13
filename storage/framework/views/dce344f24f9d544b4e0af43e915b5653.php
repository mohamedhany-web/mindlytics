<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e(__('student.order_details_title')); ?> #<?php echo e($order->id); ?></h1>
                <p class="text-gray-600 mt-1 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-xs"></i>
                    <?php echo e($order->created_at->format('d/m/Y - H:i')); ?>

                </p>
            </div>
            <a href="<?php echo e(route('orders.index')); ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>
                <?php echo e(__('student.back_to_orders')); ?>

            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- تفاصيل الطلب -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات الكورس أو المسار التعليمي -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas <?php echo e($order->academic_year_id ? 'fa-route' : 'fa-book-open'); ?> text-sky-600"></i>
                        <?php echo e($order->academic_year_id ? __('student.learning_path_info') : __('student.course_info')); ?>

                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-24 h-24 bg-gradient-to-br <?php echo e($order->academic_year_id ? 'from-green-500 to-green-600' : 'from-sky-500 to-sky-600'); ?> rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <?php if($order->academic_year_id && $order->learningPath && $order->learningPath->thumbnail): ?>
                                <img src="<?php echo e(asset('storage/' . $order->learningPath->thumbnail)); ?>" alt="<?php echo e($order->learningPath->name ?? 'مسار تعليمي'); ?>" 
                                     class="w-full h-full object-cover rounded-xl">
                            <?php elseif($order->course && $order->course->thumbnail): ?>
                                <img src="<?php echo e(asset('storage/' . $order->course->thumbnail)); ?>" alt="<?php echo e($order->course->title ?? 'كورس'); ?>" 
                                     class="w-full h-full object-cover rounded-xl">
                            <?php else: ?>
                                <i class="fas <?php echo e($order->academic_year_id ? 'fa-route' : 'fa-play-circle'); ?> text-white text-3xl"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-1">
                            <?php if($order->academic_year_id && $order->learningPath): ?>
                                <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo e($order->learningPath->name ?? 'مسار تعليمي'); ?></h3>
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <i class="fas fa-route ml-1 text-xs"></i>
                                        مسار تعليمي
                                    </span>
                                    <?php if($order->learningPath->price): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-money-bill-wave ml-1 text-xs"></i>
                                        <?php echo e(number_format($order->learningPath->price, 2)); ?> ج.م
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php if($order->learningPath->description): ?>
                                    <p class="text-sm text-gray-600 mb-3">
                                        <?php echo e(Str::limit($order->learningPath->description, 120)); ?>

                                    </p>
                                <?php endif; ?>
                                <a href="<?php echo e(route('public.learning-path.show', Str::slug($order->learningPath->name))); ?>" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-medium transition-colors shadow-lg shadow-green-500/30">
                                    <i class="fas fa-eye"></i>
                                    عرض المسار
                                </a>
                            <?php elseif($order->course): ?>
                                <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo e($order->course->title ?? 'كورس غير محدد'); ?></h3>
                                <?php if($order->course->academicYear || $order->course->academicSubject): ?>
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <?php if($order->course->academicYear): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-700">
                                        <i class="fas fa-graduation-cap ml-1 text-xs"></i>
                                        <?php echo e($order->course->academicYear->name); ?>

                                    </span>
                                    <?php endif; ?>
                                    <?php if($order->course->academicSubject): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <i class="fas fa-layer-group ml-1 text-xs"></i>
                                        <?php echo e($order->course->academicSubject->name); ?>

                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if($order->course->description): ?>
                                    <p class="text-sm text-gray-600 mb-3">
                                        <?php echo e(Str::limit($order->course->description, 120)); ?>

                                    </p>
                                <?php endif; ?>
                                <a href="<?php echo e(route('courses.show', $order->course)); ?>" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                                    <i class="fas fa-eye"></i>
                                    عرض الكورس
                                </a>
                            <?php else: ?>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    <?php echo e(\Illuminate\Support\Str::before($order->notes ?? 'طلب بدون عنوان', "\n") ?: 'طلب بدون عنوان'); ?>

                                </h3>
                                <p class="text-sm text-gray-600">هذا طلب حجز كورس أوفلاين/أونلاين.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تفاصيل الدفع -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-credit-card text-sky-600"></i>
                        تفاصيل الدفع
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-gradient-to-br from-sky-50 to-slate-50 rounded-xl border border-sky-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                                <i class="fas fa-money-bill-wave ml-1"></i>
                                المبلغ
                            </label>
                            <div class="text-2xl font-bold bg-gradient-to-r from-sky-600 to-sky-700 bg-clip-text text-transparent">
                                <?php echo e(number_format($order->amount, 2)); ?> <span class="text-base text-gray-600">ج.م</span>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gradient-to-br from-sky-50 to-slate-50 rounded-xl border border-sky-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                                <i class="fas fa-wallet ml-1"></i>
                                طريقة الدفع
                            </label>
                            <div class="text-base font-bold text-gray-900">
                                <?php if($order->payment_method == 'bank_transfer'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <i class="fas fa-university ml-1 text-xs"></i>
                                        تحويل بنكي
                                    </span>
                                <?php elseif($order->payment_method == 'cash'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <i class="fas fa-money-bill ml-1 text-xs"></i>
                                        نقدي
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        <i class="fas fa-question-circle ml-1 text-xs"></i>
                                        أخرى
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gradient-to-br from-sky-50 to-slate-50 rounded-xl border border-sky-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                                <i class="fas fa-calendar-alt ml-1"></i>
                                تاريخ الطلب
                            </label>
                            <div class="text-base font-bold text-gray-900">
                                <?php echo e($order->created_at->format('d/m/Y')); ?>

                                <span class="text-sm text-gray-500 font-normal">- <?php echo e($order->created_at->format('H:i')); ?></span>
                            </div>
                        </div>
                        
                        <?php if($order->approved_at): ?>
                        <div class="p-4 bg-gradient-to-br from-sky-50 to-slate-50 rounded-xl border border-sky-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                                <i class="fas fa-check-circle ml-1"></i>
                                تاريخ الموافقة
                            </label>
                            <div class="text-base font-bold text-gray-900">
                                <?php echo e($order->approved_at->format('d/m/Y')); ?>

                                <span class="text-sm text-gray-500 font-normal">- <?php echo e($order->approved_at->format('H:i')); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if($order->notes): ?>
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-sky-500"></i>
                                ملاحظاتك
                            </label>
                            <div class="text-sm text-gray-700">
                                <?php echo e($order->notes); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- صورة الإيصال -->
            <?php if($order->payment_proof): ?>
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-receipt text-sky-600"></i>
                            إيصال الدفع
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="inline-block p-2 bg-gray-50 rounded-xl border border-gray-200">
                                <?php
                                    $fullPath = storage_path('app/public/' . $order->payment_proof);
                                    $imageExists = file_exists($fullPath);
                                    
                                    $imageUrl = null;
                                    if ($imageExists) {
                                        // استخدام asset أولاً (الرابط الرمزي)
                                        $imageUrl = asset('storage/' . $order->payment_proof);
                                        // إذا كان الرابط الرمزي لا يعمل، استخدم route أو url
                                        if (!file_exists(public_path('storage/' . $order->payment_proof))) {
                                            try {
                                                $imageUrl = route('storage.file', ['path' => $order->payment_proof]);
                                            } catch (\Exception $e) {
                                                $imageUrl = url('/storage/' . $order->payment_proof);
                                            }
                                        }
                                    }
                                ?>
                                <?php if($imageExists): ?>
                                <img src="<?php echo e($imageUrl); ?>" 
                                     alt="إيصال الدفع" 
                                     class="max-w-full h-auto rounded-lg shadow-md cursor-pointer hover:shadow-xl transition-all duration-300"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"
                                     onclick="openImageModal(this.src)">
                                <div class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>الصورة غير متوفرة حالياً</span>
                                    </p>
                                </div>
                                <?php else: ?>
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>الصورة غير موجودة في الخادم</span>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-500 mt-4 flex items-center justify-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                اضغط على الصورة لعرضها بحجم أكبر
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- حالة الطلب -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r 
                    <?php if($order->status == 'pending'): ?> from-amber-50 to-yellow-50
                    <?php elseif($order->status == 'approved'): ?> from-emerald-50 to-green-50
                    <?php else: ?> from-rose-50 to-red-50
                    <?php endif; ?>">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas 
                            <?php if($order->status == 'pending'): ?> fa-clock text-amber-600
                            <?php elseif($order->status == 'approved'): ?> fa-check-circle text-emerald-600
                            <?php else: ?> fa-times-circle text-rose-600
                            <?php endif; ?>"></i>
                        حالة الطلب
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-xl flex items-center justify-center shadow-lg
                            <?php if($order->status == 'pending'): ?> bg-amber-100
                            <?php elseif($order->status == 'approved'): ?> bg-emerald-100
                            <?php else: ?> bg-rose-100
                            <?php endif; ?>">
                            <i class="fas 
                                <?php if($order->status == 'pending'): ?> fa-clock text-amber-600 text-3xl
                                <?php elseif($order->status == 'approved'): ?> fa-check text-emerald-600 text-3xl
                                <?php else: ?> fa-times text-rose-600 text-3xl
                                <?php endif; ?>"></i>
                        </div>
                        
                        <div class="text-xl font-bold mb-2
                            <?php if($order->status == 'pending'): ?> text-amber-600
                            <?php elseif($order->status == 'approved'): ?> text-emerald-600
                            <?php else: ?> text-rose-600
                            <?php endif; ?>">
                            <?php echo e($order->status_text); ?>

                        </div>
                        <p class="text-sm text-gray-500">
                            <?php if($order->status == 'pending'): ?>
                                جاري المراجعة
                            <?php elseif($order->status == 'approved'): ?>
                                تمت الموافقة
                            <?php else: ?>
                                تم الرفض
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if($order->status == 'pending'): ?>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-amber-800 flex items-start gap-2">
                                <i class="fas fa-info-circle mt-0.5"></i>
                                <span>طلبك قيد المراجعة من قبل الإدارة. سيتم الرد عليك قريباً.</span>
                            </p>
                        </div>
                    <?php elseif($order->status == 'approved'): ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-emerald-800 flex items-start gap-2">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <span>تمت الموافقة على طلبك! يمكنك الآن الدخول للكورس.</span>
                            </p>
                        </div>
                        <?php if($order->course): ?>
                        <a href="<?php echo e(route('courses.show', $order->course)); ?>" 
                           class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white py-3 px-4 rounded-lg font-medium transition-colors text-center block shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-arrow-left ml-2"></i>
                            ادخل للكورس
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-rose-800 flex items-start gap-2">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>تم رفض طلبك. يمكنك تقديم طلب جديد أو التواصل مع الإدارة.</span>
                            </p>
                        </div>
                        <?php if($order->course): ?>
                        <a href="<?php echo e(route('courses.show', $order->course)); ?>" 
                           class="w-full bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white py-3 px-4 rounded-lg font-medium transition-colors text-center block shadow-lg shadow-sky-500/30">
                            <i class="fas fa-redo ml-2"></i>
                            تقديم طلب جديد
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($order->approver): ?>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">تمت المراجعة بواسطة:</p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    <?php echo e(substr($order->approver->name ?? 'غير محدد', 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-bold text-gray-900"><?php echo e($order->approver->name ?? 'غير محدد'); ?></p>
                                    <?php if($order->approved_at): ?>
                                        <p class="text-xs text-gray-500"><?php echo e($order->approved_at->format('d/m/Y - H:i')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لعرض الصورة -->
<div id="imageModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="max-w-5xl max-h-[90vh] p-4 relative">
        <button onclick="closeImageModal()" class="absolute -top-12 left-0 text-white hover:text-gray-300 text-3xl font-bold transition-colors">
            <i class="fas fa-times-circle"></i>
        </button>
        <img id="modalImage" src="" alt="إيصال الدفع" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl">
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\orders\show.blade.php ENDPATH**/ ?>