<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Font\DefaultFont;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Size;
use PdfGenerator\IR\Structure\Document\Page\Content\ImagePlacement;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle;
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\Document\Page\Content\Text;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class Printer
{
    /**
     * @var Document
     */
    protected $document;

    /**
     * @var TextStyle
     */
    protected $textStyle;

    /**
     * @var RectangleStyle
     */
    protected $rectangleStyle;

    /**
     * Printer constructor.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;

        $font = $document->getOrCreateDefaultFont(DefaultFont::FONT_HELVETICA, DefaultFont::STYLE_DEFAULT);
        $this->textStyle = new TextStyle($font, 12);

        $color = new Color(0, 0, 0);
        $this->rectangleStyle = new RectangleStyle(1, $color, null);
    }

    public function printText(Page $page, Position $position, string $text)
    {
        $text = new Text($text, $position, $this->textStyle);

        $page->addContent($text);
    }

    public function printImage(Page $page, Position $position, Image $image, float $width, float $height)
    {
        $size = new Size($width, $height);
        $imagePlacement = new ImagePlacement($image, $position, $size);

        $page->addContent($imagePlacement);
    }

    public function printRectangle(Page $page, Position $position, float $width, float $height)
    {
        $size = new Size($width, $height);
        $text = new Rectangle($position, $size, $this->rectangleStyle);

        $page->addContent($text);
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->textStyle = $textStyle;
    }

    public function getRectangleStyle(): RectangleStyle
    {
        return $this->rectangleStyle;
    }

    public function setRectangleStyle(RectangleStyle $rectangleStyle): void
    {
        $this->rectangleStyle = $rectangleStyle;
    }
}
