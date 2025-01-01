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

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\AllocationVisitor;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\Frontend\Resource\Meta;
use Famoser\PdfGenerator\Frontend\Resource\Meta\MetaConverter;
use Famoser\PdfGenerator\IR;

class Document
{
    private readonly IR\Document $document;
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
    public function __construct(private readonly array $pageSize = [210, 297], mixed $margin = [15, 15, 15, 15], Meta $meta = new Meta())
    {
        $this->margin = is_array($margin) ? $margin : array_fill(0, 4, $margin);

        $this->imageRepository = ImageRepository::instance();
        $this->fontRepository = FontRepository::instance();
        $this->document = new IR\Document(MetaConverter::convert($meta));
        $this->addPage();
    }

    public function add(AbstractElement $block): self
    {
        $currentBlock = $block;
        do {
            $allocation = $this->allocate($currentBlock);
            [, $height] = $this->getUsableSpace();
            if ($allocation->getHeight() > $height && $this->currentY > 0) {
                $this->addPage();
                continue;
            }

            $pagePrinter = $this->createPrinter();
            $pagePrinter->place($allocation);

            $this->currentY += $allocation->getHeight();
            $currentBlock = $allocation->getOverflow();
        } while (null !== $currentBlock);

        return $this;
    }

    public function allocate(AbstractElement $block): Allocation
    {
        $usableSpace = $this->getUsableSpace();
        $allocationVisitor = new AllocationVisitor(...$usableSpace);

        return $block->accept($allocationVisitor);
    }

    /**
     * @param float[]|null $pageSize
     */
    public function addPage(?array $pageSize = null): self
    {
        $nextPageIndex = $this->getPageCount();
        $page = new IR\Document\Page(strval($nextPageIndex + 1), $pageSize ?? $this->pageSize);
        $this->document->addPage($page);

        $this->currentY = 0;
        $this->currentPageIndex = $nextPageIndex;

        return $this;
    }

    public function getPageCount(): int
    {
        return count($this->document->getPages());
    }

    public function save(): string
    {
        return $this->document->save();
    }

    public function setPosition(?float $currentY = null, ?int $currentPageIndex = null): self
    {
        $this->currentY = $currentY ?? $this->currentY;
        $this->currentPageIndex = $currentPageIndex ?? $this->currentPageIndex;

        return $this;
    }

    public function createPrinter(?float $currentY = null, ?int $currentPageIndex = null): Printer
    {
        $this->setPosition($currentY, $currentPageIndex);

        $page = $this->document->getPages()[$this->currentPageIndex];
        $left = $this->margin[0];
        $top = $this->currentY + $this->margin[1];

        return new Printer($this->imageRepository, $this->fontRepository, $page, $left, $top);
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
}
