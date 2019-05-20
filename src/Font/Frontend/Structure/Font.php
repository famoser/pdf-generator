<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\Structure;

use PdfGenerator\Font\Frontend\Structure\Table\CMapTable;
use PdfGenerator\Font\Frontend\Structure\Table\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\TableDirectoryEntry;

class Font
{
    /**
     * @var OffsetTable
     */
    private $offsetTable;

    /**
     * @var TableDirectoryEntry[]
     */
    private $tableDirectoryEntries = [];

    /**
     * @var CMapTable|null
     */
    private $cMapTable;

    /**
     * @return OffsetTable
     */
    public function getOffsetTable(): OffsetTable
    {
        return $this->offsetTable;
    }

    /**
     * @param OffsetTable $offsetTable
     */
    public function setOffsetTable(OffsetTable $offsetTable): void
    {
        $this->offsetTable = $offsetTable;
    }

    /**
     * @return TableDirectoryEntry[]
     */
    public function getTableDirectoryEntries(): array
    {
        return $this->tableDirectoryEntries;
    }

    /**
     * @param TableDirectoryEntry $tableDirectoryEntry
     */
    public function addTableDirectoryEntry(TableDirectoryEntry $tableDirectoryEntry): void
    {
        $this->tableDirectoryEntries[] = $tableDirectoryEntry;
    }

    /**
     * @return CMapTable|null
     */
    public function getCMapTable(): ?CMapTable
    {
        return $this->cMapTable;
    }

    /**
     * @param CMapTable|null $cMapTable
     */
    public function setCMapTable(?CMapTable $cMapTable): void
    {
        $this->cMapTable = $cMapTable;
    }
}
