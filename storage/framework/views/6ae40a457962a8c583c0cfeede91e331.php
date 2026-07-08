<?php if(auth()->user()?->isSubjectToWorkSchedule() && !empty($employeeAttendance) && ($employeeAttendance['mode'] ?? '') !== 'exempt'): ?>
<?php
    $att = $employeeAttendance;
    $mode = $att['mode'] ?? '';
?>
<div class="flex items-center gap-2" x-data="employeeWorkTimer(<?php echo \Illuminate\Support\Js::from([
    'mode' => $mode,
    'workedSeconds' => (int) ($att['worked_seconds'] ?? 0),
    'requiredSeconds' => (int) ($att['required_seconds'] ?? 0),
    'canClockIn' => (bool) ($att['can_clock_in'] ?? false),
    'canClockOut' => (bool) ($att['can_clock_out'] ?? false),
    'statusUrl' => route('employee.attendance.status'),
])->toHtml() ?>)" x-init="init()">
    <?php if($mode === 'working'): ?>
        <div class="hidden sm:flex flex-col items-end leading-tight px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-200">
            <span class="text-[10px] font-semibold text-emerald-700">تايمر العمل</span>
            <span class="text-sm font-black text-emerald-800 tabular-nums" x-text="workTimerLabel"></span>
        </div>
        <div class="hidden md:flex items-center gap-1.5 px-2 py-1 rounded-lg bg-sky-50 border border-sky-200" title="يجب إبقاء النظام مفتوحاً طوال الدوام">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[10px] font-semibold text-sky-800">مراقبة التواجد</span>
        </div>
        <?php if($att['can_clock_out'] ?? false): ?>
            <form method="post" action="<?php echo e(route('employee.attendance.clock-out')); ?>" onsubmit="return confirm('إنهاء يوم العمل؟');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow">
                    <i class="fas fa-stop-circle"></i>
                    <span class="hidden sm:inline">إنهاء العمل</span>
                </button>
            </form>
        <?php else: ?>
            <span class="hidden md:inline text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded-lg">
                <?php echo e(max(0, (int) ceil((($att['required_seconds'] ?? 0) - ($att['worked_seconds'] ?? 0)) / 60))); ?> د متبقية
            </span>
        <?php endif; ?>
    <?php elseif($att['can_clock_in'] ?? false): ?>
        <form method="post" action="<?php echo e(route('employee.attendance.clock-in')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow animate-pulse">
                <i class="fas fa-fingerprint"></i>
                تسجيل الحضور
            </button>
        </form>
    <?php endif; ?>
</div>
<script>
function employeeWorkTimer(cfg) {
    return {
        worked: cfg.workedSeconds || 0,
        required: cfg.requiredSeconds || 0,
        workTimerLabel: '00:00:00',
        init() {
            if (cfg.mode !== 'working') return;
            const tick = () => {
                this.worked++;
                const h = Math.floor(this.worked / 3600);
                const m = Math.floor((this.worked % 3600) / 60);
                const s = this.worked % 60;
                this.workTimerLabel = [h,m,s].map(v => String(v).padStart(2,'0')).join(':');
            };
            tick();
            setInterval(tick, 1000);
            setInterval(() => fetch(cfg.statusUrl).then(r => r.json()).then(d => {
                if (d.attendance?.can_clock_out) location.reload();
            }).catch(() => {}), 60000);
        }
    };
}
</script>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/components/employee-attendance-bar.blade.php ENDPATH**/ ?>