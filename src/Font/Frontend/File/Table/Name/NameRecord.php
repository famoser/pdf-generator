<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Name;

/**
 * defines the location & type of strings.
 */
class NameRecord
{
    /**
     * platform id.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $platformID;

    /**
     * encoding id.
     * called platformSpecificID in the apple specification.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $encodingID;

    /**
     * language id.
     * if the id > 0x8000 then it refers to the langTagRecords
     * langTagRecordIndex = languageID - 0x8000.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $languageID;

    /**
     * name id.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $nameID;

    /**
     * length.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $length;

    /**
     * offset from start of storage area.
     *
     * @ttf-type offset16
     *
     * @var int
     */
    private $offset;

    /**
     * the actual read out value.
     *
     * @var string
     */
    private $value;

    /**
     * @return int
     */
    public function getPlatformID(): int
    {
        return $this->platformID;
    }

    /**
     * @param int $platformID
     */
    public function setPlatformID(int $platformID): void
    {
        $this->platformID = $platformID;
    }

    /**
     * @return int
     */
    public function getEncodingID(): int
    {
        return $this->encodingID;
    }

    /**
     * @param int $encodingID
     */
    public function setEncodingID(int $encodingID): void
    {
        $this->encodingID = $encodingID;
    }

    /**
     * @return int
     */
    public function getLanguageID(): int
    {
        return $this->languageID;
    }

    /**
     * @param int $languageID
     */
    public function setLanguageID(int $languageID): void
    {
        $this->languageID = $languageID;
    }

    /**
     * @return int
     */
    public function getNameID(): int
    {
        return $this->nameID;
    }

    /**
     * @param int $nameID
     */
    public function setNameID(int $nameID): void
    {
        $this->nameID = $nameID;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
