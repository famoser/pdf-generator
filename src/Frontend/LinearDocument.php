<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

use DocumentGenerator\DocumentInterface;
use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use PdfGenerator\Frontend\LayoutEngine\Allocate\AllocationVisitor;
use PdfGenerator\Frontend\LayoutEngine\Place\Placement;
use PdfGenerator\Frontend\LayoutEngine\Place\PlacementVisitor;
use PdfGenerator\IR\Document\Page;

class LinearDocument implements DocumentInterface
{
    private readonly \PdfGenerator\IR\Document $document;

    public Page $currentPage;
    public float $currentY = 0;

    public function __construct(private array $pageSize = [210, 297], private array $margin = [35, 35, 35, 35])
    {
        $this->document = new \PdfGenerator\IR\Document();
        $this->addPage();
    }

    public function add(BaseBlock $block): void
    {
        $allocation = $this->allocate($block);

        // auto-advance page
        if (!$allocation->getContent() && $this->currentY > 0) {
            $this->addPage();
        }

        $lastPlacement = $this->place($block);
        $this->currentY += $lastPlacement->getHeight();

        while ($lastPlacement->getOverflow()) {
            $this->addPage();
            $lastPlacement = $this->place($lastPlacement->getOverflow());
            $this->currentY += $lastPlacement->getHeight();
        }
    }

    public function allocate(BaseBlock $block): Allocation
    {
        [$width, $height] = $this->getPrintingArea();
        $allocationVisitor = new AllocationVisitor($width, $height);

        return $block->accept($allocationVisitor);
    }

    public function place(BaseBlock $block): Placement
    {
        $left = $this->margin[3];
        $top = $this->currentY + $this->margin[0];
        [$width, $height] = $this->getPrintingArea();
        $pagePrinter = new Printer($this->document, $this->currentPage, $left, $top);
        $placementVisitor = new PlacementVisitor($pagePrinter, $width, $height);

        return $block->accept($placementVisitor);
    }

    /**
     * @return float[]
     */
    public function getPrintingArea(): array
    {
        $heightMargin = $this->margin[0] + $this->margin[2];
        $widthMargin = $this->margin[1] + $this->margin[3];
        $height = $this->pageSize[1] - $this->currentY - $heightMargin;
        $width = $this->pageSize[0] - $widthMargin;

        return [$width, $height];
    }

    public function addPage(): void
    {
        $page = new Page(count($this->document->getPages()) + 1, $this->pageSize);
        $this->document->addPage($page);

        $this->currentY = 0;
        $this->currentPage = $page;
    }

    public function save(): string
    {
        return $this->document->save();
    }
}
