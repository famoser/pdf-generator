<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\Glyf;

use PdfGenerator\Font\Backend\File\Traits\NullableRawContent;

class ComponentGlyf
{
    use NullableRawContent;

    /**
     * flags as seen above.
     *
     * @ttf-type uint16
     */
    private int $flags;

    /**
     * glyph index of this component.
     *
     * @ttf-type uint16
     */
    private int $glyphIndex;

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
