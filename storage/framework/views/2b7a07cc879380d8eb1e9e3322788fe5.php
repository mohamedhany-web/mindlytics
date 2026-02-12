

<?php $__env->startSection('title', 'سياسة الاسترجاع - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            سياسة الاسترجاع
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            نحن ملتزمون برضاك التام
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed mb-8">
                    نحن ملتزمون برضاك التام. إذا لم تكن راضياً عن خدمتنا، يمكنك طلب استرجاع المبلغ خلال 30 يوماً من تاريخ الشراء.
                </p>
                
                <div class="space-y-8">
                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-list-check text-sky-500 ml-3"></i>
                            شروط الاسترجاع
                        </h2>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-sky-500 ml-3 mt-1"></i>
                                <span>يجب أن يكون الطلب خلال 30 يوماً من تاريخ الشراء</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-sky-500 ml-3 mt-1"></i>
                                <span>لم تكمل أكثر من 50% من محتوى الكورس</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-sky-500 ml-3 mt-1"></i>
                                <span>يجب تقديم سبب واضح للاسترجاع</span>
                            </li>
                        </ul>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-envelope-open text-sky-500 ml-3"></i>
                            كيفية طلب الاسترجاع
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            يمكنك طلب الاسترجاع من خلال التواصل معنا عبر صفحة 
                            <a href="<?php echo e(route('public.contact')); ?>" class="text-sky-600 hover:underline font-semibold">تواصل معنا</a> 
                            أو إرسال بريد إلكتروني إلى دعم العملاء.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clock text-sky-500 ml-3"></i>
                            مدة معالجة الطلب
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            سيتم معالجة طلبك خلال 5-7 أيام عمل. سيتم إرجاع المبلغ إلى نفس طريقة الدفع المستخدمة في الشراء.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">هل تحتاج مساعدة؟</h3>
        <p class="text-gray-600 mb-6">فريقنا جاهز لمساعدتك في أي وقت</p>
        <a href="<?php echo e(route('public.contact')); ?>" class="btn-primary">
            <i class="fas fa-envelope ml-2"></i>
            تواصل معنا
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/refund.blade.php ENDPATH**/ ?>