<?php

namespace Famoser\PdfGenerator\Tests\Resources;

class ResourcesProvider
{
    public static function getImage1Path(): string
    {
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'images'.\DIRECTORY_SEPARATOR.'image1.jpg');
    }

    public static function getFontOpenSansPath(): string
    {
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'fonts'.\DIRECTORY_SEPARATOR.'OpenSans.ttf');
    }
}
