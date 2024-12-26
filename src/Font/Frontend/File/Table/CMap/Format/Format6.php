<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\FormatVisitorInterface;

/**
 * two-byte encoding format for a single dense character range.
 */
class Format6 extends Format
{
    /**
     * first character code of subrange.
     *
     * @ttf-type uint16
     */
    private int $firstCode;

    /**
     * number of character codes in subrange.
     *
     * @ttf-type uint16
     */
    private int $entryCount;

    /**
     * glyph index value per character code in subrange.
     *
     * @ttf-type uint16[entryCount]
     *
     * @var int[]
     */
    private array $glyphIndexArray;

    /**
     * the format of the encoding.
     *
     * @ttf-type uint16
     */
    public function getFormat(): int
    {
        return self::FORMAT_6;
    }

    public function getFirstCode(): int
    {
        return $this->firstCode;
    }

    public function setFirstCode(int $firstCode): void
    {
        $this->firstCode = $firstCode;
    }

    public function getEntryCount(): int
    {
        return $this->entryCount;
    }

    public function setEntryCount(int $entryCount): void
    {
        $this->entryCount = $entryCount;
    }

    /**
     * @return int[]
     */
    public function getGlyphIndexArray(): array
    {
        return $this->glyphIndexArray;
    }

    /**
     * @param int[] $glyphIndexArray
     */
    public function setGlyphIndexArray(array $glyphIndexArray): void
    {
        $this->glyphIndexArray = $glyphIndexArray;
    }

    public function accept(FormatVisitorInterface $formatVisitor)
    {
        return $formatVisitor->visitFormat6($this);
    }
}
