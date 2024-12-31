<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Grid;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\FlowAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\GridAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\TableAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators\TextAllocator;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @implements ElementVisitorInterface<Allocation>
 */
readonly class AllocationVisitor implements ElementVisitorInterface
{
    public function __construct(private float $maxWidth, private float $maxHeight)
    {
    }

    public function visitBlock(Block $block): Allocation
    {
        $usableSpace = $this->getUsableSpace($block);

        $blockAllocationVisitor = new AllocationVisitor(...$usableSpace);
        $blockAllocation = $block->getBlock()->accept($blockAllocationVisitor);
        $overflow = $blockAllocation->getOverflow() ? $block->cloneWithBlock($blockAllocation->getOverflow()) : null;

        return $this->allocateBlock($block, $blockAllocation->getWidth(), $blockAllocation->getHeight(), [$blockAllocation], [], $overflow);
    }

    public function visitFlow(Flow $flow): ?Allocation
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

    public function visitGrid(Grid $grid): Allocation
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

    public function visitTable(Table $table)
    {
        $usableSpace = $this->getUsableSpace($table);

        $allocator = new TableAllocator(...$usableSpace);
        $usedWidth = 0;
        $usedHeight = 0;
        $overflowBody = [];
        $allocatedBlocks = $allocator->allocate($table, $overflowBody, $usedWidth, $usedHeight);
        assert(count($allocatedBlocks) > 0);

        $overflow = count($overflowBody) > 0 ? $table->cloneWithBody($overflowBody) : null;

        return $this->allocateBlock($table, $usedWidth, $usedHeight, $allocatedBlocks, [], $overflow);
    }

    public function visitText(Text $text)
    {
        $usableSpace = $this->getUsableSpace($text);

        $textAllocator = new TextAllocator(...$usableSpace);
        $usedWidth = 0;
        $usedHeight = 0;
        $overflowSpans = [];
        $content = $textAllocator->allocate($text, $overflowSpans, $usedWidth, $usedHeight);

        $overflow = count($overflowSpans) > 0 ? $text->cloneWithSpans($overflowSpans) : null;

        return $this->allocateBlock($text, $usedWidth, $usedHeight, [], [$content], $overflow);
    }

    public function visitContentBlock(ContentBlock $contentBlock): Allocation
    {
        $content = $contentBlock->getContent();

        if (!$content) {
            return $this->allocateBlock($contentBlock);
        }

        return $this->allocateBlock($contentBlock, $content->getWidth(), $content->getHeight(), [], [$content]);
    }

    /**
     * @return array{float, float}
     */
    private function getUsableSpace(AbstractElement $block): array
    {
        $availableMaxWidth = $this->maxWidth - $block->getXMargin();
        $availableMaxHeight = $this->maxHeight - $block->getYMargin();

        $usableWidth = ($block->getWidth() ?? $availableMaxWidth) - $block->getXPadding();
        $usableHeight = ($block->getHeight() ?? $availableMaxHeight) - $block->getYPadding();

        return [$usableWidth, $usableHeight];
    }

    /**
     * @param Allocation[]   $blockAllocations
     * @param AbstractContent[] $content
     */
    private function allocateBlock(AbstractElement $block, float $contentWidth = 0.0, float $contentHeight = 0.0, array $blockAllocations = [], array $content = [], ?AbstractElement $overflow = null): Allocation
    {
        $background = $this->allocateBackground($block, $contentWidth, $contentHeight);
        if ($background) {
            $backgroundAllocation = new Allocation(-$block->getLeftPadding(), -$block->getTopPadding(), $background->getWidth(), $background->getHeight(), [], [$background]);
            array_unshift($blockAllocations, $backgroundAllocation);
        }

        $width = $block->getWidth() ? $block->getWidth() + $block->getXMargin() : $contentWidth + $block->getXSpace();
        $height = $block->getHeight() ? $block->getHeight() + $block->getYMargin() : $contentHeight + $block->getYSpace();
        $allocationOverflows = $width > $this->maxWidth || $height > $this->maxHeight;

        return new Allocation($block->getLeftSpace(), $block->getTopSpace(), $width, $height, $blockAllocations, $content, $allocationOverflows, $overflow);
    }

    private function allocateBackground(AbstractElement $block, float $contentWidth, float $contentHeight): ?AbstractContent
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

        $drawingStyle = DrawingStyle::createFromBlockStyle($blockStyle);
        return new Rectangle($width, $height, $drawingStyle);
    }
}
