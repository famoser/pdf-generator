<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;
use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizerRepository;
use PdfGenerator\IR\Text\TextSizer\Phrase;

class PhraseColumnBreaker
{
    /**
     * @var ColumnBreaker
     */
    private $columnBreaker;

    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * ColumnBreaker constructor.
     */
    public function __construct(Phrase $phrase, WordSizerRepository $wordSizerRepository)
    {
        $this->textStyle = $phrase->getTextStyle();

        $wordSizer = $wordSizerRepository->getWordSizer($this->textStyle->getFont());
        $this->columnBreaker = new ColumnBreaker($wordSizer, $phrase->getText());
    }

    public function hasMoreLines(): bool
    {
        return $this->columnBreaker->hasMoreLines();
    }

    private function getScale()
    {
        return $this->textStyle->getFontScaling();
    }

    public function nextLine(float $targetWidth, bool $allowEmpty)
    {
        $scale = $this->getScale();
        [$line, $lineWidth] = $this->columnBreaker->nextLine($targetWidth * $scale, $allowEmpty);

        $scaledWidth = $lineWidth / $scale;

        return [$line, $scaledWidth];
    }

    public function nextColumn(float $targetWidth, int $maxLines, float $indent, bool $newParagraph)
    {
        $scale = $this->getScale();
        [$lines, $lineWidths] = $this->columnBreaker->nextColumn($targetWidth * $scale, $maxLines, $indent * $scale, $newParagraph);

        $scaledLineWidths = array_map(function ($entry) use ($scale) { return $entry / $scale; }, $lineWidths);

        return [$lines, $scaledLineWidths];
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }
}
