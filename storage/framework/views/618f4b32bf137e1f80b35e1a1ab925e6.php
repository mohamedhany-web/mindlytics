
<?php
    $title = $title ?? 'Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي';
    $description = $description ?? 'تعلم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف مع Mindlytics. كورسات برمجة عربية شاملة، دروس تفاعلية، مشاريع عملية، وشهادات معتمدة.';
    $keywords = $keywords ?? 'برمجة, ذكاء اصطناعي, تعلم البرمجة, كورسات برمجة, دورات برمجة, برمجة عربية';
    $image = $image ?? asset('images/og-image.jpg');
    $url = $url ?? url()->current();
    $type = $type ?? 'website';
?>

<!-- Primary Meta Tags -->
<title><?php echo e($title); ?></title>
<meta name="title" content="<?php echo e($title); ?>">
<meta name="description" content="<?php echo e($description); ?>">
<meta name="keywords" content="<?php echo e($keywords); ?>">
<meta name="author" content="Mindlytics">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="language" content="Arabic">
<meta name="revisit-after" content="7 days">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo e($url); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo e($type); ?>">
<meta property="og:url" content="<?php echo e($url); ?>">
<meta property="og:title" content="<?php echo e($title); ?>">
<meta property="og:description" content="<?php echo e($description); ?>">
<meta property="og:image" content="<?php echo e($image); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="ar_AR">
<meta property="og:site_name" content="Mindlytics">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo e($url); ?>">
<meta name="twitter:title" content="<?php echo e($title); ?>">
<meta name="twitter:description" content="<?php echo e($description); ?>">
<meta name="twitter:image" content="<?php echo e($image); ?>">

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\components\seo-meta.blade.php ENDPATH**/ ?>