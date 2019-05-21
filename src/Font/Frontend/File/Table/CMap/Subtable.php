<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\CMap;

use PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format;

/**
 * contains character to glyph mapping; is part of the 'cmap' table.
 */
class Subtable
{
    /**
     * encoding identifier
     * 0 for Unicode
     * 1 for Macintosh
     * 3 for Microsoft.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $platformID;

    /**
     * encoding identifier supplement.
     *
     * if platformID == 0, the following applies:
     * 0 for Default Semantics
     * 1 for Version 1.1 semantics
     * 2 for ISO 10646 1993 semantics (deprecated)
     * 3 for Unicode 2.0 or later semantics (BMP only)
     * 4 for Unicode 2.0 or later semantics (non-BMP characters allowed)
     * 5 for Unicode Variation Sequences
     * 6 for Full Unicode coverage (used with type 13.0 cmaps by OpenType)
     *
     * if plaformID == 3, the following applies:
     * 0 for Symbol
     * 1 for Unicode BMP-only (UCS-2)
     * 2 for Shift-JIS
     * 3 for PRC
     * 4 for BigFive
     * 5 for Johab
     * 10 for Unicode UCS-4
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $platformSpecificID;

    /**
     * the location of the encoding table.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $offset;

    /**
     * the encoding table.
     *
     * @var Format
     */
    private $format;

    /**
     * @return int
     */
    public function getPlatformID(): int
    {
        return $this->platformID;
    }

    /**
     * @param int $platformID
     */
    public function setPlatformID(int $platformID): void
    {
        $this->platformID = $platformID;
    }

    /**
     * @return int
     */
    public function getPlatformSpecificID(): int
    {
        return $this->platformSpecificID;
    }

    /**
     * @param int $platformSpecificID
     */
    public function setPlatformSpecificID(int $platformSpecificID): void
    {
        $this->platformSpecificID = $platformSpecificID;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return Format|null
     */
    public function getFormat(): ?Format
    {
        return $this->format;
    }

    /**
     * @param Format|null $format
     */
    public function setFormat(?Format $format): void
    {
        $this->format = $format;
    }
}
