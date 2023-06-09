<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\Content;

use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Size;
use PdfGenerator\IR\Structure\Document\Page\ContentVisitor;

class ImagePlacement extends BaseContent
{
    private Image $image;

    private Position $position;

    private Size $size;

    /**
     * ImagePlacement constructor.
     */
    public function __construct(Image $image, Position $position, Size $size)
    {
        $this->image = $image;
        $this->position = $position;
        $this->size = $size;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function accept(ContentVisitor $visitor): ?\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent
    {
        return $visitor->visitImagePlacement($this);
    }
}
