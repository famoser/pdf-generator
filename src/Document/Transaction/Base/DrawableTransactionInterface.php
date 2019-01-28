<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Transaction\Base;

interface DrawableTransactionInterface
{
    /**
     * register a callable which prints directly to the document before the real print is started.
     * at the end of the callable, reset the layout to the state before the invocation to ensure the layout works as expected.
     *
     * @param callable $callable the arguments are decided by the transaction implementation. At least the document(s) to print to should be included.
     */
    public function registerDrawablePrePrint(callable $callable);

    /**
     * register a callable which prints directly to the document after the real print has ended.
     * at the end of the callable, reset the layout to the state before the invocation to ensure the layout works as expected.
     *
     * @param callable $callable the arguments are decided by the transaction implementation. At least the document(s) to print to should be included.
     */
    public function registerDrawablePostPrint(callable $callable);
}
