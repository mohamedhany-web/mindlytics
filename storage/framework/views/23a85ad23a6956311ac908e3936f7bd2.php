

<?php $__env->startSection('title', 'التقويم'); ?>
<?php $__env->startSection('header', 'التقويم'); ?>

<?php $__env->startPush('styles'); ?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
<style>
    .fc {
        font-family: 'IBM Plex Sans Arabic', sans-serif;
    }
    .fc-toolbar-title {
        font-weight: 700;
    }
    .fc-event-follow_up {
        border-radius: 4px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- إحصائيات -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">إجمالي الأحداث</p>
            <p class="text-2xl font-black text-slate-900"><?php echo e($stats['total']); ?></p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-blue-100 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">المهام</p>
            <p class="text-2xl font-black text-blue-700"><?php echo e($stats['tasks']); ?></p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-teal-100 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">
                <?php echo e(!empty($stats['is_sales_manager']) ? 'متابعات الفريق' : 'المتابعات'); ?>

            </p>
            <p class="text-2xl font-black text-teal-700"><?php echo e($stats['followups'] ?? 0); ?></p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-emerald-100 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">الإجازات</p>
            <p class="text-2xl font-black text-emerald-700"><?php echo e($stats['leaves']); ?></p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-violet-100 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">الاجتماعات</p>
            <p class="text-2xl font-black text-violet-700"><?php echo e($stats['meetings']); ?></p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-rose-100 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 mb-1">قادمة</p>
            <p class="text-2xl font-black text-rose-700"><?php echo e($stats['upcoming']); ?></p>
        </div>
    </div>

    <?php if(($stats['followups'] ?? 0) > 0 || auth()->user()->isSalesStaff()): ?>
    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
        <span class="font-semibold text-slate-700">دليل المتابعات:</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-rose-600"></span> متأخرة</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-500"></span> اليوم</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-teal-600"></span> قادمة</span>
        <?php if(!empty($stats['is_sales_manager'])): ?>
            <span class="text-slate-400">·</span>
            <span>يظهر اسم الموظف بجانب كل متابعة</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- التقويم -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-6">
        <div id="calendar"></div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '<?php echo e(route("employee.calendar.events")); ?>',
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        eventClassNames: function(arg) {
            return arg.event.extendedProps.type ? ['fc-event-' + arg.event.extendedProps.type] : [];
        },
        eventDisplay: 'block',
        height: 'auto'
    });
    calendar.render();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\calendar\index.blade.php ENDPATH**/ ?>