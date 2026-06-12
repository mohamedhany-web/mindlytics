<?php $__env->startSection('title', 'إشعارات التطبيق — Mindlytics Community'); ?>
<?php $__env->startSection('page_title', 'Mindlytics Community · إشعارات التطبيق'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-4 sm:space-y-6" style="background: #f8fafc;">

    <?php if(session('success')): ?>
        <div class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white px-5 py-4 text-emerald-900 shadow-lg flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"><i class="fas fa-check text-lg"></i></span>
            <span class="font-bold"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50/95 px-5 py-4 shadow-lg">
            <p class="font-black text-rose-900 mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-1 text-sm text-rose-800">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="rounded-2xl p-6 sm:p-8 relative overflow-hidden border-2 border-violet-200/60 shadow-xl w-full group"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.96) 45%, rgba(237,233,254,0.92) 100%);">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
            <div class="flex items-start gap-5 min-w-0 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center shadow-xl shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 55%, #4f46e5 100%); box-shadow: 0 12px 32px rgba(99, 102, 241, 0.38);">
                    <i class="fas fa-bell text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-violet-700/90 mb-1">Mindlytics Community · تطبيق الطلاب</p>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight bg-gradient-to-r from-violet-900 via-indigo-800 to-violet-800 bg-clip-text text-transparent">
                        إشعارات التطبيق
                    </h1>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed max-w-3xl">
                        أنشئ إشعاراً في جدول <span class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded">notifications</span> كما يظهر للطالب في الموقع وتطبيق Flutter.
                        المعاينة على اليمين تقارب شكل البطاقة على الجهاز (عنوان + نص + التطبيق).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        
        <div class="rounded-2xl border-2 border-slate-200/80 bg-white p-6 sm:p-8 shadow-lg">
            <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-paper-plane text-violet-600"></i>
                إنشاء إشعار جديد
            </h2>
            <form action="<?php echo e(route('admin.mobile-app.notifications.store')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">العنوان</label>
                    <input type="text" name="title" id="field-title" value="<?php echo e(old('title')); ?>" required maxlength="255"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-slate-900 focus:border-violet-400 focus:ring-0"
                           placeholder="مثال: تذكير بالواجب">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">الرسالة</label>
                    <textarea name="message" id="field-message" rows="4" required maxlength="8000"
                              class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-slate-900 focus:border-violet-400 focus:ring-0"
                              placeholder="نص يظهر في قائمة الإشعارات والمعاينة"><?php echo e(old('message')); ?></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">النوع</label>
                        <select name="type" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 bg-white">
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php if(old('type', 'general') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">الأولوية</label>
                        <select name="priority" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 bg-white">
                            <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php if(old('priority', 'normal') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">المستلمون</label>
                    <select name="scope" id="scope" onchange="window.mobileNotifScope && window.mobileNotifScope()"
                            class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 bg-white">
                        <option value="all_students" <?php if(old('scope') === 'all_students'): echo 'selected'; endif; ?>>جميع الطلاب النشطين</option>
                        <option value="course" <?php if(old('scope') === 'course'): echo 'selected'; endif; ?>>طلاب كورس محدد</option>
                        <option value="user" <?php if(old('scope') === 'user'): echo 'selected'; endif; ?>>طالب واحد</option>
                    </select>
                </div>
                <div id="wrap-course" class="<?php echo e(old('scope') === 'course' ? '' : 'hidden'); ?>">
                    <label class="block text-sm font-bold text-slate-700 mb-1">الكورس</label>
                    <select name="advanced_course_id" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 bg-white max-h-48 overflow-auto">
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php if((string)old('advanced_course_id') === (string)$c->id): echo 'selected'; endif; ?>>
                                #<?php echo e($c->id); ?> — <?php echo e(\Illuminate\Support\Str::limit($c->title, 80)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div id="wrap-user" class="<?php echo e(old('scope') === 'user' ? '' : 'hidden'); ?>">
                    <label class="block text-sm font-bold text-slate-700 mb-1">الطالب</label>
                    <select name="user_id" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 bg-white max-h-48 overflow-auto">
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php if((string)old('user_id') === (string)$s->id): echo 'selected'; endif; ?>>
                                <?php echo e($s->name); ?> · <?php echo e($s->email); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">رابط عند النقر (اختياري)</label>
                        <input type="text" name="action_url" value="<?php echo e(old('action_url')); ?>"
                               class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 font-mono text-sm"
                               placeholder="https://... أو مسار نسبي مثل /my-courses">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">نص الزر (اختياري)</label>
                        <input type="text" name="action_text" value="<?php echo e(old('action_text')); ?>" maxlength="255"
                               class="w-full rounded-xl border-2 border-slate-200 px-4 py-3"
                               placeholder="افتح في المتصفح">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">انتهاء الصلاحية (اختياري)</label>
                    <input type="datetime-local" name="expires_at" value="<?php echo e(old('expires_at')); ?>"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3">
                </div>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-8 py-3.5 font-black text-white shadow-lg transition hover:opacity-95"
                        style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #4f46e5 100%);">
                    <i class="fas fa-bell"></i>
                    إرسال الإشعار
                </button>
            </form>
        </div>

        
        <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-b from-slate-900 to-slate-950 p-6 sm:p-8 shadow-xl text-white">
            <h2 class="text-lg font-black mb-2 flex items-center gap-2 text-indigo-100">
                <i class="fas fa-mobile-screen-button"></i>
                معاينة الظهور على الجهاز
            </h2>
            <p class="text-xs text-slate-400 mb-6">تتحدث تلقائياً مع العنوان والرسالة أعلاه (تقريب بصري لشريط الإشعارات).</p>
            <div class="mx-auto max-w-[280px]">
                <div class="rounded-[2rem] border-4 border-slate-600 bg-slate-950 overflow-hidden shadow-2xl">
                    <div class="flex justify-between items-center px-5 pt-3 pb-2 text-[10px] text-slate-400 font-medium">
                        <span>9:41</span>
                        <div class="flex gap-1"><span class="opacity-80">LTE</span><i class="fas fa-battery-three-quarters text-xs"></i></div>
                    </div>
                    <div class="px-3 pb-8 pt-2 space-y-3">
                        <div id="mock-toast" class="rounded-2xl bg-white/95 text-slate-900 p-3 shadow-lg border border-white/20">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center shrink-0 shadow-md">
                                    <span class="text-white text-lg font-black">M</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="pv-app" class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Mindlytics</p>
                                    <p id="pv-title" class="text-sm font-black text-slate-900 leading-tight truncate"><?php echo e(old('title', 'عنوان الإشعار')); ?></p>
                                    <p id="pv-body" class="text-xs text-slate-600 mt-1 line-clamp-4"><?php echo e(old('message', 'نص الرسالة كما يقرأه الطالب في التطبيق.')); ?></p>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-center text-slate-500 px-2">في Flutter تُعرض ضمن شاشة «الإشعارات» مع تمييز غير المقروء؛ التنبيه المحلي يستخدم نفس العنوان والنص عند التجربة.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border-2 border-slate-200/80 bg-white p-6 sm:p-8 shadow-lg overflow-hidden">
        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-history text-violet-600"></i>
            آخر الإرساليات (مجمّعة بالدقيقة — صف واحد لكل دفعة)
        </h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-700 font-bold">
                    <tr>
                        <th class="text-right px-4 py-3">الوقت</th>
                        <th class="text-right px-4 py-3">العنوان</th>
                        <th class="text-right px-4 py-3 hidden md:table-cell">الرسالة</th>
                        <th class="text-right px-4 py-3">المستلمين</th>
                        <th class="text-right px-4 py-3 hidden lg:table-cell">النوع / الأولوية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-violet-50/40">
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600"><?php echo e($batch['sent_at']->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3 font-bold text-slate-900 max-w-[200px]"><?php echo e(\Illuminate\Support\Str::limit($batch['title'], 48)); ?></td>
                            <td class="px-4 py-3 text-slate-600 hidden md:table-cell max-w-md"><?php echo e(\Illuminate\Support\Str::limit($batch['message'], 90)); ?></td>
                            <td class="px-4 py-3 font-black text-violet-700"><?php echo e($batch['recipients']); ?></td>
                            <td class="px-4 py-3 hidden lg:table-cell text-xs">
                                <span class="inline-block rounded-lg bg-slate-100 px-2 py-0.5"><?php echo e($types[$batch['type']] ?? $batch['type']); ?></span>
                                ·
                                <span class="inline-block rounded-lg bg-slate-100 px-2 py-0.5"><?php echo e($priorities[$batch['priority']] ?? $batch['priority']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد إشعارات بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="rounded-2xl border-2 border-slate-200/80 bg-white p-5 sm:p-6 shadow-lg w-full">
        <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-compass text-violet-600"></i>
            أقسام تطبيق الطلاب
        </h2>
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <a href="<?php echo e(route('admin.mobile-app.notifications')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-violet-100 border-violet-300 px-4 py-2 text-sm font-semibold text-violet-900"><i class="fas fa-bell text-violet-600"></i> إشعارات</a>
            <a href="<?php echo e(route('admin.mobile-app.maintenance')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200"><i class="fas fa-tools text-slate-600"></i> الصيانة</a>
            <a href="<?php echo e(route('admin.mobile-app.links')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200"><i class="fas fa-link text-indigo-600"></i> الروابط</a>
            <a href="<?php echo e(route('admin.mobile-app.appearance')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200"><i class="fas fa-palette text-pink-600"></i> المظهر</a>
            <a href="<?php echo e(route('admin.mobile-app.edit')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200"><i class="fas fa-home text-blue-600"></i> الصفحة الرئيسية</a>
        </div>
    </div>
</div>

<script>
(function () {
    function syncScope() {
        var s = document.getElementById('scope');
        if (!s) return;
        var v = s.value;
        var wc = document.getElementById('wrap-course');
        var wu = document.getElementById('wrap-user');
        if (wc) wc.classList.toggle('hidden', v !== 'course');
        if (wu) wu.classList.toggle('hidden', v !== 'user');
    }
    window.mobileNotifScope = syncScope;

    function syncPreview() {
        var t = document.getElementById('field-title');
        var m = document.getElementById('field-message');
        var pt = document.getElementById('pv-title');
        var pb = document.getElementById('pv-body');
        if (pt && t) pt.textContent = t.value.trim() || 'عنوان الإشعار';
        if (pb && m) pb.textContent = m.value.trim() || 'نص الرسالة كما يقرأه الطالب في التطبيق.';
    }

    document.addEventListener('DOMContentLoaded', function () {
        syncScope();
        syncPreview();
        ['field-title', 'field-message'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', syncPreview);
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/mobile-app/notifications.blade.php ENDPATH**/ ?>