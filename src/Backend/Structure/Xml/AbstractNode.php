<?php

namespace Famoser\PdfGenerator\Backend\Structure\Xml;

use Famoser\PdfGenerator\Backend\Structure\XmlSerializerVisitor;

abstract readonly class AbstractNode
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(private string $tag, private array $attributes) {
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

    public abstract function visit(XmlSerializerVisitor $visitor): string;
}
