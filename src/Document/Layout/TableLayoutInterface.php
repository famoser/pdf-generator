<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Layout;

use PdfGenerator\Document\Layout\Base\RootLayoutInterface;

interface TableLayoutInterface extends RootLayoutInterface
{
    /**
     * @return TableRowLayoutInterface
     */
    public function startNewRow();

    /**
     * @param callable $callable
     */
    public function setOnRowCommit(callable $callable): void;
}
