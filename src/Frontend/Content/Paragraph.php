<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Content\Paragraph\Phrase;
use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use Famoser\PdfGenerator\Frontend\Printer;

class Paragraph extends AbstractContent
{
    final public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    public function __construct(private string $alignment = self::ALIGNMENT_LEFT)
    {
    }

    public function setAlignment(string $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function add(TextStyle $textStyle, string $text): void
    {
        $phrase = new Phrase($text, $textStyle);
        $this->phrases[] = $phrase;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    /**
     * @return Phrase[]
     */
    public function getPhrases(): array
    {
        return $this->phrases;
    }

    /**
     * @param Phrase[] $phrases
     */
    public function cloneWithPhrases(array $phrases): self
    {
        $clone = clone $this;
        $clone->phrases = $phrases;

        return $clone;
    }

    public function accept(ContentVisitorInterface $visitor)
    {
        return $visitor->visitParagraph($this);
    }

    public function print(Printer $printer, float $width, float $height): void
    {
        $printer->printPhrases($this->getPhrases());
    }
}
