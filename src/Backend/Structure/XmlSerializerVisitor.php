<?php

namespace Famoser\PdfGenerator\Backend\Structure;

use Famoser\PdfGenerator\Backend\Structure\Xml\AbstractNode;

readonly class XmlSerializerVisitor
{
    private const ESCAPES = ['"' => '&quot;', "'" => '&apos;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;'];
    private const IDENT_STEP = 4;

    public function __construct(private int $ident = 0)
    {
    }

    public function visitNode(Xml\Node $param): string
    {
        $content = '';
        if (count($param->getChildren()) > 0) {
            $childVisitor = new XmlSerializerVisitor($this->ident + self::IDENT_STEP);
            $children = array_map(function (AbstractNode $node) use ($childVisitor) {
                return $node->visit($childVisitor);
            }, $param->getChildren());
            $content = "\n" . implode("\n", $children) . "\n";
        }

        return $this->renderTag($param, $content);
    }

    public function visitTerminal(Xml\Terminal $param): string
    {
        $content = self::escape($param->getValue());

        return $this->renderTag($param, $content);
    }

    private function renderTag(AbstractNode $node, string $content): string
    {
        $openTag = $this->renderOpenTag($node);
        $closeTag = $this->renderCloseTag($node);

        return $openTag . $content . $closeTag;
    }

    private function renderOpenTag(AbstractNode $node): string
    {
        $prefix = str_repeat(' ', $this->ident);
        $tag = $prefix . '<' . $node->getTag();

        if (count($node->getAttributes()) > 0) {
            $attributes = [];
            foreach ($node->getAttributes() as $key => $value) {
                $attributes[] = $key . '="' . self::escape($value) . '"';
            }
            $tag .= ' ' . implode(' ', $attributes);
        }

        return $tag . ">";
    }

    private function renderCloseTag(AbstractNode $node): string
    {
        $prefix = str_repeat(' ', $this->ident);
        return $prefix . '</' . $node->getTag();
    }

    private static function escape(string $value): string
    {
        return str_replace(array_keys(self::ESCAPES), self::ESCAPES, $value);
    }
}
