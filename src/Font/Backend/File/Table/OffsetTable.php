<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\TableVisitor;
use PdfGenerator\Font\Backend\File\Traits\BinaryTreeSearchableTrait;

/**
 * the offset table (also called sfnt) defines how many tables the font consists of.
 *
 * it also contains the dimensions a binary search tree that could be constructed from the amount of tables contained in the font
 * it assumes the binary search tree will have a value at each node; hence not all tables will fit in most cases (except there is an x such that numTables == 2**x)
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/otff
 */
class OffsetTable extends BaseTable
{
    /*
     * for numberOfEntries = numTables
     */
    use BinaryTreeSearchableTrait;
    /**
     * identifies what kind of font this is
     * 0x74727565 or 0x00010000 for TrueType fonts
     * 0x74797031 for PostScript font
     * 0x4F54544F for OpenType font with PostScript outlines (CFF instead of glyph table).
     *
     * @ttf-type uint32
     */
    private int $scalerType;

    /**
     * number of tables contained.
     *
     * @ttf-type uint16
     */
    private int $numTables;

    /**
     * of which size the binary tree is constructed.
     */
    protected function getNumberOfEntries(): int
    {
        return $this->numTables;
    }

    public function isTrueTypeFont(): bool
    {
        return 0x74727565 === $this->scalerType || 0x00010000 === $this->scalerType;
    }

    public function getScalerType(): int
    {
        return $this->scalerType;
    }

    public function setScalerType(int $scalerType): void
    {
        $this->scalerType = $scalerType;
    }

    public function getNumTables(): int
    {
        return $this->numTables;
    }

    public function setNumTables(int $numTables): void
    {
        $this->numTables = $numTables;
    }

    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitOffsetTable($this);
    }
}
