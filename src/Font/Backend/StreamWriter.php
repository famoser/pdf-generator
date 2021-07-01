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
    private $stream = '';

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

    public function writeUInt8(int $value)
    {
        $stream = pack('n', $value);

        // big endian; hence first parts are 0
        $this->stream .= substr($stream, 1);
    }

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

    public function writeUInt32(int $value)
    {
        $this->stream .= pack('N', $value);
    }

    public function writeUInt64(int $value)
    {
        $this->stream .= pack('J', $value);
    }

    public function writeFWORD(int $value)
    {
        $this->writeInt16($value);
    }

    public function writeTagFromString(string $tag)
    {
        $this->stream .= $tag;
    }

    public function writeStream(string $content)
    {
        $this->stream .= $content;
    }

    public function byteAlign(int $multiple)
    {
        $length = $this->getLength();
        $rest = $length % $multiple;
        while ($rest >= 8) {
            $this->writeUInt64(0);
            $rest -= 8;
        }

        if ($rest >= 4) {
            $this->writeUInt32(0);
            $rest -= 4;
        }

        if ($rest >= 2) {
            $this->writeUInt16(0);
            $rest -= 2;
        }

        if ($rest === 1) {
            $this->writeUInt8(0);
        }
    }

    public function writeOffset16(int $value)
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

    public function writeFixed(float $value)
    {
        $mantissa = (int)$value;
        $fraction = (int)(($value - $mantissa) * 65536);

        $this->writeInt16($mantissa);
        $this->writeUInt16($fraction);
    }

    public function writeUFWORD(int $value)
    {
        $this->writeUInt16($value);
    }

    public function writeLONGDATETIME(int $value)
    {
        $unsigned = self::transformToUnSinged($value, 64);
        $this->writeUInt64($unsigned);
    }

    private static function transformToUnSinged(int $number, int $bits): int
    {
        return $number >= 0 ? $number : $number + 2 ** $bits;
    }

    public function writeUInt32Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeUInt32($value);
        }
    }

    public function writeUInt8Array(array $values)
    {
        foreach ($values as $value) {
            $this->writeUInt8($value);
        }
    }
}
