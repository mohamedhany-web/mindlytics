<?php $__env->startSection('title', $job->title . ' — التقديم | Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('careers._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $requirementLines = array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n|•|·|-(?=\s)/', (string) $job->requirements)),
        fn ($line) => $line !== '' && mb_strlen($line) > 1
    ));
?>

<?php echo $__env->make('careers._hero', [
    'title' => $job->title,
    'subtitle' => $job->department ? 'قسم ' . $job->department : 'فرصة عمل في Mindlytics',
    'backUrl' => route('careers.index'),
    'backLabel' => 'جميع الوظائف',
    'metaChips' => array_values(array_filter([
        $job->department ? ['label' => $job->department, 'icon' => 'fas fa-building', 'tone' => 'blue'] : null,
        $job->location ? ['label' => $job->location, 'icon' => 'fas fa-map-marker-alt', 'tone' => 'green'] : null,
        $job->employment_type ? ['label' => $job->employment_type, 'icon' => 'fas fa-clock', 'tone' => 'violet'] : null,
    ])),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="py-10 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php if($job->department): ?>
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-0.5">القسم</p>
                        <p class="text-sm font-extrabold text-slate-900"><?php echo e($job->department); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($job->location): ?>
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-0.5">المكان</p>
                        <p class="text-sm font-extrabold text-slate-900"><?php echo e($job->location); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($job->employment_type): ?>
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-0.5">نوع التوظيف</p>
                        <p class="text-sm font-extrabold text-slate-900"><?php echo e($job->employment_type); ?></p>
                    </div>
                </div>
            <?php endif; ?>
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
                        عن الوظيفة
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">تفاصيل الوظيفة</h2>
                </div>

                <?php if($job->description): ?>
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-align-right"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">الوصف الوظيفي</h3>
                        </div>
                        <div class="p-6 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                            <?php echo nl2br(e($job->description)); ?>

                        </div>
                    </article>
                <?php endif; ?>

                <?php if($job->requirements): ?>
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">المتطلبات</h3>
                        </div>
                        <div class="p-6">
                            <?php if(count($requirementLines) > 1): ?>
                                <ul class="req-list space-y-1 text-sm">
                                    <?php $__currentLoopData = $requirementLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($line); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                                    <?php echo nl2br(e($job->requirements)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if(! $job->description && ! $job->requirements): ?>
                    <div class="content-panel p-8 text-center text-slate-500 text-sm">
                        لا توجد تفاصيل إضافية — يمكنك التقديم مباشرة عبر النموذج.
                    </div>
                <?php endif; ?>
            </div>

            
            <aside class="lg:col-span-5 space-y-6 lg:sticky lg:top-28">
                <div class="content-panel">
                    <div class="content-panel-head">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-sky-500 text-white flex items-center justify-center shadow-md">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">قدّم طلبك الآن</h3>
                        </div>
                        <p class="text-xs text-slate-500 mr-13 pr-13">
                            <i class="fas fa-shield-alt text-sky-500 ml-1"></i>
                            بياناتك محمية ولا تُشارَك إلا مع فريق التوظيف
                        </p>
                    </div>

                    <form method="post" action="<?php echo e(route('careers.apply', $job)); ?>" enctype="multipart/form-data"
                          class="p-5 sm:p-6 space-y-6" x-data="{ cvName: '', attachCount: 0 }">
                        <?php echo csrf_field(); ?>

                        
                        <div>
                            <div class="form-section-title">
                                <span class="form-section-num">1</span>
                                البيانات الشخصية
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="careers-label">الاسم بالكامل <span class="text-rose-500">*</span></label>
                                    <input name="full_name" value="<?php echo e(old('full_name')); ?>" required placeholder="مثال: أحمد محمد" class="careers-input">
                                </div>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="careers-label">البريد الإلكتروني</label>
                                        <input name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="name@email.com" class="careers-input" dir="ltr">
                                    </div>
                                    <div>
                                        <label class="careers-label">رقم الهاتف</label>
                                        <input name="phone" value="<?php echo e(old('phone')); ?>" placeholder="01xxxxxxxxx" class="careers-input" dir="ltr">
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <div class="form-section-title">
                                <span class="form-section-num">2</span>
                                الروابط المهنية
                            </div>
                            <div class="grid sm:grid-cols-1 gap-4">
                                <div>
                                    <label class="careers-label">LinkedIn</label>
                                    <input name="linkedin_url" type="url" value="<?php echo e(old('linkedin_url')); ?>" placeholder="https://linkedin.com/in/..." class="careers-input" dir="ltr">
                                </div>
                                <div>
                                    <label class="careers-label">Portfolio / GitHub</label>
                                    <input name="portfolio_url" type="url" value="<?php echo e(old('portfolio_url')); ?>" placeholder="https://..." class="careers-input" dir="ltr">
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <div class="form-section-title">
                                <span class="form-section-num">3</span>
                                رسالة تعريفية (اختياري)
                            </div>
                            <textarea name="cover_letter" rows="4" placeholder="لماذا ترغب بالانضمام لهذه الوظيفة؟" class="careers-input resize-y min-h-[96px]"><?php echo e(old('cover_letter')); ?></textarea>
                        </div>

                        
                        <div>
                            <div class="form-section-title">
                                <span class="form-section-num">4</span>
                                المرفقات
                            </div>
                            <div class="grid sm:grid-cols-1 gap-4">
                                <div>
                                    <label class="careers-label">السيرة الذاتية (CV) <span class="text-rose-500">*</span></label>
                                    <label class="upload-zone block cursor-pointer">
                                        <input type="file" name="cv" required accept=".pdf,.doc,.docx" class="sr-only"
                                               @change="cvName = $event.target.files[0]?.name || ''">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="min-w-0 text-right">
                                                <p class="text-sm font-bold text-slate-800 truncate" x-text="cvName || 'اختر ملف CV'"></p>
                                                <p class="text-xs text-slate-500 mt-0.5">PDF أو Word — حد أقصى 10 MB</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <label class="careers-label">مرفقات إضافية (اختياري)</label>
                                    <label class="upload-zone block cursor-pointer">
                                        <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.zip" class="sr-only"
                                               @change="attachCount = $event.target.files?.length || 0">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-paperclip"></i>
                                            </div>
                                            <div class="min-w-0 text-right">
                                                <p class="text-sm font-bold text-slate-800" x-text="attachCount ? attachCount + ' ملف/ملفات محددة' : 'إرفاق ملفات إضافية'"></p>
                                                <p class="text-xs text-slate-500 mt-0.5">حتى 5 ملفات</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <button type="submit" class="careers-btn-submit w-full">
                                <i class="fas fa-paper-plane"></i>
                                إرسال الطلب
                            </button>
                            <p class="text-[11px] text-slate-500 text-center mt-3">بالضغط على «إرسال» أنت توافق على مراجعة بياناتك من فريق HR</p>
                        </div>
                    </form>
                </div>

                
                <div class="content-panel p-6">
                    <h4 class="text-base font-extrabold text-blue-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-route text-sky-500"></i>
                        خطوات التقديم
                    </h4>
                    <ol class="space-y-4">
                        <li class="step-item">
                            <span class="step-num">1</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">املأ النموذج</p>
                                <p class="text-xs text-slate-500 mt-0.5">أدخل بياناتك وارفع سيرتك الذاتية</p>
                            </div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">2</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">مراجعة HR</p>
                                <p class="text-xs text-slate-500 mt-0.5">فريق التوظيف يقيّم طلبك</p>
                            </div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">3</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">التواصل معك</p>
                                <p class="text-xs text-slate-500 mt-0.5">مقابلة أو عرض في حال الملاءمة</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="stat-card p-5">
                    <p class="text-sm font-extrabold text-slate-800 flex items-center gap-2 mb-2">
                        <i class="fas fa-question-circle text-blue-600"></i>
                        استفسار؟
                    </p>
                    <p class="text-xs text-slate-600 leading-relaxed">لأي سؤال عن الوظيفة أو عملية التقديم، تواصل معنا.</p>
                    <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-blue-700 hover:text-blue-900 transition-colors">
                        صفحة التواصل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/careers/show.blade.php ENDPATH**/ ?>