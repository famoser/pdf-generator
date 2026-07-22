<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Base;

use Famoser\PdfGenerator\Backend\Catalog\Content;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\ContentVisitor;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

abstract readonly class BaseContent
{
    abstract public function accept(ContentVisitor $visitor): Content;

    /**
     * @return BaseState[]
     */
    abstract public function getInfluentialStates(): array;
}
