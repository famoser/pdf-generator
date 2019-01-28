<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Transaction;

use PdfGenerator\Transaction\Base\DrawableTransactionInterface;
use PdfGenerator\Transaction\Base\RootTransactionInterface;

interface TransactionInterface extends DrawableTransactionInterface, RootTransactionInterface
{
}
