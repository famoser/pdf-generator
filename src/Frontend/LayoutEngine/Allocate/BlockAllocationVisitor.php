<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @implements AbstractBlockVisitor<BlockAllocation>
 */
class BlockAllocationVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly float $width, private readonly float $height)
    {
    }

    public function visitContentBlock(ContentBlock $contentBlock): ?BlockAllocation
    {
        [$usableWidth, $usableHeight] = $this->getUsableSpace($contentBlock);
        if (!$usableWidth || !$usableHeight) {
            return null;
        }

        $contentAllocationVisitor = new ContentAllocationVisitor($usableWidth, $usableHeight);
        $contentAllocation = $contentBlock->getContent()->accept($contentAllocationVisitor);
        if (!$contentAllocation) {
            return null;
        }

        return $this->allocateFullSizeBlock($contentBlock, [], [$contentAllocation]);
    }

    public function visitBlock(Block $block): ?BlockAllocation
    {
        [$usableWidth, $usableHeight] = $this->getUsableSpace($block);
        if (!$usableWidth || !$usableHeight) {
            return null;
        }

        $blockAllocationVisitor = new BlockAllocationVisitor($usableWidth, $usableHeight);
        $blockAllocation = $block->getBlock()->accept($blockAllocationVisitor);
        if (!$blockAllocation) {
            return null;
        }

        return $this->allocateFullSizeBlock($block, [$blockAllocation]);
    }

    public function visitFlow(Flow $flow): ?BlockAllocation
    {
        // TODO: Continue refactoring of the placement visitor
        // TODO: Consider introducing background drawing mode (to force sound architecture)
        // TODO: Consider removing n:n (many content allocations, many block allocations), as probably not needed
        [$usableWidth, $usableHeight] = $this->getUsableSpace($flow);
        if (!$usableWidth || !$usableHeight) {
            return null;
        }

        $usedWidth = 0;
        $usedHeight = 0;
        /** @var BlockAllocation[] $blockAllocations */
        $blockAllocations = [];
        /** @var AbstractBlock[] $overflowBlocks */
        $overflowBlocks = [];
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $block = $flow->getBlocks()[$i];

            // check if enough space available
            $availableWidth = $usableWidth - $usedWidth;
            $availableHeight = $usableHeight - $usedHeight;
            $necessaryWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $flow->getDimension($i) : null;
            $necessaryHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $flow->getDimension($i) : null;
            if ($availableWidth < (int) $necessaryWidth || $availableHeight < (int) $necessaryHeight) {
                $overflowBlocks = [...array_slice($flow->getBlocks(), $i)];
                break;
            }

            // get allocation of child
            $providedWeight = $necessaryWidth ?? $usableWidth;
            $providedHeight = $necessaryHeight ?? $usableHeight;
            $allocationVisitor = new BlockAllocationVisitor($providedWeight, $providedHeight);
            /** @var BlockAllocation $allocation */
            $allocation = $block->accept($allocationVisitor);
            if (!$allocation) {
                $overflowBlocks = [...array_slice($flow->getBlocks(), $i)];
                break;
            }

            // update allocated content
            if (Flow::DIRECTION_ROW === $flow->getDirection()) {
                $blockAllocations[] = new BlockAllocation($usedWidth, 0, $allocation->getWidth(), $allocation->getHeight(), [$allocation]);
                $usedHeight = max($usedHeight, $allocation->getHeight());
                $usedWidth += $allocation->getWidth() + $flow->getGap();
            } else {
                $blockAllocations[] = new BlockAllocation(0, $usedHeight, $allocation->getWidth(), $allocation->getHeight(), [$allocation]);
                $usedHeight += $allocation->getHeight() + $flow->getGap();
                $usedWidth = max($usedWidth, $allocation->getWidth());
            }

            // abort if child overflowed
            if ($allocation->getOverflow()) {
                $overflowBlocks = [$allocation->getOverflow(), ...array_slice($flow->getBlocks(), $i + 1)];
                break;
            }
        }

        if (0 === count($blockAllocations)) {
            return null;
        }

        // remove gap from last iteration
        if (Flow::DIRECTION_ROW === $flow->getDirection()) {
            $usedWidth -= $flow->getGap();
        } else {
            $usedHeight -= $flow->getGap();
        }

        $overflow = $flow->cloneWithBlocks($overflowBlocks);

        return $this->allocateFullSizeBlock($flow, $blockAllocations, [], $overflow);
    }

    private function getUsableSpace(AbstractBlock $block): ?array
    {
        $availableMaxWidth = $this->width - $block->getXMargin();
        $availableMaxHeight = $this->height - $block->getYMargin();

        $tooWide = $block->getWidth() && $availableMaxWidth < $block->getWidth();
        $tooHigh = $block->getHeight() && $availableMaxHeight < $block->getHeight();
        if ($tooWide || $tooHigh) {
            return [null, null];
        }

        $usableWidth = ($block->getWidth() ?? $availableMaxWidth) - $block->getXPadding();
        $usableHeight = ($block->getHeight() ?? $availableMaxHeight) - $block->getYPadding();

        return [$usableWidth, $usableHeight];
    }

    /**
     * @param BlockAllocation[]   $blockAllocations
     * @param ContentAllocation[] $contentAllocations
     */
    private function allocateFullSizeBlock(AbstractBlock $block, array $blockAllocations = [], array $contentAllocations = [], AbstractBlock $overflow = null): BlockAllocation
    {
        $background = $this->allocateBackground($block);
        if ($background) {
            array_unshift($contentAllocations, $background);
        }

        return new BlockAllocation($block->getLeftSpace(), $block->getTopSpace(), $this->width, $this->height, $blockAllocations, $contentAllocations, $overflow);
    }

    private function allocateBackground(AbstractBlock $block): ?ContentAllocation
    {
        // print block background
        $blockStyle = $block->getStyle();
        $hasBorder = $blockStyle->getBorderWidth() && $blockStyle->getBorderColor();
        if ($hasBorder || $blockStyle->getBackgroundColor()) {
            $drawingStyle = new DrawingStyle($blockStyle->getBorderWidth() ?? 0, $blockStyle->getBorderColor(), $blockStyle->getBackgroundColor());
            $rectangle = new Rectangle($drawingStyle);

            return new ContentAllocation($this->width, $this->height, $rectangle);
        }

        return null;
    }
}
