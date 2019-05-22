<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend;

/**
 * can read supported pdf value types.
 *
 * notes:
 *  - always uses Big Endian ordering for parsing
 */
class StreamReader
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var int
     */
    private $byteCount;

    /**
     * Reader constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
        $this->byteCount = \strlen($content);
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readUInt8(): int
    {
        // append empty string if less than two bytes left because have to be able to unpack uInt16
        $offset = $this->offset - 1;
        if ($offset === -1) {
            $uInt16 = static::unpackUInt16(' ' . $this->content[0], 0);
        } else {
            $uInt16 = static::unpackUInt16($this->content, $offset);
        }
        ++$this->offset;

        return self::transformTo8Bit($uInt16);
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return int[]
     */
    public function readUInt8Array(int $size): array
    {
        $array = [];
        for ($i = 0; $i < $size; ++$i) {
            $array[] = $this->readUInt8();
        }

        return $array;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readInt8(): int
    {
        $uInt8 = $this->readUInt8();

        return self::transformToSinged($uInt8, 8);
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readUInt16(): int
    {
        $uInt16 = static::unpackUInt16($this->content, $this->offset);
        $this->offset += 2;

        return $uInt16;
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return int[]
     */
    public function readUInt16Array(int $size): array
    {
        $array = [];
        for ($i = 0; $i < $size; ++$i) {
            $array[] = $this->readUInt16();
        }

        return $array;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readInt16(): int
    {
        $uInt16 = $this->readUInt16();

        return self::transformToSinged($uInt16, 16);
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return int[]
     */
    public function readInt16Array(int $size): array
    {
        $array = [];
        for ($i = 0; $i < $size; ++$i) {
            $array[] = $this->readInt16();
        }

        return $array;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readUInt24(): int
    {
        $uInt16 = $this->readUInt16();
        $uInt8 = $this->readUInt8();

        return $uInt16 << 8 | $uInt8;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readUInt32(): int
    {
        $uInt32 = static::unpackUInt32($this->content, $this->offset);
        $this->offset += 4;

        return $uInt32;
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return int[]
     */
    public function readUInt32Array(int $size): array
    {
        $array = [];
        for ($i = 0; $i < $size; ++$i) {
            $array[] = $this->readUInt32();
        }

        return $array;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readInt32(): int
    {
        $uInt32 = $this->readUInt32();

        return self::transformToSinged($uInt32, 32);
    }

    /**
     * @throws \Exception
     *
     * @return float
     */
    public function readFixed(): float
    {
        $mantissa = $this->readInt16();
        $fraction = $this->readUInt16();

        return $mantissa + ($fraction / 65536.0);
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readFWORD(): int
    {
        return $this->readInt16();
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readLONGDATETIME(): int
    {
        $uInt64 = self::unpackUInt64($this->content, $this->offset);
        $this->offset += 8;

        $int64 = self::transformToSinged($uInt64, 64);

        return $int64;
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function readTag(): array
    {
        $uInt32 = $this->readUInt32();
        $val1 = $uInt32 >> 8;
        $val2 = $val1 >> 8;
        $val3 = $val2 >> 8;

        return [$val3 & 0xff, $val2 & 0xff, $val1 & 0xff, $uInt32 & 0xff];
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function readTagAsString(): string
    {
        $result = '';
        foreach ($this->readTag() as $entry) {
            $result .= \chr($entry);
        }

        return $result;
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readOffset16(): int
    {
        return $this->readUInt16();
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return array
     */
    public function readOffset16Array(int $size): array
    {
        return $this->readUInt16Array($size);
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readOffset32(): int
    {
        return $this->readUInt32();
    }

    /**
     * @param int $size
     *
     * @throws \Exception
     *
     * @return int[]
     */
    public function readOffset32Array(int $size): array
    {
        return $this->readUInt32Array($size);
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function readUFWORD()
    {
        return $this->readUInt16();
    }

    /**
     * @throws \Exception
     *
     * @return float
     */
    public function readF2DOT14(): float
    {
        $uInt16 = $this->readUInt16();

        // decimal are first two bits as two's complement
        $decimal = $uInt16 >> 14;
        if ($decimal > 1) {
            $decimal -= 4;
        }

        // clear the top two entries as these are for the decimals
        $numerator = $uInt16 & 0x3fff;
        $fraction = sprintf('%.6f', $numerator / 16384);

        return $decimal + $fraction;
    }

    /**
     * aligns the pointer by long.
     */
    public function alignLong(): void
    {
        $align = 8 - $this->offset % 8;
        $this->offset += $align;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isEndOfFileReached(): bool
    {
        return $this->offset >= $this->byteCount - 1;
    }

    /**
     * @param $content
     * @param int $offset
     *
     * @return int
     */
    private static function unpackUInt16($content, int $offset): int
    {
        return unpack('nnumber', $content, $offset)['number'];
    }

    /**
     * @param $content
     * @param int $offset
     *
     * @return int
     */
    private static function unpackUInt32($content, int $offset): int
    {
        return unpack('Nnumber', $content, $offset)['number'];
    }

    /**
     * @param $content
     * @param int $offset
     *
     * @return int
     */
    private static function unpackUInt64($content, int $offset): int
    {
        return unpack('Jnumber', $content, $offset)['number'];
    }

    /**
     * @param $number
     *
     * @return int
     */
    private static function transformTo8Bit(int $number): int
    {
        // zero out all bits except the last 8
        return $number & 0xff;
    }

    /**
     * @param int $number
     * @param int $bits
     *
     * @return int
     */
    private static function transformToSinged(int $number, int $bits): int
    {
        $cutoff = 2 ** ($bits - 1);

        return $number < $cutoff ? $number : $number - 2 ** $bits;
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
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    private $pushedOffsets = [];

    /**
     * remembers the current offset location and then sets the offset to the new value.
     *
     * @param int $offset
     */
    public function pushOffset(int $offset)
    {
        $this->pushedOffsets[] = $this->getOffset();
        $this->setOffset($offset);
    }

    /**
     * recovers the last remembered offset location.
     */
    public function popOffset()
    {
        $offset = array_pop($this->pushedOffsets);
        $this->setOffset($offset);
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function readUntil(int $offset)
    {
        $result = '';

        while ($this->offset < $offset) {
            $result .= $this->content[$this->offset++];
        }

        return $result;
    }
}
