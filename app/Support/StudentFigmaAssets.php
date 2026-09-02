<?php

namespace App\Support;

class StudentFigmaAssets
{
    public static function url(string $name): string
    {
        return asset('images/student-portal/' . ltrim($name, '/'));
    }

    public static function urls(): array
    {
        return [
            'dashboard' => self::url('icon-dashboard.svg'),
            'courses' => self::url('icon-courses.svg'),
            'classes' => self::url('icon-classes.svg'),
            'messages' => self::url('icon-messages.svg'),
            'notifications' => self::url('icon-notifications.svg'),
            'calendar' => self::url('icon-calendar.svg'),
            'community' => self::url('icon-community.svg'),
            'settings' => self::url('icon-settings.svg'),
            'search' => self::url('icon-search.svg'),
            'exams' => self::url('icon-exams.svg'),
            'certificates' => self::url('icon-certificates.svg'),
            'wallet' => self::url('icon-wallet.svg'),
            'orders' => self::url('icon-orders.svg'),
            'profile' => self::url('icon-profile.svg'),
            'path' => self::url('icon-path.svg'),
            'admin' => self::url('icon-admin.svg'),
            'star' => self::url('icon-star.svg'),
            'chevron' => self::url('icon-chevron.svg'),
            'plus' => self::url('icon-plus.svg'),
            'trend' => self::url('icon-trend.svg'),
            'dropdown' => self::url('icon-dropdown.svg'),
            'app_arrow' => self::url('icon-app-arrow.svg'),
            'app_blob' => self::url('app-blob.svg'),
            'cell' => self::url('icon-cell-1.svg'),
            'cell_code' => self::url('icon-cell-code.svg'),
            'cell_camera' => self::url('icon-cell-camera.svg'),
            'progress_ring' => self::url('progress-ring.svg'),
            'cal_arrow' => self::url('cal-arrow.svg'),
            'promo' => self::url('promo-illustration.png'),
            'promo_card' => self::url('promo-card.png'),
            'avatar_1' => self::url('avatar-1.png'),
            'avatar_2' => self::url('avatar-2.png'),
            'avatar_3' => self::url('avatar-3.png'),
            'avatar_4' => self::url('avatar-4.png'),
            'avatar_fallback' => self::url('avatar-1.png'),
        ];
    }
}
