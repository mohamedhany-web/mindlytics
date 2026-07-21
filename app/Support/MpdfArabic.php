<?php

namespace App\Support;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Throwable;

class MpdfArabic
{
    /**
     * @param  array<string, mixed>  $overrides
     */
    public static function make(array $overrides = []): Mpdf
    {
        $tempDir = storage_path('app/mpdf-temp');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        // DejaVu is always shipped with mPDF and supports Arabic; avoid custom/broken TTFs (e.g. cairo).
        $preferred = ['dejavusans', 'xbriyaz', 'freesans'];
        $font = 'dejavusans';
        foreach ($preferred as $candidate) {
            if (isset($defaultFontConfig['fontdata'][$candidate])) {
                $font = $candidate;
                break;
            }
        }

        $config = array_merge([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 14,
            'margin_bottom' => 14,
            'margin_header' => 6,
            'margin_footer' => 6,
            'tempDir' => $tempDir,
            'fontDir' => $defaultConfig['fontDir'],
            'fontdata' => $defaultFontConfig['fontdata'],
            'default_font' => $font,
            'directionality' => 'rtl',
            'autoScriptToLang' => false,
            'autoLangToFont' => false,
        ], $overrides);

        try {
            return new Mpdf($config);
        } catch (Throwable $e) {
            // Last resort: minimal config without custom fontDir overrides.
            return new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'tempDir' => $tempDir,
                'default_font' => 'dejavusans',
                'directionality' => 'rtl',
                'autoScriptToLang' => false,
                'autoLangToFont' => false,
            ]);
        }
    }
}
