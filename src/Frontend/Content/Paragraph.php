<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content;

use PdfGenerator\Frontend\Content\Paragraph\Phrase;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\LayoutEngine\ContentVisitorInterface;
use PdfGenerator\Frontend\Printer;

class Paragraph extends AbstractContent
{
    final public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    private string $alignment;

    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    public function __construct(string $alignment = self::ALIGNMENT_LEFT)
    {
        $this->alignment = $alignment;
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

    public function cloneWithPhrases(array $phrases): self
    {
        $clone = clone $this;
        $clone->phrases = $phrases;

        return $clone;
    }

    public function accept(ContentVisitorInterface $visitor): mixed
    {
        return $visitor->visitParagraph($this);
    }

    public function print(Printer $printer, float $width, float $height): void
    {
        $printer->printPhrases($this->getPhrases());
    }
}
