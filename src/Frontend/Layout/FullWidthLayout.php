<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use DocumentGenerator\Layout\FullWidthLayoutInterface;
use DocumentGenerator\Transaction\TransactionInterface;
use PdfGenerator\Frontend\Layout\Supporting\PrintBuffer;
use PdfGenerator\Frontend\PdfDocument;
use PdfGenerator\Frontend\Transaction\PrintTransaction;

class FullWidthLayout implements FullWidthLayoutInterface
{
    /**
     * @var PdfDocument
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
     * @param PdfDocument $pdfDocument
     * @param float $width
     */
    public function __construct(PdfDocument $pdfDocument, float $width)
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
     * @param callable $callable takes a PdfDocument as an argument
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
