<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document\Layout\Base;

use App\Service\Report\Document\Transaction\TransactionInterface;

interface RootLayoutInterface
{
    /**
     * will produce a transaction with the to-be-printed document.
     *
     * @return TransactionInterface
     */
    public function getTransaction();
}
