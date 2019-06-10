<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure2\Content;

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\ImageContent;
use PdfGenerator\Backend\Content\TextContent;
use PdfGenerator\Backend\Structure\Image;
use PdfGenerator\IR\Structure2\Content\Common\Position;
use PdfGenerator\IR\Structure2\Content\Rectangle\Style;
use PdfGenerator\IR\Transformation\DocumentResources;
use PdfGenerator\IR\Transformation\PageResources;

class ContentVisitor
{
    /**
     * @var DocumentResources
     */
    private $documentResources;

    /**
     * @var PageResources
     */
    private $pageResources;

    /**
     * ContentVisitor constructor.
     *
     * @param DocumentResources $documentResources
     * @param PageResources $pageResources
     */
    public function __construct(DocumentResources $documentResources, PageResources $pageResources)
    {
        $this->documentResources = $documentResources;
        $this->pageResources = $pageResources;
    }

    /**
     * @param ImagePlacement $placement
     *
     * @return BaseContent
     */
    public function visitImagePlacement(ImagePlacement $placement)
    {
        $image = $this->documentResources->getImage($placement->getImage());

        $this->applyImagePlacementPositionAndSize($image, $placement);
        $pageLevel = $this->pageResources->getPageLevel();

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
     * @return \PdfGenerator\Backend\Content\Rectangle|null
     */
    public function visitRectangle(Rectangle $rectangle)
    {
        $width = $rectangle->getSize()->getWidth();
        $height = $rectangle->getSize()->getHeight();
        $paintingMode = $this->getPaintingMode($rectangle);
        if ($paintingMode === false) {
            return null;
        }

        $this->applyPosition($rectangle->getPosition());
        $this->applyRectangleStyle($rectangle->getStyle());
        $pageLevel = $this->pageResources->getPageLevel();

        return new \PdfGenerator\Backend\Content\Rectangle($width, $height, $paintingMode, $pageLevel);
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
     * @param Style $style
     */
    private function applyRectangleStyle(Style $style)
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
                return \PdfGenerator\Backend\Content\Rectangle::PAINTING_MODE_STROKE_FILL;
            }

            return \PdfGenerator\Backend\Content\Rectangle::PAINTING_MODE_FILL;
        } elseif ($rectangle->getStyle()->getBorderColor() !== null) {
            return \PdfGenerator\Backend\Content\Rectangle::PAINTING_MODE_STROKE;
        } else {
            return false;
        }
    }

    /**
     * @param Text $param
     *
     * @return TextContent
     */
    public function visitText(Text $param)
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $param->getText());
        $lines = explode("\n", $textWithNormalizedNewlines);

        $this->applyPosition($param->getPosition());
        $this->applyTextStyle($param->getStyle());
        $textLevel = $this->pageResources->getTextLevel();

        return new TextContent($lines, $textLevel);
    }

    /**
     * @param Text\Style $style
     */
    private function applyTextStyle(Text\Style $style)
    {
        $font = $this->documentResources->getFont($style->getFont());
        $this->pageResources->getTextStateRepository()->setFont($font);
        $this->pageResources->getTextStateRepository()->setFontSize($style->getFontSize());
    }
}
