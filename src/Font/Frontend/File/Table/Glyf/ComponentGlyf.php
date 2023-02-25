<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Glyf;

use PdfGenerator\Font\Frontend\File\Traits\NullableRawContent;

class ComponentGlyf
{
    use NullableRawContent;
    public const ARG_1_AND_2_ARE_WORDS = 0x1;
    public const ARGS_ARE_XY_VALUES = 0x2;
    public const ROUND_XY_TO_GRID = 0x4;
    public const WE_HAVE_A_SCALE = 0x8;
    public const MORE_COMPONENTS = 0x20;
    public const WE_HAVE_AN_X_AND_Y_SCALE = 0x40;
    public const WE_HAVE_A_TWO_BY_TWO = 0x80;
    public const WE_HAVE_INSTRUCTIONS = 0x100;
    public const USE_MY_METRICS = 0x200;
    public const OVERLAP_COMPOUND = 0x400;
    public const SCALED_COMPONENT_OFFSET = 0x800;
    public const UNSCALED_COMPONENT_OFFSET = 0x1000;

    /**
     * flags as seen above.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $flags;

    /**
     * glyph index of this component.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $glyphIndex;

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function getGlyphIndex(): int
    {
        return $this->glyphIndex;
    }

    public function setGlyphIndex(int $glyphIndex): void
    {
        $this->glyphIndex = $glyphIndex;
    }
}
