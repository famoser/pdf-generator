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
use PdfGenerator\Font\Backend\File\Table\HMtx\LongHorMetric;
use PdfGenerator\Font\Backend\File\TableVisitor;

/**
 * the horizontal metrics table defines how the horizontal font has to be rendered
 * the numberOfHMetrics field of the htmx table defines how many entries this table has.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hmtx.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/hmtx
 *
 * each entry defines the width and the offset to the left
 * if not an entry for each glyph exists, the last entry is used for the remaining glyphs
 */
class HMtxTable extends BaseTable
{
    /**
     * simply ignore or set to 0.
     *
     * @ttf-type longHorMetric[hhea.numOfLongHorMetrics]
     *
     * @var LongHorMetric[]
     */
    private $longHorMetrics = [];

    /**
     * the left side bearing of the characters not specified using the longHorMetric.
     * these characters use an advanceWidth equal to the last entry in the array above.
     *
     * @ttf-type uint16
     *
     * @var int[]
     */
    private $leftSideBearings = [];

    /**
     * @return LongHorMetric[]
     */
    public function getLongHorMetrics(): array
    {
        return $this->longHorMetrics;
    }

    /**
     * @param LongHorMetric $longHorMetric
     */
    public function addLongHorMetric(LongHorMetric $longHorMetric)
    {
        $this->longHorMetrics[] = $longHorMetric;
    }

    /**
     * @return int[]
     */
    public function getLeftSideBearings(): array
    {
        return $this->leftSideBearings;
    }

    /**
     * @param int $leftSideBearing
     */
    public function addLeftSideBearing(int $leftSideBearing): void
    {
        $this->leftSideBearings[] = $leftSideBearing;
    }

    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitHMtxTable($this);
    }
}
