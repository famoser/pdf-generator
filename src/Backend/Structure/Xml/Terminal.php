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
