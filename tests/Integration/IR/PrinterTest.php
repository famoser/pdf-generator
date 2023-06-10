<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Integration\IR;

use PdfGenerator\IR\CursorPrinter;
use PdfGenerator\IR\Structure\Document;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPrintTextTextInResultFile()
    {
        // arrange
        $document = new Document();
        $document->addPage(new Document\Page(1, [210, 297]));

        $printer = new CursorPrinter($document);

        // act
        $printer->printText($printer->getCursor(), 'hallo welt', 20);

        // assert
        $result = $document->save();
        $this->assertStringContainsString('hallo welt', $result);
        file_put_contents('pdf.pdf', $result);
    }
}
