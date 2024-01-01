<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Document;

use PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\Phrase;
use PdfGenerator\Backend\Structure\Document\Page\Content\ParagraphContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use PdfGenerator\IR\Document\Content\Common\Position;
use PdfGenerator\IR\Document\Content\ContentVisitorInterface;
use PdfGenerator\IR\Document\Content\ImagePlacement;
use PdfGenerator\IR\Document\Content\Paragraph;
use PdfGenerator\IR\Document\Content\Rectangle;
use PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Document\Content\Text;
use PdfGenerator\IR\Document\Resource\PageResources;

class ContentVisitor implements ContentVisitorInterface
{
    public function __construct(private readonly PageResources $pageResources)
    {
    }

    public function visitImagePlacement(ImagePlacement $placement): ImageContent
    {
        $image = $this->pageResources->getImage($placement->getImage());

        $this->applyImagePlacementPositionAndSize($placement);
        $drawingState = $this->pageResources->getDrawingState();

        return new ImageContent($image, $drawingState);
    }

    public function visitRectangle(Rectangle $rectangle): RectangleContent
    {
        $width = $rectangle->getSize()->getWidth();
        $height = $rectangle->getSize()->getHeight();
        $paintingMode = $this->getPaintingMode($rectangle);

        $this->applyPosition($rectangle->getPosition());
        $this->applyRectangleStyle($rectangle->getStyle());
        $drawingState = $this->pageResources->getDrawingState();

        return new RectangleContent($width, $height, $paintingMode, $drawingState);
    }

    public function visitText(Text $text): TextContent
    {
        $lines = $this->splitAtNewlines($text->getText());

        $this->applyPosition($text->getPosition());
        $this->applyTextStyle($text->getStyle());
        $writingState = $this->pageResources->getWritingState();

        return new TextContent($lines, $writingState);
    }

    public function visitParagraph(Paragraph $paragraph): ParagraphContent
    {
        $this->applyPosition($paragraph->getPosition());
        $generalGraphicState = $this->pageResources->getGeneralGraphicState();
        /** @var Phrase[] $phrases */
        $phrases = [];
        foreach ($paragraph->getPhrase() as $phrase) {
            $lines = $this->splitAtNewlines($phrase->getText());
            $this->applyTextStyle($phrase->getStyle());
            $writingState = $this->pageResources->getWritingState();
            $phrases[] = new Phrase($lines, $writingState);
        }

        return new ParagraphContent($phrases, $generalGraphicState);
    }

    /**
     * @return string[]
     */
    private function splitAtNewlines(string $text): array
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);

        return explode("\n", $textWithNormalizedNewlines);
    }

    private function applyImagePlacementPositionAndSize(ImagePlacement $placement): void
    {
        $scaleX = $placement->getSize()->getWidth();
        $scaleY = $placement->getSize()->getHeight();

        $this->applyPosition($placement->getPosition(), $scaleX, $scaleY);
    }

    private function applyPosition(Position $position, float $scaleX = 1, float $scaleY = 1): void
    {
        $startX = $position->getStartX();
        $startY = $position->getStartY();

        $this->pageResources->getGeneralGraphicStateRepository()->setPosition($startX, $startY, $scaleX, $scaleY);
    }

    private function applyRectangleStyle(RectangleStyle $style): void
    {
        $this->pageResources->getColorStateRepository()->setBorderColor($style->getBorderColor());
        $this->pageResources->getColorStateRepository()->setFillColor($style->getFillColor());
        $this->pageResources->getGeneralGraphicStateRepository()->setLineWidth($style->getLineWidth());
    }

    private function getPaintingMode(Rectangle $rectangle): int
    {
        if (null !== $rectangle->getStyle()->getFillColor()) {
            if (null !== $rectangle->getStyle()->getBorderColor()) {
                return RectangleContent::PAINTING_MODE_STROKE_FILL;
            }

            return RectangleContent::PAINTING_MODE_FILL;
        } elseif (null !== $rectangle->getStyle()->getBorderColor()) {
            return RectangleContent::PAINTING_MODE_STROKE;
        }

        return RectangleContent::PAINTING_MODE_NONE;
    }

    private function applyTextStyle(Content\Text\TextStyle $style): void
    {
        $font = $style->getFont();
        $textStateRepository = $this->pageResources->getTextStateRepository();

        $textStateRepository->setFontSize($style->getFontSize());
        $textStateRepository->setFont($this->pageResources->getFont($font));

        $scale = $style->getFontSize() / $font->getUnitsPerEm();
        $leadingUnit = $font->getBaselineToBaselineDistance() * $scale;
        $textStateRepository->setLeading($style->getLineHeight() * $leadingUnit);

        $this->pageResources->getColorStateRepository()->setFillColor($style->getColor());
    }
}
