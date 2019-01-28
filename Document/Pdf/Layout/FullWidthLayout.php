<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf\Layout;

use App\Service\Report\Document\Layout\FullWidthLayoutInterface;
use App\Service\Report\Document\Pdf\Layout\Supporting\PrintBuffer;
use App\Service\Report\Document\Pdf\PdfDocumentInterface;
use App\Service\Report\Document\Pdf\Transaction\PrintTransaction;
use App\Service\Report\Document\Transaction\TransactionInterface;

class FullWidthLayout implements FullWidthLayoutInterface
{
    /**
     * @var PdfDocumentInterface
     */
    private $pdfDocument;

    /**
     * @var float
     */
    private $width;

    /**
     * @var PrintBuffer
     */
    private $buffer;

    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocumentInterface $pdfDocument
     * @param float $width
     */
    public function __construct(PdfDocumentInterface $pdfDocument, float $width)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;

        $this->buffer = new PrintBuffer($pdfDocument, $width);
    }

    /**
     * register a callable which prints to the pdf document
     * The position of the cursor at the time the callable is invoked is decided by the layout
     * ensure the cursor is below the printed content after the callable is finished to not mess up the layout.
     *
     * @param callable $callable takes a PdfDocumentInterface as an argument
     */
    public function registerPrintable(callable $callable)
    {
        $this->buffer->addPrintable($callable);
    }

    /**
     * will end the columned layout.
     *
     * @return TransactionInterface
     */
    public function getTransaction()
    {
        return new PrintTransaction($this->pdfDocument, $this->width, $this->buffer->flushBufferClosure());
    }
}
