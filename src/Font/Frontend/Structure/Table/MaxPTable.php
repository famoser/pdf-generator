<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table;

/**
 * the maximum profile table contains performance metrics of the font.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6maxp.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/maxp
 */
class MaxPTable
{
    /**
     * version of the table.
     *
     * @ttf-type fixed
     *
     * @var float
     */
    private $version;

    /**
     * number of glyphs provided by this font.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numGlyphs;

    /**
     * max points in non-composite glyphs.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxPoints;

    /**
     * max contours in non-composite glyph.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxContours;

    /**
     * max points in composite glyphs.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxCompositePoints;

    /**
     * max contours in composite glyph.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxCompositeContours;

    /**
     * max zones used by instructions
     * 1 if no twilight zone is used
     * 2 if twilight zone is used.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxZones;

    /**
     * max points used in the twilight zone.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxTwilightPoints;

    /**
     * number of storage area locations.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxStorage;

    /**
     * count of defined functions.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxFunctionDefs;

    /**
     * count of defined instructions.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxInstructionDefs;

    /**
     * max stack depth over font/cvt/glyph programs.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxStackElements;

    /**
     * max byte count over all glyph instructions.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxSizeOfInstructions;

    /**
     * max top-level referenced components by composite glyphs.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxComponentElements;

    /**
     * max level of recursion
     * 1 for simple components.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $maxComponentDepth;

    /**
     * @return float
     */
    public function getVersion(): float
    {
        return $this->version;
    }

    /**
     * @param float $version
     */
    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

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
     * @return int
     */
    public function getMaxPoints(): int
    {
        return $this->maxPoints;
    }

    /**
     * @param int $maxPoints
     */
    public function setMaxPoints(int $maxPoints): void
    {
        $this->maxPoints = $maxPoints;
    }

    /**
     * @return int
     */
    public function getMaxContours(): int
    {
        return $this->maxContours;
    }

    /**
     * @param int $maxContours
     */
    public function setMaxContours(int $maxContours): void
    {
        $this->maxContours = $maxContours;
    }

    /**
     * @return int
     */
    public function getMaxCompositePoints(): int
    {
        return $this->maxCompositePoints;
    }

    /**
     * @param int $maxCompositePoints
     */
    public function setMaxCompositePoints(int $maxCompositePoints): void
    {
        $this->maxCompositePoints = $maxCompositePoints;
    }

    /**
     * @return int
     */
    public function getMaxCompositeContours(): int
    {
        return $this->maxCompositeContours;
    }

    /**
     * @param int $maxCompositeContours
     */
    public function setMaxCompositeContours(int $maxCompositeContours): void
    {
        $this->maxCompositeContours = $maxCompositeContours;
    }

    /**
     * @return int
     */
    public function getMaxZones(): int
    {
        return $this->maxZones;
    }

    /**
     * @param int $maxZones
     */
    public function setMaxZones(int $maxZones): void
    {
        $this->maxZones = $maxZones;
    }

    /**
     * @return int
     */
    public function getMaxTwilightPoints(): int
    {
        return $this->maxTwilightPoints;
    }

    /**
     * @param int $maxTwilightPoints
     */
    public function setMaxTwilightPoints(int $maxTwilightPoints): void
    {
        $this->maxTwilightPoints = $maxTwilightPoints;
    }

    /**
     * @return int
     */
    public function getMaxStorage(): int
    {
        return $this->maxStorage;
    }

    /**
     * @param int $maxStorage
     */
    public function setMaxStorage(int $maxStorage): void
    {
        $this->maxStorage = $maxStorage;
    }

    /**
     * @return int
     */
    public function getMaxFunctionDefs(): int
    {
        return $this->maxFunctionDefs;
    }

    /**
     * @param int $maxFunctionDefs
     */
    public function setMaxFunctionDefs(int $maxFunctionDefs): void
    {
        $this->maxFunctionDefs = $maxFunctionDefs;
    }

    /**
     * @return int
     */
    public function getMaxInstructionDefs(): int
    {
        return $this->maxInstructionDefs;
    }

    /**
     * @param int $maxInstructionDefs
     */
    public function setMaxInstructionDefs(int $maxInstructionDefs): void
    {
        $this->maxInstructionDefs = $maxInstructionDefs;
    }

    /**
     * @return int
     */
    public function getMaxStackElements(): int
    {
        return $this->maxStackElements;
    }

    /**
     * @param int $maxStackElements
     */
    public function setMaxStackElements(int $maxStackElements): void
    {
        $this->maxStackElements = $maxStackElements;
    }

    /**
     * @return int
     */
    public function getMaxSizeOfInstructions(): int
    {
        return $this->maxSizeOfInstructions;
    }

    /**
     * @param int $maxSizeOfInstructions
     */
    public function setMaxSizeOfInstructions(int $maxSizeOfInstructions): void
    {
        $this->maxSizeOfInstructions = $maxSizeOfInstructions;
    }

    /**
     * @return int
     */
    public function getMaxComponentElements(): int
    {
        return $this->maxComponentElements;
    }

    /**
     * @param int $maxComponentElements
     */
    public function setMaxComponentElements(int $maxComponentElements): void
    {
        $this->maxComponentElements = $maxComponentElements;
    }

    /**
     * @return int
     */
    public function getMaxComponentDepth(): int
    {
        return $this->maxComponentDepth;
    }

    /**
     * @param int $maxComponentDepth
     */
    public function setMaxComponentDepth(int $maxComponentDepth): void
    {
        $this->maxComponentDepth = $maxComponentDepth;
    }
}
