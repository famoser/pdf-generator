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

    public function visitContentBlock(ContentBlock $contentBlock): BlockAllocation
    {
        [$usableWidth, $usableHeight] = $this->getUsableSpace($contentBlock);
        if (!$usableWidth || !$usableHeight) {
            return BlockAllocation::createEmpty(true);
        }

        $contentAllocationVisitor = new ContentAllocationVisitor($usableWidth, $usableHeight);
        /** @var ContentAllocation $allocation */
        $allocation = $contentBlock->getContent()->accept($contentAllocationVisitor);

        $content = $allocation->getContent() ? $contentBlock->cloneWithContent($allocation->getContent()) : null;

        return BlockAllocation::create($contentBlock, $allocation->getWidth(), $allocation->getHeight(), $content, $allocation->hasOverflow());
    }

    public function visitBlock(Block $block): BlockAllocation
    {
        [$usableWidth, $usableHeight] = $this->getUsableSpace($block);
        if (!$usableWidth || !$usableHeight) {
            return BlockAllocation::createEmpty(true);
        }

        $blockAllocationVisitor = new BlockAllocationVisitor($usableWidth, $usableHeight);
        /** @var BlockAllocation $allocation */
        $allocation = $block->getBlock()->accept($blockAllocationVisitor);

        $content = $allocation->getContent() ? $block->cloneWithBlock($allocation->getContent()) : null;

        return BlockAllocation::create($block, $allocation->getWidth(), $allocation->getHeight(), $content, $allocation->hasOverflow());
    }

    public function visitFlow(Flow $flow): BlockAllocation
    {
        [$usableWidth, $usableHeight] = $this->getUsableSpace($flow);
        if (!$usableWidth || !$usableHeight) {
            return BlockAllocation::createEmpty(true);
        }

        $usedWidth = 0;
        $usedHeight = 0;
        /** @var AbstractBlock[] $blocks */
        $blocks = [];
        $overflow = false;
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $block = $flow->getBlocks()[$i];

            // check if enough space available
            $availableWidth = $usableWidth - $usedWidth;
            $availableHeight = $usableHeight - $usedHeight;
            $necessaryWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $flow->getDimension($i) : null;
            $necessaryHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $flow->getDimension($i) : null;
            if ($availableWidth < (int) $necessaryWidth || $availableHeight < (int) $necessaryHeight) {
                $overflow = true;
                break;
            }

            // get allocation of child
            $providedWeight = $necessaryWidth ?? $usableWidth;
            $providedHeight = $necessaryHeight ?? $usableHeight;
            $allocationVisitor = new BlockAllocationVisitor($providedWeight, $providedHeight);
            /** @var BlockAllocation $allocation */
            $allocation = $block->accept($allocationVisitor);
            if (!$allocation->getContent()) {
                $overflow = true;
                break;
            }

            // update allocated content
            $blocks[] = $allocation->getContent();
            if (Flow::DIRECTION_ROW === $flow->getDirection()) {
                $usedHeight = max($usedHeight, $allocation->getHeight());
                $usedWidth += $allocation->getWidth() + $flow->getGap();
            } else {
                $usedHeight += $allocation->getHeight() + $flow->getGap();
                $usedWidth = max($usedWidth, $allocation->getWidth());
            }

            // abort if child overflowed
            if ($allocation->hasOverflow()) {
                $overflow = true;
                break;
            }
        }

        // remove gap from last iteration
        if (Flow::DIRECTION_ROW === $flow->getDirection()) {
            $usedWidth -= $flow->getGap();
        } else {
            $usedHeight -= $flow->getGap();
        }

        $allocatedFlow = $flow->cloneWithBlocks($blocks);

        return BlockAllocation::create($flow, $usedWidth, $usedHeight, $allocatedFlow, $overflow);
    }

    private function getUsableSpace(AbstractBlock $block): ?array
    {
        $availableMaxWidth = $this->maxWidth - $block->getXMargin();
        $availableMaxHeight = $this->maxHeight - $block->getYMargin();

        $tooWide = $block->getWidth() && $availableMaxWidth < $block->getWidth();
        $tooHigh = $block->getHeight() && $availableMaxHeight < $block->getHeight();
        if ($tooWide || $tooHigh) {
            return [null, null];
        }

        $usableWidth = ($block->getWidth() ?? $availableMaxWidth) - $block->getXPadding();
        $usableHeight = ($block->getHeight() ?? $availableMaxHeight) - $block->getYPadding();

        return [$usableWidth, $usableHeight];
    }
}
