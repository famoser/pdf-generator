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

abstract readonly class AbstractNode
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(private string $tag, private array $attributes)
    {
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    abstract public function visit(XmlSerializerVisitor $visitor): string;
}
