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

use PdfGenerator\Font\Frontend\File\Table\CMap\Subtable;
use PdfGenerator\Font\Frontend\File\Table\Interfaces\WritableTableInterface;
use PdfGenerator\Font\Frontend\File\Table\Interfaces\WritableTableVisitorInterface;

/**
 * the character map table maps character codes to glyph indices.
 * contains of multiple subtables, each defines a different encoding.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6cmap.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/cmap
 * @see https://github.com/opentypejs/opentype.js/blob/master/src/tables/cmap.js#L84
 *
 * limitations:
 *  format2 not implemented as primarily for asian fonts
 *  format8 not implemented because limited use, complicated, discouraged by apple/microsoft
 *  format10 not implemented because limited use, not supported by windows
 *  format13 not implemented because only used for last-resort fonts (used for debugging)
 *  format14 not implemented because complicated, primarily useful for variations (emojis)
 */
class CMapTable implements WritableTableInterface
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

    /**
     * @param WritableTableVisitorInterface $writableTableVisitor
     *
     * @return mixed
     */
    public function accept(WritableTableVisitorInterface $writableTableVisitor)
    {
        return $writableTableVisitor->visitCMap($this);
    }
}
