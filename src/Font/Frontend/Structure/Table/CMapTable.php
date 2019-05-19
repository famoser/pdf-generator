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

use PdfGenerator\Font\Frontend\Structure\Table\CMap\Subtable;

/**
 * the character map table maps character codes to glyph indices.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6cmap.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/cmap
 * @see https://github.com/opentypejs/opentype.js/blob/master/src/tables/cmap.js#L84
 *
 * contains of multiple subtables, each defines a different encoding
 * when reading, support as many formats as possible; at least Windows format 4 and 12.
 * when writing, only need unicode platform (0) and unicode 2.0 encoding (4)
 */
class CMapTable
{
    /**
     * simply ignore or set to 0.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $version;

    /**
     * the number of provided encoding subtables.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $numberSubtables;

    /**
     * the encoding subtables.
     *
     * @var Subtable[]
     */
    private $subtables;

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
     * @return int
     */
    public function getNumberSubtables(): int
    {
        return $this->numberSubtables;
    }

    /**
     * @param int $numberSubtables
     */
    public function setNumberSubtables(int $numberSubtables): void
    {
        $this->numberSubtables = $numberSubtables;
    }

    /**
     * @param Subtable $subtable
     */
    public function addSubtable(Subtable $subtable)
    {
        $this->subtables[] = $subtable;
    }

    /**
     * @return Subtable[]
     */
    public function getSubtables(): array
    {
        return $this->subtables;
    }
}
