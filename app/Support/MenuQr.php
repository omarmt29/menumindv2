<?php

namespace App\Support;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class MenuQr
{
    public static function svg(string $contents): string
    {
        $options = new QROptions([
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
            'quietzoneSize' => 2,
            'addQuietzone' => true,
            'drawLightModules' => true,
            'svgUseFillAttributes' => true,
        ]);

        return (new QRCode($options))->render($contents);
    }
}
