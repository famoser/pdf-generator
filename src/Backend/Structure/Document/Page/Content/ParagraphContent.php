<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content;

use Famoser\PdfGenerator\Backend\Catalog\Content;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\Phrase;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;

readonly class ParagraphContent extends BaseContent
{
    /**
     * @param Phrase[] $phrases
     */
    public function __construct(private array $phrases, private GeneralGraphicState $generalGraphicState)
    {
    }

    /**
     * @return Phrase[]
     */
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
}
