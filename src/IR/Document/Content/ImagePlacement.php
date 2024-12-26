<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Content;

use Famoser\PdfGenerator\IR\Document\Content\Base\BaseContent;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Common\Size;
use Famoser\PdfGenerator\IR\Document\Resource\Image;

readonly class ImagePlacement extends BaseContent
{
    public function __construct(private Image $image, private Position $position, private Size $size)
    {
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

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitImagePlacement($this);
    }
}
