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

use PdfGenerator\Backend\Content\Base\BaseContent;
use PdfGenerator\Backend\Content\Operators\Level\WritingState;
use PdfGenerator\Backend\Content\Operators\State\Base\BaseState;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Backend\Structure\ContentVisitor;

class TextContent extends BaseContent
{
    /**
     * @var string[]
     */
    private $lines;

    /**
     * @var WritingState
     */
    private $text;

    /**
     * TextSymbol constructor.
     *
     * @param array $lines
     * @param WritingState $text
     */
    public function __construct(array $lines, WritingState $text)
    {
        $this->lines = $lines;
        $this->text = $text;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return $this->text->getState();
    }

    /**
     * @param ContentVisitor $visitor
     *
     * @return BaseObject
     */
    public function accept(ContentVisitor $visitor): BaseObject
    {
        return $visitor->visitTextContent($this);
    }
}
