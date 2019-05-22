<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Backend;

class StreamWriter
{
    /**
     * @var string
     */
    private $stream;

    /**
     * @return int
     */
    public function getLength(): int
    {
        return \strlen($this->stream);
    }

    /**
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }

    public function writeUInt32(int $getScalerType)
    {
    }

    public function writeUInt16(int $getNumTables)
    {
    }

    public function writeInt16(int $getXMin)
    {
    }

    public function writeFWORD(int $getXMin)
    {
    }

    public function writeOffset32(int $getOffset)
    {
    }

    public function writeUInt16Array(array $getEndCodes)
    {
    }

    public function writeInt16Array(array $getIdDeltas)
    {
    }

    public function writeTagFromString(string $getTag)
    {
    }

    public function writeRaw(string $getContent)
    {
    }

    public function writeOffset16Array(array $getOffsets)
    {
    }

    public function writeOffset32Array(array $getOffsets)
    {
    }

    public function writeFixed(float $getVersion)
    {
    }

    public function writeUFWORD(float $getLineGap)
    {
    }

    public function writeLONGDATETIME(int $getModified)
    {
    }
}
