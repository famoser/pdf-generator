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

use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\Layout\Content\Rectangle;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * This allocates content on the PDF.
 *
 * Importantly, the printer guarantees progress (i.e. with each print call, less to-be-printed content remains).
 * For this guarantee, the printer is allowed to disrespect boundaries (e.g. print content wider than the maxWidth).
 *
 * @implements AbstractBlockVisitor<Allocation>
 */
class AllocationVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly float $maxWidth, private readonly float $maxHeight)
    {
    }

    public function visitRectangle(Rectangle $rectangle): Allocation
    {
        $allocation = new Allocation($rectangle->getWidth(), $rectangle->getHeight(), $rectangle, false);

        return $this->allocateBlock($allocation, $rectangle);
    }

    public function visitFlow(Flow $flow): Allocation
    {
        [$availableMaxWidth, $availableMaxHeight] = $this->getAvailableSpace($flow);
        $usedWidth = 0;
        $usedHeight = 0;
        /** @var BaseBlock[] $blocks */
        $blocks = [];
        $overflow = true;
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $block = $flow->getBlocks()[$i];

            $necessaryWidth = Flow::DIRECTION_ROW === $flow->getDirection() ? $flow->getDimension($i) : null;
            $necessaryHeight = Flow::DIRECTION_COLUMN === $flow->getDirection() ? $flow->getDimension($i) : null;
            if ($availableMaxWidth < $necessaryWidth || $availableMaxHeight < $necessaryHeight) {
                break;
            }

            $providedWeight = $necessaryWidth ?? $availableMaxWidth;
            $providedHeight = $necessaryHeight ?? $availableMaxHeight;
            $allocationVisitor = new AllocationVisitor($providedWeight, $providedHeight);
            /** @var Allocation $allocation */
            $allocation = $block->accept($allocationVisitor);

            $blocks[] = $allocation->getContent();
            if (Flow::DIRECTION_ROW === $flow->getDirection()) {
                $usedHeight = max($usedHeight, $allocation->getHeight());
                $usedWidth += $allocation->getWidth() + $flow->getGap();
            } else {
                $usedHeight += $allocation->getHeight() + $flow->getGap();
                $usedWidth = max($usedWidth, $allocation->getWidth());
            }

            if ($allocation->hasOverflow()) {
                break;
            }

            if ($i + 1 === count($flow->getBlocks())) {
                $overflow = false;
            }
        }

        // remove gap again
        if (Flow::DIRECTION_ROW === $flow->getDirection()) {
            $usedWidth -= count($blocks) > 0 ? $flow->getGap() : 0;
        } else {
            $usedHeight -= count($blocks) > 0 ? $flow->getGap() : 0;
        }

        $block = $flow->cloneWithBlocks($blocks);

        return new Allocation($usedWidth, $usedHeight, $block, $overflow);
    }

    private function getAvailableSpace(BaseBlock $block): array
    {
        $availableMaxWidth = $this->maxWidth - $block->getXSpace();
        $availableMaxHeight = $this->maxHeight - $block->getYSpace();

        return [$availableMaxWidth, $availableMaxHeight];
    }

    private function allocateBlock(Allocation $allocation, BaseBlock $block): Allocation
    {
        $totalWidth = $allocation->getWidth() + $block->getXSpace();
        $totalHeight = $allocation->getHeight() + $block->getYSpace();

        if (($this->maxWidth <= $totalWidth) || ($this->maxHeight <= $totalHeight)) {
            return Allocation::createEmpty(true);
        }

        return new Allocation($totalWidth, $totalHeight, $block, $allocation->hasOverflow());
    }
}
