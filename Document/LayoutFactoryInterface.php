<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Report\Document;

use App\Service\Report\Document\Layout\AutoColumnLayoutInterface;
use App\Service\Report\Document\Layout\ColumnLayoutInterface;
use App\Service\Report\Document\Layout\Configuration\ColumnConfiguration;
use App\Service\Report\Document\Layout\FullWidthLayoutInterface;
use App\Service\Report\Document\Layout\GroupLayoutInterface;
use App\Service\Report\Document\Layout\TableLayoutInterface;

interface LayoutFactoryInterface
{
    /**
     * starts a region with 100% width.
     *
     * @return FullWidthLayoutInterface
     */
    public function createFullWidthLayout();

    /**
     * will avoid a page break between the next printed elements
     * will add a page break before all elements if they do not fit on the same page
     * active until end region is called.
     *
     * @return GroupLayoutInterface
     */
    public function createGroupLayout();

    /**
     * starts a region with columns.
     *
     * @param int $columnCount
     *
     * @return ColumnLayoutInterface
     */
    public function createColumnLayout(int $columnCount);

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
