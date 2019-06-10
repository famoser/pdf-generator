<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Font;

class CharacterMapping
{
    /**
     * @var int
     */
    private $startByte;

    /**
     * @var int
     */
    private $endByte;

    /**
     * @var int
     */
    private $startGlyphIndex;

    /**
     * CharacterMapping constructor.
     *
     * @param int $startByte
     * @param int $endByte
     * @param int $startGlyphIndex
     */
    public function __construct(int $startByte, int $endByte, int $startGlyphIndex)
    {
        $this->startByte = $startByte;
        $this->endByte = $endByte;
        $this->startGlyphIndex = $startGlyphIndex;
    }

    /**
     * @return int
     */
    public function getStartByte(): int
    {
        return $this->startByte;
    }

    /**
     * @return int
     */
    public function getEndByte(): int
    {
        return $this->endByte;
    }

    /**
     * @return int
     */
    public function getStartGlyphIndex(): int
    {
        return $this->startGlyphIndex;
    }
}
