<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table;

/**
 * the horizontal header table defines how a horizontal font has to be rendered
 * for special characters, the htmx table may override settings.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hhea.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/hhea
 *
 * sets properties like how much angle the character is displayed with (for italic) and baseline properties
 */
class HHeaTable
{
    /**
     * version of table.
     *
     * @ttf-type fixed
     */
    private float $version;

    /**
     * distance from baseline to highest ascender
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     */
    private int $ascent;

    /**
     * distance from baseline to highest ascender
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     */
    private int $descent;

    /**
     * line gap (additional spacing to form the line height after summation of ascent + descend)
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     */
    private int $lineGap;

    /**
     * max advance width
     * computed values over all glyphs.
     *
     * @ttf-type ufword
     */
    private int $advanceWidthMax;

    /**
     * min left side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     */
    private int $minLeftSideBearing;

    /**
     * min right side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     */
    private int $minRightSideBearing;

    /**
     * max extent
     * calculated: max of all glyphs of (leftSideBearing + (xMax-xMin)).
     *
     * @ttf-type fword
     */
    private int $xMaxExtent;

    /**
     * used to calculate the angle of the characters
     * angle: caretSlopeRise / caretSlopeRun.
     *
     * @ttf-type int16
     */
    private int $caretSlopeRise;

    /**
     * used to calculate the angle of the characters
     * angle: caretSlopeRise / caretSlopeRun.
     *
     * @ttf-type int16
     */
    private int $caretSlopeRun;

    /**
     * caret offset
     * by which amount the highlight of the slanted character should be shifted.
     *
     * @ttf-type int16
     */
    private int $caretOffset;

    /**
     * metric data format
     * 0 for current format.
     *
     * @ttf-type int16
     */
    private int $metricDataFormat;

    /**
     * number of advance widths in metrics table.
     *
     * @ttf-type uint16
     */
    private int $numOfLongHorMetrics;

    public function getVersion(): float
    {
        return $this->version;
    }

    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

    public function getAscent(): int
    {
        return $this->ascent;
    }

    public function setAscent(int $ascent): void
    {
        $this->ascent = $ascent;
    }

    public function getDescent(): int
    {
        return $this->descent;
    }

    public function setDescent(int $descent): void
    {
        $this->descent = $descent;
    }

    public function getLineGap(): int
    {
        return $this->lineGap;
    }

    public function setLineGap(int $lineGap): void
    {
        $this->lineGap = $lineGap;
    }

    public function getAdvanceWidthMax(): int
    {
        return $this->advanceWidthMax;
    }

    public function setAdvanceWidthMax(int $advanceWidthMax): void
    {
        $this->advanceWidthMax = $advanceWidthMax;
    }

    public function getMinLeftSideBearing(): int
    {
        return $this->minLeftSideBearing;
    }

    public function setMinLeftSideBearing(int $minLeftSideBearing): void
    {
        $this->minLeftSideBearing = $minLeftSideBearing;
    }

    public function getMinRightSideBearing(): int
    {
        return $this->minRightSideBearing;
    }

    public function setMinRightSideBearing(int $minRightSideBearing): void
    {
        $this->minRightSideBearing = $minRightSideBearing;
    }

    public function getXMaxExtent(): int
    {
        return $this->xMaxExtent;
    }

    public function setXMaxExtent(int $xMaxExtent): void
    {
        $this->xMaxExtent = $xMaxExtent;
    }

    public function getCaretSlopeRise(): int
    {
        return $this->caretSlopeRise;
    }

    public function setCaretSlopeRise(int $caretSlopeRise): void
    {
        $this->caretSlopeRise = $caretSlopeRise;
    }

    public function getCaretSlopeRun(): int
    {
        return $this->caretSlopeRun;
    }

    public function setCaretSlopeRun(int $caretSlopeRun): void
    {
        $this->caretSlopeRun = $caretSlopeRun;
    }

    public function getCaretOffset(): int
    {
        return $this->caretOffset;
    }

    public function setCaretOffset(int $caretOffset): void
    {
        $this->caretOffset = $caretOffset;
    }

    public function getMetricDataFormat(): int
    {
        return $this->metricDataFormat;
    }

    public function setMetricDataFormat(int $metricDataFormat): void
    {
        $this->metricDataFormat = $metricDataFormat;
    }

    public function getNumOfLongHorMetrics(): int
    {
        return $this->numOfLongHorMetrics;
    }

    public function setNumOfLongHorMetrics(int $numOfLongHorMetrics): void
    {
        $this->numOfLongHorMetrics = $numOfLongHorMetrics;
    }
}
