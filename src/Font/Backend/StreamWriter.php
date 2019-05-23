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

    /**
     * @param int $value
     */
    public function writeUInt8(int $value)
    {
        $stream = pack('n', $value);

        // big endian; hence first parts are 0
        $this->stream .= substr($stream, 1);
    }

    /**
     * @param int $value
     */
    public function writeInt8(int $value)
    {
        $unsigned = self::transformToUnSinged($value, 8);
        $this->writeUInt8($unsigned);
    }

    /**
     * @param int[] $values
     */
    public function writeInt8Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeInt8($value);
        }
    }

    /**
     * @param int $value
     */
    public function writeInt16(int $value)
    {
        $unsigned = self::transformToUnSinged($value, 16);
        $this->writeUInt16($unsigned);
    }

    /**
     * @param int[] $values
     */
    public function writeInt16Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeInt16($value);
        }
    }

    /**
     * @param int $value
     */
    public function writeUInt16(int $value)
    {
        $this->stream .= pack('n', $value);
    }

    /**
     * @param int[] $values
     */
    public function writeUInt16Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeUInt16($value);
        }
    }

    /**
     * @param int $value
     */
    public function writeUInt32(int $value)
    {
        $this->stream .= pack('N', $value);
    }

    /**
     * @param int $value
     */
    public function writeUInt64(int $value)
    {
        $this->stream .= pack('J', $value);
    }

    /**
     * @param int $value
     */
    public function writeFWORD(int $value)
    {
        $this->writeInt16($value);
    }

    /**
     * @param string $tag
     */
    public function writeTagFromString(string $tag)
    {
        $this->stream .= $tag;
    }

    /**
     * @param string $content
     */
    public function writeStream(string $content)
    {
        $this->stream .= $content;
    }

    /**
     * @param int $value
     */
    private function writeOffset16(int $value)
    {
        $this->writeUInt16($value);
    }

    /**
     * @param int[] $values
     */
    public function writeOffset16Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeOffset16($value);
        }
    }

    /**
     * @param int $value
     */
    public function writeOffset32(int $value)
    {
        $this->writeUInt32($value);
    }

    /**
     * @param int[] $values
     */
    public function writeOffset32Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeOffset32($value);
        }
    }

    /**
     * @param float $value
     */
    public function writeFixed(float $value)
    {
        $mantissa = (int)$value;
        $fraction = ($value - $mantissa) * 65536;

        $this->writeInt16($mantissa);
        $this->writeUInt16($fraction);
    }

    /**
     * @param int $value
     */
    public function writeUFWORD(int $value)
    {
        $this->writeUInt16($value);
    }

    /**
     * @param int $value
     */
    public function writeLONGDATETIME(int $value)
    {
        $unsigned = self::transformToUnSinged($value, 64);
        $this->writeUInt64($unsigned);
    }

    /**
     * @param int $number
     * @param int $bits
     *
     * @return int
     */
    private static function transformToUnSinged(int $number, int $bits): int
    {
        return $number >= 0 ? $number : $number + 2 ** $bits;
    }
}
