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

/**
 * the horizontal header table defines how a horizontal font has to be rendered
 * for special characters, the htmx table may override settings.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hhea.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/hhea
 *
 * sets properties like how much angle the character is displayed with (for italic) and baseline properties
 */
class HHeaTable extends BaseTable
{
    /**
     * number of tables contained.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $version;

    /**
     * distance from baseline to highest ascender
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $ascent;

    /**
     * distance from baseline to highest ascender
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $decent;

    /**
     * line gap (additional spacing to form the line height after summation of ascent + descend)
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $lineGap;

    /**
     * max advance width
     * computed values over all glyphs.
     *
     * @ttf-type ufword
     *
     * @var int
     */
    private $advanceWidthMax;

    /**
     * min left side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $minLeftSideBearing;

    /**
     * min right side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $minRightSideBearing;

    /**
     * max extent
     * calculated: max of all glyphs of (leftSideBearing + (xMax-xMin)).
     *
     * @ttf-type fword
     *
     * @var int
     */
    private $xMaxExtent;

    /**
     * used to calculate the angle of the characters
     * angle: caretSlopeRise / caretSlopeRun.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $caretSlopeRise;

    /**
     * used to calculate the angle of the characters
     * angle: caretSlopeRise / caretSlopeRun.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $caretSlopeRun;

    /**
     * caret offset
     * by which amount the highlight of the slanted character should be shifted.
     *
     * @ttf-type fword
     *
     * @var float
     */
    private $caretOffset;

    /**
     * metric data format
     * 0 for current format.
     *
     * @ttf-type int16
     *
     * @var int
     */
    private $metricDataFormat;

    /**
     * number of advance widths in metrics table.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numOfLongHorMetrics;

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getAscent(): int
    {
        return $this->ascent;
    }

    /**
     * @param int $ascent
     */
    public function setAscent(int $ascent): void
    {
        $this->ascent = $ascent;
    }

    /**
     * @return int
     */
    public function getDecent(): int
    {
        return $this->decent;
    }

    /**
     * @param int $decent
     */
    public function setDecent(int $decent): void
    {
        $this->decent = $decent;
    }

    /**
     * @return int
     */
    public function getLineGap(): int
    {
        return $this->lineGap;
    }

    /**
     * @param int $lineGap
     */
    public function setLineGap(int $lineGap): void
    {
        $this->lineGap = $lineGap;
    }

    /**
     * @return int
     */
    public function getAdvanceWidthMax(): int
    {
        return $this->advanceWidthMax;
    }

    /**
     * @param int $advanceWidthMax
     */
    public function setAdvanceWidthMax(int $advanceWidthMax): void
    {
        $this->advanceWidthMax = $advanceWidthMax;
    }

    /**
     * @return int
     */
    public function getMinLeftSideBearing(): int
    {
        return $this->minLeftSideBearing;
    }

    /**
     * @param int $minLeftSideBearing
     */
    public function setMinLeftSideBearing(int $minLeftSideBearing): void
    {
        $this->minLeftSideBearing = $minLeftSideBearing;
    }

    /**
     * @return int
     */
    public function getMinRightSideBearing(): int
    {
        return $this->minRightSideBearing;
    }

    /**
     * @param int $minRightSideBearing
     */
    public function setMinRightSideBearing(int $minRightSideBearing): void
    {
        $this->minRightSideBearing = $minRightSideBearing;
    }

    /**
     * @return int
     */
    public function getXMaxExtent(): int
    {
        return $this->xMaxExtent;
    }

    /**
     * @param int $xMaxExtent
     */
    public function setXMaxExtent(int $xMaxExtent): void
    {
        $this->xMaxExtent = $xMaxExtent;
    }

    /**
     * @return int
     */
    public function getCaretSlopeRise(): int
    {
        return $this->caretSlopeRise;
    }

    /**
     * @param int $caretSlopeRise
     */
    public function setCaretSlopeRise(int $caretSlopeRise): void
    {
        $this->caretSlopeRise = $caretSlopeRise;
    }

    /**
     * @return int
     */
    public function getCaretSlopeRun(): int
    {
        return $this->caretSlopeRun;
    }

    /**
     * @param int $caretSlopeRun
     */
    public function setCaretSlopeRun(int $caretSlopeRun): void
    {
        $this->caretSlopeRun = $caretSlopeRun;
    }

    /**
     * @return float
     */
    public function getCaretOffset(): float
    {
        return $this->caretOffset;
    }

    /**
     * @param float $caretOffset
     */
    public function setCaretOffset(float $caretOffset): void
    {
        $this->caretOffset = $caretOffset;
    }

    /**
     * @return int
     */
    public function getMetricDataFormat(): int
    {
        return $this->metricDataFormat;
    }

    /**
     * @param int $metricDataFormat
     */
    public function setMetricDataFormat(int $metricDataFormat): void
    {
        $this->metricDataFormat = $metricDataFormat;
    }

    /**
     * @return int
     */
    public function getNumOfLongHorMetrics(): int
    {
        return $this->numOfLongHorMetrics;
    }

    /**
     * @param int $numOfLongHorMetrics
     */
    public function setNumOfLongHorMetrics(int $numOfLongHorMetrics): void
    {
        $this->numOfLongHorMetrics = $numOfLongHorMetrics;
    }

    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitHHeaTable($this);
    }
}
