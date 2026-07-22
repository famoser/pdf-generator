<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content;

use Famoser\PdfGenerator\Backend\Catalog\Content;
use Famoser\PdfGenerator\Backend\Structure\Document\Image;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\DrawingState;

readonly class ImageContent extends BaseContent
{
    public function __construct(private Image $image, private DrawingState $drawingState)
    {
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->drawingState->getState();
    }

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitImageContent($this);
    }
}
