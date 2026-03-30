

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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الأحداث</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">المهام</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($stats['tasks']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">الإجازات</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($stats['leaves']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">الاجتماعات</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($stats['meetings']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-red-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">قادمة</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($stats['upcoming']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

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
        eventDisplay: 'block',
        height: 'auto'
    });
    calendar.render();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/calendar/index.blade.php ENDPATH**/ ?>