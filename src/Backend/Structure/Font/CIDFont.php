<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Font;

class CIDFont
{
    const SUBTYPE_CID_FONT_TYPE_2 = 'CIDFontType2';

    const CID_TO_GID_MAPPING = 'Identity';

    /**
     * @var string
     */
    private $subType = self::SUBTYPE_CID_FONT_TYPE_2;

    /**
     * determined by looking at the TTF 'name' table (9.6.3)
     * if subset, then prefix with 6 uppercase letters unique for that specific subset followed by a + sign.
     *
     * @var string
     */
    private $baseFont;

    /**
     * @var CIDSystemInfo
     */
    private $cIDSystemInfo;

    /**
     * @var FontDescriptor
     */
    private $fontDescriptor;

    /**
     * default width of a character.
     *
     * @var int
     */
    private $dW = 1000;

    /**
     * width per character.
     *
     * @var int[]
     */
    private $w = [];

    /**
     * @var string
     */
    private $cIDToGIDMap = self::CID_TO_GID_MAPPING;
}
