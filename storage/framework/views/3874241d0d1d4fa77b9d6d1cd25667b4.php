

<?php $__env->startSection('title', 'سياسة الخصوصية - Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hero-legal {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-legal::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h40v40H0V0zm2 2h36v36H2V2z'/%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.6;
    }
    .legal-card {
        transition: all 0.2s ease;
        border: 2px solid #e2e8f0;
    }
    .legal-card:hover {
        border-color: rgba(59, 130, 246, 0.25);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.08);
    }
    .section-bar {
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        border-radius: 2px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="hero-legal min-h-[38vh] flex items-center relative pt-24 pb-14 lg:pt-28 lg:pb-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
            سياسة الخصوصية
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto" style="text-shadow: 0 1px 4px rgba(0,0,0,0.2);">
            نحن ملتزمون بحماية خصوصيتك وبياناتك
        </p>
    </div>
</section>

<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <p class="text-slate-700 text-lg leading-relaxed mb-10">
            في Mindlytics نلتزم بحماية خصوصيتك. توضح هذه السياسة كيفية جمعنا واستخدامنا وحمايتنا لمعلوماتك الشخصية.
        </p>

        <div class="space-y-6">
            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-emerald-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-database"></i>
                    </span>
                    1. المعلومات التي نجمعها
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    نجمع المعلومات التي تقدمها عند التسجيل واستخدام الخدمة، مثل الاسم، البريد الإلكتروني، رقم الهاتف، ومعلومات الدفع عند الاشتراك في كورسات مدفوعة. قد نجمع أيضاً بيانات تقنية مثل عنوان IP ونوع المتصفح لتحسين الخدمة.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-emerald-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tasks"></i>
                    </span>
                    2. كيفية استخدام المعلومات
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    نستخدم المعلومات لتقديم وتحسين خدماتنا، إدارة حسابك، معالجة الطلبات والدفعات، إرسال الإشعارات المهمة، والرد على استفساراتك. لا نستخدم بياناتك لأغراض تسويقية غير مرغوبة دون موافقتك.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-emerald-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-lock"></i>
                    </span>
                    3. حماية المعلومات
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    نتخذ إجراءات أمنية مناسبة (تشفير، جدران نارية، وصول محدود) لحماية معلوماتك من الوصول غير المصرح به أو التعديل أو الكشف. كلمات المرور مخزنة بشكل مشفر.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-emerald-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-share-alt"></i>
                    </span>
                    4. مشاركة المعلومات
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة. قد نشارك بيانات محدودة مع مزودي خدمات ضروريين (مثل معالجة الدفع أو الاستضافة) بموجب اتفاقيات سرية. قد نكشف معلومات إذا اقتضى القانون ذلك.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-emerald-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-cookie"></i>
                    </span>
                    5. الكوكيز والبيانات التقنية
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    نستخدم تقنيات مثل الكوكيز وتخزين الجلسة لتشغيل الموقع وتذكر تفضيلاتك وتحسين تجربة التصفح. يمكنك ضبط متصفحك لرفض الكوكيز، مع العلم أن بعض الميزات قد لا تعمل بشكل كامل.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="py-14 bg-white border-t border-slate-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-2xl">
        <h3 class="text-2xl font-bold text-slate-800 mb-2">أسئلة حول الخصوصية؟</h3>
        <p class="text-slate-600 mb-6">تواصل معنا لأي استفسار متعلق ببياناتك</p>
        <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-600 transition-all">
            <i class="fas fa-envelope"></i>
            تواصل معنا
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/privacy.blade.php ENDPATH**/ ?>