

<?php $__env->startSection('title', $plan->title . ' — الاستثمار'); ?>

<?php $__env->startSection('content'); ?>
<section class="pt-28 pb-8 bg-gradient-to-br from-amber-700 to-orange-600 text-white">
    <div class="container mx-auto px-4">
        <a href="<?php echo e(route('investment.index')); ?>" class="text-amber-100 text-sm hover:text-white mb-3 inline-block"><i class="fas fa-arrow-right ml-1"></i> كل الخطط</a>
        <h1 class="text-3xl md:text-4xl font-black"><?php echo e($plan->title); ?></h1>
        <p class="text-amber-100 mt-2"><?php echo e($plan->planTypeLabel()); ?> · <?php echo e($plan->returnModelLabel()); ?></p>
    </div>
</section>

<div class="container mx-auto px-4 py-10 grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <?php if(session('success')): ?>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
                <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
            </div>
        <?php endif; ?>

        <article class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-black mb-4">تفاصيل الفرصة</h2>
            <p class="text-slate-700 whitespace-pre-wrap leading-relaxed"><?php echo e($plan->description ?: $plan->short_description); ?></p>
            <?php if($plan->benefits): ?>
                <h3 class="font-bold mt-6 mb-2">المزايا</h3>
                <p class="text-slate-700 whitespace-pre-wrap"><?php echo e($plan->benefits); ?></p>
            <?php endif; ?>
            <?php if($plan->process_steps): ?>
                <h3 class="font-bold mt-6 mb-3">خطوات التنفيذ</h3>
                <ol class="space-y-2">
                    <?php $__currentLoopData = $plan->process_steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex gap-3 text-sm">
                            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-bold shrink-0"><?php echo e($i + 1); ?></span>
                            <div><strong><?php echo e($step['title'] ?? ''); ?></strong><p class="text-slate-600"><?php echo e($step['description'] ?? ''); ?></p></div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            <?php endif; ?>
        </article>

        <article class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-black mb-4">الإطار القانوني</h2>
            <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($plan->legal_notes ?: $policy->legal_framework); ?></p>
            <?php if($plan->terms_summary): ?>
                <h3 class="font-bold mt-4 mb-2">شروط الخطة</h3>
                <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($plan->terms_summary); ?></p>
            <?php endif; ?>
        </article>
    </div>

    <aside class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 text-sm space-y-2 sticky top-24">
            <p><span class="text-slate-500">الحد الأدنى</span><br><strong class="text-lg"><?php echo e($plan->formattedMinInvestment()); ?></strong></p>
            <?php if($plan->max_investment): ?><p><span class="text-slate-500">الحد الأقصى</span><br><strong><?php echo e(number_format($plan->max_investment, 0)); ?> <?php echo e($plan->currency); ?></strong></p><?php endif; ?>
            <?php if($plan->duration_months): ?><p><span class="text-slate-500">المدة</span><br><strong><?php echo e($plan->duration_months); ?> شهر</strong></p><?php endif; ?>
            <p><span class="text-slate-500">المخاطر</span><br><strong><?php echo e($plan->riskLevelLabel()); ?></strong></p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6" id="apply">
            <h2 class="text-lg font-black mb-4">قدّم طلب استثمار</h2>
            <form method="POST" action="<?php echo e(route('investment.apply', $plan->slug)); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="text" name="full_name" value="<?php echo e(old('full_name')); ?>" required placeholder="الاسم الكامل *" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required placeholder="البريد الإلكتروني *" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm dir-ltr">
                <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required placeholder="رقم الهاتف *" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm dir-ltr">
                <select name="investor_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <?php $__currentLoopData = \App\Models\InvestmentInquiry::investorTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(old('investor_type') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="text" name="company_name" value="<?php echo e(old('company_name')); ?>" placeholder="اسم الشركة (اختياري)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="number" step="0.01" min="0" name="proposed_amount" value="<?php echo e(old('proposed_amount')); ?>" placeholder="المبلغ المقترح (<?php echo e($plan->currency); ?>)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <textarea name="message" rows="3" placeholder="رسالتك أو أسئلتك" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><?php echo e(old('message')); ?></textarea>
                <label class="flex items-start gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="accept_terms" value="1" required class="mt-1 rounded border-slate-300">
                    <span>أوافق على <a href="#terms" class="text-amber-700 underline">الشروط والأحكام</a> وإخلاء المسؤولية</span>
                </label>
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-xl transition-colors">إرسال الطلب</button>
            </form>
        </div>

        <div id="terms" class="bg-amber-50 rounded-2xl p-5 text-xs text-amber-950 whitespace-pre-wrap border border-amber-100">
            <strong>الشروط:</strong><br><?php echo e($policy->terms_conditions); ?><br><br><strong>إخلاء المسؤولية:</strong><br><?php echo e($policy->disclaimer); ?>

        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\investment\show.blade.php ENDPATH**/ ?>