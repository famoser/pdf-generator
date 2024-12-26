<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMapTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HeadTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HMtxTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\LocaTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\NameTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\OS2Table;
use Famoser\PdfGenerator\Font\Frontend\File\Table\PostTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\RawTable;

class FontFile
{
    private bool $isTrueTypeFile;

    private ?CMapTable $cMapTable = null;

    /**
     * lists values which can be referenced by instructions.
     */
    private ?RawTable $cvtTable = null;

    /**
     * lists instructions to be executed when first opening the font.
     */
    private ?RawTable $fpgmTable = null;

    /**
     * defines rasterization techniques based on the ppem of the device.
     */
    private ?RawTable $gaspTable = null;

    /**
     * contains additional glyph properties such as ligatures.
     */
    private ?RawTable $gDEFTable = null;

    /**
     * @var (GlyfTable|null)[]
     */
    private array $glyfTables = [];

    /**
     * defines the position of glyphs for complex usages.
     */
    private ?RawTable $gPOSTable = null;

    /**
     * includes glyph substitutions.
     */
    private ?RawTable $gSUBTable = null;

    private ?HeadTable $headTable = null;

    private ?HHeaTable $hHeaTable = null;

    private ?HMtxTable $hMtxTable = null;

    private ?LocaTable $locaTable = null;

    private ?MaxPTable $maxPTable = null;

    private NameTable $nameTable;

    private OS2Table $oS2Table;

    /**
     * lists instructions to be executed before each glyph is drawn.
     */
    private ?RawTable $prepTable = null;

    private ?PostTable $postTable = null;

    /**
     * any other table not recognised.
     *
     * @var RawTable[]
     */
    private array $rawTables = [];

    public function getIsTrueTypeFile(): bool
    {
        return $this->isTrueTypeFile;
    }

    public function setIsTrueTypeFile(bool $isTrueTypeFile): void
    {
        $this->isTrueTypeFile = $isTrueTypeFile;
    }

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
     * @return (GlyfTable|null)[]
     */
    public function getGlyfTables(): array
    {
        return $this->glyfTables;
    }

    /**
     * @param (GlyfTable|null)[] $glyfTables
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
