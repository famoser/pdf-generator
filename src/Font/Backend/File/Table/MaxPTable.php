<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Backend\File\Table;

use Famoser\PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use Famoser\PdfGenerator\Font\Backend\File\TableVisitor;

/**
 * the maximum profile table contains performance metrics of the font.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6maxp.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/maxp
 */
class MaxPTable extends BaseTable
{
    /**
     * version of the table.
     *
     * @ttf-type fixed
     */
    private float $version;

    /**
     * number of glyphs provided by this font.
     *
     * @ttf-type uint16
     */
    private int $numGlyphs;

    /**
     * max points in non-composite glyphs.
     *
     * @ttf-type uint16
     */
    private int $maxPoints;

    /**
     * max contours in non-composite glyph.
     *
     * @ttf-type uint16
     */
    private int $maxContours;

    /**
     * max points in composite glyphs.
     *
     * @ttf-type uint16
     */
    private int $maxCompositePoints;

    /**
     * max contours in composite glyph.
     *
     * @ttf-type uint16
     */
    private int $maxCompositeContours;

    /**
     * max zones used by instructions
     * 1 if no twilight zone is used
     * 2 if twilight zone is used.
     *
     * @ttf-type uint16
     */
    private int $maxZones;

    /**
     * max points used in the twilight zone.
     *
     * @ttf-type uint16
     */
    private int $maxTwilightPoints;

    /**
     * number of storage area locations.
     *
     * @ttf-type uint16
     */
    private int $maxStorage;

    /**
     * count of defined functions.
     *
     * @ttf-type uint16
     */
    private int $maxFunctionDefs;

    /**
     * count of defined instructions.
     *
     * @ttf-type uint16
     */
    private int $maxInstructionDefs;

    /**
     * max stack depth over font/cvt/glyph programs.
     *
     * @ttf-type uint16
     */
    private int $maxStackElements;

    /**
     * max byte count over all glyph instructions.
     *
     * @ttf-type uint16
     */
    private int $maxSizeOfInstructions;

    /**
     * max top-level referenced components by composite glyphs.
     *
     * @ttf-type uint16
     */
    private int $maxComponentElements;

    /**
     * max level of recursion
     * 1 for simple components.
     *
     * @ttf-type uint16
     */
    private int $maxComponentDepth;

    public function getVersion(): float
    {
        return $this->version;
    }

    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

    public function getNumGlyphs(): int
    {
        return $this->numGlyphs;
    }

    public function setNumGlyphs(int $numGlyphs): void
    {
        $this->numGlyphs = $numGlyphs;
    }

    public function getMaxPoints(): int
    {
        return $this->maxPoints;
    }

    public function setMaxPoints(int $maxPoints): void
    {
        $this->maxPoints = $maxPoints;
    }

    public function getMaxContours(): int
    {
        return $this->maxContours;
    }

    public function setMaxContours(int $maxContours): void
    {
        $this->maxContours = $maxContours;
    }

    public function getMaxCompositePoints(): int
    {
        return $this->maxCompositePoints;
    }

    public function setMaxCompositePoints(int $maxCompositePoints): void
    {
        $this->maxCompositePoints = $maxCompositePoints;
    }

    public function getMaxCompositeContours(): int
    {
        return $this->maxCompositeContours;
    }

    public function setMaxCompositeContours(int $maxCompositeContours): void
    {
        $this->maxCompositeContours = $maxCompositeContours;
    }

    public function getMaxZones(): int
    {
        return $this->maxZones;
    }

    public function setMaxZones(int $maxZones): void
    {
        $this->maxZones = $maxZones;
    }

    public function getMaxTwilightPoints(): int
    {
        return $this->maxTwilightPoints;
    }

    public function setMaxTwilightPoints(int $maxTwilightPoints): void
    {
        $this->maxTwilightPoints = $maxTwilightPoints;
    }

    public function getMaxStorage(): int
    {
        return $this->maxStorage;
    }

    public function setMaxStorage(int $maxStorage): void
    {
        $this->maxStorage = $maxStorage;
    }

    public function getMaxFunctionDefs(): int
    {
        return $this->maxFunctionDefs;
    }

    public function setMaxFunctionDefs(int $maxFunctionDefs): void
    {
        $this->maxFunctionDefs = $maxFunctionDefs;
    }

    public function getMaxInstructionDefs(): int
    {
        return $this->maxInstructionDefs;
    }

    public function setMaxInstructionDefs(int $maxInstructionDefs): void
    {
        $this->maxInstructionDefs = $maxInstructionDefs;
    }

    public function getMaxStackElements(): int
    {
        return $this->maxStackElements;
    }

    public function setMaxStackElements(int $maxStackElements): void
    {
        $this->maxStackElements = $maxStackElements;
    }

    public function getMaxSizeOfInstructions(): int
    {
        return $this->maxSizeOfInstructions;
    }

    public function setMaxSizeOfInstructions(int $maxSizeOfInstructions): void
    {
        $this->maxSizeOfInstructions = $maxSizeOfInstructions;
    }

    public function getMaxComponentElements(): int
    {
        return $this->maxComponentElements;
    }

    public function setMaxComponentElements(int $maxComponentElements): void
    {
        $this->maxComponentElements = $maxComponentElements;
    }

    public function getMaxComponentDepth(): int
    {
        return $this->maxComponentDepth;
    }

    public function setMaxComponentDepth(int $maxComponentDepth): void
    {
        $this->maxComponentDepth = $maxComponentDepth;
    }

    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitMaxPTable($this);
    }
}
