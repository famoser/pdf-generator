<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator;

use PdfGenerator\Frontend\Block\Row;
use PdfGenerator\Frontend\Block\Style\TableStyle;
use PdfGenerator\Frontend\Block\Table;

class TableAllocator
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var TableStyle
     */
    private $tableStyle;

    /**
     * @var bool
     */
    private $firstTime = true;

    /**
     * TableAllocator constructor.
     */
    public function __construct(Table $table, TableStyle $tableStyle)
    {
        $this->table = $table;
        $this->tableStyle = $tableStyle;
    }

    public function allocate(float $width, float $height)
    {
        $locatedContent = [];
        $currentHeight = $height;
        if ($this->firstTime || $this->tableStyle->getRepeatHeader()) {
            foreach ($this->table->getHeaderRows() as $headerRow) {
                [$locatedContents, $currentWidth, $currentHeight] = $this->allocateRow($headerRow, $width);
            }
        }
    }

    private function allocateRow(Row $row, float $width)
    {
    }
}
