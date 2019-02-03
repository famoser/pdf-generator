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
use PdfGenerator\Frontend\Layout\Supporting\PrintBuffer;
use PdfGenerator\Frontend\PdfDocument;

class PrintTransaction implements TransactionInterface
{
    /**
     * @var \Closure
     */
    private $content;

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
    private $prePrintBuffer;

    /**
     * @var PrintBuffer
     */
    private $postPrintBuffer;

    /**
     * PrintBuffer constructor.
     *
     * @param PdfDocument $pdfDocument
     * @param float $width
     * @param \Closure $content
     */
    public function __construct(PdfDocument $pdfDocument, float $width, \Closure $content)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;

        $this->content = $content;

        $this->prePrintBuffer = new PrintBuffer($this->pdfDocument, $this->width);
        $this->postPrintBuffer = new PrintBuffer($this->pdfDocument, $this->width);
    }

    /**
     * prints the contained components.
     */
    public function commit()
    {
        $prePrint = $this->prePrintBuffer->flushBufferClosure();
        $prePrint();

        $printBuffer = $this->content;
        $printBuffer();

        $postPrint = $this->postPrintBuffer->flushBufferClosure();
        $postPrint();
    }

    /**
     * register a callable which prints directly to the document before the real print is started.
     * at the end of the callable, reset the layout to the state before the invocation to ensure the layout works as expected.
     *
     * @param callable $callable the arguments are decided by the transaction implementation. At least the document(s) to print to should be included.
     */
    public function registerDrawablePrePrint(callable $callable)
    {
        // TODO: call multiple times for each rectangle the transaction occupies
        $this->prePrintBuffer->addPrintable($callable, function () {
            return [];
        });
    }

    /**
     * register a callable which prints directly to the document after the real print has ended.
     * at the end of the callable, reset the layout to the state before the invocation to ensure the layout works as expected.
     *
     * @param callable $callable the arguments are decided by the transaction implementation. At least the document(s) to print to should be included.
     */
    public function registerDrawablePostPrint(callable $callable)
    {
        $this->postPrintBuffer->addPrintable($callable, function () {
            return [];
        });
    }
}
