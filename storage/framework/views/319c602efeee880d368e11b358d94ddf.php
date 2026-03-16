<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تأكيد حجز الورشة</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;font-family:'Tahoma','Arial',sans-serif;direction:rtl;text-align:right;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f172a;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0f172a,#020617);border-radius:20px;overflow:hidden;color:#f9fafb;border:1px solid #1e293b;">
                    <tr>
                        <td style="padding:24px 28px 16px 28px;border-bottom:1px solid #1f2937;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-size:20px;font-weight:800;color:#e5e7eb;">
                                        تأكيد حجزك في الورشة
                                    </td>
                                    <td align="left" style="font-size:12px;color:#9ca3af;">
                                        Mindlytics
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px 8px 28px;">
                            <p style="margin:0 0 8px 0;font-size:14px;color:#e5e7eb;">
                                مرحباً <?php echo e($registration->name ?? 'متدربنا العزيز'); ?> 👋
                            </p>
                            <p style="margin:0 0 12px 0;font-size:13px;color:#cbd5f5;line-height:1.7;">
                                نشكرك على تسجيلك في الورشة التالية، ويسعدنا إبلاغك أنه تم قبول حجزك مبدئياً. يرجى الاحتفاظ بهذه الرسالة وإبراز رمز الحضور (الباركود) عند دخول الورشة.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 8px 28px;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:radial-gradient(circle at top,#1d4ed8 0,#020617 55%);border-radius:16px;padding:16px 18px;border:1px solid #1e3a8a;">
                                <tr>
                                    <td style="font-size:15px;font-weight:800;color:#e5e7eb;padding-bottom:4px;">
                                        <?php echo e($workshop->title); ?>

                                    </td>
                                </tr>
                                <?php if($workshop->location): ?>
                                <tr>
                                    <td style="font-size:12px;color:#cbd5f5;padding-bottom:4px;">
                                        📍 <strong>المكان:</strong> <?php echo e($workshop->location); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if($workshop->starts_at): ?>
                                <tr>
                                    <td style="font-size:12px;color:#cbd5f5;padding-bottom:4px;">
                                        📅 <strong>الميعاد:</strong>
                                        <?php echo e($workshop->starts_at->format('Y-m-d H:i')); ?>

                                        <?php if($workshop->ends_at): ?>
                                            — <?php echo e($workshop->ends_at->format('H:i')); ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="font-size:12px;color:#cbd5f5;padding-bottom:4px;">
                                        🎟 <strong>طريقة حضورك:</strong>
                                        <?php
                                            $modeText = $registration->attendance_mode === 'offline'
                                                ? 'في المكان (أوفلاين)'
                                                : ($registration->attendance_mode === 'online' ? 'أونلاين (عن بُعد)' : 'سيتم تحديدها مع فريق الدعم');
                                        ?>
                                        <?php echo e($modeText); ?>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 28px 8px 28px;">
                            <p style="margin:0 0 8px 0;font-size:13px;color:#e5e7eb;font-weight:700;">
                                رمز الحضور الخاص بك (Barcode / QR):
                            </p>
                            <p style="margin:0 0 8px 0;font-size:11px;color:#9ca3af;">
                                احتفظ بهذا الرمز ولا تشاركه مع أي شخص. سيقوم فريق الاستقبال بمسحه عند دخولك للتأكد من حضورك.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:4px 28px 20px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="background-color:#020617;border-radius:18px;padding:14px 18px;border:1px dashed #1e293b;">
                                <tr>
                                    <td align="center" style="padding-bottom:8px;">
                                        <img src="<?php echo e($qrUrl); ?>" alt="QR Code" width="180" height="180" style="display:block;border-radius:12px;border:1px solid #1f2933;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size:11px;color:#9ca3af;direction:ltr;">
                                        <?php echo e($registration->checkin_token); ?>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 20px 28px;">
                            <p style="margin:0 0 8px 0;font-size:12px;color:#cbd5f5;line-height:1.7;">
                                في حال وجود أي استفسار بخصوص الورشة أو طريقة الحضور، يمكنك الرد على هذه الرسالة أو التواصل مع فريق الدعم لدى Mindlytics.
                            </p>
                            <p style="margin:0;font-size:11px;color:#6b7280;">
                                نتمنى لك تجربة ثرية وممتعة 💙
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 28px 18px 28px;border-top:1px solid #1f2937;font-size:10px;color:#6b7280;text-align:center;">
                            © <?php echo e(date('Y')); ?> Mindlytics. جميع الحقوق محفوظة.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/emails/workshop-acceptance.blade.php ENDPATH**/ ?>