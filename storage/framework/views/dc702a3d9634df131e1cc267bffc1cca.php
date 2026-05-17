<?php
    /** @var \App\Mail\PlatformNotificationMail $this */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($titleText); ?></title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Tahoma,Arial,sans-serif;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
            <div style="padding:18px 20px;background:linear-gradient(135deg,#0ea5e9,#2563eb);color:#fff;">
                <div style="font-size:16px;font-weight:700;line-height:1.4;"><?php echo e($titleText); ?></div>
            </div>
            <div style="padding:18px 20px;color:#111827;">
                <div style="font-size:14px;line-height:1.8;white-space:pre-wrap;"><?php echo e($messageText); ?></div>

                <?php if(!empty($actionUrl)): ?>
                    <div style="margin-top:18px;">
                        <a href="<?php echo e($actionUrl); ?>"
                           style="display:inline-block;background:#0ea5e9;color:#fff;text-decoration:none;padding:10px 14px;border-radius:12px;font-weight:700;font-size:14px;">
                            <?php echo e($actionText ?: 'فتح'); ?>

                        </a>
                    </div>
                <?php endif; ?>

                <div style="margin-top:18px;color:#6b7280;font-size:12px;line-height:1.6;">
                    تم إرسال هذه الرسالة تلقائياً من منصة <?php echo e(config('app.name', 'Mindlytics')); ?>.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/emails/platform-notification.blade.php ENDPATH**/ ?>