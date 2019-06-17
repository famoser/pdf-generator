<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Page\Content;

use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Page\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Page\Content\Common\Size;
use PdfGenerator\IR\Structure\Page\ContentVisitor;

class ImagePlacement extends BaseContent
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var Size
     */
    private $size;

    /**
     * ImagePlacement constructor.
     *
     * @param Image $image
     * @param Position $position
     * @param Size $size
     */
    public function __construct(Image $image, Position $position, Size $size)
    {
        $this->image = $image;
        $this->position = $position;
        $this->size = $size;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @return Size
     */
    public function getSize(): Size
    {
        return $this->size;
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent
     */
    public function accept(ContentVisitor $visitor): \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent
    {
        return $visitor->visitImagePlacement($this);
    }
}
