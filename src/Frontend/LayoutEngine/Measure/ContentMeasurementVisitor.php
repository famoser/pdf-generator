<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Measure;

use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Spacer;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\ParagraphMeasurer;

/**
 * @implements ContentVisitorInterface<Measurement>
 */
readonly class ContentMeasurementVisitor implements ContentVisitorInterface
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
        $getimagesize = getimagesize($imagePlacement->getImage()->getSrc());
        if (!$getimagesize) {
            throw new \Exception("Cannot measure image size: " . $imagePlacement->getImage()->getSrc());
        }

        [$width, $height] = $getimagesize;

        return new Measurement($width * $height, 0, 0);
    }

    public function visitParagraph(Paragraph $paragraph): Measurement
    {
        $measurer = new ParagraphMeasurer();

        return $measurer->measure($paragraph->getPhrases());
    }
}
