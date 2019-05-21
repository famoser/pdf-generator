<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Unit\Font\Frontend;

use PdfGenerator\Font\Frontend\StreamReader;
use PHPUnit\Framework\TestCase;

class StreamReaderTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testUInt8_multipleNumbers_resultAsExpected()
    {
        $input = 2172;
        $output = [0, 0, 8, 124];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readUInt8());
        $this->assertSame($output[1], $reader->readUInt8());
        $this->assertSame($output[2], $reader->readUInt8());
        $this->assertSame($output[3], $reader->readUInt8());
    }

    /**
     * @throws \Exception
     */
    public function testUInt8_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            240 => 240,
            255 => 255,
            256 => 0,
            27172 => 36,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('n', $input);
            $reader = new StreamReader($packedInput);
            $reader->readUInt8();
            $this->assertSame($expectedOutput, $reader->readUInt8());
        }
    }

    /**
     * @throws \Exception
     */
    public function testInt8_multipleNumbers_resultAsExpected()
    {
        $input = 781111;
        $output = [0, 11, -21, 55];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readInt8());
        $this->assertSame($output[1], $reader->readInt8());
        $this->assertSame($output[2], $reader->readInt8());
        $this->assertSame($output[3], $reader->readInt8());
    }

    /**
     * @throws \Exception
     */
    public function testInt8_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            124 => 124,
            127 => 127,
            -128 => -128,
            129 => -127,
            130 => -126,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('n', $input);
            $reader = new StreamReader($packedInput);
            $reader->readInt8();
            $this->assertSame($expectedOutput, $reader->readInt8());
        }
    }

    /**
     * @throws \Exception
     */
    public function testUInt16_multipleNumbers_resultAsExpected()
    {
        $input = 65537;
        $output = [1, 1];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readUInt16());
        $this->assertSame($output[1], $reader->readUInt16());
    }

    /**
     * @throws \Exception
     */
    public function testUInt16_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            1073741951 => 127,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $reader->readUInt16();
            $this->assertSame($expectedOutput, $reader->readUInt16());
        }
    }

    /**
     * @throws \Exception
     */
    public function testInt16_multipleNumbers_resultAsExpected()
    {
        $input = 134250623;
        $output = [2048, -32641];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readInt16());
        $this->assertSame($output[1], $reader->readInt16());
    }

    /**
     * @throws \Exception
     */
    public function testInt16_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32776 => -32760,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $reader->readInt16();
            $this->assertSame($expectedOutput, $reader->readInt16());
        }
    }

    /**
     * @throws \Exception
     */
    public function testUInt24_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            327760 => 327760,
            8716368 => 8716368,
            2128 => 2128,
            67633152 => 524288,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $reader->readUInt8();
            $this->assertSame($expectedOutput, $reader->readUInt24());
        }
    }

    /**
     * @throws \Exception
     */
    public function testUInt32_multipleNumbers_resultAsExpected()
    {
        $input = 576460754450916516;
        $output = [134217728, 2147493028];

        $packed = pack('J', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readUInt32());
        $this->assertSame($output[1], $reader->readUInt32());
    }

    /**
     * @throws \Exception
     */
    public function testUInt32_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            138412032 => 138412032,
            4294967295 => 4294967295,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('J', $input);
            $reader = new StreamReader($packedInput);
            $reader->readUInt32();
            $this->assertSame($expectedOutput, $reader->readUInt32());
        }
    }

    /**
     * @throws \Exception
     */
    public function testInt32_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            2147493888 => -2147473408,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('J', $input);
            $reader = new StreamReader($packedInput);
            $reader->readUInt32();
            $this->assertSame($expectedOutput, $reader->readInt32());
        }
    }

    /**
     * @throws \Exception
     */
    public function testFixed_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            1073741824 => 16384.0,
            2147483648 => -32768.0,
            1280 => 0.128,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $this->assertSame($expectedOutput, $reader->readFixed());
        }
    }

    /**
     * @throws \Exception
     */
    public function testFSDOT14_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0x7fff => 1.999939,
            0x7000 => 1.75,
            0x0001 => 0.000061,
            0 => 0.0,
            0xffff => -0.000061,
            0x8000 => -2.0,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $reader->readInt16();
            $this->assertSame($expectedOutput, $reader->readF2DOT14());
        }
    }

    /**
     * @throws \Exception
     */
    public function testLONGDATETIME_singleNumbers_resultAsExpected()
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            2147493888 => 2147493888,
            0x1000000000000500 => 0x1000000000000500,
        ];

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('J', $input);
            $reader = new StreamReader($packedInput);
            $this->assertSame($expectedOutput, $reader->readLONGDATETIME());
        }
    }

    /**
     * @throws \Exception
     */
    public function testTag_multipleNumbers_resultAsExpected()
    {
        $input = 2172;
        $output = [0, 0, 8, 124];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $tag = $reader->readTag();
        $this->assertSame($output[0], $tag[0]);
        $this->assertSame($output[1], $tag[1]);
        $this->assertSame($output[2], $tag[2]);
        $this->assertSame($output[3], $tag[3]);
    }

    /**
     * @throws \Exception
     */
    public function testTagAsString_resultAsExpected()
    {
        $input = 0x20202020;
        $output = '    '; // four spaces

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $tag = $reader->readTagAsString();
        $this->assertSame($tag, $output);
    }

    /**
     * @throws \Exception
     */
    public function testAlignLong_alignedAsExpected()
    {
        $input = 0x2020202020202020;
        $input2 = 0x3030;

        $packed = pack('J', $input) . pack('n', $input2);
        $reader = new StreamReader($packed);

        $reader->readUInt16();
        $reader->alignLong();

        $output = $reader->readUInt16();
        $this->assertSame($output, $input2);
    }
}
