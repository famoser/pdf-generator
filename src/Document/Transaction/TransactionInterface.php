<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Transaction;

use PdfGenerator\Document\Transaction\Base\DrawableTransactionInterface;
use PdfGenerator\Document\Transaction\Base\RootTransactionInterface;

interface TransactionInterface extends DrawableTransactionInterface, RootTransactionInterface
{
}
