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
use PdfGenerator\Font\Frontend\File\Table\PostTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;

class TableDirectory
{
    /**
     * @var RawTable|null
     */
    private $cvtTable;

    /**
     * @var RawTable|null
     */
    private $fpgmTable;

    /**
     * @var RawTable|null
     */
    private $gaspTable;

    /**
     * @var RawTable|null
     */
    private $gDEFTable;

    /**
     * @var RawTable|null
     */
    private $gPOSTable;

    /**
     * @var RawTable|null
     */
    private $gSUBTable;

    /**
     * @var HeadTable
     */
    private $headTable;

    /**
     * @var HHeaTable
     */
    private $hHeaTable;

    /**
     * @var MaxPTable
     */
    private $maxPTable;

    /**
     * @var RawTable|null
     */
    private $nameTable;

    /**
     * @var RawTable|null
     */
    private $oS2Table;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var RawTable|null
     */
    private $prepTable;

    /**
     * @var RawTable[]
     */
    private $rawTables = [];

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
     * @return RawTable|null
     */
    public function getNameTable(): ?RawTable
    {
        return $this->nameTable;
    }

    /**
     * @param RawTable|null $nameTable
     */
    public function setNameTable(?RawTable $nameTable): void
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
