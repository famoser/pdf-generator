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

    public function add(TextStyle $textStyle, string $text): void
    {
        $phrase = new TextSpan($text, $textStyle);
        $this->spans[] = $phrase;
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

        return $self;
    }

    public function accept(ElementVisitorInterface $visitor)
    {
        return $visitor->visitText($this);
    }
}
