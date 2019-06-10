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

use PdfGenerator\IR\Structure2\Content\Base\BaseContent;
use PdfGenerator\IR\Structure2\Content\Common\Position;
use PdfGenerator\IR\Structure2\Content\Common\Size;
use PdfGenerator\IR\Structure2\Content\Rectangle\Style;

class Rectangle extends BaseContent
{
    /**
     * @var Position
     */
    private $position;

    /**
     * @var Size
     */
    private $size;

    /**
     * @var Style
     */
    private $style;

    /**
     * Rectangle constructor.
     *
     * @param Position $position
     * @param Size $size
     * @param Style $style
     */
    public function __construct(Position $position, Size $size, Style $style)
    {
        $this->position = $position;
        $this->size = $size;
        $this->style = $style;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @return Size
     */
    public function getSize(): Size
    {
        return $this->size;
    }

    /**
     * @return Style
     */
    public function getStyle(): Style
    {
        return $this->style;
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return \PdfGenerator\Backend\Content\Base\BaseContent
     */
    public function accept(ContentVisitor $visitor): \PdfGenerator\Backend\Content\Base\BaseContent
    {
        return $visitor->visitRectangle($visitor);
    }
}
