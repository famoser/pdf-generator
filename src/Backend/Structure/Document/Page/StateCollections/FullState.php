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
     * TextLevel constructor.
     */
    public function __construct(private GeneralGraphicState $generalGraphicsState, private ColorState $colorState, private TextState $textState)
    {
    }

    public static function createInitial(): FullState
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
