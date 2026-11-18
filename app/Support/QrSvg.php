<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrSvg
{
    public static function render(string $text, int $size = 140): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($text);
        $svg = preg_replace('/^<\?xml[^>]*>\s*/', '', $svg) ?? $svg;

        return str_replace('<svg ', '<svg class="h-auto w-full" ', $svg);
    }
}
