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
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
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

    public function visitBlock(Block $block): BlockPlacement
    {
        [$contentPrinter, $contentWidth, $contentHeight] = $this->positionBlock($block);

        $blockVisitor = new BlockPlacementVisitor($contentPrinter, $contentWidth, $contentHeight);
        /** @var ContentPlacement $contentPlacement */
        $contentPlacement = $block->getBlock()->accept($blockVisitor);

        return BlockPlacement::create($block, $contentWidth, $contentHeight, $contentPlacement->getOverflow());
    }

    public function visitFlow(Flow $flow): BlockPlacement
    {
        /** @var Printer $contentPrinter */
        [$contentPrinter, $contentWidth, $contentHeight] = $this->positionBlock($flow);
        $usedWidth = 0;
        $usedHeight = 0;
        /** @var AbstractBlock[] $overflowBlocks */
        $overflowBlocks = [];
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $block = $flow->getBlocks()[$i];

            // check if enough space available
            $availableWidth = $contentWidth - $usedWidth;
            $availableHeight = $contentHeight - $usedHeight;
            $necessaryWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $flow->getDimension($i) : null;
            $necessaryHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $flow->getDimension($i) : null;
            if (($availableWidth < $necessaryWidth || $availableHeight < $necessaryHeight) && $i > 0) {
                $overflowBlocks = [...array_slice($flow->getBlocks(), $i)];
                break;
            }

            // get placement of child
            $providedWeight = $necessaryWidth ?? $availableWidth;
            $providedHeight = $necessaryHeight ?? $availableHeight;
            $blockPlacementVisitor = new BlockPlacementVisitor($contentPrinter, $providedWeight, $providedHeight);
            /** @var BlockPlacement $placement */
            $placement = $block->accept($blockPlacementVisitor);

            if ($placement->getOverflow()) {
                $overflowBlocks = [$placement->getOverflow(), ...array_slice($flow->getBlocks(), $i + 1)];
                break;
            }

            // update printer
            if (Flow::DIRECTION_ROW === $flow->getDirection()) {
                $leftAdvance = $placement->getWidth() + $flow->getGap();
                $usedWidth += $leftAdvance;
                $contentPrinter = $contentPrinter->position($leftAdvance, 0);
            } else {
                $topAdvance = $placement->getHeight() + $flow->getGap();
                $usedHeight += $topAdvance;
                $contentPrinter = $contentPrinter->position(0, $topAdvance);
            }
        }

        $overflowBlock = count($overflowBlocks) > 0 ? $flow->cloneWithBlocks($overflowBlocks) : null;

        return BlockPlacement::create($flow, $contentWidth, $contentHeight, $overflowBlock);
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
