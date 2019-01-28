<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Pdf\Transaction;

use App\Service\Report\Document\Pdf\Cursor;
use App\Service\Report\Document\Pdf\Layout\Supporting\PrintBuffer;
use App\Service\Report\Document\Pdf\PdfDocumentInterface;
use App\Service\Report\Document\Transaction\TransactionInterface;

class PrintTransaction implements TransactionInterface
{
    /**
     * @var \Closure
     */
    private $content;

    /**
     * @var PdfDocumentInterface
     */
    private $pdfDocument;

    /**
     * @var float
     */
    private $width;

    /**
     * @var Cursor[]
     */
    private $printAreaCache;

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
     * @param PdfDocumentInterface $pdfDocument
     * @param float $width
     * @param \Closure $content
     */
    public function __construct(PdfDocumentInterface $pdfDocument, float $width, \Closure $content)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;

        $this->content = $content;

        $this->prePrintBuffer = new PrintBuffer($this->pdfDocument, $this->width);
        $this->postPrintBuffer = new PrintBuffer($this->pdfDocument, $this->width);
    }

    /**
     * get the area of the to-be printed area by this transaction
     * returns an array where the first entry is the start cursor; the second the end cursor.
     *
     * @return Cursor[]
     */
    public function calculatePrintArea()
    {
        if (!$this->printAreaCache) {
            $before = $this->pdfDocument->getCursor();
            $after = $this->pdfDocument->cursorAfterwardsIfPrinted($this->content);

            $after = $after->setX($before->getXCoordinate() + $this->width);

            $this->printAreaCache = [$before, $after];
        }

        return $this->printAreaCache;
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
        $this->prePrintBuffer->addPrintable($callable, function () {
            return [$this->calculatePrintArea()[1]];
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
            return [$this->calculatePrintArea()[1]];
        });
    }
}
