<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table;

class TableDirectoryEntry
{
    /**
     * identifier of the referenced table.
     *
     * @ttf-type uint32
     */
    private string $tag;

    /**
     * checksum.
     *
     * @ttf-type uint32
     */
    private int $checkSum;

    /**
     * offset from start of font directory.
     *
     * @ttf-type uint32
     */
    private int $offset;

    /**
     * length of this table.
     *
     * @ttf-type uint32
     */
    private int $length;

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
}
