<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Transaction;

use App\Service\Report\Document\Transaction\Base\DrawableTransactionInterface;
use App\Service\Report\Document\Transaction\Base\RootTransactionInterface;

interface TransactionInterface extends DrawableTransactionInterface, RootTransactionInterface
{
}
