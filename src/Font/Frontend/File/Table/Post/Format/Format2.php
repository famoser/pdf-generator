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

/**
 * used to specific glyphs within or without the standard macintosh character set
 * the one format which should be used.
 */
class Format2
{
    /**
     * number of glyphs.
     * same number than the one in maxp profile.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numGlyphs;

    /**
     * maps glyph index to the character it represents
     * if 0-257 then uses the macintosh standard order
     * else subtract 258 and use as offset to the pascal strings.
     *
     * @ttf-type uint16[]
     *
     * @var int[]
     */
    private $glyphNameIndex;

    /**
     * glyph names with length byte.
     *
     * @ttf-type int8[]
     *
     * @var int[]
     */
    private $names;

    /**
     * @return int
     */
    public function getNumGlyphs(): int
    {
        return $this->numGlyphs;
    }

    /**
     * @param int $numGlyphs
     */
    public function setNumGlyphs(int $numGlyphs): void
    {
        $this->numGlyphs = $numGlyphs;
    }

    /**
     * @return int[]
     */
    public function getGlyphNameIndex(): array
    {
        return $this->glyphNameIndex;
    }

    /**
     * @param int[] $glyphNameIndex
     */
    public function setGlyphNameIndex(array $glyphNameIndex): void
    {
        $this->glyphNameIndex = $glyphNameIndex;
    }

    /**
     * @return int[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param int[] $names
     */
    public function setNames(array $names): void
    {
        $this->names = $names;
    }
}
