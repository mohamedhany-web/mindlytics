<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم تفعيل الكورس</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; color: #334155; }
        .box { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
        h1 { font-size: 1.25rem; color: #0f172a; margin: 0 0 16px; }
        p { margin: 0 0 10px; font-size: 0.9375rem; line-height: 1.6; }
        .card { background: #f8fafc; border-radius: 12px; padding: 14px; margin: 12px 0; border-right: 4px solid #3b82f6; }
        .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .value { font-weight: 600; color: #0f172a; }
        .btn { display: inline-block; margin-top: 16px; padding: 12px 24px; background: #2563eb; color: #fff !important; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9375rem; }
        .btn:hover { background: #1d4ed8; }
        .note { font-size: 0.8125rem; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <?php
            $student = $enrollment->student;
            $course  = $enrollment->course;
        ?>
        <h1>تم تفعيل الكورس الخاص بك</h1>
        <p>
            مرحباً <?php echo e($student?->name ?? 'طالبنا العزيز'); ?>،
        </p>
        <p>
            تم تفعيل اشتراكك في الكورس:
        </p>

        <div class="card">
            <div class="label">اسم الكورس</div>
            <div class="value"><?php echo e($course?->title ?? 'الكورس'); ?></div>
        </div>

        <?php if($course && $course->academicYear): ?>
            <div class="card">
                <div class="label">المسار / السنة الأكاديمية</div>
                <div class="value"><?php echo e($course->academicYear->name); ?></div>
            </div>
        <?php endif; ?>

        <p>
            يمكنك الآن الدخول إلى الكورس وبدء التعلّم من خلال المنصة.
        </p>

        <a href="<?php echo e(url(route('public.course.show', $course?->id ?? 0))); ?>" class="btn">
            الدخول إلى صفحة الكورس
        </a>

        <p class="note">
            في حال لم تتمكن من الدخول، تأكد من تسجيل الدخول بنفس البريد الإلكتروني الذي استلم هذه الرسالة.
        </p>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/emails/course-enrollment-activated.blade.php ENDPATH**/ ?>