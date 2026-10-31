<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @json(...) يطبع نصوصاً بين علامتي "…"؛ داخل خاصية HTML بين "…" (x-data="…") يُغلق الخاصية مبكراً
 * فلا يعمل Alpine (هكذا تعطّل نموذج «عميل جديد» في المبيعات). داخل الخصائص استخدم {{ Js::from(...) }}.
 */
class BladeJsonInAttributeTest extends TestCase
{
    public function test_no_json_directive_inside_double_quoted_attributes(): void
    {
        $root = dirname(__DIR__, 2);
        $problems = [];
        foreach (['resources/views', 'app/Domains'] as $dir) {
            if (! is_dir("{$root}/{$dir}")) {
                continue;
            }
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("{$root}/{$dir}")) as $file) {
                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }
                $source = file_get_contents($file->getPathname());
                if (! str_contains($source, '@json(')) {
                    continue;
                }
                preg_match_all('/\s(x-data|x-init|x-bind:[\w.-]+|:[\w.-]+|@[\w.-]+|x-on:[\w.-]+)="/', $source, $matches, PREG_OFFSET_CAPTURE);
                foreach ($matches[0] as [$attr, $offset]) {
                    $start = $offset + strlen($attr);
                    $end = strpos($source, '"', $start);
                    $value = substr($source, $start, $end === false ? 0 : $end - $start);
                    if (str_contains($value, '@json(')) {
                        $line = substr_count(substr($source, 0, $start), "\n") + 1;
                        $problems[] = str_replace($root.'/', '', $file->getPathname()).":{$line} ".trim($attr);
                    }
                }
            }
        }

        $this->assertSame([], $problems, "@json inside an HTML attribute (use {{ Js::from(...) }}):\n".implode("\n", $problems));
    }
}
