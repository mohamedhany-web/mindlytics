

<?php $__env->startSection('title', 'رقابة المدرب - ' . $instructor->name); ?>
<?php $__env->startSection('header', 'رقابة المدرب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="<?php echo e(route('admin.quality-control.index')); ?>" class="text-sky-600 hover:text-sky-700">الرقابة والجودة</a>
        <span class="mx-1">/</span>
        <a href="<?php echo e(route('admin.quality-control.instructors')); ?>" class="text-sky-600 hover:text-sky-700">المدربين</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700"><?php echo e($instructor->name); ?></span>
    </nav>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <?php if($instructor->profile_image): ?>
                    <img src="<?php echo e($instructor->profile_image_url); ?>" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center">
                        <i class="fas fa-user-graduate text-sky-600 text-2xl"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900"><?php echo e($instructor->name); ?></h1>
                    <p class="text-slate-500"><?php echo e($instructor->email); ?></p>
                    <p class="text-sm text-slate-500">آخر دخول: <?php echo e($instructor->last_login_at ? $instructor->last_login_at->diffForHumans() : '—'); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.quality-control.instructors.export', $instructor)); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                <i class="fas fa-file-excel"></i>
                تصدير تقرير Excel
            </a>
        </div>
    </section>

    <!-- ملخص أرقام -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">ملخص النشاط</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/50 p-4">
                <p class="text-xs font-semibold text-sky-600">كورسات أونلاين</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($advancedCourses->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50/50 p-4">
                <p class="text-xs font-semibold text-violet-600">محاضرات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($lectures->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4">
                <p class="text-xs font-semibold text-amber-600">كورسات أوفلاين</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($offlineCourses->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4">
                <p class="text-xs font-semibold text-emerald-600">تسجيلات الطلاب</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($enrollmentsCount); ?></p>
                <p class="text-xs text-slate-500">نشط: <?php echo e($enrollmentsActive); ?> — مكتمل: <?php echo e($enrollmentsCompleted); ?></p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4">
                <p class="text-xs font-semibold text-rose-600">اتفاقيات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($agreements->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                <p class="text-xs font-semibold text-slate-600">واجبات</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($assignments->count()); ?></p>
            </div>
        </div>
    </section>

    <!-- البيانات الشخصية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">البيانات الشخصية الكاملة</h2>
        </div>
        <div class="p-5 sm:p-8">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">الاسم</dt><dd class="text-slate-900 font-medium"><?php echo e($instructor->name); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">البريد</dt><dd class="text-slate-900"><?php echo e($instructor->email ?? '—'); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">الهاتف</dt><dd class="text-slate-900"><?php echo e($instructor->phone ?? '—'); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">تاريخ الميلاد</dt><dd class="text-slate-900"><?php echo e($instructor->birth_date ? $instructor->birth_date->format('Y-m-d') : '—'); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">العنوان</dt><dd class="text-slate-900"><?php echo e($instructor->address ?? '—'); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">الحالة</dt><dd class="text-slate-900"><span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($instructor->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>"><?php echo e($instructor->is_active ? 'نشط' : 'معطّل'); ?></span></dd></div>
                <div class="md:col-span-2"><dt class="text-slate-500 font-medium mb-1">النبذة</dt><dd class="text-slate-900"><?php echo e($instructor->bio ?? '—'); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">تاريخ التسجيل</dt><dd class="text-slate-900"><?php echo e($instructor->created_at->format('Y-m-d H:i')); ?></dd></div>
                <div class="flex justify-between md:block"><dt class="text-slate-500 font-medium">آخر دخول</dt><dd class="text-slate-900"><?php echo e($instructor->last_login_at ? $instructor->last_login_at->format('Y-m-d H:i') : '—'); ?></dd></div>
            </dl>
        </div>
    </section>

    <!-- الكورسات (أونلاين) -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">الكورسات (أونلاين)</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($advancedCourses->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">السعر</th>
                        <th class="px-4 py-3">نشط</th>
                        <th class="px-4 py-3">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $advancedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo e($c->id); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.advanced-courses.show', $c)); ?>" class="font-medium text-sky-600 hover:text-sky-700"><?php echo e($c->title); ?></a></td>
                        <td class="px-4 py-3"><?php echo e($c->price ? number_format($c->price, 2) . ' ج.م' : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($c->is_active ? 'نعم' : 'لا'); ?></td>
                        <td class="px-4 py-3"><?php echo e($c->created_at->format('Y-m-d')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا توجد كورسات أونلاين.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- المحاضرات (أونلاين) — رقابة المحاضرات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">المحاضرات (أونلاين) — رقابة المحاضرات</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($lectures->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">الكورس</th>
                        <th class="px-4 py-3">مجدولة في</th>
                        <th class="px-4 py-3">المدة</th>
                        <th class="px-4 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo e($l->id); ?></td>
                        <td class="px-4 py-3"><?php echo e($l->title ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($l->course ? $l->course->title : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($l->scheduled_at ? $l->scheduled_at->format('Y-m-d H:i') : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($l->duration_minutes ?? '—'); ?> د</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs <?php echo e($l->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($l->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')); ?>"><?php echo e($l->status ?? '—'); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا توجد محاضرات أونلاين.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- الكورسات الأوفلاين + محاضرات أوفلاين -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">الكورسات والمحاضرات الأوفلاين</h2>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            <?php if($offlineCourses->count() > 0): ?>
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-3">الكورسات الأوفلاين</h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $offlineCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('admin.offline-courses.show', $oc)); ?>" class="text-sky-600 hover:text-sky-700"><?php echo e($oc->title ?? 'كورس #' . $oc->id); ?></a> — <?php echo e($oc->created_at->format('Y-m-d')); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if($offlineLectures->count() > 0): ?>
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-3">المحاضرات الأوفلاين</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                        <thead class="bg-slate-50"><tr class="text-xs font-semibold uppercase text-slate-500"><th class="px-4 py-2">العنوان</th><th class="px-4 py-2">الكورس</th><th class="px-4 py-2">التاريخ</th></tr></thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php $__currentLoopData = $offlineLectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td class="px-4 py-2"><?php echo e($ol->title ?? '—'); ?></td><td class="px-4 py-2"><?php echo e($ol->offlineCourse ? $ol->offlineCourse->title : '—'); ?></td><td class="px-4 py-2"><?php echo e($ol->scheduled_at ? $ol->scheduled_at->format('Y-m-d H:i') : '—'); ?></td></tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <?php if($offlineCourses->count() === 0 && $offlineLectures->count() === 0): ?>
            <p class="text-slate-500 text-center py-4">لا توجد كورسات أو محاضرات أوفلاين.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- الاتفاقيات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">اتفاقيات المدرب</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($agreements->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">رقم الاتفاقية</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">نوع الفوترة</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">من - إلى</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo e($a->agreement_number ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->title ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->billing_type ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->total_amount ? number_format($a->total_amount, 2) . ' ج.م' : ($a->monthly_amount ? number_format($a->monthly_amount, 2) . ' ج.م/شهر' : '—')); ?></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($a->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($a->status ?? '—'); ?></span></td>
                        <td class="px-4 py-3"><?php echo e($a->start_date ? $a->start_date->format('Y-m-d') : '—'); ?> — <?php echo e($a->end_date ? $a->end_date->format('Y-m-d') : '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا توجد اتفاقيات.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- الواجبات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">الواجبات التي أنشأها المدرب</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($assignments->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">الكورس</th>
                        <th class="px-4 py-3">النقاط</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">استحقاق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo e($a->id); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->title ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->course ? $a->course->title : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->max_score ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->status ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->due_date ? $a->due_date->format('Y-m-d') : '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا توجد واجبات.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- طلبات السحب -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">طلبات السحب</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($withdrawals->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo e($w->id); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format($w->amount ?? 0, 2)); ?> ج.م</td>
                        <td class="px-4 py-3"><?php echo e($w->status ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($w->created_at->format('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا توجد طلبات سحب.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- سجل النشاط -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">سجل النشاط (آخر 100)</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($activityLogs->count() > 0): ?>
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الإجراء</th>
                        <th class="px-4 py-3">الوصف</th>
                        <th class="px-4 py-3">النموذج</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2"><?php echo e($log->created_at->format('Y-m-d H:i')); ?></td>
                        <td class="px-4 py-2"><?php echo e($log->action ?? '—'); ?></td>
                        <td class="px-4 py-2"><?php echo e(\Illuminate\Support\Str::limit($log->description ?? '—', 60)); ?></td>
                        <td class="px-4 py-2"><?php echo e(class_basename($log->model_type ?? '') ?: '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="p-6 text-center text-slate-500">لا يوجد سجل نشاط.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/quality-control/instructor-show.blade.php ENDPATH**/ ?>