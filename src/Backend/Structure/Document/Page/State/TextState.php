<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\State;

use PdfGenerator\Backend\Structure\Document\Font;
use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

readonly class TextState extends BaseState
{
    final public const RENDER_MODE_FILL = 0;
    final public const RENDER_MODE_STROKE = 1;
    final public const RENDER_MODE_FILL_STROKE = 2;
    final public const RENDER_MODE_INVISIBLE = 3;
    final public const RENDER_MODE_PATH_FILL = 4;
    final public const RENDER_MODE_PATH_STROKE = 5;
    final public const RENDER_MODE_PATH_FILL_STROKE = 6;
    final public const RENDER_MODE_PATH_INVISIBLE = 7;

    /**
     * @param Font $font the font
     * @param float $fontSize the font size to be used
     * @param float $leading vertical distance between baselines, i.e. the line height
     * @param float $charSpace space between chars
     * @param float $wordSpace space between words; @see $charSpace but only applies to SPACE
     * @param float $scale percentage of normal width
     * @param float $renderMode fill/stroke render combinations
     * @param float $rise upwards shift from the baseline
     */
    public function __construct(private Font $font, private float $fontSize, private float $leading = 0, private float $charSpace = 0, private float $wordSpace = 0, private float $scale = 100, private float $renderMode = self::RENDER_MODE_FILL, private float $rise = 0)
    {
    }

    public function getFont(): ?Font
    {
        return $this->font;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getCharSpace(): float
    {
        return $this->charSpace;
    }

    public function getWordSpace(): float
    {
        return $this->wordSpace;
    }

    public function getScale(): float
    {
        return $this->scale;
    }

    public function getLeading(): float
    {
        return $this->leading;
    }

    public function getRenderMode(): float
    {
        return $this->renderMode;
    }

    public function getRise(): float
    {
        return $this->rise;
    }

    /**
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitTextState($this);
    }
}
