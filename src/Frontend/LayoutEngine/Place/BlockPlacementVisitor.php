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

use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;

/**
 * This places content on the PDF.
 *
 * Importantly, the placement guarantees progress (i.e. with each call, less to-be-placed content remains).
 * For this guarantee, boundaries might be disrespected (e.g. content wider than the maxWidth is placed).
 *
 * @implements AbstractBlockVisitor<BlockPlacement>
 */
class BlockPlacementVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly Printer $printer, private readonly float $width, private readonly float $height)
    {
    }

    public function visitContentBlock(ContentBlock $contentBlock): BlockPlacement
    {
        [$contentPrinter, $contentWidth, $contentHeight] = $this->positionBlock($contentBlock);

        $contentVisitor = new ContentPlacementVisitor($contentPrinter, $contentWidth, $contentHeight);
        /** @var ContentPlacement $contentPlacement */
        $contentPlacement = $contentBlock->getContent()->accept($contentVisitor);
        $overflowBlock = $contentPlacement->getOverflow() ? new ContentBlock($contentPlacement->getOverflow()) : null;

        return BlockPlacement::create($contentBlock, $contentWidth, $contentHeight, $overflowBlock);
    }

    private function positionBlock(AbstractBlock $block): array
    {
        $width = $block->getWidth() ?? $this->width - $block->getXMargin();
        $height = $block->getHeight() ?? $this->height - $block->getYMargin();

        // print block background
        $blockStyle = $block->getStyle();
        $hasBorder = $blockStyle->getBorderWidth() && $blockStyle->getBorderColor();
        if ($hasBorder || $blockStyle->getBackgroundColor()) {
            $rectangleStyle = new RectangleStyle($blockStyle->getBorderWidth() ?? 0, $blockStyle->getBorderColor(), $blockStyle->getBackgroundColor());
            $blockPrinter = $this->printer->position($block->getLeftMargin(), $block->getTopMargin());
            $blockPrinter->printRectangle($width, $height, $rectangleStyle);
        }

        $contentPrinter = $this->printer->position($block->getLeftSpace(), $block->getTopSpace());
        $contentWidth = $width - $block->getXPadding();
        $contentHeight = $height - $block->getYPadding();

        return [$contentPrinter, $contentWidth, $contentHeight];
    }
}
