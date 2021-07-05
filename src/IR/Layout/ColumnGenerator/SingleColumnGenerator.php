<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout\ColumnGenerator;

use PdfGenerator\IR\Layout\Column;

class SingleColumnGenerator implements ColumnGenerator
{
    /**
     * @var int
     */
    private $page;

    private $pageSize;

    private $margin;

    public function getNextColumn(): Column
    {
        // TODO: Implement getNextColumn() method.
    }
}
