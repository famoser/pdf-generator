<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Xml;

use Famoser\PdfGenerator\Backend\Structure\XmlSerializerVisitor;

readonly class Node extends AbstractNode
{
    /**
     * @param array<string, string> $attributes
     * @param AbstractNode[]        $children
     */
    public function __construct(string $tag, private array $children, array $attributes = [])
    {
        parent::__construct($tag, $attributes);
    }

    /**
     * @return AbstractNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function visit(XmlSerializerVisitor $visitor): string
    {
        return $visitor->visitNode($this);
    }
}
