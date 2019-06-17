<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Operators\State;

use PdfGenerator\Backend\Catalog\Font;
use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Operators\State\Base\BaseState;

class TextState extends BaseState
{
    const RENDER_MODE_FILL = 0;
    const RENDER_MODE_STROKE = 1;
    const RENDER_MODE_FILL_STROKE = 2;
    const RENDER_MODE_INVISIBLE = 3;
    const RENDER_MODE_PATH_FILL = 4;
    const RENDER_MODE_PATH_STROKE = 5;
    const RENDER_MODE_PATH_FILL_STROKE = 6;
    const RENDER_MODE_PATH_INVISIBLE = 7;

    /**
     * the font
     * pdf-operator: Tt.
     *
     * @var Font?
     */
    private $font;

    /**
     * the font size to be used
     * pdf-operator: Tts.
     *
     * @var float
     */
    private $fontSize = 0;

    /**
     * space between chars
     * pdf-operator: Tc.
     *
     * @var float
     */
    private $charSpace = 0;

    /**
     * space between words (like @see $charSpace, but only applies to SPACE)
     * pdf-operator: Tw.
     *
     * @var float
     */
    private $wordSpace = 0;

    /**
     * percentage of normal width
     * pdf-operator: Th.
     *
     * @var float
     */
    private $scale = 100;

    /**
     * vertical distance between baselines (the line height)
     * pdf-operator: Tl.
     *
     * @var float
     */
    private $leading = 0;

    /**
     * fill/stroke render combinations
     * pdf-operator: Tr.
     *
     * @var float
     */
    private $renderMode = self::RENDER_MODE_FILL;

    /**
     * upwards shift from the baseline
     * pdf-operator: Tr.
     *
     * @var float
     */
    private $rise = 0;

    /**
     * @param Font $font
     */
    public function setFont(Font $font): void
    {
        $this->font = $font;
    }

    /**
     * @return Font|null
     */
    public function getFont(): ?Font
    {
        return $this->font;
    }

    /**
     * @param float $fontSize
     */
    public function setFontSize(float $fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    /**
     * @return float
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    /**
     * @param float $charSpace
     */
    public function setCharSpace(float $charSpace): void
    {
        $this->charSpace = $charSpace;
    }

    /**
     * @return float
     */
    public function getCharSpace(): float
    {
        return $this->charSpace;
    }

    /**
     * @param float $wordSpace
     */
    public function setWordSpace(float $wordSpace): void
    {
        $this->wordSpace = $wordSpace;
    }

    /**
     * @return float
     */
    public function getWordSpace(): float
    {
        return $this->wordSpace;
    }

    /**
     * @param float $scale
     */
    public function setScale(float $scale): void
    {
        $this->scale = $scale;
    }

    /**
     * @return float
     */
    public function getScale(): float
    {
        return $this->scale;
    }

    /**
     * @param float $leading
     */
    public function setLeading(float $leading): void
    {
        $this->leading = $leading;
    }

    /**
     * @return float
     */
    public function getLeading(): float
    {
        return $this->leading;
    }

    /**
     * @param int $renderMode
     */
    public function setRenderMode(int $renderMode): void
    {
        \assert($renderMode >= self::RENDER_MODE_FILL && $renderMode <= self::RENDER_MODE_PATH_INVISIBLE);

        $this->renderMode = $renderMode;
    }

    /**
     * @return float
     */
    public function getRenderMode(): float
    {
        return $this->renderMode;
    }

    /**
     * @param float $rise
     */
    public function setRise(float $rise): void
    {
        $this->rise = $rise;
    }

    /**
     * @return float
     */
    public function getRise(): float
    {
        return $this->rise;
    }

    /**
     * @param StateTransitionVisitor $visitor
     *
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitTextState($this);
    }
}
