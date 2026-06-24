

<?php $__env->startSection('title', 'إرسال واتساب - ' . $workshop->title); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 lg:p-8 space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <i class="fab fa-whatsapp text-green-600"></i>
            <span>إرسال رسائل واتساب - <?php echo e($workshop->title); ?></span>
        </h1>
        <p class="text-sm text-slate-500 mt-2">
            تم تجهيز <?php echo e(count($links)); ?> رابط واتساب. يمكنك الضغط على "فتح الكل" لفتح تبويبات الرسائل.
        </p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap gap-2">
            <button id="open-all-links" type="button" class="inline-flex items-center gap-2 rounded-xl bg-green-600 hover:bg-green-700 px-4 py-2 text-sm font-semibold text-white shadow-md">
                <i class="fab fa-whatsapp"></i>
                <span>فتح كل الروابط</span>
            </button>
            <a href="<?php echo e(route('admin.workshops.show', $workshop)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                <span>العودة لصفحة الورشة</span>
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
            <div class="text-xs text-slate-500 mb-1">نص الرسالة:</div>
            <div class="text-sm text-slate-800 whitespace-pre-line"><?php echo e($message); ?></div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الرقم</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">إجراء</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($i + 1); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-800 font-semibold"><?php echo e($item['phone']); ?></td>
                            <td class="px-4 py-3 text-sm">
                                <a href="<?php echo e($item['url']); ?>" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 rounded-lg bg-green-50 hover:bg-green-100 px-3 py-1.5 text-green-700 border border-green-200">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>فتح</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const links = <?php echo json_encode(array_values(array_map(fn($x) => $x['url'], $links)), 512) ?>;
        const btn = document.getElementById('open-all-links');
        if (!btn) return;

        function openAll() {
            links.forEach((url, index) => {
                setTimeout(() => window.open(url, '_blank'), index * 150);
            });
        }

        btn.addEventListener('click', openAll);
        // محاولة فتح الروابط تلقائياً عند تحميل الصفحة
        setTimeout(openAll, 300);
    })();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\whatsapp-launch.blade.php ENDPATH**/ ?>