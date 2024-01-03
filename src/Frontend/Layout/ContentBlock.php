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
use PdfGenerator\Frontend\LayoutEngine\BlockVisitorInterface;

class ContentBlock extends AbstractBlock
{
    public function __construct(private AbstractContent $content)
    {
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
     * @param BlockVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(BlockVisitorInterface $visitor): mixed
    {
        return $visitor->visitContentBlock($this);
    }
}
