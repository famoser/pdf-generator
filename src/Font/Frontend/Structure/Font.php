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
use PdfGenerator\Font\Frontend\Structure\Table\GlyfTable;
use PdfGenerator\Font\Frontend\Structure\Table\HeadTable;
use PdfGenerator\Font\Frontend\Structure\Table\HHeaTable;
use PdfGenerator\Font\Frontend\Structure\Table\HMtxTable;
use PdfGenerator\Font\Frontend\Structure\Table\LocaTable;
use PdfGenerator\Font\Frontend\Structure\Table\MaxPTable;
use PdfGenerator\Font\Frontend\Structure\Table\NameTable;
use PdfGenerator\Font\Frontend\Structure\Table\OffsetTable;
use PdfGenerator\Font\Frontend\Structure\Table\OS2Table;
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
     * @var GlyfTable[]
     */
    private $glyfTables = [];

    /**
     * @var HeadTable|null
     */
    private $headTable;

    /**
     * @var HHeaTable|null
     */
    private $hHeaTable;

    /**
     * @var HMtxTable|null
     */
    private $hMtxTable;

    /**
     * @var LocaTable|null
     */
    private $locaTable;

    /**
     * @var MaxPTable|null
     */
    private $maxPTable;

    /**
     * @var NameTable|null
     */
    private $nameTable;

    /**
     * @var OS2Table|null
     */
    private $oS2Table;

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

    /**
     * @return GlyfTable[]
     */
    public function getGlyfTables(): array
    {
        return $this->glyfTables;
    }

    /**
     * @param GlyfTable[] $glyfTables
     */
    public function setGlyfTables(array $glyfTables): void
    {
        $this->glyfTables = $glyfTables;
    }

    /**
     * @return HeadTable|null
     */
    public function getHeadTable(): ?HeadTable
    {
        return $this->headTable;
    }

    /**
     * @param HeadTable|null $headTable
     */
    public function setHeadTable(?HeadTable $headTable): void
    {
        $this->headTable = $headTable;
    }

    /**
     * @return HHeaTable|null
     */
    public function getHHeaTable(): ?HHeaTable
    {
        return $this->hHeaTable;
    }

    /**
     * @param HHeaTable|null $hHeaTable
     */
    public function setHHeaTable(?HHeaTable $hHeaTable): void
    {
        $this->hHeaTable = $hHeaTable;
    }

    /**
     * @return HMtxTable|null
     */
    public function getHMtxTable(): ?HMtxTable
    {
        return $this->hMtxTable;
    }

    /**
     * @param HMtxTable|null $hMtxTable
     */
    public function setHMtxTable(?HMtxTable $hMtxTable): void
    {
        $this->hMtxTable = $hMtxTable;
    }

    /**
     * @return LocaTable|null
     */
    public function getLocaTable(): ?LocaTable
    {
        return $this->locaTable;
    }

    /**
     * @param LocaTable|null $locaTable
     */
    public function setLocaTable(?LocaTable $locaTable): void
    {
        $this->locaTable = $locaTable;
    }

    /**
     * @return MaxPTable|null
     */
    public function getMaxPTable(): ?MaxPTable
    {
        return $this->maxPTable;
    }

    /**
     * @param MaxPTable|null $maxPTable
     */
    public function setMaxPTable(?MaxPTable $maxPTable): void
    {
        $this->maxPTable = $maxPTable;
    }

    /**
     * @return NameTable|null
     */
    public function getNameTable(): ?NameTable
    {
        return $this->nameTable;
    }

    /**
     * @param NameTable|null $nameTable
     */
    public function setNameTable(?NameTable $nameTable): void
    {
        $this->nameTable = $nameTable;
    }

    /**
     * @return OS2Table|null
     */
    public function getOS2Table(): ?OS2Table
    {
        return $this->oS2Table;
    }

    /**
     * @param OS2Table|null $oS2Table
     */
    public function setOS2Table(?OS2Table $oS2Table): void
    {
        $this->oS2Table = $oS2Table;
    }
}
