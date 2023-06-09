<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\Content;

use PdfGenerator\IR\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;
use PdfGenerator\IR\Structure\Document\Page\ContentVisitor;

class Text extends BaseContent
{
    private string $text;

    private Position $position;

    private TextStyle $style;

    /**
     * TextPlacement constructor.
     */
    public function __construct(string $text, Position $position, TextStyle $style)
    {
        $this->text = $text;
        $this->position = $position;
        $this->style = $style;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getStyle(): TextStyle
    {
        return $this->style;
    }

    public function accept(ContentVisitor $visitor): ?\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent
    {
        return $visitor->visitText($this);
    }
}
