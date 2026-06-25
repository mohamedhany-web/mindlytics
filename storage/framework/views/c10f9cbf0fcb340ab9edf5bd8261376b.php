<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title><?php echo e($title ?? 'Video'); ?></title>
    <style>
        html, body { height: 100%; margin: 0; background: #000; }
        .wrap { position: fixed; inset: 0; }
        iframe, video { width: 100%; height: 100%; border: 0; display: block; }
    </style>
</head>
<body>
<div class="wrap">
    <?php if(($type ?? '') === 'html5'): ?>
        <video playsinline controls controlsList="nodownload noplaybackrate noremoteplayback" disablePictureInPicture disableRemotePlayback>
            <source src="<?php echo e($src); ?>" type="<?php echo e($mime ?? 'video/mp4'); ?>">
        </video>
    <?php else: ?>
        <iframe src="<?php echo e($src); ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                allowfullscreen
                loading="eager"
                title="<?php echo e($title ?? 'Video'); ?>"></iframe>
    <?php endif; ?>
</div>
</body>
</html>

<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/video/protected-embed.blade.php ENDPATH**/ ?>