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

use PdfGenerator\Frontend\Content\Base\Content;
use PdfGenerator\Frontend\Content\Paragraph\Phrase;
use PdfGenerator\Frontend\Content\Style\ParagraphStyle;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\ContentVisitor;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Paragraph extends Content
{
    private readonly ParagraphStyle $style;

    /**
     * @var Phrase[]
     */
    private array $phrases = [];

    public function __construct(ParagraphStyle $style = null)
    {
        $this->style = $style ?? new ParagraphStyle();
    }

    public function add(TextStyle $textStyle, string $text): void
    {
        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setTextStyle($textStyle);

        $this->phrases[] = $phrase;
    }

    /**
     * @return Phrase[]
     */
    public function getPhrases(): array
    {
        return $this->phrases;
    }

    public function getStyle(): ParagraphStyle
    {
        return $this->style;
    }

    public function accept(ContentVisitor $contentVisitor): MeasuredContent
    {
        return $contentVisitor->visitParagraph($this);
    }
}
