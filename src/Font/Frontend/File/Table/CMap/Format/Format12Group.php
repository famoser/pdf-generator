<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\CMap\Format;

class Format12Group
{
    /**
     * first character code in group.
     *
     * @ttf-type uint32
     */
    private int $startCharCode;

    /**
     * last character code in group.
     *
     * @ttf-type uint32
     */
    private int $endCharCode;

    /**
     * the index of the first character in the group
     * subsequent characters use a respectively incremented index.
     *
     * @ttf-type uint32
     */
    private int $startGlyphCode;

    public function getStartCharCode(): int
    {
        return $this->startCharCode;
    }

    public function setStartCharCode($startCharCode): void
    {
        $this->startCharCode = $startCharCode;
    }

    public function getEndCharCode(): int
    {
        return $this->endCharCode;
    }

    public function setEndCharCode($endCharCode): void
    {
        $this->endCharCode = $endCharCode;
    }

    public function getStartGlyphCode(): int
    {
        return $this->startGlyphCode;
    }

    public function setStartGlyphCode($startGlyphCode): void
    {
        $this->startGlyphCode = $startGlyphCode;
    }
}
