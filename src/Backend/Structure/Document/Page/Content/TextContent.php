<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\Content;

use PdfGenerator\Backend\Catalog\Content;
use PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\WritingState;

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

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitTextContent($this);
    }
}
