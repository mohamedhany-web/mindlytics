<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subjectLine }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        .wrapper { width: 100%; background-color: #f1f5f9; padding: 32px 16px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 50%, #0f766e 100%); padding: 32px 28px; text-align: center; }
        .header-title { color: #ffffff; font-size: 22px; font-weight: 800; margin: 0 0 6px; letter-spacing: -0.02em; }
        .header-sub { color: rgba(255,255,255,0.9); font-size: 14px; margin: 0; }
        .body-cell { padding: 28px 28px 32px; color: #334155; font-size: 16px; line-height: 1.7; }
        .body-cell p { margin: 0 0 14px; }
        .body-cell p:last-child { margin-bottom: 0; }
        .greeting { color: #0f172a; font-weight: 700; font-size: 17px; margin-bottom: 16px !important; }
        .footer { background: #f8fafc; padding: 20px 28px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer-text { color: #64748b; font-size: 13px; margin: 0; }
        .footer-brand { color: #0ea5e9; font-weight: 700; text-decoration: none; }
        .divider { height: 1px; background: #e2e8f0; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="wrapper">
            <tr>
                <td align="center">
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="container">
                        <tr>
                            <td>
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="card">
                                    <tr>
                                        <td class="header">
                                            <p class="header-title">مجتمع الذكاء الاصطناعي</p>
                                            <p class="header-sub">إشعار من إدارة المجتمع</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="body-cell">
                                            @if($recipientName)
                                                <p class="greeting">مرحباً {{ $recipientName }}،</p>
                                            @else
                                                <p class="greeting">مرحباً،</p>
                                            @endif
                                            <div class="divider" style="height:1px;background:#e2e8f0;margin:20px 0;"></div>
                                            <div style="color:#334155;font-size:16px;line-height:1.7;">
                                                {!! nl2br(e($body)) !!}
                                            </div>
                                            <div class="divider" style="height:1px;background:#e2e8f0;margin:24px 0;"></div>
                                            <p style="margin:0;font-size:14px;color:#64748b;">يمكنك زيارة مجتمعنا من لوحة التحكم أو من صفحة المجتمع على الموقع.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="footer">
                                            <p class="footer-text">هذه الرسالة من <a href="{{ url('/') }}" class="footer-brand">{{ config('app.name') }}</a> — مجتمع البيانات والذكاء الاصطناعي.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
