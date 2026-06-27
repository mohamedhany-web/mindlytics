<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\MpdfArabic;

$html = <<<'HTML'
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8">
<style>body{font-family:cairo,sans-serif;direction:rtl;text-align:right;font-size:14pt;}</style>
</head>
<body>
<h1>تقرير أداء موظف المبيعات</h1>
<p>مرحباً — اختبار العربية: الأربعاء، المؤشر المركّب، إيرادات الفوز، صفقات مكسوبة.</p>
<p>1234567890 — أرقام إنجليزية بجانب نص عربي.</p>
</body>
</html>
HTML;

$mpdf = MpdfArabic::make();
$mpdf->WriteHTML($html);
$out = __DIR__.'/../storage/app/test-arabic-pdf.pdf';
$mpdf->Output($out, 'F');

echo "Wrote: {$out}\n";
