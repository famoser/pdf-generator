<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\FormatVisitorInterface;

/**
 * two-byte encoding format for continuous code ranges with spaces in between.
 */
class Format0 extends Format
{
    /**
     * entries referenced to for continuous code ranges where the idRangeOffset is not 0.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private array $glyphIndexArray = [];

    /**
     * the format of the encoding.
     *
     * @ttf-type uint16
     */
    public function getFormat(): int
    {
        return self::FORMAT_0;
    }

    /**
     * @return int[]
     */
    public function getGlyphIndexArray(): array
    {
        return $this->glyphIndexArray;
    }

    /**
     * @param int[] $glyphIndexArray
     */
    public function setGlyphIndexArray(array $glyphIndexArray): void
    {
        $this->glyphIndexArray = $glyphIndexArray;
    }

    public function accept(FormatVisitorInterface $formatVisitor)
    {
        return $formatVisitor->visitFormat0($this);
    }
}
