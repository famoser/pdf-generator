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
    public function __construct(private readonly ?float $maxWidth, private readonly ?float $maxHeight)
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
        for ($i = 0; $i < count($flow->getBlocks()); ++$i) {
            $block = $flow->getBlocks()[$i];

            // TODO; get width out of dimension array check whether enough space or abort
            $availableWidth = Flow::DIRECTION_ROW === $flow->getDirection() && $flow->getDimensions() ? $flow->getDimensions() : null;

            $allocationVisitor = new AllocationVisitor($availableMaxWidth, $availableMaxHeight);
            $allocation = $block->accept($allocationVisitor);

            // TODO: update available width vars
        }
    }

    private function getAvailableSpace(BaseBlock $block): array
    {
        $maxWidth = $block->getWidth() ?? $this->maxWidth;
        $maxHeight = $block->getHeight() ?? $this->maxHeight;

        $availableMaxWidth = $maxWidth ? $maxWidth - $block->getXSpace() : null;
        $availableMaxHeight = $maxHeight ? $maxHeight - $block->getYSpace() : null;

        return [$availableMaxWidth, $availableMaxHeight];
    }

    private function allocateBlock(Allocation $allocation, BaseBlock $block): Allocation
    {
        $totalWidth = $allocation->getWidth() + $block->getXSpace();
        $totalHeight = $allocation->getHeight() + $block->getYSpace();

        $tooWide = $this->maxWidth && $this->maxWidth <= $totalWidth;
        $tooHigh = $this->maxHeight && $this->maxHeight <= $totalHeight;
        if ($tooWide || $tooHigh) {
            return Allocation::createEmpty(true);
        }

        return new Allocation($totalWidth, $totalHeight, $block, $allocation->hasOverflow());
    }
}
