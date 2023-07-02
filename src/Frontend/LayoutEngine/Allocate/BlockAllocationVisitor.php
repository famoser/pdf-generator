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
use PdfGenerator\Frontend\Layout\Style\BlockSize;
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
    public function __construct(private readonly float $maxWidth, private readonly float $maxHeight)
    {
    }

    public function visitContentBlock(ContentBlock $contentBlock): ?BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($contentBlock);
        if (!$usableSpace) {
            return null;
        }

        $contentAllocationVisitor = new ContentAllocationVisitor(...$usableSpace);
        /** @var ContentAllocation|null $contentAllocation */
        $contentAllocation = $contentBlock->getContent()->accept($contentAllocationVisitor);
        if (!$contentAllocation) {
            return null;
        }

        $overflow = $contentAllocation->getOverflow() ? $contentBlock->cloneWithContent($contentAllocation->getOverflow()) : null;

        $content = $contentAllocation->getWidth() > 0 && $contentAllocation->getHeight() > 0 ? [$contentAllocation] : [];
        if (0 == count($content)) {
            var_dump('empty content block');
        }

        return $this->allocateBlock($contentBlock, $contentAllocation->getWidth(), $contentAllocation->getHeight(), [], $content, $overflow);
    }

    public function visitBlock(Block $block): ?BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($block);
        if (!$usableSpace) {
            return null;
        }

        $blockAllocationVisitor = new BlockAllocationVisitor(...$usableSpace);
        $blockAllocation = $block->getBlock()->accept($blockAllocationVisitor);
        if (!$blockAllocation) {
            return null;
        }

        $overflow = $blockAllocation->getOverflow() ? $block->cloneWithBlock($blockAllocation->getOverflow()) : null;

        return $this->allocateBlock($block, $blockAllocation->getWidth(), $blockAllocation->getHeight(), [$blockAllocation], [], $overflow);
    }

    public function visitFlow(Flow $flow): ?BlockAllocation
    {
        // TODO: Consider removing n:n (many content allocations, many block allocations), as probably not needed
        $usableSpace = $this->getUsableSpace($flow);
        if (!$usableSpace) {
            return null;
        }

        [$usableWidth, $usableHeight] = $usableSpace;
        $usedWidth = 0;
        $usedHeight = 0;
        /** @var BlockAllocation[] $blockAllocations */
        $blockAllocations = [];
        /** @var AbstractBlock[] $pendingBlocks */
        $pendingBlocks = $flow->getBlocks();
        while (count($pendingBlocks) > 0) {
            $block = $pendingBlocks[0];

            // get allocation of child
            $availableWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $usableWidth - $usedWidth : $usableWidth;
            $availableHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $usableHeight - $usedHeight : $usableHeight;
            $allocationVisitor = new BlockAllocationVisitor($availableWidth, $availableHeight);
            /** @var BlockAllocation $allocation */
            $allocation = $block->accept($allocationVisitor);
            if (!$allocation) {
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

            if ($allocation->getOverflow()) {
                $pendingBlocks[0] = $allocation->getOverflow();
            } else {
                array_shift($pendingBlocks);
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

        $overflow = count($pendingBlocks) > 0 ? $flow->cloneWithBlocks($pendingBlocks) : null;

        return $this->allocateBlock($flow, $usedWidth, $usedHeight, $blockAllocations, [], $overflow);
    }

    private function getUsableSpace(AbstractBlock $block): ?array
    {
        $availableMaxWidth = $this->maxWidth - $block->getXMargin();
        $availableMaxHeight = $this->maxHeight - $block->getYMargin();

        $tooWide = $block->getWidth() && $availableMaxWidth < $block->getWidth();
        $tooHigh = $block->getHeight() && $availableMaxHeight < $block->getHeight();
        if ($tooWide || $tooHigh) {
            return null;
        }

        $usableWidth = ($block->getWidth() ?? $availableMaxWidth) - $block->getXPadding();
        $usableHeight = ($block->getHeight() ?? $availableMaxHeight) - $block->getYPadding();

        return [$usableWidth, $usableHeight];
    }

    /**
     * @param BlockAllocation[]   $blockAllocations
     * @param ContentAllocation[] $contentAllocations
     */
    private function allocateBlock(AbstractBlock $block, float $contentWidth, float $contentHeight, array $blockAllocations = [], array $contentAllocations = [], AbstractBlock $overflow = null): BlockAllocation
    {
        $background = $this->allocateBackground($block, $contentWidth, $contentHeight);
        if ($background) {
            $backgroundAllocation = new BlockAllocation(-$block->getLeftPadding(), -$block->getTopPadding(), $background->getWidth(), $background->getHeight(), [], [$background]);
            array_unshift($blockAllocations, $backgroundAllocation);
        }

        $width = $block->getWidth() ? $block->getWidth() + $block->getXMargin() : $contentWidth + $block->getXSpace();
        $height = $block->getHeight() ? $block->getHeight() + $block->getYMargin() : $contentHeight + $block->getYSpace();

        return new BlockAllocation($block->getLeftSpace(), $block->getTopSpace(), $width, $height, $blockAllocations, $contentAllocations, $overflow);
    }

    private function allocateBackground(AbstractBlock $block, float $contentWidth, float $contentHeight): ?ContentAllocation
    {
        // print block background
        $blockStyle = $block->getStyle();
        $hasBorder = $blockStyle->getBorderWidth() && $blockStyle->getBorderColor();
        if (!$hasBorder && !$blockStyle->getBackgroundColor()) {
            return null;
        }

        $drawingStyle = new DrawingStyle($blockStyle->getBorderWidth() ?? 0, $blockStyle->getBorderColor(), $blockStyle->getBackgroundColor());
        $rectangle = new Rectangle($drawingStyle);

        if (BlockSize::INNER === $blockStyle->getBlockSize()) {
            $width = $contentWidth + $block->getXPadding();
            $height = $contentHeight + $block->getYPadding();
        } else {
            $width = $this->maxWidth - $block->getXMargin();
            $height = $this->maxHeight - $block->getYMargin();
        }

        return new ContentAllocation($width, $height, $rectangle);
    }
}
