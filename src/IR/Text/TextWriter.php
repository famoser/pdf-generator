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
use PdfGenerator\IR\Text\TextWriter\MeasuredPhrase;
use PdfGenerator\IR\Text\TextWriter\Phrase;
use PdfGenerator\IR\Text\TextWriter\TextBlock;

class TextWriter
{
    /**
     * @var Document\Page\Content\Text\TextStyle
     */
    private $textStyle;

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

    public function setTextStyle(Document\Page\Content\Text\TextStyle $textStyle)
    {
        $this->textStyle = $textStyle;
    }

    public function writeText(string $text)
    {
        $phrase = new Phrase();
        $phrase->setText($text);
        $phrase->setTextStyle($this->textStyle);

        $this->phrases[] = $phrase;
    }

    public function isEmpty()
    {
        return $this->phraseColumnBreaker === null && \count($this->phrases) === 0;
    }

    private function ensurePhraseColumnBreakerInitialized()
    {
        if ($this->phraseColumnBreaker === null) {
            return $this->advancePhraseColumnBreaker();
        }

        return true;
    }

    private function advancePhraseColumnBreaker()
    {
        if (\count($this->phrases) === 0) {
            return false;
        }

        $phrase = array_shift($this->phrases);
        $this->phraseColumnBreaker = new PhraseColumnBreaker($phrase, $this->wordSizerRepository);

        return true;
    }

    public function getTextBlock(float $maxWidth, int $maxLineCount, float $indent = 0): ?TextBlock
    {
        if (!$this->ensurePhraseColumnBreakerInitialized()) {
            return null;
        }

        $remainingLineCount = $maxLineCount;
        $textBlock = new TextBlock();

        $newParagraph = true;

        while (true) {
            [$lines, $lineWidths] = $this->phraseColumnBreaker->nextColumn($maxWidth, $remainingLineCount, $indent, $newParagraph);
            $measuredPhrase = MeasuredPhrase::create($this->phraseColumnBreaker->getTextStyle(), $lines, $lineWidths, $indent);
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
            $remainingLineCount -= (\count($lines) - 1);

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
