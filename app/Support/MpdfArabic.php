<?php

namespace App\Support;

use Mpdf\Config\ConfigVariables;
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
            'default_font' => 'xbriyaz',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ], $overrides);

        return new Mpdf($config);
    }
}
