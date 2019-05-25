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
     * @var PostTable
     */
    private $postTable;

    /**
     * @var RawTable[]
     */
    private $rawTables = [];

    /**
     * @return CMapTable
     */
    public function getCMapTable(): CMapTable
    {
        return $this->cMapTable;
    }

    /**
     * @param CMapTable $cMapTable
     */
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

    /**
     * @return HeadTable
     */
    public function getHeadTable(): HeadTable
    {
        return $this->headTable;
    }

    /**
     * @param HeadTable $headTable
     */
    public function setHeadTable(HeadTable $headTable): void
    {
        $this->headTable = $headTable;
    }

    /**
     * @return HHeaTable
     */
    public function getHHeaTable(): HHeaTable
    {
        return $this->hHeaTable;
    }

    /**
     * @param HHeaTable $hHeaTable
     */
    public function setHHeaTable(HHeaTable $hHeaTable): void
    {
        $this->hHeaTable = $hHeaTable;
    }

    /**
     * @return HMtxTable
     */
    public function getHMtxTable(): HMtxTable
    {
        return $this->hMtxTable;
    }

    /**
     * @param HMtxTable $hMtxTable
     */
    public function setHMtxTable(HMtxTable $hMtxTable): void
    {
        $this->hMtxTable = $hMtxTable;
    }

    /**
     * @return LocaTable
     */
    public function getLocaTable(): LocaTable
    {
        return $this->locaTable;
    }

    /**
     * @param LocaTable $locaTable
     */
    public function setLocaTable(LocaTable $locaTable): void
    {
        $this->locaTable = $locaTable;
    }

    /**
     * @return MaxPTable
     */
    public function getMaxPTable(): MaxPTable
    {
        return $this->maxPTable;
    }

    /**
     * @param MaxPTable $maxPTable
     */
    public function setMaxPTable(MaxPTable $maxPTable): void
    {
        $this->maxPTable = $maxPTable;
    }

    /**
     * @return PostTable
     */
    public function getPostTable(): PostTable
    {
        return $this->postTable;
    }

    /**
     * @param PostTable $postTable
     */
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
}
