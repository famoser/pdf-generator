<?php

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

class ContentBlock extends AbstractElement
{
    public function __construct(private readonly ?AbstractContent $content = null)
    {
    }

    public function getContent(): ?AbstractContent
    {
        return $this->content;
    }

    /**
     * @template T
     *
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(ElementVisitorInterface $visitor): mixed
    {
        return $visitor->visitContentBlock($this);
    }
}
