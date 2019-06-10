<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\PageContent;

use PdfGenerator\Backend\Catalog\Image;
use PdfGenerator\Backend\Structure\ImageContent;
use PdfGenerator\Backend\Structure\TextContent;
use PdfGenerator\IR\Structure\PageContent\Common\Position;
use PdfGenerator\IR\Structure\PageContent\Rectangle\RectangleStyle;
use PdfGenerator\IR\Transformation\PageResources;

class ContentVisitor
{
    /**
     * @var PageResources
     */
    private $pageResources;

    /**
     * ContentVisitor constructor.
     *
     * @param PageResources $pageResources
     */
    public function __construct(PageResources $pageResources)
    {
        $this->pageResources = $pageResources;
    }

    /**
     * @param ImagePlacement $placement
     *
     * @return ImageContent
     */
    public function visitImagePlacement(ImagePlacement $placement): ImageContent
    {
        $image = $this->pageResources->getImage($placement->getImage());

        $this->applyImagePlacementPositionAndSize($image, $placement);
        $pageLevel = $this->pageResources->getDrawingState();

        return new ImageContent($image, $pageLevel);
    }

    /**
     * @param Image $image
     * @param ImagePlacement $placement
     */
    private function applyImagePlacementPositionAndSize(Image $image, ImagePlacement $placement)
    {
        $scaleX = $placement->getSize()->getWidth() / $image->getWidth();
        $scaleY = $placement->getSize()->getHeight() / $image->getHeight();

        $this->applyPosition($placement->getPosition(), $scaleX, $scaleY);
    }

    /**
     * @param Rectangle $rectangle
     *
     * @return \PdfGenerator\Backend\Structure\Rectangle
     */
    public function visitRectangle(Rectangle $rectangle): \PdfGenerator\Backend\Structure\Rectangle
    {
        $width = $rectangle->getSize()->getWidth();
        $height = $rectangle->getSize()->getHeight();
        $paintingMode = $this->getPaintingMode($rectangle);
        $this->applyPosition($rectangle->getPosition());
        $this->applyRectangleStyle($rectangle->getStyle());
        $pageLevel = $this->pageResources->getDrawingState();

        return new \PdfGenerator\Backend\Structure\Rectangle($width, $height, $paintingMode, $pageLevel);
    }

    /**
     * @param Position $position
     * @param float $scaleX
     * @param float $scaleY
     */
    private function applyPosition(Position $position, float $scaleX = 1, float $scaleY = 1)
    {
        $startX = $position->getStartX();
        $startY = $position->getStartY();

        $this->pageResources->getGeneralGraphicStateRepository()->setPosition($startX, $startY, $scaleX, $scaleY);
    }

    /**
     * @param RectangleStyle $style
     */
    private function applyRectangleStyle(RectangleStyle $style)
    {
        $this->pageResources->getColorStateRepository()->setBorderColor($style->getBorderColor());
        $this->pageResources->getColorStateRepository()->setFillColor($style->getFillColor());
        $this->pageResources->getGeneralGraphicStateRepository()->setLineWidth($style->getLineWidth());
    }

    /**
     * @param Rectangle $rectangle
     *
     * @return bool|int
     */
    private function getPaintingMode(Rectangle $rectangle)
    {
        if ($rectangle->getStyle()->getFillColor() !== null) {
            if ($rectangle->getStyle()->getBorderColor() !== null) {
                return \PdfGenerator\Backend\Structure\Rectangle::PAINTING_MODE_STROKE_FILL;
            }

            return \PdfGenerator\Backend\Structure\Rectangle::PAINTING_MODE_FILL;
        } elseif ($rectangle->getStyle()->getBorderColor() !== null) {
            return \PdfGenerator\Backend\Structure\Rectangle::PAINTING_MODE_STROKE;
        }

        return \PdfGenerator\Backend\Structure\Rectangle::PAINTING_MODE_NONE;
    }

    /**
     * @param Text $param
     *
     * @return TextContent
     */
    public function visitText(Text $param): TextContent
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $param->getText());
        $lines = explode("\n", $textWithNormalizedNewlines);

        $this->applyPosition($param->getPosition());
        $this->applyTextStyle($param->getStyle());
        $textLevel = $this->pageResources->getWritingState();

        return new TextContent($lines, $textLevel);
    }

    /**
     * @param Text\TextStyle $style
     */
    private function applyTextStyle(Text\TextStyle $style)
    {
        $font = $this->pageResources->getFont($style->getFont());
        $this->pageResources->getTextStateRepository()->setFont($font);
        $this->pageResources->getTextStateRepository()->setFontSize($style->getFontSize());
    }
}
