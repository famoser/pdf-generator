<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Analysis;

use PdfGenerator\IR\Document\Content\Common\Size;
use PdfGenerator\IR\Document\Content\ContentVisitorInterface;
use PdfGenerator\IR\Document\Content\ImagePlacement;
use PdfGenerator\IR\Document\Content\Paragraph;
use PdfGenerator\IR\Document\Content\Rectangle;
use PdfGenerator\IR\Document\Content\Text;

/**
 * @implements ContentVisitorInterface<void>
 */
class AnalyzeContentVisitor implements ContentVisitorInterface
{
    /**
     * @var Size[]
     */
    private array $maxSizePerImage = [];

    /**
     * @var string[]
     */
    private array $textPerFont = [];

    public function visitImagePlacement(ImagePlacement $placement): void
    {
        $identifier = $placement->getImage()->getIdentifier();

        if (!\array_key_exists($identifier, $this->maxSizePerImage)) {
            $this->maxSizePerImage[$identifier] = $placement->getSize();

            return;
        }

        $existing = $this->maxSizePerImage[$identifier];
        $new = $placement->getSize();

        $maxWidth = max($existing->getWidth(), $new->getWidth());
        $maxHeight = max($existing->getHeight(), $new->getHeight());
        $size = new Size($maxWidth, $maxHeight);

        $this->maxSizePerImage[$identifier] = $size;
    }

    public function visitRectangle(Rectangle $rectangle)
    {
    }

    public function visitText(Text $text): void
    {
        $identifier = $text->getStyle()->getFont()->getIdentifier();
        if (!\array_key_exists($identifier, $this->textPerFont)) {
            $this->textPerFont[$identifier] = '';
        }

        $this->textPerFont[$identifier] .= $text->getText();
    }

    public function visitParagraph(Paragraph $paragraph): void
    {
        foreach ($paragraph->getPhrase() as $phrase) {
            $identifier = $phrase->getStyle()->getFont()->getIdentifier();
            if (!\array_key_exists($identifier, $this->textPerFont)) {
                $this->textPerFont[$identifier] = '';
            }

            $this->textPerFont[$identifier] .= $phrase->getText();
        }
    }

    public function getAnalysisResult(): AnalysisResult
    {
        return new AnalysisResult($this->maxSizePerImage, $this->textPerFont);
    }
}
