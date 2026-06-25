<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إشعار خصم من الراتب</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #334155; }
        .box { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
        h1 { font-size: 1.25rem; color: #0f172a; margin: 0 0 16px; }
        p { margin: 0 0 10px; font-size: 0.9375rem; line-height: 1.6; }
        .card { background: #fef2f2; border-radius: 12px; padding: 14px; margin: 12px 0; border-right: 4px solid #dc2626; }
        .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .value { font-weight: 600; color: #0f172a; }
        .amount { font-size: 1.25rem; font-weight: 700; color: #b91c1c; }
        .btn { display: inline-block; margin-top: 16px; padding: 12px 24px; background: #2563eb; color: #fff !important; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9375rem; }
        .btn:hover { background: #1d4ed8; }
        .note { font-size: 0.8125rem; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>تم تسجيل خصم من راتبك</h1>
        <p>مرحباً <?php echo e($deduction->employee->name ?? 'الموظف'); ?>،</p>
        <p>تم تسجيل خصم جديد على راتبك من إدارة المنصة.</p>

        <div class="card">
            <div class="label">رقم الخصم</div>
            <div class="value"><?php echo e($deduction->deduction_number); ?></div>
        </div>
        <div class="card">
            <div class="label">عنوان الخصم</div>
            <div class="value"><?php echo e($deduction->title); ?></div>
        </div>
        <?php if($deduction->description): ?>
            <div class="card">
                <div class="label">الوصف</div>
                <div class="value" style="font-weight: normal;"><?php echo e($deduction->description); ?></div>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="label">نوع الخصم</div>
            <div class="value">
                <?php if($deduction->type === 'tax'): ?> ضريبة
                <?php elseif($deduction->type === 'insurance'): ?> تأمين
                <?php elseif($deduction->type === 'loan'): ?> قرض
                <?php elseif($deduction->type === 'penalty'): ?> غرامة
                <?php else: ?> أخرى
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="label">المبلغ (ج.م)</div>
            <div class="amount"><?php echo e(number_format($deduction->amount, 2)); ?> ج.م</div>
        </div>
        <div class="card">
            <div class="label">تاريخ الخصم</div>
            <div class="value"><?php echo e($deduction->deduction_date?->format('Y-m-d')); ?></div>
        </div>
        <div class="card">
            <div class="label">الحالة</div>
            <div class="value">
                <?php if($deduction->status === 'pending'): ?> معلقة
                <?php elseif($deduction->status === 'applied'): ?> مطبقة
                <?php else: ?> ملغاة
                <?php endif; ?>
            </div>
        </div>

        <a href="<?php echo e(url(route('employee.accounting.index'))); ?>" class="btn">عرض المحاسبة والاتفاقيات</a>
        <p class="note">يمكنك الدخول إلى لوحة الموظف → المحاسبة لعرض تفاصيل الراتب والخصومات والاتفاقيات.</p>
    </div>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/emails/employee-deduction-added.blade.php ENDPATH**/ ?>