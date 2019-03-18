<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content;

use PdfGenerator\Backend\Content\Base\PlacedContent;
use PdfGenerator\Backend\ContentVisitor;
use PdfGenerator\Backend\File\File;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\Font;

class TextContent extends PlacedContent
{
    /**
     * @var Font
     */
    private $font;

    /**
     * @var float
     */
    private $fontSize;

    /**
     * @var string
     */
    private $text;

    /**
     * TextContent constructor.
     *
     * @param float $xCoordinate
     * @param float $yCoordinate
     * @param string $text
     * @param Font $font
     * @param float $fontSize
     */
    public function __construct(float $xCoordinate, float $yCoordinate, string $text, Font $font, float $fontSize)
    {
        parent::__construct($xCoordinate, $yCoordinate);

        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->text = $text;
    }

    /**
     * @param ContentVisitor $visitor
     * @param File $file
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor, File $file): BaseObject
    {
        return $visitor->visitTextContent($this, $file);
    }

    /**
     * @return Font
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @return float
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
