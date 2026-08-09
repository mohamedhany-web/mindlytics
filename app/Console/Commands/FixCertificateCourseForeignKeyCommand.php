<?php

namespace App\Console\Commands;

use App\Support\CertificateCourseForeignKey;
use Illuminate\Console\Command;

class FixCertificateCourseForeignKeyCommand extends Command
{
    protected $signature = 'certificates:fix-course-fk';

    protected $description = 'Point certificates.course_id foreign key to advanced_courses instead of legacy courses';

    public function handle(CertificateCourseForeignKey $fixer): int
    {
        $this->info('Fixing certificates.course_id foreign key...');

        $ok = $fixer->fix();

        if ($ok && $fixer->referencesAdvancedCourses()) {
            $this->info('Done — course_id now references advanced_courses.');

            return self::SUCCESS;
        }

        $this->error('Fix did not complete. Check DB user privileges for ALTER TABLE.');

        return self::FAILURE;
    }
}
