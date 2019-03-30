<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DocumentGenerator;

use DocumentGenerator\Layout\AutoColumnLayoutInterface;
use DocumentGenerator\Layout\Configuration\ColumnConfiguration;
use DocumentGenerator\Layout\TableLayoutInterface;

interface LayoutFactoryInterface
{
    /**
     * starts a region with columns and the column is chosen automatically.
     *
     * @param int $columnCount
     *
     * @return AutoColumnLayoutInterface
     */
    public function createAutoColumnLayout(int $columnCount);

    /**
     * starts a table.
     *
     * @param ColumnConfiguration[] $tableColumns
     *
     * @return TableLayoutInterface
     */
    public function createTableLayout(array $tableColumns);
}
