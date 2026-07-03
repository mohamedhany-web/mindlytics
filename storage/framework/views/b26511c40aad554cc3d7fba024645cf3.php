<?php $__env->startSection('title', 'خصم التقرير اليومي — المبيعات'); ?>
<?php $__env->startSection('header', 'خصم التقرير اليومي — المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background: #f8fafc; min-height: 100vh;">
  <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 font-semibold"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
      <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء في النموذج:</p>
      <ul class="list-disc list-inside space-y-0.5">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
    <div class="px-6 py-5 border-b bg-gradient-to-r from-rose-50 to-white flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-xl font-black text-slate-900">إعدادات الخصم التلقائي — التقرير اليومي</h2>
        <p class="text-sm text-slate-600 mt-1">يُطبَّق عند عدم تسليم موظف المبيعات تقريره قبل نهاية اليوم.<?php if(\Illuminate\Support\Facades\Route::has('admin.sales.daily-reports.index')): ?> يظهر في <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>" class="text-emerald-700 font-semibold underline">تقارير المبيعات اليومية</a>.<?php endif; ?></p>
      </div>
      <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="text-sm text-slate-600 hover:text-rose-700 font-semibold"><i class="fas fa-arrow-right ml-1"></i> خصومات الموظفين</a>
    </div>
    <div class="p-6">
      <?php echo $__env->make('admin.sales.daily-reports._settings_form', [
          'formAction' => route('admin.employee-deductions.daily-report-penalty-settings.update'),
          'method' => 'PUT',
          'settings' => $settings,
          'cancelUrl' => route('admin.employee-deductions.index'),
          'layout' => 'compact',
      ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-deductions\daily-report-penalty-settings.blade.php ENDPATH**/ ?>