<?php

namespace Tests\Unit;

use App\Models\CourseCommunityPost;
use App\Support\CourseCommunityFeedBuilder;
use ReflectionClass;
use Tests\TestCase;

class CourseCommunityFeedBuilderCursorTest extends TestCase
{
    public function test_cursor_round_trip_pinned_and_unpinned(): void
    {
        $ref = new ReflectionClass(CourseCommunityFeedBuilder::class);
        $encode = $ref->getMethod('encodeCursor');
        $encode->setAccessible(true);
        $decode = $ref->getMethod('decodeCursor');
        $decode->setAccessible(true);

        $pinned = new CourseCommunityPost(['is_pinned' => true]);
        $pinned->setAttribute('id', 42);
        $raw = $encode->invoke(null, $pinned);
        $decoded = $decode->invoke(null, $raw);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['p']);
        $this->assertSame(42, $decoded['i']);

        $unpinned = new CourseCommunityPost(['is_pinned' => false]);
        $unpinned->setAttribute('id', 100);
        $raw2 = $encode->invoke(null, $unpinned);
        $decoded2 = $decode->invoke(null, $raw2);
        $this->assertSame(0, $decoded2['p']);
        $this->assertSame(100, $decoded2['i']);
    }

    public function test_decode_cursor_rejects_invalid_input(): void
    {
        $ref = new ReflectionClass(CourseCommunityFeedBuilder::class);
        $decode = $ref->getMethod('decodeCursor');
        $decode->setAccessible(true);

        $this->assertNull($decode->invoke(null, ''));
        $this->assertNull($decode->invoke(null, '!!!'));
    }
}
