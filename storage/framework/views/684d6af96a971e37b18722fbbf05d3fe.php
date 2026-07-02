

<?php $__env->startSection('title', 'التقارير والإحصائيات'); ?>
<?php $__env->startSection('header', 'التقارير والإحصائيات'); ?>

<?php $__env->startPush('styles'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الفلاتر -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-6">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-bold text-gray-700">الفترة الزمنية:</label>
            <select name="period" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="week" <?php echo e($period === 'week' ? 'selected' : ''); ?>>أسبوع</option>
                <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>>شهر</option>
                <option value="quarter" <?php echo e($period === 'quarter' ? 'selected' : ''); ?>>ربع سنوي</option>
                <option value="year" <?php echo e($period === 'year' ? 'selected' : ''); ?>>سنة</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-bold transition-colors">
                <i class="fas fa-filter ml-2"></i>
                تطبيق
            </button>
        </form>
    </div>

    <!-- إحصائيات المهام -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المهام</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($taskStats['total']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($taskStats['completed']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">معدل الإنجاز</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($taskStats['completion_rate']); ?>%</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-red-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">متأخرة</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($taskStats['overdue']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الإجازات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الإجازات</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($leaveStats['total']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">موافق عليها</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($leaveStats['approved']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">قيد الانتظار</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($leaveStats['pending']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الأيام</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($leaveStats['total_days']); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-lg">
            <h3 class="text-lg font-black text-gray-900 mb-4">الأداء الشهري</h3>
            <canvas id="monthlyChart"></canvas>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-lg">
            <h3 class="text-lg font-black text-gray-900 mb-4">المهام حسب الأولوية</h3>
            <canvas id="priorityChart"></canvas>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// الأداء الشهري
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthlyPerformance, 'month_name')); ?>,
        datasets: [{
            label: 'معدل الإنجاز (%)',
            data: <?php echo json_encode(array_column($monthlyPerformance, 'rate')); ?>,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// المهام حسب الأولوية
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
new Chart(priorityCtx, {
    type: 'doughnut',
    data: {
        labels: ['عاجل', 'عالي', 'متوسط', 'منخفض'],
        datasets: [{
            data: [
                <?php echo e($tasksByPriority['urgent']); ?>,
                <?php echo e($tasksByPriority['high']); ?>,
                <?php echo e($tasksByPriority['medium']); ?>,
                <?php echo e($tasksByPriority['low']); ?>

            ],
            backgroundColor: [
                'rgb(239, 68, 68)',
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(156, 163, 175)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\reports\index.blade.php ENDPATH**/ ?>