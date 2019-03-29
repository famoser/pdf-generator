<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Content\Text;

use PdfGenerator\Backend\Structure\Font;

/**
 * Class TextState.
 */
class TextState
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
     * @var Font Tt
     *           the font which has been added to the document
     */
    private $font;

    /**
     * @var int|float Tts
     *                the font size to be used
     */
    private $fontSize;

    /**
     * @var int|float Tc
     *                space between chars
     */
    private $charSpace = 0;

    /**
     * @var int|float Tw
     *                space between words (like @see $charSpace, but only applies to SPACE)
     */
    private $wordSpace = 0;

    /**
     * @var int|float Th
     *                percentage of normal width
     */
    private $scale = 100;

    /**
     * @var float|int Tl
     *                vertical distance between baselines (the line height)
     */
    private $leading = 0;

    /**
     * @var float|int Tr
     *                fill/stroke render combinations
     */
    private $renderMode = self::RENDER_MODE_FILL;

    /**
     * @var float|int Tr
     *                upwards shift from the baseline
     */
    private $rise = 0;

    /**
     * TextState constructor.
     *
     * @param Font $font
     * @param int|float $fontSize
     */
    public function __construct(Font $font, $fontSize)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
    }

    /**
     * @return Font
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @return float|int
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @return float|int
     */
    public function getCharSpace()
    {
        return $this->charSpace;
    }

    /**
     * @return float|int
     */
    public function getWordSpace()
    {
        return $this->wordSpace;
    }

    /**
     * @return float|int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return float|int
     */
    public function getLeading()
    {
        return $this->leading;
    }

    /**
     * @return float|int
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }

    /**
     * @return float|int
     */
    public function getRise()
    {
        return $this->rise;
    }
}
