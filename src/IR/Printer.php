<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\IR\Document\Image;
use PdfGenerator\IR\Document\Page;
use PdfGenerator\IR\Document\Page\Content\Common\Position;
use PdfGenerator\IR\Document\Page\Content\Common\Size;
use PdfGenerator\IR\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Page\Content\Text;
use PdfGenerator\IR\Document\Page\Content\Text\TextStyle;

class Printer
{
    public function printText(Page $page, Position $position, string $text, TextStyle $textStyle): void
    {
        $text = new Text($text, $position, $textStyle);

        $page->addContent($text);
    }

    public function printImage(Page $page, Position $position, Image $image, float $width, float $height): void
    {
        $size = new Size($width, $height);
        $imagePlacement = new ImagePlacement($image, $position, $size);

        $page->addContent($imagePlacement);
    }

    public function printRectangle(Page $page, Position $position, float $width, float $height, RectangleStyle $rectangleStyle): void
    {
        $size = new Size($width, $height);
        $text = new Rectangle($position, $size, $rectangleStyle);

        $page->addContent($text);
    }
}
