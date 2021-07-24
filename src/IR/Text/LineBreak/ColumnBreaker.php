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

use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizer;

class ColumnBreaker
{
    /**
     * @var string[]
     */
    private $remainingLines;

    /**
     * @var LineBreaker
     */
    private $lineBreaker;

    /**
     * @var WordSizer
     */
    private $sizer;

    /**
     * ColumnBreaker constructor.
     */
    public function __construct(WordSizer $sizer, string $text)
    {
        $this->sizer = $sizer;
        $this->remainingLines = explode("\n", $text);
        $this->advanceLineBreaker();
    }

    private function advanceLineBreaker()
    {
        if (\count($this->remainingLines) === 0) {
            return false;
        }

        $line = array_shift($this->remainingLines);
        $this->lineBreaker = new LineBreaker($this->sizer, $line);

        return true;
    }

    public function hasMoreLines(): bool
    {
        return $this->lineBreaker->hasNextLine() || \count($this->remainingLines) > 0;
    }

    public function nextLine(float $targetWidth, bool $allowEmpty)
    {
        if (!$this->lineBreaker->hasNextLine()) {
            $this->advanceLineBreaker();
        }

        return $this->lineBreaker->nextLine($targetWidth, $allowEmpty);
    }

    public function nextColumn(float $targetWidth, int $maxLines, float $indent, bool $newParagraph)
    {
        $allowEmpty = !$newParagraph; // if new paragraph force content on first line, else do not
        $requestedWidth = $targetWidth - $indent;

        $lines = [];
        $lineWidths = [];
        while (\count($lines) < $maxLines) {
            if (!$this->lineBreaker->hasNextLine() && !$this->advanceLineBreaker()) {
                break;
            }

            [$line, $width] = $this->lineBreaker->nextLine($requestedWidth, $allowEmpty);
            $lines[] = $line;
            $lineWidths[] = $width;

            // while first line may have indent, further lines do not
            $allowEmpty = false;
            $requestedWidth = $targetWidth;
        }

        return [$lines, $lineWidths];
    }
}
