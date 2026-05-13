<?php
    $faviconUrl = $platformFaviconUrl ?? \App\Support\SiteBranding::faviconUrl();
    $logoUrl = $platformLogoUrl ?? \App\Support\SiteBranding::logoUrl();
?>
<meta name="theme-color" content="#0ea5e9">
<meta name="msapplication-TileColor" content="#0ea5e9">
<link rel="icon" href="<?php echo e($faviconUrl); ?>" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo e($faviconUrl); ?>" type="image/x-icon">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($faviconUrl); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($faviconUrl); ?>">
<link rel="icon" type="image/png" sizes="48x48" href="<?php echo e($faviconUrl); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($logoUrl); ?>">
<link rel="manifest" href="<?php echo e(url('/site.webmanifest')); ?>">
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/components/favicon-meta.blade.php ENDPATH**/ ?>