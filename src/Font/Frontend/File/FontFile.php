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
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
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
     * @var NameTable
     */
    private $nameTable;

    /**
     * @var OS2Table
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

    public function getCMapTable(): ?CMapTable
    {
        return $this->cMapTable;
    }

    public function setCMapTable(?CMapTable $cMapTable): void
    {
        $this->cMapTable = $cMapTable;
    }

    public function getCvtTable(): ?RawTable
    {
        return $this->cvtTable;
    }

    public function setCvtTable(?RawTable $cvtTable): void
    {
        $this->cvtTable = $cvtTable;
    }

    public function getFpgmTable(): ?RawTable
    {
        return $this->fpgmTable;
    }

    public function setFpgmTable(?RawTable $fpgmTable): void
    {
        $this->fpgmTable = $fpgmTable;
    }

    public function getGaspTable(): ?RawTable
    {
        return $this->gaspTable;
    }

    public function setGaspTable(?RawTable $gaspTable): void
    {
        $this->gaspTable = $gaspTable;
    }

    public function getGDEFTable(): ?RawTable
    {
        return $this->gDEFTable;
    }

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

    public function getGPOSTable(): ?RawTable
    {
        return $this->gPOSTable;
    }

    public function setGPOSTable(?RawTable $gPOSTable): void
    {
        $this->gPOSTable = $gPOSTable;
    }

    public function getGSUBTable(): ?RawTable
    {
        return $this->gSUBTable;
    }

    public function setGSUBTable(?RawTable $gSUBTable): void
    {
        $this->gSUBTable = $gSUBTable;
    }

    public function getHeadTable(): ?HeadTable
    {
        return $this->headTable;
    }

    public function setHeadTable(?HeadTable $headTable): void
    {
        $this->headTable = $headTable;
    }

    public function getHHeaTable(): ?HHeaTable
    {
        return $this->hHeaTable;
    }

    public function setHHeaTable(?HHeaTable $hHeaTable): void
    {
        $this->hHeaTable = $hHeaTable;
    }

    public function getHMtxTable(): ?HMtxTable
    {
        return $this->hMtxTable;
    }

    public function setHMtxTable(?HMtxTable $hMtxTable): void
    {
        $this->hMtxTable = $hMtxTable;
    }

    public function getLocaTable(): ?LocaTable
    {
        return $this->locaTable;
    }

    public function setLocaTable(?LocaTable $locaTable): void
    {
        $this->locaTable = $locaTable;
    }

    public function getMaxPTable(): ?MaxPTable
    {
        return $this->maxPTable;
    }

    public function setMaxPTable(?MaxPTable $maxPTable): void
    {
        $this->maxPTable = $maxPTable;
    }

    public function getNameTable(): NameTable
    {
        return $this->nameTable;
    }

    public function setNameTable(NameTable $nameTable): void
    {
        $this->nameTable = $nameTable;
    }

    public function getOS2Table(): ?OS2Table
    {
        return $this->oS2Table;
    }

    public function setOS2Table(?OS2Table $oS2Table): void
    {
        $this->oS2Table = $oS2Table;
    }

    public function getPrepTable(): ?RawTable
    {
        return $this->prepTable;
    }

    public function setPrepTable(?RawTable $prepTable): void
    {
        $this->prepTable = $prepTable;
    }

    public function getPostTable(): ?PostTable
    {
        return $this->postTable;
    }

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

    public function addRawTable(RawTable $rawTable): void
    {
        $this->rawTables[] = $rawTable;
    }
}
