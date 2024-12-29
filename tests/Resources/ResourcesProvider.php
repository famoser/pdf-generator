<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Resources;

class ResourcesProvider
{
    public static function getImage1Path(): string
    {
        /** @phpstan-ignore-next-line  */
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'images'.\DIRECTORY_SEPARATOR.'image1.jpg');
    }

    public static function getFontOpenSansPath(): string
    {
        /** @phpstan-ignore-next-line  */
        return realpath(__DIR__.\DIRECTORY_SEPARATOR.'fonts'.\DIRECTORY_SEPARATOR.'OpenSans.ttf');
    }
}
