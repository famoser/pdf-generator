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
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @implements AbstractBlockVisitor<Allocation|null>
 */
class AllocationVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly float $maxWidth, private readonly float $maxHeight)
    {
    }

    public function visitRectangle(Rectangle $rectangle): ?Allocation
    {
        $usableSpace = $this->getUsableSpace($rectangle);
        if (!$usableSpace) {
            return null;
        }

        $allocation = new Allocation($usableSpace[0], $usableSpace[1], $rectangle, false);

        return $this->allocateBlock($allocation, $rectangle);
    }

    public function visitBlock(Block $block): ?Allocation
    {
        $usableSpace = $this->getUsableSpace($block);
        if (!$usableSpace) {
            return null;
        }

        $allocationVisitor = new AllocationVisitor($usableSpace[0], $usableSpace[1]);
        /** @var Allocation $allocation */
        $allocation = $block->accept($allocationVisitor);

        return $this->allocateBlock($allocation, $block);
    }

    public function visitFlow(Flow $flow): Allocation
    {
        [$availableWidth, $availableHeight] = $this->getUsableSpace($flow);
        $usedWidth = 0;
        $usedHeight = 0;
        /** @var AbstractBlock[] $blocks */
        $blocks = [];
        $overflow = false;
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $allocatedFlow = $flow->getBlocks()[$i];

            // check if enough space available
            $necessaryWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $flow->getDimension($i) : null;
            $necessaryHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $flow->getDimension($i) : null;
            if ($availableWidth < $necessaryWidth || $availableHeight < $necessaryHeight) {
                $overflow = true;
                break;
            }

            // get allocation of child
            $providedWeight = $necessaryWidth ?? $availableWidth;
            $providedHeight = $necessaryHeight ?? $availableHeight;
            $allocationVisitor = new AllocationVisitor($providedWeight, $providedHeight);
            /** @var Allocation $allocation */
            $allocation = $allocatedFlow->accept($allocationVisitor);

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
        $contentAllocation = new Allocation($usedWidth, $usedHeight, $allocatedFlow, $overflow);

        return $this->allocateBlock($contentAllocation, $allocatedFlow);
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

    private function allocateBlock(Allocation $contentAllocation, AbstractBlock $content): Allocation
    {
        $width = $contentAllocation->getWidth() + $content->getXSpace();
        $height = $contentAllocation->getHeight() + $content->getYSpace();

        return new Allocation($width, $height, $content, $contentAllocation->hasOverflow());
    }
}
