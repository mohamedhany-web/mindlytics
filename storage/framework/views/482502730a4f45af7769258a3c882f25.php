

<?php $__env->startSection('title', 'التقويم - جدول الجلسات'); ?>
<?php $__env->startSection('header', 'التقويم'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
    .fc { direction: rtl; font-family: inherit; }
    .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 700; }
    .fc .fc-button { font-size: 0.8rem; padding: 0.35em 0.7em; }
    .fc .fc-button-primary { background-color: #3B82F6; border-color: #3B82F6; }
    .fc .fc-button-primary:hover { background-color: #2563EB; border-color: #2563EB; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #1D4ED8; border-color: #1D4ED8; }
    .fc .fc-daygrid-day-number { font-weight: 600; }
    .fc-event { cursor: pointer; border-radius: 6px; padding: 2px 6px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2"><i class="fas fa-calendar-alt text-indigo-600 ml-2"></i>تقويم الجلسات</h1>
        <p class="text-gray-600">عرض جدول جلسات الكورسات الأوفلاين الخاصة بك</p>
        <div class="flex flex-wrap gap-4 mt-3">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-sm text-gray-600">مجدولة</span></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span><span class="text-sm text-gray-600">منتهية</span></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-sm text-gray-600">ملغية</span></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div id="instructorCalendar"></div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventDetailModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 id="eventTitle" class="text-lg font-bold text-gray-900 mb-4"></h3>
            <div class="space-y-2 text-sm" id="eventDetails"></div>
            <div class="mt-4 text-left">
                <button onclick="document.getElementById('eventDetailModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إغلاق</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('instructorCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: '<?php echo e(app()->getLocale() === "ar" ? "ar" : "en"); ?>',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: {
            url: '<?php echo e(route("instructor.calendar.events")); ?>',
            method: 'GET',
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            document.getElementById('eventTitle').textContent = info.event.title;
            let html = '';
            if (props.course) html += `<p><i class="fas fa-book text-blue-500 ml-1"></i> <strong>الكورس:</strong> ${props.course}</p>`;
            if (props.group) html += `<p><i class="fas fa-users text-purple-500 ml-1"></i> <strong>المجموعة:</strong> ${props.group}</p>`;
            if (props.location) html += `<p><i class="fas fa-map-marker-alt text-red-500 ml-1"></i> <strong>المكان:</strong> ${props.location}</p>`;
            if (props.duration) html += `<p><i class="fas fa-clock text-amber-500 ml-1"></i> <strong>المدة:</strong> ${props.duration}</p>`;
            const statusTexts = { scheduled: 'مجدولة', completed: 'منتهية', cancelled: 'ملغية' };
            html += `<p><i class="fas fa-info-circle text-gray-500 ml-1"></i> <strong>الحالة:</strong> ${statusTexts[props.status] || props.status}</p>`;
            if (props.notes) html += `<p class="mt-2 p-2 bg-gray-50 rounded-lg text-gray-700">${props.notes}</p>`;
            document.getElementById('eventDetails').innerHTML = html;
            document.getElementById('eventDetailModal').classList.remove('hidden');
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: true },
        height: 'auto',
    });
    calendar.render();

    document.getElementById('eventDetailModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/calendar.blade.php ENDPATH**/ ?>