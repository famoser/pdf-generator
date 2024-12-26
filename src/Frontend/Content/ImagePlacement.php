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

use Famoser\PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use Famoser\PdfGenerator\Frontend\Printer;
use Famoser\PdfGenerator\Frontend\Resource\Image;

class ImagePlacement extends AbstractContent
{
    public function __construct(private readonly Image $image)
    {
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitImagePlacement($this);
    }

    public function print(Printer $printer, float $width, float $height): void
    {
        $printer->printImage($this->getImage(), $width, $height);
    }
}
