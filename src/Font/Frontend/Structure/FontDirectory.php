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

/**
 * the font directly is at the start of the TTF file and defines where which tables are located.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/otff
 */
class FontDirectory
{
    /**
     * defines how many tables are contained in the font wrapper and what kind of wrapper it is.
     *
     * @var OffsetTable
     */
    private $offsetTable;

    /**
     * the table directory defines the type & location of the tables.
     *
     * @var TableDirectoryEntry[]
     */
    private $tableDirectoryEntries = [];

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
}
