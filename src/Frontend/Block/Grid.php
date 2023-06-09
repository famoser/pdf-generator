<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Grid\GridEntry;
use PdfGenerator\Frontend\Block\Style\GridStyle;

class Grid extends Block
{
    private GridStyle $style;

    /**
     * @var GridEntry[][]
     */
    private array $gridEntries = [];

    /**
     * @param float[]|null $dimensions
     */
    public function __construct(GridStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style ?? new GridStyle();
    }

    public function addBlock(int $columnIndex, int $rowIndex, Block $block, int $columnSpan = 1, int $rowSpan = 1): void
    {
        while (\count($this->gridEntries) <= $columnIndex) {
            $this->gridEntries[] = [];
        }
        while (\count($this->gridEntries[$columnIndex]) <= $rowIndex) {
            $this->gridEntries[$columnIndex] = [];
        }

        $gridEntry = new GridEntry($columnSpan, $rowSpan, $block);
        $this->gridEntries[$columnIndex][$rowIndex] = $gridEntry;
    }

    public function getStyle(): GridStyle
    {
        return $this->style;
    }
}
