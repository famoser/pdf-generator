<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table\Name;

/**
 * defines the location & type of strings.
 */
class NameRecord
{
    /**
     * platform id.
     *
     * @ttf-type uint16
     */
    private int $platformID;

    /**
     * encoding id.
     * called platformSpecificID in the apple specification.
     *
     * @ttf-type uint16
     */
    private int $encodingID;

    /**
     * language id.
     * if the id > 0x8000 then it refers to the langTagRecords
     * langTagRecordIndex = languageID - 0x8000.
     *
     * @ttf-type uint16
     */
    private int $languageID;

    /**
     * name id.
     *
     * @ttf-type uint16
     */
    private int $nameID;

    /**
     * length.
     *
     * @ttf-type uint16
     */
    private int $length;

    /**
     * offset from start of storage area.
     *
     * @ttf-type offset16
     */
    private int $offset;

    /**
     * the actual read out value.
     */
    private string $value;

    public function getPlatformID(): int
    {
        return $this->platformID;
    }

    public function setPlatformID(int $platformID): void
    {
        $this->platformID = $platformID;
    }

    public function getEncodingID(): int
    {
        return $this->encodingID;
    }

    public function setEncodingID(int $encodingID): void
    {
        $this->encodingID = $encodingID;
    }

    public function getLanguageID(): int
    {
        return $this->languageID;
    }

    public function setLanguageID(int $languageID): void
    {
        $this->languageID = $languageID;
    }

    public function getNameID(): int
    {
        return $this->nameID;
    }

    public function setNameID(int $nameID): void
    {
        $this->nameID = $nameID;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
