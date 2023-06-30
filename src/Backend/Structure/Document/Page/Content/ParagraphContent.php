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
use PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\Phrase;
use PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;

readonly class ParagraphContent extends BaseContent
{
    /**
     * @param Phrase[] $phrases
     */
    public function __construct(private array $phrases, private GeneralGraphicState $generalGraphicState)
    {
    }

    public function getPhrases(): array
    {
        return $this->phrases;
    }

    /**
     * @return BaseState[]
     */
    public function getInfluentialStates(): array
    {
        return [$this->generalGraphicState];
    }

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitParagraphContent($this);
    }

    public function getCurrentTransformationMatrix(): array
    {
        return $this->generalGraphicState->getCurrentTransformationMatrix();
    }
}
