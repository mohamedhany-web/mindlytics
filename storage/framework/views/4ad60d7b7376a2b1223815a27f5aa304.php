

<?php $__env->startSection('title', 'مركز المساعدة - Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hero-help {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-help::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h40v40H0V0zm2 2h36v36H2V2z'/%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.6;
    }
    .help-card {
        transition: all 0.25s ease;
        border: 2px solid #e2e8f0;
    }
    .help-card:hover {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 12px 32px rgba(59, 130, 246, 0.12);
        transform: translateY(-2px);
    }
    .topic-card {
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .topic-card:hover {
        border-color: rgba(59, 130, 246, 0.25);
        background: rgba(248, 250, 252, 0.8);
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

<section class="hero-help min-h-[42vh] flex items-center relative pt-24 pb-16 lg:pt-28 lg:pb-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
            مركز المساعدة
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto" style="text-shadow: 0 1px 4px rgba(0,0,0,0.2);">
            دليلك للتعلم على منصة Mindlytics والتسجيل والدفع والشهادات
        </p>
    </div>
</section>


<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">
        <div class="flex items-center gap-3 mb-8">
            <div class="section-bar rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800">ابدأ من هنا</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <a href="<?php echo e(route('public.faq')); ?>" class="help-card bg-white rounded-2xl shadow-md p-6 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                    <i class="fas fa-question-circle text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">الأسئلة الشائعة</h3>
                <p class="text-slate-600 text-sm">إجابات جاهزة عن المنصة، التسجيل، الدفع والشهادات</p>
            </a>
            <a href="<?php echo e(route('public.contact')); ?>" class="help-card bg-white rounded-2xl shadow-md p-6 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">تواصل معنا</h3>
                <p class="text-slate-600 text-sm">أرسل استفسارك أو مشكلتك وسنرد في أقرب وقت</p>
            </a>
            <a href="<?php echo e(route('public.courses')); ?>" class="help-card bg-white rounded-2xl shadow-md p-6 flex flex-col items-center text-center no-underline text-inherit">
                <div class="w-14 h-14 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">تصفح الكورسات</h3>
                <p class="text-slate-600 text-sm">اختر كورساً أو مساراً تعليمياً والتحق بالتعلم</p>
            </a>
        </div>

        
        <div class="flex items-center gap-3 mt-14 mb-6">
            <div class="section-bar rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800">مواضيع شائعة</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
            <div class="divide-y divide-slate-100">
                <a href="<?php echo e(route('public.faq')); ?>#default" class="topic-card block px-6 py-5 no-underline text-inherit">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">كيف أُنشئ حساباً وأبدأ التعلم؟</h3>
                            <p class="text-slate-600 text-sm">أنشئ حساباً من صفحة "إنشاء حساب"، ثم تصفح الكورسات أو المسارات واختر ما يناسبك. التسجيل في الكورسات المجانية فوري، والمدفوعة تتطلب إتمام الدفع ثم المراجعة.</p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-1"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('public.faq')); ?>#default" class="topic-card block px-6 py-5 no-underline text-inherit">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">ما طرق الدفع ومتى يُفعّل الكورس؟</h3>
                            <p class="text-slate-600 text-sm">نقبل التحويل البنكي، المحفظة الإلكترونية (فودافون كاش، إنستا باي)، والدفع الإلكتروني. بعد رفع إيصال الدفع تتم المراجعة وتفعيل الكورس تلقائياً خلال 24 ساعة عمل عادةً.</p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-1"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('public.faq')); ?>#default" class="topic-card block px-6 py-5 no-underline text-inherit">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-route"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">ما الفرق بين الكورس والمسار التعليمي؟</h3>
                            <p class="text-slate-600 text-sm">الكورس مادة واحدة (مثلاً لغة برمجة). المسار مجموعة كورسات مرتبة لهدف أكبر (مثل "مطور الويب"). التسجيل في المسار يمنحك الوصول لجميع الكورسات ضمنه.</p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-1"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('public.certificates')); ?>" class="topic-card block px-6 py-5 no-underline text-inherit">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">كيف أحصل على الشهادة؟</h3>
                            <p class="text-slate-600 text-sm">بعد إتمام متطلبات الكورس أو المسار يمكنك تحميل شهادة الإتمام من لوحة التحكم. الشهادات قابلة للمشاركة والتحقق من صحتها عبر صفحة التحقق من الشهادات.</p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-1"></i>
                    </div>
                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="topic-card block px-6 py-5 no-underline text-inherit">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">لم أجد حلاً لمشكلتي، ماذا أفعل؟</h3>
                            <p class="text-slate-600 text-sm">استخدم صفحة "اتصل بنا" لوصف مشكلتك أو سؤالك. نحرص على الرد خلال أقرب وقت. يمكنك أيضاً مراجعة الأسئلة الشائعة للتأكد من وجود إجابة جاهزة.</p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-1"></i>
                    </div>
                </a>
            </div>
        </div>

        
        <div class="flex items-center gap-3 mt-14 mb-6">
            <div class="section-bar rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800">خطوات سريعة</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border-2 border-slate-100 p-5 text-center">
                <span class="inline-flex w-10 h-10 rounded-full bg-blue-600 text-white font-bold items-center justify-center text-lg mb-3">1</span>
                <p class="text-sm font-bold text-slate-800">إنشاء الحساب</p>
                <p class="text-xs text-slate-600 mt-1">من صفحة إنشاء حساب</p>
            </div>
            <div class="bg-white rounded-xl border-2 border-slate-100 p-5 text-center">
                <span class="inline-flex w-10 h-10 rounded-full bg-blue-600 text-white font-bold items-center justify-center text-lg mb-3">2</span>
                <p class="text-sm font-bold text-slate-800">اختيار كورس أو مسار</p>
                <p class="text-xs text-slate-600 mt-1">من الكورسات أو المسارات</p>
            </div>
            <div class="bg-white rounded-xl border-2 border-slate-100 p-5 text-center">
                <span class="inline-flex w-10 h-10 rounded-full bg-blue-600 text-white font-bold items-center justify-center text-lg mb-3">3</span>
                <p class="text-sm font-bold text-slate-800">إتمام الدفع</p>
                <p class="text-xs text-slate-600 mt-1">تحويل أو محفظة أو دفع إلكتروني</p>
            </div>
            <div class="bg-white rounded-xl border-2 border-slate-100 p-5 text-center">
                <span class="inline-flex w-10 h-10 rounded-full bg-blue-600 text-white font-bold items-center justify-center text-lg mb-3">4</span>
                <p class="text-sm font-bold text-slate-800">البدء بالتعلم</p>
                <p class="text-xs text-slate-600 mt-1">بعد تفعيل الطلب من الإدارة</p>
            </div>
        </div>
    </div>
</section>


<section class="py-14 bg-white border-t border-slate-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-2xl">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 mb-4">
            <i class="fas fa-headset text-2xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">ما زلت تحتاج مساعدة؟</h3>
        <p class="text-slate-600 mb-6">فريق الدعم جاهز للرد على استفساراتك. تواصل معنا وسنوضح لك كل شيء.</p>
        <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-600 transition-all">
            <i class="fas fa-envelope"></i>
            اتصل بنا
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/help.blade.php ENDPATH**/ ?>