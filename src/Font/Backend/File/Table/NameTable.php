<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend\File\Table;

use PdfGenerator\Font\Backend\File\Table\Base\BaseTable;
use PdfGenerator\Font\Backend\File\Table\Name\NameRecord;
use PdfGenerator\Font\Backend\File\TableVisitor;

/**
 * the name table associates strings with the font for different languages.
 * does not depend on the characters included in the font.
 *
 * @see https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6name.html
 * @see https://docs.microsoft.com/en-us/typography/opentype/spec/name
 */
class NameTable extends BaseTable
{
    /**
     * the format of the table.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $format;

    /**
     * number of records.
     *
     * @ttf-type uint16
     *
     * @var int
     */
    private $count;

    /**
     * offset of start of string content from the beginning of the table.
     *
     * @ttf-type offset16
     *
     * @var int
     */
    private $stringOffset;

    /**
     * name records.
     *
     * @var NameRecord[]
     */
    private $nameRecords = [];

    /**
     * @return int
     */
    public function getFormat(): int
    {
        return $this->format;
    }

    /**
     * @param int $format
     */
    public function setFormat(int $format): void
    {
        $this->format = $format;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getStringOffset(): int
    {
        return $this->stringOffset;
    }

    /**
     * @param int $stringOffset
     */
    public function setStringOffset(int $stringOffset): void
    {
        $this->stringOffset = $stringOffset;
    }

    /**
     * @return NameRecord[]
     */
    public function getNameRecords(): array
    {
        return $this->nameRecords;
    }

    /**
     * @param NameRecord $nameRecord
     */
    public function addNameRecord(NameRecord $nameRecord): void
    {
        $this->nameRecords[] = $nameRecord;
    }

    /**
     * @param TableVisitor $visitor
     *
     * @return string
     */
    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitNameTable($this);
    }
}
