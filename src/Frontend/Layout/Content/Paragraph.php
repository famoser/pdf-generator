<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Content;

use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\Layout\Content\Paragraph\Phrase;
use PdfGenerator\Frontend\Layout\Content\Style\TextStyle;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Paragraph extends Content
{
    final public const ALIGNMENT_LEFT = 'ALIGNMENT_LEFT';

    private string $alignment;

    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    public function __construct(string $alignment = self::ALIGNMENT_LEFT)
    {
        parent::__construct();
        $this->alignment = $alignment;
    }

    public function setAlignment(string $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function add(TextStyle $textStyle, string $text): void
    {
        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setTextStyle($textStyle);

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

    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitParagraph($this);
    }
}
