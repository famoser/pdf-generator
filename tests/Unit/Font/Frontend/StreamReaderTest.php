<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Tests\Unit\Font\Frontend;

use Famoser\PdfGenerator\Font\Frontend\StreamReader;
use PHPUnit\Framework\TestCase;

class StreamReaderTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testUInt8MultipleNumbersResultAsExpected(): void
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
    public function testUInt8SingleNumbersResultAsExpected(): void
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
    public function testInt8MultipleNumbersResultAsExpected(): void
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
    public function testInt8SingleNumbersResultAsExpected(): void
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
    public function testUInt16MultipleNumbersResultAsExpected(): void
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
    public function testUInt16SingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            1_073_741_951 => 127,
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
    public function testInt16MultipleNumbersResultAsExpected(): void
    {
        $input = 134_250_623;
        $output = [2048, -32641];

        $packed = pack('N', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readInt16());
        $this->assertSame($output[1], $reader->readInt16());
    }

    /**
     * @throws \Exception
     */
    public function testInt16SingleNumbersResultAsExpected(): void
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
    public function testUInt24SingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            327760 => 327760,
            8_716_368 => 8_716_368,
            2128 => 2128,
            67_633_152 => 524288,
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
    public function testUInt32MultipleNumbersResultAsExpected(): void
    {
        $input = 576_460_754_450_916_516;
        $output = [134_217_728, 2_147_493_028];

        $packed = pack('J', $input);
        $reader = new StreamReader($packed);

        $this->assertSame($output[0], $reader->readUInt32());
        $this->assertSame($output[1], $reader->readUInt32());
    }

    /**
     * @throws \Exception
     */
    public function testUInt32SingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            138_412_032 => 138_412_032,
            4_294_967_295 => 4_294_967_295,
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
    public function testInt32SingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            32769 => 32769,
            2_147_493_888 => -2_147_473_408,
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
    public function testFixedSingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            1_073_741_824 => 16384.0,
            2_147_483_648 => -32768.0,
            1280 => 0.01953125,
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
    public function testFSDOT14SingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0x7FFF => 1.999939,
            0x7000 => 1.75,
            0x0001 => 0.000061,
            0 => 0.0,
            0xFFFF => -0.000061,
            0x8000 => -2.0,
        ];
        $epsilon = 0.00001;

        // check one-by-one
        foreach ($testNumbers as $input => $expectedOutput) {
            $packedInput = pack('N', $input);
            $reader = new StreamReader($packedInput);
            $reader->readInt16();
            $this->assertTrue($expectedOutput - $reader->readF2DOT14() < $epsilon);
        }
    }

    /**
     * @throws \Exception
     */
    public function testLONGDATETIMESingleNumbersResultAsExpected(): void
    {
        $testNumbers = [
            0 => 0,
            1 => 1,
            2_147_493_888 => 2_147_493_888,
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
    public function testTagMultipleNumbersResultAsExpected(): void
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
    public function testTagAsStringResultAsExpected(): void
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
    public function testAlignLongAlignedAsExpected(): void
    {
        $input = 0x2020202020202020;
        $input2 = 0x3030;

        $packed = pack('J', $input).pack('n', $input2);
        $reader = new StreamReader($packed);

        $reader->readUInt16();
        $reader->alignLong();

        $output = $reader->readUInt16();
        $this->assertSame($output, $input2);
    }
}
