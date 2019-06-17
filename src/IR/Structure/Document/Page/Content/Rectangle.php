<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Page\Content;

use PdfGenerator\IR\Structure\Page\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Page\Content\Common\Size;
use PdfGenerator\IR\Structure\Page\Content\Rectangle\RectangleStyle;

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
     * @var RectangleStyle
     */
    private $style;

    /**
     * Rectangle constructor.
     *
     * @param Position $position
     * @param Size $size
     * @param RectangleStyle $style
     */
    public function __construct(Position $position, Size $size, RectangleStyle $style)
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
     * @return RectangleStyle
     */
    public function getStyle(): RectangleStyle
    {
        return $this->style;
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent
     */
    public function accept(ContentVisitor $visitor): \PdfGenerator\Backend\Structure\Page\Content\Base\BaseContent
    {
        return $visitor->visitRectangle($this);
    }
}
