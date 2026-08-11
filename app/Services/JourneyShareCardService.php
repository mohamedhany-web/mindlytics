<?php

namespace App\Services;

use App\Models\JourneyProfile;
use App\Models\JourneyShareEvent;
use App\Models\PortfolioProject;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JourneyShareCardService
{
    public const TYPE_PROJECT_VERIFIED = 'project_verified';

    public const TYPE_PROFILE = 'profile';

    public const TYPE_FEATURED = 'featured';

    public const TYPE_MILESTONE = 'milestone';

    public function projectCardUrl(PortfolioProject $project, string $type = self::TYPE_PROJECT_VERIFIED): string
    {
        return route('public.portfolio.share-card', ['id' => $project->id, 'type' => $type]);
    }

    public function profileCardUrl(JourneyProfile $profile, string $type = self::TYPE_PROFILE): string
    {
        return route('public.journey.share-card', ['slug' => $profile->slug, 'type' => $type]);
    }

    public function linkedInShareUrl(string $canonicalUrl): string
    {
        return 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($canonicalUrl);
    }

    public function twitterShareUrl(string $canonicalUrl, string $text): string
    {
        return 'https://twitter.com/intent/tweet?url=' . urlencode($canonicalUrl) . '&text=' . urlencode($text);
    }

    public function facebookShareUrl(string $canonicalUrl): string
    {
        return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($canonicalUrl);
    }

    public function track(?User $user, string $shareableType, int $shareableId, string $channel, ?string $cardType = null): void
    {
        JourneyShareEvent::create([
            'user_id' => $user?->id,
            'shareable_type' => $shareableType,
            'shareable_id' => $shareableId,
            'channel' => $channel,
            'card_type' => $cardType,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 250, ''),
        ]);
    }

    /**
     * Generate a 1200×630 PNG share card using GD.
     */
    public function renderProjectPng(PortfolioProject $project, string $type = self::TYPE_PROJECT_VERIFIED): string
    {
        $project->loadMissing(['user', 'academicYear', 'advancedCourse', 'offlineCourse']);
        $cacheKey = 'share-cards/project-' . $project->id . '-' . $type . '-' . md5((string) $project->updated_at) . '.png';
        $path = storage_path('app/public/' . $cacheKey);

        if (File::exists($path) && File::lastModified($path) >= optional($project->updated_at)->getTimestamp()) {
            return $path;
        }

        File::ensureDirectoryExists(dirname($path));

        $w = 1200;
        $h = 630;
        $im = imagecreatetruecolor($w, $h);

        $bg1 = imagecolorallocate($im, 15, 23, 42); // slate-900
        $bg2 = imagecolorallocate($im, 30, 58, 138); // blue-900
        $white = imagecolorallocate($im, 255, 255, 255);
        $muted = imagecolorallocate($im, 203, 213, 225);
        $accent = imagecolorallocate($im, 16, 185, 129); // emerald
        $amber = imagecolorallocate($im, 245, 158, 11);

        // Gradient-like fill
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) (15 + (30 - 15) * $ratio);
            $g = (int) (23 + (58 - 23) * $ratio);
            $b = (int) (42 + (138 - 42) * $ratio);
            $col = imagecolorallocate($im, $r, $g, $b);
            imageline($im, 0, $y, $w, $y, $col);
        }

        // Accent bar
        imagefilledrectangle($im, 0, 0, 16, $h, $accent);

        $font = $this->resolveFont();
        $title = $type === self::TYPE_FEATURED ? 'FEATURED PROJECT' : 'PROJECT VERIFIED';
        $subtitle = 'Mindlytics Journey';
        $student = $project->user->name ?? 'Mindlytics Student';
        $projectTitle = Str::limit($project->title, 48, '…');
        $tech = is_array($project->technologies) ? implode(' · ', array_slice($project->technologies, 0, 4)) : '';
        $context = $project->programContextLabel() ?: $project->programTypeLabel();

        if ($font) {
            imagettftext($im, 22, 0, 64, 90, $muted, $font, $subtitle);
            imagettftext($im, 36, 0, 64, 160, $type === self::TYPE_FEATURED ? $amber : $accent, $font, $title);
            imagettftext($im, 42, 0, 64, 280, $white, $font, $this->fitText($projectTitle, 42, 1040, $font));
            imagettftext($im, 26, 0, 64, 360, $muted, $font, $this->fitText($student, 26, 1040, $font));
            if ($tech !== '') {
                imagettftext($im, 20, 0, 64, 430, $white, $font, $this->fitText($tech, 20, 1040, $font));
            }
            if ($context) {
                imagettftext($im, 18, 0, 64, 500, $muted, $font, $this->fitText((string) $context, 18, 900, $font));
            }
            imagettftext($im, 18, 0, 64, 575, $muted, $font, 'mindlytics.com/portfolio/' . $project->id);
        } else {
            imagestring($im, 5, 64, 70, $subtitle, $muted);
            imagestring($im, 5, 64, 120, $title, $accent);
            imagestring($im, 5, 64, 220, substr($projectTitle, 0, 60), $white);
            imagestring($im, 4, 64, 280, substr($student, 0, 50), $muted);
        }

        imagepng($im, $path, 6);
        imagedestroy($im);

        return $path;
    }

    public function renderProfilePng(JourneyProfile $profile, string $type = self::TYPE_PROFILE): string
    {
        $profile->loadMissing('user');
        $cacheKey = 'share-cards/profile-' . $profile->id . '-' . $type . '-' . md5((string) $profile->updated_at) . '.png';
        $path = storage_path('app/public/' . $cacheKey);

        if (File::exists($path) && File::lastModified($path) >= optional($profile->updated_at)->getTimestamp()) {
            return $path;
        }

        File::ensureDirectoryExists(dirname($path));

        $w = 1200;
        $h = 630;
        $im = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($im, 255, 255, 255);
        $muted = imagecolorallocate($im, 203, 213, 225);
        $accent = imagecolorallocate($im, 37, 99, 235);
        $emerald = imagecolorallocate($im, 16, 185, 129);

        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) (15 + (15 - 15) * $ratio);
            $g = (int) (23 + (40 - 23) * $ratio);
            $b = (int) (42 + (90 - 42) * $ratio);
            $col = imagecolorallocate($im, $r, $g, $b);
            imageline($im, 0, $y, $w, $y, $col);
        }
        imagefilledrectangle($im, 0, 0, 16, $h, $accent);

        $font = $this->resolveFont();
        $name = $profile->resolvedDisplayName();
        $headline = $profile->resolvedHeadline() ?: 'Building real projects at Mindlytics Academy';
        $count = PortfolioProject::published()->where('user_id', $profile->user_id)->count();
        $title = $type === self::TYPE_MILESTONE ? 'LEARNING MILESTONE' : 'MY LEARNING JOURNEY';

        if ($font) {
            imagettftext($im, 22, 0, 64, 90, $muted, $font, 'Mindlytics Journey');
            imagettftext($im, 34, 0, 64, 160, $emerald, $font, $title);
            imagettftext($im, 44, 0, 64, 280, $white, $font, $this->fitText($name, 44, 1040, $font));
            imagettftext($im, 24, 0, 64, 360, $muted, $font, $this->fitText($headline, 24, 1040, $font));
            imagettftext($im, 22, 0, 64, 450, $white, $font, $count . ' verified projects');
            if ($profile->is_open_to_work) {
                imagettftext($im, 20, 0, 64, 510, $emerald, $font, 'Open to work');
            }
            imagettftext($im, 18, 0, 64, 575, $muted, $font, 'mindlytics.com/j/' . $profile->slug);
        } else {
            imagestring($im, 5, 64, 80, 'Mindlytics Journey', $muted);
            imagestring($im, 5, 64, 200, substr($name, 0, 40), $white);
            imagestring($im, 4, 64, 280, $count . ' verified projects', $white);
        }

        imagepng($im, $path, 6);
        imagedestroy($im);

        return $path;
    }

    private function resolveFont(): ?string
    {
        $candidates = [
            storage_path('fonts/DejaVuSans.ttf'),
            public_path('fonts/DejaVuSans.ttf'),
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/segoeui.ttf',
            'C:/Windows/Fonts/tahoma.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];

        foreach ($candidates as $font) {
            if (is_string($font) && is_file($font)) {
                return $font;
            }
        }

        return null;
    }

    private function fitText(string $text, float $size, int $maxWidth, string $font): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        while (mb_strlen($text) > 3) {
            $box = @imagettfbbox($size, 0, $font, $text);
            if (! $box) {
                return $text;
            }
            $width = abs($box[2] - $box[0]);
            if ($width <= $maxWidth) {
                return $text;
            }
            $text = mb_substr($text, 0, mb_strlen($text) - 2) . '…';
        }

        return $text;
    }
}
