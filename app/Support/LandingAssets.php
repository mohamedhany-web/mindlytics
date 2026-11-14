<?php

namespace App\Support;

class LandingAssets
{
    public const BASE_PATH = '/images/landing';

    public static function path(string $name): string
    {
        return self::BASE_PATH.'/'.ltrim(str_replace('\\', '/', $name), '/');
    }

    public static function url(string $name): string
    {
        $file = ltrim(str_replace('\\', '/', $name), '/');
        $path = self::path($file);
        $full = public_path('images/landing/'.$file);

        if (is_file($full)) {
            $path .= (str_contains($path, '?') ? '&' : '?').'v='.filemtime($full);
        }

        return $path;
    }

    /**
     * @return array<string, string>
     */
    public static function urls(): array
    {
        return [
            'hero_scroll' => self::url('hero-scroll.svg'),
            'hero_dot' => self::url('hero-dot.svg'),
            'hero_play' => self::url('hero-play.svg'),
            'hero_arrow' => self::url('hero-arrow.svg'),
            'hero_stars' => self::url('hero-stars.svg'),
            'stats_divider' => self::url('stats-divider.svg'),
            'who_play' => self::url('who-play.svg'),
            'who_lock' => self::url('who-lock.svg'),
            'who_book' => self::url('who-book.svg'),
            'who_upload' => self::url('who-upload.svg'),
            'who_award' => self::url('who-award.svg'),
            'why_book' => self::url('why-book.svg'),
            'why_star' => self::url('why-star.svg'),
            'how_line' => self::url('how-line.svg'),
            'course_star' => self::url('course-star.svg'),
            'course_book' => self::url('course-book.svg'),
            'course_clock' => self::url('course-clock.svg'),
            'instructor_clock' => self::url('instructor-clock.svg'),
            'instructor_video' => self::url('instructor-video.svg'),
            'instructor_verified' => self::url('instructor-verified.svg'),
            'instructor_thumb' => self::url('instructor-thumb.svg'),
            'header_arrow' => self::url('header-arrow.svg'),
            'header_globe' => self::url('header-globe.svg'),
            'header_book' => self::url('header-book.svg'),
            'hero_video' => self::url('hero-video.jpg'),
            'hero_instructor' => self::url('hero-instructor.jpg'),
            'hero_avatar' => self::url('hero-avatar.jpg'),
            'why_media' => self::url('why-media.jpg'),
            'who_about' => self::url('who-about.jpg'),
        ];
    }
}
