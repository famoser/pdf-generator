<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Content\AbstractContent;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class ContentBlock extends AbstractBlock
{
    public function __construct(private AbstractContent $content)
    {
        parent::__construct();
    }

    public function getContent(): AbstractContent
    {
        return $this->content;
    }

    public function cloneWithContent(?AbstractContent $content): self
    {
        $clone = clone $this;
        $clone->content = $content;

        return $clone;
    }

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitContentBlock($this);
    }
}
