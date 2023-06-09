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

use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base\BaseStateCollection;

class DrawingState extends BaseStateCollection
{
    /**
     * TextLevel constructor.
     */
    public function __construct(private GeneralGraphicState $generalGraphicsState, private ColorState $colorState)
    {
    }

    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->generalGraphicsState, $this->colorState];
    }
}
