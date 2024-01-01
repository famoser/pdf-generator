<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Post\Format;

use PdfGenerator\Font\Frontend\File\Table\Post\FormatVisitorInterface;

/**
 * specifies offsets to the standard macintosh ordering.
 */
class Format25 extends Format
{
    /**
     * number of glyphs.
     * same number than the one in maxp profile.
     *
     * @ttf-type uint16
     */
    private int $numGlyphs;

    /**
     * the offset of the respective glyph to the standard ordering.
     *
     * @ttf-type int8[]
     *
     * @var int[]
     */
    private array $offsets;

    public function getNumGlyphs(): int
    {
        return $this->numGlyphs;
    }

    public function setNumGlyphs(int $numGlyphs): void
    {
        $this->numGlyphs = $numGlyphs;
    }

    /**
     * @return int[]
     */
    public function getOffsets(): array
    {
        return $this->offsets;
    }

    /**
     * @param int[] $offsets
     */
    public function setOffsets(array $offsets): void
    {
        $this->offsets = $offsets;
    }

    public function accept(FormatVisitorInterface $visitor)
    {
        return $visitor->visitFormat25($this);
    }
}
