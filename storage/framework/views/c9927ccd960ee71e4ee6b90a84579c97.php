

<?php $__env->startSection('title', 'الاستثمار في Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('careers._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .info-tile {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 1rem;
        border: 1.5px solid rgba(226, 232, 240, 0.9);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .info-tile:hover {
        border-color: rgba(59, 130, 246, 0.25);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.08);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $contact = \App\Support\PlatformSettings::contactPage();
    $policyLines = function (?string $text): array {
        return array_values(array_filter(
            array_map(fn ($line) => trim(ltrim(trim($line), "•·-\t")),
                preg_split('/\r\n|\r|\n/', (string) $text) ?: [])
        ), fn ($line) => $line !== '');
    };
?>

<?php echo $__env->make('careers._hero', [
    'badge' => 'استثمر في مستقبل التعليم التقني',
    'title' => 'الاستثمار في Mindlytics',
    'subtitle' => 'فرص استثمارية في أكاديمية البرمجة والتعليم التقني — بشفافية كاملة وإطار قانوني واضح',
    'metaChips' => [
        ['label' => 'شفافية في العوائد', 'icon' => 'fas fa-chart-line', 'tone' => 'blue'],
        ['label' => 'إطار قانوني مصري', 'icon' => 'fas fa-gavel', 'tone' => 'green'],
        ['label' => 'مراجعة خلال 3–5 أيام', 'icon' => 'fas fa-clock', 'tone' => 'violet'],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<section class="py-8 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card p-5 text-center">
                <div class="text-3xl font-black text-blue-600 mb-1 tabular-nums"><?php echo e($plans->count()); ?></div>
                <div class="text-sm font-semibold text-slate-600">خطة استثمارية متاحة</div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">امتثال قانوني وخصوصية بيانات</div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">شراكات استراتيجية في التعليم</div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/30 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <?php if(session('success')): ?>
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <div class="text-center mb-10 md:mb-12">
            <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold mb-4">
                <i class="fas fa-lightbulb text-blue-600"></i>
                لماذا Mindlytics؟
            </span>
            <h2 class="section-title text-2xl md:text-3xl font-extrabold text-blue-900">نظرة عامة</h2>
        </div>

        <article class="content-panel mb-10">
            <div class="content-panel-head flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">عن قسم الاستثمار</h3>
            </div>
            <div class="p-6 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                <?php echo nl2br(e($policy->overview)); ?>

            </div>
        </article>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-14">
            <article class="content-panel h-full">
                <div class="content-panel-head flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">قواعد الأهلية</h3>
                </div>
                <div class="p-6">
                    <ul class="req-list text-sm">
                        <?php $__currentLoopData = $policyLines($policy->eligibility_rules); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($line); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </article>

            <article class="content-panel h-full">
                <div class="content-panel-head flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">كيفية الاستثمار</h3>
                </div>
                <div class="p-6 space-y-3">
                    <?php $__currentLoopData = $policyLines($policy->process_description); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="step-item">
                            <span class="step-num"><?php echo e($i + 1); ?></span>
                            <p class="text-sm text-slate-700 leading-relaxed pt-0.5"><?php echo e(preg_replace('/^\d+\.\s*/', '', $line)); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </article>

            <article class="content-panel h-full md:col-span-2 lg:col-span-1">
                <div class="content-panel-head flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">الإطار القانوني</h3>
                </div>
                <div class="p-6">
                    <ul class="req-list text-sm">
                        <?php $__currentLoopData = $policyLines($policy->legal_framework); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($line); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </article>
        </div>

        
        <div class="text-center mb-10 md:mb-12" id="plans">
            <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold mb-4">
                <i class="fas fa-layer-group text-blue-600"></i>
                فرص الاستثمار
            </span>
            <h2 class="section-title text-2xl md:text-3xl font-extrabold text-blue-900">الخطط الاستثمارية المتاحة</h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">اختر الخطة المناسبة لأهدافك وقدّم طلبك — فريقنا يراجع الطلبات ويتواصل مع المستثمرين المؤهّلين</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('investment.show', $plan->slug)); ?>" class="job-card p-6 group">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="min-w-0 flex-1">
                            <?php if($plan->is_featured): ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-gradient-to-r from-blue-600 to-sky-500 text-white px-2.5 py-0.5 rounded-full mb-2">مميز</span>
                            <?php endif; ?>
                            <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug">
                                <?php echo e($plan->title); ?>

                            </h3>
                            <p class="text-xs font-bold text-sky-600 mt-1"><?php echo e($plan->planTypeLabel()); ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-sky-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="job-meta-chip blue"><i class="fas fa-coins text-[10px]"></i><?php echo e($plan->formattedMinInvestment()); ?></span>
                        <span class="job-meta-chip green"><i class="fas fa-shield-alt text-[10px]"></i>مخاطر <?php echo e($plan->riskLevelLabel()); ?></span>
                        <?php if($plan->duration_months): ?>
                            <span class="job-meta-chip violet"><i class="fas fa-calendar text-[10px]"></i><?php echo e($plan->duration_months); ?> شهر</span>
                        <?php endif; ?>
                    </div>

                    <?php if($plan->short_description): ?>
                        <p class="text-sm text-slate-600 leading-relaxed line-clamp-3 flex-1"><?php echo e($plan->short_description); ?></p>
                    <?php endif; ?>

                    <?php if($plan->expected_return_min): ?>
                        <p class="text-xs text-slate-500 mt-3 font-semibold">
                            عائد متوقع: <?php echo e($plan->expected_return_min); ?>% — <?php echo e($plan->expected_return_max); ?>%
                        </p>
                    <?php endif; ?>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-blue-600 group-hover:text-blue-800 transition-colors">التفاصيل والتقديم</span>
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="sm:col-span-2 lg:col-span-3 content-panel p-10 md:p-14 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-blue-100 to-sky-100 text-blue-600 flex items-center justify-center text-3xl">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">لا توجد خطط متاحة حالياً</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">تواصل معنا للاستفسار عن فرص الاستثمار القادمة أو لتقديم اهتمامك المسبق</p>
                    <a href="<?php echo e(route('public.contact')); ?>" class="btn-primary !text-base !py-3 !px-8">
                        <i class="fas fa-envelope"></i>
                        تواصل معنا
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="py-14 bg-gradient-to-b from-blue-50/40 to-white border-t border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <div class="content-panel">
            <div class="content-panel-head flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">إخلاء المسؤولية</h3>
            </div>
            <div class="p-6 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                <?php echo nl2br(e($policy->disclaimer)); ?>

            </div>
        </div>

        <?php
            $email = $policy->contact_email ?: ($contact['email'] ?? null);
            $phone = $policy->contact_phone ?: ($contact['phone'] ?? null);
        ?>
        <?php if($email || $phone): ?>
            <div class="mt-8 grid sm:grid-cols-2 gap-4">
                <?php if($email): ?>
                    <a href="mailto:<?php echo e($email); ?>" class="info-tile group no-underline text-inherit">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 mb-0.5">البريد الإلكتروني</p>
                            <p class="text-sm font-extrabold text-slate-900 dir-ltr"><?php echo e($email); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                <?php if($phone): ?>
                    <a href="tel:<?php echo e($phone); ?>" class="info-tile group no-underline text-inherit">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 mb-0.5">الهاتف</p>
                            <p class="text-sm font-extrabold text-slate-900 dir-ltr"><?php echo e($phone); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\investment\index.blade.php ENDPATH**/ ?>