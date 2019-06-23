<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page;

use PdfGenerator\Backend\Structure\Document\Page\Content\ImageContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\RectangleContent;
use PdfGenerator\Backend\Structure\Document\Page\Content\TextContent;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text;

class ToBackendContentVisitor extends ContentVisitor
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

        $this->applyImagePlacementPositionAndSize($image->getWidth(), $image->getHeight(), $placement);
        $pageLevel = $this->pageResources->getDrawingState();

        return new ImageContent($image, $pageLevel);
    }

    /**
     * @param Rectangle $rectangle
     *
     * @return RectangleContent
     */
    public function visitRectangle(Rectangle $rectangle): RectangleContent
    {
        $width = $rectangle->getSize()->getWidth();
        $height = $rectangle->getSize()->getHeight();
        $paintingMode = $this->getPaintingMode($rectangle);

        $this->applyPosition($rectangle->getPosition());
        $this->applyRectangleStyle($rectangle->getStyle());
        $pageLevel = $this->pageResources->getDrawingState();

        return new RectangleContent($width, $height, $paintingMode, $pageLevel);
    }

    /**
     * @param Text $param
     *
     * @return TextContent
     */
    public function visitText(Text $param): TextContent
    {
        $escaped = $this->escapeReservedCharacters($param->getText());
        $encoded = $param->getStyle()->getFont()->encode($escaped);
        $lines = $this->splitAtNewlines($encoded);

        $this->applyPosition($param->getPosition());
        $this->applyTextStyle($param->getStyle());
        $textLevel = $this->pageResources->getWritingState();

        return new TextContent($lines, $textLevel);
    }

    /**
     * @param string $text
     *
     * @return mixed|string
     */
    private function escapeReservedCharacters(string $text)
    {
        $reserved = ['\\', '(', ')'];

        foreach ($reserved as $entry) {
            $text = str_replace($entry, '\\' . $entry, $text);
        }

        return $text;
    }

    /**
     * @param string $text
     *
     * @return string[]
     */
    private function splitAtNewlines(string $text)
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);

        return explode("\n", $textWithNormalizedNewlines);
    }

    /**
     * @param int $width
     * @param int $height
     * @param ImagePlacement $placement
     */
    private function applyImagePlacementPositionAndSize(int $width, int $height, ImagePlacement $placement)
    {
        $scaleX = $placement->getSize()->getWidth() / $width * 100;
        $scaleY = $placement->getSize()->getHeight() / $height * 100;

        $this->applyPosition($placement->getPosition(), $scaleX, $scaleY);
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
                return RectangleContent::PAINTING_MODE_STROKE_FILL;
            }

            return RectangleContent::PAINTING_MODE_FILL;
        } elseif ($rectangle->getStyle()->getBorderColor() !== null) {
            return RectangleContent::PAINTING_MODE_STROKE;
        }

        return RectangleContent::PAINTING_MODE_NONE;
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
