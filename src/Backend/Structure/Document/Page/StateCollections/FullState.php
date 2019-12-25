<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Document\Page\State\TextState;

class FullState
{
    /**
     * @var GeneralGraphicState
     */
    private $generalGraphicsState;

    /**
     * @var ColorState
     */
    private $colorState;

    /**
     * @var TextState
     */
    private $textState;

    /**
     * TextLevel constructor.
     */
    public function __construct(GeneralGraphicState $generalGraphicsState, ColorState $colorState, TextState $textState)
    {
        $this->generalGraphicsState = $generalGraphicsState;
        $this->colorState = $colorState;
        $this->textState = $textState;
    }

    /**
     * @return FullState
     */
    public static function createInitial()
    {
        return new self(new GeneralGraphicState(), new ColorState(), new TextState());
    }

    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    public function getColorState(): ColorState
    {
        return $this->colorState;
    }

    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
