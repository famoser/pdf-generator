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
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;
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
    private $writingState;

    /**
     * TextSymbol constructor.
     */
    public function __construct(array $lines, WritingState $writingState)
    {
        $this->lines = $lines;
        $this->writingState = $writingState;
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
        return $this->writingState->getState();
    }

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitTextContent($this);
    }

    public function getCurrentTransformationMatrix(): array
    {
        return $this->writingState->getGeneralGraphicsState()->getCurrentTransformationMatrix();
    }

    public function getTextState(): TextState
    {
        return $this->writingState->getTextState();
    }
}
