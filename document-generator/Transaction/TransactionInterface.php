<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator\Transaction;

use DocumentGenerator\Transaction\Base\RootTransactionInterface;

interface TransactionInterface extends RootTransactionInterface
{
    /**
     * will group the transaction content together.
     *
     * if used in conjunction with a document with pages:, the returned transaction will add a page break before all elements if they do not fit on the same page
     *
     * @return TransactionInterface
     */
    public function asGroupedTransaction();
}
