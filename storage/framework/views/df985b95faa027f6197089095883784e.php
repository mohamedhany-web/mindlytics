

<?php $__env->startSection('title', $plan->title . ' — الاستثمار'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('careers._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .invest-input {
        width: 100%;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .invest-input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
    }
    .invest-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.375rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $requirementLines = function (?string $text): array {
        return array_values(array_filter(
            array_map(fn ($line) => trim(ltrim(trim($line), "•·-\t")),
                preg_split('/\r\n|\r|\n/', (string) $text) ?: [])
        ), fn ($line) => $line !== '');
    };
?>

<?php echo $__env->make('careers._hero', [
    'title' => $plan->title,
    'subtitle' => $plan->short_description ?: ($plan->planTypeLabel() . ' · ' . $plan->returnModelLabel()),
    'backUrl' => route('investment.index'),
    'backLabel' => 'جميع الخطط',
    'metaChips' => array_values(array_filter([
        ['label' => $plan->planTypeLabel(), 'icon' => 'fas fa-tag', 'tone' => 'blue'],
        ['label' => 'مخاطر ' . $plan->riskLevelLabel(), 'icon' => 'fas fa-shield-alt', 'tone' => 'green'],
        $plan->duration_months ? ['label' => $plan->duration_months . ' شهر', 'icon' => 'fas fa-calendar', 'tone' => 'violet'] : null,
    ])),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="py-10 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">الحد الأدنى</p>
                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($plan->formattedMinInvestment()); ?></p>
                </div>
            </div>
            <?php if($plan->max_investment): ?>
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-0.5">الحد الأقصى</p>
                        <p class="text-sm font-extrabold text-slate-900"><?php echo e(number_format($plan->max_investment, 0)); ?> <?php echo e($plan->currency); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($plan->expected_return_min): ?>
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-0.5">العائد المتوقع</p>
                        <p class="text-sm font-extrabold text-slate-900"><?php echo e($plan->expected_return_min); ?>% — <?php echo e($plan->expected_return_max); ?>%</p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">نموذج العائد</p>
                    <p class="text-sm font-extrabold text-slate-900"><?php echo e($plan->returnModelLabel()); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/25 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <?php if(session('success')): ?>
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 flex items-start gap-3 shadow-sm">
                <i class="fas fa-check-circle text-2xl text-emerald-600 mt-0.5"></i>
                <div>
                    <p class="font-extrabold text-base mb-1">تم إرسال طلبك بنجاح</p>
                    <p class="text-sm"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-8 rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
                <p class="font-bold mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء في النموذج:</p>
                <ul class="list-disc list-inside space-y-0.5 mr-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-12 gap-8 xl:gap-10 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="text-center lg:text-right mb-2">
                    <span class="careers-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        تفاصيل الفرصة
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">عن الخطة الاستثمارية</h2>
                </div>

                <article class="content-panel">
                    <div class="content-panel-head flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-align-right"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">تفاصيل الفرصة</h3>
                    </div>
                    <div class="p-6 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                        <?php echo nl2br(e($plan->description ?: $plan->short_description)); ?>

                    </div>
                </article>

                <?php if($plan->benefits): ?>
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">المزايا للمستثمر</h3>
                        </div>
                        <div class="p-6 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                            <?php echo nl2br(e($plan->benefits)); ?>

                        </div>
                    </article>
                <?php endif; ?>

                <?php if($plan->process_steps && count($plan->process_steps)): ?>
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                                <i class="fas fa-route"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">خطوات التنفيذ</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <?php $__currentLoopData = $plan->process_steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="step-item">
                                    <span class="step-num"><?php echo e($i + 1); ?></span>
                                    <div>
                                        <?php if(!empty($step['title'])): ?>
                                            <p class="font-bold text-slate-900 text-sm"><?php echo e($step['title']); ?></p>
                                        <?php endif; ?>
                                        <?php if(!empty($step['description'])): ?>
                                            <p class="text-sm text-slate-600 mt-0.5"><?php echo e($step['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </article>
                <?php endif; ?>

                <article class="content-panel" id="terms">
                    <div class="content-panel-head flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">الإطار القانوني</h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm text-slate-700">
                        <?php if($plan->legal_notes): ?>
                            <p class="whitespace-pre-line leading-relaxed"><?php echo nl2br(e($plan->legal_notes)); ?></p>
                        <?php else: ?>
                            <ul class="req-list">
                                <?php $__currentLoopData = $requirementLines($policy->legal_framework); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($line); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                        <?php if($plan->terms_summary): ?>
                            <div class="pt-4 border-t border-slate-100">
                                <h4 class="font-bold text-slate-900 mb-2">شروط الخطة</h4>
                                <p class="whitespace-pre-line leading-relaxed"><?php echo nl2br(e($plan->terms_summary)); ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="pt-4 border-t border-slate-100">
                            <h4 class="font-bold text-slate-900 mb-2">إخلاء المسؤولية</h4>
                            <p class="whitespace-pre-line leading-relaxed text-slate-600"><?php echo nl2br(e($policy->disclaimer)); ?></p>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="lg:col-span-5 lg:sticky lg:top-28 space-y-6">
                <div class="text-center lg:text-right mb-2">
                    <span class="careers-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-3">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                        تقديم طلب
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">قدّم طلب استثمار</h2>
                    <p class="text-slate-600 mt-3 text-sm">سيتواصل معك فريق Mindlytics خلال 3–5 أيام عمل</p>
                </div>

                <div class="content-panel">
                    <div class="p-5 sm:p-6">
                        <form method="POST" action="<?php echo e(route('investment.apply', $plan->slug)); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label class="invest-label">الاسم الكامل <span class="text-rose-500">*</span></label>
                                <input type="text" name="full_name" value="<?php echo e(old('full_name')); ?>" required class="invest-input" placeholder="اسمك الكامل">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="invest-label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="invest-input" placeholder="name@email.com" dir="ltr">
                                </div>
                                <div>
                                    <label class="invest-label">رقم الهاتف <span class="text-rose-500">*</span></label>
                                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required class="invest-input" placeholder="01xxxxxxxxx" dir="ltr">
                                </div>
                            </div>
                            <div>
                                <label class="invest-label">نوع المستثمر</label>
                                <select name="investor_type" class="invest-input">
                                    <?php $__currentLoopData = \App\Models\InvestmentInquiry::investorTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($val); ?>" <?php if(old('investor_type', 'individual') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="invest-label">اسم الشركة <span class="text-slate-400 font-normal">(اختياري)</span></label>
                                <input type="text" name="company_name" value="<?php echo e(old('company_name')); ?>" class="invest-input" placeholder="للمستثمرين المؤسسيين">
                            </div>
                            <div>
                                <label class="invest-label">المبلغ المقترح (<?php echo e($plan->currency); ?>)</label>
                                <input type="number" step="0.01" min="0" name="proposed_amount" value="<?php echo e(old('proposed_amount')); ?>" class="invest-input" placeholder="مثال: <?php echo e(number_format($plan->min_investment, 0)); ?>">
                            </div>
                            <div>
                                <label class="invest-label">رسالتك أو أسئلتك</label>
                                <textarea name="message" rows="4" class="invest-input resize-y min-h-[100px]" placeholder="أخبرنا عن أهدافك الاستثمارية…"><?php echo e(old('message')); ?></textarea>
                            </div>
                            <label class="flex items-start gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="accept_terms" value="1" required class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>أوافق على <a href="#terms" class="text-blue-700 font-bold hover:underline">الشروط والأحكام</a> وإخلاء المسؤولية</span>
                            </label>
                            <button type="submit" class="careers-btn-submit w-full">
                                <i class="fas fa-paper-plane"></i>
                                إرسال طلب الاستثمار
                            </button>
                        </form>
                    </div>
                </div>

                <?php if($plan->eligibility_criteria): ?>
                    <div class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">شروط الأهلية</h3>
                        </div>
                        <div class="p-5">
                            <ul class="req-list text-sm">
                                <?php $__currentLoopData = $requirementLines($plan->eligibility_criteria); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($line); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\investment\show.blade.php ENDPATH**/ ?>