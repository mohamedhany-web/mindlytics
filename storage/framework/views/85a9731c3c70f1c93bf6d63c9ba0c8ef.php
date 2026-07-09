<?php if(auth()->user()?->isSubjectToWorkSchedule() && ($employeeAttendance['mode'] ?? '') === 'working'): ?>
<script>
(function () {
    const heartbeatUrl = <?php echo json_encode(route('employee.presence.heartbeat'), 15, 512) ?>;
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const defaultInterval = <?php echo e((int) config('employee_presence.heartbeat_interval_seconds', 45)); ?> * 1000;
    let intervalMs = defaultInterval;
    let timer = null;

    function sendHeartbeat() {
        fetch(heartbeatUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).then(async (res) => {
            if (res.status === 401 || res.status === 419) {
                window.location.href = <?php echo json_encode(route('login'), 15, 512) ?>;
                return;
            }
            const data = await res.json().catch(() => ({}));
            if (data.heartbeat_interval) {
                intervalMs = Math.max(30000, data.heartbeat_interval * 1000);
            }
            if (!data.success && data.status === 'not_on_shift') {
                clearInterval(timer);
            }
        }).catch(() => {});
    }

    sendHeartbeat();
    timer = setInterval(sendHeartbeat, intervalMs);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            sendHeartbeat();
        }
    });

    window.addEventListener('beforeunload', function () {
        if (navigator.sendBeacon) {
            const body = new FormData();
            body.append('_token', csrf);
            navigator.sendBeacon(heartbeatUrl, body);
        }
    });
})();
</script>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\components\employee-presence-heartbeat.blade.php ENDPATH**/ ?>