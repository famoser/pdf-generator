<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Resource\Image;

readonly class ImagePlacement extends AbstractContent
{
    public function __construct(private float $width, private float $height, private Image $image)
    {
        parent::__construct($this->width, $this->height);
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function accept(ContentVisitorInterface $contentVisitor): void
    {
        $contentVisitor->visitImagePlacement($this);
    }
}
