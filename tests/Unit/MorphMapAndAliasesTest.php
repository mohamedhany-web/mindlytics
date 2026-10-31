<?php

namespace Tests\Unit;

use App\Support\LegacyClassAliases;
use App\Support\MorphMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tests\TestCase;

class MorphMapAndAliasesTest extends TestCase
{
    public function test_morph_map_targets_exist_and_are_models(): void
    {
        foreach (MorphMap::map() as $alias => $class) {
            $this->assertTrue(class_exists($class), "Morph target {$class} ({$alias}) does not exist");
            $this->assertTrue(is_subclass_of($class, Model::class), "{$class} is not an Eloquent model");
        }
    }

    public function test_morph_map_is_registered(): void
    {
        foreach (MorphMap::map() as $alias => $class) {
            $this->assertSame($class, Relation::getMorphedModel($alias));
        }
    }

    /** المفاتيح يجب أن تبقى النصوص المحفوظة في الداتابيز، حتى بعد نقل الـ Model. */
    public function test_morph_keys_keep_legacy_database_values(): void
    {
        foreach (array_keys(MorphMap::map()) as $alias) {
            $this->assertStringStartsWith('App\\Models\\', $alias);
        }

        $this->assertSame('App\\Models\\Lecture', (new \App\Domains\Learning\Models\Lecture)->getMorphClass());
    }

    public function test_legacy_aliases_point_to_existing_classes(): void
    {
        $this->assertIsArray(LegacyClassAliases::MAP);

        foreach (LegacyClassAliases::MAP as $old => $new) {
            $this->assertTrue(class_exists($new), "Alias target {$new} for {$old} does not exist");
            $this->assertTrue(class_exists($old), "Legacy name {$old} does not resolve");
            $this->assertSame($new, LegacyClassAliases::resolve($old));
        }
    }

    public function test_resolve_leaves_unknown_classes_untouched(): void
    {
        $this->assertSame('App\\Models\\User', LegacyClassAliases::resolve('App\\Models\\User'));
        $this->assertSame('User', LegacyClassAliases::resolve('User'));
    }
}
