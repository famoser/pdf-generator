<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\Operators\Level\DrawingState;
use PdfGenerator\Backend\Content\Operators\State\Base\BaseState;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\ContentVisitor;
use PdfGenerator\Backend\Structure\Image;

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
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor): BaseObject
    {
        return $visitor->visitImageContent($this);
    }
}
