<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdf\IR\Content;

use Pdf\Backend\Object\Base\BaseObject;
use Pdf\Backend\Structure\File;
use Pdf\IR\Content\Base\BaseContent;
use Pdf\IR\ContentVisitor;
use Pdf\IR\Structure\Font;

class TextContent extends BaseContent
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
     * @var float
     */
    private $xCoordinate;

    /**
     * @var float
     */
    private $yCoordinate;

    /**
     * @var string
     */
    private $text;

    /**
     * TextContent constructor.
     *
     * @param Font $font
     * @param float $fontSize
     * @param float $xCoordinate
     * @param float $yCoordinate
     * @param string $text
     */
    public function __construct(Font $font, float $fontSize, float $xCoordinate, float $yCoordinate, string $text)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->xCoordinate = $xCoordinate;
        $this->yCoordinate = $yCoordinate;
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
     * @return float
     */
    public function getXCoordinate(): float
    {
        return $this->xCoordinate;
    }

    /**
     * @return float
     */
    public function getYCoordinate(): float
    {
        return $this->yCoordinate;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
