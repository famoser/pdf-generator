<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Content;

use PdfGenerator\Backend\Structure\Page;
use PdfGenerator\IR\Structure2\Content\Base\BaseContent;
use PdfGenerator\IR\Structure2\Content\Common\Position;
use PdfGenerator\IR\Structure2\Content\Common\Size;
use PdfGenerator\IR\Structure2\Image;

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
     * @param Page $page
     */
    public function accept(ContentVisitor $visitor, Page $page)
    {
        $visitor->visitImagePlacement($this, $page);
    }
}
