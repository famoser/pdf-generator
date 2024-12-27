<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\DocumentGenerator;

use Famoser\DocumentGenerator\Layout\AutoColumnLayoutInterface;
use Famoser\DocumentGenerator\Layout\Configuration\ColumnConfiguration;
use Famoser\DocumentGenerator\Layout\TableLayoutInterface;

interface LayoutFactoryInterface
{
    /**
     * starts a region with columns and the column is chosen automatically.
     */
    public function createAutoColumnLayout(int $columnCount): AutoColumnLayoutInterface;

    /**
     * starts a table.
     *
     * @param ColumnConfiguration[] $tableColumns
     */
    public function createTableLayout(array $tableColumns): TableLayoutInterface;
}
