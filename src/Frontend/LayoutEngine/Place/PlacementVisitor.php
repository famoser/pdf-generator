<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Place;

use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;

/**
 * This places content on the PDF.
 *
 * Importantly, the placement guarantees progress (i.e. with each call, less to-be-placed content remains).
 * For this guarantee, boundaries might be disrespected (e.g. content wider than the maxWidth is placed).
 *
 * @implements AbstractBlockVisitor<Placement>
 */
class PlacementVisitor extends AbstractBlockVisitor
{
    public function __construct(private Printer $pagePrinter, private float $width, private float $height)
    {
    }

    public function visitRectangle(Content\Rectangle $rectangle): Placement
    {
        $rectangleStyle = self::createRectangleStyle($rectangle->getStyle());
        $placement = new Placement($rectangle->getWidth(), $rectangle->getHeight());
        [$finalPlacement, $positionedPrinter] = $this->placeBlock($placement, $rectangle);

        $positionedPrinter->printRectangle($rectangle->getWidth(), $rectangle->getHeight(), $rectangleStyle);

        return $finalPlacement;
    }

    private static function createRectangleStyle(Content\Style\DrawingStyle $drawingStyle): RectangleStyle
    {
        return new RectangleStyle($drawingStyle->getLineWidth(), $drawingStyle->getLineColor(), $drawingStyle->getFillColor());
    }

    private function placeBlock(Placement $placement, BaseBlock $block): array
    {
        $blockStyle = $block->getStyle();
        $hasBorder = $blockStyle->getBorderWidth() && $blockStyle->getBorderColor();
        if ($hasBorder || $blockStyle->getBackgroundColor()) {
            $rectangleStyle = new RectangleStyle($blockStyle->getBorderWidth() ?? 0, $blockStyle->getBorderColor(), $blockStyle->getBackgroundColor());
            $blockPrinter = $this->pagePrinter->position($block->getLeftMargin(), $block->getTopMargin());
            $blockWidth = $placement->getWidth() + $block->getXPadding();
            $blockHeight = $placement->getHeight() + $block->getYPadding();
            $blockPrinter->printRectangle($blockWidth, $blockHeight, $rectangleStyle);
        }

        $totalWidth = $placement->getWidth() + $block->getXSpace();
        $totalHeight = $placement->getHeight() + $block->getYSpace();
        $finalPlacement = new Placement($totalWidth, $totalHeight, $placement->getOverflow());

        $positionedPrinter = $this->pagePrinter->position($block->getLeftSpace(), $block->getTopSpace());

        return [$finalPlacement, $positionedPrinter];
    }
}
