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
use PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use PdfGenerator\Font\Frontend\File\Table\HeadTable;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use PdfGenerator\Font\Frontend\File\Table\LocaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\NameTable;
use PdfGenerator\Font\Frontend\File\Table\PostTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;

class FontFile
{
    /**
     * @var CMapTable|null
     */
    private $cMapTable;

    /**
     * lists values which can be referenced by instructions.
     *
     * @var RawTable|null
     */
    private $cvtTable;

    /**
     * lists instructions to be executed when first opening the font.
     *
     * @var RawTable|null
     */
    private $fpgmTable;

    /**
     * defines rasterization techniques based on the ppem of the device.
     *
     * @var RawTable|null
     */
    private $gaspTable;

    /**
     * contains additional glyph properties such as ligatures.
     *
     * @var RawTable|null
     */
    private $gDEFTable;

    /**
     * @var GlyfTable[]
     */
    private $glyfTables = [];

    /**
     * defines the position of glyphs for complex usages.
     *
     * @var RawTable|null
     */
    private $gPOSTable;

    /**
     * includes glyph substitutions.
     *
     * @var RawTable|null
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
     * contains metrics of the font only needed by microsoft/windows.
     *
     * @var RawTable|null
     */
    private $oS2Table;

    /**
     * lists instructions to be executed before each glyph is drawn.
     *
     * @var RawTable|null
     */
    private $prepTable;

    /**
     * @var PostTable|null
     */
    private $postTable;

    /**
     * any other table not recognised.
     *
     * @var RawTable[]
     */
    private $rawTables = [];

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
     * @return RawTable|null
     */
    public function getCvtTable(): ?RawTable
    {
        return $this->cvtTable;
    }

    /**
     * @param RawTable|null $cvtTable
     */
    public function setCvtTable(?RawTable $cvtTable): void
    {
        $this->cvtTable = $cvtTable;
    }

    /**
     * @return RawTable|null
     */
    public function getFpgmTable(): ?RawTable
    {
        return $this->fpgmTable;
    }

    /**
     * @param RawTable|null $fpgmTable
     */
    public function setFpgmTable(?RawTable $fpgmTable): void
    {
        $this->fpgmTable = $fpgmTable;
    }

    /**
     * @return RawTable|null
     */
    public function getGaspTable(): ?RawTable
    {
        return $this->gaspTable;
    }

    /**
     * @param RawTable|null $gaspTable
     */
    public function setGaspTable(?RawTable $gaspTable): void
    {
        $this->gaspTable = $gaspTable;
    }

    /**
     * @return RawTable|null
     */
    public function getGDEFTable(): ?RawTable
    {
        return $this->gDEFTable;
    }

    /**
     * @param RawTable|null $gDEFTable
     */
    public function setGDEFTable(?RawTable $gDEFTable): void
    {
        $this->gDEFTable = $gDEFTable;
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
     * @return RawTable|null
     */
    public function getGPOSTable(): ?RawTable
    {
        return $this->gPOSTable;
    }

    /**
     * @param RawTable|null $gPOSTable
     */
    public function setGPOSTable(?RawTable $gPOSTable): void
    {
        $this->gPOSTable = $gPOSTable;
    }

    /**
     * @return RawTable|null
     */
    public function getGSUBTable(): ?RawTable
    {
        return $this->gSUBTable;
    }

    /**
     * @param RawTable|null $gSUBTable
     */
    public function setGSUBTable(?RawTable $gSUBTable): void
    {
        $this->gSUBTable = $gSUBTable;
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
     * @return RawTable|null
     */
    public function getOS2Table(): ?RawTable
    {
        return $this->oS2Table;
    }

    /**
     * @param RawTable|null $oS2Table
     */
    public function setOS2Table(?RawTable $oS2Table): void
    {
        $this->oS2Table = $oS2Table;
    }

    /**
     * @return RawTable|null
     */
    public function getPrepTable(): ?RawTable
    {
        return $this->prepTable;
    }

    /**
     * @param RawTable|null $prepTable
     */
    public function setPrepTable(?RawTable $prepTable): void
    {
        $this->prepTable = $prepTable;
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
}
