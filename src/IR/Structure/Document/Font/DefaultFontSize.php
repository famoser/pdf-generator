<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Font;

class DefaultFontSize
{
    /**
     * @var int[][][]
     */
    private static $sizeLookup = null;

    /**
     * @var string[][]
     */
    public static function getSize(string $font, string $style)
    {
        if (static::$sizeLookup === null) {
            $json = file_get_contents(__DIR__ . \DIRECTORY_SEPARATOR . 'default_font_size.json');
            static::$sizeLookup = json_decode($json, true);
        }

        return static::$sizeLookup[$font][$style];
    }
}
