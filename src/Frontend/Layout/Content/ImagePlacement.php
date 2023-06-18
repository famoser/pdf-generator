<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Content;

use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\Resource\Image;

class ImagePlacement extends Content
{
    public function __construct(private readonly Image $image)
    {
        parent::__construct();
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitImagePlacement($this);
    }
}
