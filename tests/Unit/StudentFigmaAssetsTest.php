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

    public function test_asset_urls_use_root_relative_paths_by_default(): void
    {
        config(['app.asset_url' => null]);

        $url = StudentFigmaAssets::url('icon-dashboard.svg');

        $this->assertStringStartsWith('/images/student-portal/', $url);
        $this->assertStringNotContainsString('127.0.0.1', $url);
        $this->assertStringNotContainsString('localhost', $url);
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
            $this->assertGreaterThan(0, filesize($fullPath), "Empty student portal asset [{$key}]");
        }
    }

    public function test_every_shipped_portal_file_is_non_empty(): void
    {
        $files = StudentFigmaAssets::diskFiles();

        $this->assertNotEmpty($files, 'public/images/student-portal/ is empty');

        foreach ($files as $file) {
            $fullPath = public_path('images/student-portal/'.$file);
            $this->assertFileExists($fullPath, "Missing shipped portal file: {$file}");
            $this->assertGreaterThan(0, filesize($fullPath), "Empty shipped portal file: {$file}");
        }
    }
}
