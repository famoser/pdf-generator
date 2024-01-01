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

use PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\Frontend\Resource\Image;

class ImagePlacement extends AbstractContent
{
    public function __construct(private readonly Image $image)
    {
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function accept(ContentVisitorInterface $visitor): mixed
    {
        return $visitor->visitImagePlacement($this);
    }

    public function print(Printer $printer, float $width, float $height): void
    {
        $printer->printImage($this->getImage(), $width, $height);
    }
}
