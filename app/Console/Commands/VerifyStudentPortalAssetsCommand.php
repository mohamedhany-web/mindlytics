<?php

namespace App\Console\Commands;

use App\Support\StudentFigmaAssets;
use Illuminate\Console\Command;

class VerifyStudentPortalAssetsCommand extends Command
{
    protected $signature = 'student-portal:verify-assets';

    protected $description = 'Verify student portal dashboard/sidebar images and icons exist on disk';

    public function handle(): int
    {
        $dir = public_path('images/student-portal');
        $this->line('Directory: '.$dir);

        if (! is_dir($dir)) {
            $this->error('Missing directory public/images/student-portal — run git pull on the server.');
            return self::FAILURE;
        }

        $diskFiles = StudentFigmaAssets::diskFiles();
        $this->info('Files on disk: '.count($diskFiles));

        $missing = [];
        foreach (StudentFigmaAssets::urls() as $key => $url) {
            $pathPart = parse_url($url, PHP_URL_PATH) ?: $url;
            $file = basename($pathPart);
            $full = StudentFigmaAssets::fullPath($file);
            if (! is_file($full) || filesize($full) <= 0) {
                $missing[] = $key.' => '.$file;
            }
        }

        if ($missing !== []) {
            $this->error('Missing or empty referenced assets:');
            foreach ($missing as $line) {
                $this->line('  - '.$line);
            }
            $this->newLine();
            $this->warn('Fix: git pull origin main  (then php artisan view:clear)');

            return self::FAILURE;
        }

        $this->info('All referenced portal assets OK ('.count(StudentFigmaAssets::urls()).').');

        $sample = StudentFigmaAssets::url('icon-dashboard.svg');
        $this->line('Sample icon URL path: '.$sample);

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '') {
            $this->line('Open in browser: '.$appUrl.$sample);
        }

        $this->newLine();
        $this->line('After deploy: php artisan view:clear');

        return self::SUCCESS;
    }
}
