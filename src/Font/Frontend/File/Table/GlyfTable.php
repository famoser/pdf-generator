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

use PdfGenerator\Font\Frontend\File\Traits\BoundingBoxTrait;
use PdfGenerator\Font\Frontend\File\Traits\RawContent;

/**
 * the glyph table specified the appearance of the glyphs
 * needs numGlyphs from maxp table (to know how many glyphs are there)
 * needs offsets of glyphys from loca table (to know which glyph corresponds to which glyph id).
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6glyf.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/glyf
 *
 * each entry contains its bounding box and the actual glyph data
 */
class GlyfTable
{
    use BoundingBoxTrait;
    /*
     * the raw glyph data
     */
    use RawContent;
    const ARG_1_AND_2_ARE_WORDS = 0x1;
    const ARGS_ARE_XY_VALUES = 0x2;
    const ROUND_XY_TO_GRID = 0x4;
    const WE_HAVE_A_SCALE = 0x8;
    const MORE_COMPONENTS = 0x20;
    const WE_HAVE_AN_X_AND_Y_SCALE = 0x40;
    const WE_HAVE_A_TWO_BY_TWO = 0x80;
    const WE_HAVE_INSTRUCTIONS = 0x100;
    const USE_MY_METRICS = 0x200;
    const OVERLAP_COMPOUND = 0x400;
    const SCALED_COMPONENT_OFFSET = 0x800;
    const UNSCALED_COMPONENT_OFFSET = 0x1000;
    /**
     * number of contours
     * if >=0 then simple glyph
     * else composite graph.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numberOfContours;

    /**
     * other glyphs called as part of this glyph
     * if non-empty, must include these glyphs into final font.
     *
     * @var int[]
     */
    private $glyphIndex = [];

    public function getNumberOfContours(): int
    {
        return $this->numberOfContours;
    }

    public function setNumberOfContours(int $numberOfContours): void
    {
        $this->numberOfContours = $numberOfContours;
    }

    public function addGlyphIndex(int $glyphIndex)
    {
        $this->glyphIndex[] = $glyphIndex;
    }
}
