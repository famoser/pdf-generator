<?php

namespace Famoser\PdfGenerator\Backend\Structure\Xml;

use Famoser\PdfGenerator\Backend\Structure\XmlSerializerVisitor;

readonly class Terminal extends AbstractNode
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(string $tag, private string $value, array $attributes = [])
    {
        parent::__construct($tag, $attributes);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function visit(XmlSerializerVisitor $visitor): string
    {
        return $visitor->visitTerminal($this);
    }
}
