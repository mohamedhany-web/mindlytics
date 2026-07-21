<?php

namespace App\Support;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class MpdfArabic
{
    /**
     * @param  array<string, mixed>  $overrides
     */
    public static function make(array $overrides = []): Mpdf
    {
        $tempDir = storage_path('app/mpdf-temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        // Prefer built-in Arabic-capable fonts shipped with mPDF (avoid custom cairo OTFs that break TT parsing).
        $font = 'xbriyaz';
        if (! isset($defaultFontConfig['fontdata'][$font])) {
            $font = 'dejavusans';
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
            // Keep language auto-detection off so mPDF does not try to load broken custom fonts (e.g. cairo).
            'autoScriptToLang' => false,
            'autoLangToFont' => false,
        ], $overrides);

        return new Mpdf($config);
    }
}
