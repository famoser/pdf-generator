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
use PdfGenerator\IR\Structure\Page\Content\Text\TextStyle;
use PdfGenerator\IR\Structure\Page\ContentVisitor;

class Text extends BaseContent
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var TextStyle
     */
    private $style;

    /**
     * TextPlacement constructor.
     *
     * @param string $text
     * @param Position $position
     * @param TextStyle $style
     */
    public function __construct(string $text, Position $position, TextStyle $style)
    {
        $this->text = $text;
        $this->position = $position;
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @return TextStyle
     */
    public function getStyle(): TextStyle
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
        return $visitor->visitText($this);
    }
}
