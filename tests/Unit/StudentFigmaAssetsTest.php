<?php

namespace Tests\Unit;

use App\Support\StudentFigmaAssets;
use Tests\TestCase;

class StudentFigmaAssetsTest extends TestCase
{
    public function test_portal_theme_component_exists(): void
    {
        $this->assertFileExists(resource_path('views/components/student/portal-theme.blade.php'));
    }

    public function test_all_referenced_portal_assets_exist_on_disk(): void
    {
        foreach (StudentFigmaAssets::urls() as $key => $url) {
            $pathPart = parse_url($url, PHP_URL_PATH) ?: '';
            $relative = ltrim($pathPart, '/');
            $fullPath = public_path($relative);

            $this->assertFileExists(
                $fullPath,
                "Missing student portal asset [{$key}]: {$relative}"
            );
        }
    }
}
