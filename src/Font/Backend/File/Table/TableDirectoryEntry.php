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
use PdfGenerator\Font\Backend\File\TableVisitor;

class TableDirectoryEntry extends BaseTable
{
    /**
     * identifier of the referenced table.
     *
     * @ttf-type uint32
     *
     * @var string
     */
    private $tag;

    /**
     * checksum.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $checkSum;

    /**
     * offset from start of font directory.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $offset;

    /**
     * length of this table.
     *
     * @ttf-type uint32
     *
     * @var int
     */
    private $length;

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function getCheckSum(): int
    {
        return $this->checkSum;
    }

    public function setCheckSum(int $checkSum): void
    {
        $this->checkSum = $checkSum;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function accept(TableVisitor $visitor): string
    {
        return $visitor->visitTableDirectoryEntry($this);
    }
}
