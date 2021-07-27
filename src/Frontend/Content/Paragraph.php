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

use PdfGenerator\Frontend\Block\Paragraph\Phrase;
use PdfGenerator\Frontend\Block\Style\ParagraphStyle;
use PdfGenerator\Frontend\BlockVisitor;
use PdfGenerator\Frontend\Content\Base\Content;
use PdfGenerator\Frontend\Content\Style\TextStyle;

class Paragraph extends Content
{
    /**
     * @var ParagraphStyle
     */
    private $style;

    /**
     * @var Phrase[]
     */
    private $phrases = [];

    public function __construct(ParagraphStyle $style = null)
    {
        $this->style = $style ?? new ParagraphStyle();
    }

    public function add(TextStyle $textStyle, string $text)
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

    public function accept(BlockVisitor $blockVisitor)
    {
        $blockVisitor->visitParagraph($this);
    }
}
