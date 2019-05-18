<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure;

/**
 * the offset table (also called sfnt) defines how many tables the font consists of.
 *
 * it also contain the dimensions a binary search tree that could be constructed from the amount of tables contained in the font
 * it assumes the binary search tree will have a value at each node; hence not all tables will fit in most cases (except there is a x such that numTables == 2**x)
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/otff
 */
class OffsetTable
{
    /**
     * identifies what kind of font this is
     * 0x74727565 or 0x00010000 for TrueType fonts
     * 0x74797031 for PostScript font
     * 0x4F54544F for OpenType font with PostScript outlines (CFF instead of gylph table).
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $scalerType;

    /**
     * number of tables contained.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numTables;

    /**
     * how many tables can be indexed by a binary search tree
     * calculated: (maximum power of 2 <= numTables)*16.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $searchRange;

    /**
     * how deep the binary search tree will be
     * calculated: log2(maximum power of 2 <= numTables).
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $entrySelector;

    /**
     * how many tables are missed if only binary search tree is looked at
     * calculated: numTables*16-searchRange.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $rangeShift;

    /**
     * @return bool
     */
    public function isTrueTypeFont(): bool
    {
        return $this->scalerType === 0x74727565 || $this->scalerType === 0x00010000;
    }

    /**
     * @return int
     */
    public function getScalerType(): int
    {
        return $this->scalerType;
    }

    /**
     * @param int $scalerType
     */
    public function setScalerType(int $scalerType): void
    {
        $this->scalerType = $scalerType;
    }

    /**
     * @return int
     */
    public function getNumTables(): int
    {
        return $this->numTables;
    }

    /**
     * @param int $numTables
     */
    public function setNumTables(int $numTables): void
    {
        $this->numTables = $numTables;
    }

    /**
     * @return int
     */
    public function getSearchRange(): int
    {
        return $this->searchRange;
    }

    /**
     * @param int $searchRange
     */
    public function setSearchRange(int $searchRange): void
    {
        $this->searchRange = $searchRange;
    }

    /**
     * @return int
     */
    public function getEntrySelector(): int
    {
        return $this->entrySelector;
    }

    /**
     * @param int $entrySelector
     */
    public function setEntrySelector(int $entrySelector): void
    {
        $this->entrySelector = $entrySelector;
    }

    /**
     * @return int
     */
    public function getRangeShift(): int
    {
        return $this->rangeShift;
    }

    /**
     * @param int $rangeShift
     */
    public function setRangeShift(int $rangeShift): void
    {
        $this->rangeShift = $rangeShift;
    }
}
