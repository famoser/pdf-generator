<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\TextLine;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\TextSegment;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use Famoser\PdfGenerator\IR\Document\Content\Common\Position;
use Famoser\PdfGenerator\IR\Document\Content\ContentVisitorInterface;
use Famoser\PdfGenerator\IR\Document\Content\ImagePlacement;
use Famoser\PdfGenerator\IR\Document\Content\Text;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle;
use Famoser\PdfGenerator\IR\Document\Content\Rectangle\RectangleStyle;
use Famoser\PdfGenerator\IR\Document\Resource\PageResources;

/**
 * @implements ContentVisitorInterface<BaseContent>
 */
readonly class ContentVisitor implements ContentVisitorInterface
{
    public function __construct(private PageResources $pageResources)
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
        $this->applyPosition($text->getPosition());
        $generalGraphicState = $this->pageResources->getGeneralGraphicState();
        $lines = [];
        foreach ($text->getLines() as $line) {
            $segments = [];
            foreach ($line->getSegments() as $segment) {
                $this->applyTextStyle($segment->getStyle());
                $writingState = $this->pageResources->getWritingState();
                $segments[] = new TextSegment($segment->getText(), $writingState);
            }

            $lines[] = new TextLine($segments, $line->getOffset());
        }

        return new TextContent($lines, $generalGraphicState);
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

    private function applyTextStyle(Text\TextStyle $style): void
    {
        $font = $style->getFont();
        $textStateRepository = $this->pageResources->getTextStateRepository();

        $textStateRepository->setFontSize($style->getFontSize());
        $textStateRepository->setFont($this->pageResources->getFont($font));

        $textStateRepository->setLeading($style->getLeading());
        $textStateRepository->setWordSpace($style->getWordSpace());

        $this->pageResources->getColorStateRepository()->setFillColor($style->getColor());
    }
}
