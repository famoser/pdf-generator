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

use PdfGenerator\Backend\Content\Base\PlacedContent;
use PdfGenerator\Backend\ContentVisitor;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\Backend\Structure\Page;

class ImageContent extends PlacedContent
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * @param float $xCoordinate
     * @param float $yCoordinate
     * @param Image $image
     * @param float $width
     * @param float $height
     */
    public function __construct(float $xCoordinate, float $yCoordinate, Image $image, float $width, float $height)
    {
        parent::__construct($xCoordinate, $yCoordinate);

        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param ContentVisitor $visitor
     * @param File $file
     * @param Page $page
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor, File $file, Page $page): BaseObject
    {
        return $visitor->visitImageContent($this, $file, $page);
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }
}
