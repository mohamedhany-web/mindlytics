<?php $__env->startSection('title', __('public.contact_page_title') . ' — Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('careers._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .contact-input {
        width: 100%;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .contact-input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
    }
    .contact-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.375rem;
    }
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
    .hours-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 0.875rem;
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    .hours-row.closed {
        background: #fef2f2;
        border-color: #fecaca;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $contact = $contact ?? \App\Support\PlatformSettings::contactPage();
    $phoneDigits = \App\Support\PlatformSettings::phoneDigits($contact['phone'] ?? '');
    $whatsapp = preg_replace('/\D+/', '', (string) ($contact['whatsapp'] ?? ''));
?>

<?php echo $__env->make('careers._hero', [
    'title' => $contact['hero_title'] ?? 'تواصل معنا',
    'subtitle' => $contact['hero_subtitle'] ?? '',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/25 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid lg:grid-cols-12 gap-8 xl:gap-10 items-start">
            
            <div class="lg:col-span-7">
                <div class="text-center lg:text-right mb-6">
                    <span class="careers-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-3">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                        رسالة جديدة
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">أرسل رسالتك</h2>
                    <p class="text-slate-600 mt-3 text-sm">سنرد عليك في أقرب وقت ممكن</p>
                </div>

                <div class="content-panel">
                    <div class="p-5 sm:p-6">
                        <?php if(session('success')): ?>
                            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold flex items-center gap-2">
                                <i class="fas fa-check-circle text-lg"></i>
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
                                <ul class="list-disc list-inside space-y-0.5">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($e); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo e(route('public.contact.store')); ?>" method="POST" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label class="contact-label">الاسم <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="contact-input" placeholder="اسمك الكامل">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="contact-label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="contact-input" placeholder="name@email.com" dir="ltr">
                                </div>
                                <div>
                                    <label class="contact-label">رقم الهاتف <span class="text-slate-400 font-normal">(اختياري)</span></label>
                                    <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" class="contact-input" placeholder="01xxxxxxxxx" dir="ltr">
                                </div>
                            </div>
                            <div>
                                <label class="contact-label">الموضوع <span class="text-rose-500">*</span></label>
                                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" required class="contact-input" placeholder="موضوع الرسالة">
                            </div>
                            <div>
                                <label class="contact-label">الرسالة <span class="text-rose-500">*</span></label>
                                <textarea name="message" rows="5" required class="contact-input resize-y min-h-[120px]" placeholder="اكتب رسالتك هنا…"><?php echo e(old('message')); ?></textarea>
                            </div>
                            <button type="submit" class="btn-primary !text-sm !py-3 !px-8 w-full sm:w-auto">
                                <i class="fas fa-paper-plane"></i>
                                إرسال الرسالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            
            <aside class="lg:col-span-5 space-y-5 lg:sticky lg:top-28">
                <div class="text-center lg:text-right mb-2">
                    <span class="careers-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-3">
                        <i class="fas fa-address-book text-blue-600"></i>
                        بيانات التواصل
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">معلومات التواصل</h2>
                </div>

                <div class="space-y-3">
                    <?php if(!empty($contact['address'])): ?>
                        <div class="info-tile">
                            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500 mb-1">العنوان</p>
                                <p class="text-sm font-semibold text-slate-900 leading-relaxed"><?php echo e($contact['address']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($contact['phone'])): ?>
                        <div class="info-tile">
                            <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500 mb-1">الهاتف</p>
                                <a href="tel:<?php echo e($phoneDigits); ?>" class="text-sm font-semibold text-slate-900 hover:text-blue-700 transition-colors" dir="ltr"><?php echo e($contact['phone']); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($contact['email'])): ?>
                        <div class="info-tile">
                            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500 mb-1">البريد الإلكتروني</p>
                                <a href="mailto:<?php echo e($contact['email']); ?>" class="text-sm font-semibold text-slate-900 hover:text-indigo-700 transition-colors break-all"><?php echo e($contact['email']); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($whatsapp !== ''): ?>
                        <div class="info-tile">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500 mb-1">واتساب</p>
                                <a href="https://wa.me/<?php echo e($whatsapp); ?>" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-slate-900 hover:text-emerald-700 transition-colors" dir="ltr"><?php echo e($contact['phone'] ?: $whatsapp); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($contact['hours'])): ?>
                    <div class="content-panel mt-6">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">ساعات العمل</h3>
                        </div>
                        <div class="p-5 space-y-2">
                            <?php $__currentLoopData = $contact['hours']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="hours-row <?php echo e(!empty($row['closed']) ? 'closed' : ''); ?>">
                                    <span class="text-sm font-bold text-slate-900"><?php echo e($row['label']); ?></span>
                                    <span class="text-sm font-semibold <?php echo e(!empty($row['closed']) ? 'text-rose-600' : 'text-slate-600'); ?>"><?php echo e($row['value'] ?: '—'); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\contact.blade.php ENDPATH**/ ?>