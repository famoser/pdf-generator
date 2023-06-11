<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content;

use PdfGenerator\Frontend\Content\Base\Content;
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
}
