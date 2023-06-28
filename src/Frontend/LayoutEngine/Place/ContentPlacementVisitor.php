<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Place;

use PdfGenerator\Frontend\Content\ImagePlacement;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Spacer;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;

/**
 * This places content on the PDF.
 *
 * Importantly, the placement guarantees progress (i.e. with each call, less to-be-placed content remains).
 * For this guarantee, boundaries might be disrespected (e.g. content wider than the maxWidth is placed).
 *
 * @implements AbstractContentVisitor<ContentPlacement>
 */
class ContentPlacementVisitor extends AbstractContentVisitor
{
    public function __construct(private readonly Printer $printer, private readonly float $width, private readonly float $height)
    {
    }

    public function visitRectangle(Rectangle $rectangle): ContentPlacement
    {
        $rectangleStyle = self::createRectangleStyle($rectangle->getStyle());
        $this->printer->printRectangle($this->width, $this->height, $rectangleStyle);

        return new ContentPlacement($this->width, $this->height);
    }

    public function visitImagePlacement(ImagePlacement $imagePlacement): ContentPlacement
    {
        $image = $this->printer->getOrCreateImage($imagePlacement->getImage()->getSrc(), $imagePlacement->getImage()->getType());
        $this->printer->printImage($image, $this->width, $this->height);

        return new ContentPlacement($this->width, $this->height);
    }

    public function visitSpacer(Spacer $spacer): ContentPlacement
    {
        return new ContentPlacement(0, 0);
    }

    private static function createRectangleStyle(DrawingStyle $drawingStyle): RectangleStyle
    {
        return new RectangleStyle($drawingStyle->getLineWidth(), $drawingStyle->getLineColor(), $drawingStyle->getFillColor());
    }
}
