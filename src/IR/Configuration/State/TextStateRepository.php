<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Configuration\State;

use PdfGenerator\Backend\Content\Operators\State\TextState;
use PdfGenerator\Backend\Structure\Font;
use PdfGenerator\IR\Structure\Content\FontRepository;

class TextStateRepository
{
    /**
     * @var Font
     */
    private $font = null;

    /**
     * @var float
     */
    private $fontSize = 8;

    /**
     * @var float
     */
    private $leading = 9.6;

    /**
     * @var TextState
     */
    private $activeTextState;

    /**
     * @var FontRepository
     */
    private $fontRepository;

    /**
     * TextRepository constructor.
     *
     * @param FontRepository $fontRepository
     */
    public function __construct(FontRepository $fontRepository)
    {
        $this->fontRepository = $fontRepository;
    }

    /**
     * @param float $fontSize
     * @param float $lineHeight
     */
    public function setFontSize(float $fontSize, float $lineHeight = 1.2)
    {
        $this->fontSize = $fontSize;
        $this->leading = $this->fontSize * $lineHeight;

        $this->activeTextState = null;
    }

    /**
     * @throws \Exception
     *
     * @return TextState
     */
    public function getTextState()
    {
        if ($this->activeTextState !== null) {
            return $this->activeTextState;
        }

        $this->activeTextState = new TextState();
        $this->activeTextState->setFontSize($this->fontSize);
        $this->activeTextState->setLeading($this->leading);

        if ($this->font === null) {
            $this->font = $this->fontRepository->getSimpleFont(FontRepository::FONT_HELVETICA, FontRepository::STYLE_DEFAULT);
        }
        $this->activeTextState->setFont($this->font);

        return $this->activeTextState;
    }
}
