<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure;

use PdfGenerator\Font\Frontend\File\Table\HeadTable;
use PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use PdfGenerator\Font\Frontend\File\Table\MaxPTable;
use PdfGenerator\Font\Frontend\File\Table\NameTable;
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
use PdfGenerator\Font\Frontend\File\Table\PostTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;

class TableDirectory
{
    private ?RawTable $cvtTable;

    private ?RawTable $fpgmTable;

    private ?RawTable $gaspTable;

    private ?RawTable $gDEFTable;

    private ?RawTable $gPOSTable;

    private ?RawTable $gSUBTable;

    private HeadTable $headTable;

    private HHeaTable $hHeaTable;

    private MaxPTable $maxPTable;

    private NameTable $nameTable;

    private OS2Table $oS2Table;

    private PostTable $postTable;

    private ?RawTable $prepTable;

    /**
     * @var RawTable[]
     */
    private array $rawTables = [];

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

    public function getMaxPTable(): MaxPTable
    {
        return $this->maxPTable;
    }

    public function setMaxPTable(MaxPTable $maxPTable): void
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

    public function getOS2Table(): OS2Table
    {
        return $this->oS2Table;
    }

    public function setOS2Table(OS2Table $oS2Table): void
    {
        $this->oS2Table = $oS2Table;
    }

    public function getPostTable(): PostTable
    {
        return $this->postTable;
    }

    public function setPostTable(PostTable $postTable): void
    {
        $this->postTable = $postTable;
    }

    public function getPrepTable(): ?RawTable
    {
        return $this->prepTable;
    }

    public function setPrepTable(?RawTable $prepTable): void
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
     * @param RawTable[] $rawTables
     */
    public function setRawTables(array $rawTables): void
    {
        $this->rawTables = $rawTables;
    }
}
