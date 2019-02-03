<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator\Layout;

use DocumentGenerator\Layout\Base\RootLayoutInterface;

interface TableLayoutInterface extends RootLayoutInterface
{
    /**
     * @return TableRowLayoutInterface
     */
    public function startNewRow();
}
