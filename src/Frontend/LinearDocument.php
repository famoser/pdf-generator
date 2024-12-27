<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend;

use Famoser\DocumentGenerator\DocumentInterface;
use Famoser\PdfGenerator\Frontend\Layout\AbstractBlock;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocationVisitor;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\IR\Document;
use Famoser\PdfGenerator\IR\Document\Page;

class LinearDocument implements DocumentInterface
{
    private readonly Document $document;
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
    public function __construct(private readonly array $pageSize = [210, 297], mixed $margin = [15, 15, 15, 15])
    {
        $this->margin = is_array($margin) ? $margin : array_fill(0, 4, $margin);

        $this->imageRepository = ImageRepository::instance();
        $this->fontRepository = FontRepository::instance();
        $this->document = new Document();
        $this->addPage();
    }

    public function add(AbstractBlock $block): void
    {
        $currentBlock = $block;
        do {
            $allocation = $this->allocate($currentBlock);
            [, $height] = $this->getUsableSpace();
            if ($allocation->getHeight() > $height && $this->currentY > 0) {
                $this->addPage();
                continue;
            }

            $this->place($allocation);
            $this->currentY += $allocation->getHeight();
            $currentBlock = $allocation->getOverflow();
        } while (null !== $currentBlock);
    }

    /**
     * @return float[]
     */
    private function getUsableSpace(): array
    {
        $widthMargin = $this->margin[0] + $this->margin[2];
        $width = $this->pageSize[0] - $widthMargin;

        $heightMargin = $this->margin[1] + $this->margin[3];
        $height = $this->pageSize[1] - $this->currentY - $heightMargin;

        return [$width, $height];
    }

    public function allocate(AbstractBlock $block): BlockAllocation
    {
        $usableSpace = $this->getUsableSpace();
        $allocationVisitor = new BlockAllocationVisitor(...$usableSpace);

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

    /**
     * @param float[]|null $pageSize
     */
    public function addPage(?array $pageSize = null): void
    {
        $nextPageIndex = $this->getPageCount();
        $page = new Page(strval($nextPageIndex + 1), $pageSize ?? $this->pageSize);
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

    public function position(float $currentY, ?int $currentPageIndex = null): void
    {
        $this->currentY = $currentY;
        $this->currentPageIndex = $currentPageIndex ?? $this->currentPageIndex;
    }
}
