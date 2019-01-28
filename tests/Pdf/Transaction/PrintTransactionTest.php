<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Tests\Pdf\Transaction;

use PdfGenerator\Pdf\Cursor;
use PdfGenerator\Pdf\PdfDocumentInterface;
use PdfGenerator\Pdf\Transaction\PrintTransaction;
use PdfGenerator\Tests\Pdf\Mock\PdfDocumentMock;
use PHPUnit\Framework\TestCase;

class PrintTransactionTest extends TestCase
{
    /**
     * @var PdfDocumentInterface
     */
    private $pdfDocument;

    /**
     * LayoutFactoryTest constructor.
     *
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->pdfDocument = new PdfDocumentMock();
    }

    public function testCalculatePrintArea_returnsCorrectResult()
    {
        // test if print area returns expected cursors with phpunit mocks
        $start = new Cursor(0, 200, 0);
        $end = new Cursor(0, 200, 1);

        $pdfDocument = \Mockery::mock(PdfDocumentInterface::class, [
            'getCursor' => $start,
            'cursorAfterwardsIfPrinted' => $end,
        ]);
        $width = 200;
        $transaction = new PrintTransaction($pdfDocument, $width, function () { });

        [$before, $after] = $transaction->calculatePrintArea();

        $this->assertNotNull($before);
        $this->assertNotNull($after);

        $this->cursorMatch($start, $before);
        $this->cursorMatch($end->setX($end->getXCoordinate() + $width), $after);
    }

    private function cursorMatch(Cursor $expected, Cursor $actual)
    {
        $this->assertSame($expected->getXCoordinate(), $actual->getXCoordinate());
        $this->assertSame($expected->getYCoordinate(), $actual->getYCoordinate());
        $this->assertSame($expected->getPage(), $actual->getPage());
    }
}
