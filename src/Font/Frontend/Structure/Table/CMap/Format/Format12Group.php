<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure\Table\CMap\Format;

class Format12Group
{
    /**
     * first character code in group.
     *
     * @ttf-type uint32
     *
     * @return int
     */
    private $startCharCode;

    /**
     * last character code in group.
     *
     * @ttf-type uint32
     *
     * @return int
     */
    private $endCharCode;

    /**
     * the index of the first character in the group
     * subsequent characters use a respectively incremented index.
     *
     * @ttf-type uint32
     *
     * @return int
     */
    private $startGlyphCode;

    /**
     * @return mixed
     */
    public function getStartCharCode()
    {
        return $this->startCharCode;
    }

    /**
     * @param mixed $startCharCode
     */
    public function setStartCharCode($startCharCode): void
    {
        $this->startCharCode = $startCharCode;
    }

    /**
     * @return mixed
     */
    public function getEndCharCode()
    {
        return $this->endCharCode;
    }

    /**
     * @param mixed $endCharCode
     */
    public function setEndCharCode($endCharCode): void
    {
        $this->endCharCode = $endCharCode;
    }

    /**
     * @return mixed
     */
    public function getStartGlyphCode()
    {
        return $this->startGlyphCode;
    }

    /**
     * @param mixed $startGlyphCode
     */
    public function setStartGlyphCode($startGlyphCode): void
    {
        $this->startGlyphCode = $startGlyphCode;
    }
}
