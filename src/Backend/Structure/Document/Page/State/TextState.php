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

class TextState extends BaseState
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
     * the font
     * pdf-operator: Tt.
     */
    private Font $font;

    /**
     * the font size to be used
     * pdf-operator: Tts.
     */
    private float $fontSize = 0;

    /**
     * space between chars
     * pdf-operator: Tc.
     */
    private float $charSpace = 0;

    /**
     * space between words (like @see $charSpace, but only applies to SPACE)
     * pdf-operator: Tw.
     */
    private float $wordSpace = 0;

    /**
     * percentage of normal width
     * pdf-operator: Th.
     */
    private float $scale = 100;

    /**
     * vertical distance between baselines (the line height)
     * pdf-operator: Tl.
     */
    private float $leading = 0;

    /**
     * fill/stroke render combinations
     * pdf-operator: Tr.
     */
    private float $renderMode = self::RENDER_MODE_FILL;

    /**
     * upwards shift from the baseline
     * pdf-operator: Tr.
     */
    private float $rise = 0;

    public function setFont(Font $font): void
    {
        $this->font = $font;
    }

    public function getFont(): ?Font
    {
        return $this->font;
    }

    public function setFontSize(float $fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function setCharSpace(float $charSpace): void
    {
        $this->charSpace = $charSpace;
    }

    public function getCharSpace(): float
    {
        return $this->charSpace;
    }

    public function setWordSpace(float $wordSpace): void
    {
        $this->wordSpace = $wordSpace;
    }

    public function getWordSpace(): float
    {
        return $this->wordSpace;
    }

    public function setScale(float $scale): void
    {
        $this->scale = $scale;
    }

    public function getScale(): float
    {
        return $this->scale;
    }

    public function setLeading(float $leading): void
    {
        $this->leading = $leading;
    }

    public function getLeading(): float
    {
        return $this->leading;
    }

    public function setRenderMode(int $renderMode): void
    {
        \assert($renderMode >= self::RENDER_MODE_FILL && $renderMode <= self::RENDER_MODE_PATH_INVISIBLE);

        $this->renderMode = $renderMode;
    }

    public function getRenderMode(): float
    {
        return $this->renderMode;
    }

    public function setRise(float $rise): void
    {
        $this->rise = $rise;
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
