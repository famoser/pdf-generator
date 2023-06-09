<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\Content;

use PdfGenerator\Backend\Catalog\Content;
use PdfGenerator\Backend\Structure\Document\Image;
use PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\DrawingState;

class ImageContent extends BaseContent
{
    public function __construct(private readonly Image $image, private readonly DrawingState $drawingState)
    {
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getCurrentTransformationMatrix(): array
    {
        return $this->drawingState->getGeneralGraphicsState()->getCurrentTransformationMatrix();
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
