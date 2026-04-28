#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Static security audit (no browser). Run: php scripts/security_audit.php
 * Exit 0 = no blocking issues; 1 = fix required.
 */

use Symfony\Component\Process\Process;

$root = dirname(__DIR__);

if (! is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "Run composer install first.\n");
    exit(1);
}

require $root.'/vendor/autoload.php';

$failures = [];
$warnings = [];

// --- Composer advisory DB ---
$composerCandidates = [
    ['composer', 'audit', '--format=json'],
];
if (PHP_OS_FAMILY === 'Windows') {
    // Some Windows setups expose composer as composer.bat only.
    array_unshift($composerCandidates, ['composer.bat', 'audit', '--format=json']);
}
if (is_file($root.'/composer.phar')) {
    $composerCandidates[] = [PHP_BINARY, 'composer.phar', 'audit', '--format=json'];
}

$auditJson = null;
$auditRan = false;
$auditErrors = [];
foreach ($composerCandidates as $cmd) {
    $audit = new Process($cmd, $root, null, null, 300.0);
    $audit->run();
    if ($audit->isSuccessful()) {
        $auditRan = true;
        $auditJson = $audit->getOutput();
        break;
    }
    $auditErrors[] = trim($audit->getErrorOutput()) ?: trim($audit->getOutput());
}

if (! $auditRan) {
    // Local/dev environments may not have composer available in PATH.
    // This should not fail the whole audit script; emit warning instead.
    $warnings[] = 'composer audit failed to run (composer not available?): '.implode(' | ', array_filter($auditErrors));
} else {
    $json = json_decode((string) $auditJson, true);
    if (! is_array($json)) {
        $failures[] = 'composer audit returned invalid JSON';
    } elseif (! empty($json['advisories'])) {
        $failures[] = 'composer audit reports advisories: '.json_encode($json['advisories'], JSON_UNESCAPED_UNICODE);
    }
}

// --- .env must not be committed (gitignore) ---
$gitignore = $root.'/.gitignore';
if (is_readable($gitignore)) {
    $gi = file_get_contents($gitignore);
    if ($gi !== false && ! preg_match('/^\.env$/m', $gi) && ! preg_match('/^\.env\s/m', $gi)) {
        $warnings[] = '.gitignore may be missing a standalone .env entry';
    }
}

// --- Blade: raw echo of variables (high XSS risk) ---
$allowedSubstrings = [
    'BlogHtmlSanitizer::purify',
    'json_encode(',
    'nl2br(e(',
    '__(',
    'VideoHelper::generateEmbedHtml',
    '$lecturesDataJson',
];

$viewsDir = $root.'/resources/views';
if (is_dir($viewsDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $fileInfo) {
        $path = $fileInfo->getPathname();
        if (! str_ends_with($path, '.blade.php')) {
            continue;
        }
        $lines = @file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            continue;
        }
        foreach ($lines as $num => $line) {
            if (! str_contains($line, '{!!')) {
                continue;
            }
            $allowed = false;
            foreach ($allowedSubstrings as $frag) {
                if (str_contains($line, $frag)) {
                    $allowed = true;
                    break;
                }
            }
            if ($allowed) {
                continue;
            }
            // {!! $var or {!!$var without allowlist
            if (preg_match('/\{!!\s*\$/', $line)) {
                $rel = str_replace($root.DIRECTORY_SEPARATOR, '', $path);
                $warnings[] = sprintf('Review raw Blade echo in %s:%d — %s', $rel, $num + 1, trim($line));
            }
        }
    }
}

// --- Dangerous PHP patterns under app/ (not vendor) ---
$appDir = $root.'/app';
$riskPatterns = [
    '/\beval\s*\(\s*[\'"]/' => 'eval() with string literal',
    '/\bassert\s*\(\s*[\'"]/' => 'assert() with string (deprecated risky pattern)',
    '/\b(passthru|shell_exec|system|exec)\s*\(\s*\$_(GET|POST|REQUEST)/' => 'shell primitive fed directly from request',
];
if (is_dir($appDir)) {
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $fileInfo) {
        if ($fileInfo->getExtension() !== 'php') {
            continue;
        }
        $content = @file_get_contents($fileInfo->getPathname());
        if ($content === false) {
            continue;
        }
        foreach ($riskPatterns as $regex => $label) {
            if (preg_match($regex, $content)) {
                $rel = str_replace($root.DIRECTORY_SEPARATOR, '', $fileInfo->getPathname());
                $failures[] = "Risk pattern in app: {$label} ({$rel})";
            }
        }
    }
}

// --- Report ---
foreach ($warnings as $w) {
    fwrite(STDERR, "[WARN] {$w}\n");
}

foreach ($failures as $f) {
    fwrite(STDERR, "[FAIL] {$f}\n");
}

if ($failures !== []) {
    fwrite(STDERR, "\nSecurity audit finished with ".count($failures)." failure(s).\n");
    exit(1);
}

echo "Security audit OK (".count($warnings)." warning(s)).\n";
exit(0);
