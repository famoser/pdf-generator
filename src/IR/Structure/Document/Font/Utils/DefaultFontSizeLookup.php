<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Font\Utils;

class DefaultFontSizeLookup
{
    /**
     * @var int[][][]|null
     */
    private static ?array $sizeLookup = null;

    /**
     * @return string[][]
     *
     * @throws \JsonException
     */
    public static function getSize(string $font, string $style): array
    {
        if (null === self::$sizeLookup) {
            $json = file_get_contents(__DIR__.\DIRECTORY_SEPARATOR.'default_font_size.json');
            self::$sizeLookup = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        }

        return self::$sizeLookup[$font][$style];
    }
}