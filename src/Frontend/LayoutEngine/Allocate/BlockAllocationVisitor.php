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

use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\FlowAllocator;
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\GridAllocator;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @extends AbstractBlockVisitor<BlockAllocation>
 */
class BlockAllocationVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly float $maxWidth, private readonly float $maxHeight)
    {
    }

    public function visitContentBlock(ContentBlock $contentBlock): BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($contentBlock);

        $contentAllocationVisitor = new ContentAllocationVisitor(...$usableSpace);
        $contentAllocation = $contentBlock->getContent()->accept($contentAllocationVisitor); /** @var ContentAllocation $contentAllocation */
        $overflow = $contentAllocation->getOverflow() ? $contentBlock->cloneWithContent($contentAllocation->getOverflow()) : null;

        return $this->allocateBlock($contentBlock, $contentAllocation->getWidth(), $contentAllocation->getHeight(), [], [$contentAllocation], $overflow);
    }

    public function visitBlock(Block $block): ?BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($block);

        $blockAllocationVisitor = new BlockAllocationVisitor(...$usableSpace);
        $blockAllocation = $block->getBlock()->accept($blockAllocationVisitor); /** @var BlockAllocation $blockAllocation */
        $overflow = $blockAllocation->getOverflow() ? $block->cloneWithBlock($blockAllocation->getOverflow()) : null;

        return $this->allocateBlock($block, $blockAllocation->getWidth(), $blockAllocation->getHeight(), [$blockAllocation], [], $overflow);
    }

    public function visitFlow(Flow $flow): ?BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($flow);

        $allocator = new FlowAllocator(...$usableSpace);
        $usedWidth = 0;
        $usedHeight = 0;
        $overflowBlocks = [];
        $allocatedBlocks = $allocator->allocate($flow, $overflowBlocks, $usedWidth, $usedHeight);
        assert(count($allocatedBlocks) > 0);

        $overflow = count($overflowBlocks) > 0 ? $flow->cloneWithBlocks($overflowBlocks) : null;

        return $this->allocateBlock($flow, $usedWidth, $usedHeight, $allocatedBlocks, [], $overflow);
    }

    public function visitGrid(Grid $grid): BlockAllocation
    {
        $usableSpace = $this->getUsableSpace($grid);

        $allocator = new GridAllocator(...$usableSpace);
        $usedWidth = 0;
        $usedHeight = 0;
        $overflowRows = [];
        $allocatedBlocks = $allocator->allocate($grid, $overflowRows, $usedWidth, $usedHeight);
        assert(count($allocatedBlocks) > 0);

        $overflow = count($overflowRows) > 0 ? $grid->cloneWithRows($overflowRows) : null;

        return $this->allocateBlock($grid, $usedWidth, $usedHeight, $allocatedBlocks, [], $overflow);
    }

    private function getUsableSpace(AbstractBlock $block): array
    {
        $availableMaxWidth = $this->maxWidth - $block->getXMargin();
        $availableMaxHeight = $this->maxHeight - $block->getYMargin();

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
        $allocationOverflows = $width > $this->maxWidth || $height > $this->maxHeight;

        return new BlockAllocation($block->getLeftSpace(), $block->getTopSpace(), $width, $height, $blockAllocations, $contentAllocations, $allocationOverflows, $overflow);
    }

    private function allocateBackground(AbstractBlock $block, float $contentWidth, float $contentHeight): ?ContentAllocation
    {
        // print block background
        $blockStyle = $block->getStyle();
        if (!$blockStyle) {
            return null;
        }

        if (!$blockStyle->hasImpact()) {
            return null;
        }

        $width = $contentWidth + $block->getXPadding();
        $height = $contentHeight + $block->getYPadding();

        return ContentAllocation::createFromBlockStyle($width, $height, $blockStyle);
    }
}
