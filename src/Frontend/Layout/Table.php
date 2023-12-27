<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Layout\Traits\ColumnStylesTrait;
use PdfGenerator\Frontend\Layout\Traits\HeadersTrait;
use PdfGenerator\Frontend\Layout\Traits\RowsTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Table extends AbstractBlock
{
    use RowsTrait;
    use ColumnStylesTrait;
    use HeadersTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitTable($this);
    }
}
