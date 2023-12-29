<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend;

use PdfGenerator\Frontend\Content\Paragraph\Phrase;
use PdfGenerator\Frontend\Content\Style\DrawingStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use PdfGenerator\Frontend\Resource\Font\FontRepository;
use PdfGenerator\Frontend\Resource\Image\ImageRepository;
use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\Common\Size;
use PdfGenerator\IR\Document\Content\ImagePlacement;
use PdfGenerator\IR\Document\Content\Paragraph;
use PdfGenerator\IR\Document\Content\Rectangle;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text;
use PdfGenerator\IR\Document\Page;

readonly class Printer
{
    public function __construct(private \PdfGenerator\IR\Document $document, private ImageRepository $imageRepository, private FontRepository $fontRepository, private Page $page, private float $left, private float $top)
    {
    }

    public function position(float $left, float $top): self
    {
        return new self($this->document, $this->imageRepository, $this->fontRepository, $this->page, $this->left + $left, $this->top + $top);
    }

    public function print(BlockAllocation $allocation): void
    {
        $placedPrinter = self::position($allocation->getLeft(), $allocation->getTop());

        foreach ($allocation->getBlockAllocations() as $blockAllocation) {
            $placedPrinter->print($blockAllocation);
        }

        foreach ($allocation->getContentAllocations() as $contentAllocation) {
            $contentAllocation->getContent()->print($placedPrinter, $contentAllocation->getWidth(), $contentAllocation->getHeight());
        }
    }

    public function printImage(Resource\Image $image, float $width, float $height): void
    {
        $IRImage = $this->imageRepository->getImage($image);

        $position = $this->getPosition($height);
        $size = new Size($width, $height);

        $imagePlacement = new ImagePlacement($IRImage, $position, $size);
        $this->page->addContent($imagePlacement);
    }

    public function printRectangle(float $width, float $height, DrawingStyle $drawingStyle): void
    {
        $rectangleStyle = self::createRectangleStyle($drawingStyle);

        $position = $this->getPosition($height);
        $size = new Size($width, $height);

        $rectangle = new Rectangle($position, $size, $rectangleStyle);
        $this->page->addContent($rectangle);
    }

    public function printText(string $text, TextStyle $textStyle): void
    {
        $IRTextStyle = self::createTextStyle($textStyle);

        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);
        $ascender = $fontMeasurement->getAscender();

        $position = $this->getPosition($ascender);

        $textContent = new Text($text, $position, $IRTextStyle);
        $this->page->addContent($textContent);
    }

    /**
     * @param Phrase[] $phrases
     */
    public function printPhrases(array $phrases): void
    {
        $heightShift = 0;
        if (count($phrases) > 0) {
            $phrase = $phrases[0];
            $fontMeasurement = $this->fontRepository->getFontMeasurement($phrase->getTextStyle());
            $heightShift = $fontMeasurement->getAscender();
        }

        /** @var Text\Phrase[] $IRPhases */
        $IRPhases = [];
        foreach ($phrases as $phrase) {
            $textStyle = self::createTextStyle($phrase->getTextStyle());
            $IRPhases[] = new Text\Phrase($phrase->getText(), $textStyle);
        }

        $position = $this->getPosition($heightShift);

        $paragraph = new Paragraph($IRPhases, $position);
        $this->page->addContent($paragraph);
    }

    private function getPosition(float $height): Position
    {
        $top = $this->page->getSize()[1] - $this->top - $height;

        return new Position($this->left, $top);
    }

    private static function createRectangleStyle(DrawingStyle $drawingStyle): RectangleStyle
    {
        return new RectangleStyle($drawingStyle->getLineWidth(), $drawingStyle->getLineColor(), $drawingStyle->getFillColor());
    }

    private function createTextStyle(TextStyle $textStyle): Text\TextStyle
    {
        $font = $this->fontRepository->getFont($textStyle->getFont());

        return new Text\TextStyle($font, $textStyle->getFontSize(), $textStyle->getLineHeight(), $textStyle->getColor());
    }
}
