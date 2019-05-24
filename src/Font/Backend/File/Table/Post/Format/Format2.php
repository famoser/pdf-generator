<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\Post\Format;

use PdfGenerator\Font\Backend\File\Table\Post\FormatVisitor;
use PdfGenerator\Font\Backend\StreamWriter;

/**
 * used to specific glyphs within or without the standard macintosh character set
 * the one format which should be used.
 */
class Format2 extends Format
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
    private $glyphNameIndex = [];

    /**
     * glyph names with length byte.
     *
     * @ttf-type pascal string stream
     *
     * @var string
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
     * @param int $glyphNameIndex
     */
    public function addGlyphNameIndex(int $glyphNameIndex): void
    {
        $this->glyphNameIndex[] = $glyphNameIndex;
    }

    /**
     * @return string
     */
    public function getNames(): string
    {
        return $this->names;
    }

    /**
     * @param string $names
     */
    public function setNames(string $names): void
    {
        $this->names = $names;
    }

    /**
     * @param FormatVisitor $formatVisitor
     * @param StreamWriter $streamWriter
     */
    public function accept(FormatVisitor $formatVisitor, StreamWriter $streamWriter): void
    {
        $formatVisitor->visitFormat2($this, $streamWriter);
    }
}
