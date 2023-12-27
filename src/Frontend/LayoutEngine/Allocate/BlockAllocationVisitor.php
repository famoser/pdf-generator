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
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\FlowAllocator;

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

        return $this->allocateBlock($contentBlock, $contentAllocation->getWidth(), $contentAllocation->getHeight(), [], [$contentAllocation], $overflow);
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

        $allocator = new FlowAllocator(...$usableSpace);
        $usedWidth = 0;
        $usedHeight = 0;
        $overflowBlocks = [];
        $allocatedBlocks = $allocator->allocate($flow, $overflowBlocks, $usedWidth, $usedHeight);

        if (0 === count($allocatedBlocks)) {
            return null;
        }

        $overflow = count($overflowBlocks) > 0 ? $flow->cloneWithBlocks($overflowBlocks) : null;

        return $this->allocateBlock($flow, $usedWidth, $usedHeight, $allocatedBlocks, [], $overflow);
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
