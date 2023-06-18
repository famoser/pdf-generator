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

use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\IR\Document;

/**
 * This places content on the PDF.
 *
 * Importantly, the placement guarantees progress (i.e. with each call, less to-be-placed content remains).
 * For this guarantee, boundaries might be disrespected (e.g. content wider than the maxWidth is placed).
 *
 * @implements AbstractBlockVisitor<Placement>
 */
class PlacementVisitor extends AbstractBlockVisitor
{
    public function __construct(private Printer $pagePrinter, private float $width, private float $height)
    {
    }

    public function visitRectangle(Content\Rectangle $param): Placement
    {
        // TODO: convert frontend rectangle style to IR rectangle style
        $rectangleStyle = new Document\Content\Rectangle\RectangleStyle();
        $this->pagePrinter->printRectangle($param->getWidth(), $param->getHeight(), $rectangleStyle);

        return new Placement($param->getWidth(), $param->getHeight());
    }
}
