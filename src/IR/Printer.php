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
use PdfGenerator\IR\Structure\Font\DefaultFont;
use PdfGenerator\IR\Structure\Image;
use PdfGenerator\IR\Structure\Optimization\Configuration;
use PdfGenerator\IR\Structure\PageContent\Common\Color;
use PdfGenerator\IR\Structure\PageContent\Common\Position;
use PdfGenerator\IR\Structure\PageContent\Common\Size;
use PdfGenerator\IR\Structure\PageContent\ImagePlacement;
use PdfGenerator\IR\Structure\PageContent\Rectangle;
use PdfGenerator\IR\Structure\PageContent\Rectangle\RectangleStyle;
use PdfGenerator\IR\Structure\PageContent\Text;
use PdfGenerator\IR\Structure\PageContent\Text\TextStyle;

class Printer
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * @var RectangleStyle
     */
    private $rectangleStyle;

    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * Printer constructor.
     */
    public function __construct()
    {
        $this->document = new Document();

        $font = new DefaultFont(DefaultFont::FONT_HELVETICA, DefaultFont::STYLE_DEFAULT);
        $this->textStyle = new TextStyle($font, 12);

        $color = new Color(0, 0, 0);
        $this->rectangleStyle = new RectangleStyle(1, $color, null);

        $this->cursor = new Cursor(10, 10, 1);
    }

    /**
     * @param string $text
     */
    public function printText(string $text)
    {
        $position = $this->getPosition();

        $text = new Text($text, $position, $this->textStyle);

        $page = $this->getPage();
        $page->addContent($text);
    }

    /**
     * @param string $imagePath
     * @param float $width
     * @param float $height
     */
    public function printImage(string $imagePath, float $width, float $height)
    {
        $position = $this->getPosition();
        $size = new Size($width, $height);

        $image = new Image($imagePath);
        $imagePlacement = new ImagePlacement($image, $position, $size);

        $page = $this->getPage();
        $page->addContent($imagePlacement);
    }

    /**
     * @param float $width
     * @param float $height
     */
    public function printRectangle(float $width, float $height)
    {
        $position = $this->getPosition();
        $size = new Size($width, $height);

        $text = new Rectangle($position, $size, $this->rectangleStyle);

        $page = $this->getPage();
        $page->addContent($text);
    }

    /**
     * @return Structure\Page
     */
    private function getPage()
    {
        return $this->document->getOrCreatePage($this->cursor->getPage());
    }

    /**
     * @return Position
     */
    private function getPosition()
    {
        return new Position($this->cursor->getXCoordinate(), $this->cursor->getYCoordinate());
    }

    /**
     * @param TextStyle $textStyle
     */
    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->textStyle = $textStyle;
    }

    /**
     * @param RectangleStyle $rectangleStyle
     */
    public function setRectangleStyle(RectangleStyle $rectangleStyle): void
    {
        $this->rectangleStyle = $rectangleStyle;
    }

    /**
     * @param Cursor $cursor
     */
    public function setCursor(Cursor $cursor): void
    {
        $this->cursor = $cursor;
    }

    /**
     * @param Configuration|null $configuration
     *
     * @return string
     */
    public function save(Configuration $configuration = null)
    {
        if ($configuration === null) {
            $configuration = new Configuration();
        }

        return $this->document->save($configuration);
    }
}
