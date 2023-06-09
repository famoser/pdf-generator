<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator\Layout\Base;

use DocumentGenerator\Transaction\TransactionInterface;

interface RootLayoutInterface
{
    /**
     * will produce a transaction with the to-be-printed document.
     */
    public function getTransaction(): TransactionInterface;
}
