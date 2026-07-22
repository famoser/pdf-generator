<?php

namespace Famoser\PdfGenerator\Frontend;

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\AllocationVisitor;
use Famoser\PdfGenerator\Frontend\Page\PageProperties;
use Famoser\PdfGenerator\Frontend\Page\PagePropertiesConverter;
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

    public function __construct(private PageProperties $pageProperties = new PageProperties(), Meta $meta = new Meta())
    {
        $this->imageRepository = ImageRepository::instance();
        $this->fontRepository = FontRepository::instance();
        $this->document = new IR\Document(MetaConverter::convert($meta));
        $this->addPage();
    }

    public function getPageProperties(): PageProperties
    {
        return $this->pageProperties;
    }

    public function setPageProperties(PageProperties $pageProperties): void
    {
        $this->pageProperties = $pageProperties;
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

    public function addPage(?PageProperties $pageProperties = null): self
    {
        $nextPageIndex = $this->getPageCount();
        $irPageProperties = PagePropertiesConverter::convert($pageProperties ?? $this->pageProperties);
        $page = new IR\Document\Page(strval($nextPageIndex + 1), $irPageProperties);
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

    public function shiftPosition(?float $yShift = null, ?int $pageIndexShift = null): self
    {
        $this->currentY = $yShift ? $this->currentY + $yShift : $this->currentY;
        $this->currentPageIndex = $pageIndexShift ? $this->currentPageIndex + $pageIndexShift : $this->currentPageIndex;

        return $this;
    }

    public function createPrinter(?float $currentY = null, ?int $currentPageIndex = null): Printer
    {
        $this->setPosition($currentY, $currentPageIndex);

        $page = $this->document->getPages()[$this->currentPageIndex];
        $left = $this->pageProperties->getMargin()[0];
        $top = $this->currentY + $this->pageProperties->getMargin()[1];

        return new Printer($this->imageRepository, $this->fontRepository, $page, $left, $top);
    }

    /**
     * @return float[]
     */
    private function getUsableSpace(): array
    {
        $margin = $this->pageProperties->getMargin();
        $pageSize = $this->pageProperties->getSize();

        $widthMargin = $margin[0] + $margin[2];
        $width = $pageSize[0] - $widthMargin;

        $heightMargin = $margin[1] + $margin[3];
        $height = $pageSize[1] - $this->currentY - $heightMargin;

        return [$width, $height];
    }
}
