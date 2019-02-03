<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\Frontend;

use PdfGenerator\IR\Cursor;
use PdfGenerator\IR\Printer;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintText_textInResultFile()
    {
        $text = 'Hi mom';
        $result = $this->getResultOf(function (Printer $printer) use ($text) {
            $printer->printText($text, 200);
        });

        $this->assertContains($text, $result);
    }

    /**
     * @param callable $callable
     *
     * @throws \Exception
     *
     * @return false|string
     */
    private function getResultOf(callable $callable)
    {
        $printer = new Printer();
        $printer->setCursor(new Cursor(1, 1, 1));

        $callable($printer);

        $tempFilePath = __DIR__ . \DIRECTORY_SEPARATOR . 'PrinterTest_' . uniqid() . '.pdf';
        $printer->save($tempFilePath);
        $result = file_get_contents($tempFilePath);

        return $result;
    }
}
