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
            4294967295,
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
            7128381,
            42949672958123,
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

    /**
     * @param array $values
     * @param $type
     */
    private function assertValueWritesUsingReader(array $values, $type)
    {
        foreach ($values as $value) {
            $this->assertValueWriteUsingReader($value, $type);
        }
    }

    /**
     * @param $value
     * @param $type
     */
    private function assertValueWriteUsingReader($value, $type)
    {
        $writeFunction = 'write' . $type;
        $streamWriter = new StreamWriter();
        $streamWriter->$writeFunction($value);

        $output = $streamWriter->getStream();

        $readFunction = 'read' . $type;
        $streamReader = new StreamReader($output);
        $actual = $streamReader->$readFunction();

        $this->assertSame($value, $actual);
    }
}
