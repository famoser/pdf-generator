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
use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Operators\Level\DrawingState;
use PdfGenerator\Backend\Structure\Operators\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent;

class ImageContent extends BaseContent
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var DrawingState
     */
    private $color;

    /**
     * @param Image $image
     * @param DrawingState $color
     */
    public function __construct(Image $image, DrawingState $color)
    {
        $this->image = $image;
        $this->color = $color;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return DrawingState
     */
    public function getColor(): DrawingState
    {
        return $this->color;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->color->getState();
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return Content
     */
    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitImageContent($this);
    }
}
