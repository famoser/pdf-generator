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
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Content\Spacer;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;
use PdfGenerator\Frontend\Printer;
use PdfGenerator\Frontend\Resource\Font\FontRepository;
use PdfGenerator\Frontend\Resource\Image\ImageRepository;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text\Phrase;

/**
 * This places content on the PDF.
 *
 * Importantly, the placement guarantees progress (i.e. with each call, less to-be-placed content remains).
 * For this guarantee, boundaries might be disrespected (e.g. content wider than the maxWidth is placed).
 *
 * @implements AbstractContentVisitor<void>
 */
class ContentPlacementVisitor extends AbstractContentVisitor
{
    private ImageRepository $imageRepository;
    private FontRepository $fontRepository;

    public function __construct(private readonly Printer $printer, private readonly float $width, private readonly float $height)
    {
        $this->imageRepository = ImageRepository::instance();
        $this->fontRepository = FontRepository::instance();
    }

    public function visitRectangle(Rectangle $rectangle): null
    {
        $rectangleStyle = self::createRectangleStyle($rectangle->getStyle());
        $this->printer->printRectangle($this->width, $this->height, $rectangleStyle);

        return null;
    }

    public function visitImagePlacement(ImagePlacement $imagePlacement): null
    {
        $image = $this->imageRepository->getOrCreateImage($imagePlacement->getImage()->getSrc(), $imagePlacement->getImage()->getType());
        $this->printer->printImage($image, $this->width, $this->height);

        return null;
    }

    public function visitParagraph(Paragraph $paragraph): null
    {
        /** @var Phrase[] $phrases */
        $phrases = [];
        $heightShift = 0;
        if (count($paragraph->getPhrases()) > 0) {
            $phrase = $paragraph->getPhrases()[0];
            $fontMeasurement = $this->fontRepository->getFontMeasurement($phrase->getTextStyle());
            $heightShift = $fontMeasurement->getAscender();
        }

        foreach ($paragraph->getPhrases() as $phrase) {
            $textStyle = self::createTextStyle($phrase->getTextStyle());
            $phrases[] = new Phrase($phrase->getText(), $textStyle);
        }

        $this->printer->printPhrases($phrases, $heightShift);

        return null;
    }

    public function visitSpacer(Spacer $spacer): null
    {
        return null;
    }

    private static function createRectangleStyle(DrawingStyle $drawingStyle): RectangleStyle
    {
        return new RectangleStyle($drawingStyle->getLineWidth(), $drawingStyle->getLineColor(), $drawingStyle->getFillColor());
    }

    private function createTextStyle(TextStyle $textStyle): \PdfGenerator\IR\Document\Content\Text\TextStyle
    {
        $font = $this->fontRepository->getFont($textStyle->getFont());

        return new \PdfGenerator\IR\Document\Content\Text\TextStyle($font, $textStyle->getFontSize(), $textStyle->getLineHeight());
    }
}
