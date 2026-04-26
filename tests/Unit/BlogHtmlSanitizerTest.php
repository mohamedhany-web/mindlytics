<?php

namespace Tests\Unit;

use App\Support\BlogHtmlSanitizer;
use Tests\TestCase;

class BlogHtmlSanitizerTest extends TestCase
{
    public function test_strips_script_tags(): void
    {
        $out = BlogHtmlSanitizer::purify('<p>Hello</p><script>alert(1)</script>');

        $this->assertStringContainsString('<p>Hello</p>', $out);
        $this->assertStringNotContainsString('<script>', $out);
        $this->assertStringNotContainsString('alert', $out);
    }

    public function test_strips_onclick_handlers(): void
    {
        $out = BlogHtmlSanitizer::purify('<p onclick="evil()">x</p>');

        $this->assertStringNotContainsString('onclick', $out);
    }

    public function test_allows_safe_paragraph(): void
    {
        $out = BlogHtmlSanitizer::purify('<p class="intro"><strong>Bold</strong></p>');

        $this->assertStringContainsString('<p', $out);
        $this->assertStringContainsString('Bold', $out);
    }
}
