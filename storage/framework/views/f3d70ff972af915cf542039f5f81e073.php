

<?php $__env->startSection('title', 'تفاصيل التقديم — HR'); ?>
<?php $__env->startSection('header', 'تفاصيل التقديم — HR'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.hr._shared', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $score = $application->score;
    $selectedRubricId = (int) (request('rubric_id')
        ?? old('rubric_id')
        ?? $score?->rubric_id
        ?? ($rubrics->firstWhere('is_default', true)?->id ?? ($rubrics->first()?->id)));
    $selectedRubric = $rubrics->firstWhere('id', (int) $selectedRubricId);
    $criteria = $selectedRubric?->criteria_json ?? [];
    $existingScores = is_array($score?->scores_json) ? $score->scores_json : [];
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._nav', ['active' => 'applications'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => $application->full_name,
        'subtitle' => 'وظيفة: ' . ($application->job?->title ?: '—') . ' — تاريخ التقديم: ' . ($application->submitted_at?->format('Y-m-d H:i') ?: '—'),
        'icon' => 'fas fa-user',
        'actions' => '<a href="' . route('admin.hr.applications.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع للقائمة</a>',
        'statCards' => [
            ['label' => 'الحالة', 'value' => $statusLabels[$application->status] ?? $application->status, 'icon' => 'fas fa-flag', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'التقييم', 'value' => $score?->total_score !== null ? number_format((float) $score->total_score, 2) : '—', 'icon' => 'fas fa-star', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['label' => 'الملفات', 'value' => $application->files->count(), 'icon' => 'fas fa-paperclip', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 items-start">
        <section class="<?php echo e($hrSectionClass); ?> xl:col-span-7">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-id-card text-pink-600"></i>
                    بيانات المتقدم
                </h3>
                <form method="post" action="<?php echo e(route('admin.hr.applications.status', $application)); ?>" class="flex flex-wrap items-center gap-2">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <select name="status" class="<?php echo e($hrSelectClass); ?> !py-2 !w-auto min-w-[140px]">
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(old('status', $application->status) === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="<?php echo e($hrBtnDark); ?> !py-2">
                        <i class="fas fa-save"></i>
                        حفظ الحالة
                    </button>
                </form>
            </div>
            <div class="p-5 sm:p-6 space-y-5">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">الاسم</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo e($application->full_name); ?></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">الهاتف</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo e($application->phone ?: '—'); ?></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">البريد</p>
                        <p class="text-sm font-bold text-slate-900 break-all"><?php echo e($application->email ?: '—'); ?></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">المصدر</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo e($application->source ?: '—'); ?></p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">LinkedIn</p>
                        <?php if($application->linkedin_url): ?>
                            <a target="_blank" href="<?php echo e($application->linkedin_url); ?>" class="text-sm font-semibold text-sky-700 hover:underline">فتح الملف</a>
                        <?php else: ?>
                            <p class="text-sm font-bold text-slate-900">—</p>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">Portfolio</p>
                        <?php if($application->portfolio_url): ?>
                            <a target="_blank" href="<?php echo e($application->portfolio_url); ?>" class="text-sm font-semibold text-sky-700 hover:underline">فتح الرابط</a>
                        <?php else: ?>
                            <p class="text-sm font-bold text-slate-900">—</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($application->cover_letter): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-2">رسالة التقديم</p>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 whitespace-pre-line leading-relaxed"><?php echo e($application->cover_letter); ?></div>
                    </div>
                <?php endif; ?>

                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-paperclip text-pink-600"></i>
                        الملفات المرفقة
                    </p>
                    <div class="space-y-2">
                        <?php $__empty_1 = true; $__currentLoopData = $application->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between gap-3 rounded-xl border-2 border-slate-200 bg-white px-4 py-3 shadow-sm">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate"><?php echo e($file->original_name ?: $file->path); ?></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">نوع: <?php echo e($file->kind); ?> — <?php echo e($file->mime ?: '—'); ?> — <?php echo e($file->size ? number_format($file->size/1024, 1) . ' KB' : '—'); ?></p>
                                </div>
                                <a href="<?php echo e(stored_upload_file_url($file->asStoredUploadArray())); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-sm transition-colors">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">لا توجد ملفات.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <aside class="xl:col-span-5 space-y-4 sm:space-y-6">
            <section class="<?php echo e($hrSectionClass); ?>">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-rose-50 to-pink-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-star text-rose-600"></i>
                        التقييم
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">قالب التقييم + درجات لكل معيار. يُحسب المجموع تلقائياً.</p>
                </div>
                <div class="p-5 space-y-4">
                    <?php if($rubrics->isEmpty()): ?>
                        <div class="rounded-xl border-2 border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                            لا يوجد قوالب تقييم. أنشئ واحداً من
                            <a class="underline font-semibold" href="<?php echo e(route('admin.hr.rubrics.create')); ?>">قوالب التقييم</a>.
                        </div>
                    <?php else: ?>
                        <form method="post" action="<?php echo e(route('admin.hr.applications.score', $application)); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                            <div>
                                <label class="<?php echo e($hrLabelClass); ?>">قالب التقييم</label>
                                <select name="rubric_id" class="<?php echo e($hrSelectClass); ?>"
                                        onchange="window.location.search = new URLSearchParams({ rubric_id: this.value }).toString();">
                                    <?php $__currentLoopData = $rubrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r->id); ?>" <?php if((int) $selectedRubricId === (int) $r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?><?php if($r->is_default): ?> (افتراضي) <?php endif; ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">عند تغيير القالب سيتم إعادة تحميل الصفحة لعرض المعايير.</p>
                            </div>

                            <div class="space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $key = (string) ($c['key'] ?? '');
                                        $label = (string) ($c['label'] ?? $key);
                                        $weight = (float) ($c['weight'] ?? 1);
                                        $max = (float) ($c['max'] ?? 10);
                                    ?>
                                    <?php if($key !== ''): ?>
                                        <div class="rounded-xl border-2 border-slate-200 bg-slate-50/80 p-4">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <p class="text-sm font-bold text-slate-900"><?php echo e($label); ?></p>
                                                <p class="text-xs text-slate-500">وزن: <?php echo e($weight); ?> · الحد: <?php echo e($max); ?></p>
                                            </div>
                                            <input type="number" name="scores[<?php echo e($key); ?>]" step="0.1" min="0" max="<?php echo e($max); ?>"
                                                   value="<?php echo e(old('scores.'.$key, $existingScores[$key] ?? 0)); ?>"
                                                   class="<?php echo e($hrInputClass); ?>">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="text-sm text-slate-600 rounded-xl border border-dashed border-slate-300 p-4 text-center">لا توجد معايير في هذا القالب.</div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="<?php echo e($hrLabelClass); ?>">ملاحظات</label>
                                <textarea name="notes" rows="3" class="<?php echo e($hrTextareaClass); ?>"><?php echo e(old('notes', $score?->notes ?? '')); ?></textarea>
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-700 hover:to-red-600 text-white text-sm font-semibold shadow-lg transition-all">
                                <i class="fas fa-save"></i>
                                حفظ التقييم
                            </button>
                        </form>
                    <?php endif; ?>

                    <div class="rounded-xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-5 text-center">
                        <p class="text-xs font-semibold text-slate-500 mb-1">النتيجة الحالية</p>
                        <p class="text-3xl font-black text-slate-900 tabular-nums"><?php echo e($score?->total_score !== null ? number_format((float) $score->total_score, 2) : '—'); ?></p>
                        <p class="text-[11px] text-slate-500 mt-2">آخر تقييم: <?php echo e($score?->scored_at?->format('Y-m-d H:i') ?: '—'); ?></p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\hr\applications\show.blade.php ENDPATH**/ ?>