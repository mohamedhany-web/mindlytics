

<?php $__env->startSection('title', 'فريق المبيعات'); ?>
<?php $__env->startSection('header', 'مدير المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto py-16 text-center">
    <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl">
        <i class="fas fa-users-cog"></i>
    </div>
    <h2 class="text-xl font-black text-slate-900 mb-2">لم يُربط فريق بك بعد</h2>
    <p class="text-sm text-slate-600 leading-relaxed mb-6">
        حسابك مُعرَّف كمدير مبيعات، لكن لا يوجد فريق مرتبط بك في النظام.
        تواصل مع الإدارة لإنشاء فريق وإضافة الأعضاء من لوحة الإدارة.
    </p>
    <a href="<?php echo e(route('employee.dashboard')); ?>"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold transition-colors">
        <i class="fas fa-home"></i>
        العودة للوحة التحكم
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\no-team.blade.php ENDPATH**/ ?>