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

use PdfGenerator\Backend\Structure\Document\Page\Content\StateTransitionVisitor;
use PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;

class ColorState extends BaseState
{
    /**
     * the line/border color.
     *
     * @var float[]
     */
    private array $rgbStrokingColour = [0, 0, 0];

    /**
     * the fill color.
     *
     * @var float[]
     */
    private array $rgbNonStrokingColour = [0, 0, 0];

    /**
     * @return float[]
     */
    public function getRgbStrokingColour(): array
    {
        return $this->rgbStrokingColour;
    }

    /**
     * @param float[] $rgbStrokingColour
     */
    public function setRgbStrokingColour(array $rgbStrokingColour): void
    {
        \assert(3 === \count($rgbStrokingColour));

        $this->rgbStrokingColour = $rgbStrokingColour;
    }

    /**
     * @return float[]
     */
    public function getRgbNonStrokingColour(): array
    {
        return $this->rgbNonStrokingColour;
    }

    /**
     * @param float[] $rgbNonStrokingColour
     */
    public function setRgbNonStrokingColour(array $rgbNonStrokingColour): void
    {
        \assert(3 === \count($rgbNonStrokingColour));

        $this->rgbNonStrokingColour = $rgbNonStrokingColour;
    }

    /**
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitColorState($this);
    }
}
