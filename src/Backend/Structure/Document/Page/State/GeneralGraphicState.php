<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\State;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateTransitionVisitor;

readonly class GeneralGraphicState extends BaseState
{
    final public const LINE_CAP_BUTT = 0;
    final public const LINE_CAP_ROUND = 1;
    final public const LINE_CAP_PROJECTING_SQUARE = 2;

    final public const LINE_JOIN_MITER = 0;
    final public const LINE_JOIN_ROUND = 1;
    final public const LINE_JOIN_BEVEL = 2;

    /**
     * @param float[] $currentTransformationMatrix transformation matrix
     *                                             translate: [1, 0, 0, 1, x, y]
     *                                             scale: [scale-x, 0, 0, scale-y, 0, 0]
     *                                             translate & scale: [scale-x, 0, 0, scale-y, x, y]
     *                                             rotation: [cos q, sin q, -sin q, cos q, 0, 0]
     *                                             skew: [1, tan a, tan b, 1, 0, 0]
     * @param float   $lineWidth                   line width
     *                                             if the value is 0, the thinnest line possible on the device will be rendered
     * @param int     $lineCap                     how the end of a line looks like
     *                                             butt cap stops squared at the end of the path
     *                                             round cap produces a semicircular arch with the diameter = @param int $lineJoin how two meeting lines are brought together
     *                                             miter join produces a sharp edge, by extending the meeting lines until the @param float $miterLimit impose maximum height of the sharp edge produced by a miter join
     *                                             when the threshold is reached, a bevel join is used
     *                                             calculate miterLimit = lineWidth * sin(angle / 2)
     * @param float[] $dashArray                   the pattern of on / off parts, repeated indefinitely
     *                                             if empty, then a solid line will be rendered
     * @param float   $dashPhase                   the pattern shift at start
     *
     * default arguments correspond to PDF defaults
     *
     * @see lineWidth
     *                                             projecting square cap stops squared at the end of the path + @see lineWidth/2
     * @see miterLimit
     *                                             round join creates an arch around the edge with diameter = @see lineWidth
     *                                             bevel join produces a flat edge, by adding a triangle into the free space produced by the two lines with butt caps meeting
     */
    public function __construct(private array $currentTransformationMatrix = [1, 0, 0, 1, 0, 0], private float $lineWidth = 0, private int $lineCap = self::LINE_CAP_BUTT, private int $lineJoin = self::LINE_JOIN_MITER, private float $miterLimit = 2.0, private array $dashArray = [], private float $dashPhase = 0)
    {
        \assert($lineCap >= self::LINE_CAP_BUTT && $lineCap <= self::LINE_CAP_PROJECTING_SQUARE);
    }

    /**
     * @return float[]
     */
    public function getCurrentTransformationMatrix(): array
    {
        return $this->currentTransformationMatrix;
    }

    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    public function getLineCap(): int
    {
        return $this->lineCap;
    }

    public function getLineJoin(): int
    {
        return $this->lineJoin;
    }

    public function getMiterLimit(): float
    {
        return $this->miterLimit;
    }

    /**
     * @return float[]
     */
    public function getDashArray(): array
    {
        return $this->dashArray;
    }

    public function getDashPhase(): float
    {
        return $this->dashPhase;
    }

    /**
     * @return string[]
     */
    public function accept(StateTransitionVisitor $visitor): array
    {
        return $visitor->visitGeneralGraphicState($this);
    }
}
