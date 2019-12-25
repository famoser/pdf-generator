<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Transaction;

use DocumentGenerator\Transaction\TransactionInterface;
use PdfGenerator\Frontend\Document;
use PdfGenerator\Frontend\Layout\Supporting\PrintBuffer;

class PrintTransaction implements TransactionInterface
{
    /**
     * @var PrintBuffer
     */
    private $printBuffer;

    /**
     * @var Document
     */
    private $pdfDocument;

    /**
     * @var float
     */
    private $width;

    /**
     * PrintBuffer constructor.
     */
    public function __construct(Document $pdfDocument, float $width, PrintBuffer $printBuffer)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;

        $this->printBuffer = $printBuffer;
    }

    /**
     * prints the contained components.
     */
    public function commit()
    {
        $this->printBuffer->flushBufferClosure()();
    }

    /**
     * will group the transaction content together.
     *
     * if used in conjunction with a document with pages:, the returned transaction will add a page break before all elements if they do not fit on the same page
     *
     * @return TransactionInterface
     */
    public function asGroupedTransaction()
    {
        return $this;
    }
}
