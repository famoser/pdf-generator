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
     * @var float
     */
    private $ascent;

    /**
     * distance from baseline to highest ascender
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     *
     * @var float
     */
    private $decent;

    /**
     * line gap (additional spacing to form the line height after summation of ascent + descend)
     * designers intention only; not calculated.
     *
     * @ttf-type fword
     *
     * @var float
     */
    private $lineGap;

    /**
     * max advance width
     * computed values over all glyphs.
     *
     * @ttf-type ufword
     *
     * @var float
     */
    private $advanceWidthMax;

    /**
     * min left side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     *
     * @var float
     */
    private $minLeftSideBearing;

    /**
     * min right side bearing
     * computed values over all glyphs.
     *
     * @ttf-type fword
     *
     * @var float
     */
    private $minRightSideBearing;

    /**
     * max extent
     * calculated: max of all glyphs of (leftSideBearing + (xMax-xMin)).
     *
     * @ttf-type fword
     *
     * @var float
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
     * @return float
     */
    public function getAscent(): float
    {
        return $this->ascent;
    }

    /**
     * @param float $ascent
     */
    public function setAscent(float $ascent): void
    {
        $this->ascent = $ascent;
    }

    /**
     * @return float
     */
    public function getDecent(): float
    {
        return $this->decent;
    }

    /**
     * @param float $decent
     */
    public function setDecent(float $decent): void
    {
        $this->decent = $decent;
    }

    /**
     * @return float
     */
    public function getLineGap(): float
    {
        return $this->lineGap;
    }

    /**
     * @param float $lineGap
     */
    public function setLineGap(float $lineGap): void
    {
        $this->lineGap = $lineGap;
    }

    /**
     * @return float
     */
    public function getAdvanceWidthMax(): float
    {
        return $this->advanceWidthMax;
    }

    /**
     * @param float $advanceWidthMax
     */
    public function setAdvanceWidthMax(float $advanceWidthMax): void
    {
        $this->advanceWidthMax = $advanceWidthMax;
    }

    /**
     * @return float
     */
    public function getMinLeftSideBearing(): float
    {
        return $this->minLeftSideBearing;
    }

    /**
     * @param float $minLeftSideBearing
     */
    public function setMinLeftSideBearing(float $minLeftSideBearing): void
    {
        $this->minLeftSideBearing = $minLeftSideBearing;
    }

    /**
     * @return float
     */
    public function getMinRightSideBearing(): float
    {
        return $this->minRightSideBearing;
    }

    /**
     * @param float $minRightSideBearing
     */
    public function setMinRightSideBearing(float $minRightSideBearing): void
    {
        $this->minRightSideBearing = $minRightSideBearing;
    }

    /**
     * @return float
     */
    public function getXMaxExtent(): float
    {
        return $this->xMaxExtent;
    }

    /**
     * @param float $xMaxExtent
     */
    public function setXMaxExtent(float $xMaxExtent): void
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
}
