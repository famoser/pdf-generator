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

use PdfGenerator\Frontend\Position;
use PdfGenerator\Frontend\Size;

class ContentAreaGenerator implements ContentAreaGeneratorInterface
{
    /**
     * @param float[] $margins
     */
    public function __construct(private array $margins = [25, 25, 25, 25], private float $gutter = 5, private int $columnCount = 1, private int $rowCount = 1)
    {
    }

    /**
     * @param float[] $pageDimensions
     *
     * @return ContentArea[]
     */
    public function getContentAreas(array $pageDimensions, int $pageIndex): array
    {
        $startLeft = $this->margins[3];
        $startTop = $this->margins[0];
        $width = $pageDimensions[0] - $startLeft - $this->margins[1];
        $height = $pageDimensions[1] - $startTop - $this->margins[2];

        $columnWidth = ($width - $this->gutter * ($this->columnCount - 1)) / $this->columnCount;
        $rowHeight = ($height - $this->gutter * ($this->rowCount - 1)) / $this->rowCount;

        /** @var ContentArea[] $contentAreas */
        $contentAreas = [];
        for ($j = 0; $j < $this->columnCount; ++$j) {
            for ($i = 0; $i < $this->rowCount; ++$i) {
                $left = $startLeft + $i * ($columnWidth + $this->gutter);
                $top = $startTop + $j * ($rowHeight + $this->gutter);
                $start = new Position($left, $top);

                $size = new Size($columnWidth, $rowHeight);

                $contentAreas[] = new ContentArea($start, $size);
            }
        }

        return $contentAreas;
    }
}
