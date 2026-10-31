<?php

namespace Tests\Feature\Architecture;

use App\Support\Architecture\ModularityChecks;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * سلامة نقل الكود إلى app/Domains بدون قاعدة بيانات (docs/MODULARIZATION_PLAN.md).
 * نفس الفحوصات يشغّلها السيرفر بعد الرفع: php artisan modularity:verify
 */
class ModularityIntegrityTest extends TestCase
{
    public function test_every_referenced_class_exists(): void
    {
        $this->assertSame([], ModularityChecks::missingClassReferences(), 'Classes referenced in code do not exist');
    }

    public function test_every_referenced_view_exists(): void
    {
        $this->assertSame([], ModularityChecks::missingViewReferences(), 'Views referenced in code do not exist');
    }

    public function test_every_blade_template_compiles_to_valid_php(): void
    {
        $this->assertSame([], ModularityChecks::brokenBladeTemplates(), 'Blade templates with PHP syntax errors');
    }

    public function test_class_names_stored_in_the_database_are_unchanged(): void
    {
        $this->assertSame([], ModularityChecks::storedClassNameProblems(), 'Stored class names (morph types) would change');
    }

    public function test_domains_are_wired(): void
    {
        $this->assertSame([], ModularityChecks::wiringProblems(), 'Domain providers, views, route files or observers are not wired');
    }

    /** لو أُصلح view مفقود من قبل النقل احذفه من القائمة حتى تبقى دقيقة. */
    public function test_known_missing_views_are_still_missing(): void
    {
        $fixed = array_values(array_filter(ModularityChecks::KNOWN_MISSING_VIEWS, fn ($v) => View::exists($v)));
        $this->assertSame([], $fixed, 'These views exist now; remove them from ModularityChecks::KNOWN_MISSING_VIEWS');
    }

    /** لو أُصلح قالب مكسور من قبل النقل احذفه من القائمة حتى يُفحص من جديد. */
    public function test_known_broken_templates_are_still_broken(): void
    {
        $fixed = [];
        foreach (ModularityChecks::KNOWN_BROKEN_TEMPLATES as $name) {
            try {
                token_get_all(Blade::compileString(file_get_contents(View::getFinder()->find($name))), TOKEN_PARSE);
                $fixed[] = $name;
            } catch (\Throwable) {
            }
        }
        $this->assertSame([], $fixed, 'These templates compile now; remove them from ModularityChecks::KNOWN_BROKEN_TEMPLATES');
    }
}
