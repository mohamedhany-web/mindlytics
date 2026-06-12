

<?php $__env->startSection('title', 'حضور المحاضرة - ' . $offlineCourse->title); ?>
<?php $__env->startSection('header', 'حضور المحاضرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.attendance.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors">الحضور والغياب</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">محاضرة</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-emerald-600"></i>
                    <?php echo e($session->title ?: 'جلسة'); ?> — <?php echo e($session->group?->name ?? 'مجموعة'); ?>

                </h1>
                <p class="text-sm text-slate-600 mt-1">
                    <?php echo e($date ?? optional($session->session_date)->format('Y-m-d')); ?>

                    <?php if($session->start_time): ?>
                        · <?php echo e($session->start_time); ?><?php echo e($session->end_time ? ' - '.$session->end_time : ''); ?>

                    <?php endif; ?>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('instructor.offline-courses.attendance.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
                <button type="button"
                        id="saveAttendanceBtn"
                        data-save-url="<?php echo e(route('instructor.offline-courses.attendance.mark', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>"
                        data-group-id="<?php echo e((int) ($session->group_id ?? 0)); ?>"
                        data-session-id="<?php echo e((int) ($session->id ?? 0)); ?>"
                        data-date="<?php echo e($date ?? optional($session->session_date)->format('Y-m-d')); ?>"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    حفظ
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <?php if($students->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-user-slash text-4xl mb-3 opacity-50"></i>
                <p>لا يوجد طلاب في هذه المحاضرة/المجموعة.</p>
            </div>
        <?php else: ?>
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="text-sm text-slate-700 font-semibold">عدد الطلاب: <?php echo e($students->count()); ?></div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold" data-bulk="present">تحديد الكل حاضر</button>
                        <button type="button" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold" data-bulk="absent">تحديد الكل غائب</button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-slate-600">
                            <th class="text-right py-3 px-3 font-bold">الطالب</th>
                            <th class="text-right py-3 px-3 font-bold">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $rec = ($attendanceRecords[$st->id] ?? null);
                                $status = $rec->status ?? 'absent';
                            ?>
                            <tr>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-slate-800"><?php echo e($st->name ?? $st->email ?? ('طالب #' . $st->id)); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e($st->email ?? ''); ?></div>
                                </td>
                                <td class="py-3 px-3">
                                    <select class="w-full rounded-lg border border-slate-200 px-3 py-2 attendance-status" data-student-id="<?php echo e($st->id); ?>">
                                        <option value="present" <?php echo e($status === 'present' ? 'selected' : ''); ?>>حاضر</option>
                                        <option value="late" <?php echo e($status === 'late' ? 'selected' : ''); ?>>متأخر</option>
                                        <option value="excused" <?php echo e($status === 'excused' ? 'selected' : ''); ?>>مستأذن</option>
                                        <option value="absent" <?php echo e($status === 'absent' ? 'selected' : ''); ?>>غائب</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var saveBtn = document.getElementById('saveAttendanceBtn');
    if (!saveBtn) return;

    function collectRecords() {
        var records = [];
        document.querySelectorAll('select.attendance-status').forEach(function (s) {
            records.push({
                student_id: parseInt(s.getAttribute('data-student-id'), 10),
                status: s.value
            });
        });
        return records;
    }

    async function save() {
        var url = saveBtn.getAttribute('data-save-url');
        var groupId = parseInt(saveBtn.getAttribute('data-group-id'), 10);
        var sessionId = parseInt(saveBtn.getAttribute('data-session-id'), 10);
        var date = saveBtn.getAttribute('data-date');
        var records = collectRecords();

        saveBtn.disabled = true;
        var old = saveBtn.textContent;
        saveBtn.textContent = 'جارٍ الحفظ...';
        try {
            var res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ group_id: groupId, session_id: sessionId, date: date, records: records })
            });
            if (!res.ok) {
                var text = await res.text();
                throw new Error('HTTP ' + res.status + ' ' + text);
            }
            saveBtn.textContent = 'تم الحفظ';
            setTimeout(function () { saveBtn.textContent = old; }, 1200);
        } catch (e) {
            saveBtn.textContent = 'فشل الحفظ';
            console.error(e);
            alert('فشل الحفظ. التفاصيل في الكونسول/اللوج.\n' + (e && e.message ? e.message : ''));
            setTimeout(function () { saveBtn.textContent = old; }, 1200);
        } finally {
            saveBtn.disabled = false;
        }
    }

    document.addEventListener('click', function (e) {
        var bulk = e.target.closest('[data-bulk]');
        if (!bulk) return;
        var v = bulk.getAttribute('data-bulk');
        document.querySelectorAll('select.attendance-status').forEach(function (s) { s.value = v; });
    });

    saveBtn.addEventListener('click', function (e) {
        e.preventDefault();
        save();
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/offline-courses/attendance/session.blade.php ENDPATH**/ ?>