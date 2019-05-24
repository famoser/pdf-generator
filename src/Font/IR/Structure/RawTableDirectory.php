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

use PdfGenerator\Font\Frontend\File\Table\CvtTable;
use PdfGenerator\Font\Frontend\File\Table\FpgmTable;
use PdfGenerator\Font\Frontend\File\Table\GaspTable;
use PdfGenerator\Font\Frontend\File\Table\GDEFTable;
use PdfGenerator\Font\Frontend\File\Table\GPOSTable;
use PdfGenerator\Font\Frontend\File\Table\GSUBTable;
use PdfGenerator\Font\Frontend\File\Table\NameTable;
use PdfGenerator\Font\Frontend\File\Table\OS2Table;
use PdfGenerator\Font\Frontend\File\Table\PrepTable;
use PdfGenerator\Font\Frontend\File\Table\RawTable;

class RawTableDirectory
{
    /**
     * @var CvtTable|null
     */
    private $cvtTable;

    /**
     * @var FpgmTable|null
     */
    private $fpgmTable;

    /**
     * @var GaspTable|null
     */
    private $gaspTable;

    /**
     * @var GDEFTable|null
     */
    private $gDEFTable;

    /**
     * @var GPOSTable|null
     */
    private $gPOSTable;

    /**
     * @var GSUBTable|null
     */
    private $gSUBTable;

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
    public function getFpgmTable(): ?FpgmTable
    {
        return $this->fpgmTable;
    }

    /**
     * @param FpgmTable|null $fpgmTable
     */
    public function setFpgmTable(?FpgmTable $fpgmTable): void
    {
        $this->fpgmTable = $fpgmTable;
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
     * @param RawTable[] $rawTables
     */
    public function setRawTables(array $rawTables): void
    {
        $this->rawTables = $rawTables;
    }
}
