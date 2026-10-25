<!DOCTYPE html>
<html lang="{{ ($design['rtl'] ?? false) ? 'ar' : 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $design['name'] ?? 'Certificate' }} preview</title>
    <style>body{margin:0;background:#111;}</style>
</head>
<body>
@include('components.certificates.designs.'.$key, [
    'studentName' => $sampleName,
    'courseTitle' => $sampleCourse,
    'serialNumber' => 'MIND-'.date('Y').'-PREVIEW',
    'issueDateFormatted' => now()->format('j / n / Y'),
    'instructorName' => $sampleInstructor,
    'templateDomId' => 'preview-cert',
])
</body>
</html>
