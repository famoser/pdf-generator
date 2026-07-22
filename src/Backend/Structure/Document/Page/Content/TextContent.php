<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content;

use Famoser\PdfGenerator\Backend\Catalog\Content;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Base\BaseContent;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph\TextLine;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;

readonly class TextContent extends BaseContent
{
    /**
     * @param TextLine[] $lines
     */
    public function __construct(private array $lines, private GeneralGraphicState $generalGraphicState)
    {
    }

    /**
     * @return TextLine[]
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
        return [$this->generalGraphicState];
    }

    public function accept(ContentVisitor $visitor): Content
    {
        return $visitor->visitTextContent($this);
    }
}
