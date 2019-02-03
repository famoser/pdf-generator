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
use PdfGenerator\Frontend\PdfDocument;

class ComposedTransaction implements TransactionInterface
{
    /**
     * @var PdfDocument
     */
    private $pdfDocument;

    /**
     * @var TransactionInterface[]
     */
    private $transactions;

    /**
     * PrintBuffer constructor.
     *
     * @param PdfDocument $pdfDocument
     * @param array $transactions
     */
    public function __construct(PdfDocument $pdfDocument, array $transactions)
    {
        $this->pdfDocument = $pdfDocument;
        $this->transactions = $transactions;
    }

    /**
     * prints the contained components.
     */
    public function commit()
    {
        foreach ($this->transactions as $transaction) {
            $transaction->commit();
        }
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
