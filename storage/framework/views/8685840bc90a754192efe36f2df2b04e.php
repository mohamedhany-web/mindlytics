

<?php $__env->startSection('title', 'الشروط والأحكام - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            الشروط والأحكام
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            يرجى قراءة الشروط والأحكام التالية بعناية
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed mb-8">
                    مرحباً بك في منصة Mindlytics. يرجى قراءة الشروط والأحكام التالية بعناية قبل استخدام الخدمة.
                </p>
                
                <div class="space-y-8">
                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-sky-500 ml-3"></i>
                            1. القبول
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            باستخدامك لهذه المنصة، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق على أي جزء من هذه الشروط، يرجى عدم استخدام الخدمة.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-shield-alt text-sky-500 ml-3"></i>
                            2. استخدام الخدمة
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            يجب عليك استخدام الخدمة لأغراض قانونية فقط. لا يجوز لك استخدام الخدمة لأي غرض غير قانوني أو محظور.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-shield text-sky-500 ml-3"></i>
                            3. الحسابات
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            أنت مسؤول عن الحفاظ على سرية معلومات حسابك وكلمة المرور. أنت توافق على إبلاغنا فوراً بأي استخدام غير مصرح به.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-copyright text-sky-500 ml-3"></i>
                            4. الملكية الفكرية
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            جميع المحتويات والمواد المتاحة على هذه المنصة محمية بحقوق الطبع والنشر والملكية الفكرية.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-edit text-sky-500 ml-3"></i>
                            5. التعديلات
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            نحتفظ بالحق في تعديل هذه الشروط والأحكام في أي وقت. سيتم إشعارك بأي تغييرات جوهرية.
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
        <h3 class="text-2xl font-bold text-gray-900 mb-4">هل لديك استفسار؟</h3>
        <p class="text-gray-600 mb-6">نحن هنا لمساعدتك في أي وقت</p>
        <a href="<?php echo e(route('public.contact')); ?>" class="btn-primary">
            <i class="fas fa-envelope ml-2"></i>
            تواصل معنا
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/terms.blade.php ENDPATH**/ ?>