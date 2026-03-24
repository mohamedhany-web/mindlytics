<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>رمز الدخول</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #334155; }
        .box { max-width: 400px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
        h1 { font-size: 1.25rem; color: #0f172a; margin: 0 0 16px; }
        .code { font-size: 1.75rem; letter-spacing: 0.5rem; font-weight: 700; color: #0369a1; text-align: center; padding: 16px; background: #f0f9ff; border-radius: 12px; margin: 16px 0; }
        p { margin: 0 0 8px; font-size: 0.9375rem; line-height: 1.5; }
        .note { font-size: 0.8125rem; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>رمز المصادقة الثنائية</h1>
        <p>استخدم الرمز التالي لإكمال تسجيل الدخول:</p>
        <div class="code"><?php echo e($code); ?></div>
        <p class="note">هذا الرمز صالح لمدة 10 دقائق. لم تطلب هذا الرمز؟ تجاهل هذه الرسالة وتأكد من تغيير كلمة المرور.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/emails/two-factor-code.blade.php ENDPATH**/ ?>