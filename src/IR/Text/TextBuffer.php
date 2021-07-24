<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text;

use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Text\LineBreak\PhraseColumnBreaker;
use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizerRepository;
use PdfGenerator\IR\Text\TextBuffer\MeasuredPhrase;
use PdfGenerator\IR\Text\TextBuffer\Phrase;
use PdfGenerator\IR\Text\TextBuffer\TextBlock;

class TextBuffer
{
    /**
     * @var Phrase[]
     */
    private $phrases = [];

    /**
     * @var PhraseColumnBreaker|null
     */
    private $phraseColumnBreaker = null;

    /**
     * @var WordSizerRepository
     */
    private $wordSizerRepository;

    public function __construct()
    {
        $this->wordSizerRepository = new WordSizerRepository();
    }

    public function write(Document\Page\Content\Text\TextStyle $textStyle, string $text)
    {
        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setTextStyle($textStyle);

        $this->phrases[] = $phrase;
    }

    public function isEmpty()
    {
        return $this->phraseColumnBreaker === null && \count($this->phrases) === 0;
    }

    public function getNextTextStyle()
    {
        if ($this->phraseColumnBreaker === null && !$this->advancePhraseColumnBreaker()) {
            return null;
        }

        return $this->phraseColumnBreaker->getTextStyle();
    }

    private function advancePhraseColumnBreaker()
    {
        if (\count($this->phrases) === 0) {
            $this->phraseColumnBreaker = null;

            return false;
        }

        $phrase = array_shift($this->phrases);
        $this->phraseColumnBreaker = new PhraseColumnBreaker($phrase, $this->wordSizerRepository);

        return true;
    }

    public function getTextBlock(float $maxWidth, float $maxHeight, float $indent = 0): ?TextBlock
    {
        if ($this->phraseColumnBreaker === null && !$this->advancePhraseColumnBreaker()) {
            return null;
        }

        $remainingHeight = $maxHeight;
        $textBlock = new TextBlock();
        $textBlock->setIndent($indent);

        $newParagraph = true;

        while (true) {
            $leading = $this->phraseColumnBreaker->getTextStyle()->getLeading();
            $remainingLineCount = floor($remainingHeight / $leading);

            [$lines, $lineWidths] = $this->phraseColumnBreaker->nextColumn($maxWidth, $remainingLineCount, $indent, $newParagraph);
            $measuredPhrase = MeasuredPhrase::create($this->phraseColumnBreaker->getTextStyle(), $lines, $lineWidths);
            $textBlock->addMeasuredPhrase($measuredPhrase);

            if ($this->phraseColumnBreaker->hasMoreLines()) {
                // the column is full and the phrase has even more text ready
                // user needs to call getTextBlock again
                break;
            }

            if (!$this->advancePhraseColumnBreaker()) {
                // the current phrase is empty and no further phrases available
                // user needs to add more phrases
                break;
            }

            $newParagraph = false;
            $remainingHeight -= (\count($lines) - 1) * $leading;

            if (\count($lines) === 1) {
                // line adds to existing indent
                $indent += $lineWidths[0];
            } else {
                // last line is new, hence starts its own indent
                $indent = $lineWidths[\count($lineWidths) - 1];
            }
        }

        return $textBlock;
    }
}
