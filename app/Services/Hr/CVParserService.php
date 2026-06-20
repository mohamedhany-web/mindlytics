<?php

namespace App\Services\Hr;

use App\Models\HrApplicationFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class CVParserService
{
    /**
     * @param  list<string>  $extraSkillHints
     * @return array{
     *     text: string,
     *     email: ?string,
     *     phone: ?string,
     *     skills: list<string>,
     *     education: ?string,
     *     experience_years: ?float
     * }
     */
    public function parseFromFile(HrApplicationFile $file, array $extraSkillHints = []): array
    {
        $text = $this->extractText($file);

        return $this->parseText($text, $extraSkillHints);
    }

    /**
     * @param  list<string>  $extraSkillHints
     * @return array{
     *     text: string,
     *     email: ?string,
     *     phone: ?string,
     *     skills: list<string>,
     *     education: ?string,
     *     experience_years: ?float
     * }
     */
    public function parseText(string $text, array $extraSkillHints = []): array
    {
        $normalized = $this->normalizeText($text);

        return [
            'text' => $normalized,
            'email' => $this->extractEmail($normalized),
            'phone' => $this->extractPhone($normalized),
            'skills' => $this->extractSkills($normalized, $extraSkillHints),
            'education' => $this->extractEducation($normalized),
            'experience_years' => $this->extractExperienceYears($normalized),
        ];
    }

    private function extractText(HrApplicationFile $file): string
    {
        try {
            $binary = Storage::disk($file->disk)->get($file->path);
        } catch (\Throwable) {
            return '';
        }

        if ($binary === '' || $binary === null) {
            return '';
        }

        $temp = tempnam(sys_get_temp_dir(), 'hr-cv-');
        if ($temp === false) {
            return '';
        }

        file_put_contents($temp, $binary);

        try {
            $ext = strtolower(pathinfo((string) $file->path, PATHINFO_EXTENSION));
            $mime = strtolower((string) ($file->mime ?? ''));

            if ($ext === 'pdf' || str_contains($mime, 'pdf')) {
                return $this->extractPdfText($temp);
            }

            if (in_array($ext, ['docx', 'doc'], true) || str_contains($mime, 'word')) {
                return $this->extractDocxText($temp, $ext);
            }

            return '';
        } finally {
            @unlink($temp);
        }
    }

    private function extractPdfText(string $path): string
    {
        try {
            $parser = new PdfParser;

            return trim($parser->parseFile($path)->getText());
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractDocxText(string $path, string $ext): string
    {
        if ($ext === 'docx' || $this->isZipFile($path)) {
            return $this->extractDocxZipText($path);
        }

        return '';
    }

    private function isZipFile(string $path): bool
    {
        $fh = @fopen($path, 'rb');
        if (! $fh) {
            return false;
        }
        $header = fread($fh, 2);
        fclose($fh);

        return $header === "PK";
    }

    private function extractDocxZipText(string $path): string
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! is_string($xml) || $xml === '') {
            return '';
        }

        $xml = preg_replace('/<w:tab\/>/', ' ', $xml) ?? $xml;
        $xml = preg_replace('/<\/w:p>/', "\n", $xml) ?? $xml;
        $text = strip_tags($xml);

        return trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function extractEmail(string $text): ?string
    {
        if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $text, $m)) {
            return strtolower($m[0]);
        }

        return null;
    }

    private function extractPhone(string $text): ?string
    {
        if (preg_match('/(?:\+?\d{1,3}[\s\-]?)?(?:\(?\d{2,4}\)?[\s\-]?)?\d{3,4}[\s\-]?\d{3,4}[\s\-]?\d{0,4}/', $text, $m)) {
            $digits = preg_replace('/\D+/', '', $m[0]);
            if ($digits !== null && strlen($digits) >= 8) {
                return $m[0];
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $extraSkillHints
     * @return list<string>
     */
    private function extractSkills(string $text, array $extraSkillHints = []): array
    {
        $haystack = mb_strtolower($text);
        $found = [];

        $keywords = array_unique(array_merge(
            config('hr.skill_keywords', []),
            array_map('strtolower', $extraSkillHints)
        ));

        foreach ($keywords as $keyword) {
            $keyword = trim(mb_strtolower((string) $keyword));
            if ($keyword === '') {
                continue;
            }

            if (str_contains($haystack, $keyword)) {
                $found[] = $this->titleSkill($keyword);
            }
        }

        return array_values(array_unique($found));
    }

    private function extractEducation(string $text): ?string
    {
        $haystack = mb_strtolower($text);
        $keywords = config('hr.education_keywords', []);
        $best = null;
        $bestRank = 0;
        $levels = config('hr.education_levels', []);

        foreach ($keywords as $level => $terms) {
            foreach ($terms as $term) {
                if (str_contains($haystack, mb_strtolower($term))) {
                    $rank = (int) ($levels[$level]['rank'] ?? 0);
                    if ($rank >= $bestRank) {
                        $bestRank = $rank;
                        $best = $level;
                    }
                }
            }
        }

        return $best;
    }

    private function extractExperienceYears(string $text): ?float
    {
        $haystack = mb_strtolower($text);
        $candidates = [];

        $patterns = [
            '/(\d+(?:\.\d+)?)\s*\+?\s*(?:years?|yrs?|year\'s)\s*(?:of\s*)?(?:experience|exp)?/i',
            '/(\d+(?:\.\d+)?)\s*\+?\s*(?:سنة|سنوات|عام|أعوام)/u',
            '/experience\s*[:\-]?\s*(\d+(?:\.\d+)?)/i',
            '/خبرة\s*[:\-]?\s*(\d+(?:\.\d+)?)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $haystack, $matches)) {
                foreach ($matches[1] as $num) {
                    $val = (float) $num;
                    if ($val >= 0 && $val <= 50) {
                        $candidates[] = $val;
                    }
                }
            }
        }

        if ($candidates === []) {
            return null;
        }

        return max($candidates);
    }

    private function titleSkill(string $skill): string
    {
        if (str_contains($skill, ' ')) {
            return Str::title($skill);
        }

        $map = [
            'sql' => 'SQL',
            'php' => 'PHP',
            'html' => 'HTML',
            'css' => 'CSS',
            'aws' => 'AWS',
            'seo' => 'SEO',
            'r' => 'R',
            'c#' => 'C#',
            'c++' => 'C++',
        ];

        return $map[$skill] ?? Str::title($skill);
    }
}
