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
use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizer;

class ScaledColumnBreaker
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
    public function __construct(TextStyle $textStyle, WordSizer $sizer, string $text)
    {
        $this->columnBreaker = new ColumnBreaker($sizer, $text);
        $this->textStyle = $textStyle;
    }

    public function hasMoreLines(): bool
    {
        return $this->columnBreaker->hasMoreLines();
    }

    private function getScale()
    {
        return $this->textStyle->getFontScaling();
    }

    public function nextLine(float $targetWidth)
    {
        $scale = $this->getScale();
        [$line, $lineWidth] = $this->columnBreaker->nextLine($targetWidth * $scale);

        $scaledWidth = $lineWidth / $scale;

        return [$line, $scaledWidth];
    }

    public function nextColumn(float $targetWidth, int $maxLines)
    {
        $scale = $this->getScale();
        [$lines, $lineWidths] = $this->columnBreaker->nextColumn($targetWidth * $scale, $maxLines);

        $scaledLineWidths = array_map(function ($entry) use ($scale) { return $entry / $scale; }, $lineWidths);

        return [$lines, $scaledLineWidths];
    }
}
