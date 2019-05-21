<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File;

use PdfGenerator\Font\Frontend\File\Table\CMapTable;
use PdfGenerator\Font\Frontend\File\Table\CvtTable;
use PdfGenerator\Font\Frontend\File\Table\FpgmTable;
use PdfGenerator\Font\Frontend\File\Table\GaspTable;
use PdfGenerator\Font\Frontend\File\Table\GDEFTable;
use PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use PdfGenerator\Font\Frontend\File\Table\GPOSTable;
use PdfGenerator\Font\Frontend\File\Table\GSUBTable;
use PdfGenerator\Font\Frontend\File\Table\HeadTable;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Table\LocaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\NameTable;
use PdfGenerator\Font\Frontend\File\Table\OffsetTable;
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
use PdfGenerator\Font\Frontend\File\Table\PostTable;
use PdfGenerator\Font\Frontend\File\Table\PrepTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;
use PdfGenerator\Font\Frontend\File\Table\TableDirectoryEntry;

class FontFile
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
     * @var CvtTable|null
     */
    private $cvtTable;

    /**
     * @var FpgmTable|null
     */
    private $fpqmTable;

    /**
     * @var GaspTable|null
     */
    private $gaspTable;

    /**
     * @var GDEFTable|null
     */
    private $gDEFTable;

    /**
     * @var GlyfTable[]
     */
    private $glyfTables = [];

    /**
     * @var GPOSTable|null
     */
    private $gPOSTable;

    /**
     * @var GSUBTable|null
     */
    private $gSUBTable;

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
     * @var PrepTable|null
     */
    private $prepTable;

    /**
     * @var RawTable[]
     */
    private $rawTables = [];

    /**
     * @var PostTable|null
     */
    private $postTable;

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
     * @return CvtTable|null
     */
    public function getCvtTable(): ?CvtTable
    {
        return $this->cvtTable;
    }

    /**
     * @param CvtTable|null $cvtTable
     */
    public function setCvtTable(?CvtTable $cvtTable): void
    {
        $this->cvtTable = $cvtTable;
    }

    /**
     * @return FpgmTable|null
     */
    public function getFpqmTable(): ?FpgmTable
    {
        return $this->fpqmTable;
    }

    /**
     * @param FpgmTable|null $fpqmTable
     */
    public function setFpqmTable(?FpgmTable $fpqmTable): void
    {
        $this->fpqmTable = $fpqmTable;
    }

    /**
     * @return GaspTable|null
     */
    public function getGaspTable(): ?GaspTable
    {
        return $this->gaspTable;
    }

    /**
     * @param GaspTable|null $gaspTable
     */
    public function setGaspTable(?GaspTable $gaspTable): void
    {
        $this->gaspTable = $gaspTable;
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

    /**
     * @return PrepTable|null
     */
    public function getPrepTable(): ?PrepTable
    {
        return $this->prepTable;
    }

    /**
     * @param PrepTable|null $prepTable
     */
    public function setPrepTable(?PrepTable $prepTable): void
    {
        $this->prepTable = $prepTable;
    }

    /**
     * @return RawTable[]
     */
    public function getRawTables(): array
    {
        return $this->rawTables;
    }

    /**
     * @param RawTable $rawTable
     */
    public function addRawTable(RawTable $rawTable): void
    {
        $this->rawTables[] = $rawTable;
    }

    /**
     * @return PostTable|null
     */
    public function getPostTable(): ?PostTable
    {
        return $this->postTable;
    }

    /**
     * @param PostTable|null $postTable
     */
    public function setPostTable(?PostTable $postTable): void
    {
        $this->postTable = $postTable;
    }

    /**
     * @return GDEFTable|null
     */
    public function getGDEFTable(): ?GDEFTable
    {
        return $this->gDEFTable;
    }

    /**
     * @param GDEFTable|null $gDEFTable
     */
    public function setGDEFTable(?GDEFTable $gDEFTable): void
    {
        $this->gDEFTable = $gDEFTable;
    }

    /**
     * @return GPOSTable|null
     */
    public function getGPOSTable(): ?GPOSTable
    {
        return $this->gPOSTable;
    }

    /**
     * @param GPOSTable|null $gPOSTable
     */
    public function setGPOSTable(?GPOSTable $gPOSTable): void
    {
        $this->gPOSTable = $gPOSTable;
    }

    /**
     * @return GSUBTable|null
     */
    public function getGSUBTable(): ?GSUBTable
    {
        return $this->gSUBTable;
    }

    /**
     * @param GSUBTable|null $gSUBTable
     */
    public function setGSUBTable(?GSUBTable $gSUBTable): void
    {
        $this->gSUBTable = $gSUBTable;
    }
}
