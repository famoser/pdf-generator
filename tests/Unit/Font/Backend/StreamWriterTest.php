<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Unit\Font\Backend;

use PdfGenerator\Font\Backend\StreamWriter;
use PdfGenerator\Font\Frontend\StreamReader;
use PHPUnit\Framework\TestCase;

class StreamWriterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testInt8()
    {
        $values = [
            -128,
            -21,
            -1,
            0,
            1,
            56,
            127,
        ];

        $this->assertValueWritesUsingReader($values, 'Int8');
    }

    /**
     * @throws \Exception
     */
    public function testInt16()
    {
        $values = [
            -32768,
            -2371,
            -1,
            0,
            1,
            723,
            32767,
        ];

        $this->assertValueWritesUsingReader($values, 'Int16');
    }

    /**
     * @throws \Exception
     */
    public function testUInt32()
    {
        $values = [
            0,
            9213,
            4_294_967_295,
        ];

        $this->assertValueWritesUsingReader($values, 'UInt32');
    }

    /**
     * @throws \Exception
     */
    public function testLONGDATETIME()
    {
        $values = [
            0,
            1,
            7_128_381,
            42_949_672_958_123,
        ];

        $this->assertValueWritesUsingReader($values, 'LONGDATETIME');
    }

    /**
     * @throws \Exception
     */
    public function testFixed()
    {
        $values = [
            0.0,
            1.25,
            4.5,
            8.125,
        ];

        $this->assertValueWritesUsingReader($values, 'Fixed');
    }

    private function assertValueWritesUsingReader(array $values, $type)
    {
        foreach ($values as $value) {
            $this->assertValueWriteUsingReader($value, $type);
        }
    }

    private function assertValueWriteUsingReader($value, $type)
    {
        $writeFunction = 'write'.$type;
        $streamWriter = new StreamWriter();
        $streamWriter->$writeFunction($value);

        $output = $streamWriter->getStream();

        $readFunction = 'read'.$type;
        $streamReader = new StreamReader($output);
        $actual = $streamReader->$readFunction();

        $this->assertSame($value, $actual);
    }
}
