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
use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocationVisitor;
use PdfGenerator\Frontend\LayoutEngine\Place\BlockPlacement;
use PdfGenerator\Frontend\LayoutEngine\Place\BlockPlacementVisitor;
use PdfGenerator\IR\Document\Page;

class LinearDocument implements DocumentInterface
{
    private readonly \PdfGenerator\IR\Document $document;

    public Page $currentPage;
    public float $currentY = 0;

    public function __construct(private array $pageSize = [210, 297], private array $margin = [25, 25, 25, 25])
    {
        $this->document = new \PdfGenerator\IR\Document();
        $this->addPage();
    }

    public function add(AbstractBlock $block): void
    {
        do {
            $allocation = $this->allocate($block);

            // advance page if no sensible allocation
            if (!$allocation->getContent() && $this->currentY > 0) {
                $this->addPage();
                $allocation = $this->allocate($block);
            }

            var_dump($allocation);

            $lastPlacement = $this->place($allocation->getContent());
            $this->currentY += $lastPlacement->getHeight();
        } while ($block = $lastPlacement->getOverflow());
    }

    public function allocate(AbstractBlock $block): BlockAllocation
    {
        [$width, $height] = $this->getPrintingArea();
        $allocationVisitor = new BlockAllocationVisitor($width, $height);

        return $block->accept($allocationVisitor);
    }

    public function place(AbstractBlock $block): BlockPlacement
    {
        $left = $this->margin[3];
        $top = $this->currentY + $this->margin[0];
        [$width, $height] = $this->getPrintingArea();
        $pagePrinter = new Printer($this->document, $this->currentPage, $left, $top);
        $placementVisitor = new BlockPlacementVisitor($pagePrinter, $width, $height);

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
