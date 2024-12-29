<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend;

use Famoser\PdfGenerator\Frontend\Content\Paragraph\Phrase;
use Famoser\PdfGenerator\Frontend\Content\Style\DrawingStyle;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;
use Famoser\PdfGenerator\Frontend\Resource\Image\ImageRepository;
use Famoser\PdfGenerator\IR\Document;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\Common\Size;
use Famoser\PdfGenerator\IR\Document\Content\ImagePlacement;
use Famoser\PdfGenerator\IR\Document\Content\Paragraph;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use Famoser\PdfGenerator\IR\Document\Content\Text;
use Famoser\PdfGenerator\IR\Document\Page;

readonly class Printer
{
    public function __construct(private Document $document, private ImageRepository $imageRepository, private FontRepository $fontRepository, private Page $page, private float $left, private float $top)
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
        /** @var Text\Phrase[] $IRPhases */
        $IRPhases = [];
        foreach ($phrases as $phrase) {
            $textStyle = self::createTextStyle($phrase->getTextStyle());
            $IRPhases[] = new Text\Phrase($phrase->getText(), $textStyle);
        }

        $heightShift = 0;
        if (count($phrases) > 0) {
            $fontMeasurement = $this->fontRepository->getFontMeasurement($phrases[0]->getTextStyle());
            $heightShift = $fontMeasurement->getAscender();
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
