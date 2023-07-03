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
use PdfGenerator\Frontend\Resource\Font\FontRepository;
use PdfGenerator\Frontend\Resource\Image\ImageRepository;
use PdfGenerator\IR\Document\Page;

class LinearDocument implements DocumentInterface
{
    private readonly \PdfGenerator\IR\Document $document;
    private readonly ImageRepository $imageRepository;
    private readonly FontRepository $fontRepository;

    private int $currentPageIndex = 0;
    private float $currentY = 0;

    /**
     * @var float[]
     */
    private array $margin;

    /**
     * @param float[]       $pageSize
     * @param float|float[] $margin
     */
    public function __construct(private array $pageSize = [210, 297], mixed $margin = [15, 15, 15, 15])
    {
        $this->margin = is_array($margin) ? $margin : array_fill(0, 4, $margin);

        $this->imageRepository = ImageRepository::instance();
        $this->fontRepository = FontRepository::instance();
        $this->document = new \PdfGenerator\IR\Document();
        $this->addPage();
    }

    public function add(AbstractBlock $block): void
    {
        $currentBlock = $block;
        do {
            $allocation = $this->allocate($currentBlock);

            // advance page if no sensible allocation
            if (!$allocation) {
                if ($this->currentY > 0) {
                    $this->addPage();
                    continue;
                } else {
                    // TODO: measure min width, then allocate using that min width
                    throw new \Exception('Page too small to fit content.');
                }
            }

            $this->place($allocation);
            $this->currentY += $allocation->getHeight();
            $currentBlock = $allocation->getOverflow();
        } while (null !== $currentBlock);
    }

    public function allocate(AbstractBlock $block): ?BlockAllocation
    {
        $widthMargin = $this->margin[0] + $this->margin[2];
        $width = $this->pageSize[0] - $widthMargin;

        $heightMargin = $this->margin[1] + $this->margin[3];
        $height = $this->pageSize[1] - $this->currentY - $heightMargin;

        $allocationVisitor = new BlockAllocationVisitor($width, $height);

        return $block->accept($allocationVisitor);
    }

    public function place(BlockAllocation $allocation): void
    {
        $left = $this->margin[0];
        $top = $this->currentY + $this->margin[1];
        $pagePrinter = $this->createPrinter($this->currentPageIndex, $left, $top);
        $pagePrinter->print($allocation);
    }

    public function createPrinter(int $pageIndex, float $left, float $top): Printer
    {
        $page = $this->document->getPages()[$pageIndex];

        return new Printer($this->document, $this->imageRepository, $this->fontRepository, $page, $left, $top);
    }

    public function addPage(array $pageSize = null): void
    {
        $nextPageIndex = $this->getPageCount();
        $page = new Page($nextPageIndex + 1, $pageSize ?? $this->pageSize);
        $this->document->addPage($page);

        $this->currentY = 0;
        $this->currentPageIndex = $nextPageIndex;
    }

    public function getPageCount(): int
    {
        return count($this->document->getPages());
    }

    public function save(): string
    {
        return $this->document->save();
    }

    public function position(float $currentY, int $currentPageIndex = null): void
    {
        $this->currentY = $currentY;
        $this->currentPageIndex = $currentPageIndex ?? $this->currentPageIndex;
    }
}
