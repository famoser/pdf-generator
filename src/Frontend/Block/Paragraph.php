<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Paragraph\Phrase;
use PdfGenerator\Frontend\Block\Paragraph\TextStyle;
use PdfGenerator\Frontend\Style\ParagraphStyle;

class Paragraph extends Block
{
    /**
     * @var ParagraphStyle
     */
    private $style;

    /**
     * @var Phrase[]
     */
    private $phrases = [];

    public function __construct(ParagraphStyle $style, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->style = $style;
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
}
