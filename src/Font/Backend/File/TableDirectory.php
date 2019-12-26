<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File;

use PdfGenerator\Font\Backend\File\Table\CMapTable;
use PdfGenerator\Font\Backend\File\Table\GlyfTable;
use PdfGenerator\Font\Backend\File\Table\HeadTable;
use PdfGenerator\Font\Backend\File\Table\HHeaTable;
use PdfGenerator\Font\Backend\File\Table\HMtxTable;
use PdfGenerator\Font\Backend\File\Table\LocaTable;
use PdfGenerator\Font\Backend\File\Table\MaxPTable;
use PdfGenerator\Font\Backend\File\Table\NameTable;
use PdfGenerator\Font\Backend\File\Table\OS2Table;
use PdfGenerator\Font\Backend\File\Table\PostTable;
use PdfGenerator\Font\Backend\File\Table\RawTable;

class TableDirectory
{
    /**
     * @var CMapTable
     */
    private $cMapTable;

    /**
     * @var GlyfTable[]
     */
    private $glyphTables = [];

    /**
     * @var HeadTable
     */
    private $headTable;

    /**
     * @var HHeaTable
     */
    private $hHeaTable;

    /**
     * @var HMtxTable
     */
    private $hMtxTable;

    /**
     * @var LocaTable
     */
    private $locaTable;

    /**
     * @var MaxPTable
     */
    private $maxPTable;

    /**
     * @var NameTable
     */
    private $nameTable;

    /**
     * @var OS2Table
     */
    private $os2Table;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var RawTable[]
     */
    private $rawTables = [];

    public function getCMapTable(): CMapTable
    {
        return $this->cMapTable;
    }

    public function setCMapTable(CMapTable $cMapTable): void
    {
        $this->cMapTable = $cMapTable;
    }

    /**
     * @return GlyfTable[]
     */
    public function getGlyphTables(): array
    {
        return $this->glyphTables;
    }

    /**
     * @param GlyfTable[] $glyphTables
     */
    public function setGlyphTables(array $glyphTables): void
    {
        $this->glyphTables = $glyphTables;
    }

    public function getHeadTable(): HeadTable
    {
        return $this->headTable;
    }

    public function setHeadTable(HeadTable $headTable): void
    {
        $this->headTable = $headTable;
    }

    public function getHHeaTable(): HHeaTable
    {
        return $this->hHeaTable;
    }

    public function setHHeaTable(HHeaTable $hHeaTable): void
    {
        $this->hHeaTable = $hHeaTable;
    }

    public function getHMtxTable(): HMtxTable
    {
        return $this->hMtxTable;
    }

    public function setHMtxTable(HMtxTable $hMtxTable): void
    {
        $this->hMtxTable = $hMtxTable;
    }

    public function getLocaTable(): LocaTable
    {
        return $this->locaTable;
    }

    public function setLocaTable(LocaTable $locaTable): void
    {
        $this->locaTable = $locaTable;
    }

    public function getMaxPTable(): MaxPTable
    {
        return $this->maxPTable;
    }

    public function setMaxPTable(MaxPTable $maxPTable): void
    {
        $this->maxPTable = $maxPTable;
    }

    public function getPostTable(): PostTable
    {
        return $this->postTable;
    }

    public function setPostTable(PostTable $postTable): void
    {
        $this->postTable = $postTable;
    }

    /**
     * @return RawTable[]
     */
    public function getRawTables(): array
    {
        return $this->rawTables;
    }

    /**
     * @param RawTable[] $rawTables
     */
    public function setRawTables(array $rawTables)
    {
        $this->rawTables = $rawTables;
    }

    public function getOs2Table(): OS2Table
    {
        return $this->os2Table;
    }

    public function setOs2Table(OS2Table $os2Table): void
    {
        $this->os2Table = $os2Table;
    }

    public function getNameTable(): NameTable
    {
        return $this->nameTable;
    }

    public function setNameTable(NameTable $nameTable): void
    {
        $this->nameTable = $nameTable;
    }
}
