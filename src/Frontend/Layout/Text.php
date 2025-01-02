<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Layout\Text\Alignment;
use Famoser\PdfGenerator\Frontend\Layout\Text\Structure;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

class Text extends AbstractElement
{
    /**
     * @var TextSpan[]
     */
    private array $spans;

    public function __construct(private readonly Structure $level = Structure::Paragraph, private readonly Alignment $alignment = Alignment::ALIGNMENT_LEFT)
    {
    }

    public function add(TextSpan $span): void
    {
        $this->spans[] = $span;
    }

    public function addSpan(string $text, TextStyle $textStyle, float $fontSize = 4, float $lineHeight = 1.2): void
    {
        $this->add(new TextSpan($text, $textStyle, $fontSize, $lineHeight));
    }

    /**
     * @return TextSpan[]
     */
    public function getSpans(): array
    {
        return $this->spans;
    }

    public function getLevel(): Structure
    {
        return $this->level;
    }

    public function getAlignment(): Alignment
    {
        return $this->alignment;
    }

    /**
     * @param TextSpan[] $spans
     */
    public function cloneWithSpans(array $spans): self
    {
        $self = new self($this->level, $this->alignment);
        $self->spans = $spans;
        $self->writeStyle($this);

        return $self;
    }

    public function accept(ElementVisitorInterface $visitor)
    {
        return $visitor->visitText($this);
    }
}
