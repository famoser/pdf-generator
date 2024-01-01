<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Measure;

use PdfGenerator\Frontend\Content\ImagePlacement;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Spacer;
use PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\ParagraphMeasurer;

/**
 * @implements ContentVisitorInterface<Measurement>
 */
class ContentMeasurementVisitor implements ContentVisitorInterface
{
    public function visitRectangle(Rectangle $rectangle): Measurement
    {
        return Measurement::zero();
    }

    public function visitSpacer(Spacer $spacer): Measurement
    {
        return Measurement::zero();
    }

    public function visitImagePlacement(ImagePlacement $imagePlacement): Measurement
    {
        [$width, $height] = getimagesize($imagePlacement->getImage()->getSrc());

        return new Measurement($width * $height, 0, 0);
    }

    public function visitParagraph(Paragraph $paragraph): Measurement
    {
        $measurer = new ParagraphMeasurer();

        return $measurer->measure($paragraph->getPhrases());
    }
}
